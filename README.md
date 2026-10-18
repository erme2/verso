# Greenroom

Greenroom is planned as a headless WordPress content API for a React application. WordPress will provide the editor experience and a custom plugin will shape and secure the REST API; the public site will be served by the React application.

## Project status

This repository is at the planning stage. [ADR-0001](ADR.md) records the proposed architecture and its open questions. The decision is still marked **Proposed**, and the implementation has not been scaffolded yet.

## Engineering workflow

[Yaup](https://yaup.app) is the project's source of truth for work tracking and the primary engineering guardrail. All planned and active work must have a Yaup ticket. Follow the ticket's acceptance criteria, status, and review lifecycle; keep implementation scope and progress aligned with the ticket. Do not treat this README or an untracked request as a replacement for Yaup.

Repository-specific agent guidance is in [AGENTS.md](AGENTS.md). Record durable architecture decisions in ADRs and update them when decisions change.

## Planned architecture

- WordPress is used for content management and REST API responses, not public theme rendering.
- A versioned custom WordPress plugin owns headless configuration, CORS, front-end blocking, custom endpoints, permissions, caching, and API surface restrictions.
- A React application consumes the API from the public site.

See [ADR-0001](ADR.md) for the proposed v1 scope, trade-offs, risks, and unresolved questions.

## Development

There are no application source files or setup commands in this repository yet. Once implementation is tracked and scaffolded, document prerequisites, local setup, configuration, and verification commands here. Do not infer deployment settings or credentials from the architecture proposal.

## License

See [LICENSE](LICENSE).
