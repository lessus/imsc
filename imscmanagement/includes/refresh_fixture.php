<?php 

include_once(drupal_get_path('module', 'imsccountdown') . '/includes/get_score.php');

//get teams from group
function get_teams_by_group($group, $id, $tournament){
	
	// $results = db_query("SELECT `team_id` FROM `tr_teams` WHERE `tr_".$tournament."_id` = ".$id." AND `team_group_".$tournament."` = '".$group."'");

	$results = db_query("SELECT tr_teams.team_id
		FROM tr_teams
		INNER JOIN tr_teams_tournaments
		ON tr_teams.team_id = tr_teams_tournaments.team_id
		INNER JOIN tr_status
		ON tr_teams_tournaments.team_id = tr_status.team_id
		WHERE tr_teams.tr_".$tournament."_id = ".$id." AND tr_teams.team_group_".$tournament." = '".$group."' AND tr_teams_tournaments.".$tournament." = 1 AND tr_status.status = 1 ORDER BY tr_teams.team_group_".$tournament."_date ASC");

	$num_rows = $results->rowCount();

	$teams = array(0,0,0,0);

	
	$i = 0;
	if($num_rows):
		
		foreach($results as $team):
			$teams[$i] = $team->team_id;
			$i++;

		endforeach;

	endif;

	return $teams;
}

//get winner from group
function get_winner_from_group($count_groups, $id, $tournament, $letter){

	
	
	$winners = TRUE;


	// for ($letter = 0 ; $letter < $count_groups ; $letter++):



		// if($winners == FALSE) break;



		$results = db_query("SELECT `team_id_1` , `team_id_2` FROM `tr_matches` WHERE `group_name_short` = '".$letter."' AND `tournament_id` = ".$id);
		
		// print $letter;

		$j = 0;
		$count_teams = array();
		foreach($results as $row):
			if(($row->team_id_1 != 0) && ($row->team_id_2 != 0)):
				$count_teams[$row->team_id_1] = array(
					'id'			=> $row->team_id_1,
					);
				$count_teams[$row->team_id_2] = array(
					'id'			=> $row->team_id_2,
					);
			endif;
		endforeach;

		

		if(count($count_teams) == 4):
			
			$results = db_query("SELECT * FROM `tr_matches` WHERE `group_name_short` = '".$letter."' AND `tournament_id` = ".$id);

			

			foreach($results as $row):
				if($row->save == 0)	$winners = FALSE;
				else $winners = TRUE;
		
			endforeach;

		
		
			$teams = getTeams($tournament , $letter, $id);
	
			$add_to_ranking = FALSE;

			if($winners == TRUE) $add_to_ranking = TRUE;

			//If all values are unique
			if($add_to_ranking == TRUE):
					
				$rank = 1;
	

				foreach($teams as $value):

					db_update('tr_ranking')
					->fields(array(
						'team_id' => $value['team_id'],
					))
					->condition('tournament_id', $id, '=')
					->condition('rank', $rank, '=')
					->condition('group_name', $letter, '=')
					->execute();

					$rank++;
		
				endforeach;
				
			else:

			endif;
		else:
			
		endif;
	// endfor;

	return $winners;
}

function unique_multidim_array($array, $key) { 
    $temp_array = array(); 
    $i = 0; 
    $key_array = array(); 
    
    foreach($array as $val) { 
        if (!in_array($val[$key], $key_array)) { 
            $key_array[$i] = $val[$key]; 
            $temp_array[$i] = $val; 
        } 
        $i++; 
    } 
    return $temp_array; 
} 

//Check round of 16
function check_latest($id , $groups){

	$check = TRUE;

	foreach($groups as $group):

		if($check == FALSE) break;

		$results = db_query("SELECT `team_id_1` , `team_id_2` , `team_1_pts` , `team_2_pts` FROM `tr_matches` WHERE `tournament_id` = ".$id." AND `group_name_short` = '".$group."'");
		
		foreach($results as $row):

			if($row->team_id_1 != 0 && $row->team_id_2 != 0):
				if($row->team_1_pts == $row->team_2_pts) $check = FALSE;
			endif;

			break;
		endforeach;
		
	endforeach;

	return $check;
}

//Get winner from round of 16 <-> Final
function get_winner($group, $id){
	$results = db_query("SELECT `team_id_1` , `team_id_2` , `team_1_pts` , `team_2_pts` FROM `tr_matches` WHERE `tournament_id` = ".$id." AND `group_name_short` = '".$group."'");

	foreach($results as $group):
		if($group->team_1_pts > $group->team_2_pts):
			return $group->team_id_1;
		elseif($group->team_1_pts < $group->team_2_pts):
			return $group->team_id_2;
		else:
			return 0;
		endif;

		break;
	endforeach;
}

//Get loser from round of 16 <-> Final
function get_loser($group, $id){
	$results = db_query("SELECT `team_id_1` , `team_id_2` , `team_1_pts` , `team_2_pts` FROM `tr_matches` WHERE `tournament_id` = ".$id." AND `group_name_short` = '".$group."'");

	foreach($results as $group):
		if($group->team_1_pts < $group->team_2_pts):
			return $group->team_id_1;
		elseif($group->team_1_pts > $group->team_2_pts):
			return $group->team_id_2;
		else:
			return 0;
		endif;

		break;
	endforeach;
}

//Get the best

// function array_msort($array, $cols)
// {
//     $colarr = array();
//     foreach ($cols as $col => $order) {
//         $colarr[$col] = array();
//         foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
//     }
//     $eval = 'array_multisort(';
//     foreach ($cols as $col => $order) {
//         $eval .= '$colarr[\''.$col.'\'],'.$order.',';
//     }
//     $eval = substr($eval,0,-1).');';
//     eval($eval);
//     $ret = array();
//     foreach ($colarr as $col => $arr) {
//         foreach ($arr as $k => $v) {
//             $k = substr($k,1);
//             if (!isset($ret[$k])) $ret[$k] = $array[$k];
//             $ret[$k][$col] = $array[$k][$col];
//         }
//     }
//     return $ret;

// }

function get_the_best($id){
	$results = db_query('SELECT `team_id` FROM `tr_ranking` WHERE `rank` = 1 AND `tournament_id` = '.$id);

	$j = 0;
	$teams = array();
	foreach($results as $row):
		$teams[$row->team_id] = array(
			'pts'			=> 0,
			'difference'	=> 0,
			'goals'			=> 0,
			'id'			=> $row->team_id,
			);
	endforeach;

	foreach($teams as $team):

		$results = db_query("SELECT `team_id_1` , `team_id_2` , `team_1_pts` , `team_2_pts` FROM `tr_matches` WHERE `tournament_id` = ".$id." AND (`team_id_1` = ".$team['id']." OR `team_id_2` = ".$team['id'].")");

		foreach($results as $row):
			if($team['id'] == $row->team_id_1):

				//pts
				if($row->team_1_pts > $row->team_2_pts):
					$teams[$row->team_id_1]['pts'] += 3;
				elseif($row->team_1_pts == $row->team_2_pts):
					$teams[$row->team_id_1]['pts'] += 1;
				endif;
				//goals difference
				$teams[$row->team_id_1]['difference'] += abs($row->team_1_pts - $row->team_2_pts);

				//number of goals
				$teams[$row->team_id_1]['goals'] += $row->team_1_pts;

			elseif($team['id'] == $row->team_id_2):

				//pts
				if($row->team_2_pts > $row->team_1_pts):
					$teams[$row->team_id_2]['pts'] += 3;
				elseif($row->team_1_pts == $row->team_2_pts):
					$teams[$row->team_id_2]['pts'] += 1;
				endif;
				//goals difference
				$teams[$row->team_id_2]['difference'] += abs($row->team_1_pts - $row->team_2_pts);

				//number of goals
				$teams[$row->team_id_2]['goals'] += $row->team_2_pts;

			endif;
		endforeach;

	endforeach;

	$after_sort = array_msort($teams, array('pts'=>SORT_DESC, 'difference'=>SORT_DESC , 'goals'=>SORT_DESC));

	reset($after_sort);
	$first_key = key($after_sort);

	return $first_key;
}

function check_game($game, $id){
	$check = db_query("SELECT `save` FROM `tr_matches` WHERE `tournament_id` = ".$id." AND `group_name_short` = '".$game."'")->fetchField();

	if($check == 1):
		return TRUE;
	else:
		return FALSE;
	endif;
}

//Refresh fixture
function refreshFixture($id){

	$node = node_load($id);
    $node_wrapper = entity_metadata_wrapper('node', $node);

   	//Get tournament type
   	$tournament = $node_wrapper->field_tournament_type->value();

   	//Get count groups
   	$count_groups = $node_wrapper->field_tournament_groups->value();

	$matches = db_query('SELECT `match_id` , `game` , `group_name` , `group_name_short` , `team_id_1` , `team_id_2` FROM `tr_matches` WHERE `tournament_id` = '.$id);

	//iterators for groups
	$group_iterator = 1;
	$group_pair = 0;

	//iterators for round of 16
	$round_iterator = 1;

	//iterators for quarter final
	$quarter_iterator = 1;

	//iterators for semi final
	$semi_iterator = 1;

	//Check winners from groups
	$winners = FALSE;

	//Check winners from round of 16
	$round = FALSE;

	//Check winners from quarter
	$quarter = FALSE;

	//Check winners from quarter
	$semi = FALSE;

	foreach($matches as $match):

		//For groups
		if (strpos($match->group_name, 'Group') !== false):

				$letter = $match->group_name_short;
	
				if($count_groups > 5):
					if($group_iterator % ($count_groups/2) == 1):
						$group_pair++;
					endif;
				elseif($count_groups == 5):
					if($group_iterator % 3 == 1):
						$group_pair++;
					endif;	
				elseif($count_groups == 4 OR $count_groups == 3):
					if($group_iterator % $count_groups == 1):
						$group_pair++;
					endif;
				endif;

				if($group_pair == 7):
					$group_pair = 1;
				endif;

				$versus = array(
					1 => array(0,1),
					2 => array(2,3),
					3 => array(0,2),
					4 => array(1,3),
					5 => array(0,3),
					6 => array(1,2),
					);

				$teams = get_teams_by_group($letter, $id, $tournament);
	
				for($i = 0; $i <= 1; $i++):
					$team_nr = 'team_id_'.($i+1);
	
					if(isset($teams[$versus[$group_pair][$i]])):					
						$match->$team_nr = $teams[$versus[$group_pair][$i]];
					endif;

				endfor;

				db_update('tr_matches')
						->fields(array(
							'team_1_placeholder' => $letter.($versus[$group_pair][0]+1),
							'team_2_placeholder' => $letter.($versus[$group_pair][1]+1),
						))
						->condition('match_id', $match->match_id, '=')
						->execute();
	

				

				if($group_pair == 7):
					$group_iterator = 1;
				else:
					$group_iterator++;
				endif;

		//For round of 16

		elseif($match->group_name == 'Round of sixteen'):

			if($winners == FALSE):	
				
			endif;

			//For 8 groups
			if($count_groups == 8):
				$versus = array(
					1 => array('A1','B2'),
					2 => array('C1','D2'),
					3 => array('B1','A2'),
					4 => array('D1','C2'),
					5 => array('E1','F2'),
					6 => array('G1','H2'),
					7 => array('F1','E2'),
					8 => array('H1','G2'),
					);
			elseif($count_groups == 6):
				$versus = array(
					1 => array('A2','C2'),
					2 => array('B1','input'),
					3 => array('D1','input'),
					4 => array('A1','input'),
					5 => array('C1','input'),
					6 => array('F1','E2	'),
					7 => array('E1','D2'),
					8 => array('B2','F2'),
					);
			elseif($count_groups == 5):
				$versus = array(
					1 => array('A1','C3'),
					2 => array('B1','D3'),
					3 => array('C1','E3'),
					4 => array('D1','A3'),
					5 => array('E1','B3'),
					6 => array('C2','E3'),
					7 => array('B2','D3'),
					8 => array('A2','input'),
					);
			elseif($count_groups == 4):
				$versus = array(
					1 => array('A1','B4'),
					2 => array('B1','A4'),
					3 => array('C1','D4'),
					4 => array('D1','C4'),
					5 => array('A2','B3'),
					6 => array('B2','A3'),
					7 => array('C2','D3'),
					8 => array('D2','C3'),
					);
			endif;

			// if($winners == TRUE):
				// print '2';
			
				for($i = 0; $i <= 1; $i++):

					
					$group = $versus[$round_iterator][$i];

					$current_letter =  $versus[$round_iterator][$i][0];

					if($current_letter != 'Z' AND $current_letter != 'i'):
					
						$winners = get_winner_from_group($count_groups, $id, $tournament, $current_letter);
					endif;
					// print_r($group);
					
					if($group == 'input'):
						$win = 'input';
						$input = 'input_'.(string)($i+1);
						db_update('tr_matches')
						->fields(array(
							$input => 1,
						))
						->condition('match_id', $match->match_id, '=')
						->execute();

					// elseif($group == 'Z1'):
					// 	//get the best team from groups
					// 	$win = get_the_best($id);
					else:
						$win = db_query("SELECT `team_id` FROM `tr_ranking` WHERE `group_name` = '".$group[0]."' AND `rank` = ".$group[1]." AND `tournament_id` = ".$id)->fetchField();
					endif;

					$team_nr = 'team_id_'.($i+1);				
					$match->$team_nr = $win;


				endfor;

				
				
			// else:
			// 	$match->team_id_1 = 0;
			// 	$match->team_id_2 = 0;
			// endif;

			// print $versus[$round_iterator][0].' '.$versus[$round_iterator][1].'//';

			if(substr($versus[$round_iterator][0] , -1) == 1):
				$title_1 = 'W';
				// print substr($versus[$round_iterator][0] , -1);
			elseif(substr($versus[$round_iterator][0] , -1) == 2):
				$title_1 = 'S';
				// print substr($versus[$round_iterator][0] , -1);
			elseif(substr($versus[$round_iterator][0] , -1) == 3):
				$title_1 = 'T';
			elseif(substr($versus[$round_iterator][0] , -1) == 4):
				$title_1 = 'L';
			endif;

			if(substr($versus[$round_iterator][1] , -1) == 1):
				$title_2 = 'W';
				// print substr($versus[$round_iterator][1] , -1);
			elseif(substr($versus[$round_iterator][1] , -1) == 2):
				$title_2 = 'S';
				// print substr($versus[$round_iterator][1] , -1);
			elseif(substr($versus[$round_iterator][1] , -1) == 3):
				$title_2 = 'T';
			elseif(substr($versus[$round_iterator][1] , -1) == 4):
				$title_2 = 'L';
			endif;

			if($versus[$round_iterator][1] == 'Z1'):
				db_update('tr_matches')
					->fields(array(
						'team_1_placeholder' => $title_1.substr($versus[$round_iterator][0], 0 , 1),
						'team_2_placeholder' => 'FT',
					))
					->condition('match_id', $match->match_id, '=')
					->execute();
			elseif($versus[$round_iterator][1] == 'input'):
				// print $title_1.substr($versus[$round_iterator][0], 0 , 1).' + '.$title_2.substr($versus[$round_iterator][1], 0 , 1).'///';
				db_update('tr_matches')
					->fields(array(
						'team_1_placeholder' 	=> $title_1.substr($versus[$round_iterator][0], 0 , 1),
						'team_2_placeholder' 	=> 'input',
						'input_2'				=> 1,
					))
					->condition('match_id', $match->match_id, '=')
					->execute();
			else:
				// print $title_1.substr($versus[$round_iterator][0], 0 , 1).' + '.$title_2.substr($versus[$round_iterator][1], 0 , 1).'///';
				db_update('tr_matches')
					->fields(array(
						'team_1_placeholder' => $title_1.substr($versus[$round_iterator][0], 0 , 1),
						'team_2_placeholder' => $title_2.substr($versus[$round_iterator][1], 0 , 1),
					))
					->condition('match_id', $match->match_id, '=')
					->execute();
			endif;
		
			$round_iterator++;

		//For quarter finale

		elseif($match->group_name == 'Quarter final'):


			// if($count_groups != 3):

			// 	if($round == FALSE):

			// 		$round = check_latest($id , array('AF1' , 'AF2' , 'AF3' , 'AF4' , 'AF5' , 'AF6' , 'AF7' , 'AF8'));
			// 	endif;

			if($count_groups == 3):
				if($round == FALSE):		
					$round = get_winner_from_group($count_groups, $id);
				endif;
			endif;

			// $round == TRUE;

			//For 8 groups
			if($count_groups == 8):
				$versus = array(
					1 => array('AF5','AF6'),
					2 => array('AF1','AF2'),
					3 => array('AF7','AF8'),
					4 => array('AF3','AF4'),
					);
			//For 6 groups
			elseif($count_groups == 6):
				$versus = array(
					1 => array('AF1','AF3'),
					2 => array('AF2','AF6'),
					3 => array('AF5','AF7'),
					4 => array('AF4','AF8'),
					);
			//For 5 groups
			elseif($count_groups == 5):
				$versus = array(
					1 => array('AF1','AF2'),
					2 => array('AF3','AF4'),
					3 => array('AF5','AF6'),
					4 => array('AF7','AF8'),
					);
			//For 4 groups
			elseif($count_groups == 4):
				$versus = array(
					1 => array('AF4','AF5'),
					2 => array('AF3','AF6'),
					3 => array('AF2','AF7'),
					4 => array('AF1','AF8'),
					);
			//For 3 groups
			elseif($count_groups == 3):
				$versus = array(
					1 => array('A1','input'),
					2 => array('B1','input'),
					3 => array('B2','C2'),
					4 => array('C1','A2'),
					);
			endif;


			// if($round == TRUE):

				for($i = 0; $i <= 1; $i++):

					$group = $versus[$quarter_iterator][$i];

					if($count_groups != 3):
						$check_game = check_game($group, $id);
					endif;

					$team_nr = 'team_id_'.($i+1);

					if($check_game == TRUE):

						if($group == 'input'):
							$win = 'input';
							$input = 'input_'.(string)($i+1);
							db_update('tr_matches')
							->fields(array(
								$input => 1,
							))
							->condition('match_id', $match->match_id, '=')
							->execute();

						else:
							if($count_groups == 3):
								if($round == TRUE):
									$win = db_query("SELECT `team_id` FROM `tr_ranking` WHERE `group_name` = '".$group[0]."' AND `rank` = ".$group[1]." AND `tournament_id` = ".$id)->fetchField();
								else:
									$win = 0;
								endif;
							else:
								$win = get_winner($group, $id);
							endif;
						endif;

						
						$match->$team_nr = $win;
					else:
						$match->$team_nr = 0;
					endif;
				endfor;
			// else:
			// 	$match->team_id_1 = 0;
			// 	$match->team_id_2 = 0;	
			// endif;

			if($count_groups == 3):
				if(substr($versus[$quarter_iterator][0] , -1) == 1):
					$title_1 = 'W';
				elseif(substr($versus[$quarter_iterator][0] , -1) == 2):
					$title_1 = 'S';
				elseif(substr($versus[$quarter_iterator][0] , -1) == 3):
					$title_1 = 'T';
				elseif(substr($versus[$quarter_iterator][0] , -1) == 4):
					$title_1 = 'L';
				endif;

				if(substr($versus[$quarter_iterator][1] , -1) == 1):
					$title_2 = 'W';
				elseif(substr($versus[$quarter_iterator][1] , -1) == 2):
					$title_2 = 'S';
				elseif(substr($versus[$quarter_iterator][1] , -1) == 3):
					$title_2 = 'T';
				elseif(substr($versus[$quarter_iterator][1] , -1) == 4):
					$title_2 = 'L';
				endif;

				if($versus[$quarter_iterator][1] == 'input'):
				db_update('tr_matches')
					->fields(array(
						'team_1_placeholder' 	=> $title_1.$versus[$quarter_iterator][0],
						'team_2_placeholder' 	=> 'input',
						'input_2'				=> 1,
					))
					->condition('match_id', $match->match_id, '=')
					->execute();
				else:
					db_update('tr_matches')
					->fields(array(
						'team_1_placeholder' => $title_1.$versus[$quarter_iterator][0],
						'team_2_placeholder' => $title_2.$versus[$quarter_iterator][1],
					))
					->condition('match_id', $match->match_id, '=')
					->execute();
				endif;
			else:
				db_update('tr_matches')
					->fields(array(
						'team_1_placeholder' => 'WAF'.substr($versus[$quarter_iterator][0], -1),
						'team_2_placeholder' => 'WAF'.substr($versus[$quarter_iterator][1], -1),
					))
					->condition('match_id', $match->match_id, '=')
					->execute();
			endif;

			$quarter_iterator++;

		

		//For semi final

		elseif($match->group_name == 'Semi final'):

			// if($quarter == FALSE):
			// 	$quarter = check_latest($id , array('VF1' , 'VF2' , 'VF3' , 'VF4'));
			// endif;

			//For 8 groups
			if($count_groups == 8):
				$versus = array(
					1 => array('VF2','VF1'),
					2 => array('VF4','VF3'),
					);
			//For 6 groups
			elseif($count_groups == 6):
				$versus = array(
					1 => array('VF1','VF2'),
					2 => array('VF3','VF4'),
					);
			//For 5 groups
			elseif($count_groups == 5):
				$versus = array(
					1 => array('VF1','VF3'),
					2 => array('VF2','VF4'),
					);
			//For 4 groups
			elseif($count_groups == 4):
				$versus = array(
					1 => array('VF2','VF3'),
					2 => array('VF1','VF4'),
					);
			//For 3 groups
			elseif($count_groups == 3):
				$versus = array(
					1 => array('VF1','VF3'),
					2 => array('VF2','VF4'),
					);
			endif;


			// if($quarter == TRUE):
	
				for($i = 0; $i <= 1; $i++):
					$group = $versus[$semi_iterator][$i];
					$check_game = check_game($group, $id);
					$team_nr = 'team_id_'.($i+1);

					if($check_game == TRUE):

						$win = get_winner($group, $id);
						$match->$team_nr = $win;
					else:
						$match->$team_nr = 0;
					endif;
					
				endfor;
			// else:
			// 	$match->team_id_1 = 0;
			// 	$match->team_id_2 = 0;	
			// endif;

			db_update('tr_matches')
				->fields(array(
					'team_1_placeholder' => 'WVF'.substr($versus[$semi_iterator][0], -1),
					'team_2_placeholder' => 'WVF'.substr($versus[$semi_iterator][1], -1),
				))
				->condition('match_id', $match->match_id, '=')
				->execute();

			$semi_iterator++;

		//For small final

		elseif($match->group_name == 'Match for third place'):


			// if($semi == FALSE):
			// 	$semi = check_latest($id , array('HF1' , 'HF2'));
			// endif;

			// if($semi == TRUE):
				$versus = array('HF1','HF2');
				for($i = 0; $i <= 1; $i++):
					$group = $versus[$i];
					$check_game = check_game($group, $id);
					$team_nr = 'team_id_'.($i+1);

					if($check_game == TRUE):
						$loser = get_loser($group, $id);
						$match->$team_nr = $loser;
					else:
						$match->$team_nr = 0;
					endif;
					
					
				endfor;
			// else:
			// 	$match->team_id_1 = 0;
			// 	$match->team_id_2 = 0;	
			// endif;

			db_update('tr_matches')
				->fields(array(
					'team_1_placeholder' => 'LSF1',
					'team_2_placeholder' => 'LSF2',
				))
				->condition('match_id', $match->match_id, '=')
				->execute();

		//For final

		elseif($match->group_name == 'Final'):


			// if($semi == TRUE):
				$versus = array('HF1','HF2');
				for($i = 0; $i <= 1; $i++):
					$group = $versus[$i];
					$check_game = check_game($group, $id);
					$team_nr = 'team_id_'.($i+1);
					if($check_game == TRUE):
						$win = get_winner($group, $id);
						$match->$team_nr = $win;
					else:
						$match->$team_nr = 0;
					endif;
				endfor;
			// else:
			// 	$match->team_id_1 = 0;
			// 	$match->team_id_2 = 0;	
			// endif;

			db_update('tr_matches')
				->fields(array(
					'team_1_placeholder' => 'WSF1',
					'team_2_placeholder' => 'WSF2',
				))
				->condition('match_id', $match->match_id, '=')
				->execute();

		endif;
		
		if($match->team_id_2 != 'input'):
			db_update('tr_matches')
				->fields(array(
					'team_id_1' => $match->team_id_1,
					'team_id_2' => $match->team_id_2,
				))
				->condition('match_id', $match->match_id, '=')
				->execute();
		else:
			db_update('tr_matches')
				->fields(array(
					'team_id_1' => $match->team_id_1,
				))
				->condition('match_id', $match->match_id, '=')
				->execute();
		endif;
		
	endforeach;
}

?>