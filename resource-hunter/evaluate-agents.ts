/**
 * evaluate-agents.ts
 *
 * Serial evaluator: one Cursor SDK agent reviews all candidate lists produced
 * by scout-agents.ts against AGENTS.md acceptance criteria, then appends
 * ACCEPT entries to the human review queue. Never edits HTML.
 *
 * Usage:
 *   CURSOR_API_KEY=cursor_... npx tsx evaluate-agents.ts [--scratch /tmp/lopp-scouts]
 */
import { readFile, readdir } from "node:fs/promises";
import path from "node:path";
import { setMaxListeners } from "node:events";

import { Agent, CursorAgentError } from "@cursor/sdk";

// Same benign warning as scout-agents.ts: the SDK registers abort listeners
// per run lifecycle on shared AbortSignals, and the evaluator's single run
// plus its internal bookkeeping can still cross Node's default cap of 10.
// One listener per live run is intentional, not a leak. Raised process-wide;
// 64 keeps the heuristic meaningful for genuine leaks.
setMaxListeners(64);

const DEFAULT_SCRATCH = "/tmp/lopp-scouts";
const QUEUE_FILE = "resources-review-queue.md";

function argValue(flag: string): string | undefined {
  const idx = process.argv.indexOf(flag);
  return idx >= 0 ? process.argv[idx + 1] : undefined;
}

const scratch = argValue("--scratch") ?? DEFAULT_SCRATCH;

async function main() {
  const apiKey = process.env.CURSOR_API_KEY;
  if (!apiKey) {
    console.error("CURSOR_API_KEY is not set");
    process.exit(1);
  }

  const files = (await readdir(scratch))
    .filter((f) => f.startsWith("candidates-") && f.endsWith(".md"))
    .sort();
  if (files.length === 0) {
    console.error(`No candidate lists found in ${scratch}. Run scout-agents.ts first.`);
    process.exit(1);
  }

  // Evaluator needs full repo context (AGENTS.md catalogs, README rules, grep
  // dedupe against existing HTML), so it runs locally with cwd = repo root —
  // same workspace the scouts read their playbook from.
  const prompt = `You are the resource EVALUATOR for lopp.net.

Inputs:
1. Read AGENTS.md sections 1 (acceptance criteria), 3-4 (page/section catalogs), and 6 (escalation).
2. Read docs/resource-sourcing.md sections 4-5 (verdict rules, queue format).
3. Candidate lists live in ${scratch}: ${files.join(", ")}. Read them all.

For every candidate, produce exactly one verdict per docs/resource-sourcing.md:
- Dedupe: grep the repo HTML (${'"'}bitcoin-information/*.html${'"'} and lightning-information.html) for the candidate's domain before judging.
- VERIFY each URL yourself before accepting; drop any that fail to resolve or are UNVERIFIED with no working alternate.
- ACCEPT requires ALL of: clears the substantially-better bar vs any similar listed resource; no referral-revenue mechanism; reputation acceptable for its category (fund-holding services need years of track record); bitcoin-first scope.
- For ACCEPTs, pick page + section using AGENTS.md's catalogs and overlap map. If two sections seem equally right, follow the overlap map; if still ambiguous, mark the entry AMBIGUOUS in the notes rather than guessing.
- If no existing section fits, do not invent one — reject with reason "no fitting section; escalate".

Output — two files, nothing else:
1. VERDICTS: append one markdown line per candidate to ${scratch}/verdicts-<today>.md: "VERDICT | <name> | ACCEPT|HOLD|REJECT | <page>.html § <section-id> | <one-line reason>".
2. QUEUE: append ACCEPTED entries in the section-4 format to ${scratch}/${QUEUE_FILE}, one block per resource: name, suggested page § section, URL, suggested annotation text, evaluator notes. Create the queue file if it does not exist.

Do NOT modify any repo file, any HTML, or anything outside ${scratch}. Reply with a one-paragraph summary: counts per verdict plus the two or three most interesting accepts.`;

  try {
    // Durable-with-follow-ups shape is unnecessary here: evaluation is a
    // single serial pass, so Agent.prompt() one-shot + wait is sufficient.
    // Serial (not Promise.all) on purpose: the evaluator reasons over the
    // union of candidate files and must stay consistent across verdicts.
    const result = await Agent.prompt(prompt, {
      apiKey,
      local: { cwd: process.cwd() },
      model: { id: "auto" },
    });

    if (result.status === "error") {
      console.error(`[run-failed] evaluator run ${result.id} — inspect transcript before rerunning.`);
      process.exit(2);
    }
    console.log(`[ok] evaluator run ${result.id}`);
    console.log("Agent summary:");
    console.log(result.result);
    console.log(`\nVerdicts:    ${scratch}/verdicts-*.md`);
    console.log(`Review queue: ${scratch}/${QUEUE_FILE}`);
    console.log("Next: human review of the queue, then merge using AGENTS.md section 2 markup.");
    process.exit(0);
  } catch (err) {
    if (err instanceof CursorAgentError) {
      console.error(
        `[startup-failed] ${err.message} (retryable=${err.isRetryable})` +
          (err.isRetryable ? " — safe to rerun this script." : " — fix auth/config first."),
      );
      process.exit(1);
    }
    throw err;
  }
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
