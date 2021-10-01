<?php

/**
 * @link              https://listingslab.com
 * @package           listingslab
 *
 * @wordpress-plugin
 * Version:           1.2.1
 * Plugin Name:       @Pingpong by listingslab
 * Description:       Really connect with people on WordPress
 * Plugin URI:        https://github.com/listingslab-software/listingslab-download
 * Author:            listingslab
 * Author URI:        https://listingslab.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       listingslab
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) or die( 'Really?' );
require_once 'php/Listingslab.php';

$Listingslab = Listingslab::GetInstance();
$Listingslab->InitPlugin();
