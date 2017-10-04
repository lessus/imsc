<?php 

//Get hours of matches
function getHours($hour, $matches){

	$start = strtotime($hour);

	$hours[] = date('H:i', $start);

	for ($i = 0; $i < $matches; $i++):
		$start = strtotime('+10 minutes',$start);
		$hours[] = date('H:i', $start);
	endfor;

	return $hours;
}

//Create fixture by tournament id

function createFixture($id){

	$node = node_load($id);
    $node_wrapper = entity_metadata_wrapper('node', $node);

   	//Get number of groups
   	$count_groups = $node_wrapper->field_tournament_groups->value();

	if($count_groups == 8):
		$games = array(
			'loop_1_6'	=>	array( 'Group A', 'Group B' , 'Group C' , 'Group D'),
			'loop_2_6'	=>	array( 'Group E', 'Group F' , 'Group G' , 'Group H'),
			'loop_3_8'	=>	array( 'Round of sixteen' ),
			'loop_4_4'	=>	array( 'Quarter final' ),
			'loop_5_2'	=>	array( 'Semi final'),
			'loop_6_1'	=>	array( 'Match for third place'),
			'loop_7_1'	=>  array( 'Final'),
		);
	elseif($count_groups == 6):
		$games = array(
			'loop_1_6'	=>	array( 'Group A', 'Group B' , 'Group C'),
			'loop_2_6'	=>	array( 'Group D', 'Group E' , 'Group F'),
			'loop_3_8'	=>	array( 'Round of sixteen' ),
			'loop_4_4'	=>	array( 'Quarter final' ),
			'loop_5_2'	=>	array( 'Semi final'),
			'loop_6_1'	=>	array( 'Match for third place'),
			'loop_7_1'	=>  array( 'Final'),
		);
	elseif($count_groups == 5):
		$games = array(
			'loop_1_6'	=>	array( 'Group A', 'Group B' , 'Group C'),
			'loop_2_6'	=>	array( 'Group D' , 'Group E'),
			'loop_3_8'	=>	array( 'Round of sixteen' ),
			'loop_4_4'	=>	array( 'Quarter final' ),
			'loop_5_2'	=>	array( 'Semi final'),
			'loop_6_1'	=>	array( 'Match for third place'),
			'loop_7_1'	=>  array( 'Final'),
		);
	elseif($count_groups == 4):
		$games = array(
			'loop_1_6'	=>	array( 'Group A', 'Group B' , 'Group C' , 'Group D'),
			'loop_2_8'	=>	array( 'Round of sixteen' ),
			'loop_3_4'	=>	array( 'Quarter final' ),
			'loop_4_2'	=>	array( 'Semi final'),
			'loop_5_1'	=>	array( 'Match for third place'),
			'loop_6_1'	=>  array( 'Final'),
		);
	elseif($count_groups == 3):
		$games = array(
			'loop_1_6'	=>	array( 'Group A', 'Group B' , 'Group C'),
			'loop_2_4'	=>	array( 'Quarter final' ),
			'loop_3_2'	=>	array( 'Semi final'),
			'loop_4_1'	=>	array( 'Match for third place'),
			'loop_5_1'	=>  array( 'Final'),
		);
	endif;

	//Create ranking table
	$letters = array("A" , "B" , "C" , "D" , "E" , "F" , "G", "H");
	for ($i = 0; $i < $count_groups; $i++):
		for ($j = 1; $j <= 4; $j++):
			db_insert('tr_ranking')
				->fields(array(
					'tournament_id'		=> $id,
					'group_name'		=> $letters[$i],
					'team_id'			=> 0,
					'rank'				=> $j,
				))
				->execute();
		endfor;
	endfor;


	//Get begin hour of tournament
	$hour = $node_wrapper->field_tournament_begin->value();
	$matches = 0;
	
	foreach($games as $key => $value):
		$matches+=$key[7]*count($value);
	endforeach;

	$hours = getHours($hour, $matches);

	$game_iterator = 0;

	foreach($games as $key => $value):

		for($i = 0; $i < (int)$key[7] ; $i++):

			

			foreach($value as $game):
				if (strpos($game, 'Group') !== false):
					$group_name = explode(' ', $game);
					$short = $group_name[1];
				elseif ($game === 'Round of sixteen'):
					$short = 'AF'.(string)($i+1);
				elseif ($game === 'Quarter final'):
					$short = 'VF'.(string)($i+1);
				elseif ($game === 'Semi final'):
					$short = 'HF'.(string)($i+1);
				elseif ($game === 'Match for third place'):
					$short = 'VHF';
				elseif ($game === 'Final'):
					$short = 'SHF';
				endif;
	
				

				db_insert('tr_matches')
					->fields(array(
						'tournament_id'		=> $id,
						'game' 				=> $game_iterator+1,
						'group_name'		=> $game,
						'group_name_short'	=> $short,
						'match_begin'		=> (string)$hours[$game_iterator],
						'team_id_1'			=> 0,
						'team_id_2'			=> 0,
						'team_1_pts'		=> 0,
						'team_2_pts'		=> 0,
						'video'				=> '',
						'def'				=> 0,
						'input_1'			=> 0,
						'input_2'			=> 0,
						'team_1_placeholder'=> '',
						'team_2_placeholder'=> '',
					))
					->execute();

				$game_iterator++;

			endforeach;

		endfor;

	endforeach;
}

?>