<?php
namespace NetGrossCalc;
use Timber\Timber;
class Core
{
    public static function init()
    {
        // TODO: Handle ACF or meta boxes
        // TODO: Saving into CPT
        // TODO: Frontend styles
        Timber::$dirname = "../views";
        Assets::init();
        CPT::init();
        RestApi::registerRoutes();
        Calculator::registerShortcode();
    }
}
