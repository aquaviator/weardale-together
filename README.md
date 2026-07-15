# Weardale Together CIC — WordPress Platform Rebuild

This repository contains the complete, production-ready WordPress classic PHP theme and core custom plugin for **Weardale Together CIC**, a grassroots Community Interest Company serving remote rural populations in the North Pennines.

---

## 🗺️ Project Overview & Dual-Environment Model
This repository uses a modern **dual-layer architecture** designed to bridge agile visual prototyping in the sandbox with structured CMS production delivery:

### Layer 1: The AI Studio Prototyping Workbench (Vite / TypeScript / React)
* **Purpose**: Serves as a standalone interactive workbench and visual prototyping environment within Google AI Studio for rapid layout validation, accessibility evaluation, and client demonstrations.
* **Environment**: Powered by Vite and React, located in the workspace root (excluding the `/wordpress/` directory).
* **Execution**: Run `npm run dev` to boot the interactive preview server (runs on port 3000).
* **Isolation Note**: This layer is purely for local and sandbox visual verification. It is never deployed to the live WordPress server and has zero runtime dependencies on PHP, Apache, or MySQL.

### Layer 2: The Production CMS Engine (WordPress / PHP / MySQL)
* **Purpose**: The actual production-ready deliverable containing our custom, accessible classic PHP theme and core system plugin.
* **Location**: Located inside the `/wordpress/` directory.
  * **Theme (`weardale-together`)**: `/wordpress/wp-content/themes/weardale-together/`
  * **Plugin (`weardale-platform`)**: `/wordpress/wp-content/plugins/weardale-platform/`
* **Execution**: Deployed and tested locally using standard **XAMPP** (Apache, MariaDB, PHP 8.0+).

---

## 🔒 CRITICAL SYSTEM ISOLATION POLICY
Weardale Together operates under a strict isolation mandate:
1. **DO NOT MODIFY OR TOUCH** the existing local system in `C:\xampp\htdocs\WT\` or the database `weardale_together`. These are protected legacy resources.
2. **MANDATORY FRESH ENVIRONMENT**: All new development, migrations, and local tests must be isolated to a fresh directory `C:\xampp\htdocs\weardale-together\` and target a new database `weardale_together_v2`.

---

## 📂 Project Structure
```
weardale-together/
├── wordpress/
│   └── wp-content/
│       ├── themes/
│       │   └── weardale-together/          # Production WordPress Classic PHP Theme
│       └── plugins/
│           └── weardale-platform/          # Custom Core Post Types & Taxonomy Plugins
├── docs/
│   ├── xampp-guide.md                      # Comprehensive Local XAMPP Setup & Portability Guide
│   └── editor-guide.md                     # Training manual for content editors & administrators
├── scripts/
│   └── seed-db.sql                         # Database seed queries (safe for weardale_together_v2)
├── src/                                    # Workbench prototyping React source files
├── index.html                              # Workbench entry point
├── package.json                            # Workbench dependencies & scripts
└── README.md                               # This file
```

---

## ⚡ Local Setup & Testing Quickstart
To get this site running locally, follow these simple steps (see `/docs/xampp-guide.md` for the full step-by-step instructions):

1. **Prerequisites**: Download and install **XAMPP** (PHP 8.0+) and a fresh copy of **WordPress core**.
2. **Directory Mapping**: Create a directory at `C:\xampp\htdocs\weardale-together\` and extract WordPress core files.
3. **Custom Assets Copy**: Copy the theme and plugin from `/wordpress/wp-content/` in this repo to:
   * `C:\xampp\htdocs\weardale-together\wp-content\themes\weardale-together\`
   * `C:\xampp\htdocs\weardale-together\wp-content\plugins\weardale-platform\`
4. **Isolated Database Creation**: In phpMyAdmin, create a database named `weardale_together_v2` with `utf8mb4_unicode_ci` collation.
5. **Seeding**: Import the `/scripts/seed-db.sql` file into your `weardale_together_v2` database.
6. **Local Config**: Set up a local, untracked `wp-config.php` file pointing to `weardale_together_v2`, generate fresh salts, and enable debugging.
7. **Installer wizard**: Access `http://localhost/weardale-together/` in your browser to run the WordPress web installation.
8. **Activation**:
   * Go to **Plugins > Installed Plugins** and activate the **Weardale Platform** plugin.
   * Go to **Appearance > Themes** and activate the **Weardale Together** theme.
9. **Permalinks**: Go to **Settings > Permalinks**, choose the **Post Name** option, and click **Save Changes** (generates `.htaccess`).
10. **Validation**: Check your local setup against the **Local Validation Checklist** inside `/docs/xampp-guide.md`!

---

## ♿ Accessibility Compliance
The Weardale Together platform is engineered to comply with the **WCAG 2.2 AA** international accessibility standard:
* **Skip-to-Content Link**: Included on all headers to bypass repetitive navigation lists using keyboard tabs.
* **Semantic Landmarks**: Standardized structural tags (`<header>`, `<nav>`, `<main>`, `<article>`, `<footer>`) to allow ease of screen-reading.
* **Keyboard Navigation**: Highly visible focus halos on links and inputs. All buttons and dropdown controls are fully accessible via keys.
* **Dynamic Fallbacks**: The hub-and-spoke interactive diagram automatically converts to an ordered list structure on mobile devices or when CSS styling fails, eliminating layout constraints.
