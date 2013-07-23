/***********************************************************************
*
* This needs to be added to the custom_shortcodes.php file, it does NOT replace it.
*
* It's based on the LISTATTENDEES shortcode
*
* Created by Dean Robinson - this isn't endorsed or supported by Event Espresso - use at your own risk!
*
* 
*
******/



if (!function_exists('ee_checkin')) {

  function ee_checkin($event_id='NULL', $event_identifier='NULL', $category_identifier='NULL', $show_gravatar='false', $show_expired='false', $show_secondary='false', $show_deleted='false', $show_recurrence='true', $limit='0', $paid_only='false', $sort_by='last name') {
		
		global $this_event_id;
		
		$show_expired = $show_expired == 'false' ? " AND e.start_date >= '" . date('Y-m-d') . "' " : '';
		$show_secondary = $show_secondary == 'false' ? " AND e.event_status != 'S' " : '';
		$show_deleted = $show_deleted == 'false' ? " AND e.event_status != 'D' " : '';
		$show_recurrence = $show_recurrence == 'false' ? " AND e.recurrence_id = '0' " : '';
		$sort = $sort_by == 'last name' ? " ORDER BY lname " : '';
		$limit = $limit > 0 ? " LIMIT 0," . $limit . " " : '';
		if ($event_identifier != 'NULL' || $event_id != 'NULL' || (isset($this_event_id) && !empty($this_event_id)) ) {
			$type = 'event';
			if (isset($this_event_id) && !empty($this_event_id)){
				$event_id = $this_event_id;
			}
		} else if ($category_identifier != 'NULL') {
			$type = 'category';
		}

		if (!empty($type) && $type == 'event') {
			$sql = "SELECT e.* FROM " . EVENTS_DETAIL_TABLE . " e ";
			$sql .= " WHERE e.is_active = 'Y' ";
			if ($event_id != 'NULL'){
				$sql .= " AND e.id = '" . $event_id . "' ";
			}else{
				$sql .= " AND e.event_identifier = '" . $event_identifier . "' ";
			}
			$sql .= $show_secondary;
			$sql .= $show_expired;
			$sql .= $show_deleted;
			$sql .= $show_recurrence;
			$sql .= $limit;
			ee_checkin_list($sql, $show_gravatar, $paid_only, $sort);
		} else if (!empty($type) && $type == 'category') {
			$sql = "SELECT e.* FROM " . EVENTS_CATEGORY_TABLE . " c ";
			$sql .= " JOIN " . EVENTS_CATEGORY_REL_TABLE . " r ON r.cat_id = c.id ";
			$sql .= " JOIN " . EVENTS_DETAIL_TABLE . " e ON e.id = r.event_id ";
			$sql .= " WHERE c.category_identifier = '" . $category_identifier . "' ";
			$sql .= " AND e.is_active = 'Y' ";
			$sql .= $show_secondary;
			$sql .= $show_expired;
			$sql .= $show_deleted;
			$sql .= $show_recurrence;
			$sql .= $limit;
			ee_checkin_list($sql, $show_gravatar, $paid_only, $sort);
		} else {
			$sql = "SELECT e.* FROM " . EVENTS_DETAIL_TABLE . " e ";
			$sql .= " WHERE e.is_active='Y' ";
			$sql .= $show_secondary;
			$sql .= $show_expired;
			$sql .= $show_deleted;
			$sql .= $show_recurrence;
			$sql .= $limit;
			ee_checkin_list($sql, $show_gravatar, $paid_only, $sort);
		}
	}

}

if (!function_exists('event_espresso_list_attendees2')) {

	function event_espresso_list_attendees2($atts) {
		//echo $atts;
		extract(shortcode_atts(array('event_id' => 'NULL', 'event_identifier' => 'NULL', 'category_identifier' => 'NULL', 'event_category_id' => 'NULL', 'show_gravatar' => 'NULL', 'show_expired' => 'NULL', 'show_secondary' => 'NULL', 'show_deleted' => 'NULL', 'show_recurrence' => 'NULL', 'limit' => 'NULL', 'paid_only' => 'NULL'), $atts));
		global $load_espresso_scripts;
		$load_espresso_scripts = true; //This tells the plugin to load the required scripts
		//get the event identifiers
		$event_id = "{$event_id}";
		$event_identifier = "{$event_identifier}";
		
		$show_gravatar = "{$show_gravatar}";

		//get the category identifiers
		$category_identifier = "{$category_identifier}";
		$event_category_id = "{$event_category_id}";
		$category_identifier = ($event_category_id != 'NULL') ? $event_category_id : $category_identifier;

		//Get the extra parameters
		$show_expired = "{$show_expired}";
		$show_secondary = "{$show_secondary}";
		$show_deleted = "{$show_deleted}";
		$show_recurrence = "{$show_recurrence}";
		$paid_only = "{$paid_only}";

		ob_start();
		ee_checkin($event_id, $event_identifier, $category_identifier, $show_gravatar, $show_expired, $show_secondary, $show_deleted, $show_recurrence, $limit, $paid_only);
		$buffer = ob_get_contents();		
		ob_end_clean();
		
		return $buffer;

	}

}
add_shortcode('EE_CHECKIN', 'event_espresso_list_attendees2');
