<?php   

// Agregar nuevo endpoint "reembolsos"
function set_new_tab_endpoints() {
    add_rewrite_endpoint( 'mis-tarjetas', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'reembolsos', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'set_new_tab_endpoints' );

// Agregar variable de consulta personalizada para "reembolsos"
function set_new_tab_variables( $vars ) {
    $vars[] = 'mis-tarjetas';
    $vars[] = 'reembolsos';
    return $vars;
}
add_filter( 'query_vars', 'set_new_tab_variables', 0 );

// Agregar nueva pestaña al menú de la cuenta del usuario
function add_tabs_to_profile( $tabs ) {
    $tabs['mis-tarjetas'] = 'Mis tarjetas';
    $tabs['reembolsos'] = 'Reembolsos';
    return $tabs;
}
add_filter( 'woocommerce_account_menu_items', 'add_tabs_to_profile' );


function add_tabs_content_mis_tarjetas() {

    echo '<div class="mis-tarjetas-container">';
        echo '<div class="user-cards-container">';
            echo do_shortcode('[show_user_cards tokenization_form="true" delete_card_button="true"]');
        echo '</div>';
        // echo '<div class="tokenize-form-container">';
        //     echo do_shortcode('[tokenization_form]');
        // echo '</div>';
    echo '</div>';

    return;
}
add_action( 'woocommerce_account_mis-tarjetas_endpoint', 'add_tabs_content_mis_tarjetas' );

function add_tabs_content_reembolsos() {
    echo do_shortcode( '[your_custom_shortcode_for_reembolsos_here]' );
}
add_action( 'woocommerce_account_reembolsos_endpoint', 'add_tabs_content_reembolsos' );

?>