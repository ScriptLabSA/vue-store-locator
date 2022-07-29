<?php

/**
 * Plugin Name:       Vue Store Locator
 * Plugin URI:        https://scriptlab.co.za
 * Description:       Super fast and friendly store locator plugin
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Script Lab
 * Author URI:        https://scriptlab.co.za
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       vue-store-locator
 */

/*
Vue Store Locator is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Vue Store Locator is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Vue Store Locator. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/


defined('ABSPATH') || exit();

if (!defined('VSL_PLUGIN_FILE')) {
    define('VSL_PLUGIN_FILE', __FILE__);
}
if (!defined('VSL_PLUGIN_BASENAME')) {
    define('VSL_PLUGIN_BASENAME', plugin_basename(VSL_PLUGIN_FILE));
}
if (!defined('VSL_PLUGIN_NAME')) {
    define('VSL_PLUGIN_NAME', trim(dirname(VSL_PLUGIN_BASENAME), '/'));
}
if (!defined('VSL_PLUGIN_DIR')) {
    define('VSL_PLUGIN_DIR', dirname(VSL_PLUGIN_FILE));
}
if (!defined('VSL_PLUGIN_URI')) {
    define('VSL_PLUGIN_URL', plugin_dir_url(VSL_PLUGIN_FILE));
}

define('VSL_APP_DIR', VSL_PLUGIN_DIR . '/app/');
define('VSL_APP_URL', VSL_PLUGIN_URL . 'app/');

if (!class_exists('VslMain')) {

    require_once(VSL_PLUGIN_DIR . '/includes/functions.php');
    require_once(VSL_PLUGIN_DIR . '/includes/classes/VslConstants.php');
    require_once(VSL_PLUGIN_DIR . '/includes/classes/VslFieldFactory.php');
    require_once(VSL_PLUGIN_DIR . '/includes/classes/VslController.php');
    require_once(VSL_PLUGIN_DIR . '/includes/classes/AppLoader.php');
    require_once(VSL_PLUGIN_DIR . '/includes/classes/VslPostType.php');
    require_once(VSL_PLUGIN_DIR . '/includes/classes/VslMain.php');
}

if(!function_exists('vslBootstrap')){
function vslBootstrap()
    {
        $instance = VslMain::getInstance();
        $instance->run();

        new VslPostType();
    }
}
vslBootstrap();
