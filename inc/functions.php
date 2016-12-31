<?php 

// get_main_site_for_network() func comes from the following plugin!!?!?
// https://wordpress.org/plugins/wp-multi-network/
if ( ! function_exists( 'get_main_site_for_network' ) ) :
/**
 * Get main site for a network
 *
 * @param int|stdClass $network Network ID or object, null for current network
 * @return int Main site ("blog" in old terminology) ID
 */
function get_main_site_for_network( $network = null ) {
	global $wpdb;

	// Get network
	$network = ! empty( $network )
		? wp_get_network( $network )
		: $GLOBALS['current_site'];

	// Network not found
	if ( empty( $network ) ) {
		return false;
	}

	// Use object site ID
	if ( ! empty( $network->blog_id ) ) {
		$primary_id = $network->blog_id;

	// Look for cached value
	} else {
		$primary_id = wp_cache_get( "network:{$network->id}:main_site", 'site-options' );

		if ( false === $primary_id ) {
			$sql        = "SELECT blog_id FROM {$wpdb->blogs} WHERE domain = %s AND path = %s";
			$query      = $wpdb->prepare( $sql, $network->domain, $network->path );
			$primary_id = $wpdb->get_var( $query );
			wp_cache_add( "network:{$network->id}:main_site", $primary_id, 'site-options' );
		}
	}

	return (int) $primary_id;
}
endif;