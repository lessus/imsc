jQuery(document).ready(function($){

	//Admin tabs
	$('#admin_tabs .tabs .tab').click(function(){
		if($(this).is('.active')){

		}else{
			$(this).addClass('active');
			$('#admin_tabs .tabs .tab.active').not($(this)).removeClass('active');
			var tab = $(this).attr('data-tab');
			$('#admin_tabs .tabs-content .tab-content[data-tab="'+tab+'"]').addClass('active');
			$('#admin_tabs .tabs-content .tab-content.active').not('[data-tab="'+tab+'"]').removeClass('active');
		}
	});

});