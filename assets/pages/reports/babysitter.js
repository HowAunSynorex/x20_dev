

function update_student_date_call(domThis) {
	
	$.ajax({
		url: base_url+"students/json_edit_date_call",
		type: "GET",
		dataType: "json",
		data: {
			'id': domThis.attr('data-id'),
			'date_call': domThis.val(),
		},
		
		success: function(data) {
			toastr["success"]("Date Call has been updated");
		}
	});
	
}