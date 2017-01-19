<?php

wp_register_script('wecinv-scripts', plugin_dir_url( __FILE__ ).'js/wecinv_scripts.js');

wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-tabs');
wp_enqueue_script('wecinv-scripts', array('jquery', 'jquery-ui-tabs' ), '1.1', true);


echo '<h1>'.plugin_dir_url( __FILE__ ).'js/wecinv_scripts.js</h1>';

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

<div id="tabs">
  <ul>
    <li><a href="#tabs-1">Nunc tincidunt</a></li>
    <li><a href="#tabs-2">Proin dolor</a></li>
    <li><a href="#tabs-3">Aenean lacinia</a></li>
  </ul>
  <div id="tabs-1">
    <p>Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.</p>
  </div>
  <div id="tabs-2">
    <p>Morbi tincidunt, dui sit amet facilisis feugiat, odio metus gravida ante, ut pharetra massa metus id nunc. Duis scelerisque molestie turpis. Sed fringilla, massa eget luctus malesuada, metus eros molestie lectus, ut tempus eros massa ut dolor. Aenean aliquet fringilla sem. Suspendisse sed ligula in ligula suscipit aliquam. Praesent in eros vestibulum mi adipiscing adipiscing. Morbi facilisis. Curabitur ornare consequat nunc. Aenean vel metus. Ut posuere viverra nulla. Aliquam erat volutpat. Pellentesque convallis. Maecenas feugiat, tellus pellentesque pretium posuere, felis lorem euismod felis, eu ornare leo nisi vel felis. Mauris consectetur tortor et purus.</p>
  </div>
  <div id="tabs-3">
    <p>Mauris eleifend est et turpis. Duis id erat. Suspendisse potenti. Aliquam vulputate, pede vel vehicula accumsan, mi neque rutrum erat, eu congue orci lorem eget lorem. Vestibulum non ante. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce sodales. Quisque eu urna vel enim commodo pellentesque. Praesent eu risus hendrerit ligula tempus pretium. Curabitur lorem enim, pretium nec, feugiat nec, luctus a, lacus.</p>
    <p>Duis cursus. Maecenas ligula eros, blandit nec, pharetra at, semper at, magna. Nullam ac lacus. Nulla facilisi. Praesent viverra justo vitae neque. Praesent blandit adipiscing velit. Suspendisse potenti. Donec mattis, pede vel pharetra blandit, magna ligula faucibus eros, id euismod lacus dolor eget odio. Nam scelerisque. Donec non libero sed nulla mattis commodo. Ut sagittis. Donec nisi lectus, feugiat porttitor, tempor ac, tempor vitae, pede. Aenean vehicula velit eu tellus interdum rutrum. Maecenas commodo. Pellentesque nec elit. Fusce in lacus. Vivamus a libero vitae lectus hendrerit hendrerit.</p>
  </div>
</div>

<!-- Start Of Plugin Options -->

<div class="wrap">

    <?php  echo "<h2>" . __( 'WP Content Inventory Options', 'wecinv_trdom' ) . "</h2>";  ?>

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

<!-- End Of Plugin Options -->

<!-- Start Of Tracking Status -->

<?php

echo '<style>';
    echo '.inventory-table { width:95%; box-sizing:border-box; }';
echo '</style>';

echo '<h2>Content Inventory</h2>';

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

        if ($post_type->hierarchical) {

            wecinv_list_posts_hierarchically($post_type->name);

        } else {

            wecinv_list_posts_by_date($post_type->name);

        }

    }

}

?>

<!-- End Of Tracking Status -->
