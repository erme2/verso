# Verso

Verso is planned as a headless WordPress content API for a React application. WordPress will provide the editor experience and a custom plugin will shape and secure the REST API; the public site will be served by the React application.

## Project status

The WordPress plugin skeleton is in place. [ADR-0001](ADR.md) records the proposed architecture and its open questions. The decision is still marked **Proposed**; the API, settings, CORS, and front-end behavior have not been implemented.

## Engineering workflow

[Yaup](https://yaup.app) is the project's source of truth for work tracking and the primary engineering guardrail. All planned and active work must have a Yaup ticket. Follow the ticket's acceptance criteria, status, and review lifecycle; keep implementation scope and progress aligned with the ticket. Do not treat this README or an untracked request as a replacement for Yaup.

Repository-specific agent guidance is in [AGENTS.md](AGENTS.md). Record durable architecture decisions in ADRs and update them when decisions change.

## Planned architecture

- WordPress is used for content management and REST API responses, not public theme rendering.
- A versioned custom WordPress plugin owns headless configuration, CORS, front-end blocking, custom endpoints, permissions, caching, and API surface restrictions.
- A React application consumes the API from the public site.

See [ADR-0001](ADR.md) for the proposed v1 scope, trade-offs, risks, and unresolved questions.

## Development

From the repository root, run `npx @wordpress/env start` to start the local WordPress environment. The Verso plugin is mounted from this repository and is available at [http://localhost:8888](http://localhost:8888). Stop the environment with `npx @wordpress/env stop`.

The environment uses [`wp-env`](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/), configured in `.wp-env.json`.

The plugin entry point is `verso.php`. Do not infer deployment settings or credentials from the architecture proposal.

## License

See [LICENSE](LICENSE).
