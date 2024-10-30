<?php

/**
 * CF7 to Mountstride CRM Settings
 *
 *
 * @link       https://profiles.wordpress.org/vsourz1td/
 * @since      1.0.0
 *
 * @package    Cf7_To_Mountstride
 * @subpackage Cf7_To_Mountstride/admin/partials
 */
?>
<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	die('Un-authorized access!');
}

/**
 * Detect plugin. For use in Admin area only.
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

//Check contact form class exist or not
if(!is_plugin_active('contact-form-7/wp-contact-form-7.php')){
	?><div class="wrap"><div class="notice error">
		<p><?php _e( 'Please activate Contact Form plugin first.', CF72MUT_TEXT_DOMAIN ); ?></p>
	</div></div><?php
	return;
}
else if(defined('WPCF7_VERSION') && WPCF7_VERSION < '4.6'){
	?><div class="wrap"><div class="notice error">
		<p><?php _e( 'Please update latest version for Contact Form plugin first.', CF72MUT_TEXT_DOMAIN ); ?></p>
	</div></div><?php
	return;
}

wp_enqueue_style( 'font-awesome.min' );
wp_enqueue_style( 'bootstrap-min-css' );
wp_enqueue_style( 'jquery-ui-css' );
wp_enqueue_style( 'cf7-to-mountstride-admin-css' );

//Saving to the database starts from here
$options_updated = false;
$options_error = false;
$error_message = array();
if( isset( $_POST['cf72mut_setting_submit'] ) && $_POST['cf72mut_setting_submit'] == "Save Settings"  ){

	$ch_cat_arr = array();

	if(check_admin_referer( 'update_cf72mut_settings','ch_setting_nonce')){

		if(isset( $_POST['cf72mot_api_enable'])){

			update_option( 'cf72mot_api_enable', sanitize_text_field( $_POST['cf72mot_api_enable'] ) );
			if( isset( $_POST['cf72mot_api_url'] ) && !empty( $_POST['cf72mot_api_url'] ) ){
				update_option( 'cf72mot_api_url', sanitize_text_field( $_POST['cf72mot_api_url'] ) );
			}
			else{
				$options_error = true;
				$error_message[] = 'Kindly add the API Endpoint.';
			}

			if(isset($_POST['cf72mot_authorization_key'] ) && !empty( $_POST['cf72mot_authorization_key'] ) ){
				update_option( 'cf72mot_authorization_key', sanitize_text_field( $_POST['cf72mot_authorization_key'] ) );
			}
			else{
				$options_error = true;
				$error_message[] = 'Kindly add the Authorization key.';
			}

			if( isset( $_POST['cf72mot_token_key'] ) && !empty( $_POST['cf72mot_token_key'] ) ){
				update_option( 'cf72mot_token_key', sanitize_text_field( $_POST['cf72mot_token_key'] ) );
			}
			else{
				$options_error = true;
				$error_message[] = 'Kindly add the Token key.';
			}

			if(isset($_POST['cf72mot_log_enable'])){
				update_option( 'cf72mot_log_enable', sanitize_text_field( $_POST['cf72mot_log_enable'] ) );
			}
			else{
				update_option( 'cf72mot_log_enable', '');
			}

			if(!$options_error){
				$options_updated = true;
			}
		}
		else{
			update_option( 'cf72mot_api_enable', '');
			$arr_option = array('cf72mot_api_url','cf72mot_authorization_key','cf72mot_token_key');
			foreach($arr_option as $opt_key){
				update_option($opt_key, trim(sanitize_text_field($_POST[$opt_key])));
			}

			if(isset($_POST['cf72mot_log_enable'])){
				update_option( 'cf72mot_log_enable', sanitize_text_field( $_POST['cf72mot_log_enable'] ) );
			}
			else{
				update_option( 'cf72mot_log_enable', '');
			}

			$options_updated = true;
		}
	}
	else{
		$options_error = true;
	}

}
?>
<style type="text/css">
	.wrap{
		background: #fff;
		padding: 20px;
		width: 600px;
	}
	.ch_submit_btn{
		text-align: left;
		padding-top: 20px;
		display: block;
    	justify-content: space-between;
	}
	.ch-fields{
		font-size: 13px;
		line-height: 1.4em;
		margin-bottom: 10px;
	}
	.ch-fields label{
		width: 30%;
		display: inline-block;
	}

	/* Tooltip container */
	.tooltip {
	  position: relative;
	  display: inline-block;
	  border-bottom: 1px dotted black; /* If you want dots under the hoverable text */
	}

	/* Tooltip text */
	.tooltip .tooltiptext {
	  visibility: hidden;
	  width: 120px;
	  background-color: #555;
	  color: #fff;
	  text-align: center;
	  padding: 5px 0;
	  border-radius: 6px;

	  /* Position the tooltip text */
	  position: absolute;
	  z-index: 1;
	  bottom: 125%;
	  left: 50%;
	  margin-left: -60px;

	  /* Fade in tooltip */
	  opacity: 0;
	  transition: opacity 0.3s;
	}

	/* Tooltip arrow */
	.tooltip .tooltiptext::after {
	  content: "";
	  position: absolute;
	  top: 100%;
	  left: 50%;
	  margin-left: -5px;
	  border-width: 5px;
	  border-style: solid;
	  border-color: #555 transparent transparent transparent;
	}

	/* Show the tooltip text when you mouse over the tooltip container */
	.tooltip:hover .tooltiptext {
	  visibility: visible;
	  opacity: 1;
	}

	small.tooltip {
	    background-color: #9ca1a6;
	    border-radius: 50%;
	    font-size: 12px;
	    width: 10%;
	    text-align: center;
	    color: #ffffff;
        border: none;
	}
	.wp-core-ui .notice.is-dismissible {
	    padding-right: 38px;
	    position: relative;
	    background-color: #f2f2f2f2;
	}
	span .vsz-reset{
		color: #ffffff;
		text-decoration: none;
		display: inline;
	}
	.button.button-primary.bdr-15{
		border-radius: 15px;
	}
</style>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h3><?php _e( 'CF7 to mountstride CRM API Settings', CF72MUT_TEXT_DOMAIN ); ?></h3>
    <p><?php _e( '', CF72MUT_TEXT_DOMAIN ); ?></p>
    <hr></hr>
    <?php
    	if( $options_updated ){
    		echo '<div class="notice notice-success is-dismissible"><p>Settings saved successfully</p></div>';
    	}
    	if( $options_error ){
    		if(!empty( $error_message)){
    			foreach ($error_message as $msg) {
    				echo '<div class="notice notice-error is-dismissible"><p>'.$msg.'</p></div>';
    			}
    		}
			else{
    			echo '<div class="notice notice-error is-dismissible"><p>You are not authorized to do any actions.</p></div>';
    		}
    	}

    ?><form name="update_cf72mut_settings" id="update_cf72mut_settings" class="" method="post" action="<?php echo admin_url('admin.php?page=cf72mut-api-settings'); ?>">
    	<?php
    		wp_nonce_field( 'update_cf72mut_settings','ch_setting_nonce' );
    	?>

    	<!-- Enable API -->
    	<div class="ch-fields login-enable crm-api-sec">
    		<label for="cf72mot_api_enable"><?php _e( 'Enable mountstride CRM API', CF72MUT_TEXT_DOMAIN ); ?></label>
    		<input type="checkbox" <?php if( get_option('cf72mot_api_enable') ){ echo "checked='checked'"; } ?> name="cf72mot_api_enable" id="cf72mot_api_enable" value="yes" class="regular-text code" >
    	</div>

    	<!-- API URL -->
    	<div class="ch-fields">
    		<label for="cf72mot_api_url"><?php _e( 'API Endpoint*', CF72MUT_TEXT_DOMAIN ); ?></label>
    		<input type="text" name="cf72mot_api_url" id="cf72mot_api_url" value="<?php echo get_option('cf72mot_api_url'); ?>" class="regular-text code" >
    	</div>

    	<!-- Authorization Key -->
    	<div class="ch-fields">
    		<label for="cf72mot_authorization_key"><?php _e( 'Authorization Key*', CF72MUT_TEXT_DOMAIN ); ?></label>
    		<input type="text" name="cf72mot_authorization_key" id="cf72mot_authorization_key" value="<?php echo get_option('cf72mot_authorization_key'); ?>" class="regular-text code" >
    	</div>

    	<!-- Token Key -->
		<div class="ch-fields">
    		<label for="cf72mot_token_key"><?php _e( 'Token Key*', CF72MUT_TEXT_DOMAIN ); ?></label>
    		<input type="text" name="cf72mot_token_key" id="cf72mot_token_key" value="<?php echo get_option('cf72mot_token_key'); ?>" class="regular-text code" >
    	</div>

    	<!-- Enable Log -->
    	<div class="ch-fields login-enable">
    		<label for="cf72mot_log_enable"><?php _e( 'Enable Log', CF72MUT_TEXT_DOMAIN ); ?></label>
    		<input type="checkbox" <?php if( get_option('cf72mot_log_enable') ){ echo "checked='checked'"; } ?> name="cf72mot_log_enable" id="cf72mot_log_enable" value="yes" class="regular-text code" ><?php
			if(file_exists(ABSPATH.'wp-content/uploads/mountstride-logs/api-log.txt')){
				?><span style="margin-left:15px;" ><a href="<?php print content_url().'/uploads/mountstride-logs/api-log.txt';?>" title="View debug log" target="_blank"><?php _e( 'View debug log', CF72MUT_TEXT_DOMAIN ); ?></a></span><?php
			}
		?></div>

    	<!-- Submit form -->
    	<div class="ch_submit_btn">
    		<input type="submit" class="button button-primary save-settings" name="cf72mut_setting_submit" id="cf72mut_setting_submit" value="Save Settings">

    	</div>
    </form>
</div>

