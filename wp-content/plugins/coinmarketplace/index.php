<?php
/*
    Plugin Name: Coinmarketplace Plugin
    Version: 4.1.3
    Author: Automattic
    Author URI: https://github.com/gooddimoh/CoinMarketCap
    Text Domain: Coinmarketplace Plugin
*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require(dirname(__FILE__) . '/wp-load.php');

if (!defined('ABSPATH'))
    define('ABSPATH', dirname(__FILE__) . '/');


class coinmarketplace
{
    public function __construct()
    {
        add_action("admin_menu", "addMenu");
    }

    function activate()
    {
        flush_rewrite_rules();
    }

    function deactivate()
    {
        flush_rewrite_rules();
    }

    function uninstall()
    {
    }
}

if (class_exists('coinmarketplace')) {
    $coinmarketplace = new coinmarketplace('Check Plugin initialized!');
}

// activation
register_activation_hook(__FILE__, array($coinmarketplace, 'activate'));

// deactivation
register_deactivation_hook(__FILE__, array($coinmarketplace, 'deactivate'));

// uninstall
register_activation_hook(__FILE__, array($coinmarketplace, 'activate'));

function addMenu()
{
    add_menu_page("coinmarketcap", "coinmarketcap", 4, "coinmarketcap", "coinmarketcap");
}

function get_data_coinmarketplace()
{
    $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';
    $parameters = ['start' => '1', 'limit' => '5000', 'convert' => 'USD'];
    $headers = ['Accepts: application/json', 'X-CMC_PRO_API_KEY: b54bcf4d-1bca-4e8e-9a24-22ff2c3d462c'];
    $qs = http_build_query($parameters); // query string encode the parameters
    $request = "{$url}?{$qs}"; // create the request URL
    $curl = curl_init(); // Get cURL resource
    curl_setopt_array($curl, array(
        CURLOPT_URL => $request,            // set the request URL
        CURLOPT_HTTPHEADER => $headers,     // set the headers
        CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
    ));
    $response = curl_exec($curl); // Send the request, save the response
    $result = $response; // print json decoded response
    curl_close($curl); // Close request

    return $result;
}

function coinmarketplace($atts)
{
    function theme_enable_random_api()
    {
        register_rest_route('convert/v2', '/insert', array(
            'methods' => 'GET',
            'callback' => 'wp_json_insert_v2',
            'permission_callback' => function (WP_REST_Request $request) {
                return true;
            }
        ));

        register_rest_route('convert/v2', '/select', array(
            'methods' => 'GET',
            'callback' => 'wp_json_select_v2',
            'permission_callback' => function (WP_REST_Request $request) {
                return true;
            }
        ));
        function wp_json_convert_v2($request)
        {
            global $wpdb2;
            $wpdb2 = new wpdb('mysqluser', 'mysqluser', 'coinmarketcap', 'localhost');
            if (!empty($wpdb2->error)) {
                wp_die($wpdb2->error);
            }
            $parameters = $request->get_query_params();
            $result = $wpdb2->get_results("SELECT * FROM coinmarketcap");

            return "";
        }

        function wp_json_insert_v2($request)
        {
            global $wpdb2;
            $wpdb2 = new wpdb('mysqluser', 'mysqluser', 'coinmarketcap', 'localhost');
            if (!empty($wpdb2->error)) {
                wp_die($wpdb2->error);
            }

            $parameters = $request->get_query_params();
            $results = $wpdb2->get_results("INSERT INTO *");

            return "";
        }
    }

    add_action('init', 'theme_enable_random_api');

    // get 10 item after insert 10 items         //
    // INSERT INTO table_name (column1, column2, column3, ...) VALUES (value1, value2, value3, ...);
    // <li><a href="">0.00019325 BCH to HKD Just Now</a></li>
    // foreach ($variable as $key => $value) { } //
    return get_data_coinmarketplace();
}

add_shortcode('coinmarketplace', 'coinmarketplace');


//function Menu(){}

//function foobar_shortcode(){
//    return "date";
//}

//function GoogleApi(){
//  // https://coinmarketcap.com/ API //
//}

//function work_with_wp_bd(){
//  $data = "data";
//  return $data;
//}
