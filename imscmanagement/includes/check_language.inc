<?php

function checkLanguage(){
		global $language;
		return $language->name;
	}

function getCountries($language){

	if($language === 'English'):
		include_once(drupal_get_path('theme', 'imsc') . '/includes/countries_en.php');
	elseif($language === 'German'):
		include_once(drupal_get_path('theme', 'imsc') . '/includes/countries_de.php');
	elseif($language === 'French'):	
		include_once(drupal_get_path('theme', 'imsc') . '/includes/countries_fr.php');
	elseif($language === 'Italian'):
		include_once(drupal_get_path('theme', 'imsc') . '/includes/countries_it.php');
	endif;	

	$resultsCountries = array(
		'countries' => $countries,
		'regions' => $regions[0], 
	);	

	return $resultsCountries;
}


?>