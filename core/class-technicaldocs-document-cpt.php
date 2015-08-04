<?php
/**
 * Creates and manages the document CPT.
 *
 * @since      0.1.0
 *
 * @package    TechnicalDocs
 * @subpackage TechnicalDocs/core
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class TechnicalDocs_Document_CPT {

	private $post_type = 'document';
	private $label_singular = 'Document';
	private $label_plural = 'Documents';

	private $meta_fields = array(
		'_document',
	);

	function __construct() {

		$this->add_actions();
	}

	private function add_actions() {

		add_action( 'init', array( $this, '_create_cpt' ) );
		add_filter( 'post_updated_messages', array( $this, '_post_messages' ) );
		add_action( 'add_meta_boxes', array( $this, '_add_meta_boxes' ), 100 );
		add_action( 'save_post', array( $this, '_save_meta' ) );
		add_action( 'current_screen', array( $this, '_current_screen' ) );

		add_action( 'add_meta_boxes', array( $this, '_add_page_meta_box' ) );
	}

	function _create_cpt() {

		$labels = array(
			'name'               => $this->label_plural,
			'singular_name'      => $this->label_singular,
			'menu_name'          => $this->label_plural,
			'name_admin_bar'     => $this->label_singular,
			'add_new'            => "Add New",
			'add_new_item'       => "Add New $this->label_singular",
			'new_item'           => "New $this->label_singular",
			'edit_item'          => "Edit $this->label_singular",
			'view_item'          => "View $this->label_singular",
			'all_items'          => "All $this->label_plural",
			'search_items'       => "Search $this->label_plural",
			'parent_item_colon'  => "Parent $this->label_plural:",
			'not_found'          => "No $this->label_plural found.",
			'not_found_in_trash' => "No $this->label_plural found in Trash.",
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'menu_icon'          => 'dashicons-media-document',
			'capability_type'    => 'post',
			'has_archive'        => true,
			'rewrite' => array(
				'slug' => 'documents',
			),
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title' )
		);

		register_post_type( $this->post_type, $args );

		$label_single = 'Category';
		$label_plural = 'Categories';

		$labels = array(
			'name'               => $label_plural,
			'singular_name'      => $label_single,
			'menu_name'          => $label_plural,
			'name_admin_bar'     => $label_single,
			'add_new'            => "Add New",
			'add_new_item'       => "Add New $label_single",
			'new_item'           => "New $label_single",
			'edit_item'          => "Edit $label_single",
			'view_item'          => "View $label_single",
			'all_items'          => "All $label_plural",
			'search_items'       => "Search $label_plural",
			'parent_item_colon'  => "Parent $label_plural:",
			'not_found'          => "No $label_plural found.",
			'not_found_in_trash' => "No $label_plural found in Trash.",
		);

		register_taxonomy( 'document-category', 'document', array(
			'labels'            => $labels,
			'show_admin_column' => true,
			'hierarchical' => true,
		) );
	}

	function _post_messages( $messages ) {

		$post             = get_post();
		$post_type_object = get_post_type_object( $this->post_type );

		$messages[ $this->post_type ] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => "$this->label_singular updated.",
			2  => 'Custom field updated.',
			3  => 'Custom field deleted.',
			4  => "$this->label_singular updated.",
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? "$this->label_singular restored to revision from " . wp_post_revision_title( (int) $_GET['revision'], false ) : false,
			6  => "$this->label_singular published.",
			7  => "$this->label_singular saved.",
			8  => "$this->label_singular submitted.",
			9  => "$this->label_singular scheduled for: <strong>" . date( 'M j, Y @ G:i', strtotime( $post->post_date ) ) . '</strong>.',
			10 => "$this->label_singular draft updated.",
		);

		if ( $post_type_object->publicly_queryable ) {
			$permalink = get_permalink( $post->ID );

			$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), "View $this->label_singular" );
			$messages[ $this->post_type ][1] .= $view_link;
			$messages[ $this->post_type ][6] .= $view_link;
			$messages[ $this->post_type ][9] .= $view_link;

			$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
			$preview_link      = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), "Preview $this->label_singular" );
			$messages[ $this->post_type ][8] .= $preview_link;
			$messages[ $this->post_type ][10] .= $preview_link;
		}

		return $messages;
	}

	function _add_meta_boxes() {

		add_meta_box(
			'documentuploaddiv',
			'Upload Document',
			array( $this, '_meta_box_document_upload' ),
			'document',
			'side'
		);
	}

	function _meta_box_document_upload( $post ) {

		$option = get_post_meta( $post->ID, '_document', true );

		$preview = $option ? wp_get_attachment_url( $option ) : '';

		wp_nonce_field( 'document_upload_nonce', 'document_upload_nonce_save' );
		?>
		<p class="technicaldocs-media-uploader" data-button-text="Use Document" data-title-text="Choose a Document">
			<code class="url-preview" style="max-width: 100%; word-wrap: break-word;"><?php echo $preview; ?></code>
			<input type="hidden" name="_document" class="image-id" value="<?php echo $option; ?>"/>
			<br/>
			<input type="button" class="upload button" value="Choose or Upload a Document"/>
		</p>
		<?php
	}

	function _save_meta( $post_ID ) {

		if ( defined( 'DOING_AUTOSAVE' ) || ! current_user_can( 'edit_page', $post_ID ) ) {
			return;
		}

		if ( isset( $_POST['document_upload_nonce_save'] ) &&
		     wp_verify_nonce( $_POST['document_upload_nonce_save'], 'document_upload_nonce' )
		) {
			$this->save_document_meta( $post_ID );
		}

		if ( isset( $_POST['attach_documents_nonce_save'] ) &&
		     wp_verify_nonce( $_POST['attach_documents_nonce_save'], 'attach_documents_nonce' )
		) {
			$this->save_page_meta( $post_ID );
		}
	}

	private function save_document_meta( $post_ID ) {

		foreach ( $this->meta_fields as $field ) {

			if ( ! isset( $_POST[ $field ] ) || empty( $_POST[ $field ] ) ) {
				delete_post_meta( $post_ID, $field );
			}

			update_post_meta( $post_ID, $field, $_POST[ $field ] );
		}
	}

	private function save_page_meta( $post_ID ) {

		if ( isset( $_POST['_attached_documents'] ) ) {
			update_post_meta( $post_ID, '_attached_documents', $_POST['_attached_documents'] );
		} else {
			delete_post_meta( $post_ID, '_attached_documents' );
		}
	}

	function _current_screen( $screen ) {

		add_action( 'admin_enqueue_scripts', array( $this, '_enqueue_media' ) );
	}

	function _enqueue_media() {

		global $post;

		if ( is_admin() && $post->post_type == $this->post_type ) {
			wp_enqueue_media();
		}
	}

	function _add_page_meta_box() {

		add_meta_box(
			'attached_documents',
			'Attached Documents',
			array( $this, '_attach_documents' ),
			'page',
			'side'
		);
	}

	function _attach_documents( $post ) {

		$attached_documents = get_post_meta( $post->ID, '_attached_documents', true );

		$documents = get_posts( array(
			'post_type'   => 'document',
			'numberposts' => - 1,
		) );

		wp_nonce_field( 'attach_documents_nonce', 'attach_documents_nonce_save' );

		if ( $documents ) : ?>
			<select name="_attached_documents[]" class="technicaldocs-chosen" style="width: 100%;" multiple
			        data-placeholder="Select documents">
				<?php foreach ( $documents as $document ) : ?>

					<option value="<?php echo $document->ID; ?>"
						<?php echo in_array( $document->ID, $attached_documents ) ? 'selected' : ''; ?>>
						<?php echo $document->post_title; ?>
					</option>

				<?php endforeach; ?>
			</select>
		<?php endif;
	}
}