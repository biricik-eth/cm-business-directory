<?php
//include_once CMBD_PLUGIN_DIR . '/shared/cm-business-directory-shared.php';
include_once CMBD_PLUGIN_DIR . '/frontend/cm-business-directory-business-page.php';

if( !defined('ABSPATH') )
{
    exit;
}

class CMBusinessDirectoryBackend
{
    public static $calledClassName;
    protected static $instance = NULL;
    protected static $cssPath = NULL;
    protected static $jsPath = NULL;
    protected static $viewsPath = NULL;

    const PAGE_YEARLY_OFFER = 'https://www.cminds.com/store/cm-wordpress-plugins-yearly-membership/';

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
        self::setupConstans();

        self::$cssPath = CMBD_PLUGIN_URL . 'backend/assets/css/';
        self::$jsPath = CMBD_PLUGIN_URL . 'backend/assets/js/';
        self::$viewsPath = CMBD_PLUGIN_DIR . 'backend/views/';

        add_action('admin_enqueue_scripts', array(self::$calledClassName, 'cmbd_enqeue_scripts'));
        add_action('admin_menu', array(self::$calledClassName, 'cmbd_admin_menu'));

        add_filter('manage_edit-' . CMBusinessDirectoryShared::POST_TYPE . '_columns', array(self::$calledClassName, 'editScreenColumns'));
//        add_filter('manage_' . CMBusinessDirectoryShared::POST_TYPE . '_pages_custom_column', array(self::$calledClassName, 'editScreenColumnsContent'), 10, 2);
        add_filter('manage_' . CMBusinessDirectoryShared::POST_TYPE . '_posts_custom_column', array(self::$calledClassName, 'editScreenColumnsContent'), 10, 2);
        add_filter('manage_edit-' . CMBusinessDirectoryShared::POST_TYPE . '_sortable_columns', array(self::$calledClassName, 'editScreenSortableColumns'));

        add_action('restrict_manage_posts', array(self::$calledClassName, 'editScreenCustomFilters'));
        add_filter('parse_query', array(self::$calledClassName, 'editScreenCustomPostTypeRequest'));

        add_action('add_meta_boxes', array(self::$calledClassName, 'addMetaBox'));
        add_action('save_post', array(self::$calledClassName, 'saveMetabox'));
        add_filter('CMBD_admin_settings', array(self::$calledClassName, 'addAdminSettings'));

        add_image_size('cm-admin-thumb', 100, 100);
        $permalink_structure = get_option('permalink_structure');
        if( empty($permalink_structure) )
        {
            echo '<div id="message" class="error"><p><strong>Permalinks are disabled, CM Business Directory can work incorrectly</strong></p></div>';
        }
    }

    public static function postPublished()
    {
        do_action('save_post');
    }

    public static function setupConstans()
    {
        if( !defined('CMBD_BACKUP_FILENAME') )
        {
            define('CMBD_BACKUP_FILENAME', 'exportData.json');
        }
    }

    public static function cmbd_enqeue_scripts()
    {
        $currentScreen = get_current_screen();

        if( $currentScreen->id == 'cm-business_page_cm-business-directory-settings' OR $currentScreen->id == 'cm-business' )
        {
            $path = self::$jsPath . 'cmbd_admin_scripts.js';
            wp_enqueue_script('cmbd-admin-functions', $path, array('jquery', 'jquery-ui-datepicker', 'wp-color-picker', 'media-upload', 'thickbox'));

            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');

            wp_enqueue_style('wp-color-picker');
            wp_enqueue_style('thickbox');
            wp_enqueue_style('cmbd-admin', self::$cssPath . 'cmbd_admin.css');
            wp_register_script('cmbd-functions', self::$jsPath . 'cmbd-functions.js', array('jquery'));
        }
    }

    public static function cmbd_admin_menu()
    {
        add_submenu_page('edit.php?post_type=' . CMBusinessDirectoryShared::POST_TYPE, 'Settings', __('Settings', CMBD_SLUG_NAME), 'manage_options', CMBD_SLUG_NAME . '-settings', array(self::$calledClassName, 'cmbd_render_page'));
        add_submenu_page('edit.php?post_type=' . CMBusinessDirectoryShared::POST_TYPE, 'About', __('About', CMBD_SLUG_NAME), 'manage_options', CMBD_SLUG_NAME . '-about', array(self::$calledClassName, 'cmbd_render_page'));
        add_submenu_page('edit.php?post_type=' . CMBusinessDirectoryShared::POST_TYPE, 'Pro', __('Pro', CMBD_SLUG_NAME), 'manage_options', CMBD_SLUG_NAME . '-pro', array(self::$calledClassName, 'cmbd_render_page'));
        add_submenu_page('edit.php?post_type=' . CMBusinessDirectoryShared::POST_TYPE, 'User Guide', __('User Guide', CMBD_SLUG_NAME), 'manage_options', CMBD_SLUG_NAME . '-userguide', array(self::$calledClassName, 'cmbd_render_page'));
        global $submenu;
        $pages = $submenu['edit.php?post_type=' . CMBusinessDirectoryShared::POST_TYPE];
        foreach($pages as $key => $page)
        {
            $searchvalue = $page[2];
            if( $searchvalue === CMBD_SLUG_NAME . '-userguide' )
            {
                $submenu['edit.php?post_type=' . CMBusinessDirectoryShared::POST_TYPE][$key][2] = 'edit.php?post_type=cm-business&page=cm-business-directory-userguide&noheader=1';
            }
        }
        if( current_user_can('manage_options') )
        {
            $submenu['edit.php?post_type=' . CMBusinessDirectoryShared::POST_TYPE][999] = array('Yearly membership offer', 'manage_options', self::PAGE_YEARLY_OFFER);
            add_action('admin_footer', array(__CLASS__, 'admin_head'));
        }
    }

    public static function admin_head()
    {
        echo '<style type="text/css">
        		ul.wp-submenu li a[href*="cm-wordpress-plugins-yearly-membership"] {color: white !important;}
                        a[href*="cm-wordpress-plugins-yearly-membership"]:before {font-size: 16px; vertical-align: middle; padding-right: 5px; color: #d54e21;
    				content: "\f487";
				    display: inline-block;
					-webkit-font-smoothing: antialiased;
					font: normal 16px/1 \'dashicons\';
    			}
    			ul.wp-submenu li a[href*="cm-wordpress-plugins-yearly-membership"]:before {vertical-align: bottom !important;}
        	</style>';
    }

    public static function cmbd_render_page()
    {
        global $wpdb;
        $pageId = filter_input(INPUT_GET, 'page');

        switch($pageId)
        {
            case CMBD_SLUG_NAME . '-settings':
                {
                    wp_enqueue_style('jquery-ui-tabs-css', self::$cssPath . 'jquery-ui-tabs.css');
                    wp_enqueue_script('jquery-ui-tabs');

                    $params = apply_filters('CMBD_admin_settings', array());
                    extract($params);

                    ob_start();
                    include_once self::$viewsPath . 'settings.phtml';
                    $content = ob_get_contents();
                    ob_end_clean();
                    break;
                }
            case CMBD_SLUG_NAME . '-about':
                {
                    ob_start();
                    include_once self::$viewsPath . 'about.phtml';
                    $content = ob_get_contents();
                    ob_end_clean();
                    break;
                }
            case CMBD_SLUG_NAME . '-pro':
                {
                    ob_start();
                    include_once self::$viewsPath . 'pro.phtml';
                    $content = ob_get_contents();
                    ob_end_clean();
                    break;
                }
            case CMBD_SLUG_NAME . '-userguide':
                {
                    wp_redirect('https://www.cminds.com/store/purchase-cm-business-directory-plugin-for-wordpress/');
                    exit();
                    $content = '';
                }
        }

        self::displayAdminPage($content);
    }

    public static function downloadImage($url, $parent_post, $img_title)
    {
        $image_data = file_get_contents($url);
        $upload_dir = wp_upload_dir();
        $filename = basename($url);
        if( wp_mkdir_p($upload_dir['path']) ) $file = $upload_dir['path'] . '/' . $filename;
        else $file = $upload_dir['basedir'] . '/' . $filename;
        file_put_contents($file, $image_data);
        $guid = $upload_dir['path'] . '/' . $filename;
// $upload_dir = wp_upload_dir();
        /* $attachment = array(
          'post_title' => $img_title,
          'post_status' => 'inherit',
          'post_parent' => $parent_post,
          'post_type' => 'attachment',
          'comment_status' => 'closed',
          'guid' => $guid,
          );  // to insert post */
// Check image file type
        $wp_filetype = wp_check_filetype($filename, null);
        $atache_path = $upload_dir['subdir'] . '/' . $filename;
        $attachment = array(
            'guid'           => $upload_dir['url'] . '/' . $filename,
            'post_mime_type' => $wp_filetype['type'],
            'post_title'     => $img_title,
            'post_content'   => '',
            'post_status'    => 'inherit',
        );
//$attach_id = wp_insert_attachment($attachment, $guid, $parent_post);
//$attach_id = wp_insert_post($attachment);
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
// Generate the metadata for the attachment, and update the database record.
        $attach_id = wp_insert_attachment($attachment, $atache_path, $parent_post);
        $attach_data = wp_generate_attachment_metadata($attach_id, $guid);
        wp_update_attachment_metadata($attach_id, $attach_data);
        return $attach_id;
    }

    public static function addAdminSettings($params = array())
    {
        $tmp_post = self::_isPost();
        if( self::_isPost() )
        {
            if( isset($_POST['cmbd_nonce']) && wp_verify_nonce($_POST['cmbd_nonce'], 'dg@3vasdHHT4$bsdcs_SDdSS34637') )
            {
                $params = CMBD_Settings::processPostRequest();
            }


// Labels
            $labels = CMBD_Labels::getLabels();
            foreach($labels as $labelKey => $label)
            {
                if( isset($_POST['label_' . $labelKey]) )
                {
                    CMBD_Labels::setLabel($labelKey, stripslashes($_POST['label_' . $labelKey]));
                }
            }

            if( filter_input(INPUT_POST, 'cmbd_pluginCleanup') )
            {
                self::_cleanup();
            }
            if( filter_input(INPUT_POST, 'cmbd_pluginSetDefault') )
            {
                self::_setdefault();
            }
        }

        return $params;
    }

    public static function displayAdminPage($content)
    {
        $nav = self::getAdminNav();
        include_once self::$viewsPath . 'template.phtml';
    }

    public static function getAdminNav()
    {
        global $self, $parent_file, $submenu_file, $plugin_page, $typenow, $submenu;
        ob_start();
        $submenus = array();

        $menuBusiness = 'edit.php?post_type=' . CMBusinessDirectoryShared::POST_TYPE;

        if( isset($submenu[$menuBusiness]) )
        {
            $thisMenu = $submenu[$menuBusiness];

            foreach($thisMenu as $sub_business)
            {
                $slug = $sub_business[2];

// Handle current for post_type=post|page|foo pages, which won't match $self.
                $self_type = !empty($typenow) ? $self . '?post_type=' . $typenow : 'nothing';

                $isCurrent = FALSE;
                $subpageUrl = get_admin_url('', 'edit.php?post_type=' . CMBusinessDirectoryShared::POST_TYPE . '&page=' . $slug);

                if(
                        (!isset($plugin_page) && $self == $slug ) ||
                        ( isset($plugin_page) && $plugin_page == $slug && ( $menuBusiness == $self_type || $menuBusiness == $self || file_exists($menuBusiness) === false ) )
                )
                {
                    $isCurrent = TRUE;
                }

                $url = (strpos($slug, '.php') !== false || strpos($slug, 'http://') !== false || strpos($slug, 'https://') !== false ) ? $slug : $subpageUrl;
                $url = ( strpos($slug, $typenow . '_') === false) ? $url : $subpageUrl;
                $submenus[] = array(
                    'link'    => $url,
                    'title'   => $sub_business[0],
                    'current' => $isCurrent
                );
            }
            include self::$viewsPath . 'nav.phtml';
        }
        $nav = ob_get_contents();
        ob_end_clean();
        return $nav;
    }

    /**
     * Adds the meta box container.
     */
    public static function addMetaBox($post_type)
    {
        /*
         * Limit meta box to custom post type
         */
        $post_types = array(CMBusinessDirectoryShared::POST_TYPE);
        if( in_array($post_type, $post_types) )
        {
            add_meta_box(
                    CMBD_SLUG_NAME . '-custom-fields'
                    , __('CM Business Directory Custom Fields', CMBD_SLUG_NAME)
                    , array(self::$calledClassName, 'renderMetaboxContent')
                    , $post_type
                    , 'advanced'
                    , 'high'
            );
        }
    }

    /**
     * Save the meta when the post is saved.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public static function saveMetabox($post_id)
    {
        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */

// Check if our nonce is set.
        if( !isset($_POST[CMBD_SLUG_NAME . '-custom-fields-nonce']) )
        {
            return $post_id;
        }

        $nonce = $_POST[CMBD_SLUG_NAME . '-custom-fields-nonce'];

// Verify that the nonce is valid.
        if( !wp_verify_nonce($nonce, CMBD_SLUG_NAME . '-custom-fields') )
        {
            return $post_id;
        }

// If this is an autosave, our form has not been submitted,
//     so we don't want to do anything.
        if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        {
            return $post_id;
        }

// Check the user's permissions.
        if( !current_user_can('edit_post', $post_id) )
        {
            return $post_id;
        }

        $postData = filter_input_array(INPUT_POST);
        $options_names = array_filter(array_keys($postData), array(self::$calledClassName, 'getTheOptionNames'));

        foreach($options_names as $option_name)
        {
            if( !isset($postData[$option_name]) )
            {
                update_post_meta($post_id, $option_name, '');
            }
            else
            {
                $optionValue = $postData[$option_name];
                if( $option_name == 'cmbd_web_url' )
                {
                    if( !(filter_var($optionValue, FILTER_VALIDATE_URL) AND preg_match('/^https?:/', $optionValue)) )
                    {
                        $optionValue = '';
                    }
                }
                update_post_meta($post_id, $option_name, $optionValue);
            }
        }
    }

    public static function getTheOptionNames($k)
    {
        return strpos($k, 'cmbd_') === 0;
    }

    /**
     * Render Meta Box content.
     *
     * @param WP_Post $post The post object.
     */
    public static function renderMetaboxContent($post)
    {
        include_once self::$viewsPath . 'metabox.phtml';
    }

    public static function editScreenColumns($columns)
    {
        $baseColumns = $columns;
        $columns = array(
            'cb'                                                        => '<input type="checkbox" />',
            'thumbnail'                                                 => __('Logo'),
            'title'                                                     => __('Business name'),
            'status'                                                    => __('Status'),
            'taxonomy-' . CMBusinessDirectoryShared::POST_TYPE_TAXONOMY => $baseColumns['taxonomy-' . CMBusinessDirectoryShared::POST_TYPE_TAXONOMY],
            'date'                                                      => __('Date'),
        );

        return $columns;
    }

    public static function editScreenSortableColumns($columns)
    {
        $columns = array(
            'title'         => 'title',
            'status'        => 'status',
            'categories'    => 'categories',
            'purchase_link' => 'purchase_link',
            'info_link'     => 'info_link',
            'date'          => 'date',
        );

        return $columns;
    }

    public static function editScreenCustomFilters()
    {
        global $typenow;

        if( $typenow != CMBusinessDirectoryShared::POST_TYPE )
        {
            return;
        }

        $filters = get_object_taxonomies($typenow);

        foreach($filters as $tax_slug)
        {
            $tax_obj = get_taxonomy($tax_slug);
            $selected = isset($_GET[$tax_slug]) ? $_GET[$tax_slug] : false;

            wp_dropdown_categories(array(
                'show_option_all' => __('Show All ' . $tax_obj->label),
                'taxonomy'        => $tax_slug,
                'name'            => $tax_obj->name,
                'orderby'         => 'name',
                'selected'        => $selected,
                'hierarchical'    => $tax_obj->hierarchical,
                'show_count'      => false,
                'hide_empty'      => true
            ));
        }
    }

    public static function editScreenCustomPostTypeRequest($query)
    {
        global $pagenow, $typenow;

        if( $typenow != CMBusinessDirectoryShared::POST_TYPE )
        {
            return;
        }

        if( 'edit.php' == $pagenow )
        {
            $filters = get_object_taxonomies($typenow);
            foreach($filters as $tax_slug)
            {
                $var = &$query->query_vars[$tax_slug];
                if( !empty($var) )
                { //was isset, now !empty because $var = 0 during searching business on admin side
                    $business = get_term_by('id', $var, $tax_slug);
                    $var = $business->slug;
                }
            }
        }
    }

    public static function editScreenColumnsContent($column, $post_id)
    {

        switch($column)
        {
            case 'thumbnail' :
                $url = CMBusinessDirectoryBusinessPage::getBusinessGallery($post_id);
                if( empty($url) )
                {
                    return;
                }
                $url = $url[0]['thumb'][0];

                echo '<img width="100" height="100" src="' . esc_attr($url) . '">';

                // the_post_thumbnail('cm-admin-thumb', $attr);
                break;
            case 'purchase_link' :
                $link = get_post_meta($post_id, 'cmbd_purchase', true);
                echo '<a href="' . esc_attr($link) . '" target="_blank">' . $link . '</a>';
                break;
            case 'info_link' :
                $link = get_post_meta($post_id, 'cmbd_link', true);
                echo '<a href="' . esc_attr($link) . '" target="_blank">' . $link . '</a>';
                break;
            case 'status' :
                $paused = get_post_meta($post_id, 'cmbd_pause_prod', true);

                $status = $paused == "1" ? CMBD_Labels::getLocalized('Paused') : CMBD_Labels::getLocalized('Published');
                echo $status;
                break;
        }
    }

    protected static function _isPost()
    {
        return strtolower($_SERVER['REQUEST_METHOD']) == 'post';
    }

    /**
     * Function set all setings to default.
     */
    protected static function _setdefault()
    {
        $options = CMBD_Settings::getOptionsConfig();
        foreach($options as $name => $optionConfig)
        {
            update_option($name, $optionConfig['default']);
        }
        $options = array();
        $options = apply_filters('cmbd_set_default_option', $options);
        foreach($options as $name => $value)
        {
            update_option($name, $value);
        }
    }

    /**
     * Function cleans up the plugin, removing the business, resetting the options etc.
     *
     * @return string
     */
    protected static function _cleanup($force = true)
    {
        $directoryBusiness = get_posts(array(
            'post_type'                  => CMBusinessDirectoryShared::POST_TYPE,
            'numberposts'                => -1,
            'update_post_meta_cache'     => false,
            'update_post_business_cache' => false,
            'suppress_filters'           => false
        ));

        /*
         * Remove the business
         */
        foreach($directoryBusiness as $post)
        {
            wp_delete_post($post->ID, $force);
        }

// Delete all custom business for this taxonomy
        $business = get_terms(CMBusinessDirectoryShared::POST_TYPE_TAXONOMY);
        foreach($business as $business)
        {
            wp_delete_business($business->ID, CMBusinessDirectoryShared::POST_TYPE_TAXONOMY);
        }

        //CMBD_Settings::deleteAllOptions();
    }

    public static function getBusinessGalleryImageIds($post_id)
    {
        $image_id = CMBusinessDirectory::meta($post_id, 'cmbd_business_gallery_id');
        if( !empty($image_id) )
        {
            $image = wp_get_attachment_image_src($image_id, 'cmbd_image');
            $image = array('html' => $image[0], 'id' => $image_id, 'width' => $image[1], 'height' => $image[2]);
        }
        else
        {
            $image = null;
        }
        return $image;
    }

}