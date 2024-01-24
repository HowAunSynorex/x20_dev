$(document).ready(function() {
	
	var check = 0;
	$.each($("td").find("input"), function(k, v) {
		if(v.checked == false) {
			check = 1;
		}
	})

	if(check == 0) {
		$("#select-all-checkbox").prop("checked", true);
	}
})

function select_all() {
	var check = 0;
	$.each($("td").find("input"), function(k, v) {
		if(v.checked == false) {
			check = 1;
		}
	})
	
	if(check == 1) {
		$.each($("td").find("input"), function(k, v) {
			if(v.checked == false) {
				v.click();
			}
		})
	} else {
		$.each($("td").find("input"), function(k, v) {
			v.click();
		})
	}
}

function add_to_removed_list(input){
	var id = input.id;
	var default_val = $('[name="removed_list"]').val();
	if(default_val.indexOf(id) >= 0) {
		default_val = default_val.replace(id+",", "");
		$('[name="removed_list"]').val(default_val);
	} else {
		$('[name="removed_list"]').val(default_val+id+",");
	}
}