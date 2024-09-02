<?php

namespace NetGrossCalc;

use WP_REST_Request, WP_REST_Response;

class RestApi
{
    public static function registerRoutes()
    {
        add_action('rest_api_init', function () {
            register_rest_route('net-gross-calc/v1', '/calculate', [
                'methods' => 'POST',
                'callback' => [__CLASS__, 'prepareCalculation'],
            ]);
        });
    }

    public static function prepareCalculation(WP_REST_Request $request)
    {
        $params = [
            'productName' => $request->get_param('productName'),
            'netAmount' => $request->get_param('netAmount'),
            'currency' => $request->get_param('currency'),
            'vatRate' => $request->get_param('vatRate'),
        ];

        //* Wanted data types:
        // 'productName' => string,
        // 'netAmount' => double,
        // 'currency' => string,
        // 'vatRate' => double (eg. 0.23) or null,

        try {
            foreach ($params as $key => $value) {
                if ($value === null) {
                    return new WP_REST_Response([
                        'message' => "'{$key}' is required."
                    ], 400);
                }
                if (
                    ($key == 'netAmount' || $key == 'vatRate')
                    && $value < 0.0
                    ) {
                    return new WP_REST_Response([
                        'message' => "'{$key}' have to be a positive value."
                    ], 400);
                }
            }

            $result = Calculator::calculate($params);

            return new WP_REST_Response([
                'message' => 'Calculated successfully.',
                'result' => $result
            ], 200);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            error_log('Error doing calculation: ' . $message);

            return new WP_REST_Response([
                'message' => $message ?: 'Internal server error.'
            ], 500);
        }
    }
}
