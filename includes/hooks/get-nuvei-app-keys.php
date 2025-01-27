<?php

function get_nuvei_app_keys() {
    $get_plugin_config = get_plugin_config();
    $error_message = 'Hay un problema con las credenciales de tipo "app". Por favor revisa la configuración del plugin.';
    
    if (!$get_plugin_config) {
        echo '<div class="alert error-alert"><p>' . $error_message . '</p></div>';
        return false;
    }

    $get_plugin_mode = ($get_plugin_config['testmode'] ?? '') === 'yes' ? 'stg' : 'prod';

    return [
        'mode'        => $get_plugin_mode,
        'app_code'    => $get_plugin_config[$get_plugin_mode === 'stg' ? 'test_app_code' : 'app_code'] ?? '',
        'app_key'     => $get_plugin_config[$get_plugin_mode === 'stg' ? 'test_app_key' : 'app_key'] ?? ''
    ];
}

?>