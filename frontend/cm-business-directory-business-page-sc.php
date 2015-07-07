<?php

class CMBDBusinessPageShortcodes
{

    public static function outputCategories($atts)
    {
        $output = '';
        $post = get_post();
        $atts['view'] = 'business-page';
        if( !empty($post) )
        {
            $showCategories = CMBD_Settings::getOption(CMBD_Settings::OPTION_CATEGORY_SHOWBUTTON);
            if( $showCategories )
            {

                $business = wp_get_post_terms($post->ID, CMBusinessDirectoryShared::POST_TYPE_TAXONOMY, array("fields" => "all"));
                if( $business )
                {
                    $display = true;

                    $category = CMBD_Labels::getLocalized('category');
                    $output .= '<li><i class="fa fa-calendar"></i><span class="cmbd-tags-label">' . CMBusinessDirectory::__($category) . ' </span>';

                    foreach($business as $business)
                    {


                        $output .='<a class="cmbd-cat" href="' . esc_attr(site_url('/' . CMBD_SLUG_NAME . '/')) . '?cmcats=' . esc_attr(urlencode($business->slug))
                                . '">' . esc_html($business->name) . '</a> ';
                    }
                    $output .='</li>';
                }
            }
        }

        return $output;
    }

    public static function outputBusinessPitch($atts)
    {
        $output = '';
        $post = get_post();
        $tag = !empty($atts['tag']) ? $atts['tag'] : 'div';
        $class = !empty($atts['class']) ? $atts['class'] : 'cmbd-business-pitch-label';

        if( empty($post) )
        {
            return;
        }

        $business_pitch = CMBD_Labels::getLocalized('business_pitch');
        $meta = CMBusinessDirectory::meta($post->ID, 'cmbd_business_pitch');

        if( !empty($meta) )
        {
            $output .= '<' . $tag . ' class="' . esc_attr($class) . '">' . CMBusinessDirectory::__($business_pitch) . ' ' . $meta . '</' . $tag . '>';
        }
        return $output;
    }

    public static function outputYearFounded($atts)
    {
        $output = '';
        $post = get_post();
        $tag = !empty($atts['tag']) ? $atts['tag'] : 'span';
        $class = !empty($atts['class']) ? $atts['class'] : 'cmbd-year-founded-label';

        if( empty($post) )
        {
            return;
        }
        $year_founded = CMBD_Labels::getLocalized('year_founded');
        $meta = CMBusinessDirectory::meta($post->ID, 'cmbd_year_founded');

        if( !empty($meta) && $meta != 'Not indicated' )
        {
            $output .= '<li><i class="fa fa-calendar"></i><' . $tag . ' class="' . esc_attr($class) . '">' . CMBusinessDirectory::__($year_founded) . ' ' . $meta . '</' . $tag . '></li>';
        }
        return $output;
    }

    public static function outputLocationTop($atts)
    {
        $output = '';
        $post = get_post();
        $tag = !empty($atts['tag']) ? $atts['tag'] : 'span';
        $class = !empty($atts['class']) ? $atts['class'] : 'cmbd-location-label';
        $addressL = CMBD_Labels::getLocalized('address');
        $addressL = CMBusinessDirectory::__($addressL);

        $checker = CMBD_Settings::getOption(CMBD_Settings::OPTION_ADDRESS_SHOWBUTTON);
        if( empty($checker) )
        {
            return;
        }
        if( $checker !== "top" )
        {
            return;
        }
        $address = CMBusinessDirectory::meta($post->ID, 'cmbd_add_address_field');
        if( empty($address) )
        {
            return;
        }

        $location = self::buildAddress();
        if( empty($location) )
        {
            return;
        }
        $output .= '<li><i class="fa fa-map-marker"></i><' . $tag . ' class="' . esc_attr($class) . '">' . $addressL .
                ' ' . $location . '</' . $tag . '></li>';


        return $output;
    }

    public static function outputLocationSide($atts)
    {
        $checker = CMBD_Settings::getOption(CMBD_Settings::OPTION_ADDRESS_SHOWBUTTON);
        if( empty($checker) )
        {
            return;
        }
        if( $checker !== "side" )
        {
            return;
        }
        $post = get_post();
        $address = CMBusinessDirectory::meta($post->ID, 'cmbd_add_address_field');
        if( empty($address) )
        {
            return;
        }

        $output = '';

        $class = !empty($atts['class']) ? $atts['class'] : 'cmbd-location-label';

        if( empty($post) )
        {
            return;
        }

        $location = self::buildAddress();
        if( empty($location) )
        {
            return;
        }
        $output = '<div class="product-widget cmbd-address ' . $class . '">
                    <ul class="list-unstyled">';


        $addressL = CMBD_Labels::getLocalized('address');
        $addressL = CMBusinessDirectory::__($addressL);
        $cityTownL = CMBD_Labels::getLocalized('cityTown');
        $cityTownL = CMBusinessDirectory::__($cityTownL);
        $stateCountyL = CMBD_Labels::getLocalized('stateCounty');
        $stateCountyL = CMBusinessDirectory::__($stateCountyL);
        $postalcodeL = CMBD_Labels::getLocalized('postalcode');
        $postalcodeL = CMBusinessDirectory::__($postalcodeL);
        $regionL = CMBD_Labels::getLocalized('region');
        $regionL = CMBusinessDirectory::__($regionL);
        $countryL = CMBD_Labels::getLocalized('country');
        $countryL = CMBusinessDirectory::__($countryL);

        $address = CMBusinessDirectory::meta($post->ID, 'cmbd_address');
        $output .=empty($address) ? '' : '<li>' . $addressL . ' ' . $address . '</li>';
        $cityTown = CMBusinessDirectory::meta($post->ID, 'cmbd_cityTown');
        $output .= empty($cityTown) ? '' : '<li>' . $cityTownL . ' ' . $cityTown . '</li>';
        $stateCounty = CMBusinessDirectory::meta($post->ID, 'cmbd_stateCounty');
        $output .=empty($stateCounty) ? '' : '<li>' . $stateCountyL . ' ' . $stateCounty . '</li>';
        $postalcode = CMBusinessDirectory::meta($post->ID, 'cmbd_postalcode');
        $output .= empty($postalcode) ? '' : '<li>' . $postalcodeL . ' ' . $postalcode . '</li>';
        $region = CMBusinessDirectory::meta($post->ID, 'cmbd_region');
        $output .= empty($region) ? '' : '<li>' . $regionL . ' ' . $region . '</li>';
        $country = CMBusinessDirectory::meta($post->ID, 'cmbd_country');
        $output .= empty($country) ? '' : '<li>' . $countryL . ' ' . $country . '</li>';

        $output .= '</ul></div>';


        return $output;
    }

    public static function buildAddress()
    {
        $post = get_post();
        $location = '';

        $virtual_address = CMBusinessDirectory::meta($post->ID, 'cmbd_virtual_address');
        if( $virtual_address )
        {
            return $location;
        }

        $address = CMBusinessDirectory::meta($post->ID, 'cmbd_address');
        $address = empty($address) ? '' : ' ' . $address;
        $cityTown = CMBusinessDirectory::meta($post->ID, 'cmbd_cityTown');
        $cityTown = empty($cityTown) ? '' : ' ' . $cityTown;
        $stateCounty = CMBusinessDirectory::meta($post->ID, 'cmbd_stateCounty');
        $stateCounty = empty($stateCounty) ? '' : ' ,' . $stateCounty;
        $postalcode = CMBusinessDirectory::meta($post->ID, 'cmbd_postalcode');
        $postalcode = empty($postalcode) ? '' : ' ' . $postalcode;
        $region = CMBusinessDirectory::meta($post->ID, 'cmbd_region');
        $region = empty($region) ? '' : ' ' . $region;
        $country = CMBusinessDirectory::meta($post->ID, 'cmbd_country');
        $country = empty($country) ? '' : ' ' . $country;

        $location = $address . $cityTown . $stateCounty . $postalcode . $region . $country;
        return $location;
    }

    public static function buildSmallAddress()
    {
        $post = get_post();
        $location = '';
        $address = CMBusinessDirectory::meta($post->ID, 'cmbd_address');
        $address = empty($address) ? '' : ' ' . $address;
        $cityTown = CMBusinessDirectory::meta($post->ID, 'cmbd_cityTown');
        $cityTown = empty($cityTown) ? '' : ' ' . $cityTown;
        $postalcode = CMBusinessDirectory::meta($post->ID, 'cmbd_postalcode');
        $postalcode = empty($postalcode) ? '' : ' ' . $postalcode;
        $country = CMBusinessDirectory::meta($post->ID, 'cmbd_country');
        $country = empty($country) ? '' : ' ' . $country;
        $location = $address . $cityTown . $postalcode . $country;
        return $location;
    }

    public static function outputWebUrl($atts)
    {
        $output = '';
        $post = get_post();

        if( empty($post) )
        {
            return;
        }
        $web_url = CMBD_Labels::getLocalized('web_url');
        $meta = CMBusinessDirectory::meta($post->ID, 'cmbd_web_url');
        if( !empty($meta) )
        {
            $output .= '<li><span class="dashicons dashicons-admin-site cmbd_dashicons"></span><a class="cmbd-info-box-link" href="' . esc_attr($meta) . '" target="_blank"><i class="fa fa-sbusinessap"></i>' . CMBusinessDirectory::__($web_url) . '</a></li>';
        }
        return $output;
    }

    public static function outputEmail($atts)
    {
        $output = '';
        $post = get_post();

        if( empty($post) )
        {
            return;
        }

        $emailLabel = CMBD_Labels::getLocalized('bemail');
        $emailValue = CMBusinessDirectory::meta($post->ID, 'cmbd_bemail');
        if( !empty($emailValue) )
        {
            if( strpos($emailValue, '@') !== false )
            {
                $mailto = 'mailto: ';
                $showEmail = CMBD_Settings::getOption(CMBD_Settings::OPTION_BUSINESS_PAGE_SHOW_EMAIL);
                $displayEmail = !empty($showEmail) ? ' ' . $emailValue : '';
            }
            else
            {
                $displayEmail = '';
                $mailto = '';
            }

            $output .= '<li><span class="dashicons dashicons-email-alt cmbd_dashicons"></span><a class="cmbd-info-box-link" href="' . esc_attr($mailto . $emailValue) . '" target="_blank"><i class="fa fa-sbusinessap"></i>' . esc_html(CMBusinessDirectory::__($emailLabel) . $displayEmail) . '</a></li>';
        }
        return $output;
    }



 

    public static function outputPhone($atts)
    {
        $output = '';
        $post = get_post();

        if( empty($post) )
        {
            return;
        }
        $phone = CMBD_Labels::getLocalized('phone');
        $meta = CMBusinessDirectory::meta($post->ID, 'cmbd_phone');
        if( !empty($meta) )
        {
            $output .= '<li><span class="dashicons dashicons-phone cmbd_dashicons"></span><i class="fa fa-sbusinessap"></i><div class="cmbd-info-box-phone" >' . esc_html(CMBusinessDirectory::__($phone) . ' ' . $meta) . '<div></li>';
        }
        return $output;
    }

    public static function outputPostTitle()
    {
        $output = '';
        $post = get_post();
        $tag = !empty($atts['tag']) ? $atts['tag'] : 'div';
        $class = !empty($atts['class']) ? $atts['class'] : 'cmbd-title';
        $business_name = CMBD_Labels::getLocalized('business_name');
        if( !empty($post) )
        {
            $output = '<' . $tag . ' ' . ($class ? 'class="' . esc_attr($class) . '"' : '') . '>' . esc_html(CMBusinessDirectory::__($business_name) . ' ' . $post->post_title) . '</' . $tag . '>';
        }
        return $output;
    }

    public static function outputPostContent($atts)
    {
        $output = '';
        $post = get_post();

        $tag = !empty($atts['tag']) ? $atts['tag'] : 'div';
        $class = !empty($atts['class']) ? $atts['class'] : 'cmbd-description';

        if( !empty($post->post_content) )
        {
            $output = '<' . $tag . ' ' . ($class ? 'class="' . esc_attr($class) . '"' : '') . '>' . $post->post_content . '</' . $tag . '>';
        }
        return $output;
    }

    public static function outputFeaturedImage($atts)
    {
        $output = '';
        $post = get_post();

        $tag = !empty($atts['tag']) ? $atts['tag'] : 'div';
        $class = !empty($atts['class']) ? $atts['class'] : 'cmbd-image';

        if( !empty($post) )
        {
            $output = '<' . $tag . ' ' . ($class ? 'class="' . esc_attr($class) . '"' : '') . '>' . CMBusinessDirectoryFrontend::outputImage($post, array()) . '</' . $tag . '>';
        }
        return $output;
    }

    public static function outputBackLink()
    {
        $showBackLink = CMBD_Settings::getOption(CMBD_Settings::OPTION_BUSINESS_PAGE_SHOW_BACKLINK);
        $output = '';

        if( $showBackLink )
        {


            $output .= '<a class="cmbd-backlink-wrapper" href="' . esc_attr(site_url('/' . CMBD_SLUG_NAME . '/')) . '">&laquo;&nbsp;' . CMBD_Labels::getLabel('business_page_backlink_label') . '</a>';
        }
        return $output;
    }

    public static function outputGallery()
    {
        include_once CMBD_PLUGIN_DIR . '/backend/cm-business-directory-backend.php';
        $post = get_post();
        $output = '';

        $images = CMBusinessDirectoryBusinessPage::getBusinessGallery($post->ID);

        $output = '<div id="business_images_container">
			<ul class="business_images">';
        if( !empty($images) )
        {
            foreach($images as $image)
            {
                if( !empty($image['cmbd_image'][0]) )
                {
                    $output .= '<li>';
                    $output .= '<a href="' . esc_attr($image['cmbd_image'][0]) . '" class="thickbox" rel="gallery"><img src="' . esc_attr($image['thumb'][0]) . '" /></a>';
                    $output .= '</li>';
                }
            }
        }
        $output .= '</ul>
		</div>';

        return $output;
    }

    public static function outputGallerySlider()
    {
        include_once CMBD_PLUGIN_DIR . '/backend/cm-business-directory-backend.php';
        $post = get_post();
        $output = '';
        $output_pager = '';
        $output_slider = '';

        $images = CMBusinessDirectoryBusinessPage::getBusinessGallery($post->ID);

        if( !empty($images) )
        {
            $output_slider = '';

            $count = 0;
            foreach($images as $image)
            {
                if( !empty($image['cmbd_image'][0]) )
                {
                    $output_slider .= '<div class="cmbd_product_image"> <img class="img-responsive" src="' . esc_attr($image['cmbd_image'][0]) . '" /></div>';
                }
            }
        }
        else
        {
            //  $default_image = CMBD_Settings::getOption(CMBD_Settings::OPTION_BUSINESS_DEFAULT_IMAGE);
            // $default_image = empty($default_image) ? CMBD_PLUGIN_URL . 'frontend/assets/img/black.jpg' : $default_image;
            // $output_slider .= '<img class="img-responsive" src="' . esc_attr($default_image) . '" />';
        }

        $output = $output_slider;

        return $output;
    }

    public static function outputAdditions()
    {
        $output = '';
        $post = get_post();

        if( !empty($post) )
        {
            $output = apply_filters('cmbd_additions', $output, $post->ID);
        }

        return $output;
    }

}