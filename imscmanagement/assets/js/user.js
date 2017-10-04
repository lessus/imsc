jQuery(document).ready(function($){

	//Ajax load

	function spinnerShow(){
		$('#ajax_spinner').show();
	}

	function spinnerHide(){
		$('#ajax_spinner').hide();
	}

	var saveArea = $('#admin_show_team input[type="text"], #user_show_team input[type="text"] , #admin_show_sponsor input[type="text"]');

	//Enable save button when user change value in input
	saveArea.on('change keyup paste', function(){
		$(this).next('[data-operation="save"]').addClass('active');
	});

	var saveSelectArea = $('#admin_show_team [name*="captain"] , #admin_show_sponsor select');

	saveSelectArea.on('change keyup paste', function(){
		$(this).next('[data-operation="save-select"]').addClass('active');
	});


	//Save changes

	var saveButton = $('#admin_show_team [data-operation="save"] , #user_show_team [data-operation="save"], #admin_show_sponsor [data-operation="save"]');
	saveButton.click(function(){
		if($(this).is('.active')){
			var button = $(this);
			var name = $(this).prev('input[type="text"]').attr("name");
			var value = $(this).prev('input[type="text"]').val();
			if((name === "playerFirstname") || (name === "playerLastname")){
				var id = button.parent().prev().val();
				console.log(id);
			}else{
				var id = $('input[name="teamID"]').val();
			}
			
			
		
			$.ajax({
				type: "POST",
				url: "/sites/all/modules/custom/imscmanagement/includes/update.php",
				dataType: "json",
				data: { name : name , value : value, id : id},
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
	});

	function uploadSelect(name, value, id, button){
		$.ajax({
			type: "POST",
			url: "/sites/all/modules/custom/imscmanagement/includes/update.php",
			dataType: "json",
			data: { name : name , value : value, id : id},
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

	var saveSelect = $('#admin_show_team [data-operation="save-select"] , #user_show_team [data-operation="save-select"]  #admin_show_sponsor [data-operation="save-select"]');
	saveSelect.click(function(){
		if($(this).is('.active')){

			if($(this).prev().is('[data-group="one-group"]')){
				$('[data-group="one-group"]').each(function(){
					var name = $(this).attr("name");
					var value = $(this).children(":selected").val();
					var id = $('input[name="teamID"]').val();
					var button = $(this).next("i");
					uploadSelect(name, value, id, button);
				});	

			}else{
				var name = $(this).prev().attr("name");
				var value = $(this).prev().children(":selected").val();
				var id = $('input[name="teamID"]').val();
				var button = $(this);
				uploadSelect(name, value, id, button);
			}
		}
	});

	
	

	//Remove sponsor

	$('#admin_show_sponsor [data-operation="team-remove"]').click(function(){

		if (confirm('Are you sure you want to delete this sponsor? Sponsor will be deleted permanently without the possibility of returning.')) {
		    var name = $(this).attr("data-operation");
			var id = $('input[name="teamID"]').val();
			// var button = $(this);

			removeItem(name, id);
		}		
	});	

	

	//Change payment status - sponsor
	$('#admin_show_sponsor [data-function="edit"]').click(function(){
		var name = $(this).attr("data-operation");
		var id = $(this).parent().parent().children('input[type="hidden"]').val();
		var button = $(this);
		
		$.ajax({
			type: "POST",
			url: "/sites/all/modules/custom/imscmanagement/includes/update.php",
			dataType: "json",
			data: { name : name , id : id},
			success: function(json){
				console.log(json);
			},
			error: function(error){
			  	console.log("Error");
			}
		}).done(function(){

			if(name === "status-confirm"){
				button.attr("data-operation", "status-cancel");
				button.attr("title", "Cancel payment");
				button.parent().parent().children(".sponsor-status").html('<span class="paid">paid</span>');
			}
			else{
				if(name === "status-cancel"){
					button.attr("data-operation", "status-confirm");
					button.attr("title", "Confirm payment");
					button.parent().parent().children(".sponsor-status").html('<span class="unpaid">unpaid</span>');
				}
			}

        });
	});




	//Upload regions
	var country = $('select[name="captainCountry"], select[name="sponsorCountry"]');

	function getLanguage(){

		
		switch(true){
			case $('body').hasClass('i18n-en'):
				return 'English';
				break;

			case $('body').hasClass('i18n-de'):
				return 'German';
				break;

			case $('body').hasClass('i18n-fr'):
				return 'French';
				break;

			case $('body').hasClass('i18n-it'):
				return 'Italian';
				break;
		}
	}

	country.on('change', function(){
		var id = country.children(':selected').val();
		var language = getLanguage();
		console.log(id, language);
		$.ajax({
			type: "POST",
			url: "/sites/all/themes/imsc/includes/get_regions.php",
			dataType: "json",
			data: {id : id , language : language},
			success: function(json){
				$('select[name="captainCity"], select[name="sponsorCity"]').html("");

				$.each(json, function(key, value){
					$('select[name="captainCity"], select[name="sponsorCity"]').append('<option value="'+key+'">'+value+'</option>');
				});
			},
			error: function(error){
			  	console.log("Error");
			}
		});
	});


});