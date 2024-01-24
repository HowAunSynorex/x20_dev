function del_ask_item(id) {
	swal({
		text: "Are you sure want to delete this unpaid item?",
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
				url: base_url+"students/json_del_join/"+id,
				type: "POST",
				dataType: "json",
				
				success: function(data) {
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						window.location.reload()
					});
				}
			});
		}
	});
}