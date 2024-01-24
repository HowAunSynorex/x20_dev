function del_ask(id) {
	swal({
		text: "Are you sure want to delete this branch?",
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
				url: base_url+"admin/json_branches_del/"+id,
				type: "POST",
				dataType: "json",
				
				success: function(data) {
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						window.location.href = base_url+"admin/branches_list";
					});
				}
			});
		}
	});
}

function restore_ask(id) {
	swal({
		text: "Are you sure want to restore this branch?",
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
				url: base_url+"admin/json_branches_restore/"+id,
				type: "POST",
				dataType: "json",
				
				success: function(data) {
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						window.location.href = base_url+"admin/branches_list";
					});
				}
			});
		}
	});
}

function remove_ask(id) {
	swal({
		text: "Are you sure want to remove this admin?",
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
				url: base_url+"admin/json_branches_remove/"+id,
				type: "POST",
				dataType: "json",
				
				success: function(data) {
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						location.reload();
					});
				}
			});
		}
	});
}

function remove_ask_bill(id) {
	swal({
		text: "Are you sure want to remove this bill?",
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
				url: base_url+"admin/json_branches_remove/"+id,
				type: "POST",
				dataType: "json",
				
				success: function(data) {
					swal({
						"title": "Bill removed successfully",
						"icon": "success"
					}).then((e) => {
						location.reload();
					});
				}
			});
		}
	});
}

$("#modal-edit-bill").on("show.bs.modal", function (event) {
	var modal = $(this);
	var btn = $(event.relatedTarget)
	var id = btn.data('id');
	
	$.ajax({
		url: base_url+"admin/json_branches_view/"+id, 
		type: "POST", 
		dataType: "json",
		
		success:function(data) {
			$.each(data.result[0], function(k, v) {
				modal.find('[name="'+k+'"]').val(v).trigger('change');
			});
		}
	});
	
});

if( readonly_input ) {
	// $(".form-control, .btn-primary").attr("readonly", true);
	$("#form-primary .form-control, #form-primary .btn-primary").attr("disabled", true);
	// $(".select2").attr("dsiabled", true);
	init("select2");
}

function del_ask_overview(id, k) {
	swal({
		text: "Are you sure want to delete all "+k+"?",
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
				url: base_url+"admin/json_branches_overview_del/"+id+"/"+k,
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

function del_ask_active(id, k) {
	swal({
		text: "Are you sure want to delete all active "+k+"?",
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
				url: base_url+"admin/json_branches_overview_active_del/"+id+"/"+k,
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

function del_ask_inactive(id, k) {
	swal({
		text: "Are you sure want to delete all inactive "+k+"?",
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
				url: base_url+"admin/json_branches_overview_inactive_del/"+id+"/"+k,
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