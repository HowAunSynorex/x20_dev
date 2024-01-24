function send() {
	Loading(1);
	var Modal = $("#modal-add");
	$.ajax({
		type: "POST",
		data: {
			"send": true,
			"email": Modal.find('[name="username"]').val(),
		},
		dataType: "json",
		
		success: function(data) {
			Loading(0);
			if(data.status == "ok") {
				swal({
					"text": "The invitation confirmation link has been sent to this user's mailbox, please go to the mailbox and click the confirmation link",
					"icon": "success"
				}).then((e) => {
					location.reload();
				});
			} else {
				swal({
					"text": data.message,
					"icon": "error"
				});
			}
		}
	});
}

function del_ask(id) {
	swal({
		text: "Are you sure want to remove this admin?",
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
				url: base_url+"admins/json_del/"+id,
				type: "POST",
				dataType: "json",
				
				success: function(data) {
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						window.location.href = base_url+"admins/list";
					});
				}
			});
		}
	});
}