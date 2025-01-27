<?php

function save_db_user_card($user_id, $card_data) {

    if (empty($user_id) || empty($card_data)) {
        display_alert('error', 'Token de tarjeta o ID de usuario no válido.');
        return false;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'nuvei_user_cards';

    $insert_data = [
        'card_token'      => $card_data['card_token'],
        'user_id'         => intval($user_id),
        'subscription_id' => $card_data['subscription_id'] ?? null,
    ];

    $insert_result = $wpdb->insert($table_name, $insert_data);

    if (!$insert_result) {
        display_alert('error', 'Error al guardar los datos en la base de datos: ' . esc_html($wpdb->last_error));
        return false;
    }

    return true;
}

function display_alert($type, $message) {
    echo "<div class='alert {$type}-alert'><p><i class='icon-info' aria-hidden='true'></i> {$message}</p></div>";
}

?>
