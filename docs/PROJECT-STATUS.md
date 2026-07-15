# Weardale Together CIC — Reconstructed WordPress Project Status

This document serves as the primary, living engineering handover and status report for the reconstructed **Weardale Together CIC** web platform. It details the current milestone, dual-layer architecture, strict environmental isolation policies, repository layout, and development guidelines for senior engineers inheriting the codebase.

---

## 1. Project Overview
Weardale Together is a grassroots Community Interest Company (CIC) serving remote rural populations in the North Pennines valley (Stanhope, County Durham). 

* **Purpose**: Provide a modern, accessible web portal presenting the organization's multiple community strands (Café, Creative Arts, Youth, and Playrooms) alongside direct events tracking and community news.
* **Audience**: Isolated rural residents, local families, elderly community members, regional volunteers, and local authority funders.
* **Design Philosophy**: Warm, organic, handcrafted, and human. Built around the "Different Rooms in the Same House" motif (where each of the four core strands has its own visual flavor, colors, and layout while sharing a cohesive parent architecture).
* **Brief Alignment**: Rebuilt to align with the client brief's core specifications, including WCAG 2.2 AA accessibility guidelines, an interactive hub-and-spoke homepage diagram, and local portable hosting requirements.

---

## 2. Current Status

| Parameter | Value / Status |
| :--- | :--- |
| **Current Milestone** | Milestone 6: Core Repository Alignment & Local Portability Completed |
| **Project Health** | Local development baseline established. |
| **Development Phase** | Local Testing & Handover Preparation |
| **Repository** | `https://github.com/aquaviator/weardale-together` |
| **Branch** | `main` |

---

## 3. Architecture & Dual-Environment Model
The codebase is structured under an intentional **dual-layer model** separating development tooling from core CMS deliverables.

```
                  ┌──────────────────────────────────────────────────┐
                  │          weardale-together/ (Git Repository)      │
                  └─────────┬──────────────────────────────┬─────────┘
                            │                              │
                            ▼                              ▼
             ┌──────────────────────────────┐┌──────────────────────────────┐
             │ LAYER 1: DEVELOPMENT PREVIEW  ││  LAYER 2: WORDPRESS ENGINE   │
             │ (Vite / React / TypeScript)  ││ (Classic WordPress CMS Target│
             ├──────────────────────────────┤├──────────────────────────────┤
             │ • Rapid visual prototyping   ││ • Target PHP 8.0+ / MariaDB  │
             │ • Sandbox visual playground  ││ • Custom WP Theme            │
             │ • Offline state engines      ││ • Custom Editorial Plugin     │
             │ • Local NodeJS / Vite server ││ • No Node/React dependencies │
             └──────────────────────────────┘└──────────────────────────────┘
```

### Layer 1: AI Studio Prototyping Workbench (Development Tooling)
* **Components**: `/src/`, `index.html`, `package.json`, `package-lock.json`, `tsconfig.json`, `vite.config.ts`.
* **Purpose**: Allows visual prototyping, fast feedback loops, and user flow validation within a sandboxed environment.
* **Deployment Status**: Contains zero code that goes to the production WordPress live site. Node, Vite, React, and TypeScript are development tools only.

### Layer 2: WordPress CMS Target (Production Deliverables)
* **Components**: `/wordpress/wp-content/themes/weardale-together/` and `/wordpress/wp-content/plugins/weardale-platform/`.
* **Purpose**: Core custom theme and system plugin for deployment to the live site.
* **Execution Environment**: Standard PHP 8.0+, Apache/Nginx, MariaDB/MySQL. Portable to general WordPress hosting platforms.

---

## 4. Repository Structure
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

## 5. Local Development Environment Setup
The reconstructed project is configured for isolated development inside a distinct folder and database to prevent conflicts.

* **Repository / Folder**: `C:\xampp\htdocs\weardale-together\`
* **WordPress Runtime Folder**: `C:\xampp\htdocs\weardale-together\wordpress\`
* **Custom Theme Path**: `C:\xampp\htdocs\weardale-together\wordpress\wp-content\themes\weardale-together\`
* **Custom Plugin Path**: `C:\xampp\htdocs\weardale-together\wordpress\wp-content\plugins\weardale-platform\`
* **Isolated Database**: `weardale_together_v2`
* **Local URL**: `http://localhost/weardale-together/wordpress/`

---

## 6. Protected Legacy Project
The older local installation and database are considered **strictly protected legacy resources** and must remain completely untouched.

* **Protected Path**: `C:\xampp\htdocs\WT\`
* **Protected Database**: `weardale_together`
* **Protected Local URL**: `http://localhost/WT/`

### 🔒 Mandatory Database Safety Rule
Before executing any database creation, seed, migration, drop, or truncate commands:
1. Verify that your commands or connection strings target `weardale_together_v2`.
2. Do **NOT** execute any command targeting the `weardale_together` database.
3. Destructive actions like `DROP DATABASE weardale_together` or `TRUNCATE` against legacy tables are strictly forbidden.

---

## 7. Git Strategy & Repository Hygiene

* **WordPress Core Excluded**: WordPress core PHP directories (`/wp-admin/`, `/wp-includes/`, etc.) and system files (such as `wp-config.php` and `.htaccess`) are explicitly ignored inside `.gitignore` to keep the repository portable and clean.
* **Tracked Deliverables**: Only custom components inside `/wordpress/wp-content/themes/weardale-together/` and `/wordpress/wp-content/plugins/weardale-platform/` are tracked by Git.
* **Secret Security**: API keys, DB passwords, and generated WordPress salts are never committed. Developers must use standard placeholder values locally.

---

## 8. WordPress Architecture
The reconstructed platform separates code display concerns from editorial logic.

### Custom Theme: `weardale-together`
* **Responsibilities**: Primary layout renders (`header.php`, `footer.php`, `front-page.php`, `single.php`, `archive.php`).
* **Design Motifs**: Employs CSS custom properties (`variables.css`) to swap color configurations depending on post category or Custom Strand terms (the "Different Rooms in the Same House" paradigm).
* **Accessibility**: Designed for keyboard navigability with visible tab-halos, a standard "Skip-to-Content" bypass skip-link, and strict semantic HTML landmarks (such as `<main role="main">` and `<nav>`).

### Custom Plugin: `weardale-platform`
* **Responsibilities**: Editorial experience enhancements, custom data modeling, and meta configuration.
* **Custom Post Type (CPT)**: Registers the `weardale_event` post-type to model and display activity schedules.
* **Custom Taxonomy**: Registers the hierarchical `strand` taxonomy to group posts, pages, and events into strands.
* **Admin Column Injection**: Extends the `weardale_event` admin lists in `/wp-admin/` with custom custom columns for immediate calendar tracking by administrators.

---

## 9. Verified & Completed States
The local development environment baseline has been established. The following components and operations have been successfully verified on a local system:

- [x] **WordPress Core Core Integration**: WordPress core installs successfully in the isolated folder layout.
- [x] **Theme Activation**: The custom theme activates successfully without triggering PHP execution errors or broken layout paths.
- [x] **Plugin Activation**: The custom plugin activates successfully, registering the `weardale_event` custom post type and `strand` taxonomies.
- [x] **Database Isolation**: Complete isolation of the legacy `weardale_together` database and folder structure from `weardale_together_v2` is verified.
- [x] **Database Seeding**: The initial development seed content imports successfully into the empty WordPress tables post-installation.

---

## 10. Outstanding Work & Future Phases
Before this project is ready for formal deployment, several engineering audits and validation stages remain outstanding.

### 🔴 High Priority (Remaining Technical Milestones)
* **Accessibility Audit**: Formally audit the theme template renders against WCAG 2.2 AA checklists using screen readers and automated evaluation tools.
* **Browser Compatibility Testing**: Verify CSS grid, flex, and custom properties render uniformly across all modern desktop and mobile browsers (Chrome, Safari, Firefox, Edge).
* **Hosting Migration Validation**: Validate backup, restoration, and database path translation procedures on target remote hosts.

### 🟡 Medium Priority
* **Performance Testing**: Measure PageSpeed, database query load times, and implement standard static object caching policies.
* **Security Review**: Audit PHP theme scripts, form sanitation inputs, and admin permissions hierarchy.
* **Client Acceptance Testing**: Conduct full visual walk-throughs and content editing onboarding cycles with Weardale Together stakeholders.

---

## 11. Known Issues & Limitations
* **Mailchimp Embed Code**: The homepage newsletter section is a placeholder awaiting Mailchimp integration. It does not possess operational live synchronization capabilities in this baseline state and must be wired to real audience registration action endpoints in the production stage.
* **Starter Database Seed**: The file `/scripts/seed-db.sql` is an optional local development seed intended only for a fresh WordPress installation using the standard `wp_` table prefix. It contains demonstration content, should not be treated as real or final client content, and is not designed to be imported repeatedly over existing tables.
* **Mock Event Database Simulators**: The AI Studio React Preview has an interactive event generator that outputs SQL queries. These are visually precise for development sandboxing but do not modify the real local WordPress database automatically.

---

## 12. Release Vision & Deliverables
The final project release package will bundle the custom assets into a lightweight, portable payload:
1. **Custom Theme ZIP**: `weardale-together-theme.zip` (portable classic theme).
2. **Custom Plugin ZIP**: `weardale-platform-plugin.zip` (portable editorial plugin).
3. **Database Seed SQL**: Standard MySQL schema dump.
4. **Operations Manuals**: Up-to-date `.md` guides.
5. **Release Integrity**: Manifest with MD5 checksums of critical PHP templates to prevent tampering.

---

## 13. Development Principles
* **WordPress Core First**: Respect standard WordPress core architectures. Never introduce custom frameworks (like React or Tailwind) into the production PHP templates unless explicitly mandated.
* **Accessibility Compliance**: Retain WCAG 2.2 AA standards as a critical design constraint. Ensure keyboard, semantic, and contrast integrity.
* **Portable Hosting**: Code defensively using standard relative path helpers (such as `get_template_directory_uri()` and `plugin_dir_path()`) so the site is instantly deployable across diverse host environments.
* **No Unnecessary Overengineering**: Standard, clean, performant classic PHP templates are preferred over complex block-editor scaffolding or unrequested modules.
