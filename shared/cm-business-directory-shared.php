<?php

if (!defined('ABSPATH')) {
    exit;
}

class CMBusinessDirectoryShared {

    protected static $instance = NULL;
    public static $calledClassName;
    public static $lastBusinessQuery = NULL;

    const POST_TYPE = 'cm-business';
    const POST_TYPE_TAXONOMY = 'cm-business-category';

    public static function install() {
        $args = array(
            'post_type' => self::POST_TYPE,
            'meta_query' => array(
                array(
                    'key' => 'cmbd_promoted',
                    'compare' => 'NOT EXISTS',
                ),
            ),
        );
        $query = new WP_Query($args);
        $businesses = $query->get_posts();
        foreach ($businesses as $business) {
            add_post_meta($business->ID,'cmbd_promoted' , 2);
        }
    }

    public static function instance() {
        $class = __CLASS__;
        if (!isset(self::$instance) && !( self::$instance instanceof $class )) {
            self::$instance = new $class;
        }
        return self::$instance;
    }

    public function __construct() {
        if (empty(self::$calledClassName)) {
            self::$calledClassName = __CLASS__;
        }

        self::setupConstants();
        self::setupOptions();
        self::loadClasses();
        self::registerActions();
    }

    /**
     * Register the plugin's shared actions (both backend and frontend)
     */
    private static function registerActions() {
        add_action('init', array(self::$calledClassName, 'registerPostTypeAndTaxonomies'));
    }

    /**
     * Setup plugin constants
     *
     * @access private
     * @since 1.1
     * @return void
     */
    private static function setupConstants() {

    }

    /**
     * Setup plugin constants
     *
     * @access private
     * @since 1.1
     * @return void
     */
    private static function setupOptions() {
        /*
         * Adding additional options
         */
        do_action('cmbd_setup_options');
    }

    /**
     * Create taxonomies
     */
    public static function cmbd_create_taxonomies() {
        return;
    }

    /**
     * Load plugin's required classes
     *
     * @access private
     * @since 1.1
     * @return void
     */
    private static function loadClasses() {
        /*
         * Load the file with shared global functions
         */
        include_once CMBD_PLUGIN_DIR . "shared/functions.php";
    }

    public function registerShortcodes() {
        return;
    }

    public function registerFilters() {
        return;
    }

    public static function initSession() {
        if (!session_id()) {
            session_start();
        }
    }

    /**
     * Create custom post type
     */
    public static function registerPostTypeAndTaxonomies() {
        $permalink = CMBD_Settings::getOption(CMBD_Settings::OPTION_BUSINESS_PERMALINK);
        $rewrite = false;
        if (empty($permalink)) {
            $permalink = self::POST_TYPE;
        } else {
            $old_permalink = get_option('cmbd_permalink_old');
            if ($old_permalink !== $permalink) {
                update_option('cmbd_permalink_old', $permalink);
                $rewrite = true;
            }
        }
        $postType = self::POST_TYPE;
        $postTypeTaxonomy = self::POST_TYPE_TAXONOMY;

        $args = array(
            //'label'  => 'Business',
            'labels' => array(
                'name' => __('Business', CMBD_SLUG_NAME),
                'singular_name' => __('Business', CMBD_SLUG_NAME),
                'menu_name' => _x(CMBD_NAME, 'Admin menu name', CMBD_SLUG_NAME),
                'all_items' => __('Business', CMBD_SLUG_NAME),
                'add_new' => __('Add Business', CMBD_SLUG_NAME),
                'add_new_item' => __('Add New Business', CMBD_SLUG_NAME),
                'edit' => __('Edit', CMBD_SLUG_NAME),
                'edit_item' => __('Edit Business', CMBD_SLUG_NAME),
                'new_item' => __('New Business', CMBD_SLUG_NAME),
                'all_items' => __('Business', CMBD_SLUG_NAME),
                'view' => __('View Business', CMBD_SLUG_NAME),
                'view_item' => __('View Business', CMBD_SLUG_NAME),
                'search_items' => __('Search Business', CMBD_SLUG_NAME),
                'not_found' => __('No Business found', CMBD_SLUG_NAME),
                'not_found_in_trash' => __('No Business found in trash', CMBD_SLUG_NAME),
                'parent' => __('Parent Business', CMBD_SLUG_NAME)
            ),
            'description' => '',
            'map_meta_cap' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_admin_bar' => true,
            'menu_position' => 500,
            '_builtin' => false,
            'capability_type' => 'post',
            'hierarchical' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => $permalink, 'with_front' => false),
            'query_var' => true,
            'supports' => array('title', 'editor', 'excerpt', 'revisions', 'page-attributes', 'custom_fields'), //'post-thumbnails', 'thumbnail',
            'taxonomies' => array($postTypeTaxonomy));

        register_post_type($postType, $args);

        register_taxonomy($postTypeTaxonomy, $postType, array(
            'hierarchical' => true,
            'label' => __('Business Categories', CMBD_SLUG_NAME),
            'labels' => array(
                'name' => __('Business Categories', CMBD_SLUG_NAME),
                'singular_name' => __('Business Category', CMBD_SLUG_NAME),
                'menu_name' => _x('Categories', 'Admin menu name', CMBD_SLUG_NAME),
                'search_items' => __('Search Business Categories', CMBD_SLUG_NAME),
                'all_items' => __('All Business Categories', CMBD_SLUG_NAME),
                'parent_item' => __('Parent Business Category', CMBD_SLUG_NAME),
                'parent_item_colon' => __('Parent Business Category:', CMBD_SLUG_NAME),
                'edit_item' => __('Edit Business Category', CMBD_SLUG_NAME),
                'update_item' => __('Update Business Category', CMBD_SLUG_NAME),
                'add_new_item' => __('Add New Business Category', CMBD_SLUG_NAME),
                'new_item_name' => __('New Business Category Name', CMBD_SLUG_NAME)
            ),
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => $postTypeTaxonomy,
                'with_front' => false,
                'hierarchical' => true,
            )
                )
        );
        if ($rewrite) {
            flush_rewrite_rules();
        }
    }

    /**
     * Gets the list of the business
     * @param type $atts
     * @return type
     */
    public static function getBusiness_s($atts = array()) {
        $postTypes = array(CMBusinessDirectoryShared::POST_TYPE);
        $orderby = CMBD_Settings::getOption(CMBD_Settings::OPTION_BUSINESS_ORDERBY);
        $order = CMBD_Settings::getOption(CMBD_Settings::OPTION_BUSINESS_ORDER);
        if (!empty($atts['page_business'])) {
            $args = array(
                'posts_per_page' => $atts['page_business'],
                'paged' => $atts['pg'],
                'post_status' => 'publish',
                'post_type' => $postTypes,
                'meta_key' => 'cmbd_promoted',
                'orderby' => array('meta_value' => 'ASC', $orderby => $order),
                //'order' => $order,
                'suppress_filters' => true,
            );
        }
        /*
         * Don't show paused business
         */
        if (!empty($atts['paused'])) {
            $args['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key' => 'cmbd_pause_prod',
                    'value' => '0',
                ),
                array(
                    'key' => 'cmbd_pause_prod',
                    'value' => '0',
                    'compare' => 'NOT EXISTS',
                ),
            );
        }

        /*
         * Return in categories
         */
        if (!empty($atts['cmcats'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => CMBusinessDirectoryShared::POST_TYPE_TAXONOMY,
                    'terms' => $atts['cmcats'],
                    'operator' => 'IN',
                    'field' => 'slug',
                ),
            );
        }

        /*
         * Return only business with given ids
         */
        if (!empty($atts['business_ids'])) {
            $atts['business_ids'] = is_array($atts['business_ids']) ? $atts['business_ids'] : array($atts['business_ids']);
            $args['post__in'] = $atts['business_ids'];
        }

        /*
         * Return only business which title/description includes the query
         */
        if (!empty($atts['query'])) {
            $args['s'] = $atts['query'];
        }

        $query = new WP_Query($args);
        /*
         * Store the query to save info about pagination
         */
        self::$lastBusinessQuery = $query;

        $business = $query->get_posts();

        return $business;
    }

    public static function getBusiness($businessIdName) {
        if (is_numeric($businessIdName)) {
            $business = get_post($businessIdName);
            if (!$business || CMBusinessDirectoryShared::POST_TYPE !== $business->post_type)
                return null;
            return $business;
        }

        $args = array(
            'post_type' => CMBusinessDirectoryShared::POST_TYPE,
            'name' => $business,
            'numberposts' => 1
        );

        $business = get_posts($args);

        if ($business) {
            return $business[0];
        }

        return null;
    }

    public static function scanTemplateDir() {
        $template_dir = CMBD_PLUGIN_DIR . 'frontend/templates/';
        $templates = array();

        if (is_dir($template_dir)) {
            $dir = scandir($template_dir);
            foreach ($dir as $template) {
                if (!in_array($template, array(".", ".."))) {
                    if (is_dir($template_dir . $template)) {
                        if (!array_key_exists($template, $templates)) {
                            $templates[$template] = $template;
                        }
                    }
                }
            }
        }

        return $templates;
    }

}
