<?php 

	

	global $user;

	$results = db_query("SELECT tr_teams.team_id, tr_teams.name , tr_captains.phone , tr_captains.phone_code , tr_captains.phone_country , tr_captains.email , tr_status.created, tr_captains.firstname, tr_captains.lastname, tr_status.status, tr_status.payment_status
		FROM tr_teams
		INNER JOIN tr_status
		ON tr_teams.team_id=tr_status.team_id
		INNER JOIN tr_captains
		ON tr_teams.team_id=tr_captains.team_id
		WHERE tr_status.uid=".$user->uid);

	require_once(drupal_get_path('module', 'imsctournament') . '/includes/phones.php');
	global $language ;
	$lang_prefix = $language->language;

?>

	<div id="user_teams" class="user-table">
		<div class="table-area">
			<div class="inside">
				<table class="standard-table">
					<thead>
						<tr>
							<td>
								<?php echo t('Team Name'); ?>
							</td>
							<td>
								<?php echo t('Captain'); ?>
							</td>
							<td>
								<?php echo t('Phone'); ?>
							</td>
							<td>
								<?php echo t('E-mail'); ?>
							</td>
							<td>
								<?php echo t('Created on'); ?>
							</td>
							<td>
								<?php echo t('Paid'); ?>
							</td>
							<td>
								<?php echo t('Edit'); ?>
							</td>
						</tr>
					</thead>
					<tbody>
						<?php foreach($results as $row): ?>
							<tr>
								<td><?php echo $row->name; ?></td>
								<td><?php echo $row->firstname." ".$row->lastname; ?></td>
								<td><?php echo $phones[$lang_prefix][$row->phone_country].' '.$row->phone_code.' '.$row->phone; ?></td>
								<td><?php echo $row->email; ?></td>
								<td><?php echo $row->created; ?></td>
<!-- 								<td>
									<span class="<?php echo $row->status; ?>">
										<?php if($row->status == 0): ?>
											<?php print t('<i class="fa fa-ban" title="Unactive"></i>'); ?>
										<?php else: ?>
											<?php print t('<i class="fa fa-check" title="Active"></i>'); ?>
										<?php endif; ?>
									</span>
								</td> -->
								<td>
									<span class="<?php echo $row->payment_status; ?>">
										<?php if($row->payment_status == 0): ?>
											<?php print '<i class="fa fa-ban" title="'.t('Unpaid').'"></i>'; ?>
										<?php elseif($row->payment_status == 1): ?>
											<?php print '<i class="fa fa-refresh" title="'.t('In progress').'"></i>'; ?>
										<?php else: ?>
											<?php print '<i class="fa fa-check" title="'.t('Paid').'"></i>'; ?>
										<?php endif; ?>
									</span>
								</td>
								<td>
									<form action="<?php print url('/user/teams/show'); ?>" method="POST">
										<input type="hidden" name="teamID" value="<?php echo $row->team_id; ?>">
										<button value="Show" title="Show team"><i class="fa fa-edit"></i></button>
									</form>	
								</td>		
							</tr>
						<?php endforeach; ?>
						<?php $number_of_rows = $results->rowCount(); ?>
						<?php if (!$number_of_rows): ?>
    						<tr>
    							<td>
    								<?php print t("You have not created any team yet"); ?>
    							</td>
    							<td></td>
    							<td></td>
    							<td></td>
    							<td></td>
    							<td></td>
    							<td></td>
    						</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>