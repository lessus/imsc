<?php 

	$path = $_SERVER['DOCUMENT_ROOT'];
	chdir($path."/");
	define('DRUPAL_ROOT', getcwd());
	require_once './includes/bootstrap.inc';
	drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

	include_once(drupal_get_path('module', 'imsccountdown') . '/includes/get_score.php');
	include_once(drupal_get_path('module', 'imscmanagement') . '/includes/get_logo.php');

	$nid = $_GET['nid'];
		
	$node = node_load($nid);
    $node_wrapper = entity_metadata_wrapper('node', $node);
    $count_groups = $node_wrapper->field_tournament_groups->value();
    $tournament = strtolower($node_wrapper->field_tournament_type->value());

	$letter = "A";
	$groups = array();
	for($i = 0 ; $i < $count_groups ; $i++ ): 
		$groups[] = $letter;
		$letter++;
	endfor;

?>

<div id="team_results" class="team-table show-all">

	<?php foreach($groups as $group): ?>
		<?php $i = 1; ?>
		<?php $teams = getTeams($tournament , $group, $nid); ?>
		<div class="row" data-group="<?php print $group; ?>" style="margin-bottom: 24px;">
			<table>
				<thead>
					<tr>
						<td><?php print t('Nr'); ?></td>
						<td class="team"><?php print t('Group', array(), array('langcode' => $_GET['lang'])).' '.$group; ?></td>
						<td class="no-padding"><?php print t('P', array(), array('langcode' => $_GET['lang'])); ?></td>
						<td class="no-padding"><?php print t('W', array(), array('langcode' => $_GET['lang'])); ?></td>
						<td class="no-padding"><?php print t('D', array(), array('langcode' => $_GET['lang'])); ?></td>
						<td class="no-padding"><?php print t('L', array(), array('langcode' => $_GET['lang'])); ?></td>
						<td class="no-padding"><?php print t('GS', array(), array('langcode' => $_GET['lang'])); ?></td>
						<td class="no-padding"><?php print t('GA', array(), array('langcode' => $_GET['lang'])); ?></td>
						<td class="no-padding"><?php print t('+/-', array(), array('langcode' => $_GET['lang'])); ?></td>
						<td class="no-padding"><?php print t('PTS', array(), array('langcode' => $_GET['lang'])); ?></td>
					</tr>
				</thead>
				<tbody>
					<?php foreach($teams as $team): ?>
						<tr>
							<td class="number">
								<?php print $i; $i++ ?>
							</td>
							<td class="team">
								<img style="width: 30px; height: 30px; margin-right: 6px;" src="<?php print get_logo($team['fid']); ?>" alt="IMSC - <?php print $team['name']; ?>">
								<?php print $team['name']; ?>
							</td>
							<td class="p no-padding">
								<?php print $team['p']; ?>
							</td>
							<td class="w no-padding">
								<?php print $team['w']; ?>
							</td>
							<td class="d no-padding">
								<?php print $team['d']; ?>
							</td>
							<td class="l no-padding">
								<?php print $team['l']; ?>
							</td>
							<td class="gt no-padding">
								<?php print $team['gt']; ?>
							</td>
							<td class="et no-padding">
								<?php print $team['et']; ?>
							</td>
							<td class="plus-minus no-padding">
								<?php print $team['pm']; ?>
							</td>
							<td class="pts no-padding">
								<?php print $team['pts']; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if($i < 4): ?>
						<?php for($i ; $i <= 4 ; $i++): ?>
							<tr>
								<td class="number"><?php print $i;?></td>
								<td class="team">-</td>
								<td class="p no-padding">-</td>
								<td class="w no-padding">-</td>
								<td class="d no-padding">-</td>
								<td class="l no-padding">-</td>
								<td class="gt no-padding">-</td>
								<td class="et no-padding">-</td>
								<td class="plus-minus no-padding">-</td>
								<td class="pts no-padding">-</td>
							</tr>
						<?php endfor; ?>
					<?php endif; ?>
				</tbody>
			</table>	
		</div>
	<?php endforeach; ?>
</div>