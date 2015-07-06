<?php
/*
Plugin Name: Lumia Testimonials
Plugin URI: http://www.weblumia.com/lumia-testimonials
Description: Testimonials plugin allows you to display random or selected testimonials, or text with images.
Version: 1.8.3
Author: Jinesh.P.V
Author URI: http://www.weblumia.com/
*/
/**
	Copyright 2013 Jinesh.P.V (email: jinuvijay5@gmail.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */


class Lumia_Testimonials {
	
	/* constructor function for class*/
	public function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
		register_activation_hook( __FILE__, array( &$this, 'lumia_activation' ) );
		register_deactivation_hook( __FILE__, array( &$this, 'lumia_deactivation' ) );
		add_action( 'widgets_init', array( &$this, 'widgets_init' ) );
		add_action( 'wp_footer', array( &$this, 'lumia_scripts' ) );
		add_action( 'wp_footer', array( &$this, 'lumia_widget_scripts' ), 20 );
		add_shortcode( 'lumia_testimonial_list', array( &$this, 'lumia_testimonial_list' ) );
		add_shortcode( 'lumia_testimonial_widget', array( &$this, 'lumia_testimonial_widget' ) );
		register_uninstall_hook( __FILE__, array( 'lumia_testimonial', 'lumia_uninstall' ) );
	}
	
	/* init function for lumia testimonials*/
	public function init(){
		self::lumia_styles();
	}
	
	public function lumia_activation() {
		if ( ! current_user_can( 'activate_plugins' ) )
			return;

		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
		check_admin_referer( "activate-plugin_{$plugin}" );
		self::init();

		flush_rewrite_rules();
	}


	public function lumia_deactivation() {
		if ( ! current_user_can( 'activate_plugins' ) )
			return;

		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
		check_admin_referer( "deactivate-plugin_{$plugin}" );

		flush_rewrite_rules();
	}
	
	public function uninstall() {
		if ( ! current_user_can( 'activate_plugins' ) )
			return;

		if ( __FILE__ != WP_UNINSTALL_PLUGIN )
			return;

		check_admin_referer( 'bulk-plugins' );

		global $wpdb;

		self::lumia_delete_testimonials();

		$wpdb->query( "OPTIMIZE TABLE `" . $wpdb->options . "`" );
		$wpdb->query( "OPTIMIZE TABLE `" . $wpdb->postmeta . "`" );
		$wpdb->query( "OPTIMIZE TABLE `" . $wpdb->posts . "`" );
	}
	
	public static function lumia_delete_testimonials() {
		global $wpdb;

		$query					= "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'lumia-testimonials'";
		$posts					= $wpdb->get_results( $query );

		foreach( $posts as $post ) {
			$post_id			= $post->ID;
			self::lumia_delete_attachments( $post_id );

			wp_delete_post( $post_id, true );
		}
	}


	public static function lumia_delete_attachments( $post_id = false ) {
		global $wpdb;

		$post_id				= $post_id ? $post_id : 0;
		$query					= "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_parent = {$post_id}";
		$attachments			= $wpdb->get_results( $query );

		foreach( $attachments as $attachment ) {
			// true is force delete
			wp_delete_attachment( $attachment->ID, true );
		}
	}
	
	public static function lumia_styles() {
		if( !is_admin() ){
			wp_register_style( 'lumia-testimonial', plugins_url( 'lumia-testimonials-style.css', __FILE__ ) );
			wp_enqueue_style( 'lumia-testimonial' );
		}
	}
	
	public static function lumia_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'bx-slider', plugins_url( '/js/jquery.bxSlider.min.js', __FILE__ ), array( 'jquery' ), '2.0' );
	}
	
	public static function lumia_widget_scripts() {
		
		?>
		<script type="text/javascript">
		jQuery( '.wl-testimonial' ).bxSlider({
			auto: true,
			pause:5000,
			speed:500,
			mode:'fade',
			controls:false
		});
		</script>
		<?php
	}
	
	public function widgets_init() {
		require_once 'lumia-testimonials-widget-class.php';
		register_widget( 'Lumia_Testimonials_Widget' );
	}
	
	public function lumia_testimonial_list(){
		require_once 'lumia-testimonials-functions.php';
		$wlFunctions = new Lumia_Testimonial_Functions;
		$wlFunctions->lumia_testimonial_all();
	}
	
	public function lumia_testimonial_widget(){
		require_once 'lumia-testimonials-functions.php';
		$wlFunctions = new Lumia_Testimonial_Functions;
		$wlFunctions->lumia_testimonial_widget();
	}
}

add_action( 'init', 'init_post_type' );

function init_post_type() {
	$label						=	'Testimonials';
	$labels = array(
		'name' 					=>	_x( $label, 'post type general name' ),
		'singular_name' 		=>	_x( $label, 'post type singular name' ),
		'add_new'				=>	_x( 'Add New', 'lumia-testimonial' ),
		'add_new_item' 			=>	__( 'Add New Testimonial', 'lumia-testimonial' ),
		'edit_item' 			=>	__( 'Edit Testimonial', 'lumia-testimonial'),
		'new_item' 				=>	__( 'New Testimonial' , 'lumia-testimonial' ),
		'view_item' 			=>	__( 'View Testimonial', 'lumia-testimonial' ),
		'search_items'			=>	__( 'Search ' . $label ),
		'not_found'				=>	__( 'Nothing found' ),
		'not_found_in_trash'	=>	__( 'Nothing found in Trash' ),
		'parent_item_colon'		=>	''
	);

	register_post_type( 'lumia_testimonials', 
					   		array(
								'labels'				=>	$labels,
								'public'				=>	true,
								'publicly_queryable'	=>	true,
								'show_ui'				=>	true,
								'exclude_from_search'	=>	true,
								'query_var'				=>	true,
								'rewrite'				=>	true,
								'capability_type'		=>	'post',
								'has_archive'			=>	true,
								'hierarchical'			=>	false,
								'menu_position'			=>	65,
								'supports'				=>	array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
								'menu_icon'				=>	plugins_url( '/', __FILE__ ) . '/images/testimonial.png',
								'register_meta_box_cb'	=> 'lumia_testimonials_meta_boxes',
								)
						);
}

function lumia_testimonials_meta_boxes() {
	add_meta_box( 	
					'display_lumia_testimonial_meta_box',
					'Testimonial Information',
					'display_lumia_testimonial_meta_box',
					'lumia_testimonials',
					'normal', 
					'high'
				 );
}

function display_lumia_testimonial_meta_box() {
	$post_id					=	get_the_ID();
	$testimonial_data			=	get_post_meta( $post_id, '_testimonial', true );
	$location					=	( empty( $testimonial_data['location'] ) ) ? '' : $testimonial_data['location'];
	$email						=	( empty( $testimonial_data['email'] ) ) ? '' 	: $testimonial_data['email'];
	$company					=	( empty( $testimonial_data['company'] ) ) ? '' 	: $testimonial_data['company'];
	$website					=	( empty( $testimonial_data['website'] ) ) ? '' 	: $testimonial_data['website'];

	wp_nonce_field( 'lumia_testimonials', 'lumia_testimonials' );
	?>
    <style>
	.form-table td {
		padding: 10px;
	}
	.form-table td input {
		width: 100%
	}
	</style>
    <table class="form-table">
        <tr>
            <td>Location : </td>
            <td><input type="text" name="testimonial[location]" value="<?php echo $location; ?>" /></td>
        </tr>
        <tr>
            <td>Email Address: </td>
            <td><input type="text" name="testimonial[email]" value="<?php echo $email; ?>" /></td>
        </tr>
        <tr>
            <td>Company Name : </td>
            <td><input type="text" name="testimonial[company]" value="<?php echo $company; ?>" /></td>
        </tr>
        <tr>
            <td>Website URL : </td>
            <td><input type="text" name="testimonial[website]" value="<?php echo $website; ?>" /></td>
        </tr>
    </table>
	<?php
} 

add_action( 'save_post', 'lumia_testimonials_save_post' );

function lumia_testimonials_save_post( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

	if ( ! empty( $_POST['lumia_testimonials'] ) && ! wp_verify_nonce( $_POST['lumia_testimonials'], 'lumia_testimonials' ) )
		return;

	if ( ! empty( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) )
			return;
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return;
	}

	if ( ! empty( $_POST['testimonial'] ) ) {
		$testimonial_data['location']		=	( empty( $_POST['testimonial']['location'] ) ) ? '' : sanitize_text_field( $_POST['testimonial']['location'] );
		$testimonial_data['email']			=	( empty( $_POST['testimonial']['email'] ) ) ? '' 	: sanitize_text_field( $_POST['testimonial']['email'] );
		$testimonial_data['company']		=	( empty( $_POST['testimonial']['company'] ) ) ? '' 	: sanitize_text_field( $_POST['testimonial']['company'] );
		$testimonial_data['website']		=	( empty( $_POST['testimonial']['website'] ) ) ? '' 	: esc_url( $_POST['testimonial']['website'] );

		update_post_meta( $post_id, '_testimonial', $testimonial_data );
	} else {
		delete_post_meta( $post_id, '_testimonial' );
	}
}

add_filter( 'manage_edit-lumia_testimonials_columns', 'lumia_testimonials_edit_columns' );

function lumia_testimonials_edit_columns( $columns ) {
	$columns = array(
		'cb'						=>	'<input type="checkbox" />',
		'title'						=>	'Title',
		'testimonial-location'		=>	'Location',
		'testimonial-email'			=>	'Email Address',
		'testimonial-company'		=>	'Company',
		'testimonial-website'		=>	'Website',
		'date'						=>	'Date'
	);

	return $columns;
}

add_action( 'manage_posts_custom_column', 'lumia_testimonials_columns', 10, 2 );

function lumia_testimonials_columns( $column, $post_id ) {
	$testimonial_data		=	get_post_meta( $post_id, '_testimonial', true );
	switch ( $column ) {
		case 'testimonial-location':
			if ( ! empty( $testimonial_data['location'] ) )
				echo $testimonial_data['location'];
			break;
		case 'testimonial-email':
			if ( ! empty( $testimonial_data['email'] ) )
				echo $testimonial_data['email'];
			break;
		case 'testimonial-company':
			if ( ! empty( $testimonial_data['company'] ) )
				echo $testimonial_data['company'];
			break;
		case 'testimonial-website':
			if ( ! empty( $testimonial_data['website'] ) )
				echo $testimonial_data['website'];
			break;
	}
}

$wlTestimonials = new Lumia_Testimonials;
?>