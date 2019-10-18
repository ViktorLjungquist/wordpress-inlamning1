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
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'pma' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '532;^wJ}.q/rPIk:EkIUB@@#RWpq)yU0!DBNT.,pU@(8};7gq|K3=J3,o>N_h%bZ' );
define( 'SECURE_AUTH_KEY',  '9RHe-kSp,@iD{BLkeEmQ^+uYLHx0DAAV-Q@om~oKT0`Pz>j<ta6&xd>0WR$Cx1QY' );
define( 'LOGGED_IN_KEY',    '%)T5w{1WXSIZIq}1G?rl`NyP,Tl{)3g`UOj{15ui_4j>jk$lK_d9M19$X:(dsKU9' );
define( 'NONCE_KEY',        'BV[n4x~E+/:TL}iwFwE6.XEy:xa0fBm|] eVusi~wL0a2I4W,`_3t{$o+LIzE0^m' );
define( 'AUTH_SALT',        'lU|[7-Tw{<!_@oReV?|cA4?W:jmTb]]xO1aDQy=PR&QZ8W-]PJ})7#=bGZ3)!Ex3' );
define( 'SECURE_AUTH_SALT', 'Av6!mN+T,cg*)M2a%))e]2@NVC!czR; [ y]AA){#*q<kz06, ENA.&*^UdM9|nd' );
define( 'LOGGED_IN_SALT',   'jf)CM1F<gJv>W&KTVYn=!d]ScN:0A$@d2331;$/}1^;f~lKrypGqE(#K+aWU/vqz' );
define( 'NONCE_SALT',       'Mrz Rq_sM4nCV!4vSi?9fVwa`fu5)|Z0d~9bjOxB+yNg*<@JWB{SI,xi`ogRuZAs' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
