# Weardale Together CIC — WordPress Platform Rebuild

This repository contains the complete, production-ready WordPress classic PHP theme and core custom plugin for **Weardale Together CIC**, a grassroots Community Interest Company serving remote rural populations in the North Pennines.

---

## 🗺️ Project Overview & Architecture
The codebase separates concerns according to standard WordPress professional engineering guidelines:

1. **Presentation layer: Theme (`weardale-together`)**
   * Located at `/wordpress/wp-content/themes/weardale-together/`
   * Employs classic PHP templates (`front-page.php`, `page.php`, `single.php`, `archive.php`, `header.php`, `footer.php`) and a highly cohesive design system.
   * Incorporates an **accessible interactive hub-and-spoke diagram** as the primary homepage feature, which collapses into a list menu on mobile.
   * Integrates the "Different Rooms in the Same House" motif, where specific strand sub-pages dynamically display their own colors, typography headers, and illustrated layouts based on term-class bindings.
   
2. **Data & Operations layer: Plugin (`weardale-platform`)**
   * Located at `/wordpress/wp-content/plugins/weardale-platform/`
   * Registers custom post-types (`weardale_event`) and taxonomy groups (`strand`).
   * Handles metadata boxes (Event Dates, Timings, Locations, and Entry Fees) with secure save tokens (nonces) and sanitization routines.
   * Optimizes the administrative lists with custom table column monitors for easy, non-technical event tracking.

---

## 📂 Project Structure
```
project-root/
├── wordpress/
│   └── wp-content/
│       ├── themes/
│       │   └── weardale-together/          # Main WordPress classic PHP theme
│       └── plugins/
│           └── weardale-platform/          # Custom post types & admin configurations
├── docs/
│   ├── xampp-guide.md                      # Step-by-step local XAMPP setup guide
│   └── editor-guide.md                     # Manual for non-technical content managers
├── scripts/
│   └── seed-db.sql                         # SQL seed to populate database with default IA
└── README.md                               # This file
```

---

## ⚡ Local Setup & Testing Quickstart
To get this site running locally, follow these simple steps (see `/docs/xampp-guide.md` for details):
1. Install **XAMPP** on your system.
2. Clone this repository directly inside `C:\xampp\htdocs\WT\`.
3. Create a MariaDB/MySQL database named `weardale_together` inside phpMyAdmin.
4. (Optional) Run the database seed queries inside `/scripts/seed-db.sql`.
5. Run the standard WordPress installation wizard at `http://localhost/WT/`.
6. Log in to the administrator portal (`http://localhost/WT/wp-admin/`), go to **Plugins** and click **Activate** under **Weardale Platform**.
7. Go to **Appearance > Themes** and click **Activate** under **Weardale Together**.
8. Go to **Settings > Permalinks**, choose **Post Name**, and click **Save Changes**.

---

## ♿ Accessibility Compliance
The Weardale Together platform is engineered to comply with the **WCAG 2.2 AA** international accessibility standard:
* **Skip-to-Content Link**: Included on all headers to bypass repetitive navigation lists using keyboard tabs.
* **Semantic Landmarks**: Standardized structural tags (`<header>`, `<nav>`, `<main>`, `<article>`, `<footer>`) to allow ease of screen-reading.
* **Keyboard Navigation**: Highly visible focus halos on links and inputs. All buttons and dropdown controls are fully accessible via keys.
* **Dynamic Fallbacks**: The hub-and-spoke interactive diagram automatically converts to an ordered list structure on mobile devices or when CSS styling fails, eliminating layout constraints.
