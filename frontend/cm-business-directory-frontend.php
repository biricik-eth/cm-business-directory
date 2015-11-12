<?php

class CMBusinessDirectoryFrontend {

    public static $calledClassName;
    protected static $instance = NULL;
    protected static $cssPath = NULL;
    protected static $jsPath = NULL;
    protected static $viewsPath = NULL;
    protected static $viewsUrl = NULL;

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

        self::$cssPath = CMBD_PLUGIN_URL . 'frontend/assets/css/';
        self::$jsPath = CMBD_PLUGIN_URL . 'frontend/assets/js/';
        self::$viewsPath = CMBD_PLUGIN_DIR . 'frontend/views/';
        self::$viewsUrl = CMBD_PLUGIN_URL . 'frontend/views/';

            add_filter('wp_enqueue_scripts', array(self::$calledClassName, 'cmbd_enqueue_styles'));
            add_action('wp_enqueue_scripts', array(self::$calledClassName, 'cmbd_enqueue_styles'));
            add_shortcode('cmbd_business', array(self::$calledClassName, 'cmbd_business_shortcode'));
            CMBusinessDirectoryBusinessPage::cmbd_register_business_page_shortcodes();
            CMBusinessDirectoryBusinessPage::cmbd_register_actions();
    }

    public static function cmbd_enqueue_styles() {
        //Registering Scripts & Styles for the FrontEnd
        $post = get_post();
        wp_enqueue_style('dashicons');

        if ($post != null && ($post->post_type == 'cm-business' || $post->post_type == 'page' )) {
            wp_enqueue_style('cmbd-style', self::$cssPath . 'cmbd-style.css');
            wp_register_script('cmbd-functions', self::$jsPath . 'cmbd-functions.js', array('jquery'));
            //wp_enqueue_script('cmbd-functions');
        }
    }

    /**
     * Function displaying the [cmbd_business] shortcode
     *
     * @param type $atts
     * @param type $content
     * @return string
     */
    public static function cmbd_business_shortcode($atts, $content = null) {
        $defaultView = CMBD_Settings::getOption(CMBD_Settings::OPTION_BUSINESS_VIEW);
        $defaultPageBusiness = CMBD_Settings::getOption(CMBD_Settings::OPTION_BUSINESS_BUSINESS_ON_PAGE);
        $defaultShowfilter = CMBD_Settings::getOption(CMBD_Settings::OPTION_BUSINESS_SHOWFILTER);

        $showEditlink = CMBD_Settings::getOption(CMBD_Settings::OPTION_BUSINESS_SHOWEDITLINK);

        $atts = array(
            'view' => $defaultView,
            'single' => null,
            'showfilter' => $defaultShowfilter,
            'page_business' => $defaultPageBusiness,
            'cmcats' => array(),
            'business_ids' => array(),
        );


        $view = in_array($atts['view'], array('tiles', 'list', 'image-tiles', 'cube-view')) ? $atts['view'] : 'tiles';

        /*
         * Category IDS only integer, exploded by coma
         */
        if (isset($_GET["cmcats"]) && $_GET["cmcats"] != 'all') {
            $atts['cmcats'] = explode(',', $_GET['cmcats']);
        }

        /*
         * Whether or not show the filter
         */
        if (isset($_GET["showfilter"])) {
            $atts['showfilter'] = in_array($_GET["showfilter"], array('yes', 'no', true, 1, 0)) ? $_GET["showfilter"] : ($_GET["showfilter"] ? 1 : 0);
        }
        /*
         * Number of business in row
         */
        if (isset($_GET["row_business"])) {
            $atts['row_business'] = $_GET["row_business"];
        } else {
            switch ($defaultView) {
                case 'image-tiles':
                    $atts['row_business'] = CMBD_Settings::getOption(CMBD_Settings::OPTION_BUSINESS_IMAGE_TILES_BUSINESS_IN_ROW);
                    break;
                case 'directory-view':
                    self::directory_view();
                    $atts['row_business'] = CMBD_Settings::getOption(CMBD_Settings::OPTION_BUSINESS_BUSINESS_IN_ROW);
                    break;
                default:
                    break;
            }
        }

        /*
         * Number of business on page
         */
        if (isset($_GET["page_business"])) {
            $atts['page_business'] = $_GET["page_business"];
        }


        if (isset($_GET["showprice"])) {
            $atts['showprice'] = $_GET["showprice"];
        }

        if (isset($_GET["pg"])) {
            $atts['pg'] = $_GET["pg"];
        } else {
            $atts['pg'] = 1;
        }


        /*
         * Show single business only
         */
        if ($atts['single']) {
            $atts['showfilter'] = 'no';
            $atts['cmcats'] = array();
        }

        /*
         * Don't show paused
         */
        $atts['paused'] = true;

        /*
         * Explode business_ids by ","
         */
        if (!empty($atts['business_ids']) && is_string($atts['business_ids'])) {
            $atts['business_ids'] = explode(',', $atts['business_ids']);
        }

        /*
         * Search
         */
        if (isset($_GET["query"])) {
            $atts['query'] = $_GET["query"];
        }

        if ($showEditlink) {
            $atts['showeditlink'] = true;
        }

        /*
         * Get the list of business
         */
        $business = CMBusinessDirectoryShared::getBusiness_s($atts);


        //cmbd Main Container
        $output = '<div id="cmbd-container" class="cmbd-container clear ' . $view . '">';

        /*
         * Output search
         */
        $output_search = '';
        $output_search.='<form method="get">';
        $display = false;
        /*
         * Output business/business/product per page
         */
        if (CMBD_Settings::getOption(CMBD_Settings::OPTION_POST_PER_PAGE_SHOW) == true) {
            $output_search .= self::outputBusinessPerPage($atts);
            $display = true;
        }

        /*
         * Output Categories
         */
        if ((bool) $atts['showfilter'] === true) {
            $output_search .= self::outputCategories($atts);
            $display = true;
        }
        if ($display) {
            $output_search.= '<input class="cmbd-module-search-button cmbd_input_mini" type="submit" value="' . esc_attr(CMBD_Labels::getLocalized('search_submit')) . '" />';
            $output_search.='</form>';
            $output.=$output_search;
        }


        $output.='<div class="clear"></div><div class="cmbd-module-found-posts">';

        $output.='<div class="clear"></div>';
        /*
         * Output found posts
         */
        if (!empty($business) && CMBD_Settings::getOption(CMBD_Settings::OPTION_FOUND_BUSINESS)) {

            $output.= self::outputFoundPosts($atts);
        }

        $output.='</div>';

        $output.='<div class="clear"></div>';


        /*
         * Output pagination
         */
        if ($atts['page_business'] > 0 && CMBD_Settings::getOption(CMBD_Settings::OPTION_PAGINATION_TOP)) {
            $atts['pagination'] = 'top';
            $output.= self::outputPagination($atts);
        }
        $output.='<div class="clear" style="height:10px;"></div>';

        /*
         * Output Business
         */

        if (!empty($business)) {
            /* if ($defaultView == 'directory-view') {
              $output.='<div class="text-center">';
              } else { */
            $output.='<div class="cmbd-business">';
            //  }
            $k = 1;

            foreach ($business as $key => $business) {
                $atts['css_id'] = 'cmbd_business_' . $key;
                $output .= self::cmbd_display_single_business($business, $atts);

                if (!empty($atts['row_business']) && $atts['row_business'] == $k) {
                    $k = 0;
                    $output.='<div style="clear:both;height:0px;"></div>';
                }
                $k++;
            }
            $output.='</div>';
        } else {
            $output.='<div class="cmbd-no-results">';
            $output.= CMBD_Labels::getLocalized('nothing_found');
            $output.='</div>';
        }

        $output.='<div class="clear"></div>';
        /*
         * Output pagination
         */
        if ($atts['page_business'] > 0 && CMBD_Settings::getOption(CMBD_Settings::OPTION_PAGINATION_BOTTOM)) {
            $atts['pagination'] = 'bottom';
            $output.= self::outputPagination($atts);
        }

        $output.='</div>';
        //cmbd Main Container - End
        $output.='<div class="clear"></div>';

        $output = apply_filters('cmdb_after_content', $output); //filter to get content from comunity business

        return $output;
    }

    public static function directory_view() {
        ?>
        <style>
            .text-center {
                text-align: center; }

            .clearfix:after {
                content: " ";
                /* Older browser do not support empty content */
                visibility: hidden;
                display: block;
                height: 0;
                clear: both; }

            body {
                font-family: 'Varela Round', sans-serif; }

            .list-unstyled {
                padding: 0;
                margin: 0 0 15px; }
            .list-unstyled li {
                list-style: none; }

            .list-inline li {
                display: inline-block;
                margin-right: 15px; }

            .img-responsive {
                width: 100%;
                max-width: 100%;
                height: auto;
                display: block; }

            .product-container {
                width: 1024px;
                max-width: 100%;
                margin: 0 auto;
                padding-left: 15px;
                padding-right: 15px;
                box-sizing: border-box; }

            .product-title {
                font-size: 50px;
                font-weight: normal;
                margin-bottom: 15px; }

            .product-info-blocks li {
                padding: 5px;
                border: 1px solid #d9d9d9;
                color: gray;
                border-radius: 3px;
                font-size: 13px; }
            .product-info-blocks li i {
                margin-right: 5px; }

            .product-info {
                margin-top: 50px; }

            .product-aside {
                width: 30%;
                float: left; }

            .product-info-text {
                width: 70%;
                float: right;
                padding: 0 0 0 50px;
                box-sizing: border-box; }
            .product-info-text h2 {
                font-weight: normal;
                margin-top: 0; }
            .product-info-text p {
                font-size: 18px; }

            .product-widget {
                background: #2e3641;
                color: #fff;
                padding: 25px;
                margin-top: 30px; }
            .product-widget h3 {
                margin-top: 0; }
            .product-widget li {
                margin-bottom: 25px; }
            .product-widget a {
                color: #fff;
                text-decoration: none; }
            .product-widget a i {
                margin-right: 5px; }

            .product-related {
                // max-width: 150px;
                clear: none;
                float: left;
                padding: 0 0 5px;
                border-radius: 3px;
                border: 1px solid #d9d9d9;
                display: inline-block;
                margin: 0 15px 15px 0;
                position: relative;
            }
            .product-related a {
                color: gray;
                text-decoration: none; }
            .product-related h5 {
                position:absolute;
                text-align: center;
                width: 100%;
                bottom: 5px;
                z-index:2;
                margin: 5px 0 0; }
            .product-related:hover {
                background: #f4f4f4;

            }
            .cmbd_inside{
                height: 100%;
                overflow: hidden;
            }

            .cmbd-editlink {
                font-size: .7em;
                margin-left: 5px;
                position: absolute;
                right:  5px;
                bottom: 1px;
                z-index: 500;

            }
            .img_center {
                margin: 3px auto;
                display:block;
                border-radius: 3px;

            }
            @media screen and (max-width: 767px) {
                .product-aside {
                    float: right;
                    width: 100%; }

                .product-info-text {
                    width: 100%;
                    padding: 0;
                    float: left;
                    margin-bottom: 15px; } }
            @media screen and (max-width: 410px) {
                .product-related {
                    max-width: 130px;
                    margin: 0 0 15px 0; } }


        </style>
        <?php

    }

    public static function cmbd_display_single_business($business, $atts) {
        extract($atts);

        ob_start();
        include self::$viewsPath . 'single-business-' . $atts['view'] . '.phtml';
        $output = ob_get_clean();

        return $output;
    }

    public static function outputFoundPosts($atts) {
        $output = '';

        $query = CMBusinessDirectoryShared::$lastBusinessQuery;
        $output .= CMBD_Labels::getLocalized('business_found:') . ' ' . $query->found_posts;

        return $output;
    }

    public static function outputPagination($atts) {
        $output = '';


        $pagination_args = array(
            'base' => esc_url(add_query_arg('pg', '%#%')),
            'format' => '',
            'total' => CMBusinessDirectoryShared::$lastBusinessQuery->max_num_pages,
            'current' => max(1, $atts['pg']),
            'link_class' => 'cmbd-button',
        );

        $pagination = cmbd_paginate_links($pagination_args);
        $class_pagination = empty($atts['pagination']) ? '' : '-' . $atts['pagination'];
        if ($pagination) {
            $output .= '<div class="cmbd-module-pagination' . $class_pagination . '">' . CMBusinessDirectory::__('Page: ') . $pagination . '</div>';
        }

        return $output;
    }

    public static function outputEditlink($business, $atts) {
        $output = '';

        if (!empty($atts['showeditlink']) && is_user_logged_in()) {
            $output .= '<a class="cmbd-editlink" target="_blank" href="' . esc_attr(get_edit_post_link($business->ID)) . '">' . CMBusinessDirectory::__('(edit)') . '</a>';
        }

        return $output;
    }

    public static function outputCategoriesDropdown($atts) {
        $selectedId = 0;
        if (!empty($atts['cmcats']) && $atts['cmcats'][0] != 'All') {
            $selected = get_term_by('slug', $atts['cmcats'][0], CMBusinessDirectoryShared::POST_TYPE_TAXONOMY);
            $selectedId = $selected->term_id;
        }

        if (!empty($atts['cmcats']) && $atts['cmcats'][0] == 'All') {
            $selectedId = -1;
        }
        $output = '';
        $cats = get_terms(CMBusinessDirectoryShared::POST_TYPE_TAXONOMY, array('hide_empty' => 0));
        $output.='<div class="cmbd_filters"><span class="cmbd_label" id="cmbd-module-actions-top-title">' . CMBD_Labels::getLocalized('cat_filter_label') . ' </span>';
        $output .='<select class="cmbd_input_mini" name="cmcats">';
        $current = !empty($atts['cmcats']) ? $atts['cmcats'][0] : '';
        $output.= '<option ' . selected($current, 'all', 0) . ' value="all"> ' . CMBD_Labels::getLocalized('filter_all') . '</option>';

        foreach ($cats as $cat) {
            $output .= '<option ' . selected($current, $cat->slug, 0) . ' value="' . esc_attr($cat->slug) . '">' . $cat->name . '</option>';
        }
        $output .='</select>';
        $output .='<div class="clear clearfix"></div></div>';

        return $output;
    }

    public static function outputCategoriesAsTags($atts) {
        /*
         *  Getting categories
         */
        $cats = get_terms(CMBusinessDirectoryShared::POST_TYPE_TAXONOMY, array('hide_empty' => 0));


        $current = !empty($atts['cmcats']) ? $atts['cmcats'][0] : '';
        $output = '<div class="cmbd_filters2"><span class="cmbd_label" id="cmbd-module-actions-top-title">' . CMBD_Labels::getLocalized('cat_filter_label') . ' </span>';
        $output .= '<div class="cmbd_tags">';
        $output.= '<input ' . checked($current, 'all', 0) . ' class="display_none" type="radio" id="all" name="cmcats" value="all">'
                . '<label class="cmbd-button" for="all">' . CMBD_Labels::getLocalized('filter_all') . '</label>';
        if (!empty($cats)) {
            foreach ($cats as $cat) {
                $output.= '<input ' . checked($current, $cat->slug, 0) . ' class="display_none" type="radio" id="' . esc_attr($cat->slug) . '" name="cmcats" value="' . esc_attr($cat->slug) . '">'
                        . '<label class="cmbd-button" for="' . esc_attr($cat->slug) . '">' . $cat->name . '</label>';
            }
        }
        $output.='</div>';
        $output .='<div class="clear clearfix"></div></div>';

        return $output;
    }

    public static function outputCategories($atts) {
        $output = '';

        if (CMBD_Settings::getOption(CMBD_Settings::OPTION_CATEGORY_SHOW_AS) == 'tags') {
            $output .= self::outputCategoriesAsTags($atts);
        } else {
            $output .= self::outputCategoriesDropdown($atts);
        }
        return $output;
    }

    public static function outputBusinessPerPage($atts) {
        global $post;
        if (empty($post)) {
            return;
        }

        $output = '';
        $current = (int) (empty($atts['page_business'])) ? 10 : $atts['page_business'];
        $output.='<div class="cmbd_filters"><span class="cmbd_label" id="cmbd-module-actions-top-title">' . CMBD_Labels::getLocalized('post_per_page_lebel') . ' </span>';
        $i = 10;

        $output .='<select class="cmbd_input_xmini" name="page_business">';

        $selected = 1;
        $output.= '<option ' . selected($current, 1, 0) . 'value="1">1</option>';

        if ($current == 2) {
            $selected = 0;
            $output.= '<option ' . selected($current, 2, 0) . 'value="2">2</option>';
        }
        $output.= '<option '.selected($current, 3, 0).'value="3">3</option>';

        if ($current == 4 && $selected) {
            $selected = 0;
            $output.= '<option '.selected($current, 4, 0).'value="' . esc_attr($current) . '">' . $current . '</option>';
        }
        $output.= '<option '.selected($current, 5, 0).'value="5">5</option>';

        if ($current < 10 && $current % 10 && $selected) {
            $selected = 0;
            $output.= '<option '.selected(1,1,0).' value="' . esc_attr($current) . '">' . $current . '</option>';
        }
        for ($i; $i < 101; $i+=10) {
            if ($current < $i && $current % 10 && $selected) {
                $selected = 0;
                $output.= '<option '.selected(1,1,0).' value="' . esc_attr($current) . '">' . $current . '</option>';
            }
            $output.= '<option '.selected($current,$i,0).'value="' . esc_attr($i) . '">' . $i . '</option>';
        }
        $output .='</select><div class="clear clearfix"></div></div>';


        return $output;
    }

    public static function outputImage($business, $atts) {
        $output = '';
        $postId = $business->ID;
        $image = CMBusinessDirectoryBusinessPage::getBusinessGallery($postId);

        if (!empty($image)) {
            $imageUrl = $image[0]['cmbd_image'][0];
            $output .= '<img class="default-image" src="' . esc_attr($imageUrl) . '">';
        } else {
            $view = (!empty($atts['view']) && in_array($atts['view'], array('tiles', 'list', 'image-tiles'))) ? $atts['view'] : 'tiles';
            $default_image = CMBD_Settings::getOption(CMBD_Settings::OPTION_BUSINESS_DEFAULT_IMAGE);
            $default_image = empty($default_image) ? CMBD_PLUGIN_URL . 'frontend/assets/img/black.jpg' : $default_image;
            $output .= '<img class="default-image" src="' . esc_attr($default_image) . '">';
        }
        return $output;
    }
}
