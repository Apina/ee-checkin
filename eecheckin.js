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
		var container_id = jQuery(this).parent().parent().parent().parent().attr("id");
		
		var data = { 
			action: 'dr_checkin_the_attendee',
			type: 'POST',
			dataType: 'text',
			attendee_reg_id : gettheid,
			nonce : nonce
		};  

		jQuery.post(ajaxurl, data, function(response) {
			console.log(response);
			var obj = jQuery.parseJSON(response);
			
			jQuery('#'+container_id).append("<p id='checkinmessage"+container_id+"'></p>");

			jQuery('#checkinmessage'+container_id).empty();
			jQuery('#checkinmessage'+container_id).text(obj.message).css("display", "block");
			jQuery('#ac_'+obj.regid).empty()
			jQuery('#ac_'+obj.regid).text(obj.chckedin_quan + "/"+ obj.tck_quan);
			jQuery('#checkinmessage'+container_id).delay(5000).fadeOut();
setTimeout(function() {
  jQuery('#checkinmessage'+container_id).remove();
}, 6000);


//console.log(obj.chckedin_quan + "/"+ obj.tck_quan);
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
			var obj = jQuery.parseJSON(response);

			jQuery('#'+container_id).append("<p id='checkinmessage"+container_id+"'></p>");

			jQuery('#checkinmessage'+container_id).empty();
			//jQuery('#checkinmessage'+container_id).empty();
			jQuery('#checkinmessage'+container_id).text(obj.message).css("display", "block");
			jQuery('#ac_'+obj.regid).empty()
			jQuery('#ac_'+obj.regid).text(obj.chckedin_quan + "/"+ obj.tck_quan);
			jQuery('#checkinmessage'+container_id).delay(5000).fadeOut();
setTimeout(function() {
  jQuery('#checkinmessage'+container_id).remove();
}, 6000);
		}); 
		
});



});
