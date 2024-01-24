$('[name="type"]').on('change', function() {
	let Value = $(this).val()
	if(Value == 'check_in') {
		$('.credit-sec').removeClass('d-none')
		$('[name="credit"]').prop('required', 'required')
	} else {
		$('.credit-sec').addClass('d-none')
		$('[name="credit"]').removeAttr('required')
	}
})

function clone_ask(id) {
	swal({
		text: "Are you sure want to clone this class?",
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
					"clone": id,
				},
				
				success: function(data) {
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						window.location.href = base_url+"classes/edit/"+data.result;
					});
				}
			});
		}
	});
}

function del_ask(id) {
	swal({
		text: "Are you sure want to delete this class?",
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
				url: base_url+"classes/json_del/"+id,
				type: "POST",
				dataType: "json",
				
				success: function(data) {
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						window.location.href = base_url+"classes/list";
					});
				}
			});
		}
	});
}

function del_ask_swap(id) {
	swal({
		text: "Are you sure want to delete this swap?",
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
				url: base_url+"classes/json_del_swap/"+id,
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

function disable(id) {
	swal({
		text: "Are you sure want to remove this student?",
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
				url: base_url+"classes/json_edit_join/"+id,
				type: "POST",
				dataType: "json",
				
				success: function(data) {
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						window.location.href = window.location.href+"?tab=2";
					});
				}
			});
		}
	});
}

function timetable_rest(k) {
	if($("#checkbox-timetable-"+k).is(":checked")) {
		$("#group-timetable-"+k+" .form-control-timetable").attr("readonly", true).val("");
		$("#group-timetable-"+k+" .form-control-timetable").attr("required", "required").val("");
	} else {
		$("#group-timetable-"+k+" .form-control-timetable").attr("readonly", false).val("");
		$("#group-timetable-"+k+" .form-control-timetable").attr("required", "").val("");
	}
}


if( access_denied == 1 ) {
	$('.form-control, [name="save"], [data-target="#modal-add"]').attr("disabled", true);
}

$(".DTable2").DataTable({
	pageLength:100,
	bAutoWidth:!1,
	dom: 'Bfrtip',
	buttons: [
		'copy', 'csv', 'excel', 'pdf', 'print'
	]
	// order: false
});

function class_joined(class_id, user_id) {
		
	$.ajax({
		url: base_url+"students/json_joined",
		type: "GET",
		dataType: "json",
		data: {
			"class": class_id,
			"user": user_id,
		},
		
		success: function(data) {
			
			if(data.result.active == 1) {
				$("#input-join_date-"+user_id).val(data.result.date);
				$("#input-join_date-"+user_id).attr("disabled", false);
				toastr["success"]("Class has been joined");
			} else {
				$("#input-join_date-"+user_id).val("");
				$("#input-join_date-"+user_id).attr("disabled", true);
				toastr["success"]("Class has been unjoined");
			}
						
		}
	});
}

function change_class_date(class_id, user_id, date) {
	
	$.ajax({
		url: base_url+"students/json_edit_date_join",
		type: "GET",
		dataType: "json",
		data: {
			'date': date,
			'class': class_id,
			"user": user_id,
		},
		
		success: function(data) {
			toastr["success"]("Date has been updated");
		}
	});
	
}

function change_class_timetable(class_id, user_id, timetable) {
	
	$.ajax({
		url: base_url+"students/json_edit_timetable_join",
		type: "GET",
		dataType: "json",
		data: {
			'timetable': timetable,
			'class': class_id,
			"user": user_id,
		},
		
		success: function(data) {
			toastr["success"]("Date has been updated");
		}
	});
	
}

function addTime(domThis)
{
	$('#modal-time').find('.modal-title').text('New Timetable');
	$('#modal-time').find('input[name="dy_id"]').val(domThis.attr('data-day'));
	$('#modal-time').find('input[name="title"]').val('');
	$('#modal-time').find('input[name="time_start"]').val('');
	$('#modal-time').find('input[name="time_end"]').val('');
	$('#modal-time').find('input[name="action_take"]').val('add');
	$('#modal-time').modal('show');
}
function editTime(domThis)
{
	id = domThis.closest('.list-group-item').find('.timetable-id').val();
	day = domThis.closest('.list-group-item').find('.timetable-day').val();
	title = domThis.closest('.list-group-item').find('.timetable-title').val();
	time_start = domThis.closest('.list-group-item').find('.timetable-start').val();
	time_end = domThis.closest('.list-group-item').find('.timetable-end').val();
	
	$('#modal-time').find('.modal-title').text('Edit Timetable');
	$('#modal-time').find('input[name="id"]').val(id);
	$('#modal-time').find('input[name="dy_id"]').val(day);
	$('#modal-time').find('input[name="title"]').val(title);
	$('#modal-time').find('input[name="time_start"]').val(time_start);
	$('#modal-time').find('input[name="time_end"]').val(time_end);
	$('#modal-time').find('input[name="action_take"]').val('edit');
	$('#modal-time').modal('show');
}

function actionTake()
{
	action = $('#modal-time').find('input[name="action_take"]').val();
	
	id = $('#modal-time').find('input[name="id"]').val();
	class_id = $('#modal-time').find('input[name="class_id"]').val();
	dy_id = $('#modal-time').find('input[name="dy_id"]').val();
	
	title = $('#modal-time').find('input[name="title"]').val();
	time_start = $('#modal-time').find('input[name="time_start"]').val();
	time_end = $('#modal-time').find('input[name="time_end"]').val();
	
	if (dy_id && title && time_start && time_end)
	{
		Loading(1)
		$.ajax({
			url: base_url+"/classes/manage_time",
			type: "POST",
			dataType: "json",
			data: {
				'action_take': action,
				'id': id,
				'class': class_id,
				'dy_id': dy_id,
				'title': title,
				"time_start": time_start,
				"time_end": time_end,
			},
			
			success: function(data) {
				location.reload();
			}
		});
	}
}

function delTime(id) {
	swal({
		text: "Are you sure want to delete this timetable?",
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
				url: base_url+"classes/json_del_time/"+id,
				type: "POST",
				dataType: "json",
				
				success: function(data) {
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						location.reload();
					});
				}
			});
		}
	});
}

function edit_swap(id) {
	let Modal = $('#modal-edit-swap')
	
	$.ajax({
		data: {
			'edit_swap': id
		},
		type: "POST",
		dataType: "json",
		
		success: function(data) {
			Modal.find('[name="teacher"]').select2({
				data: data.teacher
			})
			Modal.find('[name="teacher"]').val(data.result.user).trigger('change')
			Modal.find('[name="class"]').select2({
				data: data.class
			})
			Modal.find('[name="class"]').val(data.result.remark).trigger('change')
			Modal.find('[name="id"]').val(id)
			init('select2')
			Modal.modal('show')
		}
	});
}