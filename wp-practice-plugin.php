<?php
    /**
     * Plugin Name: Practice Plugin
     * Description: This is a plugin
     * Version: 1.0
     * Author: Author
     */

    $accepted_tags = array("tag1", "tag3", "tag4");
    $html = "<p>Hello World!</p>";

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
        global $accepted_tags;
        $tags = get_the_tags($post->ID);

        foreach ($tags as $tag) {
            foreach ($accepted_tags as $acceptTag) {
                if (strcmp($tag->name, $acceptTag->name) == 0) {
                    add_action( 'the_content', 'wppp_add_html_content');
                }
            }
        }
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
