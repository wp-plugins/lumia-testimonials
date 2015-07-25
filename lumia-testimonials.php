<?php
/*
Plugin Name: Lumia Testimonials
Plugin URI: http://www.weblumia.com/lumia-testimonials
Description: Responsive testimonials plugin allows you to display random or selected testimonials, or text with images.
Version: 1.8.6
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
	
	private $options;
	
	/* constructor function for class*/
	public function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'admin_init', array( &$this, 'page_init' ) );
		add_action( 'admin_menu', array( &$this, 'lumia_testimonials_settings' ) );		
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
		wp_register_style('lumia-googleFonts', 'http://fonts.googleapis.com/css?family=Oswald|Open+Sans|Fontdiner+Swanky|Crafty+Girls|Pacifico|Satisfy|Gloria+Hallelujah|Bangers|Audiowide|Sacramento');
        wp_enqueue_style( 'lumia-googleFonts');
		
		if( !is_admin() ){
			wp_register_style( 'testimonials-style', plugins_url( '/lumia-testimonials-style.php', __FILE__ ) );
			wp_enqueue_style( 'testimonials-style' );
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
		
		global $post;
		$options = get_option( 'lt_settings' );
		$display_mode = isset( $options['display_mode'] ) ? esc_attr( $options['display_mode'] ) : '';
		ob_start();
		
		query_posts( 'post_type=lumia_testimonials&posts_per_page=-1&orderby=menu_order&order=ASC' );
		
		while ( have_posts() ) : the_post();		
			
			if( $display_mode === 'normal' ) {	
				$testimonial_data			=	get_post_meta( $post->ID, '_testimonial', true );
				if( $testimonial_data['company'] != '' ){
					$testimonial_meta		=	'<strong>' . $testimonial_data['company'] . '</strong>,';
				}
				
				$testimonial_meta			.=		'<small> ' .$post->post_title;
				if( $testimonial_data['location'] != '' ){
					$testimonial_meta		.=		' ( ' . $testimonial_data['location'] . ' )</small>';
				}else{
					$testimonial_meta		.=		'</small>';
				}
				
				if( $testimonial_data['email'] != '' ){
					$testimonial_email		=	'<span class="testi_email"><a href="mailto:' . $testimonial_data['email'] . '">' . $testimonial_data['email'] . '</a></span>';
				}else{
					$testimonial_email		=	'';
				}
				
				if( $testimonial_data['website'] != '' ){
					
					if( preg_match( "#https?://#", $testimonial_data['website'] ) === 0 ){
						$link				=	str_replace( array( 'http://', 'https://' ), array( 'http://', 'http://' ), $testimonial_data['website'] );
					}else{
						$link				=	'http://' . $testimonial_data['website'];
					}
					$testimonial_web		=	'<span class="testi_web"><a href="' . $link . '" target="_blank">' . str_replace( array( 'http://', 'https://' ), array( '', '' ), $testimonial_data['website'] ) . '</a></span>';
				}else{
					$testimonial_web		=	'';
				}
			} else {
				$testimonial_data			=	get_post_meta( $post->ID, '_testimonial', true );
				if( $testimonial_data['company'] != '' ){
					$company		=	$testimonial_data['company'];
				}
				
				$location			=		'<small> ' .$post->post_title;
				if( $testimonial_data['location'] != '' ){
					$location		.=		' ( ' . $testimonial_data['location'] . ' )</small>';
				}else{
					$location		.=		'</small>';
				}
				
				
				if( $testimonial_data['email'] != '' ){
					$testimonial_email		=	'<a href="mailto:' . $testimonial_data['email'] . '">' . $testimonial_data['email'] . '</a>';
				}else{
					$testimonial_email		=	'';
				}
				
				if( $testimonial_data['website'] != '' ){
					
					if( preg_match( "#https?://#", $testimonial_data['website'] ) === 0 ){
						$link				=	str_replace( array( 'http://', 'https://' ), array( 'http://', 'http://' ), $testimonial_data['website'] );
					}else{
						$link				=	'http://' . $testimonial_data['website'];
					}
					$testimonial_web		=	'<a href="' . $link . '" target="_blank">' . str_replace( array( 'http://', 'https://' ), array( '', '' ), $testimonial_data['website'] ) . '</a>';
				}else{
					$testimonial_web		=	'';
				}				
			}
				 
			if( $display_mode === 'normal' ) {
				?>	
				<div class="test_box">
                    <?php echo get_the_post_thumbnail( $post->ID, 'full' );?>
                    <?php echo apply_filters( 'the_content', $post->post_content );?>
                    <span>-<?php echo $testimonial_meta;?></span><?php echo $testimonial_email . $testimonial_web; ?>
                    <div class="clear"></div>
                </div>
                                
                <?php                
			} else {
				?>
				<div class="test_box colum-4">
                    <?php echo get_the_post_thumbnail( $post->ID, 'full' );?>
                    <?php echo apply_filters( 'the_content', $post->post_content );?>
                    <h3><?php echo $company;?></h3>
                    <?php echo $location;?>
					<ul>
						<li class="email"><?php echo $testimonial_email;?>
                        <li class="web"><?php echo $testimonial_web; ?>
                    </ul>
                    <div class="clear"></div>
                </div>
                <?php
			}
		
		endwhile;
		wp_reset_query();
		
        return ob_get_clean();
	}
	
	public function lumia_testimonial_widget(){
		require_once 'lumia-testimonials-functions.php';
		$wlFunctions = new Lumia_Testimonial_Functions;
		$wlFunctions->lumia_testimonial_widget();
	}
		 
	public function lumia_testimonials_settings() {
		
		add_submenu_page(
            'edit.php?post_type=lumia_testimonials', 
            'Settings', 
			'Settings',
            'manage_options', 
            'lumia-testimonials-setting', 
            array( $this, 'create_lumia_testimonials_admin_page' )
        );
	} 

    /**
     * Options page callback
     */
	 
    public function create_lumia_testimonials_admin_page() {
		
        // Set class property
        $this->options = get_option( 'lt_settings' );
		$display_mode = isset( $this->options['display_mode'] ) ? esc_attr( $this->options['display_mode'] ) : '';
		$background = isset( $this->options['background'] ) ? esc_attr( $this->options['background'] ) : '';
		$title_color = isset( $this->options['title_color'] ) ? esc_attr( $this->options['title_color'] ) : '';
		$font_family = isset( $this->options['font_family'] ) ? esc_attr( $this->options['font_family'] ) : '';
		$content_color = isset( $this->options['content_color'] ) ? esc_attr( $this->options['content_color'] ) : '';
		$location_color = isset( $this->options['location_color'] ) ? esc_attr( $this->options['location_color'] ) : '';
		$email_color = isset( $this->options['email_color'] ) ? esc_attr( $this->options['email_color'] ) : '';
		$company_color = isset( $this->options['company_color'] ) ? esc_attr( $this->options['company_color'] ) : '';
		$website_color = isset( $this->options['website_color'] ) ? esc_attr( $this->options['website_color'] ) : '';
        ?>
        <style>
		.form-table {
			width:70%;
		}
		.form-table td {
			padding: 10px;
		}
		.form-table td input {
			width: 100%
		}
		.form-table td input.small {
			width: 20%
		}
		</style>
        <div class="wrap">
            <h2>My Settings</h2>           
            <form method="post" action="options.php">
            <?php settings_fields( 'lumia_testimonials' ); ?>
            <table class="form-table">
                    <tr>
                        <td>Display Mode: </td>
                        <td>
                            <select name="lt_settings[display_mode]">
                                <option>Select Display Mode</option>
                                <option value="normal" <?php selected( $display_mode, 'normal' ); ?>>Normal</option>
                                <option value="modern" <?php selected( $display_mode, 'modern' ); ?>>Modern</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Background: </td>
                        <td><input type="color" name="lt_settings[background]" value="<?php echo $background; ?>" class="small" /></td>
                    </tr>
                    <tr>
                        <td>Font Family: </td>
                        <td>
                            <select id="font_family" name="lt_settings[font_family]">
                                <option value="Arial" <?php selected( $font_family, 'Arial' ); ?>>Arial</option>
                                <option value="Verdana" <?php selected( $font_family, 'Verdana' ); ?>>Verdana</option>
                                <option value="Helvetica" <?php selected( $font_family, 'Helvetica' ); ?>>Helvetica</option>
                                <option value="Comic Sans MS" <?php selected( $font_family, 'Comic Sans MS' ); ?>>Comic Sans MS</option>
                                <option value="Georgia" <?php selected( $font_family, 'Georgia' ); ?>>Georgia</option>
                                <option value="Trebuchet MS" <?php selected( $font_family, 'Trebuchet MS' ); ?>>Trebuchet MS</option>
                                <option value="Times New Roman" <?php selected( $font_family, 'Times New Roman' ); ?>>Times New Roman</option>
                                <option value="Tahoma" <?php selected( $font_family, 'Tahoma' ); ?>>Tahoma</option>
                                <option value="Oswald" <?php selected( $font_family, 'Oswald' ); ?>>Oswald</option>
                                <option value="Open Sans" <?php selected( $font_family, 'Open Sans' ); ?>>Open Sans</option>
                                <option value="Fontdiner Swanky" <?php selected( $font_family, 'Fontdiner Swanky' ); ?>>Fontdiner Swanky</option>
                                <option value="Crafty Girls" <?php selected( $font_family, 'Crafty Girls' ); ?>>Crafty Girls</option>
                                <option value="Pacifico" <?php selected( $font_family, 'Pacifico' ); ?>>Pacifico</option>
                                <option value="Satisfy" <?php selected( $font_family, 'Satisfy' ); ?>>Satisfy</option>
                                <option value="Gloria Hallelujah" <?php selected( $font_family, 'TGloria Hallelujah' ); ?>>TGloria Hallelujah</option>
                                <option value="Bangers" <?php selected( $font_family, 'Bangers' ); ?>>Bangers</option>
                                <option value="Audiowide" <?php selected( $font_family, 'Audiowide' ); ?>>Audiowide</option>
                                <option value="Sacramento" <?php selected( $font_family, 'Sacramento' ); ?>>Sacramento</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Company Name Font Color: </td>
                        <td><input type="color" name="lt_settings[company_color]" value="<?php echo $company_color; ?>" class="small" /></td>
                    </tr>
                    <tr>
                        <td>Content Font Color: </td>
                        <td><input type="color" name="lt_settings[content_color]" value="<?php echo $content_color; ?>" class="small" /></td>
                    </tr>
                    <tr>
                        <td>Location Font Color: </td>
                        <td><input type="color" name="lt_settings[location_color]" value="<?php echo $location_color; ?>" class="small" /></td>
                    </tr>
                    <tr>
                        <td>Email Font Color: </td>
                        <td><input type="color" name="lt_settings[email_color]" value="<?php echo $email_color; ?>" class="small" /></td>
                    </tr>
                    <tr>
                        <td>Website URL Font Color: </td>
                        <td><input type="color" name="lt_settings[website_color]" value="<?php echo $website_color; ?>" class="small" /></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
	 
    public function page_init() {        
        register_setting(
            'lumia_testimonials', // Option group
            'lt_settings', // Option name
            array( &$this, 'sanitize' ) // Sanitize
        );
     
    }
	
	/**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
	 
	public function sanitize( $input ) {
		 
        $new_input = array();
        if( isset( $input['display_mode'] ) )
            $new_input['display_mode'] = sanitize_text_field( isset( $input['display_mode'] ) ? $input['display_mode'] : 'normal' );

        if( isset( $input['background'] ) )
            $new_input['background'] = sanitize_text_field( $input['background'] );
			
        if( isset( $input['font_family'] ) )
            $new_input['font_family'] = sanitize_text_field( $input['font_family'] );
			
        if( isset( $input['content_color'] ) )
            $new_input['content_color'] = sanitize_text_field( $input['content_color'] );
			
        if( isset( $input['location_color'] ) )
            $new_input['location_color'] = sanitize_text_field( $input['location_color'] );
			
        if( isset( $input['email_color'] ) )
            $new_input['email_color'] = sanitize_text_field( $input['email_color'] );
			
        if( isset( $input['company_color'] ) )
            $new_input['company_color'] = sanitize_text_field( $input['company_color'] );
			
        if( isset( $input['website_color'] ) )
            $new_input['website_color'] = sanitize_text_field( $input['website_color'] );

        return $new_input;
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