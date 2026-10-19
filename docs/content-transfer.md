# Content transfer and static export

**Status: proposed.** This guide records the current product direction from [ADR-0001](../ADR.md). Import/export behavior is not implemented yet.

## Sites and workflow

Install the same Verso plugin on the existing source site and the new destination site. On the source, an administrator requests an export from the Verso plugin page. Verso writes static JSON files and an index file. The files can be downloaded or published for a frontend such as a compiled React application. On the destination, an administrator imports the exported content.

The package format will be versioned and every export is a complete snapshot of selected content. Each record will have a stable source identity so repeated imports update the corresponding destination record instead of creating a duplicate. The source is authoritative: an import overwrites destination edits to matching records. A record absent from a valid snapshot is deleted at the destination; source-trash and draft records are omitted, and restoring content at the source makes it return in the next snapshot. Whether the destination copy is moved to Trash or permanently deleted remains open.

## Content in scope

The current scope includes selected pages, posts, media and their relationships, categories/tags, and configured custom post types and fields. Drafts and source-trash records are omitted from snapshots. Private and password-protected content may be included only in encrypted output. Users, comments, navigation menus, and other data are not included unless a later decision adds them.

## Export privacy

Exports containing private or password-protected content require the password saved in WordPress settings and must be encrypted as a package, including during automated runs. If no password is set, the export is public and must contain only published public content. Encryption must use vetted authenticated encryption and a password-based KDF; each package gets a fresh random salt. Exact algorithms and parameters remain open.

The index remains unencrypted but must not reveal private titles, slugs, record details, or counts. Any private values shown there use `***`, without per-record rows or counts that reveal how many private records exist. Generated files belong to Verso's plugin-managed storage; a theme is not used as an export directory. Storage and delivery must not rely on `.htaccess` or unguessable names as private-data protection: encryption protects private package contents.

The frontend is responsible for decrypting private content and deciding how to use it. Optional S3 upload is tracked separately in [issue #9](https://github.com/erme2/verso/issues/9); any external service use must be disclosed for WordPress.org publication.

## Manual and automated exports

Manual export from the plugin page and automated export through both WordPress cron and a protected trigger for external cron services are planned. The trigger credential is separate from the export-encryption password, is sent only in an authorization header, and is stored only as a one-way hash. Never expose it in query strings or logs. The comparison must avoid timing leaks. Rate limiting, rotation, and a lock against overlapping exports still need a defined design. Exports are not regenerated automatically on every content edit, so the frontend sees the last completed snapshot.

## Import safety and open design points

Imports must check an authorized capability and nonce, validate the package schema and version, sanitize content according to the importing user's capabilities, restrict media types and sizes, and avoid fetching arbitrary URLs. The package transport (manual upload, authenticated source download, or another mechanism), stable multi-source identity, full-snapshot verification, destination deletion mode, and batching for large sites remain to be defined.

## Compatibility

The target is the current stable WordPress release and subsequent releases on standard WordPress hosting. The minimum PHP version, exact tested compatibility matrix, and final static file/media URL conventions remain to be defined. Front-end blocking, CORS, and core REST API lockdown are deferred from this static-export v1.
