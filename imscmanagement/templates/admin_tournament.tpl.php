<?php 

//Tournament overview

include_once(drupal_get_path('module', 'imscmanagement') . '/includes/get_all_tournaments.php');
$tournaments = get_all_tournaments();
?>

<div id="admin_tabs">
	<div class="tabs">
		<div class="tab tab-0 active" data-tab="0"><?php print t('Day'); ?></div>
		<div class="tab tab-1" data-tab="1"><?php print t('Night'); ?></div>
	</div>
	<div class="tabs-content">
		<?php $tab_iterator = 0; ?>
		<?php foreach($tournaments as $tournament): ?>
		<div class="tab-content tab-content-<?php print $tab_iterator; ?> <?php if($tab_iterator == 0) print 'active'; ?>" data-tab="<?php print $tab_iterator; ?>">
			<div id="admin_tournament_<?php print $tab_iterator; ?>" class="admin-table">
				<div class="table-area">
					<div class="inside">
						<table class="standard-table">
							<thead>
								<tr>
									<td>
										<?php echo t('Year'); ?>
									</td>
									<td>
										<?php echo t('Date'); ?>
									</td>
									<td>
										<?php echo t('Teams'); ?>
									</td>
									<td>
										<?php echo t('Groups'); ?>
									</td>
									<td>
										<?php echo t('Deadline'); ?>
									</td>
									<td>
										<?php echo t('Begin of tournament'); ?>
									</td>
									<td>
										<?php echo t('Default'); ?>
									</td>
									<td class="empty">
										
									</td>
								</tr>
							</thead>
							<tbody>
								<?php foreach($tournament as $row): ?>
									<tr>
										<td><?php echo $row['year']; ?></td>
										<td><?php echo $row['date']; ?></td>
										<td><?php echo $row['teams'].' '.t('teams'); ?></td>
										<td><?php echo $row['groups']; ?></td>
										<td><?php echo $row['deadline']; ?></td>
										<td><?php echo $row['begin']; ?></td>
										<td><input type="checkbox" readonly value="<?php echo $row['default']; ?>"></td>
										<td>
											<form action="<?php print url('/management/tournament-overview/edit'); ?>" method="POST" id="show_<?php echo $row['tournament_id']; ?>">
												<input type="hidden" name="tournamentID" value="<?php echo $row['tournament_id']; ?>">
												<button class="no-icon" type="submit" value="edit" title="<?php print t('Edit tournament'); ?>"><?php print t('Edit'); ?></button>
											</form>
										</td>		
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<?php $tab_iterator++; ?>
		<?php endforeach; ?>
	</div>
</div>
