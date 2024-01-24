function print() { $(".section-print").printThis(); }

function edit_outstanding(id, month = '') {
	
	$('.modal').modal('hide')
	
	let Modal = $('#modal-edit-outstanding')
	Modal.find('[name="month"]').val(month)
		
	$.ajax({
		type: "POST",
		dataType: "json",
		data: {
			'view_unpaid': id,
		},
		
		success: function(data) {
			if(data.result.length == 1) {
				let result = data.result[0]
				$.each(result, function(k, v) {
					if(k == 'title') {
						Modal.find('[name="'+k+'"]').text(v)
					} else if (k == 'amount') {
						Modal.find('[name="'+k+'"]').text(parseFloat(v).toFixed(2))
					} else if (k == 'qty') {
						Modal.find('[name="'+k+'"]').text('x'+v)
					} else if (k == 'discount') {
						if(isNaN(v)) {
							v = JSON.parse(v)
							Modal.find('[name="'+k+'"]').val(v[month])
						} else {
							Modal.find('[name="'+k+'"]').val(v)
						}
					} else {
						Modal.find('[name="'+k+'"]').val(v)
					}
				})
				Modal.modal('show')
			}
		}
	});
	
}

$('#modal-edit-outstanding').on('hide.bs.modal', function(e) {
	$('#modal-payment').modal('show')
})

function del_ask(id) {
	swal({
		text: "Are you sure want to delete this student?",
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
				url: base_url+"students/json_del/"+id,
				type: "POST",
				dataType: "json",
				
				success: function(data) {
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						window.location.href = base_url+"/students/list";
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
						window.location.href = window.location.href+"?tab=3";
					});
				}
			});
		}
	});
}

function del_ask_parent(id) {
	swal({
		text: "Are you sure want to delete this parent from student?",
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
						window.location.href = window.location.href+"?tab=3";
					});
				}
			});
		}
	});
}
$('#modal-edit-item').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget)
	var id = button.data('id')
	var modal = $(this)
	
	$.ajax({
		url: base_url+"students/json_view_join/"+id,
		type: "POST",
		dataType: "json",
		success: function(data) {
			$.each(data.result[0], function(k, v) {
				$('[name="'+k+'"]').val(v).trigger("change");
				$("#i_id").val(data.result[0]['movement_log'].split(",")[0]);
				$("#log_i_id").val(data.result[0]['movement_log'].split(",")[1]);
			});
		}
	});
	
})

var tableClass;

tableClass = $("#tableClass").DataTable({pageLength:100,bAutoWidth:!1});

function filterTableClass(domThis)
{
	if(domThis.val())
	{
		tableClass.columns(3).search(domThis.val()).draw();
	}
	else
	{
		tableClass.columns(3).search('').draw();
	}
}

if(formTitle.length > 0) {
	$('.course-filter').val(formTitle).trigger('change')
}

/*$(document).ready(function() {
	
	var user_id = $(".switch > input").attr("data-user-id");
	
	$.ajax({
		url: base_url+"students/json_list_join/"+user_id,
		type: "POST",
		dataType: "json",
		success: function(data) {
			$.each(data.result, function(k, v) {
				var i = $('[data-class-id="' + v["class"] + '"]').attr("id").replace("enable", "");
				$("#enable" + i).attr("data-id", v["id"]);
				if(v['active'] == 1) {
					$("#enable" + i).prop("checked", true);
					$("#date" + i).prop("disabled", false);
				}
				$("#date" + i).val(v["date"]);
			});
		}
	});
	
});*/

/*$(".switch > input").on("change", function() {
	
	var log_id = $(this).attr("data-id");
	var class_id = $(this).attr("data-class-id");
	var user_id = $(this).attr("data-user-id");
	var i = $(this).attr("id").replace("enable", "");
	var date_input = $("#date"+i);
	var d = new Date();
	var year = d.getFullYear();
	var month = d.getMonth() < 10 ? "0" + (d.getMonth() + 1) : (d.getMonth() + 1);
	var day = d.getDate() < 10 ? "0" + d.getDate() : d.getDate();
	var date = year + "-" + month + "-" + day;

	if($(this).prop("checked") == true) {
		
		if(log_id == '') {
			
			$.ajax({
				url: base_url+"students/json_add_join/"+user_id+"/"+class_id,
				type: "POST",
				dataType: "json",
				success: function(data) {
					date_input.val(date);
					$("#enable" + i).attr("data-id", data.result[0]['id']);
					toastr["success"]("Class has been unjoined");
				}
			});
			
		} else {
			
			$.ajax({
				url: base_url+"students/json_view_join/"+log_id,
				type: "POST",
				dataType: "json",
				success: function(data) {
											
					$.ajax({
						url: base_url+"students/json_edit_join/"+log_id+"/"+date+"/1",
						type: "POST",
						dataType: "json",
						success: function(data) {
							date_input.val(date);
							toastr["success"]("Class has been joined");
						}
					});

				}
			});
			
		}

		date_input.prop("disabled", false);
		
	} else {
				
		$.ajax({
			url: base_url+"students/json_edit_join/"+log_id+"/ /0",
			type: "POST",
			dataType: "json",
			success: function(data) {
				$(this).attr("data-id", data.result);
				toastr["success"]("Class has been unjoined");
			}
		});
		
		date_input.val(null);
		date_input.prop("disabled", true);
	}
	
});*/

/*$('[name="class_date"]').on("change", function() {
	
	Loading(1)
	var i = $(this).attr("id").replace("date", "");
	var log_id = $("#enable" + i).attr("data-id");
	var date = $(this).val();
	
	$.ajax({
		url: base_url+"students/json_edit_date_join/"+log_id+"/"+date,
		type: "POST",
		dataType: "json",
		success: function(data) {
			Loading(0)
			toastr["success"]("Date has been updated");
		}
	});
	
});*/

// function check(checkbox) {

    // var username = document.getElementById("username");
    // var password = document.getElementById("password");

    // var username_text = document.getElementById("username-text");
    // var password_text = document.getElementById("password-text");


    // if(checkbox.checked == true) {

        // username.removeAttribute('readonly');
        // username.setAttribute('required', 'required');
		// username.value = $('[name="fullname_en"]').val();
        // username_text.classList.add('text-danger');

        // password.removeAttribute('readonly');
        // password.setAttribute('required', 'required');
		// password.value = 123;
        // password_text.classList.add('text-danger');
        // $("#default_password").removeClass("d-none");

    // } else {

        // username.setAttribute('readonly', 'readonly');
        // username.removeAttribute('required');
        // username.value = '';
        // username_text.classList.remove('text-danger');
		// $("#check_username").addClass("d-none");

        // password.setAttribute('readonly', 'readonly');
        // password.removeAttribute('required');
        // password.value = '';
        // password_text.classList.remove('text-danger');
        // $("#default_password").addClass("d-none");

    // }

// };

$("#username").on("change", function() {
	var username = $(this).val();
	var id = $('[name="student"]').val();
	$.ajax({
		url: base_url+"students/check_username/"+username+"/"+id,
		type: "POST",
		dataType: "json",
		
		success: function(data) {
			if(data.result == false) {
				$("#check_username").removeClass("d-none");
			} else {
				$("#check_username").addClass("d-none");
			}
			
			if($("#check_cardid").hasClass("d-none") && $("#check_username").hasClass("d-none")) {
				$('[name="save"]').prop("disabled", false);
			} else {
				$('[name="save"]').prop("disabled", true);
			}
			
		}
	});
});

$("input[name='nric']").on("input", function() {
	var nric = $(this).val();
	
	var mykad_data = mykad(nric)[0];
	
	$('input[name="birthday"]').val(mykad_data['birthday']);
	$('#radio-gender-' + mykad_data['gender']).prop("checked", true);
});

$("#rfid_cardid").on("change", function() {
	var cardid = $(this).val();
	var id = $('[name="student"]').val();
	$.ajax({
		url: base_url+"students/check_rfid/"+cardid+"/"+id,
		type: "POST",
		dataType: "json",
		
		success: function(data) {
			if(data.result == false) {
				$("#check_cardid").removeClass("d-none");
			} else {
				$("#check_cardid").addClass("d-none");
			}
			
			if($("#check_cardid").hasClass("d-none") && $("#check_username").hasClass("d-none")) {
				$('[name="save"]').prop("disabled", false);
			} else {
				$('[name="save"]').prop("disabled", true);
			}	
			
		}
	});
});

// by steve
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
				$("#input-join_date-"+class_id).val(data.result.date);
				$("#input-join_date-"+class_id).attr("disabled", false);
				$("#input-timetable-"+class_id).attr("disabled", false);
				toastr["success"]("Class has been joined");
			} else {
				$("#input-join_date-"+class_id).val("");
				$("#input-join_date-"+class_id).attr("disabled", true);
				$("#input-timetable-"+class_id).attr("disabled", true);
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

function service_joined(service_id, user_id) {
		
	$.ajax({
		url: base_url+"students/json_joined",
		type: "GET",
		dataType: "json",
		data: {
			"service": service_id,
			"user": user_id,
		},
		
		success: function(data) {
			
			if(data.result.active == 1) {
				$("#input-join_date-"+service_id).val(data.result.date);
				$("#input-join_date-"+service_id).attr("disabled", false);
				toastr["success"]("Service has been joined");
			} else {
				$("#input-join_date-"+service_id).val("");
				$("#input-join_date-"+service_id).attr("disabled", true);
				toastr["success"]("Service has been unjoined");
			}
						
		}
	});
}

function change_service_date(service_id, user_id, date) {
	
	$.ajax({
		url: base_url+"students/json_edit_date_join",
		type: "GET",
		dataType: "json",
		data: {
			'date': date,
			'service': service_id,
			"user": user_id,
		},
		
		success: function(data) {
			toastr["success"]("Date has been updated");
		}
	});
	
}

function append_row() {
	
	let Id = Date.now(),
		Html = `
		<tr id="`+Id+`">
			<td>
				<select class="form-control select2" name="item[]" required>
					<option value="">-</option>
					`;
					
	$.each(JSON.parse(items_options), function(k, v) {
		Html += `<option value="`+v.pid+`">`+v.title+`</option>`;
	})
	
	Html +=		`</select>
			</td>
			<td>
				<input type="number" class="form-control" name="qty[]" value="1" required>
			</td>
			<td class="align-middle">
				<a href="javascript:;" onclick="del_row(`+Id+`)" class="text-danger"><i class="fa fa-fw fa-times-circle"></i></a>
			</td>
		</tr>
	`;
	
	$('#modal-add-item table tbody').append(Html)
	init('select2')
	
}

function del_row(Id) {
	
	if($('#modal-add-item table tbody tr').length > 1) {
		$('#'+Id).remove()
	}
	
}

function redirect_parent_page(v) {
	if(v == "@NEW_PARENT") {
		$("#modal-add_new_parent").modal("show");
	}
}

function update_relationship(domThis) {
	
	$.ajax({
		url: base_url+"students/json_edit_relationship",
		type: "GET",
		dataType: "json",
		data: {
			'id': domThis.attr('data-id'),
			'relationship': domThis.val(),
		},
		
		success: function(data) {
			toastr["success"]("Relationship has been updated");
		}
	});
	
}

if( access_denied === null ) {
	$('.form-control, [name="save"], [data-target="#modal-add"]').attr("disabled", true);
}
