<?php

function get_user_card($user_id, $card_ref) {

    if (empty($user_id) || empty($card_ref)) {
        return '<div class="alert error-alert"><p>Error: ID de usuario o referencia de tarjeta inválidos.</p></div>';
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'nuvei_user_cards';

    $card_data = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE user_id = %d AND card_ref = %s",
            intval($user_id),
            sanitize_text_field($card_ref)
        ),
        ARRAY_A
    );

    if (!$card_data) {
        return '<div class="alert info-alert">
                    <p>No se pudo encontrar la tarjeta. Intenta con otra o contáctanos para recibir ayuda.</p>
                </div>';
    }

    return $card_data;
}

?>
