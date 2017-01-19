<?php

wp_enqueue_style( 'wecinv-style', plugin_dir_url( __FILE__ ).'/css/wecinv.css' );

wp_register_script('wecinv-scripts', plugin_dir_url( __FILE__ ).'js/wecinv_scripts.js');

wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-tabs');
wp_enqueue_script('jquery-ui-accordion');
wp_enqueue_script('wecinv-scripts', array('jquery', 'jquery-ui-tabs' ), '1.1', true);

if($_POST['wecinv_hidden'] == 'Y') {

    //Form data sent
    foreach ($_POST as $key => $value) {

        $wecinv_track_value         = (string) $key;
        $wecinv_track_flag          = 'wecinv_track_';
        $wecinv_track_status        = strpos($key, $wecinv_track_flag);
        $wecinv_track_status_value  = $value;

        if ( $wecinv_track_status !== false ) {

            update_option($wecinv_track_value, $wecinv_track_status_value);

        }

    }

    echo '<div class="updated"><p><strong>';
        _e('Options saved.' );
    echo '</strong></p></div>';

} else {

    //Normal page display
    // $wecinv_test_option = get_option('wecinc_test_option');
    // $wecinv_test_dropdown = get_option('wecinv_test_dropdown');

}

?>

<!-- Start Of Plugin Options -->

<div class="wrap">

    <div class="preloader">

    </div>

    <div class="preloaded-content" style="display:none;">

        <div id="tabs">

          <ul>
            <li><a href="#tabs-1">Content Inventory</a></li>
            <li><a href="#tabs-2">Plugin Settings</a></li>
          </ul>

          <div id="tabs-1">

              <!-- Plugin Reports -->

              <?php

                wecinv_dashboard_stats();

              ?>

              <div id="accordion">

                  <?php
                  //Unapproved Table
                  wecinv_list_all_unapproved();

                  //Post Type Tables
                  wecinv_list_posttype();
                  ?>

              </div>

          </div>
          <div id="tabs-2">

              <!-- Plugin Settings -->

              <form name="wecinv_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">

                  <input type="hidden" name="wecinv_hidden" value="Y">

                  <?php

                  echo '<h3>Stock Post Types Tracking Settings</h3>';

                  $args = array(
                     'public'   => true,
                     '_builtin' => true
                  );

                  $output = 'objects'; // names or objects, note names is the default
                  $operator = 'and'; // 'and' or 'or'

                  $post_types = get_post_types( $args, $output, $operator );

                  foreach ( $post_types as $post_type ) {

                      $track_option = get_option('wecinv_track_'.$post_type->name);

                      echo '<p><strong>'.$post_type->label.': ';

                          echo '<select name="wecinv_track_'.$post_type->name.'">';

                              echo '<option value="0" ';
                              if ( $track_option == '0') { echo 'selected'; }
                              echo '>Do Not Track</option>';

                              echo '<option value="10" ';
                              if ( $track_option == '10') { echo 'selected'; }
                              echo '>Track </option>';

                          echo '</select>';

                      echo '</p>';

                  }

                  echo '<h3>Custom Post Types Tracking Settings</h3>';

                  $args = array(
                     'public'   => true,
                     '_builtin' => false
                  );

                  $output = 'objects'; // names or objects, note names is the default
                  $operator = 'and'; // 'and' or 'or'

                  $post_types = get_post_types( $args, $output, $operator );

                  foreach ( $post_types as $post_type ) {

                      $track_option = get_option('wecinv_track_'.$post_type->name);

                      echo '<p><strong>'.$post_type->label.': ';

                          echo '<select name="wecinv_track_'.$post_type->name.'">';

                              echo '<option value="0" ';
                              if ( $track_option == '0') { echo 'selected'; }
                              echo '>Do Not Track</option>';

                              echo '<option value="10" ';
                              if ( $track_option == '10') { echo 'selected'; }
                              echo '>Track </option>';

                          echo '</select>';

                      echo '</p>';

                  }

                  ?>

                  <p class="submit">
                      <input type="submit" name="Submit" value="<?php _e('Update Options', 'wecinv_trdom' ) ?>" />
                  </p>

              </form>

          </div>

        </div>

    </div>

</div>
