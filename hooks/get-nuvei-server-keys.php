<?php

function get_nuvei_server_keys() {
    $get_plugin_config = get_plugin_config();
    $error_message = 'Hay un problema con las credenciales de tipo "server". Por favor revisa la configuración del plugin.';
    
    if (!$get_plugin_config) {
        echo '<div class="alert error-alert"><p>' . $error_message . '</p></div>';
        return false;
    }

    // TODO CAMBIAR NOMBRES
    return ($get_plugin_config['testmode'] ?? '') === 'yes' ? [
        'server_code' => $get_plugin_config['test_app_server_code'] ?? '',
        'server_key'  => $get_plugin_config['test_app_server_key'] ?? ''
    ] : [
        'server_code' => $get_plugin_config['app_server_code'] ?? '',
        'server_key'  => $get_plugin_config['app_server_key'] ?? ''
    ];
}

?>