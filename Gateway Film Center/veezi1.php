<?php
//This processes the films from Veezi & updates showtimes and categories
$flag = false; //Flag to change "Coming Soon" category to "Now Showing"
$todays_date = date('Ymd');
$sessions = $arr;
$watcher_array = array();
$matching_cat = array();
$post_meta = get_post_meta($post_id);
$old_sessions = array();
$old = $post_meta['old_sessions'][0];
if (!empty($old)) {
	$old_sessions = explode(', ', $old);
}

//loop through all sessions and add session values to post_meta and date categories to movie
if(is_array($sessions) && !empty($sessions) && is_array($post_meta)){
	foreach( $sessions as $session) {
		$session_id = $session['Id'];
		if ( !in_array( (string)$session_id, $watcher_array)) {
			$watcher_array[] = (string)$session_id;
			$date_time2 = $session['PreShowStartTime'];
			$date_time = str_replace(['-', 'T', ':'], '', $date_time2);
			$avaseats = $session['SeatsAvailable'];

			if (in_array((string)$session_id, $old_sessions) && (string)$post_meta[ "movie_session_$session_id"][0] === (string)$date_time){
				// There are no changes to movie, adjust seats available (changes based on ticket sales?)
				update_post_meta($post_id, "movie_avaseats_$session_id", (string)$avaseats);
			} else {
				// Update/Add movie session
				update_post_meta($post_id, "movie_session_ID_$session_id", (string)$session_id);
				$cinema_id = $session['ScreenId'];
				update_post_meta($post_id, "movie_cinema_ID_$session_id", (string)$cinema_id);
				update_post_meta($post_id, "movie_session_$session_id", (string)$date_time);
				update_post_meta($post_id, "movie_avaseats_$session_id", (string)$avaseats);
			}

			//Create and set category for the session date
			$date = DateTime::createFromFormat('YmdHis', $date_time);
			if ($date){
				if( $date->format( "H") < 4) {
					date_sub($date, date_interval_create_from_date_string('1 day'));
				}
				if ($date->format('Ymd') == $todays_date) {
					$flag = true;
				}
				$valuem = $date->format('F jS Y');
				// 'Dates' category is 10
				$h = term_exists( $valuem, 'category', 10);
				if (is_array($h) && !empty($h)){
					$holder = (int)$h['term_id'];
					if(!in_array($holder, $matching_cat)) {
						$matching_cat[] = $holder;
					}
				} else {
					$holder = wp_insert_term( (string)$valuem, "category", array( 'parent'=> 10));
					$matching_cat[] = $holder['term_id'];
				}
			}
		}
	}
	$stored_terms = wp_get_post_categories( $post_id, array('fields' => 'all'));
	if(is_array($stored_terms) && !empty($stored_terms)){
		foreach ($stored_terms as $key => $rm) {
			// category '10' is "Dates"
			if (!($rm->parent == 10)) {
				if ($flag && $rm->term_id == 380) {
					//don't preserve the coming soon (380) category if it doesn't fit
				} else {
					//preserve the additional categories
					$matching_cat[] = $rm->term_id;
				}
			}
		}
	}
	if ($flag) { // add now showing (3)
		$matching_cat[] = 3;
	}
	delete_option("classified-category_children");
	wp_set_object_terms( $post_id, $matching_cat, 'category', false );

	// go through $old sessions and delete any postmeta that isn't in $watcher_array
	foreach ($old_sessions as $s) {
		if (!in_array($s, $watcher_array)) {
			delete_post_meta( $post_id, "movie_session_$s");
			delete_post_meta( $post_id, "movie_session_ID_$s");
			delete_post_meta( $post_id, "movie_avaseats_$s");
			delete_post_meta( $post_id, "movie_cinema_ID_$s");
		}
	}

	$post_string = implode(', ', $watcher_array);
	update_post_meta($post_id, 'old_sessions', $post_string);
} 
?>