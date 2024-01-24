function del_ask(id, type, title) {
	swal({
		text: "Are you sure want to delete this "+title+"?",
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
				url: base_url+"admin/json_secondary_del/"+id,
				type: "POST",
				dataType: "json",
				
				success: function(data) {
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						window.location.href = base_url+"admin/secondary_list/"+type;
					});
				}
			});
		}
	});
}

function select_all(k) {
	var cell = $(k).closest("th"),
		cellIndex = cell[0].cellIndex,
		checked = $(k).prop('checked')
	$.each($("tbody tr td"), function(k, v) {
		if(cellIndex == $(v)[0].cellIndex) {
			$(v).find('input').prop('checked', checked)
		}
	})
}

$('tbody input[type="checkbox"]').on('change', function() {
	
	let cellIndex = $(this).closest('td')[0].cellIndex,
		th = '',
		is_all_checked = true
		
	$.each($("thead th"), function(k, v) {
		if(cellIndex == $(v)[0].cellIndex) {
			th = $(v)
		}
	})
	
	$.each($("tbody tr td"), function(k, v) {
		if(cellIndex == $(v)[0].cellIndex) {
			$.each($(v).find('input'), function(k2, v2) {
				if($(v2).prop('checked') == false) {
					is_all_checked = false
				}
			})
			
		}
	})
	
	th.find('input').prop('checked', is_all_checked)
	
})