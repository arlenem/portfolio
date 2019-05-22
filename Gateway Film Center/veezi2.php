<?php
//This cleans up the posts (films that are past are deleted, films that are now showing are recategorized)
$args = array(
	'post_type' => 'post',
	'posts_per_page' => -1
);
$the_query = new WP_Query( $args );

$date_array = array();

// Remove old date categories
$dump = get_categories( array( 'child_of' => 10, "hide_empty" => false));
if (is_array($dump) && !empty($dump)) {
	foreach ($dump as $d) {
		if( date( "Ymd") >  date( "Ymd", strtotime( $d->name))) {
			wp_delete_term( $d->term_id, 'category');
			echo '<p>'.$d->name.'</p>';
		}
		else {
			$date_array[] = $d->term_id;
		}
	}
}

//create date categories for dates up to 17 days out
for( $i=1; $i<=17; $i++) {
	$valuec = date( "F jS Y", strtotime( "+$i days"));
	if( !term_exists( $valuec, "category")) {
		wp_insert_term( (string)$valuec, "category", array( 'parent'=> 10));
	}
}
// The Loop
if ( $the_query->have_posts() ) : 
	while ( $the_query->have_posts() ) : $the_query->the_post(); 
		$post = get_post();
		$post_fields = get_fields();

		//if not in any of these categories: "do not remove this post", "coming soon", "on demand", "class" and movie release date is older than yesterday
		if (isset($post_fields['movie_release_date']) && !empty($post_fields['movie_release_date'])) {
			if( !has_category( 480) && !has_category( 380) && !has_category( 381) && !has_category( 379) && strtotime( $post_fields['movie_release_date']) < strtotime( "yesterday")) {
				// if it doesn't have a date category
				if( !has_category( $date_array)) {
					wp_delete_post( $post->ID);	
				}
			}

			// if the movie release date is today or in the past, remove the "coming soon" (380) category and add "now showing" (3)
			if (has_category(380) && strtotime( $post_fields['movie_release_date']) < strtotime( "tomorrow")){
				wp_remove_object_terms( $post->ID, 380, 'category' );
				wp_set_object_terms( $post->ID, 3, 'category', true );
			}
		}
	endwhile;
endif;
?>