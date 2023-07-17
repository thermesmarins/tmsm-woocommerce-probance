<?php

/**
 * TMSM WooCommerce Probance API Library.
 *
 * @since     1.0.0
 * @author    Nicolas Mollet
 */
class Tmsm_WooCommerce_Probance_API {

	/**
	 * Probance account API key.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $usename Probance account username.
	 */
	protected $username;

	/**
	 * Probance account Key ID.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $password Probance account password.
	 */
	protected $password;

	/**
	 * Probance webservice url.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $webservice_url Probance webservice url.
	 */
	protected $webservice_url = 'https://lesthermesmarins.my-probance.one/rt/api/resource/client/lesthermesmarins_lesthermesmarins/';

	/**
	 * Initialize API library.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $username (default: '') Probance username.
	 * @param string $password  (default: '') Probance password.
	 *
	 */
	public function __construct( $username = '', $password = '' ) {

		// Assign API key to object.
		$this->username = $username;
		$this->password  = $password;

	}

	/**
	 * Get a specific Probance list member.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $email
	 *
	 * @uses   GF_Probance_API::process_request()
	 * @throws Exception
	 *
	 * @return array
	 */
	public function get_member( $email ) {
        $data = array('email' => $email);
        error_log('DATA GET MEMBER');
        error_log(print_r($data, true));
		// Prepare subscriber hash.
        $response = $this->process_request( 'search', 'GET', $data);

		return $response;

	}

	/**
	 * Add or update a Probance list member.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param array $params Params.
	 *
	 * @uses   GF_Probance_API::process_request()
	 * @throws Exception
	 *
	 * @return array
	 */
	public function update_list_member( $params, $action ) {

		$response = $this->process_request( $action, 'POST', $params );

		return $response;

	}

	/**
	 * Process Probance API request.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param string $service    Request path.
	 * @param string $method     Request method. Defaults to GET.
	 * @param array  $data       Request data.
	 * @param string $return_key Array key from response to return. Defaults to null (return full response).
	 *
	 * @throws Exception if API request returns an error, exception is thrown.
	 *
	 * @return array
	 */
	private function process_request( $service = '', $method = 'GET', $data= array(), $return_key = null ) {

		// If API key is not set, throw exception.
		if ( empty( $this->username ) ) {
			throw new Exception( 'Username must be defined to process an API request.' );
		}

		// If Key ID is not set, throw exception.
		if ( empty( $this->password ) ) {
			throw new Exception( 'Password must be defined to process an API request.' );
		}
        // Set credentials for Basic Authentication
        $credentials = base64_encode("$this->username:$this->password");

        // Add and manage the services for the request url
        if ($service == 'update'|| $service == 'search')
        {
            $request_url = $this->webservice_url . $service .'?email=' . $data['email'];

        } else
         {
            // Build base request URL.
            $request_url = $this->webservice_url . $service;

        }

        error_log('REQUEST URL');
        error_log($request_url);
        // Set header for requests
        $headers = array(
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => "Basic {$credentials}",
        );
//        error_log('HEADER');
//        error_log(print_r($headers, true));
        if ($method != 'GET') {
//            error_log('DATA POST');
//            error_log(print_r($data, true));
            $json_data = json_encode($data);
//            error_log('$json_data');
//            error_log($json_data);

            $response = wp_remote_request(
                $request_url,
                array(
                    'method'  => $method,
                    'headers' => $headers,
                    'body'    => $json_data,
                    'sslverify' => apply_filters('https_local_ssl_verify', false),
                    'timeout' => apply_filters('http_request_timeout', 30),
                ));
        } else {
            // For 'GET' need to pass the method in the header
            $method = array('method' => $method);
            $headers = array_merge($headers , $method);
            // Build base request arguments.
            $args = array(
                'headers' => $headers,
                'sslverify' => apply_filters('https_local_ssl_verify', false),
                'timeout' => apply_filters('http_request_timeout', 30),
            );
//            error_log('ARGS IN GET');
//            error_log(print_r($args, true));
            $response = wp_remote_request($request_url, $args);
        }

		// If request was not successful, throw exception.
		if ( is_wp_error( $response ) ) {
			throw new Exception( $response->get_error_message() );
		}

		// Decode response body.
		$response['body'] = json_decode( $response['body'], true );

		// Get the response code.
		$response_code = wp_remote_retrieve_response_code( $response );
        error_log('BODY RESPONSE');
        error_log(print_r($response['body'], true ));
		if ( $response_code != 200 ) {

			// If status code is set, throw exception.
			if ( isset( $response['body']['status'] ) && isset( $response['body']['title'] ) ) {

				// Initialize exception.
				$exception = new Exception( $response['body']['title'], $response['body']['status'] );

				// Add detail.
				// $exception->setDetail( $response['body']['detail'] );

				// Add errors if available.
				// if ( isset( $response['body']['errors'] ) ) {
				// 	$exception->setErrors( $response['body']['errors'] );
				// }

				throw $exception;

			}

			throw new Exception( wp_remote_retrieve_response_message( $response ), $response_code );

		}
//        $http_response_object = $response['http_response']->get_response_object();


//        if(!($http_response_object->success == true)){
//            error_log('RESPONSE HTTP');
//            error_log(print_r($response['http_response']->get_response_object()->success, true));
////			$exception = new Exception( $response['body']['ErrorMessage'] );
////			throw $exception;
//            $exception =  new WP_Error( 'broke', __( "I've fallen and can't get up", "my_textdomain" ) );
//            error_log(print_r($exception, true)) ;
//            exit;
//		}

//		 Remove links from response.
		unset( $response['body']['_links'] );

//		 If a return key is defined and array item exists, return it.
		if ( ! empty( $return_key ) && isset( $response['body'][ $return_key ] ) ) {
			return $response['body'][ $return_key ];
		}

		return $response['body'];

	}

}

