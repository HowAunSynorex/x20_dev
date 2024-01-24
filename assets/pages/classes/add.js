$('[name="type"]').on('change', function() {
	let Value = $(this).val()
	if(Value == 'check_in') {
		$('.credit-sec').removeClass('d-none')
		$('[name="credit"]').prop('required', 'required')
	} else {
		$('.credit-sec').addClass('d-none')
		$('[name="credit"]').removeAttr('required')
	}
})