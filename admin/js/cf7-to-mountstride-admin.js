(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */




})( jQuery );


//ADD new field for the CF7 fields
jQuery(document).ready(function($) {


	//Add slider toggle  on click
	jQuery('.cf7_mount_field_map .form-main-sec').click(function() {
		jQuery(this).toggleClass('slide_open');
		jQuery(this).next().slideToggle();
	});


	//add settings field related validation here
	jQuery("#cf72mut_setting_submit").click(function(){
		if(jQuery("#cf72mot_api_enable").prop('checked') == true){
			var errorFlag = false;
			if(jQuery("#cf72mot_api_url").val() == '' || jQuery("#cf72mot_api_url").val().trim().length <= 0){
				jQuery("#cf72mot_api_url").css("border","1px solid red");
				errorFlag = true;
			}
			else{
				jQuery("#cf72mot_api_url").css("border","");
			}

			if(jQuery("#cf72mot_authorization_key").val() == '' || jQuery("#cf72mot_authorization_key").val().trim().length <= 0){
				jQuery("#cf72mot_authorization_key").css("border","1px solid red");
				errorFlag = true;
			}
			else{
				jQuery("#cf72mot_authorization_key").css("border","");
			}

			if(jQuery("#cf72mot_token_key").val() == '' || jQuery("#cf72mot_token_key").val().trim().length <= 0){
				jQuery("#cf72mot_token_key").css("border","1px solid red");
				errorFlag = true;
			}
			else{
				jQuery("#cf72mot_token_key").css("border","");
			}

			if(errorFlag){
				return false;
			}
		}
	})


	//call Ajax for create new common field
	jQuery('.add_new_fields').click(function(){

		jQuery('#service_bck_loader').show();
		// Get the data from the template
		var form_id  = jQuery(this).attr("data-id");
		var rowCount = jQuery('#cf7moun_table_'+form_id+' tr').length;
		if(rowCount != 1){
			rowCount = (jQuery('#cf7moun_table_'+form_id+' tr').length) - 1;
		}

		var wrapper  = "#cf7moun_table_"+form_id;
		jQuery.ajax({
			url: cf72mot_admin_action.ajax_url,
			type: 'POST',
			data: {'action':'cf7_form_fields_template',
					'row_count':rowCount,
					'form_id' : form_id
				  },
			success: function(data){
				//Loader to hide
				jQuery('#service_bck_loader').hide();
				jQuery(wrapper).append(data);

				//Animate to top
				rowCount++;
			},
			error: function(e){
				console.log(e);
				jQuery('#service_bck_loader').hide();
			},
		});
	});


	//call Ajax for create new common note field DOM
	jQuery('.add_note_fields').click(function(){

		jQuery('#service_bck_loader').show();
		// Get the data from the template
		var form_id  = jQuery(this).attr("form-id");
		var rowCount  = jQuery(this).attr("data-id");
		var fieldSecCount = jQuery('#inner-dom-'+form_id+'-'+rowCount+' .field-dom').length;

		var wrapper  = "#inner-dom-"+form_id+"-"+rowCount;
		jQuery.ajax({
			url: cf72mot_admin_action.ajax_url,
			type: 'POST',
			data: {'action':'cf7_form_fields_template',
					'row_count':rowCount,
					'form_id' : form_id,
					'field_sec_count' : fieldSecCount,
					'dom_field_name':'note',
				  },
			success: function(data){
				//Loader to hide
				jQuery('#service_bck_loader').hide();
				jQuery(wrapper).append(data);

				//Animate to top
				rowCount++;
			},
			error: function(e){
				console.log(e);
				jQuery('#service_bck_loader').hide();
			},
		});
	});

	jQuery(".master-opt-field").on('change',function(){

		// Get the data from the template
		var formId  = jQuery(this).attr("form-id");
		var secId  = jQuery(this).attr("data-id");
		var selCf7Id = "#vsz_extra_field_value_sel-"+formId+"-"+secId;
		var txtFieldId = "#vsz_extra_field_value_txt-"+formId+"-"+secId;
		var objSelCf7 = jQuery(selCf7Id);
		var objTxtField = jQuery(txtFieldId);
		var cf7Val = objSelCf7.val();
		var txtVal =  objTxtField.val();

		if(cf7Val != ''){
			objTxtField.val('');
			objTxtField.attr("readonly",true);
			objTxtField.addClass('disable');
		}
		else if(objTxtField.attr("readonly")){
			objTxtField.removeAttr("readonly");
			objTxtField.removeClass('disable');
		}
	});


	//delete connetion
	jQuery('.delete_form_fields').live('click',function(){
		if (confirm("Are you sure you want to delete this field?") == true) {

			var formId = jQuery(this).attr("form-id");
			var secId = jQuery(this).attr("data-id");
			jQuery(this).parent().parent().remove();
			//Get value from dom
			var existDom = jQuery("#cf7moun_table_"+formId).find("tr").length;
			if(existDom == 1){
				jQuery("#add_new_fields"+formId).click();
			}
		}

	});

	//delete connetion
	jQuery('.field-dom-delete').live('click',function(){
		if (confirm("Are you sure you want to delete this field?") == true) {

			var formId = jQuery(this).attr("form-id");
			var secId = jQuery(this).attr("data-id");
			var fieldSecId = jQuery(this).attr("field-sec-id");
			jQuery(this).parent().remove();
			//Get value from dom
			var existDom = jQuery('#inner-dom-'+formId+'-'+secId+' .field-dom').length;
			if(existDom == 0){
				jQuery("#cf7moun_table_"+formId+" .add_note_fields").click();
			}
		}

	});


	//add all form fields related validation
	jQuery(".save-cf7-form").click(function(){

		var formId = jQuery(this).attr('form-id');

		//validation for common fields from here

		var checkForm = true;
		var errorSection = "#cf7moun_table_"+formId;

		//Get value from common dom
		var existDom = jQuery("#cf7moun_table_"+formId).find("tr").length;
		if(existDom >=1 ){

			//check each field value from here
			jQuery("#cf7moun_table_"+formId).find("tr").each(function(){

				var secId = jQuery(this).attr("data-id");
				var fieldApiId = "#cf7mount_api_key_"+formId+"_"+secId;
				var fieldCf7Id = "#cf7mount_cf7_key_"+formId+"_"+secId;
				var objApiKey = jQuery(fieldApiId);
				var objCf7Key = jQuery(fieldCf7Id);
				var apiKey = objApiKey.val();
				var cf7Key =  objCf7Key.val();

				var fieldType = objCf7Key.attr('data-fieldType');
				//console.log(fieldType);
				if (typeof fieldType !== typeof undefined && fieldType !== false) {
					return;
				}

				if(apiKey == '' && cf7Key != ''){
					objApiKey.css("border","1px solid red");
					checkForm = false;
					errorSection = fieldApiId;
				}
				else{
					objApiKey.css("border","");
				}

				if(cf7Key == '' && apiKey != ''){
					objCf7Key.css("border","1px solid red");
					checkForm = false;
					errorSection = fieldCf7Id;
				}
				else{
					objCf7Key.css("border","");
				}

			});
		}

		//check value for master DOM
		var existMDom = jQuery('#append_extra_details-'+formId).find(".extra-field-section").length;

		if(existDom >= 1){

			//check each fields value form here
			jQuery('#append_extra_details-'+formId).find(".extra-field-section").each(function(){

				var secId = jQuery(this).attr("data-id");
				var fieldMApiId = "#vsz_extra_field_name-"+formId+"-"+secId;
				var fieldSelVId = "#vsz_extra_field_value_sel-"+formId+"-"+secId;
				var fieldTxtVId = "#vsz_extra_field_value_txt-"+formId+"-"+secId;
				var objApiKey = jQuery(fieldMApiId);
				var objCf7Key = jQuery(fieldSelVId);
				var objTxtKey = jQuery(fieldTxtVId);
				var apiKey = objApiKey.val();
				var cf7Key =  objCf7Key.val();
				var txtKey =  objTxtKey.val();

				if(apiKey == '' && (cf7Key != '' || txtKey != '')){
					objApiKey.css("border","1px solid red");
					checkForm = false;
					errorSection = fieldMApiId;
				}
				else{
					objApiKey.css("border","");
				}

				if(cf7Key == '' && txtKey == '' && apiKey != ''){
					objCf7Key.css("border","1px solid red");
					objTxtKey.css("border","1px solid red");
					checkForm = false;
					errorSection = fieldSelVId;
				}
				else{
					objCf7Key.css("border","");
					objTxtKey.css("border","");
				}

			});
		}

		//check error flag
		if(checkForm){
			return true;
		}
		else{
			//notify error section from here
			jQuery('html, body').animate({ scrollTop: jQuery(errorSection).offset().top-200 }, 1000);
			return false;
		}

	});

});

//define add DOM section from here
function addExtraSection(fId){

	//Get value from dom
	var domCount = parseInt(jQuery("#domCount-"+fId).val())+1;

	//Set value from dom
	jQuery('#domCount-'+fId).val(domCount);


	jQuery('#service_bck_loader').show();
	// Get the data from the template
	var form_id  = fId;
	var rowCount  = domCount;

	var wrapper  = "#append_extra_details-"+fId;
	jQuery.ajax({
		url: cf72mot_admin_action.ajax_url,
		type: 'POST',
		data: {'action':'cf7_form_fields_template',
				'row_count':rowCount,
				'form_id' : form_id,
				'dom_field_name':'master',
			  },
		success: function(data){
			//Loader to hide
			jQuery('#service_bck_loader').hide();
			jQuery(wrapper).append(data);
			jQuery(".master-opt-field").on('change',function(){

				// Get the data from the template
				var formId  = jQuery(this).attr("form-id");
				var secId  = jQuery(this).attr("data-id");
				var selCf7Id = "#vsz_extra_field_value_sel-"+formId+"-"+secId;
				var txtFieldId = "#vsz_extra_field_value_txt-"+formId+"-"+secId;
				var objSelCf7 = jQuery(selCf7Id);
				var objTxtField = jQuery(txtFieldId);
				var cf7Val = objSelCf7.val();
				var txtVal =  objTxtField.val();

				if(cf7Val != ''){
					objTxtField.val('');
					objTxtField.attr("readonly",true);
					objTxtField.addClass('disable');
				}
				else if(objTxtField.attr("readonly")){
					objTxtField.removeAttr("readonly");
					objTxtField.removeClass('disable');
				}
			});
		},
		error: function(e){
			console.log(e);
			jQuery('#service_bck_loader').hide();
		},
	});
}

//delete master DOM section from here
function removeSection(fId,secId){

	if (confirm("Are you sure about delete 'This Section'?")) {
		jQuery("#extra-field-sec-"+fId+"-"+secId).remove();
		//Get value from dom
		var existDom = jQuery('#append_extra_details-'+fId).find(".extra-field-section").length;
		if(existDom == 0){
			addExtraSection(fId);
			var domCount = parseInt(jQuery("#domCount-"+fId).val());
			jQuery("#extra-field-sec-"+fId+"-"+secId).find(".delete-section").css("display","none");
		}
	}
}