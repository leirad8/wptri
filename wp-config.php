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
// define('WP_CACHE', true); //Added by WP-Cache Manager
// define( 'WPCACHEHOME', '/Users/amiller/Development/fb-tip-wordpress/wp-content/plugins/wp-super-cache-old/' ); //Added by WP-Cache Manager

// Support multiple environments
// set the config file based on current environment

if (strpos($_SERVER['HTTP_HOST'],'localhost') !== false) { // local development
    $config_file = 'config/wp-config.local.php';
}
elseif  ((strpos(getenv('WP_ENV'),'stage') !== false) ||  (strpos(getenv('WP_ENV'),'prod' )!== false )){
    $config_file = 'config/wp-config.azure.php';
} else {
  die('WP_ENV not set');
}


$path = dirname(__FILE__) . '/';
if (file_exists($path . $config_file)) {
    // include the config file if it exists, otherwise WP is going to fail
    require_once $path . $config_file;
}

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
define('AUTH_KEY',         'DwsJ|~D2C^phV]@a8,r|vVYS}]B%S;l)!9y)XQ}WlBT#xCs^LN+)s48pV}]*t<NH');
define('SECURE_AUTH_KEY',  'S:0V}5DY)#</,&5/$JlP0}n(A>m0{22m-n],|R4mOSHEzC<|_zBJ;>B]16)o-NLo');
define('LOGGED_IN_KEY',    '&~#rRmmEDjIL,fJ,_BM|]o*jl?<f3wKGCp2L;`;UogQ=}Yr)_$!J0LwnWY]pA)pg');
define('NONCE_KEY',        '0fC$!+q[WUO,Nh?|Q):a}i.FhTrvN@MYFs{&]ja5nV!3BA;eU$4DQ+q|a+j[Y:@F');
define('AUTH_SALT',        'wOEgw^+Q]u)g[rQ^W giuh6(93 1PI1Bk{!V4=];xprH7ZIs7I1mWJW|*Z-$;v;_');
define('SECURE_AUTH_SALT', 'j3;L2&Aw1O,g$SsqU@6q5|w7u$n8}]v-zl !OL)*twjSunbEDI.@:U}YM`y:cqh.');
define('LOGGED_IN_SALT',   'C-(=89W7DZZ]bsxlY2Xu{qA!)-q0DL~X]0/]TvD3@VwDobh[rto7Rwcsc/j2[:!*');
define('NONCE_SALT',       'g%xRT@c|Bs<{&18YL%oxP=)))_G6QT)>$)s(n,i^jDQO/EFX3s^eu51<O0z1DQDq');

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

//dynamically change url per environment
define('WP_HOME', 'https://' . $_SERVER['HTTP_HOST']);
define('WP_SITEURL', 'https://' . $_SERVER['HTTP_HOST']);
define('WP_CONTENT_URL', '/wp-content');
define('DOMAIN_CURRENT_SITE', $_SERVER['HTTP_HOST']);

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

/* Google Analytics Info */
if ( !defined('GOOGLE_ANALYTICS_ID') )
	define('GOOGLE_ANALYTICS_ID', 'UA-106573168-1');

if ( !defined('GOOGLE_TRACKING_CONTAINER_ID') )
	define('GOOGLE_TRACKING_CONTAINER_ID', 'GTM-52VNCGF');
