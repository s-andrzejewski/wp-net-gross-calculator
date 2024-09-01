<?php
namespace NetGrossCalc;

class Assets
{
    public static function init()
    {
        load_plugin_textdomain(
            NGC_TDOMAIN,
            false,
            NGC_NAME . '/languages/'
        );

        add_action('wp_enqueue_scripts', [__CLASS__, 'loadAssets']);
    }

    public static function loadAssets()
    {
        $cssPath = NGC_PATH . '/dist/styles/style.min.css';
        $cssDate = date('ymd-Gis', filemtime($cssPath));
        $cssUrl = NGC_URL . '/dist/styles/style.min.css';

        wp_register_style('net-gross-calc-style', $cssUrl, [], $cssDate, 'all');
        wp_enqueue_style('net-gross-calc-style');

        $jsPath = NGC_PATH . '/dist/scripts/script.min.js';
        $jsDate = date('ymd-Gis', filemtime($jsPath));
        $jsUrl = NGC_URL . '/dist/scripts/script.min.js';

        wp_enqueue_script('net-gross-calc-script', $jsUrl, [], $jsDate, true);
    }
}
