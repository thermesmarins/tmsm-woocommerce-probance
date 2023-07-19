<?php
	
if ( ! class_exists( 'Tmsm_WooCommerce_Probance_Integration' ) ) :

class Tmsm_WooCommerce_Probance_Integration extends WC_Integration {
	
	/**
	 * Init and hook in the integration.
	 */
	public function __construct() {
		
		global $woocommerce;
		
		$this->id                 = 'tmsm_woocommerce_probance';
		$this->method_title       = __( 'Tmsm WooCommerce Probance', 'tmsm-woocommerce-probance' );
		$this->method_description = __( 'Allow buyers to optin for a Probance list', 'tmsm-woocommerce-probance' );
		
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		
		// Define variables.
		$this->username          = $this->get_option( 'username' );
		$this->password  = $this->get_option( 'password' );
		$this->checkbox_label  = $this->get_option( 'checkbox_label' );
		$this->checkbox_action  = $this->get_option( 'checkbox_action' );

		// Actions.
		add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );
	}
	
	/**
	 * Initialize integration settings form fields.
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'username' => array(
				'title'             => __( 'username', 'tmsm-woocommerce-probance' ),
				'type'              => 'text',
				'description'       => __( 'Enter your Username', 'tmsm-woocommerce-probance' ),
				'desc_tip'          => true,
				'default'           => ''
			),

			'password' => array(
				'title'             => __( 'password', 'tmsm-woocommerce-probance' ),
				'type'              => 'text',
				'description'       => __( 'Enter your Password', 'tmsm-woocommerce-probance' ),
				'desc_tip'          => true,
				'default'           => ''
			),

//			'project_id' => array(
//				'title'             => __( 'Project ID', 'tmsm-woocommerce-probance' ),
//				'type'              => 'text',
//				'description'       => __( 'Enter your project ID', 'tmsm-woocommerce-probance' ),
//				'desc_tip'          => true,
//				'default'           => ''
//			),

//TODO voir si necessaire pour le mappage du champs

//			'list_id' => array(
//				'title'             => __( 'Optin Field', 'tmsm-woocommerce-probance' ),
//				'type'              => 'text',
//				'description'       => __( 'Enter your optin field', 'tmsm-woocommerce-probance' ),
//				'desc_tip'          => true,
//				'default'           => ''
//			),

			// 'checkbox_label' => array(
			// 	'title'             => __( 'Checkbox Label', 'tmsm-woocommerce-probance' ),
			// 	'type'              => 'text',
			// 	'desc_tip'          => true,
			// 	'default'           => __( 'Subscribe to our newsletter', 'tmsm-woocommerce-probance' )
			// ),

			// 'checkbox_action' => array(
			// 	'title'             => __( 'Checkbox Action', 'tmsm-woocommerce-probance' ),
			// 	'type'              => 'text',
			// 	'desc_tip'          => true,
			// 	'description'       => __( 'Action that display the checkbox', 'tmsm-woocommerce-probance' ),
			// 	'default'           => 'woocommerce_after_checkout_billing_form'
			// ),


		);
	}
}

endif;