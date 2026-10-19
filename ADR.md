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

- Pages and posts selected for frontend publication. Draft records are omitted from snapshots. Private and password-protected records may be included only in an encrypted package.
- Media and its content relationships.
- Categories and tags.
- Configured custom post types and their fields.

Users, comments, navigation menus, and other unmentioned data are out of scope unless a later decision adds them.

### Export

- An administrator can request an export from a plugin page.
- Every export is a complete snapshot of the selected content and declares that it is a full snapshot. A record absent from a valid full snapshot is considered absent from the source; source trash and drafts are not exported. Restoring a source record makes it appear in the next snapshot.
- The output is a set of static JSON files with an index file describing the package and its format version. The unencrypted index must not disclose private record titles, slugs, record data, or counts. Any private values shown there use `***`, without per-record rows or counts that reveal how many private records exist.
- Generated files are managed by the plugin, not placed in a fake theme.
- If the export includes private or password-protected records, a password is required and the package is encrypted. An export containing only public published records may be generated without a password and is public. Automated exports use the password saved in WordPress settings.
- Encryption must use a vetted authenticated-encryption implementation and a password-based key derivation function. Each package gets a fresh random salt. The exact algorithms, parameters, and encrypted package/index layout remain to be specified.
- The files can be downloaded or published for a frontend to consume and decrypt. Frontend behavior after decryption is outside this plugin's scope. The unencrypted index must not leak private metadata.
- Support manual export, WordPress cron, and a protected HTTP trigger for external cron services. The trigger credential is separate from the export-encryption password, is sent only in an authorization header, and is stored only as a one-way hash. Never put it in a query string or logs.
- Automatic regeneration on content changes is not part of the current direction; static files reflect the last completed export.

### Import

- The same Verso plugin can be installed on the source WordPress site to export content and on the destination site to import it.
- Imports consume complete snapshots. For each record, the source is authoritative: a later import overwrites destination edits to matching records. Operators should make ongoing edits to synced records at the source.
- Each record needs a stable source identity so imports update rather than duplicate it. A per-install source UUID plus source record ID, stored with the destination record, is a candidate that supports multiple sources; the final identity and conflict policy remain to be specified.
- A destination record absent from a valid full snapshot is deleted during import. A record restored at the source returns in a later snapshot. The exact WordPress deletion mode (Trash or permanent deletion) remains open.
- Import is an untrusted-input boundary. It must check an authorized capability and nonce, reject unsupported package versions, validate the package against a schema, and sanitize imported content according to the importing user's capabilities, including `unfiltered_html` behavior.
- Media import must enforce allowed file types and size limits and must not fetch arbitrary URLs. The import workflow reports unsupported content and failures clearly.
- How a package reaches the destination (manual upload, authenticated source download, or another transport) remains open.

### Compatibility and hosting

- Target the current stable WordPress release and subsequent releases.
- Set and declare a minimum PHP version that remains under security support; establish the tested WordPress/PHP matrix and reflect the minimum in the plugin header and WordPress.org `readme.txt` before publication.
- Avoid provider-specific hosting assumptions and runtime dependencies beyond what standard WordPress hosting supports.

## Options considered

| Option | Notes |
| --- | --- |
| **Reusable WordPress plugin (proposed)** | Install the same plugin on source and destination sites; provides an admin workflow and a versioned content format. |
| Store generated exports in a theme | Rejected as a storage mechanism because themes are presentation code and can be replaced or updated independently of plugin data. |
| Custom content REST API for the frontend | Deferred. Static files are the chosen v1 frontend delivery path; a future requirement may justify a REST API. |
| Front-end blocking, CORS, and REST API lockdown | Deferred/dropped from this static-export v1. The current scope does not hide the WordPress theme frontend or harden the core REST API; add a separate requirement if the WordPress site itself must be private. |

## Consequences

### Positive

- The frontend can consume static content without depending on live WordPress requests.
- The same repeatable transfer workflow can support Birra La Fortezza and other WordPress sites.
- Complete snapshots make source deletions propagate to the destination and keep the frontend aligned with source content.
- Source identities let operators refresh imported records without duplication.

### Negative / risks

- Destination edits to synced records are overwritten by the next import. The source remains authoritative.
- Static frontend data is only as fresh as the most recently completed export; current exports are manual or scheduled/triggered, not automatically regenerated on every content change.
- A static export containing private or password-protected records must be protected as an encrypted package; anyone with the password can decrypt it. The frontend owner is responsible for deciding how decrypted content is used.
- Public, password-free exports are publicly accessible and must contain only published public content.
- The WordPress settings database will hold the export-encryption password so automated exports can run. Access to the source site's database and administrator account therefore carries access to that password.
- WordPress sites can differ in custom fields, plugin data, media storage, and relationships; unsupported data must not be silently lost.
- Export storage must not rely on `.htaccess` for access control because some hosts use nginx; public delivery is intentional for public exports, while encryption is the protection for private data.
- Optional S3 delivery is tracked separately in [issue #9](https://github.com/erme2/verso/issues/9) and must not make exported objects public implicitly. Any external-service use must be disclosed in the WordPress.org readme.
- We take on ongoing maintenance of the plugin and compatibility with WordPress core updates and standard hosting environments.

## Open questions

- Which vetted authenticated-encryption and password-KDF algorithms/parameters will the package format use, and how are the password and per-package random salt represented in its header?
- How does the import identify a full, valid snapshot and bind it to the correct source before absence can trigger destination deletion?
- Should absent destination records be moved to Trash or permanently deleted?
- How are same-plugin multi-source imports identified and isolated, and what happens when records conflict?
- How does an export package reach the destination: manual upload, authenticated download from the source, or another transport?
- How should the trigger credential be rate-limited and rotated, and how will a lock prevent overlapping exports? Is the trigger a narrowly scoped export control rather than a content REST API route?
- How should pending and scheduled WordPress records be treated?
- How should large exports/imports be batched to avoid PHP time and memory limits?
- Where are files stored, and how are public files delivered without relying on `.htaccess` or obscurity for private-data protection?
- What minimum PHP version should be supported, and which exact WordPress/PHP versions will CI test?
- What are the final package layout and media URL conventions for static frontend consumption?
- What source-specific field mappings, if any, will the Birra La Fortezza pilot require?
- Which operating/hosting behaviors are required if the WordPress theme frontend must not be exposed, given that front-end blocking, CORS, and core REST API lockdown are deferred?

## Next steps

1. Resolve the open questions that block a safe package and operator workflow; then update this ADR to **Accepted**.
2. Resolve package cryptography, snapshot validation, import transport, and deletion-mode questions before implementation.
3. Specify the versioned JSON/index format and implement manual export and repeatable import with focused tests.
4. Add the static-file consumption guidance for the frontend and document password handling and deletion behavior.
5. Test transfers using synthetic WordPress fixtures before any real-site migration; evaluate optional S3 delivery separately.
