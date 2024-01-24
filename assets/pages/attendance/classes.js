function print_normal() {
	$("span").addClass("join-on-text");
	window.print();
}

function print_join_date() {
	$("span").removeClass("join-on-text");
	window.print();
}

$(document).ready(function() {
	
	var days = $('[name="days"]').val().split("+")
	$.each(days, function(k, v) {
		$("#day-"+v).mouseenter(function() {
			$(this).text("");
			
			var check;
			
			$.each($(".day-"+v).find("input"), function(k, v) {
				if(v.checked == false) {
					check = 1;
				}
			});
				
			if(check == null) {
				$(this).append("<input id='checkbox-"+v+"' onclick='select_all("+v+")' type='checkbox' checked>");
			} else {
				$(this).append("<input id='checkbox-"+v+"' onclick='select_all("+v+")' type='checkbox'>");
			}
		})
		$("#day-"+v).mouseleave(function() {
			$(this).text(v);
		})
	})
	
})

function select_all(v) {
	
	var check = 0;
	$.each($(".day-"+v).find("input"), function(k, v) {
		if(v.checked == false) {
			check = 1;
		}
	})
	
	if(check == 1) {
		$.each($(".day-"+v).find("input"), function(k, v) {
			if(v.checked == false) {
				v.click();
			}
		})
	} else {
		$.each($(".day-"+v).find("input"), function(k, v) {
			v.click();
		})
	}

}

function add_to_removed_list(input){
	var id = input.id.split("_")[1];
	var default_val = $('[name="removed_list"]').val();
	if(default_val.indexOf(id) >= 0) {
		default_val = default_val.replace(id+",", "");
		$('[name="removed_list"]').val(default_val);
	} else {
		$('[name="removed_list"]').val(default_val+id+",");
	}
}