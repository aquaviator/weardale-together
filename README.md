# Weardale Together CIC — WordPress Platform Rebuild

This repository contains the reconstructed WordPress theme, platform plugin, AI Studio development preview, local setup documentation and optional starter content for the Weardale Together website.

---

## 🗺️ Project Overview & Dual-Environment Model
This repository uses a modern **dual-layer architecture** designed to bridge agile visual prototyping in the sandbox with structured CMS production delivery:

### Layer 1: The AI Studio Prototyping Workbench (Vite / TypeScript / React)
* **Purpose**: Serves as a standalone interactive workbench and visual prototyping environment within Google AI Studio for rapid layout validation, accessibility evaluation, and client demonstrations.
* **Environment**: Powered by Vite and React, located in the workspace root (excluding the `/wordpress/` directory).
* **Execution**: Run `npm run dev` to boot the interactive preview server (runs on port 3000).
* **Isolation Note**: This layer is purely for local and sandbox visual verification. It is never deployed to the live WordPress server and has zero runtime dependencies on PHP, Apache, or MySQL.

### Layer 2: The WordPress CMS Engine (WordPress / PHP / MySQL)
* **Purpose**: Custom classic PHP theme and core system plugin designed for the live WordPress site.
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
Every folder serves a specific role in our dual-environment model:

* **`src/`**: Standalone React/TypeScript prototyping workbench source files. This represents the development-only visual preview sandbox used in AI Studio.
* **`wordpress/`**: The core directory housing our custom WordPress deliverables.
  * **`wordpress/wp-content/themes/weardale-together/`**: Custom classic PHP theme implementing our accessible design system and strand branding logic.
  * **`wordpress/wp-content/plugins/weardale-platform/`**: Core system plugin registering custom post types (`weardale_event`), taxonomic strands, and administrative panels.
* **`docs/`**: Operational, local setup, editorial, and status documentation.
* **`scripts/`**: Hand-crafted SQL scripts and development utility sequences.
* **`seed/`** *(Not present in repository root)*: Reference target for raw database SQL exports. This is kept out of active tracking to prevent bloating the Git repository.
* **`release/`** *(Not present in repository root)*: Destination for generated production ZIP distribution archives (e.g., theme and plugin zip files), excluded from version control to maintain repository hygiene.

---

## ⚡ Local Setup & Testing Quickstart
To run the WordPress implementation locally under standard Apache/MySQL environments, follow this verified, sequential setup (see `/docs/xampp-guide.md` for the complete guide):

1. **Clone Repository**: Clone this repository directly into your local XAMPP public folder:
   `C:\xampp\htdocs\weardale-together`
2. **Create Database**: Open phpMyAdmin (`http://localhost/phpmyadmin/`) and create a new, empty database named `weardale_together_v2` with `utf8mb4_unicode_ci` collation.
3. **Install WordPress Core**: Download WordPress core files (version 6.0+) and extract them inside:
   `C:\xampp\htdocs\weardale-together\wordpress\`
   *(Make sure you do not overwrite or delete our custom theme and plugin located inside the `/wordpress/wp-content/` directory).*
4. **Configure Local Environment**: Rename `wp-config-sample.php` inside the `wordpress/` directory to `wp-config.php`. Edit it to set `DB_NAME` to `weardale_together_v2`, `DB_USER` to `root`, `DB_PASSWORD` to `''`, generate fresh keys/salts, and set `WP_DEBUG` to `true`.
5. **Run Web Installer**: Access `http://localhost/weardale-together/wordpress/` in your web browser and complete the classic WordPress installation wizard.
6. **Log into Admin**: Access the dashboard at `http://localhost/weardale-together/wordpress/wp-admin/`.
7. **Activate Plugin**: Navigate to **Plugins > Installed Plugins** and activate the **Weardale Platform** plugin.
8. **Activate Theme**: Navigate to **Appearance > Themes** and activate the **Weardale Together** theme.
9. **Configure Permalinks**: Navigate to **Settings > Permalinks**, select **Post name**, and click **Save Changes**. This is a mandatory step that creates the local rewrite engine and `.htaccess` file.
10. **Import Seed Data (Optional)**: Select the `weardale_together_v2` database in phpMyAdmin, navigate to the **Import** tab, and run `/scripts/seed-db.sql`.
    * *Note: This is an optional local development seed intended only for a fresh WordPress installation using the standard `wp_` table prefix. It contains demonstration content and should not be treated as real, final client content, nor should it be imported repeatedly.*
11. **Verify Setup**: Open `http://localhost/weardale-together/wordpress/` in your browser and verify the homepage, pages, and custom event listings render correctly.

---

## ♿ Accessibility Compliance
The Weardale Together platform is engineered to comply with the **WCAG 2.2 AA** international accessibility standard:
* **Skip-to-Content Link**: Included on all headers to bypass repetitive navigation lists using keyboard tabs.
* **Semantic Landmarks**: Standardized structural tags (`<header>`, `<nav>`, `<main>`, `<article>`, `<footer>`) to allow ease of screen-reading.
* **Keyboard Navigation**: Highly visible focus halos on links and inputs. All buttons and dropdown controls are fully accessible via keys.
* **Dynamic Fallbacks**: The hub-and-spoke interactive diagram automatically converts to an ordered list structure on mobile devices or when CSS styling fails, eliminating layout constraints.
