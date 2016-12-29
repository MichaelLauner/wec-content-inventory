<?php
    /*
    Plugin Name: WP Content Inventory
    Plugin URI: http://www.westedge.us
    Description: Plugin for managing content and building review processes for clients with lots of content
    Author: Michael Launer - West Edge Collective
    Version: 1.0
    Author URI: http://www.westedge.us
    */

/**
* Add Link To Tool Menu
*/
function wec_inventory_admin_actions() {
    add_management_page("WP Content Inventory", "WP Content Inventory", 1, "WP Content Inventory", "wec_inventory_admin");
}
add_action('admin_menu', 'wec_inventory_admin_actions');

/**
* Create Admin Page
*/
function wec_inventory_admin() {
    include('wec_content_inventory_admin.php');
}

/**
* List Post Types
*/
function wec_inventory_get_custom_posttypes() {

    $args = array(
       'public'   => true,
       '_builtin' => false
    );

    $output = 'names'; // names or objects, note names is the default
    $operator = 'and'; // 'and' or 'or'

    $post_types = get_post_types( $args, $output, $operator );

    foreach ( $post_types  as $post_type ) {

       echo '<p>' . $post_type . '</p>';
    }

}


function wec_inventory_get_stock_posttypes() {

    $args = array(
       'public'   => true,
       '_builtin' => true
    );

    $output = 'names'; // names or objects, note names is the default
    $operator = 'and'; // 'and' or 'or'

    $post_types = get_post_types( $args, $output, $operator );

    foreach ( $post_types  as $post_type ) {

       echo '<p>' . $post_type . '</p>';
    }

}
