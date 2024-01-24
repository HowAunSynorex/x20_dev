function del_ask() {
	swal({
		text: "Are you sure want to delete this package?",
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
				type: "POST",
				dataType: "json",
				data: {
					'del': true,
				},
				
				success: function(data) {
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						window.location.href = base_url+"package/list";
					});
				}
			});
		}
	});
}

if( access_denied != undefined ) {
	$('.form-control, [name="save"], [data-target="#modal-add"]').attr("disabled", true);
}