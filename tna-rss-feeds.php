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

function get_external_rss_data() {

	// Get RSS Feed(s)
	include_once( ABSPATH . WPINC . '/feed.php' );

	// Get a SimplePie feed object from the specified feed source.
	$rss = fetch_feed( 'http://blog.nationalarchives.gov.uk/feed/' );

	$maxitems = 0;

	if ( ! is_wp_error( $rss ) ) : // Checks that the object is created correctly

		// Figure out how many total items there are, but limit it to 5.
		$maxitems = $rss->get_item_quantity( 5 );

		// Build an array of all the items, starting with element 0 (first element).
		$rss_items = $rss->get_items( 0, $maxitems );

	endif;
	?>

	<ul>
		<?php if ( $maxitems == 0 ) : ?>
			<li><?php _e( 'No items', 'my-text-domain' ); ?></li>
		<?php else : ?>
			<?php // Loop through each feed item and display each item as a hyperlink. ?>
			<?php foreach ( $rss_items as $item ) : ?>
				<li>
					<a href="<?php echo esc_url( $item->get_permalink() ); ?>"
					   title="<?php printf( __( 'Posted %s', 'my-text-domain' ), $item->get_date('j F Y | g:i a') ); ?>">
						<?php echo esc_html( $item->get_title() ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
	<?php
}

function rss_transient_func( $atts ){

	// Do we have this information in our transients already?
	$transient = get_transient( 'tna_rss_transient' );

	// Yep!  Just return it and we're done.
	if( ! empty( $transient ) ) {

		return $transient;

	// Nope!  We gotta make a call.
	} else {

		$rssdata = get_external_rss_data() ;

		set_transient( 'tna_rss_transient', $rssdata, MINUTE_IN_SECONDS );

		return $rssdata;

	}
}

add_shortcode( 'tna-rss', 'rss_transient_func' );

?>
