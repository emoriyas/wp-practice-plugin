<?php

   function wpppAdminMenu() {
       add_menu_page('HTML Entry', 'HTML Entry', 'edit_posts', 'wppp_add_html_entry',
               'wppp_admin_page', 'dashicons-groups', 6 ) ;
    }

    /**
     * Generates admin page for html entries.
     */
    //TODO: List existing html entries for editing and overhaul current input types.
    function wppp_admin_page() {
        echo '<form method="POST" action="admin-post.php">
              <input type="hidden" name="action" value="wppp_add_entry">
              <label>HTML: </label><input type="text" name="html" /><br />
              <label>Paragraph Greater/Fewer: </label><input type="text" name="paragraph_limit_greater" /><br />
              <label>Paragraph Limit: </label><input type="text" name="paragraph_limit_value" /><br />
              <label>Categories: </label><input type="text" name="categories_included" /><br />
              <label>Categories Excluded: </label><input type="text" name="categories_excluded" /><br />
              <label>Tags: </label><input type="text" name="tags_included" /><br />
              <label>Tags Excluded: </label><input type="text" name="tags_excluded" /><br />
              <label>Insertion Type: </label><input type="text" name="insert_condition_type" /><br />
              <label>Insertion After/Before: </label><input type="text" name="insert_condition_after" /><br />
              <label>Insertion every: </label><input type="text" name="insert_condition_value" /> lines <br />
              <label>Active: </label><input type="text" name="is_active" /> <br />
              <input type="submit" value="submit" />
              </form>';
    }

    /**
     * Adds entry to database based on wppp_add_entry POST request
     * then redirects to the page generated by wppp_admin_page().
     *
     * Currently does not work.
     */
    function wppp_add_html_entry() {
        global $wpdb;
        $tableName = $wpdb->prefix . "wppp_html_entry";
        $wpdb->insert( $table_name, $_REQUEST );
        header("Location: ./admin.php?page=wppp_add_html_entry");
    }
?>
