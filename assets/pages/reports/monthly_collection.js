$(".DTable2").DataTable({
	pageLength:100,
	bAutoWidth:!1,
	dom: 'Bfrtip',
	buttons: [
		'excel'
	]
	// order: false
});

$(function() {
	$(document).keyup(function(e) {
		var key = (e.keyCode ? e.keyCode : e.charCode);
		switch (key) {
			case 120: // f9 key
				console.log($('#draft_key').html());
				if ($('#draft_key').html().trim() != '')
				{
					$('#draft_key').html('');
				}
				else
				{
					$('#draft_key').html('<input type="hidden" name="is_draft" value="1" />');
				}
				
				$('#search_form').submit();
			break;
			default: ;
		}
	});
});