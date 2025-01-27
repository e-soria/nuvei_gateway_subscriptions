<?php

function delete_user_card($userId, $user_cards) {
    
    $auth_token = create_auth_token();
    $card_token = null;
    $card_ref = sanitize_text_field($_POST['card_token']);
    
    // Buscar la tarjeta que se debe eliminar
    foreach ($user_cards as $card) {
        if ($card_ref === $card['token']) {
            $card_token = $card['token'];
            $card_data = [
                "card" => [
                    "token" => $card_token
                ],
                "user" => [
                    "id" => $userId
                ]
            ];
            break;
        }
    }
    
    if (!$card_token) {
        return '<p>Error: No se encontró la tarjeta especificada.</p>';
    }

    $JSON_data = json_encode($card_data);
    $baseUrl = get_nuvei_urls();
    $url = $baseUrl . '/card/delete/';

    // Realizar la solicitud cURL a la API de Nuvei
    $request = curl_init();
    curl_setopt($request, CURLOPT_URL, $url);
    curl_setopt($request, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($request, CURLOPT_POSTFIELDS, $JSON_data);
    curl_setopt($request, CURLOPT_HTTPHEADER, ['Auth-Token: ' . $auth_token]);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($request);

    if (curl_errno($request)) {
        $error_message = '<p>Error en la solicitud: ' . curl_error($request) . '</p>';
        curl_close($request);
        return $error_message;
    }

    curl_close($request);
    $result = json_decode($response, true);

    // Verificar si la tarjeta fue eliminada con éxito en la API
    if (isset($result['message']) && $result['message'] === 'card deleted') {
        // Eliminar la tarjeta de nuestra base de datos
        $delete_result = delete_db_user_card($card_token, $userId);
        if ($delete_result) {
            return wp_redirect(home_url($_SERVER['REQUEST_URI']));
        } else {
            return '<p>Error al eliminar la tarjeta de la base de datos.</p>';
        }
    } else {
        return '<p>Error al eliminar la tarjeta: ' . ($result['message'] ?? 'Error desconocido') . '</p>';
    }
}
?>
