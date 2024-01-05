<?php
/**
 * Plugin Name: Shortcode Extractor
 * Description: Extracts shortcodes from given page URLs.
 * Version: 1.0
 * Author: Fahad Murtaza
 */

/**
 * Adds a menu page for the plugin.
 * This function is hooked into the 'admin_menu' action.
 *
 * @return void
 */
function my_custom_menu_page() {
	add_menu_page(
		'Extract Shortcodes',      // Page title
		'Shortcode Extractor',     // Menu title
		'manage_options',          // Capability
		'shortcode-extractor',     // Menu slug
		'shortcode_extractor_page' // Callback function
	);
}

add_action( 'admin_menu', 'my_custom_menu_page' );

/**
 * Callback function for the menu page.
 *
 * @return void
 */
function shortcode_extractor_page() {
	?>
    <div class="wrap">
        <h1>Shortcode Extractor</h1>
        <form method="post">
            <textarea name="page_urls" rows="10" cols="50" placeholder="Enter URLs, one per line"></textarea>
            <input type="submit" value="Extract Shortcodes" class="button button-primary">
        </form>
		<?php
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' && ! empty( $_POST['page_urls'] ) ) {
			// Call the function to process URLs
			process_page_urls( $_POST['page_urls'] );
		}
		?>
    </div>
	<?php
}

/**
 * Processes given page URLs.
 *
 * @param string $page_urls Page URLs to process.
 *
 * @return void
 */

function process_page_urls( $page_urls ) {
	echo "<style>
        ul:not(:first-of-type) {
            padding-left: 20px;
            list-style: none;
             margin-bottom: 10px;
            }
       
          </style>";

	echo "<ul>";
	$urls = explode( "\n", $page_urls );
	foreach ( $urls as $url ) {
		$url = trim( $url );
		if ( ! empty( $url ) ) {
			$page_id = url_to_postid( $url );
			if ( $page_id ) {
				$content    = get_post_field( 'post_content', $page_id );
				$shortcodes = extract_shortcodes_from_content( $content );
				echo "<li>[ ] " . esc_url( $url ) . "</li>";
                echo "<ul>";
				foreach ( $shortcodes as $shortcode ) {
					echo "<li>[ ] " . esc_html( $shortcode ) . "</li>";
				}
                echo "</ul>";
			} else {
				echo "<p><strong>Page not found:</strong> " . esc_url( $url ) . "</p>";
			}
		}
	}
	echo "</ul>";
}

/**
 * Extracts shortcodes from given content.
 *
 * @param string $content Content to extract shortcodes from.
 *
 * @return array Array of shortcodes.
 */


function extract_shortcodes_from_content( $content ) {
	// Pattern for getting shortcodes from content
	$pattern = get_shortcode_regex();

	// Array to hold all shortcodes
	$shortcodes = array();

	if ( preg_match_all( '/' . $pattern . '/s', $content, $matches )
	     && array_key_exists( 0, $matches )
	) {
		// Iterate over matches and add full shortcode to array
		foreach ( $matches[0] as $shortcode ) {
			$shortcodes[] = $shortcode;
		}
	}

	return $shortcodes;
}
