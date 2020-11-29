<?php
    /**
     * Plugin Name: Practice Plugin
     * Description: This is a plugin
     * Version: 1.0
     * Author: Author
     */

    $active = true;

    $acceptedTags = array("tag1", "tag3", "tag4");
    $excludedTags = array("tag2");
    $acceptedCategories = array("category1", "category2");
    $excludedCategories = array();

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
    }

    /**
     * Adds html to post content.
     *
     * @param $content Post content.
     */
    function wpppAddHtmlContent ($content) {
        global $active;
        global $post;

        if (!$active) {
            return $content;
        }

        $writeHtml = wpppCheckPostEligibility($post->ID);

        if ($writeHtml) {
            return wpppAddHtml($content);
        }

        return $content;
    }

    /**
     * Check post eligibility for HTML content insertion.
     *
     * @param $postId Post ID.
     */
    function wpppCheckPostEligibility($postId) {
        global $acceptedTags;
        global $excludedTags;
        global $acceptedCategories;
        global $excludedCategories;

        $tags = get_the_tags($postId);
        $cats = get_the_category($postId);

        // Check for excluded categories.        
        foreach ($cats as $cat) {
            foreach ($excludedCategories as $excludeCat) {
                if (strcmp($cat->name, $excludeCat) == 0) {
                echo "bbb";
                    return false;
                }
            }
        }
        // Check for excluded tags.        
        foreach ($tags as $tag) {
            foreach ($excludedTags as $excludeTag) {
                if (strcmp($tag->name, $excludeTag) == 0) {
                echo "aaa";
                    return false;
                }
            }
        }

        // Check for accepted categories.
        foreach ($cats as $cat) {
            foreach ($acceptedCategories as $acceptCat) {
                if (strcmp($cat->name, $acceptCat) == 0) {
                    return true;
                }
            }
        }
        // Check for accepted tags.
        foreach ($tags as $tag) {
            foreach ($acceptedTags as $acceptTag) {
                if (strcmp($tag->name, $acceptTag) == 0) {
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

        return $content .= $html;
    }
?>
