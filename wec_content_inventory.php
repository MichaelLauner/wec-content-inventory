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
 * Create Table Of Posts Ordered By Date
 */

function wecinv_list_posts_by_date($wecinv_post_type) {

    // The Query
    $the_query = new WP_Query(
        array(
            'post_type'         => $wecinv_post_type,
            'posts_per_page'    => -1,
            'order'             => 'ASC'
        )
    );

    // The Loop
    if ( $the_query->have_posts() ) {

        $count = $the_query->post_count;

        echo '<h2 id="posts-inventory" class="open-close">Posts: '.$count.'</h2>';

        echo '<table class="posts-inventory inventory-table" style="width:100%">';

            echo '<tr style="background-color:#c2c2c2">';

                //Column 1
                echo '<th>';
                    echo 'Featured Image';
                echo '</th>';

                //Column 2
                echo '<th>';
                    echo 'Title';
                echo '</th>';

                //Column 3
                echo '<th>';
                    echo 'Content Approval';
                echo '</th>';

                //Column 4
                echo '<th>';
                    echo 'Permalink';
                echo '</th>';


            echo '</tr>';

        	while ( $the_query->have_posts() ) {
        		$the_query->the_post();

                $permalink = get_permalink( $the_query->ID );
                $permalink = substr($permalink, 0);

                $post_id = get_the_ID();

                //update_post_meta($post_id, 'content_approval', 'Approved');

                echo '<tr style="background-color:#fce5cd">';

                    //Column 1
                    echo '<td>';
                        echo '<a href="'.$permalink.'">';
                            echo get_the_post_thumbnail($post_id,'thumbnail');
                        echo '</a>';
                    echo '</td>';

                    //Column 2
                    echo '<td>';
                        the_title();
                    echo '</td>';

                    //Column 3
                    echo '<td>';
                        if ( get_field('content_approval', $post_id)=="Approved" ) {
                            echo '<span style="color:green;"><strong>Approved</strong</span>';
                        } elseif (get_field('content_approval', $post_id)=="In Progress") {
                            echo '<span style="color:blue;"><strong>In Progress</strong</span>';
                        } else {
                            echo '<span style="color:red;"><strong>Not Approved</strong</span>';
                        }
                    echo '</td>';

                    //Column 4
                    echo '<td>';
                    $permalink = get_permalink( $post_id );
                    $permalink = substr($permalink, 0);
                    echo '<a href="'.$permalink.'">Page&nbsp;Link</a>&nbsp;-&nbsp;';

                    echo '<a href="'.get_site_url().'/wp-admin/post.php?post='.$post_id.'&action=edit">Edit</a>';
                    echo '</td>';

                echo '</tr>';


        	}
        	echo '</ul>';
        	/* Restore original Post Data */
        	wp_reset_postdata();

        echo '</table>';

    } else {
    	// no posts found
    }
}
