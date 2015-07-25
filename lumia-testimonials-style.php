<?php header( "Content-type: text/css" ); ?>
<?php include( '../../../wp-load.php' );?>
<?php $options = get_option( 'lt_settings' ); ?>
<?php $background = isset( $options['background'] ) ? esc_attr( $options['background'] ) : ''; ?>
<?php $title_color = isset( $options['title_color'] ) ? esc_attr( $options['title_color'] ) : ''; ?>
<?php $font_family = isset( $options['font_family'] ) ? esc_attr( $options['font_family'] ) : ''; ?>
<?php $content_color = isset( $options['content_color'] ) ? esc_attr( $options['content_color'] ) : ''; ?>
<?php $location_color = isset( $options['location_color'] ) ? esc_attr( $options['location_color'] ) : ''; ?>
<?php $email_color = isset( $options['email_color'] ) ? esc_attr( $options['email_color'] ) : ''; ?>
<?php $company_color = isset( $options['company_color'] ) ? esc_attr( $options['company_color'] ) : ''; ?>
<?php $website_color = isset( $options['website_color'] ) ? esc_attr( $options['website_color'] ) : ''; ?>

/**************************************************/
/*                  G E N E R A L                 */
/**************************************************/
.clear{
	clear: both;
}
.test_box{
	border-bottom:1px solid #ccc;
    background: <?php echo $background;?>;
	margin-bottom:15px;
	padding:15px;
	font:14px <?php echo $font_family;?>;
    color:<?php echo $content_color;?>;
    border-radius: 5px;
    -o-border-radius: 5px;
    -ms-border-radius: 5px;
    -moz-border-radius: 5px;
    -webkit-border-radius: 5px;
}
.test_box img{
	float:left;
	margin-right:10px;
	border-radius: 3px 3px 3px 3px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
	width:115px;
}
.test_box span{
	font:italic 16px 'Lucida Console',status-bar,sans-serif;
	color:#666;
	text-align:right;
	display:block;
}
.test_box strong{
	color:<?php echo $company_color;?>;
}
.test_box small{
	color:<?php echo $location_color;?>;
}
.test_box span.testi_email a{
	text-decoration:none;
	font: 13px 'Lucida Console',status-bar,sans-serif;
	color:<?php echo $email_color;?>;
}
.test_box span.testi_email a:hover{
	color:rgb(23, 51, 101);
}
.test_box span.testi_web a{
	text-decoration:none;
	font: 13px 'Lucida Console',status-bar,sans-serif;
	color:<?php echo $website_color;?>;
}
.test_box span.testi_web a:hover{
	color:rgb(23, 51, 101);
}
.wl-testimonial{
}
.wl-testimonial li{
	list-style:none;
	margin:0;
	padding:0;
}
.wl-testimonial li img{
	width:70px;
	float:left;
	margin-right:10px;
}
.wl-testimonial li p{
	line-height:17px !important;
	margin-bottom:15px !important;
}
.wl-testimonial li span{
	font:italic 13px 'Lucida Console',status-bar,sans-serif;
	color:#666;
	text-align:right;
	display:block;
}
.wl-testimonial li strong{
	color:rgb(247, 115, 34);
}
.wl-testimonial li small{
	color:rgb(21, 163, 235);
}
.test_box.colum-4 {
	width:31%;
    float: left;
    margin-right: 3.5%;
    padding: 15px;
    background : <?php echo $background;?> url( images/quotes.png ) no-repeat 20px 20px;
}
.test_box.colum-4:nth-child( 3n ) {
	margin-right: 0;   
}
.test_box.colum-4:first-child {
	margin-right: 3.5%;
}
.test_box.colum-4 p {
	padding: 0;
    text-align: center;
}
.test_box.colum-4 h3 {
	background: #222;
    font-size: 18px;
    line-height:35px;
    text-align: center;
    display: block;
    color:<?php echo $company_color;?>;
}
.test_box.colum-4 small {
    font-size: 13px;
    font-style: italic;
    text-align: center;
    display: block;
    margin-bottom: 5px;
    color:<?php echo $location_color;?>;
}
.test_box.colum-4 ul{
	margin: 0;
    padding: 0 20px;
}
.test_box.colum-4 ul li{
	list-style: none;
    line-height: 36px;
    padding-left: 35px;
    margin-bottom: 5px;
    border-radius: 3px;
    -o-border-radius: 3px;
    -ms-border-radius: 3px;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
}
.test_box.colum-4 ul li span{
    text-align: left;
}
.test_box.colum-4 ul li.email {
	background : #fff url( images/email.png ) no-repeat 5px center;
    color:<?php echo $website_color;?>;
}
.test_box.colum-4 ul li.web {
	background : #fff url( images/web.png ) no-repeat 5px center;
    color:<?php echo $website_color;?>;
}
.test_box.colum-4 ul li.email a {
    color:<?php echo $website_color;?>;
}
.test_box.colum-4 ul li.web a {
    color:<?php echo $website_color;?>;
}
.test_box.colum-4 ul li.email a:hover,
.test_box.colum-4 ul li.web a:hover {
	color:<?php echo $location_color;?>;
    text-decoration: none;
}
@media (max-width: 767px) {
	.test_box.colum-4 {
    	width: 100%;
     }
}
@media (min-width: 768px) and (max-width: 991px) {
	.test_box.colum-4 {
    	width: 46%;
     }
     .test_box.colum-4:nth-child(2n+1) {
        margin-right: 3.5%;
    }
}