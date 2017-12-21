 <?php
// MySQL settings
/** The name of the database for WordPress */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', getenv('DBNAME'));
/** MySQL database username */
define('DB_USER', getenv('DBUSER'));
/** MySQL database password */
define('DB_PASSWORD', getenv('DBPASSWORD'));
/** MySQL hostname */
define('DB_HOST', getenv('DBHOSTNAME'));
// echo('DB_HOST:'.DB_HOST);
// echo('<br/>DB_NAME:'.DB_NAME);
// echo('<br/>DB_USER:'.DB_USER);
// echo('<br/>DB_PASSWORD:'.DB_PASSWORD);

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 * Turn on debug logging to investigate issues without displaying to end user. For WP_DEBUG_LOG to
 * do anything, WP_DEBUG must be enabled (true). WP_DEBUG_DISPLAY should be used in conjunction
 * with WP_DEBUG_LOG so that errors are not displayed on the page */


define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
define('WP_DEBUG_DISPLAY',false);

//Security key settings
/** If you need to generate the string for security keys mentioned above, you can go the automatic generator to create new keys/values: https://api.wordpress.org/secret-key/1.1/salt **/
// define('AUTH_KEY',         getenv('authentication Key'));
// define('SECURE_AUTH_KEY',  getenv('secure Authentication Key'));
// define('LOGGED_IN_KEY',    getenv('logged In Key'));
// define('NONCE_KEY',        getenv('nonce Key'));
// define('AUTH_SALT',        getenv('authentication Salt'));
// define('SECURE_AUTH_SALT', getenv('secure Authentication Salt'));
// define('LOGGED_IN_SALT',   getenv('logged In Salt'));
// define('NONCE_SALT',       getenv('nonce Salt'));
define('AUTH_KEY',         'A^3A+0(S|agTl`uJ8sb> !l?#M.,~|*Oh|{k.&z)-YFq5TEZs; tmVc3m*lcB#M7');
define('SECURE_AUTH_KEY',  '-/Y.D/aq>2E{3v^1[!Vo ~2jC[}D$|5$N2[nTt]{wz[]#wI%0q8n+0!~#h?0|%:2');
define('LOGGED_IN_KEY',    'Cx!>T2C~t*M!Aoog!gjt5H!;-%-K,CPT)6oSq5R !^Ee<;9nRCG9v#1tKORG?}:Z');
define('NONCE_KEY',        '?uk,$Vt`%fQY-SFAzB#iu-#bji+NGnDV:l68<;.1l)lvA5%T7hSXh$B)0W#BA@&}');
define('AUTH_SALT',        '<doB)c^68d*w#- k{ q-2UR7yFlSh8%|MH/M5 BsMb?md4Q-wk``EQ2Gy=eN@NKk');
define('SECURE_AUTH_SALT', 'ZVsOT)kZ-Gc-P)BSJk(O15I1quBm!k !m,+{bKAi,l-f{qM]H%nR-}S=6tv*c#!)');
define('LOGGED_IN_SALT',   '|*a=./9WV|nIM-Pk1jn,B96`qQgLf8]LPQkp:_Th2GIYo$rG>)OW:,lP|R&DDy^L');
define('NONCE_SALT',       '++V8UVhK>0x?[}{*iMdeh%HfR<yJzvX-$:7]YfKX~W3MH ISs{k]MHD#/ ^<)}a+');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = getenv('DB_PREFIX');
