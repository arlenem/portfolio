<?php
date_default_timezone_set('America/New_York');

if ( ! wp_next_scheduled( 'ia_check_veezi' ) ) {
    wp_schedule_event( strtotime('2/20/19 4:00am'), 'daily', 'ia_check_veezi' );
}
add_action( 'ia_check_veezi', 'ia_check_veezi_function' );

function ia_check_veezi_function(){
	$temp = getSessions();
	saveFilms($temp['movieList'], $temp['arr']);
}

if ( ! wp_next_scheduled( 'ia_cleanup_filmsEvent' ) ) {
    wp_schedule_event( strtotime('2/21/19 5:00am'), 'daily', 'ia_cleanup_filmsEvent' );
}

function getSessions() {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, 'https://api.us.veezi.com/v1/session');
	curl_setopt($curl, CURLOPT_HTTPGET, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('VeeziAccessToken: xxxxxxxxxxxxx'));

	$result = curl_exec($curl);

	curl_close($curl);

	$sessions = json_decode($result);
	$movieList = array();
	$pkgList = array(); // setup for future implementation
	$tempList = array();

	if( is_array($sessions) && !empty( $sessions)) {
		foreach( $sessions as $movie) {

			if ($movie->FilmId !== null) {
				if(!in_array($movie->Title, $tempList)) {
					$tempList[] = $movie->Title;
					$movieList[] = array($movie->Title, $movie->FilmId, $movie->Id);
				}
				$sess[$movie->FilmId][] = array(
					'FilmId' => $movie->FilmId, 
					'SeatsAvailable' => $movie->SeatsAvailable, 
					'PreShowStartTime' => $movie->PreShowStartTime, 
					'ScreenId' => $movie->ScreenId, 
					'Id'  => $movie->Id 
				);
			} elseif ($movie->FilmPackageId !== null) {
				if(!in_array($movie->Title, $tempList)) {
					$tempList[] = $movie->Title;
					$pkgList[] = array($movie->Title, $movie->FilmPackageId, $movie->Id);
				}
				$pkgSess[$movie->FilmPackageId][] = array(
					'pkgId' => $movie->FilmPackageId, 
					'SeatsAvailable' => $movie->SeatsAvailable, 
					'PreShowStartTime' => $movie->PreShowStartTime, 
					'ScreenId' => $movie->ScreenId, 
					'Id'  => $movie->Id 
				);
			}
		}
	}
	return array('movieList' => $movieList, 'arr' => $sess, 'pkgList' => $pkgList, 'pkgArr' => $pkgSess);
}

function saveFilms($movieList, $sess){
	if(is_array($movieList) && is_array($sess) && !empty($movieList) && !empty($sess)){
		$t = 1;
		foreach ($movieList as $movie) {
			wp_schedule_single_event( time() + $t * 10, 'ia_saveFilmEvent', array( $movie, $sess[$movie[1]] ) );
			$t++;
		}
		wp_schedule_single_event( time() + ($t * 10 + 60), 'ia_cleanup_filmsEvent');
	}
}

function objectToArray ($object) {
    if(!is_object($object) && !is_array($object)){
        return $object;
    }
    return array_map('objectToArray', (array) $object);
}

function ia_saveFilm($movie, $arr){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, 'https://api.us.veezi.com/v1/film/' . $movie[1]);
	curl_setopt($curl, CURLOPT_HTTPGET, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('VeeziAccessToken: xxxxxxxxxxxxx'));

	$result = curl_exec($curl);

	curl_close($curl);

	$film = json_decode($result);
	include(locate_template("formats/cron-functions/cron-veezi.php"));
	include(locate_template("formats/cron-functions/cron-veezi1.php"));
}
add_action( 'ia_saveFilmEvent', 'ia_saveFilm', 10, 2 );

function ia_cleanup_films(){
	include(locate_template("formats/cron-functions/cron-veezi2.php"));
}
add_action( 'ia_cleanup_filmsEvent', 'ia_cleanup_films' );

?>