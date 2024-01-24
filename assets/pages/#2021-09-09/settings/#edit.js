function del_ask(id, type) {
	var title = 'admin'

	switch ( type ) {
		case 'admins':
			title = 'admin'
			break;

		case 'branches':
			title = 'branch'
			break;
	}

	swal({
		text: "Are you sure want to delete this " + title + "?",
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
				url: base_url+"settings/json_del/"+type+"/"+id,
				type: "POST",
				dataType: "json",
				
				success: function(data) {
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						window.location.href = base_url+"/settings/list/"+type;
					});
				}
			});
		}
	});
}
