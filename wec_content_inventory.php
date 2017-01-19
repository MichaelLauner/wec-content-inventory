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

        echo '<table class="'.$wecinv_post_type.'-inventory inventory-table">';

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

/**
 * Create Table Of Posts In Hierarchical Order
 */
function wecinv_list_posts_hierarchically($wecinv_post_type) {

    $top_level_page = get_posts(
        array(
            'post_parent' => 0,
            'sort_column' => 'menu_order',
            'sort_order' => 'desc',
            'post_type' => $wecinv_post_type,
            'posts_per_page' => -1,
            'post_status' => 'any'
        )
    );

    echo '<table class="'.$wecinv_post_type.'inventory inventory-table">';

        echo '<tr style="background-color:#c2c2c2">';

            //Column 1
            echo '<th>';
                echo 'Top Level';
            echo '</th>';

            //Column 2
            echo '<th>';
                echo 'Second Level';
            echo '</th>';

            //Column 3
            echo '<th>';
                echo 'Third Level';
            echo '</th>';

            //Column 4
            echo '<th>';
                echo 'Fourth Level';
            echo '</th>';

            //Column 5
            echo '<th>';
                echo 'Content Approval';
            echo '</th>';

            //Column 6
            echo '<th>';
                echo 'Permalink';
            echo '</th>';


        echo '</tr>';

        foreach( $top_level_page as $page ) {

            echo '<tr style="background-color:#fce5cd">';

                //Column 1
                echo '<td>';
                    echo $page->post_title;
                echo '</td>';

                //Column 2
                echo '<td>';
                echo '</td>';

                //Column 3
                echo '<td>';
                echo '</td>';

                //Column 4
                echo '<td>';
                echo '</td>';

                //Column 5
                echo '<td>';
                    if ( get_field('content_approval', $page->ID)=="Approved" ) {
                        echo '<span style="color:green;"><strong>Approved</strong</span>';
                    } elseif (get_field('content_approval', $page->ID)=="In Progress") {
                        echo '<span style="color:blue;"><strong>In Progress</strong</span>';
                    } else {
                        echo '<span style="color:red;"><strong>Not Approved</strong</span>';
                    }
                echo '</td>';

                //Column 6
                echo '<td>';
                    $permalink = get_permalink( $page->ID );
                    $permalink = substr($permalink, 0);
                    echo '<a href="'.$permalink.'">Page&nbsp;Link</a>&nbsp;-&nbsp;';

                    echo '<a href="'.get_site_url().'/wp-admin/post.php?post='.$page->ID.'&action=edit">Edit</a>';
                echo '</td>';

            echo '</tr>';

            // ==================================
            // Check For Second Level Pages
            // ==================================
            $second_level_page = get_pages(
                array(
                    'parent' => $page->ID,
                    'sort_column' => 'menu_order',
                    'sort_order' => 'desc',
                    'post_type' => $wecinv_post_type
                )
            );

            foreach( $second_level_page as $page ) {

                echo '<tr>';

                    //Column 1
                    echo '<td>';
                    echo '</td>';

                    //Column 2
                    echo '<td>';
                        echo $page->post_title;
                    echo '</td>';

                    //Column 3
                    echo '<td>';
                    echo '</td>';

                    //Column 4
                    echo '<td>';
                    echo '</td>';

                    //Column 5
                    echo '<td>';
                        if ( get_field('content_approval', $page->ID)=="Approved" ) {
                            echo '<span style="color:green;"><strong>Approved</strong</span>';
                        } elseif (get_field('content_approval', $page->ID)=="In Progress") {
                            echo '<span style="color:blue;"><strong>In Progress</strong</span>';
                        } else {
                            echo '<span style="color:red;"><strong>Not Approved</strong</span>';
                        }
                    echo '</td>';

                    //Column 6
                    echo '<td>';
                        $permalink = get_permalink( $page->ID );
                        $permalink = substr($permalink, 0);
                        echo '<a href="'.$permalink.'">Page&nbsp;Link</a>&nbsp;-&nbsp;';

                        echo '<a href="'.get_site_url().'/wp-admin/post.php?post='.$page->ID.'&action=edit">Edit</a>';
                    echo '</td>';

                echo '</tr>';

                // ==================================
                // Check For Third Level Pages
                // ==================================
                $third_level_page = get_pages(
                    array(
                        'parent' => $page->ID,
                        'sort_column' => 'menu_order',
                        'sort_order' => 'desc',
                        'post_type' => $wecinv_post_type
                    )
                );

                foreach( $third_level_page as $page ) {

                    echo '<tr>';

                        //Column 1
                        echo '<td>';
                        echo '</td>';

                        //Column 2
                        echo '<td>';
                        echo '</td>';

                        //Column 3
                        echo '<td>';
                            echo $page->post_title;
                        echo '</td>';

                        //Column 4
                        echo '<td>';
                        echo '</td>';

                        //Column 5
                        echo '<td>';
                            if ( get_field('content_approval', $page->ID)=="Approved" ) {
                                echo '<span style="color:green;"><strong>Approved</strong</span>';
                            } elseif (get_field('content_approval', $page->ID)=="In Progress") {
                                echo '<span style="color:blue;"><strong>In Progress</strong</span>';
                            } else {
                                echo '<span style="color:red;"><strong>Not Approved</strong</span>';
                            }
                        echo '</td>';

                        //Column 6
                        echo '<td>';
                            $permalink = get_permalink( $page->ID );
                            $permalink = substr($permalink, 0);
                            echo '<a href="'.$permalink.'">Page&nbsp;Link</a>&nbsp;-&nbsp;';

                            echo '<a href="'.get_site_url().'/wp-admin/post.php?post='.$page->ID.'&action=edit">Edit</a>';
                        echo '</td>';

                    echo '</tr>';

                    // ==================================
                    // Check For Fourth Level Pages
                    // ==================================
                    $fourth_level_page = get_pages(
                        array(
                            'parent' => $page->ID,
                            'sort_column' => 'menu_order',
                            'sort_order' => 'desc',
                            'post_type' => $wecinv_post_type
                        )
                    );

                    foreach( $fourth_level_page as $page ) {

                        echo '<tr>';

                            //Column 1
                            echo '<td>';
                            echo '</td>';

                            //Column 2
                            echo '<td>';
                            echo '</td>';

                            //Column 3
                            echo '<td>';
                            echo '</td>';

                            //Column 4
                            echo '<td>';
                                echo $page->post_title;
                            echo '</td>';

                            //Column 5
                            echo '<td>';
                                if ( get_field('content_approval', $page->ID)=="Approved" ) {
                                    echo '<span style="color:green;"><strong>Approved</strong</span>';
                                } elseif (get_field('content_approval', $page->ID)=="In Progress") {
                                    echo '<span style="color:blue;"><strong>In Progress</strong</span>';
                                } else {
                                    echo '<span style="color:red;"><strong>Not Approved</strong</span>';
                                }
                            echo '</td>';

                            //Column 6
                            echo '<td>';
                                $permalink = get_permalink( $page->ID );
                                $permalink = substr($permalink, 0);
                                echo '<a href="'.$permalink.'">Page&nbsp;Link</a>&nbsp;-&nbsp;';

                                echo '<a href="'.get_site_url().'/wp-admin/post.php?post='.$page->ID.'&action=edit">Edit</a>';
                            echo '</td>';

                        echo '</tr>';

                        // ==================================
                        // Check For Fifth and Beyond Level Pages
                        // ==================================
                        $fifth_level_page = get_pages(
                            array(
                                'parent' => $page->ID,
                                'sort_column' => 'menu_order',
                                'sort_order' => 'desc',
                                'post_type' => $wecinv_post_type
                            )
                        );

                        foreach( $fifth_level_page as $page ) {

                            echo '<tr>';

                                //Column 1
                                echo '<td>';
                                echo '</td>';

                                //Column 2
                                echo '<td>';
                                echo '</td>';

                                //Column 3
                                echo '<td>';
                                echo '</td>';

                                //Column 4
                                echo '<td>';
                                echo '</td>';

                                //Column 5
                                echo '<td>';
                                    if ( get_field('content_approval', $page->ID)=="Approved" ) {
                                        echo '<span style="color:green;"><strong>Approved</strong</span>';
                                    } elseif (get_field('content_approval', $page->ID)=="In Progress") {
                                        echo '<span style="color:blue;"><strong>In Progress</strong</span>';
                                    } else {
                                        echo '<span style="color:red;"><strong>Not Approved</strong</span>';
                                    }
                                echo '</td>';

                                //Column 6
                                echo '<td>';
                                    $permalink = get_permalink( $page->ID );
                                    $permalink = substr($permalink, 0);
                                    echo '<a href="'.$permalink.'">Page&nbsp;Link</a>&nbsp;-&nbsp;';

                                    echo '<a href="'.get_site_url().'/wp-admin/post.php?post='.$page->ID.'&action=edit">Edit</a>';
                                echo '</td>';

                            echo '</tr>';

                        }

                    }

                }

            }

        }

    echo '</table>';

}
