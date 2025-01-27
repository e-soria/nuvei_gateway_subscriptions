<?php

/**
    * Plugin Name: Nuvei gateway subscriptions
    * Plugin URI: https://enzosoria.com
    * Description: Este plugin fue desarrollado basado en mis conocimientos e ideas para satisfacer una necesidad en Ecuador. Todo el código y concepto son de mi autoría, y cualquier reproducción o distribución sin mi consentimiento está prohibida.
    * Version: 1.0.0
    * Author: Enzo Soria
    * Author URI: https://enzosoria.com
    * License: Proprietary
    * License URI: https://enzosoria.com
    *
    * © 2025 Enzo Soria. Todos los derechos reservados.
    * Este software es propiedad exclusiva de su autor.
    * Su uso, distribución o modificación sin autorización está prohibido.
**/

require_once plugin_dir_path(__FILE__) . 'includes/admin/plugin-activation.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/plugin-description-links.php';
include plugin_dir_path(__FILE__) . 'includes/woocommerce/checkout/nuvei-checkout-config.php';

function nuvei_gateway_subscriptions_load_files() {

    if (class_exists('WooCommerce')) {

        $plugin_base_path = plugin_dir_path(__FILE__);

        $woocommerce_tab_file = $plugin_base_path . 'includes/woocommerce/my-account-tabs.php';
        

        if (file_exists($woocommerce_tab_file)) {
            require_once $woocommerce_tab_file;
        } else {
            error_log('Error: No se encontró el archivo my-account-tabs.php');
        }

        $files_to_include = [

            '/auth/create-auth-token.php',
            '/api/debit_with_token.php',
            '/api/delete_card.php',
            '/api/list_user_cards.php',
            '/api/refund.php',
            
            '/db/create-cards-table.php',
            '/db/save-user-card.php',
            '/db/delete-user-card.php',
            '/db/update_card_from_db.php',
            '/db/get_card_from_db.php',
            
            '/includes/hooks/get-current-user-data.php',
            '/includes/hooks/get-plugin-config.php',
            '/includes/hooks/get-nuvei-server-keys.php',
            '/includes/hooks/get-nuvei-app-keys.php',
            '/includes/hooks/get-nuvei-urls.php',
            '/includes/woocommerce/checkout/register-nuvei-gateway.php',
            
            '/includes/shortcodes/tokenization-form.php',
            '/includes/shortcodes/show-user-cards.php',
            '/includes/shortcodes/refund_form.php',
            
            '/admin/index.php',
            '/assets/css/index.php',
            
        ];

        foreach ($files_to_include as $file) {
            $file_path = $plugin_base_path . $file;
            if (file_exists($file_path)) {
                include $file_path;
            } else {
                error_log("Error: No se encontró el archivo $file_path");
            }
        }
        
    }
}

add_action('plugins_loaded', 'nuvei_gateway_subscriptions_load_files');

?>
