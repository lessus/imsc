<?php 

function check_date($date){

	if (new DateTime() <= new DateTime($date)):
	    return TRUE;
	else:
		return FALSE;
	endif;
}

?>