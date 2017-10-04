<?php 

//Get name by team id
function get_team_name($team_id){
	return db_query("SELECT `name` FROM `tr_teams` WHERE `team_id` = ".$team_id)->fetchField();  
}

//Get input
function get_input($group, $count_groups , $team_id, $tournament_id){

	if($count_groups == 6):
		$inputs = array(
			'AF2'	=>	array('A3' , 'C3' , 'D3'),
			'AF3'	=>	array('B3' , 'E3' , 'F3'),
			'AF4'	=>	array('C3' , 'D3' , 'E3'),
			'AF5'	=>	array('A3' , 'B3' , 'F3'),
			);
	elseif($count_groups == 5):
		$inputs = array(
			'AF8'	=>	array('A4' , 'B4' , 'C4' , 'D4' , 'E4'),
			);
	elseif($count_groups == 3):
		$inputs = array(
			'VF1'	=>	array('A3' , 'B3' , 'C3'),
			'VF2'	=>	array('A3' , 'B3' , 'C3'),
			);
	endif;

	$text = array(
		'AF2'	=>	t('Third').' A/C/D',
		'AF3'	=>	t('Third').' B/E/F',
		'AF4'	=>	t('Third').' C/D/E',
		'AF5'	=>	t('Third').' A/B/F',
		'AF8'   =>  t('Fairest team'),
		'VF1'	=>	t('Third').' A/B/D',
		'VF2'	=>	t('Third').' A/B/D',
		);

	$teams = array(
		array(0, $text[$group]),
		);

	foreach($inputs[$group] as $input):
		$letter = $input[0];
		$number = $input[1];
		$placeholder = $text[$group];

		$team = db_query("SELECT `team_id` FROM `tr_ranking` WHERE `tournament_id` = ".$tournament_id." AND `group_name` = '".$letter."' AND `rank` = ".$number)->fetchField();
		$teams[] = array($team, get_team_name($team));
	endforeach;

	return($teams);
}

 ?>