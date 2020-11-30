<?php
    /**
     * Create database table for html entry function.
     */
    function wpppDbSetUp() {
    status_header(200);
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $tableName = $wpdb->prefix . "wppp_html_entry";
        
        $sql = "CREATE TABLE $tableName 
                (id INT PRIMARY KEY AUTO_INCREMENT,
                html TEXT,
                paragraph_limit_greater BOOLEAN,
                paragraph_limit_value INT,
                tags_included VARCHAR(255),
                tags_excluded VARCHAR(255),
                categories_included VARCHAR(255),
                categories_excluded VARCHAR(255),
                insert_condition_type VARCHAR(255),
                insert_condition_after BOOLEAN,
                insert_condition_value int,
                is_active BOOLEAN) $charset_collate;";
        dbDelta( $sql );
    }
?>
