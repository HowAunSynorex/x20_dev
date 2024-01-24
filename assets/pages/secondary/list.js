$(".branch-item").find("input").on("change", function() {

	var id = $(this).attr("data-id");
	var type = $(this).attr("data-type");

	if($(this).prop("checked") == false) {
		
		$.ajax({
			url: base_url+"secondary/json_enable/"+0+"/"+id,
			type: "POST",
			dataType: "json",
			
			success: function(data) {
				toastr['success'](type+' has been inactivated');
			}
		});

	} else {
	
		$.ajax({
			url: base_url+"secondary/json_enable/"+1+"/"+id,
			type: "POST",
			dataType: "json",

			success: function(data) {
				toastr['success'](type+' has been activated');
			}

		})

	}
	
})

$(".default-item").find("input").on("change", function() {

	var id = $(this).attr("data-id");
	var type = $(this).attr("data-type");

	if($(this).prop("checked") == false) {
		
		$.ajax({
			url: base_url+"secondary/json_enable_join/"+0+"/"+id,
			type: "POST",
			dataType: "json",
			
			success: function(data) {
				toastr['success'](type+' has been inactivated');
			}
		});

	} else {
	
		$.ajax({
			url: base_url+"secondary/json_enable_join/"+1+"/"+id,
			type: "POST",
			dataType: "json",

			success: function(data) {
				toastr['success'](type+' has been activated');
			}

		})

	}
	
})