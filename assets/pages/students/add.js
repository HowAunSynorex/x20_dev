function check(checkbox) {

    var username = document.getElementById("username");
    var password = document.getElementById("password");

    var username_text = document.getElementById("username-text");
    var password_text = document.getElementById("password-text");


    if(checkbox.checked == true) {

        username.removeAttribute('readonly');
        username.setAttribute('required', 'required');
		username.value = $('[name="fullname_en"]').val();
        username_text.classList.add('text-danger');

        password.removeAttribute('readonly');
        password.setAttribute('required', 'required');
		password.value = 123;
        password_text.classList.add('text-danger');
        $("#default_password").removeClass("d-none");

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
        $("#default_password").addClass("d-none");

    }

};

$("#username").on("change", function() {
	var username = $(this).val();
	if(username != '') {
		$.ajax({
			url: base_url+"students/check_username/"+username+"/null",
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
	} else {
		
		$("#check_username").addClass("d-none");
		
		if($("#check_cardid").hasClass("d-none") && $("#check_username").hasClass("d-none")) {
			$('[name="save"]').prop("disabled", false);
		} else {
			$('[name="save"]').prop("disabled", true);
		}
		
	}
});

$("input[name='nric']").on("input", function() {
	var nric = $(this).val();
	
	var mykad_data = mykad(nric)[0];
	
	$('input[name="birthday"]').val(mykad_data['birthday']);
	$('#radio-gender-' + mykad_data['gender']).prop("checked", true);
});

$("#rfid_cardid").on("change", function() {
	var cardid = $(this).val();
	if(cardid != '') {
		$.ajax({
			url: base_url+"students/check_rfid/"+cardid+"/null",
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
		
		if($("#check_cardid").hasClass("d-none") && $("#check_username").hasClass("d-none")) {
			$('[name="save"]').prop("disabled", false);
		} else {
			$('[name="save"]').prop("disabled", true);
		}
		
	}
	
});