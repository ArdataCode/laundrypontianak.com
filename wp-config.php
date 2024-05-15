<?php
define( 'WP_CACHE', false ); 
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', "websit19_laundrypontianak");
/** MySQL database username */
define('DB_USER', "websit19_Laundryponttt");
/** MySQL database password */
define('DB_PASSWORD', "Ardata2024!");
/** MySQL hostname */
define('DB_HOST', "localhost");
/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');
/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');
/**#@+
 * Authentication Unique Keys and Salts.
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 */
;
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';
/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('FS_METHOD','direct');
define('FS_CHMOD_DIR', (0775 & ~ umask()));
define('FS_CHMOD_FILE', (0664 & ~ umask()));
/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define( 'WP_DEBUG', false );
#define( 'WP_PLUGIN_DIR', '/home/u1567848/public_html/laundrypontianak.com/wp-content/plugins' );
#define( 'WPMU_PLUGIN_DIR', '/home/u1567848/public_html/laundrypontianak.com/wp-content/mu-plugins' );
define( 'AUTH_KEY', 'qBuyD84rhjVPi5E1xuwvGHc7255gQrze27FVspu29zp6wpDFI7M3S4DGDp9Nfddf' );
define( 'SECURE_AUTH_KEY', 'x3Qiy3Dh3DBuUuexBfofJW7v5iUmAedUIrP8HQCQ7Sj61SpYAzdTztpJMCKqXE1S' );
define( 'LOGGED_IN_KEY', 'tmQ5Lyzy207JnjLr4bncPhYiHAiWHFVjxHfLNoeVN1PjgTHrFr3Wvm27JwI6suod' );
define( 'NONCE_KEY', 'iMBKWq2gpB3j46KhGdnexMI6shaGENyYjsN063XDfbB4D2JFrUaEAcKA1Sx0Aq3p' );
define( 'AUTH_SALT', 'xsIn0MbLHuyqDWPowLzwTtPHELU23p5Mr9fyFNgH8tqFhRhFgNeho0ShM5GXRt9W' );
define( 'SECURE_AUTH_SALT', 'DzNQmd1iuHsYqrt9VpAxur077xxWUS0SvrvJA7UsUWseYrXXmRWcfp2vWGDELnE1' );
define( 'LOGGED_IN_SALT', 'shXxIXQ495Dc1BE8av1AXRTupDgV3SbVfDSEKJ7xEP0RSynIVrrRzJfTMACQiDW7' );
define( 'NONCE_SALT', '73FdLJh7WIJyWXIPBmvSDSgYCgAbnYdwwH1iN4FgjG1TiyRQXTGdJjwwmIwXJ6Gc' );
/* That's all, stop editing! Happy blogging. */
/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
        define('ABSPATH', dirname(__FILE__) . '/');
/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');