<?php 

$teams_day = db_query("SELECT tr_teams.name , tr_teams.team_group_day
					FROM tr_teams
					INNER JOIN tr_teams_tournaments
					ON tr_teams.team_id = tr_teams_tournaments.team_id
					WHERE tr_teams_tournaments.day = 1");

$teams_night = db_query("SELECT tr_teams.name , tr_teams.team_group_night
					FROM tr_teams
					INNER JOIN tr_teams_tournaments
					ON tr_teams.team_id = tr_teams_tournaments.team_id
					WHERE tr_teams_tournaments.night = 1");

$groups_day = array("A" , "B" , "C" , "D" , "E" , "F" , "G" , "H");

$groups_night = array("A" , "B" , "C" , "D" , "E" , "F");

?>

<div id="admin_groups_day" class="admin-table">
	<div class="table-area">
		<div class="inside  col-md-6 col-sm-12 col-xs-12">
			<h4>Day</h4>
			<table class="standard-table">
				<thead>
					<tr>
						<td><?php print t('Team name'); ?></td>
						<td><?php print t("Group"); ?></td>
						<td><?php print t("Operations"); ?></td>
					</tr>
				</thead>
				<tbody>
					<?php foreach($teams_day as $team): ?>
						<tr>
							<td><?php print $team->name; ?></td>
							<td>
								<select name="group-list">
									<option value="select">Select group</option>
									<?php foreach($groups_day as $group): ?>
										<option value="<?php print $group; ?>" <?php if ($group == $team->team_group_day) print 'selected'; ?>><?php print $group; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
							<td class="operations">
								<i class="fa fa-save" title="Save" data-operation="save-group"></i>
								<input type="hidden" value="team_group_day">
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div id="admin_groups_night" class="admin-table">
	<div class="table-area">
		<div class="inside col-md-6 col-sm-12 col-xs-12">
			<h4>Night</h4>
			<table class="standard-table">
				<thead>
					<tr>
						<td><?php print t('Team name'); ?></td>
						<td><?php print t("Group"); ?></td>
						<td><?php print t("Operations"); ?></td>
					</tr>
				</thead>
				<tbody>
					<?php foreach($teams_night as $team): ?>
						<tr>
							<td><?php print $team->name; ?></td>
							<td>
								<select name="group-list">
									<option value="select">Select group</option>
									<?php foreach($groups_night as $group): ?>
										<option value="<?php print $group; ?>" <?php if ($group == $team->team_group_night) print 'selected'; ?>><?php print $group; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
							<td class="operations">
								<i class="fa fa-save" title="Save" data-operation="save-group"></i>
								<input type="hidden" value="team_group_night">
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>