<?php 

function create_content($title, $body, $link, $uid){
	// entity_create replaces the procedural steps in the first example of
	// creating a new object $node and setting its 'type' and uid property
	$values = array(
	  'type' => 'news',
	  'uid' => $uid,
	  'status' => 1,
	  'comment' => 1,
	  'promote' => 0,
	);
	$entity = entity_create('node', $values);

	$ewrapper = entity_metadata_wrapper('node', $entity);

	$ewrapper->title->set($title);

	$ewrapper->body->set(array('value' => $body));

	$ewrapper->field_link->set($link);

	$entity->field_my_date[LANGUAGE_NONE][0] = array(
	   'value' => date('Y-m-d H:i:s.'),
	   'timezone' => 'UTC',
	   'timezone_db' => 'UTC',
	 );

	$ewrapper->save();
}

function create_system_message($team_name) {
	global $user;

	// Get tournament by team name
	$team_id = db_query("SELECT `team_id` FROM `tr_teams` WHERE `name` = '".$team_name."'")->fetchField();
	$tournaments = db_query("SELECT `day`, `night` FROM `tr_teams_tournaments` WHERE `team_id` = ".$team_id);
	$day = '';
	$night = '';

	foreach($tournaments as $tournament):
		$day = $tournament->day;
		$night = $tournament->night;
		break;
	endforeach;

	if ($day == 1):
		$title = t('NEUE ANMELDUNG FÜR DAS TAGES TURNIER');
		$body = t('Gerade hat sich die Mannschaft').' '.$team_name.' '.t('für das Tages Turnier angemeldet.');
		$link = url('#');
		create_content($title, $body, $link, $user->uid);
	endif;

	if ($night == 1):
		$title = "NEUE ANMELDUNG FÜR DAS NACHT TURNIER";
		$body = t('Gerade hat sich die Mannschaft').' '.$team_name.' '.t('für das Nacht Turnier angemeldet.');
		$link = url('#');
		create_content($title, $body, $link, $user->uid);
	endif;
}

?>