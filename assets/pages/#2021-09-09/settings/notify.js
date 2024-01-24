$(".collapse").find("input:checkbox").on("click", function() {
	
	var i = $(this).attr("name")
	var method;
	
	if($("#notify-form-"+i).hasClass("d-none")) {
		$("#notify-form-"+i).removeClass("d-none");
	} else {
		$("#notify-form-"+i).addClass("d-none");
		
		switch(i) {
			case "payment_success":
				method = "notify_payment";
				break;
			case "outstanding":
				method = "notify_outstanding";
				break;
			default:
				method = "notify_attendance";
				break;
		}
		$.ajax({
			url: base_url+"settings/json_disable_notify/"+method,
			type: "POST",
			dataType: "json",
			
			success: function(data) {
				$("#heading-"+i).find("span").removeClass("text-success")
				$("#heading-"+i).find("span").addClass("text-danger")
				$("#heading-"+i).find("span").text("Disabled")
				toastr['success']("Notification has been disabled")
			}
		});
	}
	
})

$(document).ready(function() {
	$.each($(".card-body-header"), function(k, v) {
		var i = v.id.split("-")[1];
		var enable_text = $("#heading-"+i).find("span").text();
		if(enable_text == 'Enabled') {
			$("#checkbox-"+i).click();
		}
	})
})