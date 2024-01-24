function send_email(id) {
	swal({
		text: "Are you sure want to send notification via email to this student?",
		icon: "warning",
		buttons: true,
		dangerMode: true,
		buttons: {
			cancel: "No",
			text: "Yes"
		}
	}).then((willDelete) => {
		if(willDelete) {
			Loading(1);
			$.ajax({
				type: "POST",
				dataType: "json",
				data: {
					"send_email": id,
				},
				success: function(data) {
					Loading(0);
					if( data.status == "ok" ) {
						swal({
							"title": "Sent Successfully",
							"text": data.message,
							"icon": "success"
						});
					} else {
						swal({
							"title": "Error",
							"text": data.message,
							"icon": "error"
						});
					}
				}
			});
			
		}
	});
}

function send_sms(id) {
	swal({
		text: "Are you sure want to send notification via SMS to this student?",
		icon: "warning",
		buttons: true,
		dangerMode: true,
		buttons: {
			cancel: "No",
			text: "Yes"
		}
	}).then((willDelete) => {
		if(willDelete) {
			Loading(1);
			$.ajax({
				type: "POST",
				dataType: "json",
				data: {
					"send_sms": id,
				},
				success: function(data) {
					Loading(0);
					swal({
						"title": data.message,
						"icon": data.status == "ok" ? "success" : "error"
					});
				}
			});
			
		}
	});
}

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

function del_ask_class(user_id, class_id, period) {
	swal({
		text: "Are you sure want to delete this unpaid class?",
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
				url: base_url+"reports/json_del_unpaid_class/"+user_id+"/"+class_id+"/"+period,
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