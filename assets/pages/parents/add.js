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
	$.ajax({
		url: base_url+"students/check_username/"+username+"/null",
		type: "POST",
		dataType: "json",
		
		success: function(data) {
			if(data.result == false) {
				$("#check_username").removeClass("d-none");
				$('[name="save"]').prop("disabled", true);
			} else {
				$("#check_username").addClass("d-none");
				$('[name="save"]').prop("disabled", false);
			}
		}
	});
});