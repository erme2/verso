# ADR-0001: Build a reusable WordPress content transfer and static export plugin

- **Status:** Proposed
- **Date:** 2026-10-18
- **Updated:** 2026-10-19
- **Deciders:** TBD

## Context

Verso is intended to become a reusable plugin suitable for publication on WordPress.org. The same plugin should be installable on an existing WordPress site and a new WordPress install used to rebuild its frontend. Birra La Fortezza can serve as one pilot, alongside a more standard example site.

The plugin will transfer content between WordPress sites and generate static files for a frontend, including a compiled React application. The WordPress theme must not become the public site as a side effect. The new install will remain manageable through wp-admin and should work on standard WordPress hosting rather than depending on a particular provider.

## Decision

We propose building a reusable WordPress plugin that supports repeatable import and static export. The frontend will consume generated static files; a custom content REST API is not part of the current v1 direction.

### Content scope

Import and export will support:

- Pages and posts, including draft and private records.
- Media and its content relationships.
- Categories and tags.
- Configured custom post types and their fields.

Users, comments, navigation menus, and other unmentioned data are out of scope unless a later decision adds them.

### Export

- An administrator can request an export from a plugin page.
- The output is a set of static JSON files with an index file describing the package and its format version.
- Generated files are managed by the plugin, not placed in a fake theme.
- If the export includes drafts or private records, a password is required and the package is encrypted. An export containing only public published records may be generated without a password and is public.
- The files can be downloaded or published for the frontend to consume. Frontend handling of private data remains unresolved; a browser application cannot keep a decryption password secret.
- Support automated export through both WordPress cron and a protected HTTP trigger for external cron services. The trigger credential is separate from the export-encryption password and must not be sent in a query string or written to logs.

### Import

- The same Verso plugin can be installed on the source WordPress site to export content and on the destination site to import it.
- Imports are repeatable. Each exported record has a stable source identity so a later import updates the matching destination record with the latest source content rather than creating a duplicate.
- The source is authoritative for records that match. If a source record has been deleted, the importer presents that proposed deletion and requires explicit confirmation before deleting its destination copy.
- The import workflow reports unsupported content and failures clearly.

### Compatibility and hosting

- Target the current stable WordPress release and subsequent releases.
- Require PHP newer than 8.0 (PHP 8.1 or newer); establish and test the exact compatibility matrix during implementation.
- Avoid provider-specific hosting assumptions and runtime dependencies beyond what standard WordPress hosting supports.

## Options considered

| Option | Notes |
| --- | --- |
| **Reusable WordPress plugin (proposed)** | Install the same plugin on source and destination sites; provides an admin workflow and a versioned content format. |
| Store generated exports in a theme | Rejected as a storage mechanism because themes are presentation code and can be replaced or updated independently of plugin data. |
| Custom content REST API for the frontend | Deferred. Static files are the chosen v1 frontend delivery path; a future requirement may justify a REST API. |

## Consequences

### Positive

- The frontend can consume static content without depending on live WordPress requests.
- The same repeatable transfer workflow can support Birra La Fortezza and other WordPress sites.
- Source identities let operators refresh imported records while requiring confirmation for source deletions.

### Negative / risks

- A static export containing private or draft records must be protected as an encrypted package; anyone with the password can decrypt it.
- A compiled browser frontend cannot safely hold the package password. The way authorized editors preview or consume private content is not decided here.
- Public, password-free exports are publicly accessible and must contain only published public content.
- WordPress sites can differ in custom fields, plugin data, media storage, and relationships; unsupported data must not be silently lost.
- Optional S3 delivery is tracked separately and must not make exported objects public implicitly.
- We take on ongoing maintenance of the plugin and compatibility with WordPress core updates and standard hosting environments.

## Open questions

- How will editors preview draft/private content in the rebuilt frontend without exposing the decryption password to browsers?
- Which exact WordPress and PHP versions will the implementation test in CI?
- What are the final package layout and media URL conventions for static frontend consumption?
- What source-specific field mappings, if any, will the Birra La Fortezza pilot require?

## Next steps

1. Resolve the open questions that block a safe package and operator workflow; then update this ADR to **Accepted**.
2. Specify the versioned JSON/index format and implement manual export and repeatable import with focused tests.
3. Add the static-file consumption guidance for the frontend and document password handling and source-deletion confirmation.
4. Test transfers using synthetic WordPress fixtures before any real-site migration.
5. Evaluate optional automation and S3 delivery as separate scoped work.
