<?php
/* 
 *	Plugin Name: Colibri Page Builder 
 *  Author: ExtendThemes
 *  Description: Colibri Page Builder adds drag and drop page builder functionality to the ColibriWP theme.
 *
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 * Version: 1.0.81
 * Text Domain: colibri-page-builder
 */

if (!in_array(get_option('template'), array('colibri-wp', 'colibri'))) {
	return;
}


if (class_exists('\ColibriWP\PageBuilder\PageBuilder')) {
	return;
}

// Make sure that the companion is not already active from another theme
if (!defined("COLIBRI_PAGE_BUILDER_AUTOLOAD")) {
	require_once __DIR__ . "/vendor/autoload.php";
	define("COLIBRI_PAGE_BUILDER_AUTOLOAD", true);
}

if (!defined("COLIBRI_PAGE_BUILDER_VERSION")) {
	define("COLIBRI_PAGE_BUILDER_VERSION", "1.0.81");
}


\ColibriWP\PageBuilder\PageBuilder::load(__FILE__);
add_filter('colibri_page_builder/installed', '__return_true');


require_once 'extend-builder/extend-builder.php';
