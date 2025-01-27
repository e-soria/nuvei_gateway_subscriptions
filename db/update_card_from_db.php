<?php

function update_user_card($user_id, $card_token, $subscription_id) {

    if (empty($user_id) || empty($card_token) || empty($subscription_id)) {
        return '<div class="alert error-alert">Error: Datos de tarjeta o usuario inválidos.</div>';
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'nuvei_user_cards';

    $update_card = $wpdb->update(
        $table_name,
        ['subscription_id' => $subscription_id],
        ['user_id' => $user_id, 'card_token' => $card_token],
        ['%d'],
        ['%d', '%s']
    );

    if ($update_card === false) {
        return '<div class="alert error-alert">
                    <p>No se pudo actualizar la tarjeta. Intenta nuevamente o contáctanos para recibir asistencia.</p>
                </div>';
    }

    return true;
}
?>
