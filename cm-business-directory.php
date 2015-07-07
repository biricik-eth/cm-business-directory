<?php
/*
  Plugin Name: CM Business Directory
  Plugin URI: http://pluins.cminds.com/cm-business-directory
  Description: Build, organize and display a local directory of business.
  Version: 1.0.0
  Author: CreativeMindsSolutions
  Author URI: http://www.cminds.com/
  Text Domain: cm-business-directory
 */

// Exit if accessed directly
if( !defined('ABSPATH') )
{
    exit;
}

/**
 * Main plugin class file.
 * What it does:
 * - checks which part of the plugin should be affected by the query frontend or backend and passes the control to the right controller
 * - manages installation
 * - manages uninstallation
 * - defines the things that should be global in the plugin scope (settings etc.)
 * @author CreativeMindsSolutions - Marcin Dudek
 */
class CMBusinessDirectory
{
    public static $calledClassName;
    protected static $instance = NULL;

    const SHORTCODE_PAGE_OPTION = 'cmbd_shortcode_page_id';

    /**
     * Main Instance
     *
     * Insures that only one instance of class exists in memory at any one
     * time. Also prevents needing to define globals all over the place.
     *
     * @since 1.0
     * @static
     * @staticvar array $instance
     * @return The one true AKRSubscribeNotifications
     */
    public static function instance()
    {
        $class = __CLASS__;
        if( !isset(self::$instance) && !( self::$instance instanceof $class ) )
        {
            self::$instance = new $class;
        }
        return self::$instance;
    }

    public function __construct()
    {

        if( empty(self::$calledClassName) )
        {
            self::$calledClassName = __CLASS__;
        }

        self::setupConstants();

        /*
         * Shared
         */
        include_once CMBD_PLUGIN_DIR . '/shared/classes/Labels.php';
        include_once CMBD_PLUGIN_DIR . '/backend/classes/Settings.php';
        include_once CMBD_PLUGIN_DIR . '/shared/cm-business-directory-shared.php';
        $CMBusinessDirectorySharedInstance = CMBusinessDirectoryShared::instance();

        if( is_admin() )
        {
            /*
             * Backend
             */
            add_action('after_setup_theme', array(__CLASS__, 'baw_theme_setup'));
            include_once CMBD_PLUGIN_DIR . '/backend/cm-business-directory-backend.php';
            $CMBusinessDirectoryBackendInstance = CMBusinessDirectoryBackend::instance();
        }
        else
        {

            include_once CMBD_PLUGIN_DIR . '/frontend/cm-business-directory-business-page-sc.php';
            include_once CMBD_PLUGIN_DIR . '/frontend/cm-business-directory-business-page.php';


            add_filter('single_template', array('CMBusinessDirectoryBusinessPage', 'get_custom_post_type_template'));

            /*
             * Frontend
             */
            include_once CMBD_PLUGIN_DIR . '/frontend/cm-business-directory-frontend.php';
            $CMBusinessDirectoryFrontendInstance = CMBusinessDirectoryFrontend::instance();
        }
    }

    /**
     * Setup plugin constants
     *
     * @access private
     * @since 1.1
     * @return void
     */
    private static function setupConstants()
    {
        /**
         * Define Plugin Version
         *
         * @since 1.0
         */
        if( !defined('CMBD_VERSION') )
        {
            define('CMBD_VERSION', '1.0.0');
        }

        /**
         * Define Plugin Directory
         *
         * @since 1.0
         */
        if( !defined('CMBD_PLUGIN_DIR') )
        {
            define('CMBD_PLUGIN_DIR', plugin_dir_path(__FILE__));
        }

        /**
         * Define Plugin URL
         *
         * @since 1.0
         */
        if( !defined('CMBD_PLUGIN_URL') )
        {
            define('CMBD_PLUGIN_URL', plugin_dir_url(__FILE__));
        }

        /**
         * Define Plugin File Name
         *
         * @since 1.0
         */
        if( !defined('CMBD_PLUGIN_FILE') )
        {
            define('CMBD_PLUGIN_FILE', __FILE__);
        }

        /**
         * Define Plugin Slug name
         *
         * @since 1.0
         */
        if( !defined('CMBD_SLUG_NAME') )
        {
            define('CMBD_SLUG_NAME', 'cm-business-directory');
        }


        /**
         * Define Plugin name
         *
         * @since 1.0
         */
        if( !defined('CMBD_NAME') )
        {
            define('CMBD_NAME', 'CM Business Directory');
        }

        /**
         * Define Plugin basename
         *
         * @since 1.0
         */
        if( !defined('CMBD_PLUGIN') )
        {
            define('CMBD_PLUGIN', plugin_basename(__FILE__));
        }

        /**
         * Define Plugin code
         *
         * @since 1.0
         */
        if( !defined('CMBD_CODE') )
        {
            define('CMBD_CODE', 'cmbd');
        }


        /**
         * Define Plugin release notes url
         *
         * @since 1.0
         */
        if( !defined('CMBD_RELEASE_NOTES') )
        {
            define('CMBD_RELEASE_NOTES', 'https://www.cminds.com/store/purchase-cm-business-directory-plugin-for-wordpress/');
        }
    }

    public static function baw_theme_setup()
    {
        add_image_size('cmbd_image', 500, 500, array('center', 'center'));
    }

    public static function _install()
    {
        global $user_ID;

        if( !get_option(self::SHORTCODE_PAGE_OPTION) )
        {
            $page['post_type'] = 'page';
            $page['post_content'] = '[cmbd_business]';
            $page['post_parent'] = 0;
            $page['post_author'] = $user_ID;
            $page['comment_status'] = 'closed';
            $page['post_status'] = 'publish';
            $page['post_title'] = CMBD_NAME;

            $pageid = wp_insert_post($page);
            add_option(self::SHORTCODE_PAGE_OPTION, $pageid);
        }
        CMBusinessDirectoryShared::install();

        return;
    }

    public static function _uninstall()
    {
        return;
    }

    public function registerAjaxFunctions()
    {
        return;
    }

    /**
     * Get localized string.
     *
     * @param string $msg
     * @return string
     */
    public static function __($msg)
    {
        return __($msg, CMBD_SLUG_NAME);
    }

    /**
     * Get business meta
     *
     * @param string $msg
     * @return string
     */
    public static function meta($id, $key, $default = null)
    {
        $result = get_post_meta($id, $key, true);
        if( $default !== null )
        {
            $result = !empty($result) ? $result : $default;
        }
        return $result;
    }

}

/**
 * The main function responsible for returning the one true plugin class
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $marcinPluginPrototype = MarcinPluginPrototypePlugin(); ?>
 *
 * @since 1.0
 * @return object The one true CM_Micropayment_Platform Instance
 */
function CMBusinessDirectoryInit()
{
    return CMBusinessDirectory::instance();
}

$CMBusinessDirectory = CMBusinessDirectoryInit();

register_activation_hook(__FILE__, array('CMBusinessDirectory', '_install'));
register_deactivation_hook(__FILE__, array('CMBusinessDirectory', '_uninstall'));
