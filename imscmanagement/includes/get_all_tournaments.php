<?php 

//List of tournaments on Tournament overview

// function get_all_tournaments(){
// 	$results = db_query('SELECT `tournament_id` , `name` , `teams` , `groups` , `year` , `month` , `day` , `hour`, `minute` , `deadline_year` , `deadline_month` , `deadline_day` , `status_default`
// 		FROM `tr_tournaments` ORDER BY `year` DESC');

// 	$tournaments = array(
// 		'day' 	=> array(),
// 		'night' => array(),
// 		);

// 	foreach($results as $tournament):
// 		$tournament_name = $tournament->name;
// 		$tournaments[$tournament_name][] = array(
// 			'tournament_id'	=> $tournament->tournament_id,
// 			'teams'			=> $tournament->teams,
// 			'groups'		=> $tournament->groups,
// 			'year'			=> $tournament->year,
// 			'date'			=> $tournament->day.'-'.$tournament->month.'-'.$tournament->year,
// 			'begin'			=> $tournament->hour.':'.$tournament->minute,
// 			'deadline'		=> $tournament->deadline_day.'-'.$tournament->deadline_month.'-'.$tournament->deadline_year,
// 			'default'		=> $tournament->status_default,
// 			);
// 	endforeach;

// 	return $tournaments;
// }

?>