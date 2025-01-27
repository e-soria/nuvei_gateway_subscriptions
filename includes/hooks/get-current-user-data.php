<?php

function get_current_user_data() {
    $current_user_id = get_current_user_id();
    $error_id_message = 'El usuario no existe, por favor revisa que el ID del usuario sea correcto.';
    $error_data_message = 'No se han encontrado datos según el ID. Por favor revisa los datos del usuario.';
    
    if (!$current_user_id) {
        echo '<div class="alert error-alert"><p>' . $error_id_message . '</p></div>';
        return false;
    }
    
    $current_user_data = get_userdata($current_user_id);
    
    if (!$current_user_data) {
        echo '<div class="alert error-alert"><p>' . $error_data_message . '</p></div>';
        return false;
    }

    return [
        'id'            => (string) $current_user_id,
        'email'         => $current_user_data->user_email ?? '',
        'user_name'     => $current_user_data->user_login ?? '',
        'first_name'    => $current_user_data->first_name ?? '',
        'last_name'     => $current_user_data->last_name ?? ''
    ];
}

?>