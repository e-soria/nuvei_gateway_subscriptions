<?php

function tokenization_form($atts) { 
  $atts = shortcode_atts(
    ['is_checkout' => false], 
  $atts);

  ?>
    
  <div id="tokenize-form">
      
    <div id='tokenize-form-container'>
          
      <div id="response"></div>
          
      <div id='tokenize_example'></div>

      <form id="tokenize_form" method="post" style="display: none;">
        <input type="hidden" name="action" value="save_card">
      </form>

      <input type="button" id='tokenize_btn' class='tok_btn' value="Guardar tarjeta">
      <input type="button" id='retry_btn' class='tok_btn' display='none' value="Guardar nueva tarjeta">
      
    </div>

  </div>

  <?php  

  load_tokenization_assets();
  wp_localize_script('init-tokenization', 'isCheckout', $atts);

}

add_shortcode('tokenization_form', 'tokenization_form'); 


function load_tokenization_assets() {
    // Obtener la URL base del plugin de forma más precisa
    $plugin_base_url = plugin_dir_url(dirname(__FILE__, 2));

    // Cargar scripts necesarios para la tokenización
    wp_enqueue_script('tokenization-form', $plugin_base_url . 'api/tokenization-form.js', ['jquery'], null, true);
    wp_enqueue_script('init-tokenization', $plugin_base_url . 'api/init-tokenization.js', ['jquery', 'tokenization-form'], null, true);

    // Obtener y enviar datos del usuario al script JS
    $current_user_data = get_current_user_data();
    wp_localize_script('tokenization-form', 'userData', $current_user_data);
    
    // Obtener y enviar credenciales de la app al script JS
    $api_credentials = get_nuvei_app_keys();
    wp_localize_script('tokenization-form', 'credentials', $api_credentials);
}

?>