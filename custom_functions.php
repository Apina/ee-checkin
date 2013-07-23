/***********************************************************************
*
* This needs to be added to the custom_functions.php file, it does NOT replace it.
*
* Created by Dean Robinson - this isn't endorsed or supported by Event Espresso - use at your own risk!
*
* 
*
******/



/**
 * Adds the WordPress Ajax Library to the frontend.
 */

add_action( 'wp_head', 'add_ajax_library' );

function add_ajax_library() {
 
    $html = '<script type="text/javascript">';
        $html .= 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '"';
    $html .= '</script>';
 
    echo $html;
 
} // end add_ajax_library




function eecheckin_loadscripts() {
  
	$uploadpath = wp_upload_dir();
	 
	wp_enqueue_script('eecheckin', $uploadpath['baseurl'] . '/espresso/eecheckin.js');

	wp_register_style( 'eecheckincss', $uploadpath['baseurl'] . '/espresso/eecheckin_css.css');
	wp_enqueue_style( 'eecheckincss' );		


	wp_localize_script('eecheckin-script', 'eecheckin_nonce_object', array(
		  'ajaxurl' => admin_url('admin-ajax.php', $protocol),
		  'eecheckin_nonce' => wp_create_nonce('eecheckin_nonce'),
		)
	
	);

}
add_action('wp_enqueue_scripts', 'eecheckin_loadscripts');


function dr_checkin_the_attendee() {

if ( !wp_verify_nonce( $_REQUEST['nonce'], "eecheckin_nonce")) {
      exit("No naughty business please");
   }
   
   
global $wpdb;

$needle = $_POST['attendee_reg_id'];

$the_ids= explode("|", $needle);


$wpdb->drcheckin = "{$wpdb->prefix}events_attendee";
$logs = $wpdb->get_row("SELECT * FROM {$wpdb->drcheckin} WHERE registration_id = '$the_ids[1]' AND id = '$the_ids[0]'");

$tck_quan = $logs->quantity;
$chckedin = $logs->checked_in;
$chckedin_quan = $logs->checked_in_quantity;

if($chckedin > 0 ) { $checkedinresult = true; }
if($chckedin > 0 && $chckedin_quan >= $chckedin) { $checkedinresult = true;  }
if($chckedin_quan < $tck_quan) { $checkedinresult = false;  }

$return_array = array(
					checkedinresult => $checkedinresult,
					tck_quan => $tck_quan,
					chckedin => $chckedin,
					chckedin_quan => $chckedin_quan,
					regid => $the_ids[1]//$needle
					  );


if($checkedinresult == true) { $return_array['message'] = "Max. checked in"; $return_array = json_encode($return_array); die($return_array); }
else {

	$result = $wpdb->update( 
				  $wpdb->drcheckin, 
				  array(
						'checked_in' => '1',
						'checked_in_quantity' => $chckedin_quan + 1
						),
				  array(
						'registration_id' => $the_ids[1],
						'id' => $the_ids[0]
						),
				  array(
						'%d',
						'%d'
						)
				  );
	
	
$return_array['message'] = "Attendee Checked In"; 
$return_array['chckedin_quan'] = $chckedin_quan + 1;
$return_array = json_encode($return_array); 
die($return_array);
}

}



//CHECKOUT

function dr_checkout_the_attendee() {

if ( !wp_verify_nonce( $_REQUEST['nonce'], "eecheckin_nonce")) {
      exit("No naughty business please");
   }
   

global $wpdb;

$needle = $_POST['attendee_reg_id'];

$the_ids= explode("|", $needle);

$wpdb->drcheckin = "{$wpdb->prefix}events_attendee";
$logs = $wpdb->get_row("SELECT * FROM {$wpdb->drcheckin} WHERE registration_id = '$the_ids[1]' AND id = '$the_ids[0]'");

$tck_quan = $logs->quantity;
$chckedin = $logs->checked_in;
$chckedin_quan = $logs->checked_in_quantity;

//if($chckedin > 0 ) { $checkedinresult = true; }
//if($chckedin > 0 && $chckedin_quan >= $chckedin) { $checkedinresult = true;  }
//if($chckedin_quan < $tck_quan) { $checkedinresult = false;  }

$return_array = array(
					checkedinresult => $checkedinresult,
					tck_quan => $tck_quan,
					chckedin => $chckedin,
					chckedin_quan => $chckedin_quan,
					regid => $the_ids[1]
					  );

if($chckedin_quan <= 0 ) { $return_array['message'] = "Cant go below zero!"; $return_array = json_encode($return_array); die($return_array); } 

else {

	$result = $wpdb->update( 
				  $wpdb->drcheckin, 
				  array(
						'checked_in_quantity' => $chckedin_quan - 1
						),
				  array(
						'registration_id' => $the_ids[1], 
						'id' => $the_ids[0]
						),
				  array(
						'%d',
						'%d'
						)
				  );
	
	
$return_array['message'] = "Attendee Checked OUT"; 
$return_array['chckedin_quan'] = $chckedin_quan - 1;
$return_array = json_encode($return_array); 
die($return_array);
}

}


add_action('wp_ajax_dr_checkin_the_attendee', 'dr_checkin_the_attendee');
add_action('wp_ajax_nopriv_dr_checkin_the_attendee', 'dr_checkin_the_attendee');
add_action('wp_ajax_dr_checkout_the_attendee', 'dr_checkout_the_attendee');
add_action('wp_ajax_nopriv_dr_checkout_the_attendee', 'dr_checkout_the_attendee');






if (!function_exists('ee_checkin_list')) {
	function ee_checkin_list($sql,$show_gravatar,$paid_only, $sort=''){
		//echo $sql;
		global $wpdb,$this_is_a_reg_page;
		$events = $wpdb->get_results($sql);
		
		$nonce = wp_create_nonce("eecheckin_nonce");
		
		foreach ($events as $event){
			$event_id = $event->id;
			$event_name = stripslashes_deep($event->event_name);
			if (!$this_is_a_reg_page){
				$event_desc = do_shortcode(stripslashes_deep($event->event_desc));
			}

?>


<div class="event-display-boxes ui-widget">

		<h2 class="event_title ui-widget-header ui-corner-top">
	<?php _e('Attendee Check In For: ','event_espresso'); ?>
	<?php echo $event_name . $event_status?></h2>
    
    <?php //var_dump($event); ?>

		<div class="event-data-display ui-widget-content ui-corner-bottom eecheckin-<?php echo $event->id; ?>">

<input type="hidden" value="<?php echo $event->id; ?>"  />
     
            <table class="list-attendees-checkin" id="list-attendees-checkin-<?php echo $event->id; ?>">
                        <tr>
                        <th>Attendee</th>
                        <th>Reg. ID</td>
                        <th></th>
                        <th></th>
                        <th>Checked/Max</th>
                        </tr>
            
				<?php
					$a_sql = "SELECT * FROM " . EVENTS_ATTENDEE_TABLE . " WHERE event_id='" . $event_id . "'";
					$a_sql .= $paid_only == 'true'? " AND (payment_status='Completed' OR payment_status='Pending' OR payment_status='Refund') ":'';
					$a_sql .= $sort;
					//echo $a_sql;
					$attendees = $wpdb->get_results($a_sql);
					
					//var_dump($attendees);
					
					foreach ($attendees as $attendee){
						
						
						$reg_id = $attendee->registration_id;
						$id = $attendee->id;
						$lname = $attendee->lname;
						$fname = $attendee->fname;
						$attendeecount = $attendee->checked_in_quantity . "/" . $attendee->quantity;
						
				
				?>
                        
                        <tr>
                        <td><?php echo stripslashes_deep($fname . ' ' . $lname); ?></td>
                        <td><?php echo $reg_id ?></td>
                        <td><input type="button" data-nonce="<?php echo $nonce; ?>" class="dr_checkin" value="Check In" id="<?php echo $id ?>|<?php echo $reg_id ?>" /></td>
                        <td><input type="button" data-nonce="<?php echo $nonce; ?>" class="dr_checkout" value="Check Out" id="<?php echo $id ?>|<?php echo $reg_id ?>" /></td>
                        <td id="ac_<?php echo $reg_id ?>"><?php echo $attendeecount; ?></td>
                        </tr>
				<?php
					}
				?>
                
                </table>
                
	</div>
</div>




<?php
		}
	}
}
 
