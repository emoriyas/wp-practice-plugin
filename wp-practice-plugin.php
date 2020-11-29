<?php
    /**
     * Plugin Name: Practice Plugin
     * Description: This is a plugin
     * Version: 1.0
     * Author: Author
     */

    __initialize();

    function __initialize() {
        add_action( 'the_post', 'wppp_add_html_control');
    }

    /**
     * Checks post tags and categories to determine if the post
     * should be modified.
     *
     * @param $post Post object.
     */
    function wppp_add_html_control ($post) {
        add_action( 'the_content', 'wppp_add_html_content');
    }

    /**
     * Adds html to post content.
     *
     * @param $content Post content.
     */
    function wppp_add_html_content ($content) {
        return $content .= "Hello World!";
    }
?>
