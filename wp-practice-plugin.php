<?php
    include "db-setup.php";

    /**
     * Plugin Name: Practice Plugin
     * Description: This is a plugin
     * Version: 1.0
     * Author: Author
     */

    $html = "<p>Hello World!</p>";

    $insertContentAfter = true;
    // Either iterate/first/last.
    $insertConditionType = "iterate";
    // Should be ignored if variable above is set to first/last.
    $insertConditionValue = 1;

    $paragraphLimitGreater = true;
    $paragraphLimitValue = 5;
    //TODO: add paragraph limit.

    main();

    function main() {
        add_action( 'the_content', 'wpppAddHtmlContent');
        register_activation_hook( __FILE__, 'wpppDbSetUp' );
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

        $queryResult = $wpdb->get_results(
                "SELECT tags_included, tags_excluded,
                    categories_included, categories_excluded, is_active
                FROM $tableName;");

        foreach ($queryResult as $result) {

            if (!$result->is_active) {
                return $content;
            }

            $tagsIncluded = $result->tags_included;
            $tagsExcluded = $result->tags_excluded;
            $categoriesIncluded = $result->categories_included;
            $categoriesExcluded = $result->categories_excluded;

            $writeHtml = wpppCheckPostEligibility($post->ID, $tagsIncluded,
                    $tagsExcluded , $categoriesIncluded, $categoriesExcluded);

            if ($writeHtml) {
                $content = wpppAddHtml($content);
                //return wpppAddHtml($content);
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

    function wpppAddHtml($content) {
        global $html;
        global $insertContentAfter;
        // Either iterate/first/last.
        global $insertConditionType;
        // Should be ignored if variable above is set to first/last.
        global $insertConditionValue;
        global $paragraphLimitGreater;
        global $paragraphLimitValue;

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
        else {
            array_splice($contentArr, 1, 0, $html);
        }

        return implode("", $contentArr);
    }
?>
