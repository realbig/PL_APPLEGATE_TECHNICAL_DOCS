<?php
/**
 * Provides helper functions.
 *
 * @since      0.1.0
 *
 * @package    TechnicalDocs
 * @subpackage TechnicalDocs/core
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Returns the main plugin object
 *
 * @since 0.1.0
 *
 * @return TechnicalDocs
 */
function TECHNICALDOCS() {
	return TechnicalDocs::getInstance();
}