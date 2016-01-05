<?php
/**
 * Plugin Name: TNA RSS Feeds
 * Plugin URI: https://github.com/nationalarchives/tna-rss-feeds
 * Description: Test - Displays RSS feeds via a shortcode
 * Version: 1.0.0
 * Author: Chris Bishop
 * Author URI: https://github.com/nationalarchives
 * License: GPL2
 */

function tna_rss_css()
{
	wp_register_style('tna-rss-styles', plugin_dir_url(__FILE__) . 'tna-rss-feeds.css', '', '1.0', 'all');
	global $post;
	if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'tna-rss')) {
		wp_enqueue_style('tna-rss-styles');
	}
}
add_action('wp_enqueue_scripts', 'tna_rss_css');

function rss_transient_func( $atts ){

	// Do we have this information in our transients already?
	$transient = get_transient( 'tna_rss_transient' );

	// Yep!  Just return it and we're done.
	if( ! empty( $transient ) ) {

		return $transient . '<p>This is the transient stored data</p>';

	// Nope!  We gotta make a call.
	} else {

		// Get feed source.
		$content = file_get_contents('http://blog.nationalarchives.gov.uk/feed/');
		$x = new SimpleXmlElement($content);

		$html .= '<div class="tna-rss"><ul>' ;
		$n = 0 ;
			// Loop through each feed item and display each item as a hyperlink
			foreach ( $x->channel->item as $item ) :
				if ( $n == 6 ) {
					break;
				}
				$html .= '<li class="clr">';
				if ($enclosure = $item->enclosure['url']) {
					$html .= '<div class="tna-rss-thumb"><a href="' . $item->link . '"><img src="' .  $enclosure . '"></a></div>';
				}
				$html .= '<div class="tna-rss-entry"><a href="' . $item->link . '">';
				$html .= '<h3>' . $item->title . '</h3>';
				$html .= '</a>';
				$html .= '<p>' . $item->description . '</p></div>';
				$html .= '</li>';
				$n++ ;
			endforeach;

		$html .= '</ul></div>';

		set_transient( 'tna_rss_transient', $html, MINUTE_IN_SECONDS );

		$html .= '<p>This is not the stored data</p>';

		return $html;

	}
}
add_shortcode( 'tna-rss', 'rss_transient_func' );

?>
