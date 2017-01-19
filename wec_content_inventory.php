<?php
/*
Plugin Name: WP Content Inventory
Plugin URI: http://www.westedge.us
Description: Plugin for managing content and building review processes for clients with lots of content
Author: Michael Launer - West Edge Collective
Version: 1.0
Author URI: http://www.westedge.us
*/

//==============================================================================

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
 * Tracked Post Types Array
 */
function wecinv_tracked_posttypes() {

    $args = array(
       'public'   => true
    );

    $output = 'objects'; // names or objects, note names is the default
    $operator = 'and'; // 'and' or 'or'

    $post_types = get_post_types( $args, $output, $operator );

    $tracked_posttypes = array();

    foreach ( $post_types as $post_type ) {

        $track_option = get_option('wecinv_track_'.$post_type->name);

        if ($track_option=='10') {

            $tracked_posttypes[] = $post_type->name;

        }

    }

    return $tracked_posttypes;

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


/**
 * List All Anapproved
 */
function wecinv_list_all_unapproved() {
    // The Query
    $the_query = new WP_Query(
        array(
            'posts_per_page'    => -1,
            'order'             => 'ASC',
            'post_type'         => wecinv_tracked_posttypes(),
            'meta_query'        => array(
                'relation' => 'OR',
                array(
                    'key'     => 'content_approval',
                    'value'   => 'Approved',
                    'compare' => '!=',
                ),
                array(
                    'key'     => 'content_approval',
                    'value'   => 'Approved',
                    'compare' => 'NOT EXISTS',
                ),
            )
        )
    );

    // The Loop
    if ( $the_query->have_posts() ) {

        $count = $the_query->post_count;

        echo '<h3 id="unapproved-inventory" class="open-close">All Unapproved: '.$count.'</h3>';

        echo '<div>';

            echo '<table class="unapproved-inventory inventory-table">';

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

        echo '</div>';

    } else {
    	// no posts found
    }
}


/**
 * Dashboard Stats
 */
function wecinv_dashboard_stats() {

    $total_page_count = get_posts(
        array(
            'sort_column'   => 'menu_order',
            'sort_order'    => 'desc',
            'post_type'     => wecinv_tracked_posttypes(),
            'posts_per_page'=> -1
        )
    );

    $completed_page_count = get_posts(
        array(
            'sort_column'   => 'menu_order',
            'sort_order'    => 'desc',
            'post_type'     => wecinv_tracked_posttypes(),
            'meta_key'      => 'content_approval',
            'meta_value'    => 'Approved',
            'posts_per_page'=> -1
        )
    );

    echo '<div style="max-width:1440px; padding:20px; margin:0 auto;">';

        echo '<h4>Total Page Count: '.count($total_page_count).'</h4>';

        echo '<h4>Pages Finished: '.count($completed_page_count).'</h4>';

        $percentage_complete = count($completed_page_count) / count($total_page_count);
        $percent_friendly = number_format( $percentage_complete * 100, 2 ) . '%';

        echo '<h4>Percentage Complete: '.$percent_friendly.'</h4>';

    echo '</div>';

}

/**
 * Create Metabox on Tracked Post Types
 */
function wecinv_list_posttype() {

    $args = array(
       'public'   => true
    );

    $output = 'objects'; // names or objects, note names is the default
    $operator = 'and'; // 'and' or 'or'

    $post_types = get_post_types( $args, $output, $operator );

    foreach ( $post_types as $post_type ) {

        $track_option = get_option('wecinv_track_'.$post_type->name);

        if ($track_option=='10') {

            echo '<h3>'.$post_type->label.'</h3>';

            echo '<div>';

                if ($post_type->hierarchical) {

                    wecinv_list_posts_hierarchically($post_type->name);

                } else {

                    wecinv_list_posts_by_date($post_type->name);

                }

            echo '</div>';

        }

    }

}


/**
 * Create Metabox on Tracked Post Types
 */
function wecinv_metabox() {

    //Add Metabox Option To All Post Types
    $args = array(
       'public'   => true
    );

    $post_types = get_post_types( $args );

    add_meta_box('wecinv_meta_box', 'WP Content Inventory', 'wecinv_mentox_content', $post_types, 'advanced', 'high');

}
add_action('admin_menu', 'wecinv_metabox');

// Callback function to show fields in meta box
function wecinv_mentox_content() {
    global $post;

    $post_id = $post->ID;

    $post_type = get_post_type( $post_id );

    if ( get_option('wecinv_track_'.$post_type) == '10' ) {

        //Cross Content Data
        $track_status = get_post_meta( $post->ID, 'content_approval', true );

        echo '<h2>Content Approval Status ';
        echo '<select name="content_approval" id="content_approval">';

            echo '<option value="Not Approved" ';
            if ( $track_status == 'Not Approved') { echo 'selected'; }
            echo '>Not Approved</option>';

            echo '<option value="In Progress" ';
            if ( $track_status == 'In Progress') { echo 'selected'; }
            echo '>In Progress</option>';

            echo '<option value="Approved" ';
            if ( $track_status == 'Approved') { echo 'selected'; }
            echo '>Approved</option>';

        echo '</select></h2>';

    }

}

/**
 * Save data from meta box
 */
function wecinv_save_status($post_id) {
    /* in production code, $slug should be set only once in the plugin,
       preferably as a class property, rather than in each function that needs it.
     */
    //$slug = 'post';

    /* check whether anything should be done */
    //$_POST += array("{$slug}_edit_nonce" => '');
    //if ( $slug != $_POST['post_type'] ) {
    //    return;
    //}
    if ( !current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    //if ( !wp_verify_nonce( $_POST["{$slug}_edit_nonce"],
    //                       plugin_basename( __FILE__ ) ) )
    //{
    //    return;
    //}

    /* Request passes all checks; update the post's metadata */
    if (isset($_REQUEST['content_approval'])) {
        update_post_meta($post_id, 'content_approval', $_REQUEST['content_approval']);
    }

}
add_action( 'save_post', 'wecinv_save_status');
