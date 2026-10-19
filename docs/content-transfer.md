# Content transfer and static export

**Status: proposed.** This guide records the current product direction from [ADR-0001](../ADR.md). Import/export behavior is not implemented yet.

## Sites and workflow

Install the same Verso plugin on the existing source site and the new destination site. On the source, an administrator requests an export from the Verso plugin page. Verso writes static JSON files and an index file. The files can be downloaded or published for a frontend such as a compiled React application. On the destination, an administrator imports the exported content.

The package format will be versioned. Each record will have a stable source identity so repeated imports update the corresponding destination record instead of creating a duplicate. For records that still exist at the source, the latest source version is authoritative. If a record was deleted at the source, Verso will show the proposed destination deletion and require explicit confirmation before applying it.

## Content in scope

The current scope includes pages, posts, media and their relationships, categories/tags, and configured custom post types and fields. Draft and private records may be included. Users, comments, navigation menus, and other data are not included unless a later decision adds them.

## Export privacy

Exports containing draft or private content require an administrator-configured password and must be encrypted as a package. If no password is set, the export is public and must contain only published public content. Generated files belong to Verso's plugin-managed storage; a theme is not used as an export directory.

Do not put the decryption password in a compiled frontend. Browser code cannot keep a password secret. How authorized users preview or consume private content remains undecided. Optional S3 upload is tracked separately in [issue #9](https://github.com/erme2/verso/issues/9).

## Manual and automated exports

Manual export from the plugin page is the initial workflow. Automated export will support both WordPress cron and a protected trigger URL for external cron services. The trigger credential is separate from the export-encryption password and must not be exposed in query strings or logs. Any automated export follows the same rule that draft/private content requires a password and encrypted output.

## Compatibility

The target is the current stable WordPress release and subsequent releases, PHP newer than 8.0, and standard WordPress hosting. The exact tested version matrix and final static file/media URL conventions remain to be defined.
