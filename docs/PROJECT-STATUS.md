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
| **Current Milestone** | Milestone 13: Newsletter, Legal & Configuration Integration Completed |
| **Project Health** | Excellent, featuring robust newsletter components, dynamic configurations, legal page dropdown selectors, and integrated configuration health diagnostics. |
| **Development Phase** | Production Release Preparation, Configuration Validation, & Accessibility Audit Preparation |
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
- [x] **Brand Logo Integration**: Built full support for standard Custom Logo (`the_custom_logo()`) across desktop, mobile, and footer contexts, equipped with a styled monogram monogram fallback.
- [x] **Header Navigation Polish**: Implemented semi-transparent sticky positioning utilizing backdrop-blur, re-themed the mobile slide-down menu in organic cream colors, and added letter-spaced uppercase typography.
- [x] **Editorial Typography Overhaul**: Instituted `72ch` column limits, square bullet list markers, and balanced vertical margins on post layouts to optimize readability.
- [x] **Reusable Block Layouts**: Styled core editorial blocks including Editorial Callouts, Feature Cards, blockquotes, Info Panels, and Highlight Boxes in components.css for layout creation by content authors.
- [x] **Event Management System (Sprint 9)**: Expanded custom editorial schemas with date ranges, booking statuses, audience requirements, and custom booking maps.
- [x] **Unified What's On Archive (Sprint 9)**: Developed `archive-weardale_event.php` utilizing reusable card template files, providing instant filtration by strand and separating upcoming vs past schedules with clear counts.
- [x] **Single Event detail template (Sprint 9)**: Completed `single-weardale_event.php` detailing all expanded schema metadata utilizing an aside complement sidebar to assist assistive technologies and guarantee WCAG 2.2 AA.
- [x] **Mobile Breakpoint Harmony (Sprint 9)**: Adjusted the primary header and mobile layout breakpoint to `1120px` to perfectly prevent wrapping of navigation elements.
- [x] **Automatic Permalinks Flush (Sprint 9)**: Programmed automatic URL rewrite flushing on plugin activation to apply the `/whats-on/` slug instantly.
- [x] **Community Directory CPT (Sprint 10)**: Created the single unified directory post-type `weardale_directory` and supporting taxonomies `directory_type`, `village`, and `service_area` to represent all listing types flexibly.
- [x] **Metadata & Meta Box (Sprint 10)**: Designed a high-contrast admin meta editor with responsive, keyboard-accessible tabs (Contact, Details, Socials) and rigorous security/sanitization checks.
- [x] **Query API Layer (Sprint 10)**: Built `weardale_platform_query_directory()` that performs advanced filtering by keyword, village, type, service area, verified status, and accessibility details.
- [x] **Seed Data Engine (Sprint 10)**: Engineered an idempotent, repeat-safe seeding system pre-populating 15 real-world-inspired Weardale listings, marked with `_weardale_demo_content = 1` and protected from over-writing edited records.
- [x] **Public Presentation & Grid (Sprint 10)**: Programmed custom archive list screens (`archive-weardale_directory.php`), single listing templates (`single-weardale_directory.php`), reusable cards (`card.php`), and empty state handlers, fully compliant with WCAG 2.2 AA guidelines.
- [x] **Navigation Setup Safety & Verification (Sprint 10B.1)**:
  - **Explicit Setup Workflow**: Removed all automatic site-configuration writes from initialization hooks (`admin_init`, `init`, etc.). The setup only executes upon an explicit form submission under **Tools → Weardale Site Setup** with rigorous nonce validation, `manage_options` check, and an intentional button click.
  - **Restrained Administrator Notices**: If the `Primary Navigation` menu location is unassigned, a polite, non-intrusive warning notice is displayed in the WordPress admin area only to users with the `manage_options` capability. The notice is completely hidden from the public-facing frontend, does not write anything to the database, and automatically disappears once the Primary menu location is assigned.
  - **Repeat-Safe and Preservation-Proof Menu Setup**:
    - *First Run*: Creates missing menus (Primary, Footer, Legal), populates items, and assigns locations only if unassigned. Items with missing destinations are automatically skipped using dynamic `weardale_platform_destination_exists()` checks, preventing placeholder links.
    - *Second Run*: Runs with absolute safety, producing no duplicate menus or items, making no changes to menu order, and preserving manually modified menus entirely. It reports that existing configurations have been successfully preserved.
  - **Menu Structures & Dynamically Checked Destinations**:
    - *Primary*: Home (`/`), Root & Branch Café (`/cafe/`), Young People (`/young-people/`), Creative Arts (`/creative-arts/`), Roots & Shoots (`/roots-shoots/`), What’s On (dynamic event archive link), Directory (dynamic directory archive link), and About WT (`/about/`).
    - *Footer*: News & Blog (`/news-blog/`), Volunteer With Us (`/volunteer/`), Newsletter Sign-up (`/newsletter/`), and Get In Touch (`/contact-us/`).
    - *Legal*: Privacy Notice (`/privacy-notice/`).
  - **Dynamic Check Helper**: Implemented `weardale_platform_destination_exists()`, which parses slugs dynamically, checks database existence of published pages via `get_page_by_path()`, and skips adding items if pages are missing (preventing dead links).
  - **Homepage Directory Integration**:
    - Integrated the Community Directory Promo block (`template-parts/homepage/directory-promo.php`) as a calm, editorial, rural, high-contrast entry point on the homepage.
    - Features an elegant Weardale palette (soft cream background, forest green badge, and a soft clay terracotta border utilizing the `--color-strand-shoots` variable).
    - Showcases custom high-contrast category icons (`🏛️`, `🚌`, `🎨`, `🤝`) in a clean, bento-grid aligned layout.
  - **Fallback Menu Review**:
    - Integrated a read-only fallback menu function `weardale_together_fallback_menu()` in `header.php`.
    - Dynamically retrieves the directory archive link with `get_post_type_archive_link( 'weardale_directory' )` and events archive link.
    - Properly escaped, performs no database writes, and is instantly replaced once a menu is formally assigned to the `primary-menu` location.
- [x] **Editorial News & Stories Platform (Sprint 11)**:
  - **Standard Post Extension**: Enhanced the native WordPress `post` system with rich Story Editorial Metadata without introducing redundant custom post types, aligning with client requirements.
  - **Secure Editorial Controls**: Implemented a styled administration meta editor panel (`news-meta.php`) with robust nonce checks, full sanitization, and user capability authorization for Programme, Event, and Directory associations.
  - **Repeat-Safe Seed Framework**: Created a dynamic seeder (`news-seed.php`) that automatically registers standard categories (Cafe, Creative Arts, Young People, etc.) and populates 10 high-quality community story articles. Identifies existing stories via `_weardale_demo_key` to prevent duplication and dynamically binds relationships using listing keys.
  - **Unified & Accessible Public Views**:
    - *home.php*: Crafted a high-end editorial Journal archive featuring active-state category navigation pills, search filters, an elegant Featured Story spotlight banner, a three-column responsive card grid, and accessible pagination.
    - *archive.php & search.php*: Oversaw identical editorial designs for category archives, tags, and keyword results with beautiful empty-state notices.
    - *single.php*: Structured the single post view to display publishing metadata and a "Community Connections" panel dynamically linking related programmes, events, and facilities.
  - **Homepage Spotlight Integration**: Updated `template-parts/homepage/news.php` to present 1 prominent Featured Story followed by 3 Latest Stories in a responsive bento layout, preventing any duplicate post display.
- [x] **Programme Story Relationship Runtime Fix (Sprint 11.1B)**:
  - **Central Normalization Helper**: Programmed a central mapping layer `weardale_platform_normalize_programme_key()` and variant generator `weardale_platform_get_programme_variants()` inside `editorial.php`. This maps display-driven or legacy keys (e.g. `'creative'`, `'cafe'`) to their corresponding canonical identifiers (e.g. `'creative-arts'`, `'root-branch-cafe'`).
  - **Robust Frontend Queries**: Updated `content-strand.php` to fetch variants for the active strand and use `compare => 'IN'` in the `meta_query` of `WP_Query`, allowing posts to appear on strand pages regardless of whether they have a legacy or canonical identifier stored in database metadata.
  - **Clean Plugin Integration**: Modified `news-meta.php` to normalize metadata on retrieval and use canonical keys in the "Associated Programme Strand" dropdown, while standardizing saving pathways to guarantee all future updates are persisted strictly as canonical identifiers.
  - **Single Post Connection Badges**: Standardized the post connection badges in `single.php` to handle both canonical and legacy identifiers seamlessly, preventing broken badge displays or links.
- [x] **Participation & Engagement (Sprint 12)**:
  - **Unified Enquiry Workflow**: Engineered a core database/PHP module (`participation.php`) providing fully unified enquiry processing for general contact, volunteers, programmes, and event contexts.
  - **Server-Side Processing & Nonce Protection**: Implemented secure post submission via `admin-post.php` with robust nonce verification, input sanitization (`sanitize_text_field`, `sanitize_textarea_field`), and validation errors stored in localized `$_SESSION` caches for instant user correction.
  - **Elegantly Handled Contextual Binding**: Automatically bind the post context dynamically to form submissions for programmes, events, and community directories. Added contextual "Enquire Online" buttons into directory sidebars and refined "Enquire About This Event" buttons in event templates to carry queries seamlessly.
  - **Spam Mitigation & Security**: Built standard honeypot checks (`wt_honey`) to silently filter bot traffic, and created a client IP-based transient rate limiter to restrict users to a maximum of 5 messages per hour.
  - **Advanced Administrator Controls**: Expanded **Tools → Weardale Site Setup** with complete, beautifully integrated form inputs allowing administrators to toggle the live enquiry system, customize the on-screen success wording, manage postal addresses and phone numbers, configure public hours, specify the delivery recipient email, and toggle standard "Reply-To" email headers.
  - **Mailchimp Integration**: Integrated a clean option in the admin settings panel to configure the Mailchimp Form Action URL. If blank, it displays a polite, custom-designed, and styled placeholder box; if populated, it swaps with a standard, accessible, responsive native Mailchimp form.
  - **Repeat-Safe Development Seeder**: Built a robust seeder pre-populating 3 realistic Volunteer Opportunity entries and 1 customizable directory listing equipped with on-site enquiries enabled, alongside standard setup fields.
  - **WCAG 2.2 AA Compliance**: Ensured that all custom templates feature clean error summaries linked to fields, high-contrast visual cues, and explicit keyboard-navigable accessibility focus anchors.
- [x] **Newsletter, Legal & Configuration Integration (Sprint 13)**:
  - **Dynamic Organisation & Legal Meta Fields**: Added database settings options inside **Tools → Weardale Site Setup** to configure legal terms, privacy notices, and cookie policy pages via standard WordPress dropdown menus (`wp_dropdown_pages`), plus core registration details (CIC company number, registered charity numbers, and company abbreviations).
  - **Dynamic Footer Transformation**: Refactored `footer.php` to remove hardcoded branding, physical locations, and contact coordinates. The footer dynamically retrieves all organisation data, email links, phone actions, social profiles, and the custom legal privacy notice page.
  - **Centralised Reusable Newsletter Component**: Formulated `weardale_platform_get_newsletter_form()` supporting responsive and context-aware styling layouts for Pages, Homepages, and Footers. Seamlessly embeds live Mailchimp submission rules with full i18n support, automated consent notices, and administrative feedback helpers.
  - **Integrated Configuration Health Board**: Added a diagnostic dashboard card to the Weardale Site Setup screen checking for operational completeness (such as Mailchimp availability, privacy page selections, email configurations, and site logo presence), allowing administrators to identify and resolve missing settings proactively.

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
* **Mailchimp Integration**: Mailchimp forms are now fully integrated and dynamically styled across Page, Homepage, and Footer layouts. They activate instantly when the administrative `weardale_mailchimp_url` setting is populated, fallback gracefully to responsive "coming soon" placeholders if blank, and require zero manual template edits.
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
