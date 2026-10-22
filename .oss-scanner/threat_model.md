# baserCMS threat model

This document is for the Anthropic OSS Scanner. It describes what baserCMS is,
where untrusted input enters, which components matter most, how to exercise the
built image, how we rate severity, and what is out of scope.

## 1. What this project does

baserCMS is a Japanese open-source CMS built on PHP 8 and CakePHP 5. It serves a
public website (pages, blogs, custom content, mail forms) and provides a
browser-based admin UI under `/baser/admin/`, a REST API under `/baser/api/`,
and an MCP server (`/mcp`, protected by OAuth 2.0) for AI agents.

A typical deployment is a shared or managed PHP host run by a small company or
web agency. The main security goal is that **nobody without admin credentials
can read or change site data or run code on the server**, and that **site
operators with limited roles cannot escalate to system administrator**.

## 2. Principals and trust

From least to most trusted:

- **Anonymous visitor** – browses the public site, submits mail forms, uses
  blog/custom-content search, posts blog comments (if enabled), hits the public
  read-only API when `USE_CORE_API` is enabled. Fully untrusted.
- **API / MCP client** – holds a JWT (admin API, `USE_CORE_ADMIN_API`) or an
  OAuth 2.0 access token (MCP). Trusted only within the scopes and the user
  group of the user the token was issued for.
- **Site operator** (user group `operators`, id 2, or any custom group) –
  logs into the admin UI. Access is restricted per controller/action by the
  permission system (`permissions`, `permission_groups`, `auth_prefix_settings`).
  Operators are **not** trusted to read other users' data outside their
  permissions, change system settings, manage users/groups, upload or edit
  themes and plugins, or run arbitrary PHP.
- **System administrator** (user group `admins`, id 1) – fully trusted
  **within the CMS**. By design admins can install plugins and themes from ZIP
  files and edit theme templates (PHP) through the `bc-theme-file` plugin,
  so an admin can already run code on the server. Admin-only RCE is therefore
  not a vulnerability unless it crosses another boundary (see §6).
- **Server operator** – owns the filesystem, `config/.env`, `config/app_local.php`
  and the database. Fully trusted.

## 3. Where untrusted input enters

- Front-end request routing and content lookup (`BcFrontMiddleware`,
  `ContentsService`, `PagesController`, blog/custom-content archives and search).
- Mail forms (`bc-mail`): field rendering, validation, file upload, confirmation
  and sending (header injection, template injection, upload handling).
- Blog comments and blog search (`bc-blog`).
- Public REST API (`plugins/*/src/Controller/Api/*`, only when `USE_CORE_API` is on).
- Admin REST API (`plugins/*/src/Controller/Api/Admin/*`, JWT auth).
- MCP server and OAuth 2.0 authorization server (`bc-mcp`): token issuance,
  PKCE, dynamic client registration, scope checks, tool argument handling.
- Authentication: login, password reset, "remember me" cookies, session
  handling, two-factor flows if present (`baser-core` `UsersController`,
  `Authentication` configuration in `BaserCorePlugin`).
- Admin forms: everything under `/baser/admin/` is CSRF-protected and
  permission-checked; look for actions that bypass either.
- File handling: `bc-uploader` (user uploads), `bc-theme-file` (template
  editor), plugin/theme ZIP install and update (`BcZip`, `BcFolder`, `BcFile`),
  site/theme config images, `BcUtil` path helpers – path traversal, arbitrary
  file write/read, unsafe extraction, uploaded files served as PHP.
- Rich text and HTML: Burger Editor (`bc-burger-editor`), editor templates,
  widget areas, custom content fields (`bc-custom-content`), SEO fields,
  content links, HTML purification (`ezyang/htmlpurifier`) – stored XSS that
  executes for admins or visitors.
- Installer (`bc-installer`): must be unreachable once `config/install.php`
  exists.
- `BcUtil`/`BcSiteConfig` helpers that read request data (host, proxy headers,
  `TRUST_PROXY`), and anything using `unserialize`, `eval`, `include` with
  variable paths, or shell commands.

Low priority: `tests/`, `docker/`, `docs/`, `.github/`, `plugins/BcPluginSample`,
`plugins/BcThemeSample`, built front-end assets, third-party code in `vendor/`.

## 4. How to exercise it

The image has a complete installation at `/var/www/html` (`/src` is a symlink to it) with MariaDB and Apache.

```bash
basercms-start                 # start MariaDB + Apache (nothing runs by default)
curl -s http://localhost/      # public site
basercms-test                  # full PHPUnit suite (slow, ~thousands of tests)
basercms-test --testsuite BcMail   # one plugin
basercms-test --filter UsersControllerTest
basercms-stop
```

- Admin UI: `http://localhost/baser/admin/`, login `foo@example.com` /
  `baserCMS1234` (system administrator, group `admins`).
- Create a less-privileged user: admin UI → ユーザー管理, assign group
  `operators` (サイト運営) to test permission boundaries, or insert a user via
  `bin/cake` / the DB. Database: `basercms` / `basercms`, db `basercms`,
  host `127.0.0.1`.
- Public API: set `USE_CORE_API="true"` in `config/.env`, then e.g.
  `GET /baser/api/baser-core/contents.json`.
- Admin API: set `USE_CORE_ADMIN_API="true"`, obtain a JWT with
  `POST /baser/api/admin/baser-core/users/login.json` (email/password), send it
  as `Authorization: Bearer`.
- MCP: enable the `BcMcp` plugin in the admin UI (プラグイン管理), register an
  OAuth client, then use `/mcp`. OAuth metadata is under `/.well-known/`.
- Logs: `/var/www/html/logs/`. Debug mode: `DEBUG="true"` in `config/.env`.
- Tests use the same MariaDB; `bin/cake setup test` prepares `.env` for them.

## 5. Severity ratings

- **Critical**: unauthenticated or operator-level RCE, SQL injection, arbitrary
  file read/write, authentication bypass, account takeover of an admin,
  reading `config/.env` or `app_local.php`, reaching the installer after
  installation, OAuth/JWT forgery, mass disclosure of user credentials.
- **High**: operator → admin privilege escalation; operators acting outside
  their permission set in a way that changes site content, settings or users;
  stored XSS a visitor or operator can plant that runs in an admin's browser;
  mail-form header injection; CSRF on state-changing admin actions; path
  traversal limited to a web-served directory.
- **Medium**: stored XSS that only affects visitors on the public site;
  reflected XSS; IDOR disclosing non-sensitive content; information leaks
  such as internal paths or stack traces in production mode; weak token or
  reset-link handling that needs extra conditions; resource exhaustion from a
  small request.
- **Low**: issues requiring `DEBUG="true"`, timing or enumeration oracles with
  little value, self-XSS, missing hardening headers.

Authenticated SQL injection reachable by an operator is High; by an admin only
it is Medium (admins already have code execution). Unproven memory-safety
claims in PHP extensions are out of scope.

## 6. Out of scope / leave alone

- Anything that **requires system administrator** credentials and stays
  within the CMS: admins can intentionally upload plugins/themes and edit PHP
  templates. Exception: an admin-only action that reads or writes files
  **outside** the baserCMS root, or that leaks server operator secrets not
  normally shown in the UI, is still worth reporting (Medium).
- Server operator configuration: Apache/PHP/MariaDB settings, file permissions
  chosen by the host, `DEBUG="true"` in production, `TRUST_PROXY` set wrongly.
- Volume-based denial of service, brute force without rate limiting, missing
  security headers, and CSP.
- Vulnerabilities in `vendor/` (CakePHP, HTMLPurifier, etc.) unless baserCMS
  uses the library unsafely.
- Test, documentation, Docker and CI code; sample plugin/theme.

## 7. Reports and patches

- Name the principal, the boundary crossed, the entry point (URL/method or
  CLI), and the code path (file:line). A curl or PHPUnit reproduction against
  the built image is ideal.
- Patches should be minimal, follow the existing code style (CakePHP coding
  standard, PSR-12), and keep the public API of services and helpers stable.
  Add or extend a PHPUnit test under the plugin's `tests/TestCase/` for the
  fixed behavior.
- Keep comments and commit messages in Japanese where the surrounding code is
  Japanese; English is fine for the report itself.
