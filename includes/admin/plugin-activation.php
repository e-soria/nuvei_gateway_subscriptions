<?php

// Activation
function nuvei_gateway_subscriptions_activation_check() {
    if (!class_exists('WooCommerce')) {
        deactivate_plugins(plugin_basename(WP_PLUGIN_DIR . '/nuvei-gateway-subscriptions/nuvei-gateway-subscriptions.php'));
    }
}

register_activation_hook(WP_PLUGIN_DIR . '/nuvei-gateway-subscriptions/nuvei-gateway-subscriptions.php', 'nuvei_gateway_subscriptions_activation_check');

// Deactivation
function nuvei_gateway_subscriptions_deactivation_check() {
    if (!class_exists('WooCommerce')) {
        deactivate_plugins(plugin_basename(WP_PLUGIN_DIR . '/nuvei-gateway-subscriptions/nuvei-gateway-subscriptions.php'));
    }
}

add_action('admin_init', 'nuvei_gateway_subscriptions_deactivation_check');

// Deactivation WooCommerce dependency alert
function nuvei_gateway_subscriptions_plugin_meta($links, $file) {
    if ($file === plugin_basename(WP_PLUGIN_DIR . '/nuvei-gateway-subscriptions/nuvei-gateway-subscriptions.php') && !class_exists('WooCommerce')) {
        $links[] = '<div class="alert error-alert is-dismissible" style="display: inline-block; background: #fff5f4; border:1px solid; border-top: 4px solid; border-color: #bf3536; border-radius: 8px; padding: 12px; margin-top: 8px;">
                        <p style="color:#bf3536; font-weight: bold;">Este plugin requiere WooCommerce para funcionar. Por favor, instale o active WooCommerce para poder usar este plugin.</p>
                    </div>';
    }
    return $links;
}

add_filter('plugin_row_meta', 'nuvei_gateway_subscriptions_plugin_meta', 10, 2);

?>
