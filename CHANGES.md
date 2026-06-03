# Changelog

---

## v1.0.1 — 2026-06-03

### Security hardening (10 issues fixed)

- **Auth on all API endpoints** — Added `session_start()` + `require_auth()` to `add_auction.php`, `delete_auction.php`, `edit_auction.php`, `update_sale.php`, and `scrape.php`. All state-changing endpoints now require an authenticated session.
- **Stored XSS fix** — `link` field now validated to only allow `http`/`https` schemes on write (API) and on render (live.php, finished.php). `javascript:` URLs are silently dropped.
- **SSRF hardening** — Scraper disables redirect following, rejects userinfo in URLs, and uses `FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE` plus explicit blocks for link-local (169.254/16), CGNAT (100.64/10), and multicast ranges.
- **Removed hardcoded fallback credentials** — `auth.php` no longer falls back to `admin/admin` when `config.json` is missing; authentication is refused instead.
- **Data directory protected** — Added `data/.htaccess` with `Require all denied` to block direct browser access to `auctions.json` and `config.json`.
- **Session security** — `session_regenerate_id(true)` added after login and after password change. Secure cookie flags (`httponly`, `samesite=Lax`, `secure` when HTTPS) set via `secure_session_start()` in `auth.php`.
- **Minimum password length raised** — From 4 to 8 characters in `change_password.php` and the Settings UI.

---

All changes to Sales Overview are documented here.

---

## v1.0.0 — 2026-06-03

### Initial Release

**Core Framework**
- PHP 8+ backend with JSON flat-file storage (no database server required)
- Session-based login with username/password authentication
- Password change via Settings page

**Pages**
- Home dashboard with stat cards (live count, sold, invested, ROI) and recent sales table
- Live Sales — sortable table with edit, finish, and delete actions per row
- Finished Sales — table with final price entry/edit and delete per row
- Statistics — KPI cards (Invested Total, Total Income, ROI Total, Sold/Not Sold) and Chart.js earnings graph with 4 period views (30 days, 6 months, 1 year, all time)
- Add New Sale — form with Scrape Info auto-fill, sold checkbox, optional finish date
- About — framework info and author details
- Settings — change password form

**Features**
- Scrape Info button auto-fetches title and production year from auction URL
- Mark as Sold checkbox on add and edit forms moves sale directly to Finished Sales
- Green checkmark button on Live Sales rows for one-click move to Finished
- Auto-status update: sales past their finish date are automatically moved to Finished on page load
- SSRF protection on scraper endpoint
- SRI integrity hashes on all external CDN resources
