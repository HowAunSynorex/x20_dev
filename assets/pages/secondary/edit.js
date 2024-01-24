function del_ask(id, type, title) {
	swal({
		text: "Are you sure want to delete this "+title+"?",
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
				url: base_url+"secondary/json_del/"+id,
				type: "POST",
				dataType: "json",
				
				success: function(data) {
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						window.location.href = base_url+"secondary/list/"+type;
					});
				}
			});
		}
	});
}

function addSubject()
{
	var subject = `
		<div class="input-group mb-2">
			<input type="text" class="form-control" name="subject[]" value="" />
			<div class="input-group-append">
				<button class="btn btn-danger" type="button" onclick="removeSubject($(this));"><i class="fas fa-trash"></i></button>
			</div>
		</div>
	`;
	$('#subject-div').append(subject);
}



function removeSubject(domThis)
{
	swal({
		text: "Are you sure want to delete this subject ?",
		icon: "warning",
		buttons: true,
		dangerMode: true,
		buttons: {
			cancel: "No",
			text: "Yes"
		}
	}).then((willDelete) => {
		if(willDelete) {
			domThis.closest('.input-group').remove();
		}
	});
}

if( access_denied != undefined ) {
	$('.form-control, [name="save"], [data-target="#modal-add"]').attr("disabled", true);
}