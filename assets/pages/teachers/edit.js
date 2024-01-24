function del_ask(id) {
	swal({
		text: "Are you sure want to delete this teacher?",
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
				url: base_url+"teachers/json_del/"+id,
				type: "POST",
				dataType: "json",
				
				success: function(data) {
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						window.location.href = base_url+"/teachers/list";
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
	var id = $('[name="teacher"]').val();
	$.ajax({
		url: base_url+"teachers/check_username/"+username+"/"+id,
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
	var id = $('[name="teacher"]').val();
	if(cardid != '') {
		$.ajax({
			url: base_url+"teachers/check_rfid/"+cardid+"/"+id,
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
	} else {
		
		$("#check_cardid").addClass("d-none");
		
		if($("#check_cardid").hasClass("d-none")) {
			$('[name="save"]').prop("disabled", false);
		} else {
			$('[name="save"]').prop("disabled", true);
		}
		
	}
	
});

if( access_denied != undefined ) {
	$('.form-control, [name="save"], [data-target="#modal-add"]').attr("disabled", true);
}