/**
 * scout-agents.ts
 *
 * Fans out Cursor SDK agents, each hunting one topic family for new
 * educational resources for lopp.net. Writes candidate lists per
 * docs/resource-sourcing.md section 3 into a scratch directory.
 *
 * Usage:
 *   CURSOR_API_KEY=cursor_... npx tsx scout-agents.ts [--topics a,b,c] [--out /tmp/lopp-scouts] [--concurrency 4]
 *
 * Topics default to all families in docs/resource-sourcing.md section 8.
 */
import { mkdir } from "node:fs/promises";
import path from "node:path";
import { setMaxListeners } from "node:events";

import { Agent, CursorAgentError } from "@cursor/sdk";

// The SDK registers an abort listener per in-flight run on shared AbortSignals.
// With parallel scouts that exceeds Node's default cap of 10 listeners per
// EventTarget and trips a false-positive MaxListenersExceededWarning (one
// listener per live agent is intentional, not a leak). This raises the default
// cap for every EventTarget/EventEmitter in this process; 64 leaves ~5x
// headroom over the fan-out width while still catching genuine leaks.
setMaxListeners(64);

const SCRATCH_DIR = "/tmp/lopp-scouts";
const PLAYBOOK = "docs/resource-sourcing.md";

const TOPIC_FAMILIES = [
  "explainers-getting-started",
  "security-custody",
  "privacy",
  "lightning-developer-tools",
  "lightning-node-liquidity",
  "economics-books",
  "mining",
  "governance-forks-history",
  "podcasts-video-documentaries",
  "developer-tools-bitcoin",
  "merchant-tax-legal",
  "data-anchors-ecash-other-layers",
] as const;

type TopicFamily = (typeof TOPIC_FAMILIES)[number];

function argValue(flag: string): string | undefined {
  const idx = process.argv.indexOf(flag);
  return idx >= 0 ? process.argv[idx + 1] : undefined;
}

const topicsArg = argValue("--topics");
const topics: TopicFamily[] = topicsArg
  ? (topicsArg.split(",").map((t) => t.trim()) as TopicFamily[])
  : [...TOPIC_FAMILIES];
const outDir = argValue("--out") ?? SCRATCH_DIR;
const concurrency = Math.max(1, Number(argValue("--concurrency") ?? 4));

function scoutPrompt(topic: TopicFamily, datestamp: string): string {
  return `You are a resource SCOUT for lopp.net's curated educational pages.

First, read ${PLAYBOOK} sections 1-3 ("Where to hunt", "Search queries that work", "Scout output format"). Follow them exactly. Do NOT read or modify any other repo file; do not edit any HTML.

Your topic family: ${topic}

Hunt the internet for NEW high-quality educational resources in this family that are not yet listed on lopp.net. Aim for 10-25 strong candidates. For each candidate verify the URL resolves (fetch title or confirm HTTP 200); mark anything you could not verify as UNVERIFIED. Flag referral-revenue patterns and fund-custody services as red flags.

When running shell commands, use only foreground commands with explicit timeouts (e.g. curl -m 20 -sS). Never launch background processes (&, nohup, watchers, dev servers): lingering processes hold file descriptors open and stall the runtime's close-event tracking.

Write your findings to ${outDir}/candidates-${topic}-${datestamp}.md in exactly the entry format from ${PLAYBOOK} section 3. Do not write any other files.`;
}

type ScoutOutcome = { topic: TopicFamily; ok: boolean; runId?: string };

async function runScout(
  topic: TopicFamily,
  datestamp: string,
  apiKey: string,
): Promise<ScoutOutcome> {
  try {
    const result = await Agent.prompt(scoutPrompt(topic, datestamp), {
      apiKey,
      // Local runtime: scouts only need repo read access to the playbook
      // plus the machine's network access for web fetching.
      local: { cwd: process.cwd() },
      model: { id: "auto" },
    });
    if (result.status === "error") {
      // Run executed but failed mid-flight: startup vs run failure are
      // distinct (see skill trap #2); report accordingly.
      console.error(`[run-failed] ${topic}: run ${result.id}`);
      return { topic, ok: false, runId: result.id };
    }
    console.log(`[ok] ${topic}: run ${result.id}`);
    return { topic, ok: true, runId: result.id };
  } catch (err) {
    if (err instanceof CursorAgentError) {
      console.error(
        `[startup-failed] ${topic}: ${err.message} (retryable=${err.isRetryable})`,
      );
    } else {
      console.error(`[unknown-error] ${topic}:`, err);
    }
    return { topic, ok: false };
  }
}

// Fixed-size worker pool: keeps at most `concurrency` SDK run processes alive
// at once. Each local run spawns child processes; bounding the pool keeps the
// fd/teardown pressure (and close-event warnings) low instead of orphaning a
// dozen runtimes at once.
async function runPool(items: TopicFamily[], worker: (t: TopicFamily) => Promise<ScoutOutcome>): Promise<ScoutOutcome[]> {
  const outcomes: ScoutOutcome[] = [];
  let index = 0;
  const runners = Array.from(
    { length: Math.min(concurrency, items.length) },
    async () => {
      while (index < items.length) {
        outcomes.push(await worker(items[index++]));
      }
    },
  );
  await Promise.all(runners);
  return outcomes;
}

async function main(): Promise<void> {
  const apiKey = process.env.CURSOR_API_KEY;
  if (!apiKey) {
    console.error("CURSOR_API_KEY is not set");
    process.exitCode = 1;
    return;
  }
  const datestamp = new Date().toISOString().slice(0, 10).replace(/-/g, "");
  await mkdir(outDir, { recursive: true });

  const outcomes = await runPool(topics, (topic) => runScout(topic, datestamp, apiKey));

  // No process.exit() here: hard-exiting orphans the SDK's spawned run
  // processes while they are still closing stdio, which is exactly what
  // produces "Close event did not fire within 5000ms after exit" warnings.
  // Setting exitCode and returning lets the event loop drain and every
  // spawned runtime finish its teardown before the process ends.
  const ok = outcomes.filter((o) => o.ok).length;
  const failed = outcomes.length - ok;
  console.log(`\nScouts complete: ${ok} succeeded, ${failed} failed.`);
  console.log(`Candidate lists: ${outDir}/candidates-*-${datestamp}.md`);
  console.log(`Next step: run evaluate-agents.ts against ${outDir}`);
  if (failed > 0) process.exitCode = 2;
}

main().catch((err) => {
  console.error(err);
  process.exitCode = 1;
});
