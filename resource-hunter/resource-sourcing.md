# Resource Sourcing Playbook

This document tells **scout agents** how to hunt for new educational resources
for lopp.net, and where to record what they find. Placement rules, section
catalogs, and markup conventions live in [AGENTS.md](../AGENTS.md) — this
playbook covers *finding* candidates, not categorizing them.

There are two roles:

- **Scout** — searches the internet for candidate resources. Reads nothing in
  the repo except this playbook.
- **Evaluator** — takes a candidate list and applies the AGENTS.md acceptance
  criteria (substantially-better rule, referral-revenue ban, reputation rule,
  dedupe-by-domain grep) before anything reaches a PR.

Scouts must NOT decide placement or edit any HTML files. Evaluators must NOT
edit HTML either — they write to the review queue only. Jameson merges.

---

## 1. Where to hunt

Ranked by hit-rate for this site's standards (established, educational,
non-commercial-leaning):

1. **Awesome lists** — `awesome-bitcoin`, `awesome-lightning-network`,
   `awesome-lnurl`, `awesome-privacy`, `awesome-cryptoeconomics`. GitHub topic
   search: `topic:bitcoin topic:awesome`. High signal; already-human-curated.
2. **Conference agendas** — previous 24 months of BTC Budapest, Bitcoin
   Amsterdam, Bitcoin Miami, Tabconf, Balaji's and Chaincode residencies,
   Brink/SPC guest lectures. Talks rarely appear in other lists.
3. **New books & documentaries** — publisher catalogs (O'Reilly, Manning,
   Bitcoin Magazine Press),比特币 film festival lineups.
4. **Podcast directories** — new bitcoin podcasts (min. 20 episodes or
   established host, per reputation rule).
5. **University courses** — public course sites with real syllabi/lectures
   (e.g. Princeton, Berkeley, Bar Ilan).
6. **Bitcoin Optech & dev mailing list** — new tooling, new papers, covenant
   and mempool-policy proposals often link primary sources before they are
   listed anywhere else.
7. **Product Hunt / Hacker News archives** — search `bitcoin` filter past
   year; most hits fail the reputation rule, but dev tools pass.
8. **Niche:** data-anchor/inscribing tools, ecash mints, LN developer tooling,
   custody/security research — these sections change fastest.

## 2. Search queries that work

Per topic family (run against a web-search tool; expect ~30–60% noise):

- Explainers: `bitcoin explained` `illustrated guide`, `visual guide bitcoin`,
  `bitcoin interactive tutorial`
- Security/custody: `multisig guide`, `metal backup`, `seed storage`,
  `coinjoin tutorial`
- Privacy: `bitcoin privacy guide`, `chain analysis techniques`
- Lightning dev: `lightning devkit tutorial`, `LSP open source`, `BOLT12
  tools`, `taproot channels`
- Economics: `bitcoin monetary policy essay`, `free book austrian economics
  bitcoin`
- Mining: `mining profitability guide`, `stratum v2 resources`
- Governance/forks: `bitcoin governance essay`, `soft fork activation`

Freshness operators help: append `after:2023` or restrict by last-2-years
where the tool supports it.

## 3. Scout output format

One markdown file per scout run, written to a **scratch directory outside
version control** (e.g. `/tmp/lopp-scouts/`), named:

```
candidates-<topic-family>-<YYYYMMDD>.md
```

Entry shape (this is the contract the evaluator parses):

```markdown
### <Resource Name>
- URL: https://...
- Type: guide|video|book|podcast|tool|service|list
- Topic guess: lightning-dev-tools | bitcoin-security | ...
- Why: <1–3 sentences: what it is, who maintains it, why it clears the bar>
- Red flags: <referral links seen? fund custody? brand-new domain? none>
```

Rules:

- 10–25 candidates per scout run. Quality over exhaustiveness.
- **No duplicates across scout files for the same domain** — scouts do not
  dedupe against the repo (evaluator's job), but must dedupe among themselves.
- Every entry needs a real URL the scout actually resolved (fetched title or
  HTTP 200) — no hallucinated links. If fetch failed, note `UNVERIFIED`.

## 4. Evaluation queue

The evaluator reads every `candidates-*.md` in the scratch directory and
produces one verdict per candidate:

- **ACCEPT** — meets every AGENTS.md criterion; evaluator suggests page +
  section (from AGENTS.md catalogs) but does NOT edit HTML.
- **HOLD** — plausible but fails one soft rule (too new, thin annotation,
  unclear maintenance status). Written to the hold file with reason.
- **REJECT** — referral-driven, duplicate of an existing entry, dead link, or
  fails the substantially-better bar. Reason recorded.

Accepted entries land in `resources-review-queue.md` in the same scratch
directory, formatted so Jameson (or a merge agent) can convert them to `<li>`
entries using the AGENTS.md section 2 template:

```markdown
## ACCEPTED <YYYY-MM-DD>
- <Resource Name> — <suggested page>.html § <suggested section id>
  - URL, annotation text, evaluator notes
```

## 5. Cadence and hygiene

- Scouts are cheap; run them per topic family, weekly or on demand.
- Evaluators run after a batch of scouts, serially (they need repo context:
  grep dedupe, README.md rules).
- The scratch directory is ephemeral. Promote anything worth keeping into the
  queue file; do not treat scratch as durable state.
- Nothing in this pipeline touches `git`. PRs stay human-approved unless
  Jameson explicitly delegates the merge step.
