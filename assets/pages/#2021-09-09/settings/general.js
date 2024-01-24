$(".collapse").find("input:checkbox").on("click", function() {
	var i = $(this).attr("name")
	var k = $(this).attr("id").split("-")[1];
	
	if($("#app-form-"+i).hasClass("d-none")) {
		$("#app-form-"+i).removeClass("d-none");
		$('[name="'+k+'"]').text("Enabled")	
		$('[name="'+k+'"]').addClass("text-success")
		$('[name="'+k+'"]').removeClass("text-danger")
	} else {
		$("#app-form-"+i).addClass("d-none");

		$.ajax({
			url: base_url+"settings/json_disable_app/"+i,
			type: "POST",
			dataType: "json",

			success: function(data) {
				$('[name="'+k+'"]').removeClass("text-success")
				$('[name="'+k+'"]').addClass("text-danger")
				$('[name="'+k+'"]').text("Disabled")	
				$('[name="bank"], [name="acc_no"], [name="acc_name"], [name="'+i+'_pg"]').val('').trigger('change');
				toastr['success']("App has been disabled")
			}
		});
	}
	
})

$(document).ready(function() {
	$.each($(".card-body-header"), function(k, v) {
		var i = v.id.split("-")[1];
		var enable_text = $('[name="'+i+'"]').text();
		if(enable_text == 'Enabled') {
			$("#checkbox-"+i).click();
		}
	})
})