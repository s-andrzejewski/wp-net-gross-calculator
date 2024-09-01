<?php
namespace NetGrossCalc;
use Timber\Timber;
class Core
{
    public static function init()
    {
        Timber::$dirname = "../views";
        Assets::init();
        CPT::init();
        RestApi::registerRoutes();
        Calculator::registerShortcode();
    }
}
