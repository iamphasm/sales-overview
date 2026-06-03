# 🤝 Sales Overview v1.0

> A tool for tracking and managing your online private sales

**Author:** Kristian Daae-Johansen — [github.com/iamphasm](https://www.github.com/iamphasm) / [phasm.no](https://www.phasm.no)

---

## Pages & Navigation

- **Home** — Dashboard with live count, products sold, total invested and ROI at a glance, plus a recent sales table.
- **Live Sales** — All active sales sorted newest first. Sales with a passed finish date are automatically moved to Finished.
- **Finished Sales** — All ended sales. Add or edit the final sale price per item to track what each item sold for.
- **Statistics** — Key financial metrics and an earnings chart with four time period views.
- **Add New Sale** — Form to register a new sale with optional auto-fill via the Scrape Info feature.

---

## Features

- **Scrape Info** — Paste an auction link and auto-fetch the title and production year.
- **Edit Live Sales** — Edit all fields of an active sale at any time via an inline modal.
- **Mark as Sold** — Move a sale to Finished directly from the Live Sales edit modal, the green checkmark button, or when adding a new sale.
- **Delete Sales** — Remove any sale permanently from both Live and Finished Sales.
- **Final Price Tracking** — Add or update the final sale price on finished items to calculate ROI.
- **Auto Status Update** — Sales whose finish date has passed are automatically moved to Finished Sales on every page load.
- **Direct Auction Links** — One-click icon to open the original auction listing in a new tab.
- **Login Protection** — Username/password login with session-based authentication.
- **Change Password** — Update your password via the Settings page in the sidebar.

---

## Statistics

- **Invested Total** — Sum of investment costs across all sales.
- **Total Income** — Sum of all recorded final sale prices.
- **ROI Total** — Total income minus investment cost for all sold items.
- **Products Sold / Not Sold** — Count of finished sales with and without a recorded final price.
- **Earnings Chart** — Bar and line chart showing revenue and ROI over Last 30 Days, Last 6 Months, Over 1 Year, or All Time.

---

## Technical

| | |
|---|---|
| **Backend** | PHP 8+ |
| **Storage** | JSON flat-file (no database server required) |
| **Frontend** | Vanilla JS, custom CSS |
| **Charts** | Chart.js 4.4 |
| **Icons** | Font Awesome 6.5 |

---

## Getting Started

```bash
# Clone the repository
git clone https://github.com/iamphasm/sales-overview.git
cd sales-overview

# Set data directory permissions
chmod 775 data/

# Create default config (admin / admin)
php -r "file_put_contents('data/config.json', json_encode(['username'=>'admin','password_hash'=>password_hash('admin',PASSWORD_BCRYPT)], JSON_PRETTY_PRINT));"

# Create empty database
echo '[]' > data/auctions.json

# Start the server
php -S localhost:8080
```

Then open [http://localhost:8080](http://localhost:8080) and log in with `admin` / `admin`.

> **Note:** Change your password immediately after first login via **Settings** in the sidebar.
