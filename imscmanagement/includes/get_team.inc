<?php 

	function getData($id){
		//SELECT ALL INFORMATION ABOUT TEAM

		$result = db_query("SELECT tr_teams.name , tr_teams.fid ,  tr_status.created , tr_teams.team_group_day , tr_teams.team_group_night , tr_status.status , tr_status.payment_date , tr_status.payment_status , tr_status.payment_type
			FROM tr_teams
			INNER JOIN tr_status
			ON tr_teams.team_id=tr_status.team_id
			WHERE tr_teams.team_id = ".$id);

		$team = array();
		foreach ($result as $row):
			$team["name"] = $row->name;
			$team["fid"] = $row->fid;
			$team["created"] = $row->created;
			$team['team_group_day'] = $row->team_group_day;
			$team['team_group_night'] = $row->team_group_night;
			$team["status"] = $row->status;
			$team["payment_date"] = $row->payment_date;
			$team["payment_status"] = $row->payment_status;
			$team["payment_type"] = $row->payment_type;
			break;
		endforeach;
		
		$result = db_query("SELECT * FROM `tr_captains` WHERE `team_id` = ".$id);

		$captain = array();
		foreach ($result as $row):
			$captain["firstname"] = $row->firstname;
			$captain["lastname"] = $row->lastname;
			$captain["street"] = $row->street;
			$captain["postcode"] = $row->postcode;
			$captain["town"] = $row->town;
			$captain["city"] = $row->city;
			$captain["country"] = $row->country;
			$captain["phone_country"] = $row->phone_country;
			$captain["phone_code"] = $row->phone_code;
			$captain["phone"] = $row->phone;
			$captain["email"] = $row->email;
			$captain["birthdate"] = $row->birthdate;
			$captain["confirm"] = $row->confirm;
			$captain["request"] = $row->request;
			$captain["information"] = $row->information;
			break;
		endforeach;

		$result = db_query("SELECT tr_players.firstname, tr_players.lastname, tr_players.player_id , tr_player_types.type
			FROM tr_players
			INNER JOIN tr_player_types
			ON tr_players.type_id=tr_player_types.type_id
			WHERE tr_players.team_id=".$id);

		$players = array();
		foreach ($result as $row):
			$players[] = array(
					"player_id" => $row->player_id,
					"firstname" => $row->firstname,
					"lastname" => $row->lastname,
					"type" => $row->type,
				);
		endforeach;

		$result = db_query("SELECT `day` , `night` , `price` , `food` , `food_price` FROM `tr_teams_tournaments` WHERE `team_id` = ".$id);
		$tournament = array();

		foreach ($result as $row):
			$tournament["day"] 			= $row->day;
			$tournament["night"] 		= $row->night;
			$tournament["price"] 		= $row->price;
			$tournament["food"] 		= $row->food;
			$tournament["food_price"] 	= $row->food_price;
			break;
		endforeach;

		$results = array(
			"team" => $team,
			"tournament" => $tournament,
			"captain" => $captain,
			"players" => $players,
		);

		return $results;
	}
	
?>