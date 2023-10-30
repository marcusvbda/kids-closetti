<?php
require_once 'vendor/autoload.php';

use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;

define('THEME_PATH', get_template_directory_uri());
define('MP_TOKEN', getenv("MP_ACCESS_TOKEN"));

function themePath($path)
{
    echo THEME_PATH . $path;
}

function formatStrong($text)
{
    return preg_replace('/\*(.*?)\*/', '<b>$1</b>', $text);
}

function loopToArray($field, $subField)
{
    $items = [];
    if (have_rows($field)) {
        while (have_rows($field)) {
            the_row();
            $items[] = get_sub_field($subField);
        }
    }
    return $items;
}

function loopToString($field, $subField, $separator = ",")
{
    echo implode($separator, loopToArray($field, $subField));
}

function customize_acf_wysiwyg_toolbar($toolbars)
{
    $toolbars['Very Simple'] = [
        [
            'bold',
            'italic',
            'underline',
            'removeformat',
            'wp_adv',
        ],
    ];
    return $toolbars;
}
add_filter('acf/fields/wysiwyg/toolbars', 'customize_acf_wysiwyg_toolbar');

function register_api_pagto()
{
    register_rest_route('api', '/pgto', array(
        'methods' => 'POST',
        'callback' => 'api_pagto',
        'permission_callback' => '__return_true',
    ));
}

add_action('rest_api_init', 'register_api_pagto');

function make_pgto_client()
{
    MercadoPagoConfig::setAccessToken(MP_TOKEN);
    return new PaymentClient();
}

function make_pagto_payload($body)
{
    return [
        'payment_method' => 'credit_card',
        'card_number' => str_replace(' ', '', $body->paymentInfo->creditcard->number),
        'card_holder_name' => $body->paymentInfo->creditcard->name,
        'card_expiration_date' => str_replace('/', '', $body->paymentInfo->creditcard->dueDate),
        'card_cvv' => $body->paymentInfo->creditcard->cvv,
        'customer' => [
            'email' => $body->personalInfo->email,
            'name' => $body->personalInfo->name,
            'document_number' =>  str_replace(' ', '', $body->personalInfo->docNumber)
        ],
    ];
}

function api_pagto($request)
{
    try {
        $body = json_decode($request->get_body());
        if (!validate_recaptcha($body->recaptchaToken)) {
            return [
                "status" => false,
                "error" => "Recaptcha inválido"
            ];
        }

        $client = make_pgto_client();
        $request = [
            "transaction_amount" => 100,
            "token" => "YOUR_CARD_TOKEN",
            "description" => "description",
            "installments" => 1,
            "payment_method_id" => "visa",
            "payer" => [
                "email" => "user@test.com",
            ]
        ];
        $payment = $client->create($request);
        return [
            "status" => true,
            "data" => $payment
        ];
    } catch (MPApiException $e) {
        $message =  "Status code: " . $e->getApiResponse()->getStatusCode() . "\n";
        $message .= "Content: " . $e->getApiResponse()->getContent() . "\n";
        return [
            "status" => false,
            "error" => $e->getMessage(),
            "message" => $message
        ];
    }
}

function make_api_bg_vars($field, $has_mobile = false, $extra = [])
{
    $field_mobile = $has_mobile ?  $field . "_mobile" : $field;
    $result = "--$field: url('" . get_field($field) . "');--" . $field_mobile . ": url('" . get_field($field_mobile) . "')";
    if (count($extra)) {
        foreach ($extra as $key => $value) {
            $result .= ";--$key: $value";
        }
    }
    echo $result;
}
