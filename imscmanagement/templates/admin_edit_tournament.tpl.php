<?php 
//DO USUNIĘCIA
//Tournament overview/edit

include_once(drupal_get_path('module', 'imscmanagement') . '/includes/get_tournament.php');
$results = get_tournament_by_id($_POST['tournamentID']);



$number_of_rows = $results->rowCount();
if ($number_of_rows):
	foreach($results as $row):
		$tournament = array(
			'tournament_id'	=> $_POST['tournamentID'],
			'name'			=> $row->name,
			'teams'			=> $row->teams,
			'groups'		=> $row->groups,
			'year'			=> $row->year,
			'date'			=> $row->day.'-'.$row->month.'-'.$row->year,
			'begin'			=> $row->hour.':'.$row->minute,
			'deadline'		=> $row->deadline_day.'-'.$row->deadline_month.'-'.$row->deadline_year,
			'default'		=> $row->status_default,
		);
	endforeach;
else:
	$tournament = array(
		'tournament_id'	=> '',
		'name'			=> '',
		'teams'			=> '',
		'groups'		=> '',
		'year'			=> '',
		'date'			=> '',
		'begin'			=> '',
		'deadline'		=> '',
		'default'		=> '0',
	);
endif;
	print $_POST['tournamentID'];
	print_r($tournament);

?>

<div id="admin_edit_panel">
	<header><h4><?php print t('Create tournament'); ?></h4></header>
	<form action="" method="POST" id="create_tournament_form">
		<div class="edit-form">
			<div class="form-item col-md-6 col-sm-12 col-xs-12">
				<label class="col-md-6 col-sm-12 col-xs-12" for="tournamentName"><?php print t('Tournament'); ?><span>*</span></label>
				<select class="col-md-6 col-sm-12 col-xs-12" name="tournamentName" id="tournament_name">
					<option value="day" <?php if($tournament['name'] == "day") print 'selected'; ?>><?php print t('Day'); ?></option>
					<option value="night" <?php if($tournament['name'] == "night") print 'selected'; ?>><?php print t('Night'); ?></option>
				</select>
			</div>
			<div class="form-item col-md-6 col-sm-12 col-xs-12">
				<label class="col-md-6 col-sm-12 col-xs-12" for="tournamentDate"><?php print t('Date'); ?><span>*</span></label>
				<input class="col-md-6 col-sm-12 col-xs-12 calendar" value="<?php print $tournament['date']; ?>" type="text" name="tournamentDate">
			</div>
			<div class="form-item col-md-6 col-sm-12 col-xs-12">
				<label class="col-md-6 col-sm-12 col-xs-12" for="tournamentTeams"><?php print t('Teams'); ?><span>*</span></label>
				<select class="col-md-6 col-sm-12 col-xs-12" name="tournamentTeams" id="tournament_teams">
					<option value="32">32</option>
				</select>
			</div>
			<div class="form-item col-md-6 col-sm-12 col-xs-12">
				<label class="col-md-6 col-sm-12 col-xs-12" for="tournamentGroups"><?php print t('Groups'); ?><span>*</span></label>
				<input class="col-md-6 col-sm-12 col-xs-12" value="<?php print $tournament['groups']; ?>" type="text" name="tournamentGroups" readonly>
			</div>
			<div class="form-item col-md-6 col-sm-12 col-xs-12">
				<label class="col-md-6 col-sm-12 col-xs-12" for="tournamentDeadline"><?php print t('Deadline'); ?><span>*</span></label>
				<input class="col-md-6 col-sm-12 col-xs-12 calendar" value="<?php print $tournament['deadline']; ?>" type="text" name="tournamentDeadline">
			</div>
			<div class="form-item col-md-6 col-sm-12 col-xs-12">
				<label class="col-md-6 col-sm-12 col-xs-12" for="tournamentBegin"><?php print t('Begin of tournament'); ?><span>*</span></label>
				<select class="col-md-6 col-sm-12 col-xs-12" name="tournamentBegin" id="tournament_begin">
					<option value="8:0">08:00</option>
					<option value="8:30">08:30</option>
					<option value="9:00">09:00</option>
					<option value="19:00">19:00</option>
					<option value="20:00">20:00</option>
					<option value="21:00">21:00</option>
				</select>
			</div>
			<div class="form-item col-md-6 col-sm-12 col-xs-12">
				<label class="col-md-6 col-sm-12 col-xs-12" for="tournamentDefault"><?php print t('Default'); ?><span>*</span></label>
				<select class="col-md-6 col-sm-12 col-xs-12" name="tournamentDefault" id="tournament_default">
					<option value="8:0">08:00</option>
					<option value="8:30">08:30</option>
					<option value="9:0">09:00</option>
					<option value="19:0">19:00</option>
					<option value="20:0">20:00</option>
					<option value="21:0">21:00</option>
				</select>
			</div>
		</div>
		<button  class="button-primary green right" type="submit" name="tournamentSubmit"><i class="fa fa-check"></i> <?php print t('Save'); ?></button>
	</form>
</div>