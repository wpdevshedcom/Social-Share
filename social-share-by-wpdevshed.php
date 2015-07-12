<?php
/*/
Plugin Name: Social Share by WP Dev Shed
Plugin URI: http://wordpress.org/plugins/social-share-by-wp-dev-shed/
Description: Adds Facebook and Twitter social share buttons to your blog posts.
Version: 1.5
Author: WP Dev Shed
Author URI: http://wpdevshed.com/
License: GNU General Public License v2.0
License URI: http://www.gnu.org/licenses/gpl-2.0.html
/*/

/**
 * Enqueue scripts and styles
 */
function rs_script_styles() {
	wp_enqueue_script( 'sswpds-script', plugin_dir_url(__FILE__) . 'js/sswpds-scripts.js', array('jquery'), '1.0.0' );
	wp_enqueue_style( 'sswpds-styles', plugin_dir_url(__FILE__) . 'sswpds-style.css' );
}
add_action('wp_enqueue_scripts', 'rs_script_styles');


/*
 * Load customize object
 */
function rs_social_share_plugin_customizer( $wp_customize ) {
	/* category link in homepage option */
	$wp_customize->add_section( 'social_share_display_section' , array(
		'title'       => __( 'Display Social Share', 'social_share' ),
		'priority'    => 34,
		'description' => __( 'NEW: You can now choose where you want to display social share button either post or page. <br/> Option to show/hide the social share before content, after content or display on both which is by default.', 'surfarama' ),
	) );

	$wp_customize->add_setting( 'social_share_display_in_posts', array (
		'default' 	=> 0,
		'sanitize_callback' => 'rs_social_share_sanitize_checkbox',
	) );
	$wp_customize->add_control('social_share_display_in_posts', array(
		'settings' 	=> 'social_share_display_in_posts',
		'label' 	=> __('Show social share only in Post?', 'social_share'),
		'section' 	=> 'social_share_display_section',
		'type' 		=> 'checkbox',
	));

	$wp_customize->add_setting( 'social_share_display_in_page', array (
		'default' 	=> 0,
		'sanitize_callback' => 'rs_social_share_sanitize_checkbox',
	) );
	$wp_customize->add_control('social_share_display_in_page', array(
		'settings' 	=> 'social_share_display_in_page',
		'label' 	=> __('Show social share only in Page?', 'social_share'),
		'section' 	=> 'social_share_display_section',
		'type' 		=> 'checkbox',
	));
	
	$wp_customize->add_setting( 'social_share_display_before_content', array (
		'default' 	=> 0,
		'sanitize_callback' => 'rs_social_share_sanitize_checkbox',
	) );
	$wp_customize->add_control('social_share_display_before_content', array(
		'settings' 	=> 'social_share_display_before_content',
		'label' 	=> __('Show social share before content?', 'social_share'),
		'section' 	=> 'social_share_display_section',
		'type' 		=> 'checkbox',
	));
	
	$wp_customize->add_setting( 'social_share_display_after_content', array (
		'default' 	=> 0,
		'sanitize_callback' => 'rs_social_share_sanitize_checkbox',
	) );
	$wp_customize->add_control('social_share_display_after_content', array(
		'settings' 	=> 'social_share_display_after_content',
		'label' 	=> __('Show social share after content?', 'social_share'),
		'section' 	=> 'social_share_display_section',
		'type' 		=> 'checkbox',
	));
}
add_action( 'customize_register', 'rs_social_share_plugin_customizer' );

/**
 * Sanitize checkbox
 */
if ( ! function_exists( 'rs_social_share_sanitize_checkbox' ) ) :
	function rs_social_share_sanitize_checkbox( $input ) {
		if ( $input == 1 ) {
			return 1;
		} else {
			return '';
		}
	}
endif;

/**
 * Add the social share buttons to the_content()
 */
function sswpds_filter_the_content( $content ) {
	$the_content_html = '';
	
	// assign social buttons
	$new_content = social_share_buttons_html();
    
	// display social share only in single page
	if ( is_single() || is_page() ) {
		
		// display social share both before and after content
		if( (get_theme_mod( 'social_share_display_before_content' )) && (get_theme_mod( 'social_share_display_after_content' )) ) {
			$the_content_html = $new_content . $content . $new_content;
		
		// display social share before content
		} else if( get_theme_mod( 'social_share_display_before_content' ) ) {
			$the_content_html = $new_content . $content;
		
		// display social share after content
		} else if( get_theme_mod( 'social_share_display_after_content' ) ) {
			$the_content_html = $content . $new_content;
		// display on both
		} else {
			$the_content_html = $content;
		}
		
	} else {
		$the_content_html = $content;
	}
	
	return $the_content_html;
}
add_filter( 'the_content', 'sswpds_filter_the_content' );


add_shortcode( 'wpdev_social_share', 'wpdev_social_share_func' );
function wpdev_social_share_func( $atts ) {
	ob_start();

	$social_share = '';
	
	// display social buttons
	social_share_buttons_html( true );
	
	return ob_get_clean();
}


function social_share_buttons_html( $echo = false ) {
	ob_start();
	
	$social_share_html = '';
	$social_share_html_filter = '';
	

	$current_post_type = get_post_type();
	?>
	<div class="sswpds-social-wrap">
		<a href="<?php echo esc_url('http://www.facebook.com/share.php?u=') . get_permalink(); ?>" target="_blank">
			<img src="<?php echo plugin_dir_url(__FILE__) . 'images/icon-fb.png'; ?>" alt="Share on Facebook" />
		</a>
		<a href="<?php echo esc_url('http://twitter.com/home?status=') . esc_attr( get_the_title() ) . ' ' . get_permalink(); ?>" target="_blank">
			<img src="<?php echo plugin_dir_url(__FILE__) . 'images/icon-tw.png'; ?>" alt="Share on Twitter" />
		</a>
	</div>
	
	<?php
	$social_share_html = ob_get_clean();
	
	// check if post is enable
	if( ( 'post' == $current_post_type && get_theme_mod( 'social_share_display_in_posts' ) )
		|| ( 'page' == $current_post_type && get_theme_mod( 'social_share_display_in_page' ) )
		) {
		$social_share_html_filter = $social_share_html;
	}
	
	if( $echo )
		echo $social_share_html_filter;
	else
		return $social_share_html_filter;
}
