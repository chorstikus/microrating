<?php //if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();

$allposts = get_posts( 'numberposts=-1&post_type=post&post_status=any' );

foreach ( $allposts as $postinfo ) {
    delete_post_meta( $postinfo->ID, 'mr_post_rating' );
}