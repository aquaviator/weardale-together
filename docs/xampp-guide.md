# Weardale Together CIC — Local XAMPP Setup & Portability Guide

This guide describes how to run and test the Weardale Together WordPress theme and plugin locally using a standard **XAMPP** environment (Apache, MariaDB, PHP).

---

## 🔒 1. CRITICAL SAFETY & DATABASE ISOLATION POLICY
Before beginning, you must understand and respect the isolation rules of the Weardale Together environment:
* **DO NOT MODIFY OR TOUCH** the existing local setup in `C:\xampp\htdocs\WT\` or its associated database `weardale_together`. This is a legacy environment and must remain completely untouched to prevent data loss or service disruption.
* **MANDATORY NEW ENVIRONMENT**: You must perform all of your new development and testing within a dedicated, isolated workspace located at `C:\xampp\htdocs\weardale-together\` using the database `weardale_together_v2`.
* **Zero Contamination**: Never reuse passwords, users, or schemas between the old environment and the new environment.

---

## 🛠️ 2. Prerequisites & System Configuration
Ensure you have the following installed on your local machine:
* **Operating System**: Windows 10/11 or macOS.
* **XAMPP**: Running PHP 8.0 or higher (PHP 8.1+ recommended).
* **WordPress**: A fresh, clean download of WordPress core (version 6.0+ recommended) from [WordPress.org](https://wordpress.org/download/).

---

## 📂 3. Directory Installation & File Mappings
To set up your file system without contaminating WordPress core or committing configuration secrets:

1. Create a new directory under your XAMPP installation:
   `C:\xampp\htdocs\weardale-together\`
2. Download and extract the standard WordPress core files into this new directory.
3. Replace or map the default `wp-content/` folders with the custom assets from this repository:
   * The contents of `/wordpress/wp-content/themes/weardale-together/` in this repository should reside at:
     `C:\xampp\htdocs\weardale-together\wp-content\themes\weardale-together\`
   * The contents of `/wordpress/wp-content/plugins/weardale-platform/` in this repository should reside at:
     `C:\xampp\htdocs\weardale-together\wp-content\plugins\weardale-platform\`

*Tip: For smooth local development, you can use a directory symbolic link (symlink) to map the files directly from your Git workspace into your XAMPP installation folder so you don't have to copy files back and forth.*

---

## 🗄️ 4. Isolated Database Setup (`weardale_together_v2`)
To initialize your local database securely:

1. Launch **XAMPP Control Panel** and start both the **Apache** and **MySQL/MariaDB** services.
2. Open your browser and navigate to the phpMyAdmin panel: `http://localhost/phpmyadmin/`
3. Click on the **Databases** tab in the main navigation.
4. Input the new, isolated database name: `weardale_together_v2`.
5. Select the collation: `utf8mb4_unicode_ci` and click **Create**.
6. **Seeding the Database**:
   * Select your newly created `weardale_together_v2` database.
   * Click on the **Import** tab.
   * Choose the file located at `scripts/seed-db.sql` in this repository.
   * Click **Go** to execute the seeding. This will populate your isolated database with the pages, event custom post types, metadata, and taxonomy terms representing our current information architecture without touching legacy records.

---

## 📝 5. Secure Local Configuration (`wp-config.php`)
To ensure security credentials are kept local and never committed to version control:

1. Inside `C:\xampp\htdocs\weardale-together\`, locate `wp-config-sample.php` and rename a copy of it to `wp-config.php`.
2. Open `wp-config.php` in your editor and configure your database parameters:

```php
// ** Database Settings - Configured for isolated weardale_together_v2 ** //
define( 'DB_NAME', 'weardale_together_v2' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' ); // Default XAMPP MariaDB password is empty
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

/**
 * Authentication unique keys and salts.
 * CRITICAL: Always generate fresh salts for your local environment.
 * Generate these from: https://api.wordpress.org/secret-key/1.1/salt/
 */
define('AUTH_KEY',         'insert-your-random-generated-salt-here');
define('SECURE_AUTH_KEY',  'insert-your-random-generated-salt-here');
define('LOGGED_IN_KEY',    'insert-your-random-generated-salt-here');
define('NONCE_KEY',        'insert-your-random-generated-salt-here');
define('AUTH_SALT',        'insert-your-random-generated-salt-here');
define('SECURE_AUTH_SALT', 'insert-your-random-generated-salt-here');
define('LOGGED_IN_SALT',   'insert-your-random-generated-salt-here');
define('NONCE_SALT',       'insert-your-random-generated-salt-here');

/**
 * WordPress database table prefix.
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 * Set to true during local development to catch PHP warnings and errors.
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true ); // Writes errors to wp-content/debug.log
define( 'WP_DEBUG_DISPLAY', false ); // Keeps layout clean from raw warning outputs
```

3. **Verify Git Exclusion**: Verify that `wp-config.php` is excluded by checking your `.gitignore` file. It should never be committed to the repository, keeping local database credentials and salts safe.

---

## 🚀 6. Web Installer & Component Activation
1. Navigate to `http://localhost/weardale-together/` in your web browser.
2. Select your language and complete the basic WordPress installation wizard (define your site title, admin username, password, and email).
3. Log in to the administrative dashboard at `http://localhost/weardale-together/wp-admin/`
4. **Activate Theme**: Navigate to **Appearance > Themes**, find **Weardale Together**, and click **Activate**.
5. **Activate Plugin**: Navigate to **Plugins > Installed Plugins**, locate **Weardale Platform**, and click **Activate**.

---

## 🔗 7. Permalink Configuration (Apache URL Rewriting)
To ensure clean, search-engine friendly URLs and custom post-type routing function without throwing 404 errors:
1. Navigate to **Settings > Permalinks** in your WordPress sidebar.
2. Select the **Post name** option.
3. Click **Save Changes**. This forces WordPress to automatically write the `.htaccess` rewrites into the `C:\xampp\htdocs\weardale-together\` directory.

---

## 🔍 8. Production Portability & Audit Notes
Our core custom PHP theme and plugin codebase has been thoroughly audited for machine-specific dependencies and hardcoded routes:
* **Relative Assets**: The theme strictly avoids hardcoding URLs like `localhost/WT`. It resolves asset and file references dynamically using `get_template_directory_uri()` and `get_stylesheet_uri()`.
* **Platform Constants**: The `weardale-platform` plugin dynamically resolves its root folder and web URLs using native `plugin_dir_path( __FILE__ )` and `plugin_dir_url( __FILE__ )` APIs, ensuring complete server and folder portability.
* **Sanitized Seeding**: The `seed-db.sql` database script is completely system-agnostic, using relative identifiers and database hooks instead of hardcoded domain options (like `siteurl` or `home`), avoiding contamination of host configurations.

---

## 🛠️ 9. Troubleshooting Common Local Issues
* **Error: Port 80 in Use (Apache crashes on startup)**:
  * *Cause*: Skype, IIS, or other local processes are occupying port 80.
  * *Fix*: In the XAMPP Control Panel, click **Config > Apache (httpd.conf)**. Search for `Listen 80` and change it to `Listen 8080`. Then access your local environment via `http://localhost:8080/weardale-together/`.
* **Error: 404 on Event Post Types**:
  * *Cause*: WordPress rewrite rules haven't been updated since registering the `weardale_event` post-type.
  * *Fix*: Simply navigate to **Settings > Permalinks** and click **Save Changes** to refresh the rewrite buffer.
* **Error: MySQL shuts down unexpectedly**:
  * *Cause*: Corruption of MariaDB data tables or port conflicts on `3306`.
  * *Fix*: Run XAMPP Control Panel as an Administrator.

---

## ✅ 10. Local Installation & Isolation Validation Checklist
Run this checklist after finishing your local setup to guarantee your environment is completely validated, functional, and fully isolated:

- [ ] **Check 1: Directory Isolation**
  Verify that your files reside in `C:\xampp\htdocs\weardale-together\` and that **absolutely no modifications** have been made to `C:\xampp\htdocs\WT\`.
- [ ] **Check 2: Database Isolation**
  Open phpMyAdmin and verify that the database in use is `weardale_together_v2`. Ensure `weardale_together` is untouched.
- [ ] **Check 3: Clean Theme Loading**
  Visit `http://localhost/weardale-together/` and confirm the homepage renders without any CSS broken paths or PHP warnings.
- [ ] **Check 4: Accessible Homepage Hub**
  Confirm the interactive hub-and-spoke diagram displays correctly. Shrink your browser window and check that it folds gracefully into a clean mobile layout.
- [ ] **Check 5: Dynamic Strand Branding**
  Navigate to a sub-page of a specific strand (e.g. `/cafe/` or `/roots-shoots/`) and confirm that the body tag contains the appropriate class (e.g., `.strand-cafe`, `.strand-shoots`) and applies the correct color palette and custom header layout.
- [ ] **Check 6: Custom Event Registration**
  Log in to the wp-admin panel, navigate to **Events**, and add a test event. Verify that metadata fields (Event Date, Location, Cost) save successfully and display on the event detail view.
- [ ] **Check 7: Debug Log Validation**
  Check that `wp-content/debug.log` has been created if any PHP warnings arise, but that no raw notices are printed directly onto user-facing screens.
- [ ] **Check 8: Accessibility Check**
  Verify that you can navigate the primary homepage links and buttons completely using the `Tab` and `Enter` keys on your keyboard, with clear halo focus highlights visible.
