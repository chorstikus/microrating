<?php
/*
Plugin Name: Microrating
Description: Adds star rating at the end of the content
Plugin URI: http://#
Author: Christian Horstmann
Author URI: http://facebook.com/Christian.horstmann1988
Version: 1.0
License: GPL2
Text Domain: microrating
Domain Path: Domain Path
*/

/*

    Copyright (C) 2014  Christian Hrostmann  c.horstmann@elancer-team.de

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Enqueue Scripts and Styles
 * @return void
 */
function mr_enqueue_script_and_styles() {
    global $post;
    wp_enqueue_script( 'rating_js', plugins_url( 'js/rating.js', __FILE__ ), array( 'jquery' ) );
    wp_enqueue_script( 'mr_main_js', plugins_url( 'js/microrating.js', __FILE__ ), array( 'jquery' ) );

    // set some local variables to javascript to handle the realtime updates
    $ratings = get_post_meta( $post->ID, 'mr_post_rating' );
    $count = sizeof( $ratings );
    $sum = array_sum( $ratings );
    $average = ( $count > 0 ) ? round( $sum / $count ) : 0;
    if ( isset( $_COOKIE['mr_voted'] ) ) {
        $value = $_COOKIE['mr_voted'];
    } else {
        $value = false;
    }
    wp_localize_script( 'mr_main_js', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'post_id' => $post->ID, 'average' => $average, 'count' => $count, 'sum' => $sum, 'voted_value' => $value, 'plugins_url' => plugins_url( 'img', __FILE__ ) ) );


    wp_enqueue_style( 'mr_main_css', plugins_url( 'css/microrating.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'mr_enqueue_script_and_styles' );


/**
 * Adds the inout fields for star rating at the end of the content
 * @param  string $content
 * @return string
 */
function mr_print_start_rating( $content ) {

    // only posts are rateable
    if ( is_single() ) {
        $ratings = get_post_meta( get_the_ID(), 'mr_post_rating' );
        $count = sizeof( $ratings );
        $average = ( $count > 0 ) ? round( array_sum( $ratings ) / $count ) : 0;

        $content .= "<div class='rating-container' itemscope itemtype='http://data-vocabulary.org/Review-aggregate'>
            <h4>Review <span itemprop='itemreviewed'>".get_the_title()."</span></h4>
            <div class='star-container clearfix'>
            </div>";

        if ( $count > 0 ) {
            $content .= "<p>
                <span itemprop='rating' itemscope itemtype='http://data-vocabulary.org/Rating'>
                    <span itemprop='average' class='average'>{$average}</span>
                    out of <span itemprop='best'>5</span>
                </span>
                based on <span itemprop='votes' class='count'>{$count}</span> ratings.
            </p>";
        } else {
            $content .= "Be the first and review this article.";
        }

        $content .= "</div>";
    }
    return $content;
}
add_filter( 'the_content', 'mr_print_start_rating' );

/**
 * The ajax function that is called if someone rates a post.
 * It will get the parameters from the ajax post request and creates a post meta entry.
 * For only reviewing once it then sets a cookie.
 */
function add_rating_to_post() {
    $vote = intval( $_POST['vote'] );
    $postID = intval( $_POST['post_id'] );

    add_post_meta( $postID, 'mr_post_rating', $vote );

    setcookie( 'mr_voted', $vote, time( ) + 3600 * 24 * 100, '/' );

    die();
}
add_action( 'wp_ajax_add_rating', 'add_rating_to_post' );
add_action( 'wp_ajax_nopriv_add_rating', 'add_rating_to_post' );
