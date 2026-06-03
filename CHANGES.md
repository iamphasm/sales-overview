# Changelog

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
