<?php
global $exchanger_db_version;
$exchanger_db_version = "1.3.0";
function exchanger_install () {
    global $wpdb;
    global $exchanger_db_version;

    $table_name = $wpdb->prefix . "exchanger_cources";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE " . $table_name . " (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          time bigint(11) DEFAULT '0' NOT NULL,
          name VARCHAR(100) NOT NULL,
          code VARCHAR(10) NOT NULL,
          exchange_name VARCHAR(100) NOT NULL,
          exchange_code VARCHAR(10) NOT NULL,
          cource float DEFAULT '0' NOT NULL,
          UNIQUE KEY id (id)
        );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        add_option("exchanger_db_version", $exchanger_db_version);

    }

    $installed_ver = get_option( "exchanger_db_version" );

    if( $installed_ver != $exchanger_db_version ) {

        $sql = "CREATE TABLE " . $table_name . " (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          time bigint(11) DEFAULT '0' NOT NULL,
          name VARCHAR(100) NOT NULL,
          code VARCHAR(10) NOT NULL,
          exchange_name VARCHAR(100) NOT NULL,
          exchange_code VARCHAR(10) NOT NULL,
          cource float DEFAULT '0' NOT NULL,
          koefficient float DEFAULT '0' NOT NULL,
          minsum float DEFAULT '0' NOT NULL,
          UNIQUE KEY id (id)
        );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $table_options_name = $wpdb->prefix . "exchanger_valutes";
        $sql = "CREATE TABLE " . $table_options_name . " (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          time bigint(11) DEFAULT '0' NOT NULL,
          name VARCHAR(100) NOT NULL,
          code VARCHAR(10) NOT NULL,
          UNIQUE KEY id (id)
        );";


        dbDelta($sql);

        $table_reserv_name = $wpdb->prefix . "exchanger_valutes_reserv";
        $sql = "CREATE TABLE " . $table_reserv_name . " (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          time bigint(11) DEFAULT '0' NOT NULL,
          name VARCHAR(100) NOT NULL,
          code VARCHAR(10) NOT NULL,
          reserv float DEFAULT '0' NOT NULL,
          UNIQUE KEY id (id)
        );";


        dbDelta($sql);

        $table_orders_name = $wpdb->prefix . "exchanger_orders";

        $sql = "CREATE TABLE " . $table_orders_name . " (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          time bigint(11) DEFAULT '0' NOT NULL,
          time_update bigint(11) DEFAULT '0' NOT NULL,
          order_num VARCHAR(100) NOT NULL,
          order_data MEDIUMBLOB NOT NULL,
          status TINYINT(2) DEFAULT '0' NOT NULL,
          UNIQUE KEY id (id)
        );";
        dbDelta($sql);

        update_option( "exchanger_db_version", $exchanger_db_version );
    }

    function the_slug_exists($post_name) {
        global $wpdb;
        if($wpdb->get_row("SELECT post_name FROM wp_posts WHERE post_name = '" . $post_name . "'", 'ARRAY_A')) {
            return true;
        } else {
            return false;
        }
    }

    $order_page_title = 'My order';
    $order_page_content = '[exchanger_order_info]';
    $order_page_check = get_page_by_title($order_page_title);
    $order_page = array(
        'post_type' => 'page',
        'post_title' => $order_page_title,
        'post_content' => $order_page_content,
        'post_status' => 'publish',
        'post_author' => 1,
        'post_slug' => 'exchange-order'
    );
    if(!isset($order_page_check->ID) && !the_slug_exists('my-order')){
        $order_page_id = wp_insert_post($order_page);
    }

    $table_name = $wpdb->prefix . "exchanger_cources";

    $sql = "CREATE TABLE " . $table_name . " (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          time bigint(11) DEFAULT '0' NOT NULL,
          name VARCHAR(100) NOT NULL,
          code VARCHAR(10) NOT NULL,
          exchange_name VARCHAR(100) NOT NULL,
          exchange_code VARCHAR(10) NOT NULL,
          cource VARCHAR(100) DEFAULT '0' NOT NULL,
          koefficient float DEFAULT '0' NOT NULL,
          minsum float DEFAULT '0' NOT NULL,
          message text DEFAULT '' NOT NULL,
          UNIQUE KEY id (id)
        );";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    $table_reserv_name = $wpdb->prefix . "exchanger_valutes_reserv";
    $sql = "CREATE TABLE " . $table_reserv_name . " (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          time bigint(11) DEFAULT '0' NOT NULL,
          name VARCHAR(100) NOT NULL,
          code VARCHAR(10) NOT NULL,
          reserv float DEFAULT '0' NOT NULL,
          valute_icon text DEFAULT '' NOT NULL,
          UNIQUE KEY id (id)
        );";

    dbDelta($sql);
}
?>