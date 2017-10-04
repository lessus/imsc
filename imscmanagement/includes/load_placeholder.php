<?php 
function load_placeholder($short, $lang){
	$placeholder = db_query("SELECT * FROM `tr_match_desc` WHERE `short`='".$short."'");
	foreach($placeholder as $row):
		print $row->$lang;
	endforeach;
	
} 

?>