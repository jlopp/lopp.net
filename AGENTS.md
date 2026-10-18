# AGENTS.md — Resource Curation Guide

This repository is the source for [lopp.net](https://www.lopp.net), Jameson Lopp's
personal site. Its heart is a hand-curated set of educational resource pages. This
guide exists so that AI agents (and human contributors) can find new educational
resources, decide whether they belong on the site, and place them in the correct
page and section.

**Curation targets:** every page in `/bitcoin-information/*.html` (39 pages) and
`/lightning-information.html`. Nothing else on the site is a resource list.

---

## 1. Acceptance criteria

These rules come from [README.md](README.md) and long-standing site practice.
Judgement is ultimately Jameson's call, but apply these filters before proposing
anything:

- **Substantially better, or it doesn't ship.** A candidate that merely
  duplicates an existing entry (similar topic, similar quality, similar format)
  is rejected. It must be better in some concrete way: more accurate, more
  current, more comprehensive, better maintained, better presented, or serving a
  clearly distinct audience or niche.
- **No referral-revenue mechanisms.** Resources built primarily to earn referral
  commissions or affiliate revenue are rejected, even if the content is decent.
  A referral link buried in otherwise-useful content is a yellow flag; a site
  whose business model *is* referrals is a hard no.
- **Reputation before listing.** Brand-new services (especially exchanges,
  wallets, lending platforms, and anything holding user funds) are held
  "indefinitely" until they have proven they are not scams. Prefer resources
  with an established multi-year reputation. Unproven but promising technical
  tools (open source, no custody of funds) are a lower risk.
- **Bitcoin-first.** The bitcoin pages list bitcoin-focused resources. General
  crypto/blockchain resources only fit in the few sections that explicitly
  include them (e.g. `other-resources.html` → "Blockchain & Crypto Resources").
- **Educational value.** Every page's purpose is education. Marketing pages,
  landing pages, and thin content do not qualify, no matter how polished.
- **Check for duplicates first.** Before adding anything, grep the repo
  (`grep -ri "domain.com" bitcoin-information/ lightning-information.html`) to
  confirm the resource, or a close equivalent, is not already listed — possibly
  under a different display name or on a different page.
- **No dead or unstable links.** Verify the URL resolves (prefer HTTPS), and
  prefer archiving unstable pages via web.archive.org when the original is
  flaky — several existing entries do exactly that.

## 2. Technical conventions

All resource pages share the same template. Follow it exactly.

### Anatomy of a resource page

- Two-column Bootstrap grid: `<div class="row">` containing two
  `<div class="text-left col-xs-12 col-sm-6 col-lg-6 col-md-6">` columns.
  The second column is preceded by the comment `<!-- RIGHT COLUMN -->`.
- Sections are `<h2>` headings with an anchor link:
  `<h2 id="snake_case_id"><a href="#snake_case_id">Section Name:</a></h2>`
  Note the trailing colon on most section names (exceptions exist, e.g.
  lightning's `research` heading "Research" has no colon).
- Section IDs are `snake_case` (e.g. `getting_started`, `technical_resources`).
- Resources live in `<ul>` lists immediately under their `<h2>`. On
  `lightning-information.html`, sections are separated by `<br>` rather than
  living in separate columns.
- Entries are ordered roughly alphabetically by display name. Insert new
  entries in alphabetical position unless the section is clearly ordered
  another way.

### Anatomy of a list entry

```html
<li><a href="https://example.com/" title="Example Title" target="_blank" rel="noopener">Example Title</a> (short annotation)</li>
```

- `title` attribute = page/site title (often the author or org name).
- `target="_blank" rel="noopener"` on every external link — mandatory.
- Optional parenthetical annotation after the link text explains what makes it
  distinctive ("for Core Lightning", "Android & iOS", "annotated").
- When several entries would share one display name, suffix numbers
  ("Invoice Decoder 2", "LND channel rebalancer 3") or add an author note.
- Internal cross-links between resource pages use relative URLs (e.g.
  `<li><a href="security.html" title="Security">Secure Your Wallet</a></li>`);
  links to local PDFs use absolute site paths (`/pdf/bitcoin.pdf`).

### Adding a new link — checklist

1. Pick the page and section (see catalog below).
2. Verify the resource is not already listed anywhere in the repo.
3. Insert an `<li>` in alphabetical position within the section's `<ul>`.
4. Match the exact markup above (`target="_blank" rel="noopener"`).
5. No `sitemap.xml` change is needed — it lists pages, not individual links.
6. Validate: run `python3 check_links.py -ff` (fast mode; add `-f` for
   thoroughness) to confirm no broken links were introduced. Requests must be
   installed: `pip install bs4 fake_headers requests tqdm`.
7. Commit with a message naming the resource and the page/section it went into.

### Common pitfalls

- Do not create new pages or new `<h2>` sections on your own initiative. If a
  resource fits nowhere, leave it out and note it in the PR description.
- Do not reorder or "clean up" existing entries; keep diffs minimal.
- Do not remove web.archive.org wrappers — they are intentional.
- Column balance is cosmetic; Jameson occasionally rebalances columns when a
  section grows. Don't move sections between columns as part of link additions.

---

## 3. Full catalog — `/bitcoin-information/` (39 pages)

Each entry lists the page, its sections verbatim (heading text as it appears),
and placement guidance. Section names below omit the trailing colon for
readability but they exist in the markup.

### Index / navigation

`/bitcoin-information.html` is the hub page linking all categories with
descriptions. New links go into the leaf pages only; the hub never lists
individual resources.

### The pages

**[getting-started.html](bitcoin-information/getting-started.html)** — Sections:
First Steps; How it Works; The Whitepaper; Misconceptions; Why It's Hard to
Understand; Why Bitcoin Matters.
Guidance: This is the beginner on-ramp and mixes internal cross-links with
external links. First Steps ≈ internal navigation (wallets, security, buying)
plus a couple of evergreen external reads — very selective. How it Works takes
beginner explainer content: videos, illustrated guides, interactive sites. The
Whitepaper is essentially closed (the whitepaper itself, annotations, readings,
translations, errata). Misconceptions takes rebuttals of common critiques. Why
It's Hard to Understand / Why Bitcoin Matters take essays on epistemology and
value proposition. New beginner explainers also compete with the
`bitcoin-explained.html` guide — prefer upgrading that guide over adding
near-duplicate explainers.

**[bitcoin-explained.html](bitcoin-information/bitcoin-explained.html)** — An
illustrated, self-contained guide (not a link list). Out of scope for link
additions; do not add resources here.

**[recommended-wallets.html](bitcoin-information/recommended-wallets.html)** —
Sections: Wallet Basics; Wallet Comparisons; Recommended Wallets; Wallet
Backups; Key Storage Hardware; Wallet Recovery Services; Wallet Recovery Tools.
Guidance: Recommended Wallets is the core list — hardware/software wallets with
a strong track record only (new wallets face the reputation rule hard). Wallet
Comparisons takes sites that compare many wallets. Key Storage Hardware takes
metal backup devices etc. Wallet Recovery Services vs Wallet Recovery Tools:
services = companies you pay to recover; tools = software/self-serve. Note the
overlap: "Wallet Backups" also appears on `security.html`; backups guidance
tends to live here, security-focused backup checklists there.

**[full-node.html](bitcoin-information/full-node.html)** — Sections: Running a
Node; DIY Guides; Full Node Dashboards; Full Node Software; Alpha / Unmaintained
Full Node Software; Plug & Play Node Hardware.
Guidance: Running a Node = why/how-to educational links (incl. node stats like
listening nodes count). DIY Guides = build-your-own guides. Full Node Software
= maintained packages (Core, Knots, etc.); anything unmaintained/alpha goes to
Alpha / Unmaintained instead of being added to the main list. Plug & Play =
commercial appliances.

**[security.html](bitcoin-information/security.html)** — Sections: General
Security; Bitcoin Security Guides; Security Checks; Wallet Backups; Cold Storage
Guides; Bitcoin Security Disclosures; Known Scams.
Guidance: General Security = non-bitcoin-specific opsec. Bitcoin Security
Guides = bitcoin-specific. Security Checks = tools to verify downloads
(signatures, checksums). Cold Storage Guides = step-by-step storage setups.
Known Scams = sites documenting scam patterns. Wallet Backups overlaps with
recommended-wallets.html — new backup resources usually belong there; only
security-angle backup content goes here.

**[buying-earning.html](bitcoin-information/buying-earning.html)** — Sections:
Buying Bitcoins; Privacy Preserving Exchanges; Lending Bitcoins; Earning
Bitcoins; Escrow Services; Automatic Cost Averaging; "Sats Back" Programs;
Buying & Selling Strategies; Retirement Accounts.
Guidance: Buying = exchanges/on-ramps (reputation rule critical). Privacy
Preserving Exchanges = KYC-free/low-KYC venues specifically. Lending = BTC
collateralized lending platforms. Earning = get-paid-in-BTC platforms & job
boards that pay in BTC (career-type job boards go to careers.html). DCA
platforms → Automatic Cost Averaging. Cashback/rewards → "Sats Back".

**[fee-estimates.html](bitcoin-information/fee-estimates.html)** — Sections:
Transaction Fee Estimates.
Guidance: Single-section page for fee estimation APIs/sites. High bar: the page
already lists the majors.

**[privacy.html](bitcoin-information/privacy.html)** — Sections: Bitcoin
Privacy Guides; Privacy Software; Alternative Comms Channels; General Privacy;
Anonymous SIM / SMS.
Guidance: Bitcoin Privacy Guides = bitcoin-specific privacy education. Privacy
Software = mixing/coinjoin and bitcoin privacy tooling. Alternative Comms
Channels & General Privacy & Anonymous SIM/SMS = non-bitcoin privacy
infrastructure (messaging, networks, anonymous numbers). A new resource about
bitcoin coinjoin → Privacy Software; about opsec in general → General Privacy.

**[history.html](bitcoin-information/history.html)** — Sections: The History of
Bitcoin; Logs & Transcripts; Satoshi Nakamoto; Security Incidents; Books;
Scaling Debate; Spam Debate.
Guidance: The History of Bitcoin = narrative histories & timelines. Logs &
Transcripts = chat logs, forum posts, mailing list archives. Satoshi Nakamoto =
Satoshi's writings & investigations. Security Incidents = post-mortems of
hacks/thefts. Books = history-focused books (general books → books.html).
Scaling Debate & Spam Debate = archives of those specific controversies.

**[books.html](bitcoin-information/books.html)** — Sections: Technical Books;
Non-technical Books; Bitcoin History; Economics & Money; Freedom.
Guidance: Route by the book's primary lens: protocol/engineering → Technical
Books; general-audience explainers & investing → Non-technical Books;
chronicles/histories → Bitcoin History; monetary economics → Economics & Money
(also check economics.html which has its own Books + Free Books sections —
economics books can land in either; books.html is the default for published
books, economics.html "Free Books" for freely-available works);
censorship/freedom themes → Freedom.

**[economics.html](bitcoin-information/economics.html)** — Sections: Economics;
Books; Free Books; Statistics.
Guidance: Economics = essays/articles on bitcoin economics & Austrian
economics. Books/Free Books = money & economics titles (see books.html
guidance). Statistics = historical/monetary data sources (live network stats →
statistics-metrics.html).

**[investment-theses.html](bitcoin-information/investment-theses.html)** —
Sections: Investment Theses; Corporate Treasuries; Value Models; On Altcoins.
Guidance: Investment Theses = long-form "why BTC" pieces. Corporate Treasuries
= companies holding BTC + treasury strategy trackers. Value Models = on-chain
or stock-to-flow style valuation models. On Altcoins = skeptical analyses of
altcoins (not general crypto resources).

**[trading.html](bitcoin-information/trading.html)** — Sections: Advanced
Trading; Charts & Metrics; Trading Bots; Trading Terminals; Trading Simulators.
Guidance: Advanced Trading = exchanges & trading education (the hub page warns
amateurs off). Charts & Metrics = charting/analytics platforms (network-wide
metrics → statistics-metrics.html; exchange rate feeds → exchange-data.html).
Bots = automated trading software. Terminals = pro trading UIs. Simulators =
paper trading.

**[exchange-data.html](bitcoin-information/exchange-data.html)** — Sections:
Exchange Data; Exchange Rate APIs; Price Indexes; ETFs.
Guidance: Exchange Data = market data aggregators/dashboards. APIs = programmatic
price feeds. Price Indexes = reference rates. ETFs = spot BTC ETF listings &
trackers.

**[statistics-metrics.html](bitcoin-information/statistics-metrics.html)** —
Sections: Blockchain Statistics; Node Statistics; Miscellaneous Metrics.
Guidance: Blockchain Statistics = on-chain data platforms. Node Statistics =
node counts/geography (also reachable from full-node.html). Miscellaneous
Metrics = everything else (lightning stats live on the lightning page).

**[mining.html](bitcoin-information/mining.html)** — Sections: Mining Info;
Mining Guides; Cloud Mining / Hosting Services; Energy Consumption; Other
Perspectives.
Guidance: Mining Info = industry info/sites. Mining Guides = how-to mining
guides. Cloud Mining / Hosting is heavily scam-prone — reputation rule at
maximum; the hub page explicitly warns against cloud mining. Energy
Consumption = energy-use analyses (CBECI etc.). Other Perspectives = contrarian
or critical mining takes.

**[developer-tools.html](bitcoin-information/developer-tools.html)** —
Sections: Blockchain Querying; Electrum Servers; Developer Tools; Bitcoin
Scripting; Programming Libraries; Signed Message Verifiers; Testnet Network
Info & Faucets.
Guidance: Blockchain Querying = RPC/explorer APIs & query services. Electrum
Servers = electrum server implementations & public lists. Developer Tools =
general dev utilities (parsers, PSBT tools, testing frameworks). Bitcoin
Scripting = script/miniscript playgrounds (miniscript-specific also has a home
on technical-resources.html). Programming Libraries = language SDKs. Signed
Message Verifiers = verification UIs. Testnet/faucets = self-explanatory.

**[technical-resources.html](bitcoin-information/technical-resources.html)** —
Sections: Technical Resources; Covenants; Miniscript Resources; Technical
Classes; Technical Books; Academic Research; Design; Contributing to Bitcoin
Core; Quantum Computing.
Guidance: Technical Resources = the general protocol documentation hub (BIPs,
Bitcoin Wiki, bitcoinops, etc.). Covenants = covenant proposal write-ups &
discussions. Miniscript Resources overlaps developer-tools.html "Bitcoin
Scripting" — miniscript-specific material goes here. Technical Classes = deep
technical courses (beginner courses → online-offline-classes.html). Technical
Books overlaps books.html "Technical Books" — canonical protocol books live in
both; default new technical books to books.html. Academic Research = papers &
journals. Design = protocol design docs (e.g. wallet design). Contributing to
Bitcoin Core = contribution guides, PR review clubs, bcore resources. Quantum
Computing = quantum-threat analyses.

**[block-explorers.html](bitcoin-information/block-explorers.html)** —
Sections: Block Explorers; Blockchain Analysis; Open Source Block Explorers;
Other Tools.
Guidance: Block Explorers = mainstream explorers. Open Source Block Explorers =
self-hostable explorers. Blockchain Analysis = chain-analysis firms &
research (privacy-counterpoint material). Other Tools = broadcast APIs,
mempool tools, etc.

**[visualizations.html](bitcoin-information/visualizations.html)** — Sections:
Realtime Transactions; Price Visualizations; Misc Visualizations.
Guidance: Realtime = live transaction visualizers. Price = price art/history
visuals. Misc = everything else visual (block marches, opinion visuals).

**[video-presentations.html](bitcoin-information/video-presentations.html)** —
Sections: Presentation Compilations; Conference Presentations; Meetup
Presentations.
Guidance: Presentation Compilations = multi-talk channels/playlists.
Conference = individual conference sites/recordings. Meetup = meetup-group
recordings. Individual YouTube talks are rare here — prefer compilations; new
conference = new section owner decision (escalate).

**[documentaries.html](bitcoin-information/documentaries.html)** — Sections:
Documentaries.
Guidance: Single-section page; full-length bitcoin documentaries only. Trailer
pages and video essays → video-presentations.html or getting-started "How it
Works".

**[podcasts.html](bitcoin-information/podcasts.html)** — Sections: Audio
Podcasts; Video Podcasts.
Guidance: Split purely by format. Active shows only; dead shows get removed
over time, so prefer currently-publishing podcasts.

**[blogs.html](bitcoin-information/blogs.html)** — Sections: Technical; Less
Technical.
Guidance: Two-column split by technical depth. Personal/company blogs with
consistent bitcoin technical output → Technical; commentary/analysis blogs →
Less Technical. Dead blogs are periodically pruned; require recent-ish
activity or historical importance.

**[news.html](bitcoin-information/news.html)** — Sections: News Sites;
Newsletters.
Guidance: News Sites = general bitcoin news orgs (no pay-to-play sites).
Newsletters = email/substack newsletters. Lightning-specific news → lightning
page's News Sites.

**[discussion-forums.html](bitcoin-information/discussion-forums.html)** —
Sections: Discussion Forums.
Guidance: Single-section page: reddit, IRC, mailing lists, Discord/Telegram,
nOSTR communities. Lightning-specific communities → lightning page's Discussion
Forums.

**[twitter.html](bitcoin-information/twitter.html)** — Sections: X Lists.
Guidance: Only curated X/Twitter *lists*, not individual accounts.

**[online-offline-classes.html](bitcoin-information/online-offline-classes.html)**
— Sections: Non-technical Online Classes; Technical Online Classes; Technical
Offline Classes; Knowledge Tests.
Guidance: Route by audience level then format. Offline = in-person
bootcamps/residencies. Knowledge Tests = quizzes/certification games. Deep
technical courses could alternatively fit technical-resources.html "Technical
Classes" — advanced/professional material there, structured learning paths
here.

**[careers.html](bitcoin-information/careers.html)** — Sections: Job Postings;
Internships; Developer Grants; Physical Community Spaces; Venture Capital.
Guidance: Job Postings = job boards (individual companies are not listed).
Internships = internship programs. Developer Grants overlaps
charity.html "Bitcoin Developer Grants" — funding organizations appear on both;
add new grant programs to both pages to stay consistent. Physical Community
Spaces = physical bitcoin co-working spaces. Venture Capital = VC firms
investing in bitcoin.

**[charity.html](bitcoin-information/charity.html)** — Sections: Lists of
Charities & Platforms; Bitcoin Developer Grants; Bitcoin Accepting Charities.
Guidance: Lists of Charities = directories/aggregators. Bitcoin Accepting
Charities = individual charities that accept BTC directly (reputation rule
applies). Developer Grants — see careers.html overlap note.

**[merchant-adoption.html](bitcoin-information/merchant-adoption.html)** —
Sections: Where to Spend Bitcoins; Gift Cards; Bitcoin Debit Cards; Merchant
Payment Processing; BTCPay Integrations; Online markets.
Guidance: Where to Spend = merchant directories. Gift Cards = gift-card
platforms. Debit Cards = BTC-backed cards. Payment Processing = processors
(non-lightning-specific; lightning processors → lightning page Merchant
Tools). BTCPay Integrations = plugins specifically for BTCPay Server. Online
markets = marketplaces.

**[tax-accounting.html](bitcoin-information/tax-accounting.html)** — Sections:
Tax Accounting Software; Tax Accounting Professionals; Appraisers.
Guidance: Software = crypto tax software. Professionals = firms/CPAs
specializing in crypto. Appraisers = valuation/appraisal services.

**[legal.html](bitcoin-information/legal.html)** — Sections: Legal News; Global
Regulations; Law Firms.
Guidance: Legal News = regulatory reporting. Global Regulations = per-country
regulatory trackers. Law Firms = bitcoin-specialty firms. Lightning regulatory
pieces → lightning page Legal.

**[governance.html](bitcoin-information/governance.html)** — Sections:
Governance.
Guidance: Single-section page on bitcoin's informal governance process.
Protocol-specific governance debates usually belong in history.html debate
sections instead.

**[forks.html](bitcoin-information/forks.html)** — Sections: BTC / BCH
Comparisions; All Bitcoin Forks.
Guidance: Comparisons = BCH-vs-BTC analyses (note the heading's typo
"Comparisions" — it is intentional; do not "fix" it casually). All Bitcoin
Forks = fork listings/trackers.

**[other-layers.html](bitcoin-information/other-layers.html)** — Sections:
Lightning Network; Sidechains; Sidechain Platforms; Drivechains; Statechains;
Softchains; Spacechains; Spiderchains; BitVM; Discreet Log Contracts; Ecash;
Rollups; Other Anchored Chains; Identity Protocols; Other Protocols;
Cross-Chain Bridges.
Guidance: One section per protocol family. Lightning Network here is just a
pointer to the lightning page — do not add lightning resources on this page.
Route new layer-2/layer-n protocols by their exact family; genuinely novel
protocols → Other Protocols. Ecash = Chaumian ecash/mint implementations.
Identity Protocols = DID/attestation layers.

**[art-music.html](bitcoin-information/art-music.html)** — Sections: Fine Art;
Digital Art & NFTs; Accessories & Apparel; Memes; Music.
Guidance: Route purely by medium. Note the site's general skepticism of NFTs —
Digital Art & NFTs is a small, curated list.

**[data-anchor.html](bitcoin-information/data-anchor.html)** — Sections: Learn;
View Data Anchors; Create Data Anchors.
Guidance: Learn = educational write-ups on data anchoring/inscribing. View =
explorers for anchored data. Create = tools to anchor data. This is a niche,
curated page — high bar.

**[other-resources.html](bitcoin-information/other-resources.html)** —
Sections: Bitcoin Resources; Other Languages; Blockchain & Crypto Resources.
Guidance: Bitcoin Resources = other curated bitcoin resource lists.
Other Languages = non-English resource collections. Blockchain & Crypto
Resources = the only place general crypto resources are listed.

---

## 4. Full catalog — `/lightning-information.html`

Single-column-ish page (sections flow across two columns), sections separated
by `<br>`. Sections in page order, with guidance:

1. **Getting Started** — beginner explainers of Lightning. Route newbie LN
   content here.
2. **History** — LN history pieces (early proposals, Lightning Torch).
3. **Miscellaneous Statistics** — network statistics dashboards (1ml,
   bitcoinvisuals).
4. **Deep Dive Technical Resources** — whitepaper, BOLTs, curricula, books.
   Protocol specs and education.
5. **Developer Documentation** — per-implementation docs & guides (LND docs,
   WebLN, etc.). vs section 4: section 4 = protocol-level; this =
   implementation/API docs.
6. **Developer Tools** — libraries, invoice decoders, test-network harnesses,
   simulators, gRPC/REST wrappers, plugins.
7. **Node Management Tools** — node dashboards, rebalancers, fee managers,
   watchtowers, automation (balanceofsatoshis, RTL, Thunderhub, etc.).
8. **Liquidity & Routing** — LSPs, liquidity marketplaces, swap services,
   channel-management guides, liquidity swap communities.
9. **Desktop Wallets** — desktop/browser-extension wallets. Cross-listed with
   Mobile when both exist (same entry repeated on both sections in some
   cases).
10. **Mobile Wallets** — mobile wallets (Android/iOS noted in annotation).
11. **Custodial Wallets** — custodial LN wallets; note in page warns about the
    trade-off. Non-custodial candidates go to 9/10.
12. **Node Implementations** — the major implementations (Core Lightning,
    Eclair, LND, LNP, Rust Lightning). Extremely high bar.
13. **DIY Nodes** — self-hosting guides (Raspibolt, Raspiblitz, Umbrel...).
14. **Plug & Play Nodes** — turnkey boxes & hosted node services.
15. **Explorers** — LN explorers & graph browsers.
16. **Visualizations** — LN-specific visual art/graphs of the network.
17. **Documentaries** — LN documentaries.
18. **Video Presentations** — LN talk videos & playlists.
19. **News Sites** — LN-tagged news & newsletters (e.g. Lightning News,
    Simple Lightning Newsletter). General bitcoin news → bitcoin news.html.
20. **Podcasts** — LN-focused podcasts.
21. **Blogs** — LN-focused blogs (companies & individuals).
22. **Discussion Forums** — LN communities: subreddit, dev mailing list, IRC,
    Slacks, Telegrams.
23. **Twitter** — curated LN Twitter/X lists (just one entry currently).
24. **Applications** — consumer lapps & app directories.
25. **Earn Satoshis** — earn-sats-via-LN platforms (faucets, podcast apps,
    microtask sites).
26. **Gaming** — LN games & game platforms. THNDR Games appears in both 25
    and 26 — earning-first vs game-first is the tiebreak.
27. **Tipping** — tipping bots/tools for social platforms.
28. **Merchant Adoption** — directories of LN-accepting merchants.
29. **Merchant Tools** — LN payment processors, PoS systems, e-commerce
    gateways, ATM hardware. Bitcoin-side processors → merchant-adoption.html.
30. **Trading** — exchange-with-LN-support lists.
31. **Economics** — LN economics pieces (routing fees, LN reference rate).
32. **Legal** — LN regulatory analyses.
33. **Research** — research orgs & research-topic sites (jamming, privacy).
34. **Other Resource Lists** — other curated LN lists (awesome lists etc.).

---

## 5. Categorization workflow

Given a candidate resource, work through this decision tree:

```
1. What IS it?
   a. Educational content (guide, video, book, podcast, paper) → step 2
   b. A tool/software → step 3
   c. A service/company (exchange, wallet, processor, casino, etc.) → step 4
   d. A directory/list of other resources → usually "Other Resources" /
      "Other Resource Lists" of the relevant page family

2. Educational content → pick medium, then topic:
   - Video: documentary → documentaries.html; conference/meetup talk →
     video-presentations.html; beginner explainer → getting-started.html
   - Text: blog → blogs.html; book → books.html; essay/article → the topic
     page that matches its subject
   - Audio: podcast → podcasts.html
   - Then: is it bitcoin-first? LN-specific → lightning-information.html

3. Tools/software → the page matching what the tool DOES:
   - Bitcoin dev utility → developer-tools.html
   - Runs a node / node management → full-node.html (bitcoin) or lightning
     Node Management Tools
   - Privacy tooling → privacy.html
   - Wallet → recommended-wallets.html (bitcoin) or lightning wallets
   - Tax → tax-accounting.html
   - Trading → trading.html
   - Anchoring data → data-anchor.html
   - Implements LN → lightning Node Implementations
   - Layer-2 protocol → other-layers.html

4. Services → the page matching who it serves:
   - On-ramp/lending/DCA → buying-earning.html
   - Merchant-facing → merchant-adoption.html (LN → lightning Merchant Tools)
   - Tax/legal help → tax-accounting.html / legal.html
   - Jobs/grants → careers.html (+ charity.html if a dev grant)
   - Holds user funds? → apply reputation rule strictly

5. Placement within the page:
   - Find the most SPECIFIC matching section; fall back to the most general.
   - Check the sibling pages for the same topic (per the overlap notes in
     section 3) and prefer the canonical page listed there.
   - Grep the repo for the domain to dedupe.

6. If no existing section fits:
   - Do NOT create a new page, section, or column.
   - Skip the resource and note it in the PR description for Jameson.
```

### Lightning vs Bitcoin page routing

If a resource is Lightning-specific, it almost always goes on
`lightning-information.html`, even when the bitcoin pages have a sibling
section (news, podcasts, blogs, forums, merchant tools, wallets). Exceptions:
general bitcoin education that merely mentions LN stays on the bitcoin pages.

### Overlap map (canonical placement)

| Topic | Canonical page | Also appears on |
| --- | --- | --- |
| Developer grants | careers.html + charity.html (add to BOTH) | — |
| Wallet backups | recommended-wallets.html | security.html |
| Technical books | books.html | technical-resources.html |
| Miniscript | technical-resources.html | developer-tools.html |
| Economics books | economics.html (free ones) / books.html (published) | — |
| LN news/podcasts/blogs/forums | lightning-information.html | news.html etc. (bitcoin-general only) |
| Lightning section on other-layers.html | pointer only — never add links there | — |
| Node stats | statistics-metrics.html | full-node.html |
| Exchange rate feeds | exchange-data.html | trading.html (charting) |

---

## 6. Escalation

- Resource fits nowhere cleanly → skip it, mention it in the PR description.
- Two sections seem equally right → pick per the overlap map; if still
  ambiguous, note the reasoning in the PR description.
- You believe a new page/section is genuinely warranted → propose it in the
  PR description only; do not implement unilaterally.
- Judgement calls (reputation, "substantially better") → when in doubt, leave
  the resource out. Jameson reviews every PR.
