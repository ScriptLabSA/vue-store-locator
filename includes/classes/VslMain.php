<?php

/**
 * VslMain class
 * Main class for Vue Store Locator
 * @author : ScriptLab
 * Date: 2021/09/02
 * Time: 07:55 AM
 */
defined('ABSPATH') || exit();


final class VslMain
{
    protected static $instance = null;

    public function __construct()
    {
        register_activation_hook(VSL_PLUGIN_FILE, array($this, 'onActivate'));
        register_deactivation_hook(VSL_PLUGIN_FILE, array($this, 'onDeactivate'));
        register_uninstall_hook(VSL_PLUGIN_FILE, array(__CLASS__, 'onUninstall'));
    }

    public static function getInstance()
    {
        if (is_null(VslMain::$instance)) {
            VslMain::$instance = new self();
        }
        return VslMain::$instance;
    }

    //On Plugin Activation Actions
    public function onActivate()
    {
        do_action('activate_vsl_action');
        !get_option('vsl_installed') ? add_option('vsl_installed', true) : false;
        $this->creteFolderTemporalPDF();
    }

    //On Plugin Deactivation Actions
    public function onDeactivate()
    {
        do_action('deactivate_vsl_action');
    }

    //On Plugin Uninstall Actions
    static public function onUninstall()
    {
        do_action('uninstall_vsl_action');
        delete_option("vsl_installed");
    }

    public function run()
    {
        VslMain::$instance->installHooks();
    }

    private function installHooks(){

    }
}
