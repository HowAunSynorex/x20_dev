function init(k) {
	switch(k) {
		
		case "select2":
			$(".select2").select2({theme:"bootstrap4"});
			break;
		
	}
}

toastr.options = {
	"closeButton": false,
	"debug": false,
	"newestOnTop": false,
	"progressBar": false,
	"positionClass": "toast-bottom-left",
	"preventDuplicates": false,
	"onclick": null,
	"showDuration": "0",
	"hideDuration": "0",
	"timeOut": "2500",
	"extendedTimeOut": "10000",
	"showEasing": "swing",
	"hideEasing": "linear",
	"showMethod": "fadeIn",
	"hideMethod": "fadeOut"
}

$(this).on('keypress', function(event) {
	if ( event.keyCode == 47 ) {
		$("#modal-command").modal("show");
		setTimeout(function() {
			$("#modal-command input").focus();
		}, 500);
	}
});

function intro() {
	introJs().start();
}

function mykad(ic)
{
	ic.match(/^(\d{2})(\d{2})(\d{2})?(\d{2})?(\d{3})(\d)$/);
	var year = RegExp.$1;
	var month = RegExp.$2;
	var day = RegExp.$3;
	var gender = RegExp.$6;

	//GET BIRTHDATE
	var now = new Date().getFullYear().toString();
	var decade = now.substr(0, 2);
	if (now.substr(2,2) > year) {
	  year = parseInt(decade.concat(year.toString()), 10);
	}

	var birthdate = new Date(year, (month - 1), day);

	//GET GENDER
	var g = ( gender & 1 ) ? "male" : "female";

	//GET AGE
	var age = new Date().getFullYear() - birthdate.getFullYear(); //birthdate refer to line 15

	var birthday = birthdate.getFullYear() + '-' + ("0" + (birthdate.getMonth() + 1)).slice(-2) + '-' + ("0" + birthdate.getDate()).slice(-2);
	mykad_data = [{'birthday': birthday, 'gender': g, 'age': age }];
	
	return mykad_data;
}