/***********************************************************************
*
* This needs to be added to the wp-content/uploads/espresso folder.
*
* Created by Dean Robinson - this isn't endorsed or supported by Event Espresso - use at your own risk!
*
* 
*
******/



jQuery(document).ready(function($) {



jQuery('.dr_checkin').click(function() {

		nonce = jQuery(this).attr("data-nonce");

		var gettheid = jQuery(this).attr('id');
					//console.log(gettheid);

		var container_id = jQuery(this).parent().parent().parent().parent().attr("id");
		
		var data = { 
			action: 'dr_checkin_the_attendee',
			type: 'POST',
			dataType: 'text',
			attendee_reg_id : gettheid,
			nonce : nonce
		};  

		jQuery.post(ajaxurl, data, function(response) {
			//console.log(response);
			var obj = jQuery.parseJSON(response);
			
			var message = obj.message;
			var status_id = obj.regid;
			
			var status;
			
			if(message == "Attendee Checked In") { status = "OK"}
			if(message == "Max. checked in") { status = "MAX"}

jQuery('#ac_'+obj.regid).empty()
jQuery('#ac_'+obj.regid).text(obj.chckedin_quan + "/"+ obj.tck_quan);

			jQuery('#status_'+ status_id).fadeIn();
			jQuery('#status_'+ status_id).html(status);
			jQuery('#status_'+ status_id).fadeOut();
	
		}); 
		
});
		


//CHECKOUT
jQuery('.dr_checkout').click(function() {


		nonce = jQuery(this).attr("data-nonce");

		var gettheid = this.id;
		var container_id = jQuery(this).parent().parent().parent().parent().attr("id");

		var data = { 
			action: 'dr_checkout_the_attendee',
			type: 'POST',
			dataType: 'text',
			attendee_reg_id : gettheid,
			nonce : nonce

		};  
		
		jQuery.post(ajaxurl, data, function(response) {
			//console.log(response);

			var obj = jQuery.parseJSON(response);

			var message = obj.message;
			var status_id = obj.regid;
			
			var status;
			
			if(message == "Attendee Checked OUT") { status = "OUT"};
			if(message == "Cant go below zero!") { status = "ERR"};

jQuery('#ac_'+obj.regid).empty()
jQuery('#ac_'+obj.regid).text(obj.chckedin_quan + "/"+ obj.tck_quan);
			
			jQuery('#status_'+ status_id).fadeIn();
			jQuery('#status_'+ status_id).html(status);
			jQuery('#status_'+ status_id).fadeOut();



});
});


});
