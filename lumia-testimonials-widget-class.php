<?php
/********************************************************/
/*                   Widget settings                    */
/********************************************************/

class Lumia_Testimonials_Widget extends WP_Widget {

	public function __construct() {

		$widget_ops			=	array( 'classname' => 'testimonial_widget', 'description' => __('Display testimonial using lumia testimonial WP Widget', 'lumia-testimonial') );
		$control_ops		=	array( 'id_base' => 'testimonial_widget' );
		$this->WP_Widget( 'testimonial_widget', __('Lumia Testimonial', 'lumia-testimonial'), $widget_ops, $control_ops );
	}

	public function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', $instance['title'] );


		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

		// Call layerslider_init to show the slider
		echo do_shortcode('[lumia_testimonial_widget]');

		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['id'] = strip_tags( $new_instance['id'] );
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

	public function form( $instance ) {

		// Defaults
		$defaults = array( 'title' => __('Testimonial', 'lumia-testimonial'));
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'lumia-testimonial'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
		</p>
	<?php
	}
}
?>