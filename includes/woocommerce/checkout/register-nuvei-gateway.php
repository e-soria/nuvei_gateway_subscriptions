<?php

function register_nuvei_payment_gateway($gateways) {
    $gateways[] = 'WC_Nuvei_Gateway_Subscriptions';
    return $gateways;
}

add_filter('woocommerce_payment_gateways', 'register_nuvei_payment_gateway');

?>
