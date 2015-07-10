<?php
/**
 * Creates and manages the shortcodes.
 *
 * @since      0.1.0
 *
 * @package    TechnicalDocs
 * @subpackage TechnicalDocs/core
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class TechnicalDocs_Shortcodes {

	public $shortcodes = array(
		'documents_list' => array(),
	);

	function __construct() {

		$this->add_shortcodes();
	}

	private function add_shortcodes() {

		foreach ( $this->shortcodes as $tag => $code ) {
			add_shortcode( $tag, array( $this, $tag ) );
		}
	}

	/**
	 * Shortcode callback for `documents_list`.
	 *
	 * @since {{VERSION}}
	 *
	 * @param array  $atts The shortcode attributes.
	 * @param string $content The shortcode content.
	 *
	 * @return string The output HTML.
	 */
	public function documents_list( $atts = array(), $content = '' ) {

		global $post;

		if ( ! $post || ! ( $post instanceof WP_Post ) ) {
			return $content;
		}

		$attached_documents = get_post_meta( $post->ID, '_attached_documents', true );

		if ( ! $attached_documents ) {
			return $content;
		}

		$output = '<ul class="technical-documents">';

		foreach ( $attached_documents as $post_ID ) {

			if ( ! $document_post = get_post( $post_ID ) ) {
				continue;
			}

			if ( ! $pdf_url = get_post_meta( $post_ID, '_document', true ) ) {
				continue;
			}

			$output .= '<li>';
			$output .= '<a href="' . $pdf_url . '">';
			$output .= '<img src="' . TECHNICALDOCS_URL . '/assets/images/Adobe_PDF_file_icon_32x32.png" />&nbsp;';
			$output .= $document_post->post_title;
			$output .= '</a>';
			$output .= '</li>';
		}

		$output .= '</ul>';

		return $output;
	}
}