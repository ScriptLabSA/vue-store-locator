<?php

/**
 * AppLoader class
 * Handles Page Template loaing for App initialization.
 * @author : DMNDEV
 * Date: 2021/09/02
 * Time: 09:00 AM
 */
defined('ABSPATH') || exit();

class AppLoader
{
    /**
     * A reference to an instance of this class.
     */
    private static $instance;

    /**
     * The array of templates that this plugin tracks.
     */
    protected $templates;

    protected $jsModules = ['dataManager', 'components/StoreViewer', 'app'];

    /**
     * Returns an instance of this class. 
     */
    public static function get_instance()
    {
        if (null == self::$instance) {
            self::$instance = new AppLoader();
        }

        return self::$instance;
    }

    /**
     * Initializes the plugin by setting filters and administration functions.
     */
    private function __construct()
    {
        $this->templates = array();

        add_filter('theme_page_templates', array($this, 'add_new_template'));

        // Add a filter to the save post to inject out template into the page cache
        add_filter('wp_insert_post_data', array($this, 'register_project_templates'));

        // Add a filter to the template include to determine if the page has our 
        // template assigned and return it's path
        add_filter('template_include', array($this, 'view_project_template'));

        add_action('wp_enqueue_scripts', array($this, 'vm_tool_load_app_scripts'));

        add_filter('script_loader_tag', array($this, 'add_type_attribute'), 10, 3);

        // Add your templates to this array.
        $this->templates = array('/app/app.php' => 'Vue Store Locator');
    }

    /**
     * Adds our template to the page dropdown for v4.7+
     *
     */
    public function add_new_template($posts_templates)
    {
        $posts_templates = array_merge($posts_templates, $this->templates);
        return $posts_templates;
    }

    /**
     * Adds our template to the pages cache in order to trick WordPress
     * into thinking the template file exists where it doens't really exist.
     */
    public function register_project_templates($atts)
    {

        // Create the key used for the themes cache
        $cache_key = 'page_templates-' . md5(get_theme_root() . '/' . get_stylesheet());
        // Retrieve the cache list. 
        // If it doesn't exist, or it's empty prepare an array
        $templates = wp_get_theme()->get_page_templates();
        if (empty($templates)) {
            $templates = array();
        }

        // New cache, therefore remove the old one
        wp_cache_delete($cache_key, 'themes');

        // Now add our template to the list of templates by merging our templates
        // with the existing templates array from the cache.
        $templates = array_merge($templates, $this->templates);

        // Add the modified cache to allow WordPress to pick it up for listing
        // available templates
        wp_cache_add($cache_key, $templates, 'themes', 1800);

        return $atts;
    }

    /**
     * Checks if the template is assigned to the page
     */
    public function view_project_template($template)
    {

        // Get global post
        global $post;

        // Return template if post is empty
        if (!$post) {
            return $template;
        }

        // Return default template if we don't have a custom one defined
        if (!isset($this->templates[get_post_meta(
            $post->ID,
            '_wp_page_template',
            true
        )])) {
            return $template;
        }

        $file = VSL_PLUGIN_DIR . get_post_meta(
            $post->ID,
            '_wp_page_template',
            true
        );

        // Just to be safe, we check if the file exist first
        if (file_exists($file)) {
            return $file;
        } else {
            echo $file;
        }

        // Return template
        return $template;
    }

    /**
     * Load app plugins and scripts
     */
    public function vm_tool_load_app_scripts()
    {
        $temp = get_post_meta(get_the_id(), '_wp_page_template', true);
        if (isset($this->templates[$temp])) {

            // Fix for Last OceanWP version (DELETE LATER)
            // wp_enqueue_script('jquery');

            // Load Plugins
            //wp_enqueue_script('qs', 'https://unpkg.com/qs/dist/qs.js', array(), null, true);
            //wp_enqueue_script('axios', 'https://unpkg.com/axios/dist/axios.min.js', array(), null, true);
            //wp_enqueue_script('vuex', 'https://unpkg.com/vuex', array(), null, true);
            //wp_enqueue_script('vue', 'https://cdn.jsdelivr.net/npm/vue@2.6.14', array(), null, true);
            //wp_enqueue_script('vue-router', 'https://unpkg.com/vue-router/dist/vue-router.js', array(), null, true);

            wp_enqueue_script('qs', VSL_APP_URL . 'js/temporal/qs.js', array(), null, true);
            wp_enqueue_script('axios', VSL_APP_URL . 'js/temporal/axios.min.js', array(), null, true);
            // wp_enqueue_script('vuex', VSL_APP_URL . 'js/temporal/vuex.js', array(), null, true);
            wp_enqueue_script('vue', VSL_APP_URL . 'js/temporal/vuedev.js', array(), null, true);
            // wp_enqueue_script('vue-router', VSL_APP_URL . 'js/temporal/vue-router.js', array(), null, true);

            // Load App JS Modules
            foreach (AppLoader::$instance->jsModules as $module) {
                // create app version codes
                $version = date("ymd-Gis", filemtime(VSL_APP_DIR . 'js/' . $module . '.js'));
                // Enqueue Modules
                wp_enqueue_script('vsl-' . $module, VSL_APP_URL . 'js/' . $module . '.js', array(), $version, true);
            }

            // Load App Stylesheet
            $app_css_ver = date("ymd-Gis", filemtime(VSL_APP_DIR . 'css/app.css'));

            wp_register_style('vslappcss',   VSL_APP_URL . 'css/app.css', array(),  $app_css_ver);

            //wp_enqueue_style('vm-animate', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css', array(), null);
            // wp_register_style('vm-animate', VSL_APP_URL . 'js/temporal/animate.min.css', array(), null);

            wp_enqueue_style('vslappcss');

            global $wp;
            // Load JS Helper
            wp_localize_script(
                'vsl-app',
                'vsl_js_object',
                array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce("vsl_nonce"),
                    'appRootUrl' => VSL_APP_URL,            
                    'currentAppUrl' => home_url($wp->request),
                    'siteUrl' => get_site_url(),
                )
            );
        }
    }

    /**
     * Set type Module to App js tags
     */
    public function add_type_attribute($tag, $handle, $src)
    {
        if (!in_array(str_replace('vsl-', '', $handle), AppLoader::$instance->jsModules)) {
            return $tag;
        }
        $tag = '<script type="module" src="' . esc_url($src) . '" id="' . $handle . '"></script>';
        return $tag;
    }
}
