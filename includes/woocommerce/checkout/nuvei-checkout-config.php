<?php

if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) return;

function nuvei_init_gateway_class() {
    
    class WC_Nuvei_Gateway_Subscriptions extends WC_Payment_Gateway {

        public function __construct() {

            $this->id = 'nuvei_gateway_subscriptions'; 
            $this->icon = 'https://hiitclub.online/wp-content/uploads/2024/08/WhatsApp-Image-2024-08-01-at-3.19.36-PM.jpeg'; 
            $this->has_fields = true;
            $this->method_title = 'Nuvei Gateway Subscriptions';
            $this->method_description = 'Use this payment method to make sales and payments through Nuvei';
            
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
                    'title'       => 'Enable/Disable',
                    'label'       => 'Enable Nuvei Gateway Subscriptions',
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'This is the title that users will see at checkout',
                    'default'     => 'Your secure payment through Nuvei.',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'Payment method description that the customer will see on your checkout.',
                    'default'     => 'We use all Nuvei security methods.',
                    'desc_tip'    => true,
                ),
                'testmode' => array(
                    'title'       => 'Test mode',
                    'label'       => 'Enable Test Mode',
                    'type'        => 'checkbox',
                    'description' => 'To use "test mode" you must enter your test credentials',
                    'default'     => 'no',
                    'desc_tip'    => true,
                ),
                'test_app_code' => array(
                    'title'       => 'Test Application Code',
                    'type'        => 'text',
                    'default'     => ''
                ),
                'test_app_key' => array(
                    'title'       => 'Test Application Key',
                    'type'        => 'password',
                    'default'     => ''
                ),
                'test_app_server_code' => array(
                    'title'       => 'Test App Server Code',
                    'type'        => 'text',
                    'default'     => ''
                ),
                'test_app_server_key'  => array(
                    'title'       => 'Test App Server Key',
                    'type'        => 'password',
                    'default'     => ''
                ),
                'app_code' => array(
                    'title'       => 'Application Code',
                    'type'        => 'text',
                    'default'     => ''
                ),
                'app_key' => array(
                    'title'       => 'Application Key',
                    'type'        => 'password',
                    'default'     => ''
                ),
                'app_server_code' => array(
                    'title'       => 'App Server Code',
                    'type'        => 'text',
                    'default'     => ''
                ),
                'app_server_key'  => array(
                    'title'       => 'App Server Key',
                    'type'        => 'password',
                    'default'     => ''
                ),
                'stg_mode_url'  => array(
                    'title'       => 'Base URL stg',
                    'type'        => 'text',
                    'default'     => 'https://ccapi-stg.paymentez.com/v2',
                    'desc_tip'    => true,
                    'description' => 'Base url for endpoints in stg mode. Ex: https://ccapi-stg.paymentez.com/v2',
                ),
                'prod_mode_url'  => array(
                    'title'       => 'Base URL prod',
                    'type'        => 'text',
                    'default'     => 'https://ccapi-prod.paymentez.com/v2',
                    'desc_tip'    => true,
                    'description' => 'Base url for endpoints in prod mode. Ex: https://ccapi-prod.paymentez.com/v2',
                ),
                'tax_enabled' => array(
                    'title'       => 'Enable Tax',
                    'label'       => 'Do you pay taxes?',
                    'type'        => 'checkbox',
                    'default'     => 'no',
                    'desc_tip'    => true,
                    'description' => 'If you pay taxes, enable this option.',
                ),
                'tax_percentage' => array(
                    'title'       => 'Tax Percentage',
                    'type'        => 'text',
                    'default'     => '0',
                    'description' => 'Enter the tax percentage do you pay. Do not include the "%" symbol',
                    'desc_tip'    => true,
                    'placeholder' => 'Example: 12'
                ),
                'customer_support_email' => array(
                    'title'       => 'Customer support email',
                    'type'        => 'email',
                    'default'     => '',
                    'description' => 'This is very important. Write the email with which you will support your users in case of a problem during the purchase process.',
                    'desc_tip'    => true,
                    'placeholder' => 'support@support.com'
                ),
            );

	 	}

		public function payment_fields() {

            if( $this->description ) {
                if( $this->testmode ) {
                    $this->description .= ' <span class="test-mode">TEST MODE ENABLED.</span> In test mode, you can use the card numbers listed in <a href="https://developers.paymentez.com/docs/payments/#javascript">documentation</a>.';
                    $this->description  = trim( $this->description );
                }
            
                echo wpautop( wp_kses_post( $this->description ) );
            }
            
            echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">';

                echo '<div id="user-cards" class="user-cards">';

                    echo '<div class="use-saved-cards-option">';
                        echo '<input type="checkbox" id="use_saved_cards" name="use_saved_cards" />';
                        echo '<label for="use_saved_cards">Deseo pagar con una de mis tarjetas</label>';
                    echo '</div>';
                    
                    do_shortcode('[show_user_cards use_card_button="true" delete_card_button="false"]');

                echo '</div>';
                    
              
                do_action( 'woocommerce_credit_card_form_start', $this->id );
                
                do_shortcode('[tokenization-form is_checkout="true"]');
            
                do_action( 'woocommerce_credit_card_form_end', $this->id );
        
            echo '<div class="clear"></div></fieldset>';

		}


	 	public function payment_scripts() {

        }

		public function validate_fields() {
            //wc_add_notice( 'Debug Info: ' . print_r( $_POST, true ), 'notice' );
        }

        
		public function process_payment( $order_id ) {

            //wc_add_notice( 'Checkout POST Method: ' . print_r( $_POST, true ), 'notice' );
           

            $cart = WC()->cart;
            $cart_items = $cart->get_cart();

            $subscription_id = null;
            
            foreach ($cart_items as $cart_item_key => $cart_item) {
                
                $product = $cart_item['data'];
                
                $product_id = isset($cart_item['product_id']) ? intval($cart_item['product_id']) : null;

                if (class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $product_id ) || $product->is_type('subscription') || $product->is_type( 'variable-subscription') ) {
                    $subscription_id = $product->get_id();
                }
                
            }
            

            $get_user_data = get_user_data();

            $user_id    = $get_user_data['user_id'];
            $user_email = $get_user_data['user_email'];
            $first_name = $get_user_data['firstname'];
            $last_name  = $get_user_data['lastname'];
            
            $user_data = [
                'user_id'    => $user_id,
                'user_email' => $user_email,
                'first_name' => $first_name,
                'last_name'  => $last_name,
            ];
            
          
            $card_token = $_POST['card_token'];
     
          
            $product_names = array();

            foreach ($cart_items as $cart_item_key => $cart_item) {
                $product = $cart_item['data'];
                $product_name = $product->get_name();
                $product_names[] = $product_name;
            }

            $order_description = $first_name . ' ' . $last_name . ' ' . 'has bought: ' . esc_html(implode(', ', $product_names)) . ' ' . 'products'; 
            
            $order_data = [
                'order_id'          => $order_id,
                'order_description' => $order_description,
                'order_subtotal'    => (float)$cart->get_subtotal() * 1.00,  // without taxes
                'order_total'       => (float)$cart->get_total('number') * 1.00, //with taxes
                'order_taxes'       => (float)$cart->get_total_tax() * 1.00, // only taxes
                'tax_percentage'    => (float)get_option('woocommerce_nuvei_settings')['tax_percentage'] * 1.00, // tax setted from plugin settings
            ];


            $excecute_debit = debit_with_token( $user_data, $card_token, $order_data );

            //wc_add_notice( 'Execute debit info: ' . print_r( $excecute_debit, true ), 'notice' );

            if ( $excecute_debit['status'] === 'success' ) {

                if (isset($_POST['use_saved_cards'])) {

                    $update_card = update_card_from_db( $user_id, $card_token, $subscription_id);
                    
                } else {
                    
                    $user_card_data = array(
                        "card_token"      => $card_token,
                        'subscription_id' => isset($subscription_id) ? $subscription_id : null,
                    );

                    //wc_add_notice( 'Debug Info: ' . print_r( $user_card_data, true ), 'notice' );
                    
                    save_card_into_db($user_id, $user_card_data);
                
                }
           
                $order = wc_get_order( $order_id );
        
                $transaction_data = $excecute_debit['transaction_data'];
                
                foreach ($transaction_data as $key => $value) {
                    $order->update_meta_data( '_nuvei_debit_' . $key, $value );
                }
                
                $meta_card_data = array(
                    'subscription_id' => $user_card_data['subscription_id']
                );
                
                update_post_meta( $order_id, '_user_card_data', $meta_card_data );

                $order->payment_complete();
                $order->reduce_order_stock();
                
                WC()->cart->empty_cart();           


                return array(
                    'result'   => 'success',
                    'redirect' => $this->get_return_url( $order )
                );
    

            } elseif ( $excecute_debit['status'] === 'pending' ) {

            } else {

                wc_add_notice(
                    '<p>
                        Error: No es posible realizar la transacción1. 
                        Por favor prueba con otra tarjeta, comunícate con tu banco o escribe a 
                        <a href="#" mailto="hi@hiitclub.online">hi@hiitclub.online</a> para recibir ayuda
                    </p>', 
                    'error' 
                );


                wp_delete_post($order_id,true);
                return;

            }

            wc_add_notice(
                '<p>
                    Error #2: No es posible realizar la transacción2. 
                    Por favor prueba con otra tarjeta, comunícate con tu banco o escribe a 
                    <a href="#" mailto="hi@hiitclub.online">hi@hiitclub.online</a> para recibir ayuda
                </p>', 
                'error' 
            );

            wp_delete_post($order_id,true);
            return;
            
        }
        
        public function woocommerce_scheduled_subscription_payment($amount_total, $renewal_order) {

          
            
            $user_id = $renewal_order->get_customer_id();
            
            foreach ( $renewal_order->get_items() as $item_id => $item ) {
                $product = $item->get_product();

                $product_id = $product->get_id();

                if (class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $product_id ) || $product->is_type('subscription') || $product->is_type( 'variable-subscription') ) {
                    $subscription_id = $product_id;
                }
            }

            
            global $wpdb;

            $table_name = $wpdb->prefix . 'user_cards';
                   
            $sql = $wpdb->prepare(
                "SELECT * FROM $table_name WHERE user_id = %d AND subscription_id = %d",
                $user_id,
                $subscription_id
            );
            
            $user_card = $wpdb->get_row($sql, ARRAY_A);

            if(!$user_card || empty($user_card)) {
                return;
            }

          
            $get_user_data = get_userdata($user_id);

            $user_data = [
                'user_id'    => $user_id,
                'user_email' => $get_user_data->user_email,
                'first_name' => $renewal_order->get_billing_first_name(),
                'last_name'  => $renewal_order->get_billing_last_name(),
            ];

           
            $card_token = $user_card['card_token'];

           
            $order_id = $renewal_order->get_id();
            
            //$order_description = $user_data['first_name'] . ' ' . $user_data['last_name'] . ' ' . 'has renewed the' . ' ' . $product_name . ' ' . 'subscription'; 
            $order_description = $user_data['first_name'] . ' ' . $user_data['last_name'] . ' ' . 'has renewed the subscription'; 
            
            $order_data = [
                'order_id'          => $order_id,
                'order_description' => $order_description,
                'order_subtotal'    => (float)$renewal_order->get_subtotal() * 1.00,  // without taxes
                'order_total'       => (float)$renewal_order->get_total('number') * 1.00, //with taxes
                'order_taxes'       => (float)$renewal_order->get_total_tax() * 1.00, // only taxes
                'tax_percentage'    => (float)get_option('woocommerce_nuvei_settings')['tax_percentage'] * 1.00, // tax setted from plugin settings
            ];

            
            $excecute_debit = debit_with_token($user_data, $card_token, $order_data );

            if ($excecute_debit['status'] === 'success') {

             
                $transaction_data = $excecute_debit['transaction_data'];

                $order = wc_get_order( $order_id );

                foreach ($transaction_data as $key => $value) {
                    $order->update_meta_data( '_nuvei_debit_' . $key, $value );
                }
                
               
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