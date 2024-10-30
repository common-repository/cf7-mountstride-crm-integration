<?php

/**
 * CF7 to mountstride CRM Form fields Mappings
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


$mountstride_fields = array();
if(function_exists('cf7_to_mountstride_api_fields')){
	$mountstride_fields = cf7_to_mountstride_api_fields();
}



//Saving to the database starts from here
$options_updated = false;

//get all contact form list
$cf7forms = $this->get_all_cf7_forms();

$arr_dom_fields = array('Note');
function sanitize_array_field( $meta_value ) {

  foreach ( (array) $meta_value as $k => $v ) {
    if ( is_array( $v ) ) {
      $meta_value[$k] =  sanitize_array_field( $v );
    } else {
      $meta_value[$k] = sanitize_text_field( $v );
    }
  }

  return $meta_value;

}
//save process define here
if(isset( $_POST['cf72mot_mapping_submit'] ) && !empty($_POST['cf72mot_mapping_submit']) && isset($_POST['current_form_id']) && !empty($_POST['current_form_id'])){

	$arr_common_fields = array();
	$arr_master_fields = array();
	$arr_final_array = array();

	$current_form_id = (int) isset($_POST['current_form_id']) && !empty($_POST['current_form_id']) ? trim(sanitize_text_field($_POST['current_form_id'])) : '';

	//get common fields related data
	if(isset($_POST['cf7mount_common_fields']) && !empty($_POST['cf7mount_common_fields']) && isset($_POST['cf7mount_common_fields'])){
		$arr_common_fields = sanitize_array_field($_POST['cf7mount_common_fields']);
	}

	//get master fields related data
	if(isset($_POST['cf7mount_master_fields']) && !empty($_POST['cf7mount_master_fields'])){
		$arr_master_fields = sanitize_array_field($_POST['cf7mount_master_fields']);
	}

	//combine data in one array
	if(!empty($arr_common_fields)){
		foreach($arr_common_fields as $fId => $innArray){
			$arr_api = $innArray['api_key'];
			$arr_cf7 = $innArray['cf7_key'];
			$arr_notes = $innArray['notes'];
			foreach($arr_api as $key =>  $api_name){

				if(empty($api_name)) continue;

				if(!empty($arr_dom_fields) && in_array($api_name,$arr_dom_fields)){

					if($api_name == 'Note'){
						$arr_final_array['common_fields'][$api_name] =  array_values(array_filter($arr_cf7['notes']));
					}
				}
				else{
					if(empty($arr_cf7[$key])) continue;
					$arr_final_array['common_fields'][$api_name] = sanitize_text_field($arr_cf7[$key]);
				}
			}

			//Save notes
			$arr_final_array['common_fields']['Note'] =  array_values(array_filter($arr_notes));
		}
	}

	if(!empty($arr_master_fields)){

		foreach($arr_master_fields as $fId => $innArray){
			if($fId == 'formID') continue;
			foreach($innArray as $key =>  $arr_val){
				if(empty($arr_val['name'])) continue;
				if(empty($arr_val['opt_value']) && empty($arr_val['txt_value'])) continue;

				if(!empty($arr_val['opt_value'])){
					$arr_final_array['master_fields'][$arr_val['name']] = sanitize_text_field($arr_val['opt_value']);
				}
				else{
					$arr_final_array['master_fields'][$arr_val['name']] = sanitize_text_field($arr_val['txt_value']);
				}
			}
		}
	}

	//get master fields related data
	if(isset($_POST['cf7mount_enable_form']) && !empty($_POST['cf7mount_enable_form'])){
		$arr_final_array['enable_form'] = sanitize_text_field($_POST['cf7mount_enable_form']);
	}
	else{
		$arr_final_array['enable_form'] = '';
	}

	$arr_existing_fields = get_option('cf7mount_api_fields_mapping');
	if(!empty($arr_existing_fields) && isset($arr_existing_fields[$current_form_id])){
		$arr_existing_fields[$current_form_id] = $arr_final_array;
	}
	else{
		$arr_existing_fields[$current_form_id] = $arr_final_array;
	}

	update_option('cf7mount_api_fields_mapping',$arr_existing_fields);
   	$options_updated = false;

}

//get all mapping fields related value
$arr_mapping_fields = array();
$arr_mapping_fields = get_option('cf7mount_api_fields_mapping');

?><!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h3><?php _e( 'CF7 to mountstride CRM Fields Mapping', CF72MUT_TEXT_DOMAIN ); ?></h3>
    <p><?php _e( 'Map the required mountstride CRM API fields to your current Contact Form 7 fields', CF72MUT_TEXT_DOMAIN ); ?></p><?php
    if( $options_updated ){
    	echo '<div class="notice notice-success is-dismissible"><p>Settings saved successfully</p></div>';
    }

	//check any contact form exist or not
	if(!empty($cf7forms)){
		print '<div id="cf7-form-listing" class="cf7-form-listing cf72m">';
		$no_cf7 = 0;
			foreach ($cf7forms as $cf7form) {
				$no_cf7++;
				//define contact form Id in variable from here
				$fId = $cf7form->id();
				$checked = '';
				if(!empty($arr_mapping_fields) && isset($arr_mapping_fields[$fId]['enable_form']) && !empty($arr_mapping_fields[$fId]['enable_form'])){
					$checked = 'checked';
				}


				?><!-- define div for have all Time schedule section -->

				<form name="cf7_form_<?php print $fId;?>" class="cf7_mount_field_map" id="cf7_form_<?php print $fId;?>" method="post">
					<input type="hidden" name="current_form_id" value="<?php print $fId;?>">
					<div id="form-listing-<?php print $fId;?>" class="form-listing">
						<!-- Display close and move icon from this section -->
						<div class="form-sec form-main-sec <?php if($no_cf7==1){echo "slide_open";}?>">
							<label class="control-label" for="form_name" id="form_name"><?php print $cf7form->title();?></label>
							<div class="dom-close-toggle-icon">
								<!-- Display move icon -->
								<label class="slider-toggle" form-id="<?php print $fId;?>"></label>
							</div>
						</div>
						<div class="slide-form-sec" <?php if($no_cf7==1){echo 'style="display:block;"';} ?>>
							<div class="enable-form">
								<input type="checkbox" name="cf7mount_enable_form" id="cf7mount_enable_form-<?php print $fId;?>" value="yes" <?php print $checked;?>>
								<label for="cf7mount_enable_form-<?php print $fId;?>"><?php _e( 'Enable this form data submitting to mountstride CRM', CF72MUT_TEXT_DOMAIN ); ?></label>
							</div>
							<div class="common-fields">
								<div class="form-sec">
									<label><?php _e( 'mountstride CRM General Fields', CF72MUT_TEXT_DOMAIN ); ?></label>
								</div>
								<div class="append-field-sec general_field_table">
									<table id="cf7moun_table_<?php print $cf7form->id();?>" >
										<tr>
											<th class="col_gen_field"><?php _e( 'mountstride CRM General Fields', CF72MUT_TEXT_DOMAIN ); ?></th>
											<th class="col_cf7_field"><?php _e( 'CF7 Fields', CF72MUT_TEXT_DOMAIN ); ?></th>
											<th class="col_action"><?php _e( 'Action', CF72MUT_TEXT_DOMAIN ); ?></th>
										</tr><?php
										$arr_form_tags_list = $cf7form->scan_form_tags();
										$arr_form_tags = array();
										//get all contact form tags list
										foreach ($arr_form_tags_list as $arr_form_tag){
											if(empty($arr_form_tag->name) ){
												continue;
											}
											$arr_form_tags[$arr_form_tag->name] = $arr_form_tag->name;
										}
										$arr_form_tags['vsz_current_date_mapping'] = 'Current Date ('.date("Y-m-d").')';
												
										//add filter so user can add additonal mapping fields
										$arr_form_tags = (array) apply_filters('cf7_to_mountstride_add_additional_mapping_field',$arr_form_tags,$fId);
										
										//get save common fields value
										$arr_com_fields = array();
										if(!empty($arr_mapping_fields) && isset($arr_mapping_fields[$fId]['common_fields'])){
											$arr_com_fields = $arr_mapping_fields[$fId]['common_fields'];
										}

										$i=1;
										//displaying all default API fields from here
										if(!empty($mountstride_fields)){
											foreach($mountstride_fields as $d_key => $d_value){

												$field_api_id = 'cf7mount_api_key_'.$fId.'_'.$i;
												$field_cf7_id = 'cf7mount_cf7_key_'.$fId.'_'.$i;
												$selected = '';
												if(!empty($arr_com_fields) && array_key_exists($d_key,$arr_com_fields)){
													$selected = $d_key;
												}
												?><tr data-id="<?php print $i;?>">
													<td class="col_gen_field"><?php
														/* ?><select data-id="<?php print $i;?>" name="cf7mount_common_fields[<?php echo $cf7form->id(); ?>][api_key][]" id="<?php print $field_api_id;?>">
															<option value="">Select Key</option><?php */
															//display all option from here
															//print cf7_to_mountstride_option_fields($mountstride_fields,$selected);
														/* ?></select><?php */
														if( "Note" != $d_key){
														?><input type="hidden" name="cf7mount_common_fields[<?php echo $cf7form->id(); ?>][api_key][]" value="<?php echo $d_key; ?>" />
														<?php } ?>
														<span><?php echo $d_value; ?></span>
													</td>
													<td><?php
														if( "Note" == $d_key){
															$inn_sec_id = 'inner-dom-'.$fId.'-'.$i;
															?><div class="dom-<?php print $i;?> note-section" >
																<div class="inner-dom-<?php print $i;?>" id="<?php print $inn_sec_id;?>" ><?php
																	if(isset($arr_com_fields['Note']) && !empty($arr_com_fields['Note']) && is_array($arr_com_fields['Note'])){
																		$field_count = 1;
																		foreach($arr_com_fields['Note'] as $iKey => $note_cf7_key){
																			$field_dom_id = 'field-dom-'.$fId.'-'.$i.'-'.$field_count;
																			?><div class="field-dom" id="<?php print $field_dom_id;?>" field-sec-id="<?php print $field_count;?>" >
																				<select data-id="<?php print $i;?>" name="cf7mount_common_fields[<?php echo $cf7form->id(); ?>][notes][]"  id="<?php print $field_cf7_id;?>" data-fieldType="default">
																					<option value=""><?php _e( 'N/A', CF72MUT_TEXT_DOMAIN );?></option><?php
																					foreach ($arr_form_tags as $Fname => $fVal ){
																						if( empty($Fname) ){
																							continue;
																						}
																						$select_term = '';
																						if($note_cf7_key == $Fname ){
																							$select_term = 'selected';
																						}
																						?><option value="<?php echo $Fname;?>" <?php echo $select_term; ?>
																							><?php _e( $fVal, CF72MUT_TEXT_DOMAIN );?></option><?php
																					}
																				?></select>
																				<div class="field-dom-delete" data-id="<?php print $i;?>" form-id="<?php echo $fId; ?>" field-sec-id="<?php print $field_count;?>">
																					<label title="Delete"><span>
																					<img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))); ?>images/remove.svg" />
																					</span></label>
																				</div>
																			</div><?php
																			$field_count++;
																		}
																	}
																	else{
																		$field_count = 1;
																		$field_dom_id = 'field-dom-'.$fId.'-'.$i.'-'.$field_count;
																		?><div class="field-dom" id="<?php print $field_dom_id;?>" field-sec-id="<?php print $field_count;?>" >
																			<select data-id="<?php print $i;?>" name="cf7mount_common_fields[<?php echo $cf7form->id(); ?>][notes][]"  id="<?php print $field_cf7_id;?>" data-fieldType="default">
																				<option value=""><?php _e( 'N/A', CF72MUT_TEXT_DOMAIN );?></option><?php
																				foreach ($arr_form_tags as $Fname => $fVal ){
																					if( empty($Fname) ){
																						continue;
																					}
																					$select_term = '';
																					if(!empty($arr_com_fields) && array_key_exists($d_key,$arr_com_fields) && $arr_com_fields[$d_key] == $Fname ){
																						$select_term = 'selected';
																						unset($arr_com_fields[$d_key]);
																					}

																					?><option value="<?php echo $Fname; ?>" <?php echo $select_term; ?>
																						><?php _e( $fVal, CF72MUT_TEXT_DOMAIN );?></option><?php
																				}
																			?></select>
																		</div><?php
																	}
																?></div>
																<label data-id="<?php print $i;?>" form-id="<?php echo $cf7form->id(); ?>" class="add_note_fields" title="Add">Add more</label>
															</div><?php
															unset($arr_com_fields[$d_key]);
														}
														else{
															?><select data-id="<?php print $i;?>" name="cf7mount_common_fields[<?php echo $cf7form->id(); ?>][cf7_key][]"  id="<?php print $field_cf7_id;?>" data-fieldType="default">
																<option value=""><?php _e( 'N/A', CF72MUT_TEXT_DOMAIN );?></option><?php
																foreach ($arr_form_tags as $Fname => $fVal){
																	if( empty($Fname) ){
																		continue;
																	}
																	$select_term = '';
																	if(!empty($arr_com_fields) && array_key_exists($d_key,$arr_com_fields) && $arr_com_fields[$d_key] == $Fname ){
																		$select_term = 'selected';
																		unset($arr_com_fields[$d_key]);
																	}

																	?><option value="<?php echo $Fname;?>"
																		<?php echo $select_term; ?>
																		><?php _e( $fVal, CF72MUT_TEXT_DOMAIN );?></option><?php
																}
															?></select><?php
														}
													?></td>
													<td> - </td>
												</tr><?php

												$i++;
											}
										}//close mountstride fields if

										//define flag for check any extra fields exist or not
										$commonFlag = true;
										//check any other fields exist or not
										if(!empty($arr_com_fields)){
											$commonFlag = false;
											foreach($arr_com_fields as $api_key => $cf7_key){
												$field_api_id = 'cf7mount_api_key_'.$fId.'_'.$i;
												$field_cf7_id = 'cf7mount_cf7_key_'.$fId.'_'.$i;
												?><tr data-id="<?php print $i;?>">
													<td width="30%">
														<input type="text" data-id="<?php print $i;?>" name="cf7mount_common_fields[<?php echo $cf7form->id(); ?>][api_key][]" id="<?php print $field_api_id;?>" value="<?php _e( $api_key, CF72MUT_TEXT_DOMAIN );?>">
													</td>
													<td>
														<select data-id="<?php print $i;?>" name="cf7mount_common_fields[<?php echo $cf7form->id(); ?>][cf7_key][]"  id="<?php print $field_cf7_id;?>" >
															<option value=""><?php _e( 'N/A', CF72MUT_TEXT_DOMAIN );?></option><?php
															foreach ($arr_form_tags as $Fname => $fVal){
																if( empty($Fname) ){
																	continue;
																}
																$select_term = '';
																if( $Fname == $cf7_key ){
																	$select_term = 'selected';
																}

																?><option value="<?php echo $Fname; ?>" <?php echo $select_term; ?>
																	><?php _e( $fVal, CF72MUT_TEXT_DOMAIN );?></option><?php
															}
														?></select>
													</td>
													<td>
														<label data-id="<?php print $i;?>" form-id="<?php print $fId;?>" class="delete_form_fields" title="Delete"><?php _e( 'Delete', CF72MUT_TEXT_DOMAIN );?></label>
													</td>
												</tr><?php
												$i++;
											}
										}//close common fields

										//displaying default extra fields from here
										if($commonFlag){
											$field_api_id = 'cf7mount_api_key_'.$fId.'_'.$i;
											$field_cf7_id = 'cf7mount_cf7_key_'.$fId.'_'.$i;
											?><tr data-id="<?php print $i;?>">
												<td width="30%">
													<input type="text" data-id="<?php print $i;?>" name="cf7mount_common_fields[<?php echo $cf7form->id(); ?>][api_key][]" id="<?php print $field_api_id;?>">
												</td>
												<td>
													<select data-id="<?php print $i;?>" name="cf7mount_common_fields[<?php echo $cf7form->id(); ?>][cf7_key][]"  id="<?php print $field_cf7_id;?>">
														<option value=""><?php _e( 'N/A', CF72MUT_TEXT_DOMAIN );?></option><?php
														foreach ($arr_form_tags as $Fname => $fVal){
															if( empty($Fname) ){
																continue;
															}
															?><option value="<?php echo $Fname; ?>">
																<?php _e( $fVal, CF72MUT_TEXT_DOMAIN );?>
															</option><?php
														}
													?></select>
												</td>
												<td>
													<label data-id="<?php print $i;?>" form-id="<?php print $fId;?>" class="delete_form_fields" title="Delete"><?php _e( 'Delete', CF72MUT_TEXT_DOMAIN );?></label>
												</td>
											</tr><?php
										}
									?></table>
									<div class="new_data_field_sec">
										<a href="javascript:void(0);" id="add_new_fields<?php print $cf7form->id();?>" class="button button-primary add_new_fields" data-id="<?php print $cf7form->id();?>"><i class="fa fa-plus"></i><label><?php _e( 'Add New Field', CF72MUT_TEXT_DOMAIN );?></label></a>
									</div>
								</div>
							</div>
							<!-- displaying master fields from here -->
							<div class="master-fields">
								<div class="form-sec">
									<label><?php _e( 'mountstride CRM Master Data Fields', CF72MUT_TEXT_DOMAIN );?></label>
								</div>
								<div class="append-field-sec general_field_table">
									<table width="100%" class="append_extra_details-table">
										<tbody id="append_extra_details-<?php print $fId;?>">
											<tr>
												<th class="col_gen_field"><?php _e( 'Extra Field Name', CF72MUT_TEXT_DOMAIN );?></th>
												<th class="col_cf7_field"><?php _e( 'Value', CF72MUT_TEXT_DOMAIN );?></th>
												<th class="col_action"><?php _e( 'Action', CF72MUT_TEXT_DOMAIN );?></th>
											</tr><?php
											if(!empty($arr_mapping_fields) && isset($arr_mapping_fields[$fId]['master_fields'])){
												$arr_mast_fields = $arr_mapping_fields[$fId]['master_fields'];
												$domCount = count($arr_mast_fields);
												?><!-- Define DOM count here -->
												<input type="hidden" name="domCount" id="domCount-<?php print $fId;?>" value="<?php print $domCount;?>"><?php
												$i=1;
												foreach($arr_mast_fields as $fName => $value){
													$secID = 'extra-field-sec-'.$fId.'-'.$i;
													$fieldName = 'cf7mount_master_fields['.$fId.']['.$i.'][name]';
													$fieldVName = 'cf7mount_master_fields['.$fId.']['.$i.'][opt_value]';
													$fieldTxtName = 'cf7mount_master_fields['.$fId.']['.$i.'][txt_value]';
													$fieldId = 'vsz_extra_field_name-'.$fId.'-'.$i;
													$fieldVId = 'vsz_extra_field_value-'.$fId.'-'.$i;
													$fieldSelVId = 'vsz_extra_field_value_sel-'.$fId.'-'.$i;
													$fieldTxtVId = 'vsz_extra_field_value_txt-'.$fId.'-'.$i;

													$txtFieldFlag = true;
													?><tr id="<?php print $secID;?>" class="extra-field-section" form-id="<?php print $fId;?>" data-id="<?php print $i;?>">
														<td valign="top">
															<input name="<?php print $fieldName;?>" id="<?php print $fieldId;?>" class="form-control" value="<?php _e( $fName, CF72MUT_TEXT_DOMAIN );?>" placeholder="Name" title="Name" type="text">
														</td>
														<td>
															<div class="row">
																<div class="col-sm-6">
																	<select class="master-opt-field" form-id="<?php print $fId;?>" data-id="<?php print $i;?>" name="<?php print $fieldVName;?>"  id="<?php print $fieldSelVId;?>" >
																		<option value=""><?php _e( 'N/A', CF72MUT_TEXT_DOMAIN );?></option><?php
																		foreach ($arr_form_tags as $f_name => $fVal){
																			if( empty($f_name) ){
																				continue;
																			}
																			$selected = '';
																			if($value == $f_name){
																				$selected = 'selected';
																				$txtFieldFlag = false;
																			}

																			?><option value="<?php echo $f_name; ?>" <?php print $selected;?>>
																				<?php _e( trim($fVal), CF72MUT_TEXT_DOMAIN );?>
																			</option><?php
																		}
																	?></select>
																</div><?php
																$txtFieldVal = '';
																$txtFieldClass = 'readonly';
																$txtFieldClassF = 'disable';
																if($txtFieldFlag){
																	$txtFieldVal = $value;
																	$txtFieldClass = '';
																	$txtFieldClassF = '';
																}
																?><div class="col-sm-6">
																	<input name="<?php print $fieldTxtName;?>" id="<?php print $fieldTxtVId;?>" class="form-control <?php print $txtFieldClassF; ?>" value="<?php _e( $txtFieldVal, CF72MUT_TEXT_DOMAIN );?>" placeholder="Value" title="Value" type="text" <?php print $txtFieldClass;?>>
																</div>
															</div>
														</td>
														<td><label class="control-label delete-section" onclick="removeSection(<?php print $fId;?>,<?php print $i;?>)" for="action"><?php _e( 'Delete', CF72MUT_TEXT_DOMAIN );?></label>
														</td>
													</tr><?php
													$i++;
												}
											}
											else{
												$domCount = 1;
												$secID = 'extra-field-sec-'.$fId.'-'.$domCount;
												$fieldName = 'cf7mount_master_fields['.$fId.']['.$domCount.'][name]';
												$fieldVName = 'cf7mount_master_fields['.$fId.']['.$domCount.'][opt_value]';
												$fieldTxtName = 'cf7mount_master_fields['.$fId.']['.$domCount.'][txt_value]';
												$fieldId = 'vsz_extra_field_name-'.$fId.'-'.$domCount;
												$fieldVId = 'vsz_extra_field_value-'.$fId.'-'.$domCount;
												$fieldSelVId = 'vsz_extra_field_value_sel-'.$fId.'-'.$domCount;
												$fieldTxtVId = 'vsz_extra_field_value_txt-'.$fId.'-'.$domCount;
												?><input type="hidden" name="domCount" id="domCount-<?php print $fId;?>" value="<?php print $domCount;?>">
												<tr id="<?php print $secID;?>" class="extra-field-section" form-id="<?php print $fId;?>" data-id="<?php print $domCount;?>">
													<td valign="top">
														<input name="<?php print $fieldName;?>" id="<?php print $fieldId;?>" class="form-control" value="" placeholder="Name" title="Name" type="text">
													</td>
													<td>

														<div class="row">
															<div class="col-sm-6">
																<select class="master-opt-field" form-id="<?php print $fId;?>" data-id="<?php print $domCount;?>" name="<?php print $fieldVName;?>"  id="<?php print $fieldSelVId;?>" >
																	<option value=""><?php _e( 'N/A', CF72MUT_TEXT_DOMAIN );?></option><?php
																	foreach ($arr_form_tags as $f_name => $fVal){
																		if( empty($f_name) ){
																			continue;
																		}
																		?><option value="<?php echo $f_name; ?>">
																			<?php _e( trim($fVal), CF72MUT_TEXT_DOMAIN );?>
																		</option><?php
																	}
																?></select>
															</div>
															<div class="col-sm-6">
																<input name="<?php print $fieldTxtName;?>" id="<?php print $fieldTxtVId;?>" class="form-control" value="" placeholder="Value" title="Value" type="text">
															</div>
														</div>
													</td>
													<td>
														<label class="control-label delete-section" onclick="removeSection(<?php print $fId;?>,1)" for="action"><?php _e( 'Delete', CF72MUT_TEXT_DOMAIN );?></label>
													</td>
												</tr><?php
											}
										?></tbody>
									</table>
									<div class="new_data_field_sec">
										<a href="javascript:void(0);" id="add_more" class="button button-primary add_new_master_fields" onclick="addExtraSection(<?php print $fId;?>)"><i class="fa fa-plus"></i><label><?php _e( 'Add New Master Data Field', CF72MUT_TEXT_DOMAIN );?></label></a>
									</div>
								</div>
							</div>
							<div class="ch_submit_btn">
								<input type="submit" form-id="<?php print $fId;?>" class="button button-primary save-cf7-form" name="cf72mot_mapping_submit" id="cf72mot_mapping_submit-<?php $fId;?>" value="<?php _e( 'Save', CF72MUT_TEXT_DOMAIN );?>">
							</div>
						</div>
					</div>
				</form><?php
			}//close form foreach
		print '</div>';

	}//close if
?></div>
<!-- Loader -->
<div class="service_bck_loader" id="service_bck_loader" style="display:none;"><div class="loader"></div></div>
