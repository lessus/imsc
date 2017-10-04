<?php
	include_once(drupal_get_path('module', 'imscmanagement') . '/includes/get_team.inc');
	include_once(drupal_get_path('module', 'imscmanagement') . '/includes/check_user_team.inc');
	drupal_add_js( drupal_get_path('theme', 'imsc').'/js/dialog.min.js');
	drupal_add_js( drupal_get_path('theme', 'imsc').'/js/user-management.js');



	//Get year of tournament

	function get_year($tournament , $team_id){
		$result = db_query('SELECT field_data_field_tournament_date.field_tournament_date_value 
			FROM field_data_field_tournament_date
			INNER JOIN tr_teams
			ON field_data_field_tournament_date.entity_id = tr_teams.tr_'.$tournament.'_id
			WHERE tr_teams.team_id = '.$team_id
		)->fetchField();

		$date = new DateTime($result);
		$year = $date->format('Y');
		return $year;
	}



	$teamID = $_POST['teamID'];
	$checkUser = checkUserTeam($user->uid, $teamID);

	if (!isset($_POST['teamID'])):		//IF ISSET POST VARIABLE
		drupal_goto(url("user"));
	endif;

	if ($checkUser === TRUE):		//IF USER CREATED CURRENT TEAM
		$team = getData($teamID);
		$languageName = checkLanguage();
		$countriesResults = getCountries($languageName, $team['captain']['country']);

	else:							//USER NOT CREATED CURRENT TEAM => REDIRECT
		drupal_goto("user");
	endif;

	//Status label

	if ($team['team']['status'] == 0): $status_label = "Inactive";
	elseif ($team['team']['status'] == 2): $status_label = "Rejected";
	else: $status_label = "Active";
	endif;

	//Payment status label
	if ($team['team']['payment_status'] == 0): $payment_status_label = "Not paid";
	elseif ($team['team']['payment_status'] == 1): $payment_status_label = "In progress";
	else: $payment_status_label = "Paid";
	endif;

	//Payment type label
	if ($team['team']['payment_type'] == 0): $payment_type_label = "Bank transfer";
	elseif ($team['team']['payment_type'] == 1): $payment_type_label = "PayPal";
	elseif ($team['team']['payment_type'] == 2): $payment_type_label = "Credit Card";
	elseif ($team['team']['payment_type'] == 3): $payment_type_label = "Sofort";
	else: $payment_status_label = "-";
	endif;

	require_once(drupal_get_path('module', 'imsctournament') . '/includes/phones.php');
	global $language ;
	$lang_prefix = $language->language ;

	if($team['tournament']['day'] == 1):
		$year = get_year('day' , $teamID);
	elseif($team['tournament']['night'] == 1):
		$year = get_year('night' , $teamID);
	endif;

	$date = explode(' - ' , $team['captain']['birthdate']);
	$captain_year = $date[0];
	$captain_month = $date[1];
	$captain_day = $date[2];

	$days = range(1, 31);
	$months = range(1, 12);
	$years = range(2016, 1900);

 ?>

<div id="admin_show_team" class="admin-table">
	<form action="<?php print drupal_get_path('module', 'imscmanagement').'/includes/update_team.php' ?>" method="POST">
		<input type="hidden" value="<?php echo $teamID; ?>" name="teamID">
		<div>
			<fieldset>
				<legend class="panel-heading">
					<span class="panel-title"><?php print t('Team status').' '.$year; ?></span>
				</legend>
				<div class="panel-body">
					<div class="col-md-3 col-sm-12 col-xs-12" style="padding-left: 0;">
						<div class="form-item form-type-checkbox tournaments-opt">
							<div class="col-sm-4 col-xs-12">
								<label class="control-label">
									<input disabled type="checkbox" value="<?php print $team['tournament']['day']; ?>" name="tournamentDay" <?php if($team['tournament']['day'] == 1) print 'checked="checked"'; ?>> Day
								</label>
							</div>
							<div class="col-sm-4 col-xs-12">
								<label class="control-label">
									<input disabled type="checkbox" value="<?php print $team['tournament']['night']; ?>" name="tournamentNight" <?php if($team['tournament']['night'] == 1) print 'checked="checked"'; ?>> Night
								</label>
							</div>
							<div class="col-sm-4 col-xs-12">
								<label class="control-label">
									<input disabled type="checkbox" value="<?php print $team['tournament']['food']; ?>" name="tournamentFood" <?php if($team['tournament']['food'] == 1) print 'checked="checked"'; ?>> <?php print t('Food'); ?>
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-3 col-sm-12 col-xs-12">
						<div class="form-item status-options">
							<label><?php print t('Participation'); ?></label>
							<div class="row">
								<span><?php print t('Team: '); ?></span>
								<span>
									<?php

									if($team['team']['status'] == 1):
										print t('Active');
									elseif($team['team']['status'] == 0):
										print t('Inactive');
									endif;

									?>
								</span>
							</div>
							<div class="row">
								<span class="created"><?php print 'Created: '.$team['team']['created']; ?></span>
							</div>
						</div>
					</div>
					<div class="col-md-3 col-sm-12 col-xs-12">
						<div class="form-item status-options">
							<label><?php print t('Payment'); ?></label>
							<div class="row">
								<span><?php print t('Payment: '); ?></span>
								<?php

								if($team['team']['payment_status'] == 2):
									print t('Paid');
								elseif($team['team']['payment_status'] == 1):
									print t('In progress');
								elseif($team['team']['payment_status'] == 0):
									print t('Not paid');
								endif;

								?>
							</div>
							<div class="row">
								<span class="type <?php print $team['team']['payment_type']; ?>"><?php print 'Type: '.$payment_type_label; ?></span>
							</div>
							<div class="row">
								<span class="payment-date <?php print $team['team']['payment_date']; ?>"><?php print 'Date: '.$team['team']['payment_date']; ?></span>
							</div>
						</div>
					</div>
					<div class="col-md-3 col-sm-12 col-xs-12">
						<div class="logo">
							<?php $imgpath = file_load($team['team']['fid'])->uri;?>
							<img  src="<?php print str_replace('public://','/sites/default/files/', $imgpath); ?>">
						</div>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6 col-sm-12 col-xs-12 half-left">
			<fieldset>
				<legend class="panel-heading">
					<span class="panel-title"><?php print t('Team name'); ?></span>
				</legend>
				<div class="panel-body">
					<div class="form-item form-type-textfield">
						<input type="text" value="<?php print $team['team']['name']; ?>" name="teamName">
					</div>
				</div>
			</fieldset>
		</div>
		<div class="edit-logo col-md-6 col-sm-12 col-xs-12 half-right">
			<fieldset>
				<legend class="panel-heading">
					<span class="panel-title"><?php print t('Team logo'); ?></span>
				</legend>
				<div class="panel-body">
					<div class="form-item">
						<?php $logo_form = drupal_get_form('imscmanagement_logo_form'); ?>
						<?php print drupal_render($logo_form); ?>
					</div>
				</div>
			</fieldset>
		</div>
		<div>
			<div class="full">
				<fieldset>
					<legend class="panel-heading">
						<span class="panel-title"><?php print t('Captain'); ?></span>
					</legend>
					<div class="panel-body">
						<div class="col-md-6 col-sm-12 col-xs-12 half-left">
							<div class="form-item form-type-textfield form-item-firstname">
								<input disabled type="text" value="<?php print $team['captain']['firstname']; ?>" name="captainFirstname">
							</div>
						</div>
						<div class="col-md-6 col-sm-12 col-xs-12 half-right">
							<div class="form-item form-type-textfield form-item-lastname">
								<input disabled type="text" value="<?php print $team['captain']['lastname']; ?>" name="captainLastname">
							</div>
						</div>
						<div class="col-md-6 col-sm-12 col-xs-12 half-left">
							<div class="form-item form-type-textfield form-item-street">
								<input disabled type="text" value="<?php print $team['captain']['street']; ?>" name="captainStreet">
							</div>
						</div>
						<div class="city-line col-md-6 col-sm-12 col-xs-12 half-right">
							<div class="col-md-4 col-sm-12 col-xs-12">
								<div class="form-item form-type-textfield form-item-postcode">
									<input disabled type="text" value="<?php print $team['captain']['postcode']; ?>" name="captainPostcode">
								</div>
							</div>
							<div class="col-md-8 col-sm-12 col-xs-12">
								<div class="form-item form-type-textfield form-item-city">
									<input disabled type="text" value="<?php print $team['captain']['town']; ?>" name="captainTown">
								</div>
							</div>
						</div>
						<div class="col-md-6 col-sm-12 col-xs-12 half-left">
							<div class="form-item form-type-select">
								<select disabled name="captainCity" data-group="one-group" class="form-control">
				 					<?php foreach($countriesResults['regions'] as $key => $value): ?>
				 						<option value="<?php print $key; ?>" <?php if ($key == $team['captain']['city']): print 'selected="selected"'; endif; ?>><?php print $value; ?></option>
				 					<?php endforeach; ?>
				 				</select>
							</div>
						</div>
						<div class="col-md-6 col-sm-12 col-xs-12 half-right">
							<div class="form-item form-type-select">
								<select disabled name="captainCountry" data-group="one-group" class="form-control">
				 					<?php foreach($countriesResults['countries'] as $key => $value): ?>
				 						<option value="<?php print $key; ?>" <?php if ($key == $team['captain']['country']): print 'selected="selected"'; endif; ?>><?php print $value; ?></option>
				 					<?php endforeach; ?>
				 				</select>
							</div>
						</div>
						<div class="date-line col-md-6 col-sm-12 col-xs-12 half-left">
							<div class="form-item form-type-select">
								<select disabled name="captainDay" class="form-control">
				 					<?php foreach($days as $day): ?>
				 						<option value="<?php print $day; ?>" <?php if ($day == $captain_day) print 'selected="selected"'; ?>><?php print $day; ?></option>
				 					<?php endforeach; ?>
				 				</select>
							</div>


							<div class="form-item form-type-select">
								<select disabled name="captainMonth" class="form-control">
				 					<?php foreach($months as $month): ?>
				 						<option value="<?php print $month; ?>" <?php if ($month == $captain_month) print 'selected="selected"'; ?>><?php print $month; ?></option>
				 					<?php endforeach; ?>
				 				</select>
							</div>

							<div class="form-item form-type-select">
								<select disabled name="captainYear" class="form-control">
				 					<?php foreach($years as $year): ?>
				 						<option value="<?php print $year; ?>" <?php if ($key == $captain_year) print 'selected="selected"'; ?>><?php print $year; ?></option>
				 					<?php endforeach; ?>
				 				</select>
							</div>
						</div>
						<div class="phone-line col-md-6 col-sm-12 col-xs-12 half-right">
							<div class="col-md-4 col-sm-12 col-xs-12">
								<div class="form-item form-type-select">
									<select disabled name="captainPhoneCountry" class="form-control">
					 					<?php foreach($phones[$lang_prefix] as $key => $value): ?>
					 						<option value="<?php print $key; ?>" <?php if ($key == $team['captain']['phone_country']): print 'selected="selected"'; endif; ?>><?php print $value; ?></option>
					 					<?php endforeach; ?>
					 				</select>
								</div>
							</div>
							<div class="col-md-4 col-sm-12 col-xs-12">
								<div class="form-item form-type-textfield">
									<input disabled type="text" value="<?php print $team['captain']['phone_code']; ?>" name="captainPhoneCode">
								</div>
							</div>
							<div class="col-md-4 col-sm-12 col-xs-12">
								<div class="form-item form-type-textfield">
									<input disabled type="text" value="<?php print $team['captain']['phone']; ?>" name="captainPhone">
								</div>
							</div>
						</div>
						<div class="col-md-6 col-sm-12 col-xs-12 half-left">
							<div class="form-item form-type-textfield form-item-email">
								<input disabled type="text" value="<?php print $team['captain']['email']; ?>" name="captainEmail">
							</div>
						</div>
						<div class="col-md-6 col-sm-12 col-xs-12 half-right">
							<div class="form-item form-type-checkbox">
								<label class="control-label">
									<input disabled type="checkbox" value="<?php print $team['captain']['confirm']; ?>" name="captainConfirm" <?php if($team['captain']['confirm'] == 1) print 'checked="checked"'; ?>> <?php print t('Ich habe auch schon Teilgenommen'); ?>
								</label>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		<div>
			<div>
				<fieldset>
					<legend class="panel-heading">
						<span class="panel-title"><?php print t('Players'); ?></span>
					</legend>
					<div class="panel-body">
						<?php foreach($team['players'] as $key => $row): ?>
				 			<div class="col-md-6 col-sm-12 col-xs-12">
				 				<input type="hidden" name="playerID_<?php print $key+1; ?>" value="<?php print $row['player_id']; ?>">
								<div class="form-item form-type-textfield form-item-firstname">
									<input type="text" value="<?php print $row['firstname']; ?>" name="playerFirstname_<?php print $key+1; ?>">
								</div>
							</div>
							<div class="col-md-6 col-sm-12 col-xs-12">
				 				<input type="hidden" name="playerID" value="<?php print $row['player_id']; ?>">
								<div class="form-item form-type-textfield form-item-firstname">
									<input type="text" value="<?php print $row['lastname']; ?>" name="playerLastname_<?php print $key+1; ?>">
								</div>
							</div>
				 		<?php endforeach; ?>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="button-area">
			<button data-operation="back_to_list" class="button-primary back"><?php print t('Back to list'); ?></button>
			<button class="save-team button-primary right" title="<?php print t('Save'); ?>" data-operation="save_team"><i class="fa fa-check" style="color: white"></i> <?php print t('Save'); ?></button>
		</div>
	</form>
</div>
<div id="dialog-confirm" title="<?php print t('Save Changes?'); ?>" style="display: none;">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span><?php print t('Do you want to save the changes on this page?'); ?></p>
</div>

<script>
	jQuery(document).ready(function($){
		$('#block-menu-menu-profile-menu .my-team').addClass('active');
	});
</script>