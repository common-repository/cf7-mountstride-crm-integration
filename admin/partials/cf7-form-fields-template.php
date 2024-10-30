<?php
if ( !defined( 'ABSPATH' ) ) {
exit;
}

if(isset($_POST['row_count']) && $_POST['row_count'] != ''){
	$cur_auto_id = sanitize_text_field($_POST['row_count']);
}
if(isset($_POST['form_id']) && $_POST['form_id'] != ''){
	$form_id = sanitize_text_field($_POST['form_id']);
}else{
	echo "";
	exit;
}

$arr_form_tags_list = $this->get_cf7_forms_fields($form_id);
if( empty( $arr_form_tags_list ) ){
	echo "";
	exit;
}

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
$arr_form_tags = (array) apply_filters('cf7_to_mountstride_add_additional_mapping_field',$arr_form_tags,$form_id);

$dom_field_name = 'general';
if(isset($_POST['dom_field_name']) && !empty($_POST['dom_field_name'])){
	$dom_field_name = trim(sanitize_text_field($_POST['dom_field_name']));
}

$field_sec_count = '';
if(isset($_POST['field_sec_count']) && !empty($_POST['field_sec_count'])){
	$field_sec_count = trim(sanitize_text_field($_POST['field_sec_count']));
}

switch($dom_field_name){

	case 'note':
		$field_sec_count++;
		$field_cf7_id = 'cf7mount_cf7_key_'.$form_id.'_'.$cur_auto_id;
		$field_dom_id = 'field-dom-'.$form_id.'-'.$cur_auto_id.'-'.$field_sec_count;
		?><div class="field-dom" id="<?php print $field_dom_id;?>" field-sec-id="<?php print $field_sec_count;?>" >
			<select data-id="<?php print $cur_auto_id;?>" name="cf7mount_common_fields[<?php echo $form_id; ?>][notes][]"  id="<?php print $field_cf7_id;?>" data-fieldType="default">
				<option value=""><?php _e( 'N/A', CF72MUT_TEXT_DOMAIN );?></option><?php
				foreach ($arr_form_tags as $Fname => $fVal ){
					if( empty($Fname) ){
						continue;
					}
					?><option value="<?php echo $Fname; ?>"><?php _e( $fVal, CF72MUT_TEXT_DOMAIN );?></option><?php
				}
			?></select>
			<div class="field-dom-delete" data-id="<?php print $cur_auto_id;?>" form-id="<?php echo $form_id; ?>" field-sec-id="<?php print $field_sec_count;?>">
				<label title="Delete"><span><img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))); ?>images/remove.svg" /></span></label>
			</div>
		</div><?php

	break;

	case 'master' :

		$secID = 'extra-field-sec-'.$form_id.'-'.$cur_auto_id;
		$fieldName = 'cf7mount_master_fields['.$form_id.']['.$cur_auto_id.'][name]';
		$fieldVName = 'cf7mount_master_fields['.$form_id.']['.$cur_auto_id.'][opt_value]';
		$fieldTxtName = 'cf7mount_master_fields['.$form_id.']['.$cur_auto_id.'][txt_value]';
		$fieldId = 'vsz_extra_field_name-'.$form_id.'-'.$cur_auto_id;
		$fieldSelVId = 'vsz_extra_field_value_sel-'.$form_id.'-'.$cur_auto_id;
		$fieldTxtVId = 'vsz_extra_field_value_txt-'.$form_id.'-'.$cur_auto_id;

		?><tr id="<?php print $secID;?>" class="extra-field-section" form-id="<?php echo $form_id; ?>" data-id="<?php print $cur_auto_id;?>">
			<td valign="top">
				<input name="<?php print $fieldName;?>" id="<?php print $fieldId;?>" class="form-control" value="" placeholder="Name" title="Name" type="text">
			</td>
			<td>
				<div class="row">
					<div class="col-sm-6">
						<select class="master-opt-field" form-id="<?php print $form_id;?>" data-id="<?php print $cur_auto_id;?>" name="<?php print $fieldVName;?>"  id="<?php print $fieldSelVId;?>" >
							<option value=""><?php _e( 'N/A', CF72MUT_TEXT_DOMAIN );?></option><?php
							foreach ($arr_form_tags as $Fname => $fVal ){
								if( empty($Fname) ){
									continue;
								}

								?><option value="<?php echo $Fname; ?>">
									<?php _e( trim($fVal), CF72MUT_TEXT_DOMAIN );?>
								</option><?php
							}
						?></select>
					</div>
					<div class="col-sm-6">
						<input name="<?php print $fieldTxtName;?>"  id="<?php print $fieldTxtVId;?>" class="form-control" value="" placeholder="Value" title="Value" type="text">
					</div>
				</div>
			</td>
			<td>
				<label class="control-label delete-section" onclick="removeSection(<?php echo $form_id; ?>,<?php print $cur_auto_id;?>)" for="action"><?php _e( 'Delete', CF72MUT_TEXT_DOMAIN );?></label>
			</td>
		</tr><?php

	break;

	case 'general':

		$cur_auto_id++;
		$field_api_id = 'cf7mount_api_key_'.$form_id.'_'.$cur_auto_id;
		$field_cf7_id = 'cf7mount_cf7_key_'.$form_id.'_'.$cur_auto_id;
		?><tr data-id="<?php print $cur_auto_id;?>">
			<td width="30%">
				<input type="text" data-id="<?php print $cur_auto_id;?>" name="cf7mount_common_fields[<?php echo $form_id; ?>][api_key][]" id="<?php print $field_api_id;?>">
			</td>
			<td>
				<select data-id="<?php print $cur_auto_id;?>" name="cf7mount_common_fields[<?php echo $form_id; ?>][cf7_key][]" id="<?php print $field_cf7_id;?>">
					<option value="">N/A</option><?php
					foreach ($arr_form_tags as $Fname => $fVal ){
						if( empty($Fname) ){
							continue;
						}
						?><option value="<?php echo $Fname; ?>"><?php
							echo $fVal;
						?></option><?php
					}
				?></select>
			</td>
			<td>
				<label data-id="<?php print $cur_auto_id;?>" form-id="<?php print $form_id;?>" class="delete_form_fields" title="Delete"><?php _e( 'Delete', CF72MUT_TEXT_DOMAIN );?></label>
			</td>
		</tr><?php
	break;

	default:

	break;
}

wp_die();




