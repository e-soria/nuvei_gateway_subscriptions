<?php

function getCurrentUserInfo() {
    $currentUserID = get_current_user_id();
    $errorIdMessage = 'El usuario no existe, por favor revisa que el ID del usuario sea correcto.';
    $errorDataMessage = 'No se han encontrado datos según el ID. Por favor revisa los datos del usuario.';
    
    if (!$currentUserID) {
        echo '<div class="alert error-alert"><p>' . $errorIdMessage . '</p></div>';
        return false;
    }
    
    $userInfo = get_userdata($currentUserID);
    
    if (!$userInfo) {
        echo '<div class="alert error-alert"><p>' . $errorDataMessage . '</p></div>';
        return false;
    }

    return [
        'id'            => (string) $currentUserID,
        'email'         => $userInfo->user_email ?? '',
        'user_name'     => $userInfo->user_login ?? '',
        'first_name'    => $userInfo->first_name ?? '',
        'last_name'     => $userInfo->last_name ?? ''
    ];
}

?>