<?php
/**
 * Plugin Name: Technical Documents
 * Description: Provides the CPT for technical documents.
 * Author: Joel Worsham
 * Author URI: http://realbigmarketing.com
 * Version: 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Define plugin constants
define( 'TECHNICALDOCS_VERSION', '0.1.0' );
define( 'TECHNICALDOCS_DIR', plugin_dir_path( __FILE__ ) );
define( 'TECHNICALDOCS_URL', plugins_url( '', __FILE__ ) );

/**
 * Class TechnicalDocs
 *
 * Initiates the plugin.
 *
 * @since   0.1.0
 *
 * @package TechnicalDocs
 */
class TechnicalDocs {

	public $cpt_document;

	private function __clone() { }

	private function __wakeup() { }

	/**
	 * Returns the *Singleton* instance of this class.
	 *
	 * @since     0.1.0
	 *
	 * @staticvar Singleton $instance The *Singleton* instances of this class.
	 *
	 * @return TechnicalDocs The *Singleton* instance.
	 */
	public static function getInstance() {

		static $instance = null;

		if ( null === $instance ) {
			$instance = new static();
		}

		return $instance;
	}

	/**
	 * Initializes the plugin.
	 *
	 * @since 0.1.0
	 */
	protected function __construct() {

		$this->add_base_actions();
		$this->require_necessities();
	}

	/**
	 * Requires necessary base files.
	 *
	 * @since 0.1.0
	 */
	public function require_necessities() {

		require_once __DIR__ . '/core/class-technicaldocs-document-cpt.php';
		$this->cpt_document = new TechnicalDocs_Document_CPT();
	}

	/**
	 * Adds global, base functionality actions.
	 *
	 * @since 0.1.0
	 */
	private function add_base_actions() {

		add_action( 'init', array( $this, '_register_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, '_enqueue_admin_assets' ) );
	}

	/**
	 * Registers the plugin's assets.
	 *
	 * @since 0.1.0
	 */
	function _register_assets() {

		// Admin
		wp_register_script(
			'technical-docs-admin',
			TECHNICALDOCS_URL . '/assets/js/technicaldocs-admin.js',
			array( 'jquery' ),
			TECHNICALDOCS_VERSION
		);

		// Chosen
		wp_register_script(
			'technical-docs-chosen',
			TECHNICALDOCS_URL . '/includes/vendor/chosen/chosen.jquery.min.js',
			array( 'jquery' ),
			'1.4.2'
		);

		wp_register_style(
			'technical-docs-chosen',
			TECHNICALDOCS_URL . '/includes/vendor/chosen/chosen.min.css',
			array(),
			'1.4.2'
		);
	}

	function _enqueue_admin_assets() {

		wp_enqueue_script( 'technical-docs-admin' );
		wp_enqueue_script( 'technical-docs-chosen' );
		wp_enqueue_style( 'technical-docs-chosen' );
	}
}

require_once __DIR__ . '/core/technicaldocs-functions.php';
TECHNICALDOCS();