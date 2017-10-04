<?php 

function get_team_name($team_id){
	return db_query("SELECT `name` FROM `tr_teams` WHERE `team_id` = ".$team_id)->fetchField();
}

 ?>