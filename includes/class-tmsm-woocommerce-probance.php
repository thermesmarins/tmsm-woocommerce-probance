<?php

/**
 * # WooCommerce Tmsm WooCommerce Probance Actions
 *
 * @since 1.0
 */

class Tmsm_WooCommerce_Probance_Actions {

	/**
	 * Constructor
	 */
	public function __construct() {

	}

	/**
	 * Instance of this class.
	 *
	 * @var object Class Instance
	 */
    private static $instance;

	/**
     * Get the class instance
	 *
	 * @return Tmsm_WooCommerce_Probance_Actions
	 */
    public static function get_instance() {
        return null === self::$instance ? ( self::$instance = new self ) : self::$instance;
    }

	/**
	 * Localisation.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'tmsm-woocommerce-probance', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Display Radio in Billing Form
	 */
	public function apply_checkbox(){
        //TODO change here for radio buttons
		$integration = new Tmsm_WooCommerce_Probance_Integration();
        $radio_label_yes = esc_html($integration->get_option( 'radio_label_yes', __( 'Oui', 'tmsm-woocommerce-probance' ) ));
        $radio_label_no = esc_html($integration->get_option( 'radio_label_no', __( 'Non', 'tmsm-woocommerce-probance' ) ));
        $radio = '<p> Souscrire à la newsletter </p>';
		$radio .= '<p class="form-row form-row-wide tmsm-woocommerce-probance-optin">';
		$radio .= '<label for="tmsm_woocommerce_probance_optin" class="woocommerce-form__label woocommerce-form__label-for-radio inline">';
		$radio .= '<input class="woocommerce-form__input woocommerce-form__input-radio input-radio" id="tmsm_woocommerce_probance_optin" type="radio" name="tmsm_woocommerce_probance_optin" value="1"> ';
		$radio .= '<span>'. $radio_label_yes . '</span>';
		$radio .= '<br>';
        $radio .= '<input class="woocommerce-form__input woocommerce-form__input-radio input-radio" id="tmsm_woocommerce_probance_optin" type="radio" name="tmsm_woocommerce_probance_optin" value="0"> ';
        $radio .= '<span>'. $radio_label_no . '</span>';
        $radio .= '</label>';
        $radio .= '</p>';
		echo $radio;
	}

	/**
	 * Process Radio in Billing Form
	 *
	 * @param int $order_id
	 * @param array $posted
	 * @param WC_Order $order
	 */
	public function process_checkbox( $order_id, $posted, $order ) {

		$status = isset( $_POST['tmsm_woocommerce_probance_optin'] ) ? (int) $_POST['tmsm_woocommerce_probance_optin'] : 0;

		if ( ! empty( $order_id ) ) {
			update_post_meta( $order_id, 'tmsm_woocommerce_probance_optin', $status );
		}
	}

	/**
	* Gets the absolute plugin path without a trailing slash, e.g.
	* /path/to/wp-content/plugins/plugin-directory
	*
	* @return string plugin path
	*/
	public function get_plugin_path() {
		return $this->plugin_path = untrailingslashit( plugin_dir_path( dirname( __FILE__ ) ) );
	}
}
