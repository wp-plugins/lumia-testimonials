<?php
class Lumia_Testimonial_Functions{
	
	public function __construct() {
	}
	
	public function lumia_testimonial_all() {
		global $post;
		query_posts( 'post_type=lumia_testimonials&posts_per_page=-1&orderby=menu_order&order=ASC' );
		
		while ( have_posts() ) : the_post();
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
			
			$html		.=	'<div class="test_box">
								' . get_the_post_thumbnail( $post->ID, 'full' ) . '
								<p>' . $post->post_content . '</p>
								<span>-' . $testimonial_meta . '</span>' . $testimonial_email . $testimonial_web . '
							</div>';
		
		endwhile;
		wp_reset_query();
		
		echo $html;
	}
	
	public function lumia_testimonial_widget(){
		
		global $post;
		query_posts( 'post_type=lumia_testimonials&posts_per_page=-1&orderby=rand&order=ASC' );
		
		$html					=	'<ul class="wl-testimonial">';
		
		while ( have_posts() ) : the_post();
		
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
			
			$html			.=	'<li>
									' . get_the_post_thumbnail( $post->ID, 'full' ) . '
									<p>' . self::content_limiter( $post->post_content, 100, '...' ) . '</p>
									<span>-' . $testimonial_meta . '</span>' . $testimonial_email . $testimonial_web . '
								</li>';
		endwhile;
		
		$html					.=	'</ul>';
		
		wp_reset_query();
		
		echo $html;
	}
	
	public function content_limiter( $text, $length=64, $tail="" ) {
		
		$text = trim( $text );
		$txtl = strlen( $text );
		if( $txtl > $length ) {
			for( $i=1; $text[$length-$i]!=" "; $i++ ) {
				if( $i == $length) {
					return substr( $text, 0, $length ) . $tail;
				}
			}
			$text = substr( $text, 0, $length-$i+1 ) . $tail;
		}
		return $text;
	}
}
?>