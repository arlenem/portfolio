<?php
$film = objectToArray($film);

if( is_array($movie) && !empty( $movie)) {
	global $wpdb;
	// store the film as post, categorize as 'Movie' (2) and 'Now Showing' (3)
	$title_str = stripslashes( $movie[0] ); 
	$title_str = str_replace("'", '', $title_str);
	if (isset($film['Synopsis']) && !is_null($film['Synopsis'])){
		$desc = $film['Synopsis']; 
	} else {
		$desc = "Synopsis unavailable";
	}
	$post = $wpdb->get_row("SELECT * FROM wp_posts WHERE post_title = '$title_str' AND post_type = 'post'", 'ARRAY_A');
	if( !$post) { // category 2 & 380 = 'Movie' & 'Coming Soon'
		$new_post = array(
		    'post_title' => $title_str,
		    'post_content' => $desc,
		    'post_status' => 'publish',
		    'post_date' => date('Y-m-d H:i:s'),
		    'post_type' => 'post',
			'post_category' => array("2", "380")
		); 
		$post_id = wp_insert_post($new_post);
		$hours = floor((int)$film['Duration'] / 60);
		$minutes = (int)$film['Duration'] % 60;
		update_post_meta($post_id, "movie_rating", $film['Rating']);
		update_post_meta($post_id, "_movie_rating", 'field_542c483fbbd84');
		update_post_meta($post_id, "movie_hours", $hours);
		update_post_meta($post_id, "_movie_hours", 'field_542c484ebbd85');
		update_post_meta($post_id, "movie_minutes", $minutes);
		update_post_meta($post_id, "_movie_minutes", 'field_542c485cbbd86');
		update_post_meta($post_id, "old_sessions", '');
	}
	else{ 
		$new_post = array(
			'ID' => $post['ID'],
			'post_status' => 'publish',
			'post_type' => 'post'
		); 
		$post_id = wp_update_post($new_post);
		
		if( in_category( "coming-soon", $post["ID"])) {
			$field = get_field('movie_release_date', $post["ID"]);
			$tenDate = date( "Ymd");
			//if the movie is currently showing (it's after the release date), update categories and add Now Showing, removes "Coming Soon"
			if( $field <= date( "Ymd", strtotime( $tenDate))) {
				wp_remove_object_terms( $post["ID"], 'coming-soon', 'category' );
				//clears taxonomy cache
				delete_option("classified-category_children");
				wp_set_post_categories( $post['ID'], "3", true); // this appends "Now Showing" Category
			}
		}
	}
	$id_str = $movie[1];
	update_post_meta($post_id, "movie_ID", (string)$id_str);
} ?>