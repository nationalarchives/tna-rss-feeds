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

		// Shortcode atts.
		extract(shortcode_atts(array(
			'url' => 'http://blog.nationalarchives.gov.uk/feed/',
			'number' => 6
		), $atts));

		// Get feed source.
		$content = file_get_contents($url);
		$x = new SimpleXmlElement($content);

		$n = 0 ;
		$html .= '<div class="tna-rss"><ul>' ;

			// Loop through each feed item and display each item as a hyperlink
			foreach ( $x->channel->item as $item ) :
				if ( $n == $number ) {
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

function tna_rss( $rssUrl, $url, $rssTitle, $image, $id ) {
	// Do we have this information in our transients already?
	$transient = get_transient( 'tna_rss_blog_transient' . $id );
	// Yep!  Just return it and we're done.
	if( ! empty( $transient ) ) {
		echo $transient ;
		// Nope!  We gotta make a call.
	} else {
		// Get feed source.
		$content = file_get_contents( $rssUrl );
		if ( $content !== false ) {
			$x = new SimpleXmlElement( $content );
			$n = 0;
			// Loop through each feed item and display each item as a hyperlink
			foreach ( $x->channel->item as $item ) :
				if ( $n == 1 ) {
					break;
				}
				$enclosure  = $item->enclosure['url'];
				$namespaces = $item->getNameSpaces( true );
				$dc         = $item->children( $namespaces['dc'] );
				$pubDate    = $item->pubDate;
				$pubDate    = date( "D, d M Y", strtotime( $pubDate ) );
				if ( ! $image == 'no' ) {
					$html .= '<a href="' . $url . '" title="' . $rssTitle . '"">';
					$html .= '<div class="image-container" style="background-image: url(' . $enclosure . ')">';
					$html .= '<h2><span><span>' . $rssTitle . '</span></span></h2>';
					$html .= '</div>';
					$html .= '</a>';
				}
				$html .= '<div class="clr">';
				$html .= '<div class="tna-rss-entry"><a href="' . $item->link . '">';
				$html .= '<h3>' . $item->title . '</h3>';
				$html .= '</a>';
				$html .= '<div class="entry-meta">' . $dc->creator . '|' . $pubDate . '</div>';
				$html .= '<p>' . $item->description . '</p></div>';
				$html .= '</div>';
				$n ++;
			endforeach;
			set_transient( 'tna_rss_blog_transient' . $id, $html, HOUR_IN_SECONDS );
			echo $html;
		}
		else {
			echo '<a href="' . $url . '" title=' . $rssTitle . '>';
			echo '<div class="image-container" style="background-image: url(http://blog.nationalarchives.gov.uk/wp-content/themes/Redesign/images/blog-banner-bg.jpg)">';
			echo '<h2><span><span>' . $rssTitle . '</span></span></h2>';
			echo '</div></a>';
			echo '<div class="tna-rss-entry"><a href="' . $url . '">';
			echo '<h3>Read more at: ' . $rssTitle . '</h3>';
			echo '</a></div>';
		}
	}
}


?>
