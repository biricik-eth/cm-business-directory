<?php

include_once CMBD_PLUGIN_DIR . '/frontend/classes/Template.php';

class CMBusinessDirectoryBusinessPageView extends CMBDBusinessPageTemplate {

    private $post;
    private $path;

    public function __construct() {
        $this->post = get_post();
    }

    public function content() {
        /*
         * Output custom CSS
         */
        $output = '';
        do_action('cmbd_before_business_page_content');
        $output .= CMBusinessDirectoryBusinessPage::loadTemplateView('content', array(
                    'post' => get_post(),
                    'post_meta' => (object) get_post_meta($this->post->ID),
                        ), true);
        return $output;
    }

    public static function cmbd_enqueue_template_scripts($path) {
        wp_enqueue_style('cmbd-styles', $path . 'assets/css/styles.css');
    }

}
