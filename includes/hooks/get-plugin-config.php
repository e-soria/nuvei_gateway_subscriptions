<?php

function get_plugin_config() {
    $get_plugin_config = get_option('woocommerce_nuvei_gateway_subscriptions_settings'); // TODO CAMBIAR ESTO

    $error_message = 'Hay un problema con el guardado y carga de las configuraciones en la DB. Contáctate con el soporte técnico para recibir ayuda.';
    
    if (!$get_plugin_config) {
        echo '<div class="alert error-alert"><p>' . $error_message . '</p></div>';
        return false;
    }

    return $get_plugin_config;
}


?>