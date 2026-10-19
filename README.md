# Verso

Verso is planned as a reusable WordPress plugin for repeatable content transfer and static content export. The same plugin can run on an existing WordPress source and a new destination install. The source is authoritative for synced content, so later imports overwrite destination edits to those records. A frontend such as a compiled React application consumes generated static files.

## Project status

The WordPress plugin skeleton is in place. [ADR-0001](ADR.md) records the proposed import/export direction and remaining open questions. The decision is still marked **Proposed**; import, export, encryption, and static frontend delivery have not been implemented.

## Engineering workflow

[Yaup](https://yaup.app) is the project's source of truth for work tracking and the primary engineering guardrail. All planned and active work must have a Yaup ticket. Follow the ticket's acceptance criteria, status, and review lifecycle; keep implementation scope and progress aligned with the ticket. Do not treat this README or an untracked request as a replacement for Yaup.

Repository-specific agent guidance is in [AGENTS.md](AGENTS.md). Record durable architecture decisions in ADRs and update them when decisions change.

## Planned architecture

- Install the same Verso plugin on source and destination WordPress sites: the source exports content and the destination imports it.
- Export selected pages, posts, media, categories/tags, custom post types, and fields as complete static JSON snapshots with an index. Drafts and source-trash records are omitted; users, comments, menus, and other data are out of scope unless added later.
- Imports are repeatable and source-authoritative: they overwrite destination edits to matching records and delete destination records missing from a valid full snapshot. Restored source content returns in a later snapshot.
- Exports containing private or password-protected records require the password saved in WordPress settings and encrypted output. An unencrypted index masks private titles, slugs, record details, and counts. Password-free exports contain only public published content.
- Manual export and both WordPress cron and protected HTTP-trigger automation are planned. Static files reflect the last completed export; a custom content REST API and automatic regeneration on each edit are outside the current scope.
- Front-end blocking, CORS, and core REST API lockdown are deferred. The exact PHP minimum and tested WordPress/PHP matrix remain to be established.

See [ADR-0001](ADR.md) for the proposed v1 scope, trade-offs, risks, and unresolved questions. [Content transfer](docs/content-transfer.md) describes the planned export/import workflow.

## Development

From the repository root, run `npx @wordpress/env start` to start the local WordPress environment. The Verso plugin is mounted from this repository and is available at [http://localhost:8888](http://localhost:8888). Stop the environment with `npx @wordpress/env stop`.

The environment uses [`wp-env`](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/), configured in `.wp-env.json`.

The plugin entry point is `verso.php`. Do not infer deployment settings or credentials from the architecture proposal.

## License

See [LICENSE](LICENSE).
