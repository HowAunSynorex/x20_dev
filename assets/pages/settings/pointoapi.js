function check_pointoapi(v) {
	$("#status-check").text("Loading...").removeClass("d-none");
	$.ajax({
		type: "POST",
		dataType: "json",
		data: {
			"json": v
		},
		
		success: function(data) {
			if(data.status == "ok") {
				
				if(v != "") {
					
					$("#status-check").html('<b class="text-success">Available</b>').removeClass("d-none");
					
				} else {
					
					$("#status-check").html('').removeClass("d-none");
					
				}
					
				$('.btn-primary').attr('disabled', false);
				
			} else {
				
				$("#status-check").html('<b class="text-danger">Unavailable</b>').removeClass("d-none");
				$('.btn-primary').attr('disabled', true);
				
			}
		}
	});
}

if( access_denied != undefined ) {
	$('.form-control, [name="save"], [data-target="#modal-add"]').attr("disabled", true);
}