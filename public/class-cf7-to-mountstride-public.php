<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/vsourz1td/
 * @since      1.0.0
 *
 * @package    Cf7_To_Mountstride
 * @subpackage Cf7_To_Mountstride/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Cf7_To_Mountstride
 * @subpackage Cf7_To_Mountstride/public
 * @author     Vsourz <mehul@vsourz.com>
 */
class Cf7_To_Mountstride_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;


	//define API related variable from here
	//define class variable from here
	var $cf72mot_api_enable;
	var $cf72mot_api_url;
	var $cf72mot_authorization_key;
	var $cf72mot_token_key;
	var $cf72mot_log_enable;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		//get all option fields value and assign in class
		$this->cf72mot_api_enable = get_option('cf72mot_api_enable');
		$this->cf72mot_api_url = get_option('cf72mot_api_url');
		$this->cf72mot_authorization_key = get_option('cf72mot_authorization_key');
		$this->cf72mot_token_key = get_option('cf72mot_token_key');
		$this->cf72mot_log_enable = get_option('cf72mot_log_enable');

	}

	/**
	 * Send the Contact from 7 submitted data to CRM before sending the email
	 *
	 */

	public function cf7_to_mountstride_before_send_email($contact_form){

		//define current contact form Id in variable from here
		$fId = $contact_form->id();

		//restrict any contact form entries, which from data not submitted in API
		$arr_exclude_form_ids = (array) apply_filters('cf7_to_mountstride_exclude_form_id',array());

		if(!empty($arr_exclude_form_ids) && in_array($fId,$arr_exclude_form_ids)){
			return $contact_form;
		}

		//get API related all settings from here
		// Live API Details
		$cf72mot_api_enable = $this->cf72mot_api_enable;
		$cf72mot_api_url =  $this->cf72mot_api_url;
		$cf72mot_authorization_key  = $this->cf72mot_authorization_key;
		$cf72mot_token_key =  $this->cf72mot_token_key;
		$cf72mot_log_enable =  $this->cf72mot_log_enable;

		//check all settings configure properly or not else return from here
		if(empty($cf72mot_api_enable) || empty($cf72mot_api_url) || empty($cf72mot_authorization_key) || empty($cf72mot_token_key)){
			return $contact_form;
		}

		//Get form related mapping fields settings from here
		$arr_fields = get_option('cf7mount_api_fields_mapping');

		$arr_data = array();

		 // we have to retrieve it from an API
        if(!isset($cf7->posted_data) && class_exists('WPCF7_Submission')){
			// we have to retrieve it from an API
			$submission = WPCF7_Submission::get_instance();
			$arr_data = $submission->get_posted_data();
		}

		//check form related settings available or not
		if(!empty($arr_data) && !empty($arr_fields) && isset($arr_fields[$fId]) && !empty($arr_fields[$fId])){

			//get common and master fields value from here
			$commonFields = isset($arr_fields[$fId]['common_fields']) && !empty($arr_fields[$fId]['common_fields']) ? $arr_fields[$fId]['common_fields'] : array();
			$masterFields = isset($arr_fields[$fId]['master_fields']) && !empty($arr_fields[$fId]['master_fields']) ? $arr_fields[$fId]['master_fields'] : array();

			//check current form data pass with API or not
			if(!empty($arr_fields) && isset($arr_fields[$fId]['enable_form']) && empty($arr_fields[$fId]['enable_form'])) return $contact_form;

			//customize common data details from here
			$commonFields = (array) apply_filters('cf7_to_mountstride_before_add_common_data',$commonFields,$fId);

			$arr_api_data = array();
			$arr_dom_fields = array('Note');
			
			//combine common fields data in final array
			if(!empty($commonFields)){

				$groupSeparator = (string) apply_filters('cf7_to_mountstride_group_field_separator','<br/>');
				foreach($commonFields as $apiKey => $cfKey){

					if(!empty($arr_dom_fields) && in_array($apiKey,$arr_dom_fields)&& is_array($cfKey) && !empty($cfKey)){
						$noteVal = '';
						foreach($cfKey as $innKey => $noteCfKey){
							
							if($noteCfKey == 'vsz_current_date_mapping'){
								$noteVal .= 'Submission Date: '.date('Y-m-d').$groupSeparator;
							}
							else{
								$noteVal .= isset($arr_data[$noteCfKey]) && !empty($arr_data[$noteCfKey]) ? $noteCfKey.': '.$arr_data[$noteCfKey].$groupSeparator : '';	
							}
						}
						$arr_api_data[$apiKey] = $noteVal;
					}
					else if($cfKey == 'vsz_current_date_mapping'){
						$arr_api_data[$apiKey] = date('Y-m-d');
					}
					else{
						$arr_api_data[$apiKey] = isset($arr_data[$cfKey]) && !empty($arr_data[$cfKey]) ? $arr_data[$cfKey] : '';
					}
				}
			}

			//customize master data details from here
			$masterFields = (array) apply_filters('cf7_to_mountstride_before_add_master_data',$masterFields,$fId);

			//combine master fields data in final array
			if(!empty($masterFields)){
				foreach($masterFields as $apiKey => $val){

					if(!empty($val) && isset($arr_data[$val]) && !empty($arr_data[$val])){
						$arr_api_data['MasterData'][$apiKey][] = $arr_data[$val];
					}
					else if($val == 'vsz_current_date_mapping'){
						$arr_api_data['MasterData'][$apiKey][] = date('Y-m-d');
					}
					else{
						$arr_api_data['MasterData'][$apiKey][] = !empty($val) ? $val : '';
					}

				}
			}
			
			//define filter for modify data before send to API
			$arr_api_data = (array) apply_filters('cf7_to_mountstride_before_send_api_data',$arr_api_data,$fId);

			//check return array exist value or not
			if(!empty($arr_api_data)){

				//add token in API request from here
				$arr_api_data['doAction'] = 'addLead';
				$arr_api_data['token'] = $cf72mot_token_key;

				//add API log data from here
				$data = json_encode($arr_api_data);

				//send data to API from here
				$response_data = $this->cf7_to_mountstride_send_api_data($data);

				do_action('cf7_to_mountstride_after_send_api_data',$response_data,$arr_api_data,$fId);
			}
		}
	}

	//call curl from here
	public function cf7_to_mountstride_send_api_data($JSONString=""){

		if(empty($JSONString)) return;

		//get API related all settings from here
		// Live API Details
		$cf72mot_api_enable = $this->cf72mot_api_enable;
		$cf72mot_api_url =  $this->cf72mot_api_url;
		$cf72mot_authorization_key  = $this->cf72mot_authorization_key;
		$cf72mot_token_key =  $this->cf72mot_token_key;
		$cf72mot_log_enable =  $this->cf72mot_log_enable;

		//check all settings configure properly or not else return from here
		if(!empty($cf72mot_api_enable) && !empty($cf72mot_api_url) && !empty($cf72mot_authorization_key) && !empty($cf72mot_token_key)){


			if(!empty($cf72mot_log_enable)){
				// The new api request add to the file
				$objDateTime = new DateTime('NOW');
				$reqstMsg = 'Send Data to API:-';
				$insertData = $objDateTime->format('Y-m-d H:i:s') . " " . $reqstMsg ." {";
				$insertData .= "Request Data:".$JSONString."\n";
				$insertData .= "}\r\n";
				$this->cf7_to_mountstride_api_log($insertData);
			}


			//call API from here
			$api_response = wp_remote_post( trim($cf72mot_api_url),array(
							'headers' => array(
											"Authorization" => $cf72mot_authorization_key,
											"cache-control" => "no-cache",
											),
						'body' => trim($JSONString),
				));


			//check request related response and add in log file
			$response_code    = wp_remote_retrieve_response_code( $api_response );
			$response_message = wp_remote_retrieve_response_message( $api_response );
			$body_info = json_decode( wp_remote_retrieve_body( $api_response ), true );
			
			if(!empty($cf72mot_log_enable)){

				if(is_wp_error( $api_response )){
				   $error_message = $api_response->get_error_message();
					//////////// START :: Decode Html Entities /////////////////
					// The new api request add to the file
					$objDateTime = new DateTime('NOW');
					$reqstMsg = 'Error Response Mountstride API:-';
					$insertData = $objDateTime->format('Y-m-d H:i:s') . " " . $reqstMsg ." {";
					$insertData .= $error_message;
					$insertData .= "}\r\n";
					$this->cf7_to_mountstride_api_log($insertData);
				}
				else if(200 != $response_code && !empty($response_message)){

					//////////// START :: Decode Html Entities /////////////////
					// The new api request add to the file
					$objDateTime = new DateTime('NOW');
					$reqstMsg = 'Response Mountstride API:-';
					$insertData = $objDateTime->format('Y-m-d H:i:s') . " " . $reqstMsg ." {";
					$insertData .= "Response Code:".$response_code."\n";
					$insertData .= "Response Message:".$response_message."\n";
					$insertData .= "}\r\n";
					$this->cf7_to_mountstride_api_log($insertData);
				}
				elseif ( 200 != $response_code ) {
					//////////// START :: Decode Html Entities /////////////////
					// The new api request add to the file
					$objDateTime = new DateTime('NOW');
					$reqstMsg = 'Response Mountstride API:-';
					$insertData = $objDateTime->format('Y-m-d H:i:s') . " " . $reqstMsg ." {";
					$insertData .= "Response Code:".$resCode."\n";
					$insertData .= "Response Body:".$api_response['body']."\n";
					$insertData .= "}\r\n";
					$this->cf7_to_mountstride_api_log($insertData);
				}
				else{
					// The new api request add to the file
					$objDateTime = new DateTime('NOW');
					$reqstMsg = 'Response Mountstride API:-';
					$insertData = $objDateTime->format('Y-m-d H:i:s') . " " . $reqstMsg ." {";
					$insertData .= "Response Code:".$resCode."\n";
					$insertData .= "Response Body:".$api_response['body']."\n";
					$insertData .= "}\r\n";
					$this->cf7_to_mountstride_api_log($insertData);
				}
			}
			//add return data related code here
			return $body_info;

		}//close if for validate API settings

	}

	//define log file function from here
	public function cf7_to_mountstride_api_log($insertData=""){

		//$objDateTime = new DateTime('NOW');
		//$fileName = $objDateTime->format('d-m-Y');
		// File path
		$upload_dir   = wp_upload_dir();
		if (!file_exists($upload_dir['basedir'].'/mountstride-logs')) {
		    wp_mkdir_p($upload_dir['basedir'].'/mountstride-logs');
		}

		// set path and name of log file (optional)
		$logPath = $upload_dir['basedir'].'/mountstride-logs/api-log.txt';

		// Write the contents to the file,
		$mode = (!file_exists($logPath)) ? 'w':'a+';
		$logfile = fopen($logPath, $mode);
		fwrite($logfile, "\r\n". $insertData);
		fclose($logfile);

	}

}
