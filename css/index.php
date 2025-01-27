<?php

function initCssFiles() {
    $pluginBasePath = plugin_dir_url( __FILE__ );
    wp_enqueue_style( 'alert-styles', $pluginBasePath . 'alert-styles.css', array(), '1.0.0', 'all' );
    wp_enqueue_style('refund-form-styles', $pluginBasePath . 'refund-form.css');
    wp_enqueue_style('show-user-cards-styles', $pluginBasePath . 'show-user-cards.css');
    wp_enqueue_style('tokenize-form-styles', $pluginBasePath . 'tokenize-form.css');
}

add_action( 'wp_enqueue_scripts', 'initCssFiles' );


function nuveiAdminPanelStyles() {
    $pluginBasePath = plugin_dir_url( __FILE__ );
    wp_enqueue_style('order-table-styles', $pluginBasePath . '/admin/styles.css');
}

add_action('admin_enqueue_scripts', 'nuveiAdminPanelStyles');



?>