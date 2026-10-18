# Agent guidance

## Source of truth and guardrails

- Yaup is the primary source of truth for project work and the engineering standard. Before making implementation changes, locate the relevant Yaup ticket and read its description, acceptance criteria, status, and linked context. If no ticket exists, create or request one through the project's established Yaup workflow before starting implementation.
- Keep changes within the ticket's agreed scope. If new requirements or architectural choices emerge, update the ticket and, when appropriate, the relevant ADR before relying on them.
- Use the Yaup lifecycle for status transitions, review, and handoff. Do not mark work complete while acceptance criteria or required validation remain outstanding.
- A user request can authorize the work, but it does not override applicable Yaup acceptance criteria or project review requirements. Surface conflicts in the ticket and resolve them through the established workflow.
- Treat this file as repository-specific guidance, subordinate to the applicable Yaup process and engineering standards.

## Working in this repository

- Read [README.md](README.md) and relevant ADRs before changing architecture or adding setup instructions.
- [ADR-0001](ADR.md) is **Proposed**. Do not present its open decisions as settled; update the ADR when the decision is accepted or revised.
- The intended product is a headless WordPress REST API consumed by a React application. WordPress theme output must not become the public site as a side effect of implementation.
- Keep headless behavior in the planned custom plugin rather than ad hoc theme edits, unless an accepted ADR or Yaup ticket changes that direction.
- Apply least privilege to REST routes and configuration. Every custom route needs an explicit permission callback; CORS origin checks alone are not authentication. Never commit secrets or expose private credentials in client-side code.
- Preserve access to `wp-admin`, login, REST, Ajax, and cron when implementing front-end blocking. Verify those paths as required by the ticket.
- Do not invent commands, runtime versions, deployment details, environment variables, or integrations. Document them only after they are established in the repository or the relevant Yaup ticket.

## Changes and validation

- Make the smallest coherent change that satisfies the ticket's acceptance criteria.
- Follow existing repository conventions as they are established. Keep architecture decisions in ADRs and operational setup in README.md.
- Run the validation required by the ticket and applicable Yaup standards. Report what ran and any checks that could not be run; do not claim unperformed validation.
- Review the final diff for scope, security, and documentation consistency before handing the work back through Yaup.
