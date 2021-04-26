<?php
/*
Plugin Name: BR Real Estate
Plugin URI: bodrosh.ru
Description: Тестовое задание от  onepix.net
Version: 1.0.0
Author: Bodrosh
Author URI: mailto:bodrovshum@gmail.com
License: ...
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'BR_RESTATE_DIR', plugin_dir_path(__FILE__ ) );
define('BR_RESTATE_URL', plugin_dir_url( __FILE__ ) );

require_once BR_RESTATE_DIR . 'includes/post-types.php';
require_once BR_RESTATE_DIR . 'includes/functions.php';