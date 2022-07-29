<?php

/**
 * VslController class
 * @author : Script Lab
 * Date: 2022/06/02
 * Time: 07:55 AM
 */
defined('ABSPATH') || exit();

class VslController
{
    protected static $instance = null;

    public static function start()
    {
        if (is_null(VslController::$instance)) {
            VslController::$instance = new self();
            // VslController::$instance->init();
            VslController::$instance->listen();
        }
        return VslController::$instance;
    }

    private function init()
    {
        
    }

    public function listen()
    {
        // Get Store Info
        add_action('wp_ajax_nopriv_vsl_store_info', array(VslController::$instance, 'vsl_store_info'));
        add_action('wp_ajax_vsl_store_info', array(VslController::$instance, 'vsl_store_info'));

         // Get Stores List
         add_action('wp_ajax_nopriv_vsl_stores_list', array(VslController::$instance, 'vsl_stores_list'));
         add_action('wp_ajax_vsl_stores_list', array(VslController::$instance, 'vsl_stores_list'));
    }


    public function vsl_store_info()
    {
        if (!check_ajax_referer( 'vsl_nonce', 'nonce', false )) {
            // error_log("Nonce error on Login");
            wp_send_json_error(new WP_Error("wrong_data", __('No naughty business please', VSL_PLUGIN_NAME)));
        }

        wp_send_json( 'OK');
        
    }

    public function vsl_stores_list(){
        if (!check_ajax_referer( 'vsl_nonce', 'nonce', false )) {
            // error_log("Nonce error on Login");
            wp_send_json_error(new WP_Error("wrong_data", __('No naughty business please', VSL_PLUGIN_NAME)));
        }

        $args['posts_per_page'] = 9;
        wp_send_json(  VslController::$instance->vslGetStoreList($args));
    }

    private function vslGetStoreList($args){
        $query = new WP_Query(array(
            'post_type' => 'vsl_store',
            'posts_per_page' => $args['posts_per_page'],
        ));
        $res = [];
        
        if($query->have_posts()){
            while($query->have_posts()){
                
                $query->the_post();
                $res[] = array(
                    'id' => get_the_id(),
                    'title' => get_the_title(),
                    'logo' => get_the_post_thumbnail_url( get_the_id(), 'medium' ),
                    'street_address' => get_post_meta(get_the_id(), 'store_street_address', true),
                    'suburb' => get_post_meta(get_the_id(), 'store_suburb', true),
                    'city' => get_post_meta(get_the_id(), 'store_city', true),
                    'province' => get_post_meta(get_the_id(), 'store_province', true),
                    'country' => get_post_meta(get_the_id(), 'store_country', true),
                    'phone' => get_post_meta(get_the_id(), 'store_phone', true),
                );
            }
        }
        wp_reset_query();
        return $res;
    }
    
}
