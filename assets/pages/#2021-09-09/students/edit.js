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

$(document).ready(function() {
	
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
	
});

$(".switch > input").on("change", function() {
	
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
	
});

$('[name="class_date"]').on("change", function() {
	
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
	
});

function check(checkbox) {

    var username = document.getElementById("username");
    var password = document.getElementById("password");

    var username_text = document.getElementById("username-text");
    var password_text = document.getElementById("password-text");


    if(checkbox.checked == true) {

        username.removeAttribute('readonly');
        username.setAttribute('required', 'required');
        username_text.classList.add('text-danger');

        password.removeAttribute('readonly');
        password.setAttribute('required', 'required');
        password_text.classList.add('text-danger');

    } else {

        username.setAttribute('readonly', 'readonly');
        username.removeAttribute('required');
        username.value = '';
        username_text.classList.remove('text-danger');
		$("#check_username").addClass("d-none");

        password.setAttribute('readonly', 'readonly');
        password.removeAttribute('required');
        password.value = '';
        password_text.classList.remove('text-danger');

    }

};

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

if( access_denied != undefined ) {
	$('.form-control, [name="save"], [data-target="#modal-add"]').attr("disabled", true);
}