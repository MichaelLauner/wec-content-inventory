<?php

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

    <div class="wrap">
        <?php  echo "<h2>" . __( 'WP Content Inventory Options', 'wecinv_trdom' ) . "</h2>";  ?>

        <form name="wecinv_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">

            <input type="hidden" name="wecinv_hidden" value="Y">

            <?php

            echo '<h3>Stock Post Types</h3>';

            $args = array(
               'public'   => true,
               '_builtin' => true
            );

            $output = 'objects'; // names or objects, note names is the default
            $operator = 'and'; // 'and' or 'or'

            $post_types = get_post_types( $args, $output, $operator );

            foreach ( $post_types as $post_type ) {

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

            echo '<h3>Custom Post Types</h3>';

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
