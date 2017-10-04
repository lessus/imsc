<?php drupal_add_js( drupal_get_path('theme', 'imsc').'/js/management_fixture.js'); ?>
<div class="button-area">
	<button id="table_view_button" class="button-primary active" data-operation="table-view"><?php print t('Table view'); ?></button>
	<button id="fixture_view_button" class="button-primary" data-operation="fixture-view"><?php print t('Fixture view'); ?></button>
</div>
<div class="admin-table">
	<div class="table-area">
		<div class="inside">
			<table class="standard-table">
				<thead>
					<tr>
						<td class="game"><?php print t('Game'); ?></td>
						<td class="group"><?php print t("Group"); ?></td>
						<td class="match-begin"><?php print t("Time"); ?></td>
						<td class="fixture"><?php print t("Fixtures"); ?></td>
						<td class="def"><?php print t('Default'); ?></td>
						<td class="link"><span class="mobile-off">Video</span> Upload</td>
						<td class="operations"></td>
					</tr>
				</thead>
				<tbody>
					<?php foreach($rows as $index => $row): ?>
						<?php print $row; ?>	
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<div id="ranking_container">
	<!-- HERE DISPLAY TABLE VIEW VIA AJAX -->
</div>