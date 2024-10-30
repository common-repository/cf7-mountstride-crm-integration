<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/vsourz1td/
 * @since      1.0.0
 *
 * @package    Cf7_To_Mountstride
 * @subpackage Cf7_To_Mountstride/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cf7_To_Mountstride
 * @subpackage Cf7_To_Mountstride/admin
 * @author     Vsourz <mehul@vsourz.com>
 */
class Cf7_To_Mountstride_Admin {

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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cf7_To_Mountstride_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cf7_To_Mountstride_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_style( 'cf7-to-mountstride-admin-css', plugin_dir_url( __FILE__ ) . 'css/cf7-to-mountstride-admin.css', array(), $this->version, 'all' );
		wp_register_style( 'jquery-ui-css', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css', array(), $this->version, 'all' );
		wp_register_style( 'font-awesome.min', plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css', array(), $this->version, 'all' );
		wp_register_style( 'bootstrap-min-css', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cf7_To_Mountstride_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cf7_To_Mountstride_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cf7-to-mountstride-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'cf72mot_admin_action', array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
		));
	}

	/**
	 * Initialize the menu
	 */
	public function cf72mut_api_settings_menu(){

		add_menu_page( "CF7 to mountstride", "CF7 to mountstride", 'manage_options', "cf72mut-api-settings", array( $this, "cf72mut_api_settings_menu_callback"), 'dashicons-visibility' , 999);

		add_submenu_page('cf72mut-api-settings', 'Fields Mapping', 'Fields Mapping', 'manage_options', 'cf72mut-mapping',array( $this, 'cf72mut_mapping_mut_fields_callback') );

	}

	/*
	 * Callback function for the setting screen
	 * View of the setting screen
	 */
	public function cf72mut_api_settings_menu_callback(){
		include_once( CF72MUT_PLUGIN_PATH."admin/partials/settings.php" );
	}

	/*
	 * Callback function for the CF7 2 Mountstride Fields Mapping screen
	 * View of the Fields Mapping screen
	 */

	public function cf72mut_mapping_mut_fields_callback(){
		include_once( CF72MUT_PLUGIN_PATH."admin/partials/mappings.php" );
	}

	/**
	 * Get all the contact form 7 list
	 * @returns : all the contact form 7 list
	 */
	public function get_all_cf7_forms(){

		$cf7Forms = array();
		$forms = WPCF7_ContactForm::find();
		foreach ($forms as $k => $v){
			//Check if form id not empty then get specific form related information
			$cf7Forms[] = $v;
	    }

	    return $cf7Forms;
	}

	/**
	 * Get all the contact form 7 fields
	 * @params $form_id - Contact form 7 ID
	 */
	public function get_cf7_forms_fields( $form_id = '' ){

		if( empty($form_id) ){
			return '';
		}

		//Get All form information
		$forms = WPCF7_ContactForm::find();
		//fetch each form information
		foreach ($forms as $k => $v){
			//Check if form id not empty then get specific form related information
			if($v->id() == $form_id){
				$cf7forms = $v;
				break;
			}
		}

		if(!empty($cf7forms) ){
			$arr_form_tags = $cf7forms->scan_form_tags();
			return $arr_form_tags;
		}
		else{
			return '';
		}
	}

	/**
	 * Template file included for form list to be viewed
	 */
	public function cf7_form_fields_template_callback(){

		include_once( CF72MUT_PLUGIN_PATH."admin/partials/cf7-form-fields-template.php" );

	}
	
	//display notification 
	public function cf7_plugin_notification(){
		global $current_screen;
		if($current_screen->id == 'cf7-to-mountstride_page_cf72mut-mapping'){
			?><div class="updated notice notice-success">
				<p ><?php _e( "Kindly set 'Current date' as to send the current submission date to the CRM.", CF72MUT_TEXT_DOMAIN ); ?>
				<br/>
				<?php _e( "Note : Set 'Current Date' against the 'Received Date' if you do not  have any field to associate.", CF72MUT_TEXT_DOMAIN ); ?>
				</p>
			</div><?php	


		}
	}

}
