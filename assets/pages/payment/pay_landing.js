function check(k) {
	
	// by soon
	/*if($(k).val() == '') {
		$("#qr-button").addClass('disabled');
	} else {
		$("#qr-button").removeClass('disabled');
	}
	
	$("#modal-qr-title").text($(k).val().split('-')[0]);
	$("#modal-qr-image").attr('src', 'https://cdn.synorex.link/assets/images/loading/default.gif');

	$.ajax({
		url: base_url+"payment/json_view_image/"+$(k).val().split('-')[1],
		type: "POST",
		dataType: "json",
		
		success: function(data) {
			$("#modal-qr-image").attr('src', data.result);
		}
	})*/
	
	// by steve
	// $("#modal-qr").modal("show");
	
	if(k == '') {
		$("#qr-button").addClass('disabled');
	} else {
		$("#qr-button").removeClass('disabled');
	}
	
	$("#modal-qr-image").attr("src", k);
	
}