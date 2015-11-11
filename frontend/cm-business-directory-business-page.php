<?php

class CMBusinessDirectoryBusinessPage {

    public static $calledClassName;
    protected static $instance = NULL;
    protected static $cssPath = NULL;
    protected static $jsPath = NULL;
    protected static $viewsPath = NULL;
    private static $template_name = 'cm_ecommerce';
    private static $path;

    public static function instance() {
        $class = __CLASS__;
        if (!isset(self::$instance) && !( self::$instance instanceof $class )) {
            self::$instance = new $class;
        }
        return self::$instance;
    }

    public static function cmbd_add_meta() {
        if (get_post() != null) {
            echo '<link rel="canonical" href="' . esc_attr(get_permalink(get_post()->ID)) . '">';
        }
    }

    public static function cmbd_get_template_name() {
        $post = get_post();
        $name = CMBusinessDirectory::meta($post->ID, 'cmbd_business_page_template');
        if (empty($name) OR $name == 0) {
            $name = CMBD_Settings::getOption(CMBD_Settings::OPTION_BUSINESS_PAGE_TEMPLATE);
        }
        return $name;
    }

    public static function cmbd_register_actions() {
        // Remove rel-canonical by deefault
        remove_action('wp_head', 'rel_canonical');

        // Add own rel-canonical
        add_action('wp_head', array(__CLASS__, 'cmbd_add_meta'));
    }

    public static function cmbd_enqueue_templates_scripts() {
        //Registering Scripts & Styles for the FrontEnd tempalte
        wp_enqueue_style('cmbd-template-style', self::$cssPath . 'assets/css/style.css');



        // include the javascript
        wp_enqueue_script('thickbox', null, array('jquery'));

        // include the thickbox styles
        wp_enqueue_style('thickbox.css', '/' . WPINC . '/js/thickbox/thickbox.css', null, '1.0');

        // include template specified filters
        if (method_exists('CMBusinessDirectoryBusinessPageView', 'cmbd_enqueue_template_scripts')) {
            $template_name = self::cmbd_get_template_name();
            self::loadTemplateClass($template_name);
            CMBusinessDirectoryBusinessPageView::cmbd_enqueue_template_scripts(CMBD_PLUGIN_URL . 'frontend/templates/' . $template_name . '/');
        }
    }

    public static function cmbd_register_template_filters() {
        add_filter('wp_enqueue_scripts', array(__CLASS__, 'cmbd_enqueue_templates_scripts'));
    }

    public static function cmbd_register_business_page_shortcodes() {
        $shortcodes = array(
            'cmbd_categories' => array('CMBDBusinessPageShortcodes', 'outputCategories'),
            'cmbd_post_title' => array('CMBDBusinessPageShortcodes', 'outputPostTitle'),
            'cmbd_post_content' => array('CMBDBusinessPageShortcodes', 'outputPostContent'),
            'cmbd_featured_image' => array('CMBDBusinessPageShortcodes', 'outputFeaturedImage'),
            'cmbd_back_to_index_link' => array('CMBDBusinessPageShortcodes', 'outputBackLink'),
            'cmbd_gallery' => array('CMBDBusinessPageShortcodes', 'outputGallery'),
            'cmbd_image' => array('CMBDBusinessPageShortcodes', 'outputGallerySlider'),
            'cmbd_location_top' => array('CMBDBusinessPageShortcodes', 'outputLocationTop'),
            'cmbd_location_side' => array('CMBDBusinessPageShortcodes', 'outputLocationSide'),
            'cmbd_web_url' => array('CMBDBusinessPageShortcodes', 'outputWebUrl'),
            'cmbd_email' => array('CMBDBusinessPageShortcodes', 'outputEmail'),
            'cmbd_business_pitch' => array('CMBDBusinessPageShortcodes', 'outputBusinessPitch'),
            'cmbd_year_founded' => array('CMBDBusinessPageShortcodes', 'outputYearFounded'),
            'cmbd_phone' => array('CMBDBusinessPageShortcodes', 'outputPhone'),
            'cmbd_additions' => array('CMBDBusinessPageShortcodes', 'outputAdditions'),
        );
        foreach ($shortcodes as $k => $v) {
            add_shortcode($k, $v);
        }
    }

    public static function get_custom_post_type_template($single_template) {
        global $post;

        if ($post->post_type == 'cm-business') {

            self::cmbd_register_template_filters();
            self::$template_name = self::cmbd_get_template_name();
            $found = self::locateTemplate(array('views/business-page-view'));
            $wp_template_dir = get_template_directory();
            if (empty($found)) { // Load default template
                self::$path = CMBD_PLUGIN_DIR . 'frontend/templates/' . self::$template_name . '/';
                self::$cssPath = CMBD_PLUGIN_URL . 'frontend/templates/' . self::$template_name . '/';
            } else {
                self::$path = $wp_template_dir . '/CMBD/';
                self::$cssPath = get_template_directory_uri() . '/CMBD/';
            }

            self::loadTemplateClass(self::$template_name);

            // Load template
            $single_template = self::$path . 'views/business-page.phtml';
        }
        return $single_template;
    }

    /**
     * Load html for given view
     *
     * @param unknown $view
     * @param string $html
     * @return string
     */
    public static function loadTemplateView($file, $data = null, $html = false) {
        $file = 'business-page-' . $file;

        ob_start();
        if (!empty($data))
            extract($data);
        include_once self::$path . 'views/' . $file . '.phtml';
        $content = ob_get_clean();

        if ($html) {
            return $content;
        } else {
            echo $content;
        }
    }

    public static function loadTemplateClass($template_name) {

        include_once CMBD_PLUGIN_DIR . 'frontend/templates/' . $template_name . '/business-page-view.php';
    }

    /**
     * Locate the template file, either in the current theme or the public views directory
     *
     * @static
     * @param array $possibilities
     * @param string $default
     * @return string
     */
    protected static function locateTemplate($possibilities, $default = '') {
        /*
         *  check if the theme has an override for the template
         */
        $theme_overrides = array();
        foreach ($possibilities as $p) {
            $theme_overrides[] = 'CMBD/' . $p . '.php';
        }
        if ($found = locate_template($theme_overrides, FALSE)) {
            return $found;
        }

        /*
         *  check for it in the public directory
         */
        foreach ($possibilities as $p) {
            if (file_exists(CMBD_PLUGIN_DIR . 'frontend/templates/' . $p . '/business-page-view.php')) {
                // echo " file exists ";
                return CMBD_PLUGIN_DIR . 'frontend/templates/' . $p . '/business-page-view.php';
            }
        }

        /*
         *  we don't have it
         */
        return $default;
    }

    public static function getBusinessGallery($post_id) {
        $image_id = CMBusinessDirectory::meta($post_id, 'cmbd_business_gallery_id');
        $images = array();

        if (!empty($image_id)) {

            $images[] = array(
                'thumb' => wp_get_attachment_image_src($image_id),
                'large' => wp_get_attachment_image_src($image_id, 'large'),
                'cmbd_image' => wp_get_attachment_image_src($image_id, 'cmbd_image')
            );
        }

        return $images;
    }

}
