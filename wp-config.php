<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'cdk');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'p{YhP>S6WzU1W3vsi[Hr)c*opyuMzk?6^tZvRi^~bhcp}}4-|rJObYh[v]?I*xC>');
define('SECURE_AUTH_KEY',  '&iJqr(|T7cChZ8AU|4tW-$IUYy$LgBuW~nJsv*r3:rAQ5V3.DIa,s+Qv{!SU2gxl');
define('LOGGED_IN_KEY',    '_yEz*[L=0Q!?`+#63f)3Vz@^X+]#!V38=tqxIT?:|eXRNZTf1Idao<0NkE4/N}.B');
define('NONCE_KEY',        'YakY.izl@*V9STF>6}nG+_BG|rj4qt-h/5[l+u9SodCB=L80<v{ba,JE>GH+t7>i');
define('AUTH_SALT',        'Ye/*^oveID<`1jVIL0<zE=Gt10oF`ULa0$5[kk2+G|]rS9q?FhP:FlO@O-dH%9|M');
define('SECURE_AUTH_SALT', 'F3?3&UUJ;4x5C -#%1|H|>J/t/egy#zT<6g]s[=[OhV>d+Y9Ih~~X&zy#bDUZi?s');
define('LOGGED_IN_SALT',   'vzE8u2nz#{5a7!5S^UI4Wx+Q,>0lVV7$6g`<17`<IxT)+1:WZt#M6H3OT&rXNWLT');
define('NONCE_SALT',       'd-l#ch6Hui#S#9N6%CxurS$inw*Uyj_$J|RMZJHDfx(v9<M|eEZ7,w<1fQGM)JI>');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */
define('WP_ALLOW_MULTISITE', true);
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', 'localhost');
define('PATH_CURRENT_SITE', '/cdk/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
