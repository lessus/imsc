<?php 

function get_logo($fid){
	global $base_url;

	if(($fid != 0) AND (file_create_url(file_load($fid)->uri) != $base_url."/")):
		$imgpath = file_load($fid)->uri;
		return str_replace("public://","/sites/default/files/", $imgpath);
	else:
		return "/sites/default/files/imsc-fav.png";
	endif;
}

?>