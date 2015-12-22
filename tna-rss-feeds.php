<?php
/**
 * Plugin Name: TNA RSS Feeds
 * Plugin URI:
 * Description: TBA
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

		return $transient;

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
			$maxitems = $rss->get_item_quantity( 6 );

			// Build an array of all the items, starting with element 0 (first element).
			$rss_items = $rss->get_items( 0, $maxitems );

		endif;

		$html .= '<ul>';
		if ( $maxitems == 0 ) :
			$html .= '<li>' . _e( 'No items', 'my-text-domain' ) . '</li>';
		else :
			// Loop through each feed item and display each item as a hyperlink
			foreach ( $rss_items as $item ) :
				$html .= '<li>';
				$html .= '<a href="' . esc_url( $item->get_permalink() ) . '">';
				$html .= '<h3>' . esc_html( $item->get_title() ) . '</h3>';
				$html .= '</a>';
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

?>
