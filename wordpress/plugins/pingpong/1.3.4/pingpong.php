<?php

/**
 * @link              https://listingslab.com
 * @package           listingslab
 *
 * @wordpress-plugin
 * Version:           1.3.4
 * Plugin Name:       @Pingpong
 * Description:       Really connect with people on WordPress
 * Plugin URI:        https://github.com/listingslab-software/listingslab-download
 * Author:            listingslab
 * Author URI:        https://listingslab.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       listingslab
 * Domain Path:       /languages
 * Requires at least: 5.0
 * Tested up to: 5.8
 * Requires PHP: 5.3.8
*/

defined( 'ABSPATH' ) or die( 'Really?' );
require_once 'php/Pingpong.php';

$Listingslab = Listingslab::GetInstance();
$Listingslab->InitPlugin();
