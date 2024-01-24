
function check_all() {
	if($("#bulk_check").prop("checked")) {
		$.each($(".student"), function(k, v) {
			$(v).prop("checked", true)
			$(".action-sec").removeClass("d-none")
		})
	} else {
		$.each($(".student"), function(k, v) {
			$(v).prop("checked", false)
			$(".action-sec").addClass("d-none")
		})
	}
}

function check() {
	
	let is_checked = false,
		is_all_checked = true
	
	$.each($(".student"), function(k, v) {
		if($(v).prop("checked")) {
			is_checked = true
		} else {
			is_all_checked = false
		}
	})
	
	if(is_checked) {
		$(".action-sec").removeClass("d-none")
	} else {
		$(".action-sec").addClass("d-none")
	}
	
	if(is_all_checked) {
		$("#bulk_check").prop("checked", true)
	} else {
		$("#bulk_check").prop("checked", false)
	}
	
}

function take_action(domThis) {
	var count = 0;
	$.each($(".student"), function(k, v) {
		if($(v).prop("checked")) {
			count++;
		}
	})
	
	if (count > 0)
	{
		swal({
			text: "Are you sure want to "+ domThis.attr('data-action') + " insurance for selected student?",
			icon: "warning",
			buttons: true,
			dangerMode: true,
			buttons: {
				cancel: "No",
				text: "Yes"
			}
		}).then((willDelete) => {
			if(willDelete) {
				$('input[name="action_take"]').val(domThis.attr('data-action'));
				
				domThis.attr("type", "submit").attr("onclick", "").click();
			}
		});
	}
	else
	{
		swal({text: 'Please select at least one student', icon: 'error'});
	}
}