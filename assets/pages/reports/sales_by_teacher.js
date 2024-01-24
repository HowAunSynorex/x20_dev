$('.main-tr').on('click', function() {
	if(!$(this).hasClass('collapsed')) {
		let id = $(this).attr('aria-controls')
		$.each($('.'+id), function(k, v) {
			let sub_id = $(v).attr('aria-controls')
			$('.'+sub_id).collapse('hide')
		})
	}
})