<?php

function get_nuvei_urls() {

    $get_plugin_config = get_plugin_config();
    $error_message = 'Hay un problema con las URL de producción o desarrollo del plugin. Por favor revisa que el plugin esté configurado correctamente.';
    
    if (!$get_plugin_config) {
        echo '<div class="alert error-alert"><p>' . $error_message . '</p></div>';
        return false;
    }
 
    return $get_plugin_config[$get_plugin_config['testmode'] === 'yes' ? 'stg_mode_url' : 'prod_mode_url'] ?? '';
}

?>