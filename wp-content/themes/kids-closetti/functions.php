<?php
// require_once 'vendor/autoload.php';
define('THEME_PATH', get_template_directory_uri());
define('RECAPTCHA_SCORE', +getenv("RECAPTCHA_SCORE"));
define('RECAPTCHA_SECRET_TOKEN', getenv('RECAPTCHA_SECRET_TOKEN'));
define('PAGARME_SECRET_KEY', getenv("PAGARME_SECRET_KEY"));
define('PAGARME_API_URL', getenv("PAGARME_API_URL"));
define('PLANS_IDS', [
    'DEFAULT' => +getenv("PAGARME_PLAN_ID_DEFAULT"),
]);

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

function register_api_subscription()
{
    register_rest_route('api', '/subscription', array(
        'methods' => 'POST',
        'callback' => 'api_subscription',
        'permission_callback' => '__return_true',
    ));
}

add_action('rest_api_init', 'register_api_subscription');

function get_pagarme_route($path)
{
    return PAGARME_API_URL . "/$path";
}

function validate_recaptcha($token)
{
    $recaptcha_secret = RECAPTCHA_SECRET_TOKEN;
    $recaptcha_token = $token;

    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_data = [
        'secret' => $recaptcha_secret,
        'response' => $recaptcha_token,
    ];

    $options = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($recaptcha_data),
        ],
    ];

    $context = stream_context_create($options);
    $recaptcha_result = file_get_contents($recaptcha_url, false, $context);
    $recaptcha_result = json_decode($recaptcha_result);

    return $recaptcha_result?->score >= RECAPTCHA_SCORE;
}

function make_pagarme_client()
{
    return new PagarMe\Client(PAGARME_SECRET_KEY);
}

function make_subscription_payload($body)
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

function api_subscription($request)
{
    try {
        $body = json_decode($request->get_body());
        if (!validate_recaptcha($body->recaptchaToken)) {
            return [
                "status" => false,
                "error" => "Recaptcha inválido"
            ];
        }

        $pagarme = make_pagarme_client();
        $payload = make_subscription_payload($body);
        $subscription = $pagarme->subscriptions()->create($payload);
        return [
            "status" => true,
            "data" => $subscription
        ];
    } catch (Exception $e) {
        return [
            "status" => false,
            "error" => $e->getMessage()
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
