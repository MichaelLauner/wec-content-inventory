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
