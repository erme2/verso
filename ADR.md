# ADR-0001: Use WordPress as a headless REST API service, configured by a custom plugin

- **Status:** Proposed
- **Date:** 2026-10-18
- **Deciders:** TBD

## Context

We want to use a WordPress install purely as a content/data backend for a rebuilt public website such as birralafortezza.com. The current site's content should be importable into a new WordPress install, which will later serve as a private or restricted REST API backend for the rebuilt website. Editors should manage content in wp-admin, and the public should never see or use WordPress's theme output.

Making WordPress behave well in this role requires a number of adjustments (importing existing content, hiding the front end, locking down and shaping the REST API, CORS, caching, auth). We want these applied consistently and repeatably, rather than as ad hoc edits to `functions.php` or a pile of third-party plugins.

## Decision

We will run WordPress as a headless REST API service and build a **custom WordPress plugin** that applies all the required configuration. The React app will consume the API from the browser (or from a server-side layer if we later need the API URL hidden).

### Plugin responsibilities (v1 scope)

1. **Settings page** in wp-admin: allowed origin(s), optional API key, which post types to expose, cache lifetime.
2. **Front-end blocking:** redirect (or return 404/403 for) all theme/front-end requests to the real site URL. Exempt `wp-admin`, `wp-login.php`, REST requests, Ajax and cron.
3. **CORS:** send `Access-Control-Allow-Origin` for the configured origin only, and handle preflight `OPTIONS` requests.
4. **Content import:** provide an administrative import workflow to migrate existing content from another WordPress site into the headless install, including supported media and content relationships. The importer must not be exposed as a public API operation.
5. **Custom REST namespace** (e.g. `/wp-json/myapp/v1/...`) returning the exact shape React needs, with a `permission_callback` on every route.
6. **Content modelling:** register custom post types, taxonomies and meta with `show_in_rest` so data is edited in wp-admin and read by React.
7. **Caching:** set `Cache-Control`/ETag headers; optionally cache responses in transients and invalidate on `save_post` so data stays fresh.
8. **Reduce API surface:** disable core routes we don't need (users, comments, oEmbed) to limit exposure and prevent user enumeration.
9. **Auth for non-public data:** Application Passwords (built into WordPress) or a simple API key checked in the permission callback. Public read-only endpoints need none.

## Options considered

| Option | Notes |
| --- | --- |
| **Custom plugin (chosen)** | Repeatable, versioned, one place for all headless config; full control over endpoint shape. Cost: we build and maintain it. |
| Edits in theme `functions.php` | Quick, but tied to the theme, easy to lose, not portable. |
| Existing headless/security plugins | Less code to write, but multiple plugins to configure, less control over response shape, and extra dependencies. |
| Skip WordPress (use a dedicated headless CMS or custom backend) | Cleaner fit for headless, but loses the familiar editor experience and existing install. Not pursued for now. |

## Consequences

### Positive

- Editors keep the familiar WordPress admin; React gets clean, purpose-built JSON.
- All headless configuration lives in one versioned plugin that can be deployed to any install.
- Caching and a restricted API surface improve performance and reduce exposure.
- Existing WordPress content can be migrated into the headless install instead of requiring a blank-slate rebuild.

### Negative / risks

- **"Hidden" is relative.** If React calls the API from the browser, the URL is visible in the network tab. We can make it obscure and restricted, but not secret. If it must be truly hidden, a server-side proxy layer (e.g. Next.js/Node) should call WordPress instead.
- **Origin checks are not security.** Non-browser clients can spoof the `Origin` header. Real protection for non-public data comes from a key or token, and any key shipped in client-side JS is effectively public.
- **Admin must keep working.** The front-end block must not interfere with wp-admin, login, Ajax or cron.
- **Page-cache and security plugins** may interfere with REST headers or CORS and need testing.
- **Previews** of unpublished content need extra handling in a headless setup.
- **Imports need a defined source and migration contract.** WordPress sites can differ in post types, metadata, plugins, media storage, and URL structure; unsupported data or collisions could otherwise be silently lost or duplicated.
- We take on ongoing maintenance of the plugin and compatibility with WordPress core updates.

## Open questions

- What data is being served (posts, a custom post type, something else)?
- Is the data public, or does some of it require authentication?
- Does React run purely in the browser, or is there a server component that could proxy requests?
- Will WordPress live on a separate subdomain (e.g. `cms.yourdomain.com`)?
- Which hosting and caching setup will be used?
- How will the existing birralafortezza WordPress content be supplied (WordPress export, authenticated REST API, or another supported source), and is import a one-time migration or a repeatable sync?
- Which source post types, taxonomies, custom fields, media, and relationships must be preserved, and how should duplicate/conflicting content be handled?
- Should imported media be copied into the new install, and how should old URLs and internal links be rewritten for the rebuilt site?
- What review or dry-run behavior is required before imported content becomes available through the restricted API?

## Next steps

1. Answer the open questions above and update this ADR to **Accepted**.
2. Define the supported WordPress import format and migration behavior, then implement an administrator-only importer with validation and safe conflict handling.
3. Scaffold the plugin (`wp-content/plugins/<plugin-name>/`) with the settings page, front-end block and CORS.
4. Add the custom namespace, first endpoints and caching.
5. Test the import against a representative copy of the existing site, then test the React API flow, preflight requests, cache invalidation and wp-admin access.
