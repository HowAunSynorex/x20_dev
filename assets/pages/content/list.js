function del_ask(id) {
	swal({
		text: "Are you sure want to delete this slideshow?",
		icon: "warning",
		buttons: true,
		dangerMode: true,
		buttons: {
			cancel: "No",
			text: "Yes"
		}
	}).then((willDelete) => {
		if(willDelete) {
			$.ajax({
				url: base_url+"content/json_del/"+id,
				type: "POST",
				dataType: "json",
				
				success: function(data) {
					swal({
						"title": 'Slideshow deleted successfully',
						"icon": "success"
					}).then((e) => {
						window.location.href = window.location.href;
					});
				}
			});
		}
	});
}

$(".switch").find("input").on("change", function() {

	var id = $(this).attr("data-id");

	if($(this).prop("checked") == false) {
		
		$.ajax({
			url: base_url+"content/json_enable/"+0+"/"+id,
			type: "POST",
			dataType: "json",
			
			success: function(data) {
				toastr['success']('Slideshow has been inactivated');
			}
		});

	} else {
	
		$.ajax({
			url: base_url+"content/json_enable/"+1+"/"+id,
			type: "POST",
			dataType: "json",

			success: function(data) {
				toastr['success']('Slideshow has been activated');
			}

		})

	}
	
})