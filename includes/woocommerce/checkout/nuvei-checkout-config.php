<?php

if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) return;

function nuvei_init_gateway_class() {
    
    class WC_Nuvei_Gateway_Subscriptions extends WC_Payment_Gateway {

        public function __construct() {

            $this->id = 'nuvei_gateway_subscriptions'; 
            $this->icon = 'https://dev.draxdesign.com/wp-content/plugins/nuvei-gateway-subscriptions/assets/images/nuvei_logo_image.webp'; 
            $this->has_fields = true;
            $this->method_title = 'Nuvei Gateway Subscriptions';
            $this->method_description = 'Vende tus productos de tipo subscripción a través de Nuvei';
            
            $this->supports = array( 
                'products', 
                'subscriptions',
                'subscription_cancellation', 
                'subscription_suspension', 
                'subscription_reactivation',
                'subscription_amount_changes',
                'subscription_date_changes',
                'multiple_subscriptions',
            );

	        $this->init_form_fields();
           
            $this->init_settings();

            $this->title = $this->get_option( 'title' );
            $this->description = $this->get_option( 'description' );
            $this->enabled = $this->get_option( 'enabled' );
            $this->testmode = 'yes' === $this->get_option( 'testmode' ); // true or false
            
            $this->test_app_code = $this->get_option( 'test_app_code' );
            $this->test_app_key = $this->get_option( 'test_app_key' );
            $this->test_app_server_code = $this->get_option( 'test_app_server_code' );
            $this->test_app_server_key = $this->get_option( 'test_app_server_key' );

           
            $this->app_code = $this->get_option( 'app_code' );
            $this->app_key = $this->get_option( 'app_key' );
            $this->app_server_code = $this->get_option( 'app_server_code' );
            $this->app_server_key = $this->get_option( 'app_server_key' );
            
            $this->stg_mode_url = $this->get_option( 'stg_mode_url' );
            $this->prod_mode_url = $this->get_option( 'prod_mode_url' );

            $this->tax_enabled = $this->get_option( 'tax_enabled' );
            $this->tax_percentage = $this->get_option('tax_enabled') === 'no' ? '0' : $this->get_option( 'tax_percentage' );
          
            $this->customer_support_email = $this->get_option('customer_support_email');

            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
          
            add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
          
            add_action('woocommerce_scheduled_subscription_payment_' . $this->id, array($this, 'woocommerce_scheduled_subscription_payment'), 10, 2);

            //add_action('woocommerce_scheduled_subscription_payment',  array($this, 'custom_scheduled_subscription_payment'));
            //add_action( 'woocommerce_subscription_status_updated', array( $this, 'woocommerce_subscription_status_updated' ), 10, 3 );
 		}
        
		public function init_form_fields(){

            $this->form_fields = array(
                'enabled' => array(
                    'title'       => 'Activar Nuvei Gateway Subscriptions',
                    'label'       => '',
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'title' => array(
                    'title'       => 'Título',
                    'type'        => 'text',
                    'description' => 'Este título será visible en la página de pago.',
                    'default'     => 'Tus pagos seguros con Nuvei',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Descripción',
                    'type'        => 'textarea',
                    'description' => 'Esta descripción será visible en la página de pago.',
                    'default'     => 'Ingresa los datos de tu tarjeta para completar la compra.',
                    'desc_tip'    => true,
                ),
                'testmode' => array(
                    'title'       => 'Modo de pruebas',
                    'label'       => 'Activar el modo de pruebas',
                    'type'        => 'checkbox',
                    'description' => 'Marca esta casilla para realizar pruebas pre-produccion. Debes tener credenciales de pruebas para usar esta opción.',
                    'default'     => 'no',
                    'desc_tip'    => true,
                ),
                'test_app_code' => array(
                    'title'       => 'Application Code (modo pruebas)',
                    'type'        => 'text',
                    'default'     => '',
                    'desc_tip'    => true,
                    'description' => 'Esta credencial es necesaria para poder utilizar el modo de pruebas.',
                ),
                'test_app_key' => array(
                    'title'       => 'Application Key (modo pruebas)',
                    'type'        => 'password',
                    'default'     => '',
                    'desc_tip'    => true,
                    'description' => 'Esta credencial es necesaria para poder utilizar el modo de pruebas.',
                ),
                'test_app_server_code' => array(
                    'title'       => 'Application Server Code (modo pruebas)',
                    'type'        => 'text',
                    'default'     => '',
                    'desc_tip'    => true,
                    'description' => 'Esta credencial es necesaria para poder utilizar el modo de pruebas.',
                ),
                'test_app_server_key'  => array(
                    'title'       => 'Application Server Key (modo pruebas)',
                    'type'        => 'password',
                    'default'     => '',
                    'desc_tip'    => true,
                    'description' => 'Esta credencial es necesaria para poder utilizar el modo de pruebas.',
                ),
                'app_code' => array(
                    'title'       => 'Application Code (modo producción)',
                    'type'        => 'text',
                    'default'     => '',
                    'desc_tip'    => true,
                    'description' => 'Esta credencial es necesaria para poder utilizar el modo de producción.',
                ),
                'app_key' => array(
                    'title'       => 'Application Key (modo producción)',
                    'type'        => 'password',
                    'default'     => '',
                    'desc_tip'    => true,
                    'description' => 'Esta credencial es necesaria para poder utilizar el modo de producción.',
                ),
                'app_server_code' => array(
                    'title'       => 'Application Server Code (modo producción)',
                    'type'        => 'text',
                    'default'     => '',
                    'desc_tip'    => true,
                    'description' => 'Esta credencial es necesaria para poder utilizar el modo de producción.',
                ),
                'app_server_key'  => array(
                    'title'       => 'Application Server Key (modo producción)',
                    'type'        => 'password',
                    'default'     => '',
                    'desc_tip'    => true,
                    'description' => 'Esta credencial es necesaria para poder utilizar el modo de producción.',
                ),
                'stg_mode_url'  => array(
                    'title'       => 'URL de modo pruebas (stg)',
                    'type'        => 'text',
                    'default'     => 'https://ccapi-stg.paymentez.com/v2',
                    'desc_tip'    => true,
                    'description' => 'URL base para utilizar los endpoints en modo de pruebas.',
                ),
                'prod_mode_url'  => array(
                    'title'       => 'URL de modo producción (prod)',
                    'type'        => 'text',
                    'default'     => 'https://ccapi-prod.paymentez.com/v2',
                    'desc_tip'    => true,
                    'description' => 'URL base para utilizar los endpoints en modo de producción.',
                ),
                'tax_enabled' => array(
                    'title'       => 'Activar impuestos',
                    'label'       => '',
                    'type'        => 'checkbox',
                    'default'     => 'no',
                    'desc_tip'    => true,
                    'description' => 'Marca esta casilla si pagas impuestos.',
                ),
                'tax_percentage' => array(
                    'title'       => 'Porcentaje de impuestos',
                    'type'        => 'text',
                    'default'     => '0',
                    'description' => 'Indica el porcentaje de impuestos que pagas. No es necesario escribir el signo de porcentaje.',
                    'desc_tip'    => true,
                    'placeholder' => 'Example: 12'
                ),
                'customer_support_email' => array(
                    'title'       => 'Email de soporte al cliente',
                    'type'        => 'email',
                    'default'     => '',
                    'description' => 'Escribe el correo de soporte al cliente. Aparecera para ayudar a los usuarios en caso de errores.',
                    'desc_tip'    => true,
                    'placeholder' => 'support@support.com'
                ),
            );

	 	}

		public function payment_fields() {

            if( $this->description ) {
                if( $this->testmode ) {
                    $this->description .= ' <div class="alert info-alert">
                        <p class="test-mode"><strong>Has activado el modo de pruebas.</strong> Para desactivarlo haz <a target="_blank" nofollow href="/wp-admin/admin.php?page=wc-settings&tab=checkout&section=nuvei_gateway_subscriptions">click aquí para ir a los ajustes.</a></p> 
            
                    </div>';
                    $this->description  = trim( $this->description );
                }
            
                echo wpautop( wp_kses_post( $this->description ) );
            }
            
            echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">';

                    echo '<div class="use-saved-cards-option">';
                        echo '<input type="checkbox" id="use_saved_cards" name="use_saved_cards" />';
                        echo '<label for="use_saved_cards">Deseo pagar con una de mis tarjetas</label>';
                    echo '</div>';
                    
                    do_shortcode('[show_user_cards use_card_button="true" delete_card_button="false"]');

                do_action( 'woocommerce_credit_card_form_start', $this->id );
                
                do_shortcode('[tokenization_form is_checkout="true"]');
            
                do_action( 'woocommerce_credit_card_form_end', $this->id );
        
            echo '<div class="clear"></div></fieldset>';

		}


	 	public function payment_scripts() {

        }


        // Function to validate fields
		public function validate_fields() {
            //wc_add_notice( 'Debug Info: ' . print_r( $_POST, true ), 'notice' );
            
        }

        public function process_payment($order_id) {

            $cart_items = WC()->cart->get_cart();
            $subscription_id = $this->detect_subscription($cart_items);
        
            $user_data = $this->get_user_data();
            $card_token = $_POST['card_token'];
            $order_data = $this->generate_order_data($order_id, $user_data, $cart_items);
        
            // FREE TRIAL CASE
            if ($order_data['order_subtotal'] == 0 && $order_data['order_total'] == 0) {

                if (isset($_POST['use_saved_cards'])) {

                    $update_card = update_db_user_card( $user_data['user_id'], $card_token, $subscription_id);
                    
                } else {
                    
                    $user_card_data = array(
                        "card_token"      => $card_token,
                        'subscription_id' => isset($subscription_id) ? $subscription_id : null,
                    );
                 
                    save_db_user_card($user_data['user_id'], $user_card_data);
                
                }

                return $this->complete_order($order_id);
            } 
            // END FREE TRIAL CASE
        
            $execute_debit = debit_with_token($user_data, $card_token, $order_data);
            wc_add_notice('Execute debit info: ' . print_r($execute_debit, true), 'notice');
        
            if ($execute_debit['status'] === 'success') {

                $this->handle_successful_payment($order_id, $user_data['user_id'], $card_token, $subscription_id, $execute_debit);
                return $this->redirect_to_thank_you_page($order_id);

            } elseif ($execute_debit['status'] === 'pending') {

                return $this->handle_pending_payment($order_id);

            }
        
            return $this->handle_failed_payment($order_id);

        }
        
        private function detect_subscription($cart_items) {

            foreach ($cart_items as $cart_item) {
                $product_id = $cart_item['data']->get_id();
                if (class_exists('WC_Subscriptions_Product') && WC_Subscriptions_Product::is_subscription($product_id)) {
                    return $product_id;
                }
            }

            return null;

        }
        
        private function get_user_data() {

            $data = get_current_user_data();
            return [
                'user_id'    => $data['id'],
                'user_email' => $data['email'],
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
            ];

        }
        
        private function generate_order_data($order_id, $user_data, $cart_items) {

            $product_names = array_map(function($item) {
                return $item['data']->get_name();
            }, $cart_items);
        
            return [
                'order_id'          => $order_id,
                'order_description' => $user_data['first_name'] . ' ' . $user_data['last_name'] . ' has bought: ' . implode(', ', $product_names),
                'order_subtotal'    => (float) WC()->cart->get_subtotal(),
                'order_total'       => (float) WC()->cart->get_total('number'),
                'order_taxes'       => (float) WC()->cart->get_total_tax(),
                'tax_percentage'    => (float) get_option('woocommerce_nuvei_settings')['tax_percentage'],
            ];

        }
        
        private function complete_order($order_id) {

            $order = wc_get_order($order_id);
            $order->payment_complete();
            $order->reduce_order_stock();
            WC()->cart->empty_cart();
            return [
                'result'   => 'success',
                'redirect' => $this->get_return_url($order),
            ];

        }
        
        private function handle_successful_payment($order_id, $user_id, $card_token, $subscription_id, $execute_debit) {

            if (isset($_POST['use_saved_cards'])) {

                update_db_user_card($user_id, $card_token, $subscription_id);

            } else {

                save_db_user_card($user_id, [
                    'card_token'      => $card_token,
                    'subscription_id' => $subscription_id,
                ]);

            }
        
            $order = wc_get_order($order_id);
            foreach ($execute_debit['transaction_data'] as $key => $value) {
                $order->update_meta_data('_nuvei_debit_' . $key, $value);
            }
        
            update_post_meta($order_id, '_user_card_data', ['subscription_id' => $subscription_id]);
            $order->payment_complete();
            $order->reduce_order_stock();
            WC()->cart->empty_cart();

        }
        
        private function redirect_to_thank_you_page($order_id) {

            $order = wc_get_order($order_id);
            return [
                'result'   => 'success',
                'redirect' => $this->get_return_url($order),
            ];

        }
        
        private function handle_pending_payment($order_id) {
            // Implement handling of pending payment if required
            return;
        }
        
        private function handle_failed_payment($order_id) {
            wc_add_notice(
                '<p>Error: No es posible realizar la transacción. Por favor prueba con otra tarjeta, comunícate con tu banco o escribe a <a href="mailto:hi@staging.hiitclub.online">hi@staging.hiitclub.online</a> para recibir ayuda</p>',
                'error'
            );
            wp_delete_post($order_id, true);
        }        
        

        // RENOVACIÓN AUTOMÁTICA
        public function woocommerce_scheduled_subscription_payment($amount_total, $renewal_order) {

            // Get user data
            $user_id = $renewal_order->get_customer_id();
            $product_price = null;

            
            // Get product_id ( subscription )
            foreach ( $renewal_order->get_items() as $item_id => $item ) {
                $product = $item->get_product();
                $product_price = $product->get_price();
                $product_id = $product->get_id();

                if (class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $product_id ) || $product->is_type('subscription') || $product->is_type( 'variable-subscription') ) {
                    $subscription_id = $product_id;
                }
            }

            // Make a query to get the card associated with the subscription
            global $wpdb;

            $table_name = $wpdb->prefix . 'nuvei_user_cards';
                   
            $sql = $wpdb->prepare(
                "SELECT * FROM $table_name WHERE user_id = %d AND subscription_id = %d",
                $user_id,
                $subscription_id
            );
            
            $user_card = $wpdb->get_row($sql, ARRAY_A);

            if(!$user_card || empty($user_card)) {
                return;
            }

            // Get all info we need to excecute the debit_with_token() API
            // 1) User Data
            $get_user_data = get_userdata($user_id);

            $user_data = [
                'user_id'    => $user_id,
                'user_email' => $get_user_data->user_email,
                'first_name' => $renewal_order->get_billing_first_name(),
                'last_name'  => $renewal_order->get_billing_last_name(),
            ];

            // 2) User Token ID
            $card_token = $user_card['card_token'];

            // 3) Order Data
            $order_id = $renewal_order->get_id();
            
            //$order_description = $user_data['first_name'] . ' ' . $user_data['last_name'] . ' ' . 'has renewed the' . ' ' . $product_name . ' ' . 'subscription'; 
            $order_description = $user_data['first_name'] . ' ' . $user_data['last_name'] . ' ' . 'has renewed the subscription'; 
            
            $order_data = [
                'order_id'          => $order_id,
                'order_description' => $order_description,
                'order_subtotal'    => (float)$product_price * 1.00,  // without taxes
                'order_total'       => (float)$product_price * 1.00, //with taxes
                'order_taxes'       => (float)$renewal_order->get_total_tax() * 1.00, // only taxes
                'tax_percentage'    => (float)get_option('woocommerce_nuvei_settings')['tax_percentage'] * 1.00, // tax setted from plugin settings
            ];
 
            // EXCECUTE THE DEBIT_WITH_TOKEN() API
            $excecute_debit = debit_with_token($user_data, $card_token, $order_data );

            if ($excecute_debit['status'] === 'success') {

                // add order meta data
                $transaction_data = $excecute_debit['transaction_data'];

                $order = wc_get_order( $order_id );

                foreach ($transaction_data as $key => $value) {
                    $order->update_meta_data( '_nuvei_debit_' . $key, $value );
                }
                
                // TODO: indicar la subscripcion y la orden como activa
                $renewal_order->update_status('completed', 'order_note');

                $order->save();
                
                $primer_mensaje = $order_id;
                
            } else {

                return;

            }
                      
        }
        
/*
        public function woocommerce_subscription_status_updated( $subscription, $new_status, $old_status ) {
            // Get the subscription object
            $customer = $subscription->get_customer_id();
            $user = get_user_by( 'ID', $customer );

            foreach ($subscription->get_items() as $item_id => $item ) {

                $product = $item->get_product();

                if($product->is_type('subscription')) {
                    $product_name = $product->get_sku();
                    $product_id = $item->get_product_id();
                }
            }
    
        
            // Check if the subscription is active
            if ( $new_status === 'active' ) {
                // Check that it's not a guest customer and the user object is valid
                if ( is_a( $user, 'WP_User' ) && $user->ID > 0 ) {
        
                    // Add the "subscriber-a" role if it's not already set
                    if ( !in_array( $product_name, $user->roles ) ) {
                        $name = strtolower($product_name);
                        $user->add_role($name);
                    } 
        
                }
        
            } else {
                if (in_array($product_name, $user->roles)) {
                    $name = strtolower($product_name);
                    $user->remove_role($name);
                }
            }
        }
        */
     
		public function webhook() {
					
	 	}
 	}
}

add_action( 'plugins_loaded', 'nuvei_init_gateway_class' );


?>