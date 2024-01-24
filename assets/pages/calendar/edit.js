function del_ask(id, type) {
	swal({
		text: "Are you sure want to delete this "+type+"?",
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
				url: base_url+"calendar/json_del/"+id,
				type: "POST",
				dataType: "json",
				
				success: function(data) {
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						window.location.href = base_url+"/calendar/index";
					});
				}
			});
		}
	});
}

$("#checkbox-same_day").click(function() {
	if( $(this).is(":checked") ) {
		$("#section-date_end").addClass("d-none");
		$('#section-date_start [datal-label="label"]').text("Date");
		$('[name="date_end"]').val("");
	} else {
		$("#section-date_end").removeClass("d-none");
		$('#section-date_start [datal-label="label"]').text("Start");
		$('[name="date_end"]').val( $('[name="date_start"]').attr("data-end") );
	}
});

// if( access_denied != undefined ) {
	// $('.form-control, [name="save"], [data-target="#modal-add"]').attr("disabled", true);
// }

if( access_denied1 != undefined ) {
	$('input, .form-control, [name="save"], [data-target="#modal-add"]').attr("disabled", true);
	$('#del-section').addClass('d-none');
}