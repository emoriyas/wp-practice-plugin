<?php
    include "db-setup.php";
    include "option-page.php";

    /**
     * Plugin Name: Practice Plugin
     * Description: This is a plugin
     * Version: 1.0
     * Author: Author
     */

    main();

    function main() {
        add_action( 'the_content', 'wpppAddHtmlContent');
        register_activation_hook( __FILE__, 'wpppDbSetUp' );
        if (is_admin()) {
            add_action( 'admin_menu', 'wpppAdminMenu' );
            add_action('admin_post_wppp_add_entry', 'wppp_add_html_entry');
        }
    }

    /**
     * Adds html to post content.
     *
     * @param $content Post content.
     */
    function wpppAddHtmlContent ($content) {
        global $isActive;
        global $post;
        global $wpdb;
        $tableName = $wpdb->prefix . "wppp_html_entry";

        $queryResults = $wpdb->get_results("SELECT * FROM $tableName;");

        foreach ($queryResults as $result) {
            if (!$result->is_active) {
                return $content;
            }

            $tagsIncluded = $result->tags_included;
            $tagsExcluded = $result->tags_excluded;
            $categoriesIncluded = $result->categories_included;
            $categoriesExcluded = $result->categories_excluded;

            // Check if a post is eligible for HTML entry
            $writeHtml = wpppCheckPostEligibility($post->ID, $tagsIncluded,
                    $tagsExcluded , $categoriesIncluded, $categoriesExcluded);

            if ($writeHtml) {
                $content = wpppAddHtml($content, $result->html, $result->insert_condition_type,
                        $result->insert_condition_after, $result->insert_condition_value,
                        $result->paragraph_limit_greater, $result->paragraph_limit_value);
            }
        }

        return $content;
    }

    /**
     * Check post eligibility for HTML content insertion.
     *
     * @param $postId Post ID.
     */
    function wpppCheckPostEligibility($postId, $tagsIncluded,
            $tagsExcluded , $categoriesIncluded, $categoriesExcluded) {
        $tags = get_the_tags($postId);
        $cats = get_the_category($postId);

        // Check for excluded categories.
        // TODO: use a built in PHP function for matching values.
        $excludedCategories = explode(",", $categoriesExcluded);
        foreach ($cats as $cat) {
            foreach ($excludedCategories as $excludeCat) {
                if (strcmp($cat->name, trim($excludeCat)) == 0) {
                    return false;
                }
            }
        }
        // Check for excluded tags.
        $excludedTags = explode(",", $tagsExcluded);
        foreach ($tags as $tag) {
            foreach ($excludedTags as $excludeTag) {
                if (strcmp($tag->name, trim($excludeTag)) == 0) {
                    return false;
                }
            }
        }

        // Check for accepted categories.
        $acceptedCategories = explode(",", $categoriesIncluded);
        foreach ($cats as $cat) {
            foreach ($acceptedCategories as $acceptCat) {
                if (strcmp($cat->name, trim($acceptCat)) == 0) {
                    return true;
                }
            }
        }
        // Check for accepted tags.
        $acceptedTags = explode(",", $tagsIncluded);
        foreach ($tags as $tag) {
            foreach ($acceptedTags as $acceptTag) {
                if (strcmp($tag->name, trim($acceptTag)) == 0) {
                    return true;
                }
            }
        }

        // Return false if there are no matching tags or categories.
        return false;
    }

    /**
     * Adds HTML content
     */
    function wpppAddHtml($content, $html, $insertConditionType, $insertContentAfter,
            $insertConditionValue, $paragraphLimitGreater, $paragraphLimitValue) {

        // Split content by paragraph.
        $dom = new DOMDocument();
        $contentArr = array();
        $dom->loadHTML($content);
        foreach($dom->getElementsByTagName('p') as $paragraph)
        {
            $contentArr[] = $dom->saveHTML($paragraph);
        }
        $contentArrLength = count($contentArr);

        // Do not add content if the number of paragraphs are greater than the ceiling limit
        // or if the number of paragraphs are fewer than the floor limit.
        // TODO: While it works right now, add checks where one or both variables are null.
        if ((($paragraphLimitGreater && ($contentArrLength > $paragraphLimitValue)))
                || (!$paragraphLimitGreater && ($contentArrLength < $paragraphLimitValue))) {
            return $content;
        }

        //insert html bit to content.
        if (strcmp($insertConditionType, "first") == 0) {
            if ($insertContentAfter) {
                array_splice($contentArr, 1, 0, $html);
            }
            else {
                array_splice($contentArr, 0, 0, $html);
            }
        }
        else if (strcmp($insertConditionType, "last") == 0) {
            if ($insertContentAfter) {
                array_splice($contentArr, $contentArrLength, 0, $html);
            }
            else {
                array_splice($contentArr, $contentArrLength - 1, 0, $html);
            }
        }
        else if ($insertConditionValue > 0) {
            // defaults to iterate if $insertConditionValue defined but $insertConditionType
            // is not "first" or "last".
            // TODO: Does not insert if the number of paragraphs match $insertConditionValue.
            for ($x = $insertConditionValue;
                    $x < $contentArrLength; $x = ($x + $insertConditionValue + 1)) {
                if ($insertContentAfter) {
                    array_splice($contentArr, $x, 0, $html);
                    $contentArrLength++;
                }
                else {
                    array_splice($contentArr, $x - 1, 0, $html);
                    $contentArrLength++;
                }
            }
        }

        return implode("", $contentArr);
    }
?>
