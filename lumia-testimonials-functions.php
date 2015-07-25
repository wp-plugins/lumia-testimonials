<?php
class Lumia_Testimonial_Functions{
	
	public function __construct() {
	}
	
	public function lumia_testimonial_all() {
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
        $return = ob_get_clean();
		ob_end_clean();
        
		return $return;
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