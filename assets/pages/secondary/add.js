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
	domThis.closest('.input-group').remove();
}