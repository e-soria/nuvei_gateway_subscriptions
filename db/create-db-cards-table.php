<?php

function create_db_cards_table() {
   
    global $wpdb;

    $table_name = $wpdb->prefix . 'nuvei_user_cards';
    $table_collation = $wpdb->get_charset_collate();

    $create_table = "CREATE TABLE IF NOT EXISTS $table_name (
        card_id INT NOT NULL AUTO_INCREMENT,
        card_token VARCHAR(255) NOT NULL,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        subscription_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (card_id),
        UNIQUE (card_token),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID)
    ) $table_collation;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $created_table = dbDelta($create_table);

    if (is_wp_error($created_table)) {
        error_log('No se ha podido crear la tabla en la base de datos: ' . $created_table->get_error_message());
    } else {
        add_option('wp_nuvei_cards_table_created', true);
    }
}

register_activation_hook(__FILE__, 'create_cards_table');

// Verificar si la tabla ya existe antes de crearla
if (!get_option('wp_nuvei_cards_table_created')) {
    create_cards_table();
}

?>
