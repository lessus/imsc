<?php 
	function checkUserTeam($uid, $teamID){
		$uidCheck = db_query("SELECT `uid` FROM `tr_status` WHERE `team_id`=".$teamID)->fetchField();

		if($uid === $uidCheck){
			return TRUE;
		}else{
			return FALSE;
		}
	}
?>