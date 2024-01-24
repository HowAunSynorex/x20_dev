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
				url: base_url+"content/json_del/"+id,
				type: "POST",
				dataType: "json",
				
				success: function(data) {
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						window.location.href = base_url+"content/list/"+type;
					});
				}
			});
		}
	});
}

ClassicEditor
	.create(document.querySelector("#CK"), {
		ckfinder: {
            uploadUrl: base_url+"content/json_upload_image"
        }
	})
	.then(editor => {
		if( access_denied != undefined ) {
			editor.isReadOnly = true;
		}
	})
	.catch(error => {
		console.error( error );
	});
	
if( access_denied != undefined ) {
	$('.form-control, [name="save"], [data-target="#modal-add"]').attr("disabled", true);
}