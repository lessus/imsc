<?php 



header('Content-type: application/json');

$path = $_SERVER['DOCUMENT_ROOT'];
chdir($path."/");
define('DRUPAL_ROOT', getcwd());
require_once './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

include_once(drupal_get_path('module', 'imsctournament') . '/includes/update_tournament.php');
include_once(drupal_get_path('module', 'imscmanagement') . '/includes/system_message.php');

//////////////////////////////
//UPDATE FUNCTIONS
//////////////////////////////

//Update logo
function update_logo($teamID, $fid){

	if($fid != 0){
		//Get old fid
		$fid_old = db_query('SELECT `fid` FROM `tr_teams` WHERE `team_id` = '.$teamID)->fetchField();

		$check_path = db_query('SELECT `uri` FROM `file_managed` WHERE `fid` ='.$fid_old)->fetchField();

		if (strpos($check_path, 'public://logos') !== false) {
		    $file = file_load($fid_old);
	  		file_delete($file);
		}

		db_update('tr_teams')
		->fields(array(
				'fid' => $fid,
			))
		->condition('team_id', $teamID, '=')
		->execute();
	}

}

//Update tournaments
function update_tournaments($teamID, $day, $night, $food){
	db_update('tr_teams_tournaments')
	->fields(array(
			'day' => $day,
			'night' => $night,
			'food' => $food,
		))
	->condition('team_id', $teamID, '=')
	->execute();
}

//Update team name
function update_teamName($teamID, $name){
	db_update('tr_teams')
	->fields(array(
			'name' => $name,
		))
	->condition('team_id', $teamID, '=')
	->execute();
}

//update status - unactive --> active
function update_status_to_active($teamID, $mail, $lastname){

	db_update('tr_status')
	->fields(array(
			'status' => 1,
		))
	->condition('team_id', $teamID, '=')
	->execute();

	increment($teamID);

	$name = db_query('SELECT `name` FROM `tr_teams` WHERE `team_id` = '.$teamID)->fetchField();
	create_system_message($name);

	$values = array(
		"lastname" => $lastname, 
	);

	$lang_name = db_query("SELECT `language` FROM `users` WHERE `mail` = '".$mail."'")->fetchField();

	if($lang_name == 'en'):
		drupal_mail('imsctournament', 'team_confirm_en', $mail, 'en', $values);
	elseif($lang_name == 'de'):
		drupal_mail('imsctournament', 'team_confirm_de', $mail, 'de', $values);
	elseif($lang_name == 'fr'):
		drupal_mail('imsctournament', 'team_confirm_fr', $mail, 'fr', $values);
	elseif($lang_name == 'it'):
		drupal_mail('imsctournament', 'team_confirm_it', $mail, 'it', $values);
	endif;	

}

//update status to rejected
function update_status_to_rejected($teamID, $mail, $lastname){
	$old_status = db_query('SELECT `status` FROM `tr_status` WHERE `team_id` = '.$teamID)->fetchField();

	if($old_status == 1) decrement($teamID);

	db_update('tr_status')
	->fields(array(
			'status' => 2,
		))
	->condition('team_id', $teamID, '=')
	->execute();

	$values = array(
		"lastname" => $lastname, 
	);

	$lang_name = db_query("SELECT `language` FROM `users` WHERE `mail` = '".$mail."'")->fetchField();

	if($lang_name == 'en'):
		drupal_mail('imsctournament', 'team_reject_en', $mail, 'en', $values);
	elseif($lang_name == 'de'):
		drupal_mail('imsctournament', 'team_reject_de', $mail, 'de', $values);
	elseif($lang_name == 'fr'):
		drupal_mail('imsctournament', 'team_reject_fr', $mail, 'fr', $values);
	elseif($lang_name == 'it'):
		drupal_mail('imsctournament', 'team_reject_it', $mail, 'it', $values);
	endif;	
}

//update status - active --> unactive
function update_status_to_inactive($teamID){

	$old_status = db_query('SELECT `status` FROM `tr_status` WHERE `team_id` = '.$teamID)->fetchField();

	if($old_status == 1) decrement($teamID);

	db_update('tr_status')
	->fields(array(
			'status' => 0,
		))
	->condition('team_id', $teamID, '=')
	->execute();
}

//update payment
function update_payment($teamID, $payment){

	db_update('tr_status')
	->fields(array(
			'payment_status' => $payment,
		))
	->condition('team_id', $teamID, '=')
	->execute();
}

//update captain
function update_captain($teamID, $firstname, $lastname, $street, $postcode, $city, $location, $country, $email, $phoneCountry, $phoneCode, $phone, $birthdate, $confirm, $request, $information){

	db_update('tr_captains')
	->fields(array(
			'firstname' => $firstname,
			'lastname' => $lastname,
			'street' => $street,
			'postcode' => $postcode,
			'town' => $city,
			'city' => $location,
			'country' => $country,
			'email' => $email,
			'phone_country' => $phoneCountry,
			'phone_code' => $phoneCode,
			'phone' => $phone,
			'birthdate' => $birthdate,
			'confirm' => $confirm,
			'request' => $request,
			'information' => $information,
		))
	->condition('team_id', $teamID, '=')
	->execute();
}

//update player
function update_player($playerID , $firstname, $lastname){

	db_update('tr_players')
	->fields(array(
			'firstname' => $firstname,
			'lastname'	=> $lastname,
		))
	->condition('player_id', $playerID, '=')
	->execute();
}

//update sponsor data
function update_sponsor_data($sponsor_id, $data){

	db_update('tr_sponsor_data')
	->fields(array(
			'person' => $data['person'],
			'street' => $data['street'],
			'postcode' => $data['postcode'],
			'city' => $data['city'],
			'location' => $data['location'],
			'country' => $data['country'],
			'phone_country' => $data['phone_country'],
			'phone_code' => $data['phone_code'],
			'phone' => $data['phone'],
			'email' => $data['email'],
		))
	->condition('sponsor_id', $sponsor_id, '=')
	->execute();
}

//update sponsor status
function update_sponsor_status($sponsor_id, $data){

	db_update('tr_sponsor_status')
	->fields(array(
			'type' => $data['type'],
		))
	->condition('sponsor_id', $sponsor_id, '=')
	->execute();
}

//update sponsor logo
function update_sponsor_logo($sponsor_id, $fid){

	$fid = db_query("SELECT `fid` FROM `tr_sponsor_data` WHERE `sponsor_id`=".$sponsor_id)->fetchField();

	$file = file_load($fid);
    file_delete($file);

	db_update('tr_sponsor_data')
	->fields(array(
			'fid' => $fid,
		))
	->condition('sponsor_id', $sponsor_id, '=')
	->execute();
}

//////////////////////////////
//TEAM
//////////////////////////////



//REMOVE TEAM
if($_POST['name'] === "remove_team"){
	$teamID = $_POST['id'];

	decrement($teamID);

	db_delete('tr_captains')
		->condition('team_id' , $teamID, "=")
		->execute();

	db_delete('tr_players')
		->condition('team_id' , $teamID , "=")
		->execute();

	db_delete('tr_status')
		->condition('team_id' , $teamID , "=")
		->execute();

	db_delete('tr_teams')
		->condition('team_id' , $teamID , "=")
		->execute();

	db_delete('tr_teams_tournaments')
		->condition('team_id' , $teamID , "=")
		->execute();

	echo json_encode("remove");
}

//UPDATE ALL DATA OF TEAM

if($_POST['name'] === "update_data_of_team"){
	$teamID = $_POST['id'];
	$captain = $_POST['captain'];
	$team = $_POST['team'];
	$players = $_POST['players'];
	
	//update captain
	$birthdate = $captain['day'].' - '.$captain['month'].' - '.$captain['year'];
	if($captain['confirm'] == 'true'):
		$confirm = 1;
	elseif($captain['confirm'] == 'false'):
		$confirm = 0;
	endif;

	update_captain($teamID, $captain['firstname'], $captain['lastname'], $captain['street'], $captain['postcode'], $captain['city'], $captain['location'], $captain['country'], $captain['email'], $captain['phoneCountry'], $captain['phoneCode'], $captain['phone'], $birthdate, $confirm, $captain['request'], $captain['information']);

	//update logo
	update_logo($teamID, $team['logo']);

	//update tournaments
	if($team['day'] == 'true'):
		$day = 1;
	else: 
		$day = 0;
	endif;

	if($team['night'] == 'true'):
		$night = 1;
	else: 
		$night = 0;
	endif;

	if($team['food'] == 'true'):
		$food = 1;
	else: 
		$food = 0;
	endif;

	update_tournaments($teamID, $day, $night, $food);

	//update team name
	update_teamName($teamID, $team['name']);

	//Update status
	$old_status = db_query('SELECT `status` FROM `tr_status` WHERE `team_id` = '.$teamID)->fetchField();

	if(($team['status'] == 1) AND ($old_status != 1)):
		update_status_to_active($teamID, $captain['email'], $captain['lastname']);
	elseif(($team['status'] == 2) AND ($old_status != 2)):
		update_status_to_rejected($teamID, $captain['email'], $captain['lastname']);
	elseif(($team['status'] == 0) AND ($old_status != 0)):
		update_status_to_inactive($teamID);
	endif;
	
	//update payment
	update_payment($teamID, $team['payment']);

	//update players
	foreach($players as $player):
		update_player($player['id'] , $player['firstname'], $player['lastname']);
	endforeach;

	echo json_encode($confirm);
}

//BY USER
if($_POST['name'] === "update_data_of_team_by_user"){
	$teamID = $_POST['id'];
	$team = $_POST['team'];
	$players = $_POST['players'];

	//update logo
	update_logo($teamID, $team['logo']);


	//update team name
	update_teamName($teamID, $team['name']);

	//update players
	foreach($players as $player):
		update_player($player['id'] , $player['firstname'], $player['lastname']);
	endforeach;

	echo json_encode($confirm);
}


//UPDATE GROUP
elseif($_POST['name'] === "save-group"){
	$group 		= $_POST['group'];
	$team 		= $_POST['team'];
	$tournament = $_POST['tournament'];
	$date 		= date('Y-m-d H:i:s.');
	$date_name	= $tournament.'_date';

	db_update('tr_teams')
	->fields(array(
			$tournament => $group[0],
			$date_name => $group,
		))
	->condition('name', $team, '=')
	->execute();

	echo json_encode("updated");
}


//UPDATE MATCH
elseif($_POST['name'] === "save_match"){
	$match_id 		= $_POST['id'];
	$url	 		= $_POST['url'];
	$team_1_pts		= $_POST['team_1_pts'];
	$team_2_pts		= $_POST['team_2_pts'];
	$def 			= $_POST['def'];

	if($url == ''):
		db_update('tr_matches')
		->fields(array(
				'team_1_pts'	=>	$team_1_pts,
				'team_2_pts'	=>	$team_2_pts,
				'def'			=>	(string)$def,
				'save'			=>	1,
			))
		->condition('match_id', $match_id, '=')
		->execute();
	else:
		db_update('tr_matches')
		->fields(array(
				'team_1_pts'	=>	$team_1_pts,
				'team_2_pts'	=>	$team_2_pts,
				'video'			=>	$url,
				'def'			=>	(string)$def,
				'save'			=>	1,
			))
		->condition('match_id', $match_id, '=')
		->execute();
	endif;

	echo json_encode($team_1_pts);
}

//with team
elseif($_POST['name'] === "save_match_with_team"){
	$match_id 		= $_POST['id'];
	$url	 		= $_POST['url'];
	$team_1_pts		= $_POST['team_1_pts'];
	$team_2_pts		= $_POST['team_2_pts'];
	$team_2_id		= $_POST['team_2_id'];
	$def 			= $_POST['def'];
 
	if($url == ''):
		db_update('tr_matches')
		->fields(array(
				'team_1_pts'	=>	$team_1_pts,
				'team_2_pts'	=>	$team_2_pts,
				'team_id_2'		=>	$team_2_id,
				'save'			=>	1,
				'def'			=>	(string)$def,
			))
		->condition('match_id', $match_id, '=')
		->execute();
	else:
		db_update('tr_matches')
		->fields(array(
				'team_1_pts'	=>	$team_1_pts,
				'team_2_pts'	=>	$team_2_pts,
				'team_id_2'		=>	$team_2_id,
				'video'			=>	$url,
				'save'			=>	1,
				'def'			=>	(string)$def,
			))
		->condition('match_id', $match_id, '=')
		->execute();
	endif;

	echo json_encode('test');
}

elseif($_POST['name'] === "clear_tournament_data"){
	$nid = $_POST['nid'];

	db_delete('tr_matches')
    ->condition('tournament_id' , $nid, "=")
    ->execute();

    db_delete('tr_ranking')
    ->condition('tournament_id' , $nid, "=")
    ->execute();

    include_once(drupal_get_path('module', 'imscmanagement') . '/includes/create_fixture.php');
    createFixture($nid);
    include_once(drupal_get_path('module', 'imscmanagement') . '/includes/refresh_fixture.php');
    refreshFixture($nid);

    // drupal_goto(url("management/tournament-overview"));
    // drupal_set_message('Data of tournament was cleared.');
}


////////////////////
//SPONSOR
////////////////////
//REMOVE SPONSOR
elseif($_POST['name'] === "sponsor_remove"){
	$sponsorID = $_POST['id'];

	// $fid = db_query("SELECT `fid` FROM `tr_sponsor_data` WHERE `sponsor_id`=".$sponsorID)->fetchField();

	// $file = file_load($fid);
 //    file_delete($file);

	db_delete('tr_sponsor_data')
		->condition('sponsor_id' , $sponsorID, "=")
		->execute();

	db_delete('tr_sponsor_status')
		->condition('sponsor_id' , $sponsorID , "=")
		->execute();

	echo json_encode("Sponsor removed");
}

//SPONSOR LOGO REMOVE
elseif($_POST['name'] === "sponsor_logo_remove"){
	$sponsor_id = $_POST['id'];

	$fid = $_POST['fid'];

	$file = file_load($fid);
    file_delete($file);

	db_update('tr_sponsor_data')
	->fields(array(
			'fid' => 0,
		))
	->condition('sponsor_id', $sponsor_id, '=')
	->execute();

	echo json_encode("logo remove");
}

//UPDATE SPONSOR
elseif($_POST['name'] === "update_sponsor_data"){
	$sponsor_id = $_POST['id'];
	$data = $_POST['data'];

	update_sponsor_data($sponsor_id , $data);

	$status = $_POST['status'];

	update_sponsor_status($sponsor_id , $status);

	if(($_POST['fid'] != 0) && ($_POST['fid'] != '') ):
		update_sponsor_logo($sponsor_id , $_POST['fid']);
	endif;

	echo json_encode("sponsor update");


}


?>