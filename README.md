# Verso

Verso is planned as a reusable WordPress plugin for repeatable content transfer and static content export. The same plugin can run on an existing WordPress source and a new destination install. The destination remains the editor experience, while a frontend such as a compiled React application consumes generated static files.

## Project status

The WordPress plugin skeleton is in place. [ADR-0001](ADR.md) records the proposed import/export direction and remaining open questions. The decision is still marked **Proposed**; import, export, encryption, and static frontend delivery have not been implemented.

## Engineering workflow

[Yaup](https://yaup.app) is the project's source of truth for work tracking and the primary engineering guardrail. All planned and active work must have a Yaup ticket. Follow the ticket's acceptance criteria, status, and review lifecycle; keep implementation scope and progress aligned with the ticket. Do not treat this README or an untracked request as a replacement for Yaup.

Repository-specific agent guidance is in [AGENTS.md](AGENTS.md). Record durable architecture decisions in ADRs and update them when decisions change.

## Planned architecture

- Install the same Verso plugin on source and destination WordPress sites: the source exports content and the destination imports it.
- Export supported pages, posts, media, categories/tags, custom post types, and fields as static JSON files with an index. User accounts, comments, menus, and other data are out of scope unless added later.
- Imports are repeatable and update matching destination records from the latest source version. The plugin asks for confirmation before deleting destination records whose source records were deleted.
- An administrator requests exports from the plugin page. Exports containing draft or private records require a password and are encrypted; an export without a password is public and must contain only published public content.
- The frontend consumes generated static files rather than relying on a custom content REST API for v1. How authorized users preview or consume private content in a browser remains open; a browser cannot keep a decryption password secret.
- Target the current stable WordPress release and subsequent releases, with PHP newer than 8.0, and avoid provider-specific hosting assumptions. The exact tested version matrix is still to be established.

See [ADR-0001](ADR.md) for the proposed v1 scope, trade-offs, risks, and unresolved questions. [Content transfer](docs/content-transfer.md) describes the planned export/import workflow.

## Development

From the repository root, run `npx @wordpress/env start` to start the local WordPress environment. The Verso plugin is mounted from this repository and is available at [http://localhost:8888](http://localhost:8888). Stop the environment with `npx @wordpress/env stop`.

The environment uses [`wp-env`](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/), configured in `.wp-env.json`.

The plugin entry point is `verso.php`. Do not infer deployment settings or credentials from the architecture proposal.

## License

See [LICENSE](LICENSE).
