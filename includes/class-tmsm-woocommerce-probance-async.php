<?php

if ( class_exists( 'WP_Async_Request' ) ) {

	class Tmsm_WooCommerce_Probance_Async extends WP_Async_Request {

		/**
		 * @var string
		 */
		protected $action = 'tmsm_woocommerce_probance_async';

		/**
		 * Contains an instance of the Probance API library, if available.
		 *
		 * @since  1.0.0
		 * @access protected
		 * @var    object $api If available, contains an instance of the Probance API library.
		 */
		private $api = null;

		/**
		 * @var Tmsm_WooCommerce_Probance_Integration
		 */
		public $options = null;

		/**
		 * Initializes Probance API if credentials are valid.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @uses   GFAddOn::get_plugin_setting()
		 * @uses   GFAddOn::log_debug()
		 * @uses   GFAddOn::log_error()
		 * @uses   GF_Probance_API::account_details()
		 *
		 * @return bool|null
		 */
		public function initialize_api( ) {

			include_once 'class-tmsm-woocommerce-probance-api.php';

			// If API is alredy initialized, return true.
			if ( ! is_null( $this->api ) ) {
				return true;
			}

			$password = $this->options->get_option('password');
			$username = $this->options->get_option('username');

			// If the API key is blank, do not run a validation check.
			if ( empty( $password ) || empty( $username ) ) {
				return null;
			}

			// Setup a new Probance object with the API credentials.
			$probance = new Tmsm_WooCommerce_Probance_API( $username,$password );

			try {

				// Assign API library to class.
				$this->api = $probance;

				// Log that authentication test passed.
				error_log( __METHOD__ . '(): Probance successfully authenticated.' );
				return true;

			} catch ( Exception $e ) {

				// Log that authentication test failed.
				error_log( __METHOD__ . '(): Unable to authenticate with Probance; ' . $e->getMessage() );

				return false;

			}

		}

		/**
		 * Handle
		 *
		 * Override this method to perform any actions required
		 * during the async request.
		 */
		public function handle() {

			$email = sanitize_email($_POST['billing_email']);
			$subscribe = false;
			$subscribe = isset( $_POST['tmsm_woocommerce_probance_optin'] ) ?? (int) $_POST['tmsm_woocommerce_probance_optin'];

			if(empty($email) || $subscribe == false){
				return;
			}

			if($this->initialize_api()){
                error_log('API OK !!');
				$member        = false;
				$member_found  = false;
				$member_status = null;
                try
                {
                    error_log('EMAIL');
                    error_log($email);

                   $member_search=  $this->api->get_member($email);
                    error_log('$response[\'body\']');
                    error_log(print_r($member_search, true));

                   if (!empty($member_search['client']['email']) && $member_search['client']['email'] == $email )
                   {
                       $member_found = true;
                       $member_status = $member_search['client']['optin_flag'];
                   }
                } catch ( Exception $e )
                {
                        error_log(print_r($e, true));
//                    if ( $e->hasErrors() ) {
//
//                        error_log( __METHOD__ . '(): Field errors when attempting subscription: ' . print_r( $e->getErrors(), true ) );
//                        $member_found = false;
//                    }
                }

				$action = $member_found ? 'update' : 'create';

				$merge_vars[ 'optin_flag'] = $_POST['tmsm_woocommerce_probance_optin'] ;
				$merge_vars[ 'email' ] = $email;

				if ( ! empty( $_POST['billing_last_name'] ) )
                {
					$merge_vars[ 'name1' ] = sanitize_text_field($_POST['billing_first_name']);
				}
				if ( ! empty( $_POST['billing_last_name'] ) )
                {
					$merge_vars[ 'name2' ] = sanitize_text_field($_POST['billing_last_name']);
				}

				if ( class_exists( 'Tmsm_Woocommerce_Billing_Fields_Public' ) && ! empty( $_POST['billing_title'] ) )
                {
					$title_options = Tmsm_Woocommerce_Billing_Fields_Public::billing_title_options();
					$title = $title_options[ sanitize_text_field( $_POST['billing_title'] ) ];

					if ( ! empty( $title ) )
                    {
						$merge_vars['gender'] = $title;
					}
				}
				if ( class_exists( 'Tmsm_Woocommerce_Billing_Fields_Public' ) && ! empty( $_POST['billing_birthdate'] ) )
                {
					$birthday_input = sanitize_text_field( $_POST['billing_birthdate'] );
					$objdate = DateTime::createFromFormat( _x( 'd/m/Y', 'birthday date format conversion', 'tmsm-woocommerce-billing-fields' ),
						$birthday_input );

					if ( $objdate instanceof DateTime )
                    {
						$merge_vars['birthday'] = $objdate->format( 'Y-m-d' );
					}
				}
                if (!$member_found || $member_status != $merge_vars[ 'optin_flag'] )
                {
                    error_log('NEW ONE TO COME OR MIND CHANGING !!!!');
					$serverDateTime = new DateTime('now', new DateTimeZone('Europe/Paris'));
					$date_to_time =  $serverDateTime->format('Y-m-d\TH:i:s.vO');
					 $merge_vars['registration_date'] = $date_to_time;
                }

                $params = $merge_vars;
                error_log('%%% WC PARAMS %%%');
                error_log(print_r($params, true));
				try {

					$response = $this->api->update_list_member( $params, $action );

					error_log('Probance subscriber created or updated');

				} catch ( Exception $e ) {

					error_log('Unable to create or update Probance subscriber');
					return;

				}

			}
		}
	}
}
