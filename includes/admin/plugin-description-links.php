<?php

function nuvei_gateway_subscriptions_config_link($links) {

    $config_link = '<a href="admin.php?page=wc-settings&tab=checkout">Ajustes</a>';
    array_push($links, $config_link);
    return $links;

}

add_filter("plugin_action_links_" . plugin_basename(dirname(__FILE__, 3) . "/nuvei-gateway-subscriptions.php"), 'nuvei_gateway_subscriptions_config_link');

?>