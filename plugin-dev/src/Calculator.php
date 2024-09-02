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

        self::validate($params);

        $netAmount = (float)$params['netAmount'];
        $vatRate = (float)$params['vatRate'];

        $vatAmount = 0.0;
        if ($vatRate > 0.00) {
            $vatAmount = $netAmount * $vatRate;
        }

        $grossAmount = $netAmount + (float)$vatAmount;

        self::setTwoPointsAfterDot($grossAmount);
        self::setTwoPointsAfterDot($vatAmount);

        return [
            'grossAmount' => (string)$grossAmount,
            'vatAmount' => (string)$vatAmount
        ];
    }

    private static function validate(&$params)
    {
        $errors = [];

        //* productName
        if (
            empty($params['productName'])
            || !is_string($params['productName'])
        ) {
            $errors[] = 'Product name must be a non-empty string.';
        }

        //* netAmount
        $params['netAmount'] = self::validateAndFormatNumber($params['netAmount'], 'Net amount', $errors);

        //* currency
        if (empty($params['currency']) || !is_string($params['currency'])) {
            $errors[] = 'Currency must be a non-empty string.';
        }

        //* vatRate
        $params['vatRate'] = self::validateAndFormatNumber($params['vatRate'], 'VAT rate', $errors);

        if (!empty($errors)) {
            throw new \Exception(implode(' ', $errors));
        }
    }

    private static function validateAndFormatNumber($value, $fieldName, &$errors)
    {
        if (!is_string($value) || trim($value) === '') {
            $errors[] = "{$fieldName} must be a non-empty string.";
            return null;
        }

        $value = str_replace(',', '.', str_replace(' ', '', $value));

        $numberValue = filter_var($value, FILTER_VALIDATE_FLOAT);
        if ($numberValue === false) {
            $errors[] = "{$fieldName} must be a valid number.";
            return null;
        }

        self::setTwoPointsAfterDot($value);

        return $value;
    }

    private static function setTwoPointsAfterDot(&$value) {
        $value = (string)$value;

        if (!strpos($value, '.')) {
            $value .= '.00';
        } else {
            $parts = explode('.', $value);
            if (strlen($parts[1]) === 1) {
                $value .= '0';
            }
            if (strlen($parts[1]) > 2) {
                $value = number_format((float)$value, 2, '.', '');
            }
        }

        $value = (float)$value;
    }
}
