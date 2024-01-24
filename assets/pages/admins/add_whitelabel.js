function check_username(v) {
	$.ajax({
		url: base_url+"admins/json_check_username/"+encodeURIComponent(v),
		type: "POST",
		dataType: "json",
		
		success: function(data) {
			if(data.result == true) {
				$("#username-status").addClass("d-none");
				$('[type="submit"]').attr("disabled", false);
			} else {
				$("#username-status").removeClass("d-none");
				$('[type="submit"]').attr("disabled", true);
			}
		}
	});
}