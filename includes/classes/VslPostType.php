<?php

/**
 * VmMain class
 * Main class for VM Consultation Tool
 * @author : DMNDEV
 * Date: 2021/09/02
 * Time: 07:55 AM
 */
defined( 'ABSPATH' ) || exit();

class VslPostType {
    private $postname = 'vsl_store';
    private $singular = 'Store';
    private $plural = 'Stores';
    private $fields = [];

    public $settings = null;

    public function __construct() {
        $this->settings = VslSettings::instance( $this );

        $this->add_fields( $this->settings->get_store_custom_fields() );
        $this->installHooks();
    }

    private function installHooks() {
        add_action( 'init', array( $this, 'registerStorePostType' ) );
        add_action( 'add_meta_boxes', array( $this, 'addStoreMetaBox' ), 10, 2 );
        add_action( 'save_post', array( $this, 'saveStoreFields' ), 1, 2 );
    }

    public function registerStorePostType() {
        $args = apply_filters( 'vue_stores_locator_args', $this->defaultArgs() );
        register_post_type( $this->postname, $args );
    }

    public function addStoreMetaBox() {
        add_meta_box(
            $this->postname . '-meta-box',
            __( $this->singular . ' Info' ),
            array( $this, 'renderStoreFields' ),
            $this->postname, 'normal', 'default'
        );
    }

    public function renderStoreFields( $post, $arguments ) {
        wp_nonce_field( basename( __FILE__ ), 'vsl_meta_box' );
        foreach ( $this->fields as $field ) {
            $field->setValue( get_post_meta( $post->ID, $field->getName(), true ) );
            echo $field->getHtmlField();
        }
    }

    public function add_fields( $fields ) {
        foreach ( $fields as $field ) {
            $this->add_field( $field );
        }
    }

    public function add_field( $field ) {
        $this->fields[] = new VslFieldFactroy( $field );
        // return $this;
    }

    public function saveStoreFields( $post_id, $post ) {
        // Return if the user doesn't have edit permissions.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }

        // Verify this came from the our screen and with proper authorization,
        // because save_post can be triggered at other times.
        if ( isset( $_POST['vsl_meta_box'] ) && ! wp_verify_nonce( $_POST['vsl_meta_box'], basename( __FILE__ ) ) ) {
            return $post_id;
        }

        // Don't store custom data twice
        if ( 'revision' === $post->post_type ) {
            return;
        }

        // Cycle through the $events_meta array.
        // Note, in this example we just have one item, but this is helpful if you have multiple.
        foreach ( $this->fields as $field ) :
            if ( isset( $_POST[$field->getName()] ) ) {
                $this->save_meta_fields( $post_id, $field, $_POST[$field->getName()] );
            }
        endforeach;
    }

    private function save_meta_fields( $post_id, $field, $val ) {
        if ( get_post_meta( $post_id, $field->getName(), false ) ) {
            // If the custom field already has a value, update it.
            update_post_meta( $post_id, $field->getName(), sanitize_text_field( $val ) );
        } else {
            // If the custom field doesn't have a value, add it.
            add_post_meta( $post_id, $field->getName(), sanitize_text_field( $val ) );
        }
    }

    private function defaultLabels() {
        return array(
            'name'                  => _x( $this->plural, 'Post type general name', VSL_PLUGIN_NAME ),
            'singular_name'         => _x( $this->singular, 'Post type singular name', VSL_PLUGIN_NAME ),
            'menu_name'             => _x( $this->plural, 'Admin Menu text', VSL_PLUGIN_NAME ),
            'name_admin_bar'        => _x( $this->singular, 'Add New on Toolbar', VSL_PLUGIN_NAME ),
            'add_new'               => __( 'Add New', VSL_PLUGIN_NAME ),
            'add_new_item'          => __( 'Add New ' . $this->singular, VSL_PLUGIN_NAME ),
            'new_item'              => __( 'New ' . $this->singular, VSL_PLUGIN_NAME ),
            'edit_item'             => __( 'Edit ' . $this->singular, VSL_PLUGIN_NAME ),
            'view_item'             => __( 'View ' . $this->singular, VSL_PLUGIN_NAME ),
            'all_items'             => __( 'All ' . $this->plural, VSL_PLUGIN_NAME ),
            'search_items'          => __( 'Search ' . $this->plural, VSL_PLUGIN_NAME ),
            'parent_item_colon'     => __( 'Parent ' . $this->plural . ':', VSL_PLUGIN_NAME ),
            'not_found'             => __( 'No ' . $this->plural . ' found.', VSL_PLUGIN_NAME ),
            'not_found_in_trash'    => __( 'No ' . $this->plural . ' found in Trash.', VSL_PLUGIN_NAME ),
            'filter_items_list'     => _x( 'Filter ' . $this->plural . ' list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', VSL_PLUGIN_NAME ),
            'items_list_navigation' => _x( $this->plural . ' list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', VSL_PLUGIN_NAME ),
            'items_list'            => _x( $this->plural . ' list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', VSL_PLUGIN_NAME ),
        );
    }

    private function defaultArgs() {
        return array(
            'labels'             => $this->defaultLabels(),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'store' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' )
        );
    }
}