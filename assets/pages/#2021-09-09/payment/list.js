function del_ask(id) {
	swal({
		text: "Are you sure want to delete this payment?",
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
				url: base_url+"payment/json_del/"+id,
				type: "POST",
				dataType: "json",
				success: function(data) {
					
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						window.location.href = base_url+"/payment/list";
					});
					
				}
			});
			
		}
	});
}

function send_email(id) {
	swal({
		text: "Are you sure want to send invoice via email to this student?",
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
					swal({
						"title": data.message,
						"icon": data.status == "ok" ? "success" : "error"
					});
				}
			});
			
		}
	});
}

function send_sms(id) {
	swal({
		text: "Are you sure want to send invoice via SMS to this student?",
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