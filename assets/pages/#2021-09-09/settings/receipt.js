$(document).ready(function() {
	$(this).scrollTop(0);
	var selected_receipt = $(`input[name="selected-receipt"]`).val();
	$("#receipt-"+selected_receipt).addClass("d-none");
	$.each($(".receipt-group"), function() {
		var theme_value = $(this).find(`input[name="receipt"]`).val();
		if (selected_receipt == theme_value) {
			$(this).find("label").removeClass("btn-secondary");
			$(this).find("span").text("Activated");
			$(this).find("label").addClass("btn-primary");	
		}
	});
});

$(".receipt-group").find("input").on("click", function() {
	if($("this:checked")){
		$(".receipt-group").find("label").removeClass("btn-primary");
		$(".receipt-group").find("label").addClass("btn-secondary");
		$(".receipt-group").find("span").text("Active");
		$("#"+$(this).val()).find("span").text("Activated");
		$("#"+$(this).val()).removeClass("btn-secondary");
		$("#"+$(this).val()).addClass("btn-primary");
		Loading(1);
		$.ajax({
			url: base_url+"settings/update_receipt/"+$(this).val(),
			type: "POST",
			dataType: "json",
			success: function(data) {
				Loading(0);
				swal({
					"title": "Saved",
					"icon": "success"
				}).then((e) => {
					window.location.href = base_url+"settings/receipt?tab=2";
				});
			}
		});
	}
});

function return_sample() {
	
	var d = new Date()
	var yyyy = d.getFullYear()
	var yy = yyyy.toString().substr(-2)
	var mm = d.getMonth() + 1
	mm = (mm < 10) ? "0"+mm : mm;
	var dd = d.getDate()
	dd = (dd < 10) ? "0"+dd : dd;
	
	var format = $('[name="receipt_no"]').val()
	var max_no = $('[name="receipt_no_max"]').val()
	var sampel = ""

	sample = format.replace("%DD%", dd).replace("%MM%", mm).replace("%YY%", yy).replace("%YYYY%", yyyy) + "0".repeat(max_no - 1) + "1"
	
	$('[name="sample"]').val(sample)
	
}

if( access_denied != undefined ) {
	$('.form-control, [name="save"], [name="save-account"], [name="receipt"], [data-target="#modal-add"]').attr("disabled", true);
}