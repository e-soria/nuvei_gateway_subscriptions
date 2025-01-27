<?php

function delete_user_card($card_token, $user_id) {

    if (empty($card_token) || empty($user_id)) {
        return generate_alert('error', 'Error: Información de tarjeta o usuario inválida.');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'nuvei_user_cards';

    // Obtener las tarjetas asociadas al usuario
    $stored_cards = $wpdb->get_col(
        $wpdb->prepare("SELECT card_token FROM {$table_name} WHERE user_id = %d", $user_id)
    );

    if (empty($stored_cards)) {
        return generate_alert('info', 'No se encontraron tarjetas vinculadas a tu cuenta.');
    }

    if (!in_array($card_token, $stored_cards, true)) {
        return generate_alert('info', 'La tarjeta proporcionada no existe.');
    }

    // Intentar eliminar la tarjeta
    $is_deleted = $wpdb->delete($table_name, ['card_token' => $card_token, 'user_id' => $user_id]);

    return $is_deleted 
        ? true 
        : generate_alert('error', 'Error al eliminar la tarjeta.');
}

function generate_alert($type, $message) {
    return "<div class='alert {$type}-alert'><p>{$message}</p></div>";
}

?>
