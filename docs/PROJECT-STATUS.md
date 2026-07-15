# Weardale Together CIC — Reconstructed WordPress Project Status

This document serves as the primary, living engineering handover and status report for the reconstructed **Weardale Together CIC** web platform. It details the current milestone, dual-layer architecture, strict environmental isolation policies, repository layout, and development guidelines for senior engineers inheriting the codebase.

---

## 1. Project Overview
Weardale Together is a grassroots Community Interest Company (CIC) serving remote rural populations in the North Pennines valley (Stanhope, County Durham). 

* **Purpose**: Provide a modern, accessible web portal presenting the organization's multiple community strands (Café, Creative Arts, Youth, and Playrooms) alongside direct events tracking and community news.
* **Audience**: Isolated rural residents, local families, elderly community members, regional volunteers, and local authority funders.
* **Design Philosophy**: Warm, organic, handcrafted, and human. Built around the "Different Rooms in the Same House" motif (where each of the four core strands has its own visual flavor, colors, and layout while sharing a cohesive parent architecture).
* **Brief Alignment**: Rebuilt strictly to match the client brief's core specifications, including WCAG 2.2 AA accessibility standards, an interactive hub-and-spoke homepage diagram, and local portable hosting requirements.

---

## 2. Current Status

| Parameter | Value / Status |
| :--- | :--- |
| **Current Milestone** | Milestone 6: Core Repository Alignment & Local Portability Completed |
| **Project Health** | Excellent (Clean linter feedback, successful builds, verified PHP templates) |
| **Development Phase** | Local Testing & Handover Preparation (Pre-Sprint 7) |
| **Repository** | `https://github.com/aquaviator/weardale-together` |
| **Branch** | `main` |

---

## 3. Architecture & Dual-Environment Model
The codebase is structured under an intentional **dual-layer model** separating development tooling from production CMS deliverables.

```
                  ┌──────────────────────────────────────────────────┐
                  │          weardale-together/ (Git Repository)      │
                  └─────────┬──────────────────────────────┬─────────┘
                            │                              │
                            ▼                              ▼
             ┌──────────────────────────────┐┌──────────────────────────────┐
             │ LAYER 1: DEVELOPMENT PREVIEW  ││ LAYER 2: PRODUCTION DELIVERABLE│
             │ (Vite / React / TypeScript)  ││ (Classic WordPress CMS Engine│
             ├──────────────────────────────┤├──────────────────────────────┤
             │ • Rapid visual prototyping   ││ • Production PHP 8.0+ / MySQL│
             │ • Sandbox visual playground  ││ • Custom WP Theme            │
             │ • Offline state engines      ││ • Custom Editorial Plugin     │
             │ • Local NodeJS / Vite server ││ • No Node/React dependencies │
             └──────────────────────────────┘└──────────────────────────────┘
```

### Layer 1: AI Studio Prototyping Workbench (Development Tooling)
* **Components**: `/src/`, `index.html`, `package.json`, `package-lock.json`, `tsconfig.json`, `vite.config.ts`.
* **Purpose**: Allows visual prototyping, fast feedback loops, and user flow validation within a sandboxed environment.
* **Production Status**: Contains zero code that goes to the production WordPress live site. Node, Vite, React, and TypeScript are development tools only.

### Layer 2: Production WordPress Implementation (Production Target)
* **Components**: `/wordpress/wp-content/themes/weardale-together/` and `/wordpress/wp-content/plugins/weardale-platform/`.
* **Purpose**: The real-world classical WordPress CMS deployment target.
* **Execution Environment**: Standard PHP 8.0+, Apache/Nginx, MariaDB/MySQL. Completely portable to general WordPress hosting.

---

## 4. Repository Structure

```
weardale-together/
├── .gitignore                      # Configured to track only custom assets & ignore core files
├── README.md                       # Comprehensive onboarding & architectural document
├── package.json                    # Workbench dependencies and scripts
├── index.html                      # Workbench visual entry point
├── src/                            # Workbench React/TS source files
├── scripts/
│   └── seed-db.sql                 # Agnostic MariaDB SQL query script to seed databases
├── docs/
│   ├── xampp-guide.md              # Local XAMPP installation, isolation & validation checklist
│   ├── editor-guide.md             # Standard operating manual for non-technical content editors
│   └── PROJECT-STATUS.md           # This status and handover document (living asset)
└── wordpress/                      # Main WordPress content container
    └── wp-content/
        ├── plugins/
        │   └── weardale-platform/  # Custom Editorial post types & admin dashboard columns
        └── themes/
            └── weardale-together/  # Custom Classic PHP Theme (Core layout & template parts)
```

---

## 5. Local Development Environment Setup
The reconstructed project is configured for isolated development inside a distinct folder and database to prevent conflicts.

* **Target Local Path**: `C:\xampp\htdocs\weardale-together\`
* **Custom Theme Path**: `C:\xampp\htdocs\weardale-together\wordpress\wp-content\themes\weardale-together\`
* **Custom Plugin Path**: `C:\xampp\htdocs\weardale-together\wordpress\wp-content\plugins\weardale-platform\`
* **Isolated Database**: `weardale_together_v2`
* **Local url**: `http://localhost/weardale-together/wordpress/` (or matching port if configured on non-standard ports)

---

## 6. Protected Legacy Project
The older local installation and database are considered **strictly protected legacy resources** and must remain completely untouched.

* **Protected Path**: `C:\xampp\htdocs\WT\`
* **Protected Database**: `weardale_together`
* **Protected Local url**: `http://localhost/WT/`

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
* **Accessibility**: Fully keyboard navigability with visible tab-halos, a standard "Skip-to-Content" bypass skip-link, and strict semantic HTML landmarks (such as `<main role="main">` and `<nav>`).

### Custom Plugin: `weardale-platform`
* **Responsibilities**: Editorial experience enhancements, custom data modeling, and meta configuration.
* **Custom Post Type (CPT)**: Registers the `weardale_event` post-type to model and display activity schedules.
* **Custom Taxonomy**: Registers the hierarchical `strand` taxonomy to group posts, pages, and events into strands.
* **Admin Column Injection**: Extends the `weardale_event` admin lists in `/wp-admin/` with custom custom columns for immediate calendar tracking by administrators.

---

## 9. Completed Milestones
- [x] **Repository Reconstruction**: Structured clean split between development workbench and WordPress core folders.
- [x] **Theme Handcrafting**: Built classic templates with custom SVG hub-and-spoke interactive diagram and mobile responsive grid.
- [x] **Plugin Development**: Implemented `weardale-platform` plugin with Custom Post Types, Meta Boxes, and nonces.
- [x] **Environmental Isolation**: Separated legacy `WT` environment from new `weardale-together` environment.
- [x] **Database Isolation**: Protected the legacy database and set up local `weardale_together_v2` database target.
- [x] **Seed Scripts**: Created portable database seeds inside `scripts/seed-db.sql`.
- [x] **Documentation**: Produced a comprehensive `xampp-guide.md` and content-focused `editor-guide.md`.

---

## 10. Outstanding Work (Roadmap)

### 🔴 High Priority
* **WP-CLI Migration Scripts**: Finalize automated, robust CLI methods for local content setups.
* **Production Salt Setup**: Prepare fresh environment configurations for migration to live server.

### 🟡 Medium Priority
* **Events refinement**: Establish more advanced date sorting filters (such as hiding past events automatically) inside `whats-happening.php`.
* **Visual Media Seeding**: Setup responsive fallback images within the media library for local installations.

### 🟢 Future Enhancements
* **Mailchimp Block Embed integration**: Polish Gutenberg HTML custom block styles to wrap the newsletter signup cleanly.

---

## 11. Known Issues & Limitations
* **Mailchimp Embed Code**: The homepage newsletter form is a secure placeholder. It functions dynamically locally and must be wired to real audience action links in live server widgets.
* **Mock Event Database Simulators**: The AI Studio React Preview has an interactive event generator that outputs SQL queries. These are visually precise for testing but do not modify the real local WordPress database automatically.

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
