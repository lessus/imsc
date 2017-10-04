c<?php

include_once(drupal_get_path('module', 'imscmanagement') . '/includes/load_placeholder.php');
global $language;
$lang = $language->language;

//Tournament ID
$nid = $fields['nid']->content;

$count_groups = $fields['field_tournament_groups']->content;

$status = db_query("SELECT `match_id` FROM `tr_matches` WHERE `tournament_id` = ".$nid." LIMIT 1");

$num_rows = $status->rowCount();

if(!$num_rows):
	include_once(drupal_get_path('module', 'imscmanagement') . '/includes/create_fixture.php');
	createFixture($nid);
endif;

include_once(drupal_get_path('module', 'imscmanagement') . '/includes/refresh_fixture.php');
refreshFixture($nid);

$matches = db_query("SELECT * FROM `tr_matches` WHERE `tournament_id` = ".$nid);

include_once(drupal_get_path('module', 'imscmanagement') . '/includes/get_data_to_fixture.php');



?>

<div id="current_tournament_nid" style="display: none; visibility: hidden;"><?php print $nid; ?></div>

<?php foreach($matches as $match): ?>

<tr>
	<input type="hidden" value="<?php print $match->match_id; ?>" name="match_id">
	<td class="game">
		<?php print $match->game; ?>
	</td>
	<td class="group">
		<?php print $match->group_name_short; ?>
	</td>
	<td class="match-begin">
		<?php print $match->match_begin; ?>
	</td>
	<td class="fixture">
		<div class="team-one">
			<span class="name">
				<?php if($match->team_id_1 == 0): ?>
					<?php load_placeholder($match->team_1_placeholder, $lang); ?>
				<?php else: ?>
					<?php print get_team_name($match->team_id_1); ?>
				<?php endif; ?>
			</span>
			<span class="video"><?php if($match->video != ''){?><a href="<?php print $match->video;  ?>" target="_blank" title="<?php print t('Watch video'); ?>"><i class="fa fa-play-circle"></i></a><?php } ?></span>
			<input type="text" name="match_team_1_pts" value="<?php print $match->team_1_pts; ?>">
		</div>
		<span class="sep">:</span>
		<div class="team-two">
			<input type="text" name="match_team_2_pts" value="<?php print $match->team_2_pts; ?>">
			<span class="video"><?php if($match->video != ''){?><a href="<?php print $match->video;  ?>" target="_blank" title="<?php print t('Watch video'); ?>"><i class="fa fa-play-circle"></i></a><?php } ?></span>
			<span class="name">
				<?php if($match->input_2 == 1): ?>
					<?php $inputs = get_input($match->group_name_short, $count_groups, $match->team_id_2, $nid); ?>
					<select name="select_team_2">
						<?php foreach($inputs as $input): ?>
							<?php if($input[1] != ''): ?>
								<option value="<?php print $input[0]; ?>" <?php if($input[0] == $match->team_id_2) print 'selected'; ?>><?php print $input[1]; ?></option>
							<?php endif; ?>
						<?php endforeach; ?>	
					</select>
				<?php else: ?>
					<?php if($match->team_id_2 == 0): ?>
						<?php load_placeholder($match->team_2_placeholder, $lang); ?>
					<?php else: ?>
						<?php print get_team_name($match->team_id_2); ?>
					<?php endif; ?>
				<?php endif; ?>
			</span>
		</div>
	</td>
	<td class="def">
		<input type="checkbox" value="<?php print $match->def; ?>" <?php if ($match->def == 1) print 'checked'; ?>>
	</td>
	<td class="link">
		<input type="text" name="match_video" value="<?php print $match->video; ?>" placeholder="<?php print t('External url to video'); ?>">
	</td>
	<td class="operations">
		<button data-operations="save_match" class="active"><i class="fa fa-save mobile-on"></i><span class="mobile-off"><?php print 'Save'; ?></span><?php if($match->save == 1) print ' <i style="color: white" class="fa fa-check"></i>'; ?></button>
	</td>
</tr>
<?php endforeach; ?>

<script>
	jQuery(document).ready(function($){
		$('[name="match_team_1_pts"], [name="match_team_2_pts"]').keydown(function (e) {
	        // Allow: backspace, delete, tab, escape, enter and .
	        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
	             // Allow: Ctrl+A, Command+A
	            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
	             // Allow: home, end, left, right, down, up
	            (e.keyCode >= 35 && e.keyCode <= 40)) {
	                 // let it happen, don't do anything
	                 return;
	        }
	        // Ensure that it is a number and stop the keypress
	        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
	            e.preventDefault();
	        }
	    });

	    $('td [type="checkbox"]').each(function(){
	    	if($(this).val() == 1){
	    		$(this).parent().addClass('only-sign checked');
	    		$(this).attr("checked", true);
	    	}else{
	    		$(this).parent().addClass('only-sign not-checked');
	    		$(this).attr("checked", false);
	    	}
	    });

	     $('td [type="checkbox"]').click(function(){
	     	if($(this).val() == 1 ){
	     		$(this).attr('value', 0);
	     		$(this).parent().addClass('not-checked');
	     		$(this).parent().removeClass('checked');
	     	}else{
	     		$(this).attr('value', 1);
	     		$(this).parent().removeClass('not-checked');
	     		$(this).parent().addClass('checked');
	     	}
	     });

	    //Save changes
			$('#admin_fixture [data-operations="save_match"]').click(function(){

				if($(this).is('.active')){

					var button 		= $(this);
					
					var id 			= $(this).parent().parent().children('[name="match_id"]').val();
					var url			= $(this).parent().parent().children('.link').children('[name="match_video"]').val();
					var team_1_pts 	= $(this).parent().parent().children('.fixture').children().children('[name="match_team_1_pts"]').val();
					var team_2_pts 	= $(this).parent().parent().children('.fixture').children().children('[name="match_team_2_pts"]').val();
					var def 		= $(this).parent().parent().children('.def').children('[type="checkbox"]').val();



					if(button.parent().prev().prev().prev('.fixture').children('.team-two').children('.name').children().is('select')){
						var team_2_id = $(this).parent().prev().prev().prev('.fixture').children('.team-two').children('.name').children('select').children(':selected').val();
						var name 		= "save_match_with_team";
						console.log(name, id, url, team_1_pts , team_2_pts, team_2_id);

						$.ajax({
							type: "POST",
							url: "/sites/all/modules/custom/imscmanagement/includes/update.php",
							dataType: "json",
							data: { name : name , id : id, url : url , def : def , team_1_pts : team_1_pts , team_2_pts : team_2_pts , team_2_id : team_2_id},
							success: function(json){
								console.log(json);
							},
							error: function(error){
							  	console.log("Error");
							}
						}).done(function(){
							button.removeClass('active');
				        });

					}else{
						var name 		= "save_match";
						// console.log(name, id, url, team_1_pts , team_2_pts);

						$.ajax({
							type: "POST",
							url: "/sites/all/modules/custom/imscmanagement/includes/update.php",
							dataType: "json",
							data: { name : name , id : id, url : url , team_1_pts : team_1_pts , team_2_pts : team_2_pts , def : def},
							success: function(json){
								console.log(json);
							},
							error: function(error){
							  	console.log("Error");
							}
						}).done(function(){
							button.removeClass('active');
				        });
					}
				}
			});
	});
</script>