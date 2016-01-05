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

function rss_transient_func( $atts ){

	// Do we have this information in our transients already?
	$transient = get_transient( 'tna_rss_transient' );

	// Yep!  Just return it and we're done.
	if( ! empty( $transient ) ) {

		return $transient . '<p>This is the transient stored data</p>';

	// Nope!  We gotta make a call.
	} else {

		// Get RSS Feed(s)
		include_once( ABSPATH . WPINC . '/feed.php' );

		// Get a SimplePie feed object from the specified feed source.
		$url = 'http://blog.nationalarchives.gov.uk/feed/';
		$rss = fetch_feed( $url );

		$maxitems = 0;

		if ( ! is_wp_error( $rss ) ) : // Checks that the object is created correctly

			// Figure out how many total items there are and then limit it.
			$maxitems = $rss->get_item_quantity( 3 );

			// Build an array of all the items, starting with element 0 (first element).
			$rss_items = $rss->get_items( 0, $maxitems );

		endif;

		$html .= '<ul>' ;
		if ( $maxitems == 0 ) :
			$html .= '<li>' . _e( 'No items', 'my-text-domain' ) . '</li>';
		else :
			// Loop through each feed item and display each item as a hyperlink
			foreach ( $rss_items as $item ) :
				$html .= '<li>';
				if ($enclosure = $item->get_enclosure()) {
					$html .= '<img src="' .  $enclosure->get_link() . '" width="300">';
				}
				$html .= '<a href="' . esc_url( $item->get_permalink() ) . '">';
				$html .= '<h3>' . esc_html( $item->get_title() ) . '</h3>';
				$html .= '</a>';
				$html .= '<p>' . esc_html( $item->get_description() ) . '</p>';
				$html .= '</li>';
			endforeach;
		endif;
		$html .= '</ul>';

		set_transient( 'tna_rss_transient', $html, MINUTE_IN_SECONDS );

		$html .= '<p>This is not the stored data</p>';

		return $html;

	}
}

add_shortcode( 'tna-rss', 'rss_transient_func' );

function tna_rss_js() {
	wp_register_script('rss-script', plugin_dir_url(__FILE__) . 'tna-rss-feeds.js');
	global $post;
	if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'tna-rss-js')) {
		wp_enqueue_script('rss-script', '', '', '', true);
	}
}
add_action('wp_enqueue_scripts', 'tna_rss_js');

function rss_js_func( $atts ){
	echo 'test';
}

add_shortcode( 'tna-rss-js', 'rss_js_func' );

function rss_alt_func( $atts ){
	function getFeed($feed_url) {

		$content = file_get_contents($feed_url);
		$x = new SimpleXmlElement($content);

		echo "<ul>";

		foreach($x->channel->item as $item) {
			echo "<li>";
			echo "<h3><a href='$item->link' title='$item->title'>" . $item->title . "</a></h3>";
			echo "<p>" . $item->description . "</p>";
			if ($enclosure = $item->enclosure['url']) {
				echo "<img src='$enclosure' title='$item->title'>";
			}
			echo "</li>";
		}
		echo "</ul>";
	}
	getFeed('http://blog.nationalarchives.gov.uk/feed/');
}

add_shortcode( 'tna-alt-php', 'rss_alt_func' );

?>
