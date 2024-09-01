<?php
namespace NetGrossCalc;
use Timber\Timber;

class Calculator
{
    public static function render()
    {
        // $context = Timber::context();
        // return Timber::compile('calculator.twig', $context);
        return Timber::compile('calculator.twig');
    }

    public static function registerShortcode()
    {
        add_shortcode('ngc_calc_form', [__CLASS__, 'render']);
    }

    public static function calculate($params)
    {
        //* Params:
        // 'productName' => string,
        // 'netAmount' => double,
        // 'currency' => string,
        // 'vatRate' => double (eg. 0.23) or null,

        $grossAmount = 0.0;

        // TODO: Check if can be better solution for Exceptions
        //* Validation
        // TODO: Enclose in a seperate method
        if (gettype($params['netAmount']) != 'double') {
            throw new \Exception("Bad format of 'netAmount' var.");
        }
        if (gettype($params['vatRate']) != 'double') {
            throw new \Exception("Bad format of 'vatRate' var.");
        }
        if ($params['netAmount'] < 0.00) {
            throw new \Exception("'netAmount' cannot be negative.");
        }
        if ($params['vatRate'] < 0.00) {
            throw new \Exception("'vatRate' cannot be negative.");
        }

        $vatAmount = 0.0;
        if ($params['vatRate'] > 0.00) {
            $vatAmount = $params['netAmount'] * $params['vatRate'];
        }

        $grossAmount = $params['netAmount'] + $vatAmount;

        return [
            'grossAmount' => $grossAmount,
            'vatAmount' => $vatAmount
        ];
    }
}
