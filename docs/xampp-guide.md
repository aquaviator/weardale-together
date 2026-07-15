# Weardale Together CIC — Local XAMPP Setup & Portability Guide

This guide describes how to run and test the Weardale Together WordPress theme and plugin locally using a standard **XAMPP** environment (Apache, MariaDB, PHP).

---

## 1. Prerequisites & System Configuration
Ensure you have the following installed on your machine:
* **Operating System**: Windows 10/11 or macOS.
* **XAMPP**: Running PHP 8.0 or higher.
* **WordPress**: Core files (version 6.0+ recommended).

---

## 2. Directory Installation Mappings
Weardale Together should be mapped or cloned directly inside the XAMPP public directory:

* **Target Local Path**: `C:\xampp\htdocs\WT\`
* **Repository Layout mapping**:
  The contents of `/wordpress/` in the repository should map directly into the WordPress root directory `wp-content/`.
  
  ```
  C:\xampp\htdocs\WT\wp-content\themes\weardale-together\
  C:\xampp\htdocs\WT\wp-content\plugins\weardale-platform\
  ```

---

## 3. Database Initialization
To initialize your local database:

1. Launch **XAMPP Control Panel** and start **Apache** and **MySQL** services.
2. Open your browser and navigate to `http://localhost/phpmyadmin/`.
3. Click the **Database** tab.
4. Input the database name: `weardale_together`.
5. Select collation: `utf8mb4_unicode_ci` and click **Create**.
6. (Optional) Run the import queries from `scripts/seed-db.sql` to populate sample content immediately.

---

## 4. Local Configuration (`wp-config.php`)
Open `C:\xampp\htdocs\WT\wp-config.php` in your editor and configure the database connection keys:

```php
// ** Database Settings - You can get this info from your web host ** //
define( 'DB_NAME', 'weardale_together' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' ); // Default XAMPP password is empty
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

/**
 * Authentication unique keys and salts.
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
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

---

## 5. Web Installer & Activation Steps
1. Navigate to `http://localhost/WT/` in your browser.
2. Select your language and complete the installation wizard by setting up your admin account credentials.
3. Log in to the administrative dashboard (`http://localhost/WT/wp-admin/`).
4. Navigate to **Plugins > Installed Plugins** and click **Activate** under **Weardale Platform**.
5. Navigate to **Appearance > Themes** and click **Activate** under **Weardale Together**.

---

## 6. Permalink Setup (Apache URL Rewrite)
To ensure clean SEO-friendly URLs and custom post-type rewrites work without error:
1. Navigate to **Settings > Permalinks** in the sidebar.
2. Select **Post name** option.
3. Click **Save Changes**. This forces WordPress to generate or update the local `.htaccess` file inside your XAMPP folder.

---

## 7. Troubleshooting Common XAMPP Issues
* **Error: Port 80 in use (Apache crashes)**:
  * *Cause*: Another program (e.g., Skype, IIS) is occupying port 80.
  * *Fix*: In the XAMPP Control Panel, click **Config > Apache (httpd.conf)**. Search for `Listen 80` and change it to `Listen 8080`. Also update your local URL to `http://localhost:8080/WT/`.
* **Error: MySQL shuts down unexpectedly**:
  * *Cause*: Corruption of mysql tables or port conflicts.
  * *Fix*: Run XAMPP as Administrator, or check for port conflicts on `3306`.
