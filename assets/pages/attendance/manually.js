function del_ask() {
	var id = $('[name="id"]').val()
	swal({
		text: "Are you sure want to delete this attendance?",
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
				url: base_url+"attendance/json_del/"+id,
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

$("#modal-edit").on("show.bs.modal", function (event) {
	var Id = $(event.relatedTarget).data("id"), Modal = $(this);
	Loading(1);
	$.ajax({
		url: base_url+"attendance/json_view/"+Id,
		type: "POST",
		dataType: "json",
		
		success: function(data) {
			if(data.result.length == 1) {
				
				Loading(0);
				
				$.each(data.result[0], function(k, v) {
					Modal.find('[name="'+k+'"]').val(v);
					var t = data.result[0]['datetime'].split(/[- :]/);
					var month;
					if (t[1].size == 1) {
						month = '0'+t[1];
					} else {
						month = t[1];
					}
					Modal.find('[name="date"]').val( t[0]+'-'+month+'-'+t[2] );
					Modal.find('[name="time"]').val( t[3]+':'+t[4]+':'+t[5] );
				});
				
			} else {
				alert(data.message);
			}
		}
	});
})