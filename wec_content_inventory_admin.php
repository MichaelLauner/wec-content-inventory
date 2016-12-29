<?php

echo '<h1>WP Content Inventory</h1>';

echo '<p>Stock Post Types</p>';
wec_inventory_get_stock_posttypes();

echo '<p> Custom Post Types</p>';
wec_inventory_get_custom_posttypes();



?>
