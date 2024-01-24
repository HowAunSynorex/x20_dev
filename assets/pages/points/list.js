var invalidChars = [
	"-",
	"+",
	"e",
];

$(".DTable2").DataTable({
	pageLength:100,
	bAutoWidth:!1,
	dom: 'Bfrtip',
	buttons: [
		'excel',
		'pdf'
	]
	// order: false
});

$('input[type=number]').on("keydown", function(e) {
	if (invalidChars.includes(e.key)) {
		e.preventDefault();
	}
});

function select(select, type) {
    window.location.href = base_url+"points/list/"+type+"/"+select.value;
};

$("#modal-edit").on("show.bs.modal", function (event) {
	var Id = $(event.relatedTarget).data("id"), Modal = $(this);
	Loading(1);
	$.ajax({
		url: base_url+"points/json_view/"+Id,
		type: "POST",
		dataType: "json",
		
		success: function(data) {
			if(data.result.length == 1) {
				
				Loading(0);
				$.each(data.result[0], function(k, v) {
					Modal.find('[name="'+k+'"]').val(v);
					if(k == 'payment' && v != null) {
						Modal.find('.payment-sec').removeClass('d-none')
						Modal.find('.payment-sec p').text(v)
						Modal.find('input, textarea, button:not(.close)').attr('disabled', 'disabled')
						Modal.find('a').attr('onclick', '')
					}
					if(k == 'payment' && v == null) {
						Modal.find('.payment-sec').addClass('d-none')
						Modal.find('input, textarea, button').removeAttr('disabled')
						Modal.find('a').attr('onclick', "del_ask('ewallet')")
					}
				});
				
			} else {
				alert(data.message);
			}
		}
	});
})

function del_ask(type) {
	
	var id = $('[name="id"]').val()
	console.log(id)
	
	swal({
		text: "Are you sure want to delete this "+type+"?",
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
				url: base_url+"points/json_del/"+id+"/"+type,
				type: "POST",
				dataType: "json",
				
				success: function(data) {
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						window.location.href = window.location.href;
					});
				}
			});
		}
	});
}