<?php

class CMBD_Settings
{
    const TYPE_BOOL = 'bool';
    const TYPE_INT = 'int';
    const TYPE_STRING = 'string';
    const TYPE_COLOR = 'color';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_RADIO = 'radio';
    const TYPE_SELECT = 'select';
    const TYPE_MULTISELECT = 'multiselect';
    const TYPE_CSV_LINE = 'csv_line';
    const TYPE_FILEUPLOAD = 'fileupload';
    const TYPE_HIDDEN = 'hidden';
    const SETTINGS_PREFIX = 'cmbd';
    /*
     * OPTIONS
     */
    const OPTION_BUSINESS_VIEW = 'cmbd_business_view';
    const OPTION_BUSINESS_BUSINESS_ON_PAGE = 'cmbd_business_business_on_page';
    const OPTION_BUSINESS_SHOWFILTER = 'cmbd_business_showfilter';
    const OPTION_BUSINESS_SHOWDETAILS = 'cmbd_business_showdetails';
    const OPTION_BUSINESS_SHOWEDITLINK = 'cmbd_business_showeditlink';
    const OPTION_BUSINESS_PERMALINK = 'cmbd_business_permalink';
    const OPTION_BUSINESS_IMAGE_WARNING = 'cmbd_business_image_warning';
    const OPTION_BUSINESS_DEFAULT_COUNTRY = 'cmbd_business_default_country';
    const OPTION_BUSINESS_DEFAULT_YEAR = 'cmbd_business_default_year';
    const OPTION_BUSINESS_DEFAULT_IMAGE = 'cmbd_business_default_image';
    const OPTION_BUSINESS_TWITTER_FALLOWERS = 'cmbd_business_twitter_fallowers';
    const OPTION_BUSINESS_ORDERBY = 'cmbd_business_orderby';
    const OPTION_BUSINESS_ORDER = 'cmbd_business_order';
    const OPTION_BUTTONS_TARGET_BLANK = 'cmbd_buttons_target_blank';
    const OPTION_BUTTONS_BUSINES_PITCH_IMG = 'cmbd_buttons_business_pitch_img';
    const OPTION_PAGINATION_TOP = 'cmbd_pagination_top';
    const OPTION_PAGINATION_BOTTOM = 'cmbd_pagination_bottom';
    const OPTION_FOUND_BUSINESS = 'cmbd_found_business';
    const OPTION_CATEGORY_SHOWBUTTON = 'cmbd_category_button_show';
    const OPTION_ADDRESS_SHOWBUTTON = 'cmbd_address_button_show';
    const OPTION_CATEGORY_SHOW_AS = 'cmbd_category_show_as';
    const OPTION_POST_PER_PAGE_SHOW = 'cmbd_post_per_page_show';
    const OPTION_BUSINESS_WIDTH = 'cmbd_business_width';
    const OPTION_BUSINESS_HEIGHT = 'cmbd_business_height';
    const OPTION_BUSINESS_BUSINESS_IN_ROW = 'cmbd_business_business_in_row';
    const OPTION_BUSINESS_TILES_SHOW_CAT = 'cmbd_business_tiles_show_cat';
    const OPTION_BUSINESS_HEIGHT_LIST = 'cmbd_business_height_list';
    const OPTION_BUSINESS_IMAGE_TILES_WIDTH = 'cmbd_business_image_tiles_width';
    const OPTION_BUSINESS_IMAGE_TILES_HEIGHT = 'cmbd_business_image_tiles_height';
    const OPTION_BUSINESS_IMAGE_TILES_BUSINESS_IN_ROW = 'cmbd_business_image_tiles_business_in_row';
    // Templates
    // const OPTION_BUSINESS_PAGE_USE_TEMPLATE = 'cmbd_business_use_page_template';
    const OPTION_BUSINESS_PAGE_TEMPLATE = 'cmbd_business_page_template';
    const OPTION_BUSINESS_PAGE_SHOW_BACKLINK = 'cmbd_business_page_show_backlink';
    const OPTION_BUSINESS_PAGE_SHOW_EMAIL = 'cmbd_business_page_show_email';


    /*
     * OPTIONS - END
     */
    const ACCESS_EVERYONE = 0;
    const ACCESS_USERS = 1;
    const ACCESS_ROLE = 2;
    const EDIT_MODE_DISALLOWED = 0;
    const EDIT_MODE_WITHIN_HOUR = 1;
    const EDIT_MODE_WITHIN_DAY = 2;
    const EDIT_MODE_ANYTIME = 3;

    public static $categories = array(
        'general'       => 'General',
        'business'      => 'Business Page',
        'index'         => 'Business Directory',
        'appearance'    => 'Appearance',
        'labels'        => 'Labels',
    );
    public static $subcategories = array(
        'general'       => array(
            'general' => 'General Options',
        ),
        'business'      => array(
            'templates' => 'Business Page Defaults',
        ),
        'index'         => array(
            'shortcode' => 'Business Index Header'
        ),
        'appearance'    => array(
            'business'    => 'View Settings',
        ),
    );

    public static function getOptionsConfig()
    {

        return apply_filters('cmbd_options_config', array(
            self::OPTION_BUSINESS_VIEW                        => array(
                'type'        => self::TYPE_HIDDEN,
                'default'     => 'directory-view',
                'category'    => 'index',
                'subcategory' => 'shortcode',
                'title'       => 'Default view',
                'desc'        => 'Select the view for the Business Directory',
            ),
            self::OPTION_BUSINESS_ORDERBY                     => array(
                'type'        => self::TYPE_HIDDEN,
                'default'     => 'menu_order',
                'category'    => 'index',
                'subcategory' => 'shortcode',
                'title'       => 'Business order by',
                'desc'        => 'Select how the businesses in the Business Directory index page should be ordered.',
                'options'     => array('menu_order'    => 'Menu Order',
                    'post_title'    => 'Business Name',
                    'post_date'     => 'Date Added',
                    'post_modified' => 'Last Modified Date')
            ),
            self::OPTION_BUSINESS_ORDER                       => array(
                'type'        => self::TYPE_HIDDEN,
                'default'     => 'DESC',
                'category'    => 'index',
                'subcategory' => 'shortcode',
                'title'       => 'Order in ascending or descending order',
                'desc'        => 'Select whether the businesses in the directory index should be ordered in ascending or descending order.',
                'options'     => array('DESC' => 'DESC',
                    'ASC'  => 'ASC'),
            ),
            self::OPTION_BUSINESS_BUSINESS_ON_PAGE            => array(
                'type'        => self::TYPE_HIDDEN,
                'default'     => 15,
                'category'    => 'index',
                'subcategory' => 'shortcode',
                'title'       => 'Number of businesses on page',
                'desc'        => 'Set the number of items on a single directory page. Use <code>-1</code> to turn off the pagination.',
            ),
            self::OPTION_PAGINATION_TOP                       => array(
                'type'        => self::TYPE_BOOL,
                'default'     => false,
                'category'    => 'index',
                'subcategory' => 'shortcode',
                'title'       => 'Show pagination on top',
                'desc'        => 'Controls whether to show the pagination at the top of the directory index page.',
            ),
            self::OPTION_PAGINATION_BOTTOM                    => array(
                'type'        => self::TYPE_BOOL,
                'default'     => true,
                'category'    => 'index',
                'subcategory' => 'shortcode',
                'title'       => 'Show pagination on bottom',
                'desc'        => 'Controls whether to show the pagination at the bottom of the directory index page.',
            ),
            self::OPTION_FOUND_BUSINESS                       => array(
                'type'        => self::TYPE_BOOL,
                'default'     => true,
                'category'    => 'index',
                'subcategory' => 'shortcode',
                'title'       => 'Show the number of businesses found',
                'desc'        => 'Controls whether the number of the businesses found should be displayed.',
            ),
            self::OPTION_CATEGORY_SHOW_AS                     => array(
                'type'        => self::TYPE_SELECT,
                'default'     => 'tags',
                'category'    => 'index',
                'subcategory' => 'shortcode',
                'title'       => 'Display categories as',
                'desc'        => 'Select how the categories should be presented.',
                'options'     => array('tags' => 'Tags', 'dropdown' => 'Dropdown')
            ),
            self::OPTION_POST_PER_PAGE_SHOW                   => array(
                'type'        => self::TYPE_BOOL,
                'default'     => FALSE,
                'category'    => 'index',
                'subcategory' => 'shortcode',
                'title'       => 'SHow items per page selection',
                'desc'        => 'Should the number of items shown in the business directory index page be editable?'
            ),
            self::OPTION_BUSINESS_SHOWFILTER                  => array(
                'type'        => self::TYPE_BOOL,
                'default'     => TRUE,
                'category'    => 'index',
                'subcategory' => 'shortcode',
                'title'       => 'Show categories',
                'desc'        => 'Should the categories be displayed on the business directory index page?',
            ),
            // General
            self::OPTION_BUSINESS_PERMALINK                   => array(
                'type'        => self::TYPE_HIDDEN,
                'default'     => 'cm-business',
                'category'    => 'general',
                'subcategory' => 'general',
                'title'       => 'Business permalink',
                'desc'        => 'Change the permalink base (default: https://yoursite.com/cm-business/your-business)',
            ),
            self::OPTION_BUSINESS_IMAGE_WARNING               => array(
                'type'        => self::TYPE_BOOL,
                'default'     => TRUE,
                'category'    => 'general',
                'subcategory' => 'general',
                'title'       => 'Show a warning for image size',
                'desc'        => 'Show a warning if the size of the image is bigger than 500x500 ?',
            ),
            self::OPTION_BUTTONS_TARGET_BLANK                 => array(
                'type'        => self::TYPE_BOOL,
                'default'     => FALSE,
                'category'    => 'index',
                'subcategory' => 'shortcode',
                'title'       => 'Open each business page on a new tab',
                'desc'        => 'Should the business page be opened on a new tab? (target="_blank")',
            ),
            self::OPTION_BUTTONS_BUSINES_PITCH_IMG            => array(
                'type'        => self::TYPE_BOOL,
                'default'     => FALSE,
                'category'    => 'index',
                'subcategory' => 'shortcode',
                'title'       => 'Business Pitch as image title',
                'desc'        => 'Should the business pitch be shown as the title of the image? (Business Directory View/Tiles View only!)',
            ),
            self::OPTION_BUSINESS_SHOWDETAILS                 => array(
                'type'        => self::TYPE_BOOL,
                'default'     => FALSE,
                'category'    => 'appearance',
                'subcategory' => 'image_tiles',
                'title'       => 'Show details button',
                'desc'        => 'Should the business box in the directory index include the "Details" button?',
            ),
            // General
            self::OPTION_BUSINESS_SHOWEDITLINK                => array(
                'type'        => self::TYPE_BOOL,
                'default'     => TRUE,
                'category'    => 'general',
                'subcategory' => 'general',
                'title'       => 'Show admin edit link',
                'desc'        => 'Should the admin/editor edit link be shown near the business name? (for logged in users with "edit_posts" capability only)',
            ),
            // General
            self::OPTION_BUSINESS_DEFAULT_COUNTRY             => array(
                'type'        => self::TYPE_SELECT,
                'default'     => 224,
                'category'    => 'general',
                'subcategory' => 'general',
                'title'       => 'Default country',
                'desc'        => 'Choose the default business country',
                //'options' => array('top' => 'On Top', 'side' => 'On Side'),
                'options'     => array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra",
                    "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina",
                    "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan",
                    "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus",
                    "Belgium", "Belize", "Benin", "Bermuda", "Bhutan",
                    "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil",
                    "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi",
                    "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands",
                    "Central African Republic", "Chad", "Chile", "China", "Christmas Island",
                    "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the",
                    "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba",
                    "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica",
                    "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador",
                    "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)",
                    "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan",
                    "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia",
                    "Georgia", "Germany", "Ghana", "Gibraltar", "Greece",
                    "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala",
                    "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands",
                    "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland",
                    "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel",
                    "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan",
                    "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait",
                    "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho",
                    "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg",
                    "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia",
                    "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique",
                    "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of",
                    "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco",
                    "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal",
                    "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua",
                    "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands",
                    "Norway", "Oman", "Pakistan", "Palau", "Panama",
                    "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn",
                    "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion",
                    "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia",
                    "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal",
                    "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia",
                    "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain",
                    "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname",
                    "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic",
                    "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo",
                    "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey",
                    "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine",
                    "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay",
                    "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)",
                    "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia",
                    "Zambia", "Zimbabwe"),
            ),
            self::OPTION_BUSINESS_DEFAULT_YEAR                => array(
                'type'        => self::TYPE_SELECT,
                'default'     => 65,
                'category'    => 'general',
                'subcategory' => 'general',
                'title'       => 'Default year founded',
                'desc'        => 'Choose the default year when the business was established',
                //'options' => array('top' => 'On Top', 'side' => 'On Side'),
                'options'     => array('Not indicated', 1950, 1951, 1952, 1953, 1954, 1955, 1956, 1957, 1958, 1959
                    , 1960, 1961, 1962, 1963, 1964, 1965, 1966, 1967, 1968, 1969
                    , 1970, 1971, 1972, 1973, 1974, 1975, 1976, 1977, 1978, 1979
                    , 1980, 1981, 1982, 1983, 1984, 1985, 1986, 1987, 1988, 1989
                    , 1990, 1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998, 1999
                    , 2000, 2001, 2002, 2003, 2004, 2005, 2006, 2007, 2008, 2009
                    , 2010, 2011, 2012, 2013, 2014, 2015, 2016, 2017),
            ),
            self::OPTION_BUSINESS_DEFAULT_IMAGE               => array(
                'type'        => self::TYPE_FILEUPLOAD,
                'default'     => '',
                'category'    => 'general',
                'subcategory' => 'general',
                'title'       => 'Choose the default business image',
                'desc'        => 'The default image will be used only when a business doesn\'t have its own image',
            ),
            // Appearance
            self::OPTION_BUSINESS_WIDTH                       => array(
                'type'        => self::TYPE_STRING,
                'default'     => '280px',
                'category'    => 'appearance',
                'subcategory' => 'business',
                'title'       => 'Business box width',
                'desc'        => 'Set the width of the business box in the directory index. Can be any valid CSS "width" eg: 20%, auto, 50em. (numeric values will be considered as number of "px")',
            ),
            // Appearance
            self::OPTION_BUSINESS_HEIGHT                      => array(
                'type'        => self::TYPE_STRING,
                'default'     => '335px',
                'category'    => 'appearance',
                'subcategory' => 'business',
                'title'       => 'Business box height',
                'desc'        => 'Set the height of the business box in the directory index. Can be any valid CSS "width" eg: 20%, auto, 50em. (numeric values will be considered as number of "px")',
            ),
            // Appearance
            self::OPTION_BUSINESS_BUSINESS_IN_ROW             => array(
                'type'        => self::TYPE_STRING,
                'default'     => 3,
                'category'    => 'appearance',
                'subcategory' => 'business',
                'title'       => 'Businesses in a row',
                'desc'        => 'Set the number of businesses in a row shown in the directory index. Use -1 to fit as many businesses in a row as possible',
            ),
            // Appearance
            self::OPTION_BUSINESS_IMAGE_TILES_WIDTH           => array(
                'type'        => self::TYPE_STRING,
                'default'     => '280px',
                'category'    => 'appearance',
                'subcategory' => 'image_tiles',
                'title'       => 'Business tile width',
                'desc'        => 'Set the width of the tile in the directory index. Can be any valid CSS "width" eg: 20%, auto, 50em. (numeric values will be considered as number of "px")',
            ),
            // Appearance
            self::OPTION_BUSINESS_IMAGE_TILES_HEIGHT          => array(
                'type'        => self::TYPE_STRING,
                'default'     => '320px',
                'category'    => 'appearance',
                'subcategory' => 'image_tiles',
                'title'       => 'Business tile height',
                'desc'        => 'Set the height of the tile in the directory index. Can be any valid CSS "width" eg: 20%, auto, 50em. (numeric values will be considered as number of "px")',
            ),
            // Appearance
            self::OPTION_BUSINESS_IMAGE_TILES_BUSINESS_IN_ROW => array(
                'type'        => self::TYPE_STRING,
                'default'     => 3,
                'category'    => 'appearance',
                'subcategory' => 'image_tiles',
                'title'       => 'Businesses in a row',
                'desc'        => 'Set the number of businesses in a row in the directory index. Use -1 to fit as many businesses in a row as possible',
            ),
            // Templates
            self::OPTION_BUSINESS_PAGE_TEMPLATE               => array(
                'type'        => self::TYPE_SELECT,
                'default'     => 'cm_default',
                'category'    => 'business',
                'subcategory' => 'templates',
                'title'       => 'Default template',
                'desc'        => 'Select the default template for the business page',
                'options'     => CMBusinessDirectoryShared::scanTemplateDir(),
            ),
            // Templates
            self::OPTION_BUSINESS_PAGE_SHOW_BACKLINK => array(
                'type'        => self::TYPE_BOOL,
                'default'     => true,
                'category'    => 'business',
                'subcategory' => 'templates',
                'title'       => 'Show back to directory index link',
                'desc'        => 'Whether to display the back link to the business directory index on the business page.',
            ),
            self::OPTION_CATEGORY_SHOWBUTTON         => array(
                'type'        => self::TYPE_BOOL,
                'default'     => true,
                'category'    => 'business',
                'subcategory' => 'templates',
                'title'       => 'Show categories',
                'desc'        => 'Controls whether the Categories should be displayed on the business page.',
            ),
            // Templates
            self::OPTION_BUSINESS_PAGE_SHOW_EMAIL    => array(
                'type'        => self::TYPE_BOOL,
                'default'     => false,
                'category'    => 'business',
                'subcategory' => 'templates',
                'title'       => 'Show social and email section',
                'desc'        => 'Whether to show the email and social links section on the business page.',
            ),
            self::OPTION_ADDRESS_SHOWBUTTON          => array(
                'type'        => self::TYPE_SELECT,
                'default'     => 'top',
                'category'    => 'business',
                'subcategory' => 'templates',
                'title'       => 'Show address',
                'desc'        => 'Controls where the buisness address should be displayed on the business page.',
                'options'     => array('top' => 'On Top', 'side' => 'On Side'),
            ),
                )
        );
    }

    public static function getOptionsConfigByCategory($category, $subcategory = null)
    {
        $options = self::getOptionsConfig();
        return array_filter($options, function($val) use ($category, $subcategory)
        {
            if( $val['category'] == $category )
            {
                return (is_null($subcategory) OR $val['subcategory'] == $subcategory);
            }
        });
    }

    public static function getOptionConfig($name)
    {
        $options = self::getOptionsConfig();
        if( isset($options[$name]) )
        {
            return $options[$name];
        }
    }

    public static function setOption($name, $value)
    {
        $options = self::getOptionsConfig();
        $checker = isset($_POST[$name]) ? (strpos($name, self::SETTINGS_PREFIX) === 0) ? 1 : 0 : 0;
        if( isset($options[$name]) )
        {
            $field = $options[$name];
            $old = get_option($name);
            if( is_array($old) OR is_object($old) OR strlen((string) $old) > 0 )
            {
                update_option($name, self::cast($value, $field['type']));
            }
            else
            {
                $result = update_option($name, self::cast($value, $field['type']));
            }
        }
        if( empty($options[$name]) && $checker )
        {
            update_option($name, $value);
        }
    }

    public static function deleteAllOptions()
    {
        $params = array();
        $options = self::getOptionsConfig();
        foreach($options as $name => $optionConfig)
        {
            self::deleteOption($name);
        }
        return $params;
    }

    public static function deleteOption($name)
    {
        $options = self::getOptionsConfig();
        if( isset($options[$name]) )
        {
            delete_option($name);
        }
    }

    public static function getOption($name)
    {
        $options = self::getOptionsConfig();
        if( isset($options[$name]) )
        {
            $field = $options[$name];
            $defaultValue = (isset($field['default']) ? $field['default'] : null);
            return self::cast(get_option($name, $defaultValue), $field['type']);
        }
    }

    public static function getCategories()
    {
        $categories = array();
        $options = self::getOptionsConfig();
        foreach($options as $option)
        {
            $categories[] = $option['category'];
        }
        return $categories;
    }

    public static function getSubcategories($category)
    {
        $subcategories = array();
        $options = self::getOptionsConfig();
        foreach($options as $option)
        {
            if( $option['category'] == $category )
            {
                $subcategories[] = $option['subcategory'];
            }
        }
        return $subcategories;
    }

    protected static function boolval($val)
    {
        return (boolean) $val;
    }

    protected static function arrayval($val)
    {
        if( is_array($val) ) return $val;
        else if( is_object($val) ) return (array) $val;
        else return array();
    }

    protected static function cast($val, $type)
    {
        if( $type == self::TYPE_BOOL )
        {
            return (intval($val) ? 1 : 0);
        }
        else
        {
            $castFunction = $type . 'val';
            if( function_exists($castFunction) )
            {
                return call_user_func($castFunction, $val);
            }
            else if( method_exists(__CLASS__, $castFunction) )
            {
                return call_user_func(array(__CLASS__, $castFunction), $val);
            }
            else
            {
                return $val;
            }
        }
    }

    protected static function csv_lineval($value)
    {
        if( !is_array($value) ) $value = explode(',', $value);
        return $value;
    }

    public static function processPostRequest()
    {
        $params = array();
        foreach($_POST as $name => $optionConfig)
        {
            $params[$name] = $_POST[$name];
            self::setOption($name, $_POST[$name]);
        }

        return $params;
    }

    public static function userId($userId = null)
    {
        if( empty($userId) ) $userId = get_current_user_id();
        return $userId;
    }

    public static function isLoggedIn($userId = null)
    {
        $userId = self::userId($userId);
        return !empty($userId);
    }

    public static function getRolesOptions()
    {
        global $wp_roles;
        $result = array();
        if( !empty($wp_roles) AND is_array($wp_roles->roles) ) foreach($wp_roles->roles as $name => $role)
            {
                $result[$name] = $role['name'];
            }
        return $result;
    }

    public static function canReportSpam($userId = null)
    {
        return (self::getOption(self::OPTION_SPAM_REPORTING_ENABLED) AND ( self::getOption(self::OPTION_SPAM_REPORTING_GUESTS) OR self::isLoggedIn($userId)));
    }

    public static function getPagesOptions()
    {
        $pages = get_pages(array('number' => 100));
        $result = array(null => '--');
        foreach($pages as $page)
        {
            $result[$page->ID] = $page->post_title;
        }
        return $result;
    }

    public static function areAttachmentsAllowed()
    {
        $ext = self::getOption(self::OPTION_ATTACHMENTS_FILE_EXTENSIONS);
        return (!empty($ext) AND ( self::getOption(self::OPTION_ATTACHMENTS_ANSWERS_ALLOW) OR self::getOption(self::OPTION_ATTACHMENTS_QUESTIONS_ALLOW)));
    }

    public static function getLoginPageURL($returnURL = null)
    {
        if( empty($returnURL) )
        {
            $returnURL = get_permalink();
        }
        if( $customURL = CMBD_Settings::getOption(CMBD_Settings::OPTION_LOGIN_PAGE_LINK_URL) )
        {
            return esc_url(add_query_arg(array('redirect_to' => urlencode($returnURL)), $customURL));
        }
        else
        {
            return wp_login_url($returnURL);
        }
    }

}