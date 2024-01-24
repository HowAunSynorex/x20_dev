function row_del(k) {
	var i = $("tbody tr").length;
	if( i > 1 ) $('#row-'+k).remove();
}

function row_add() {
	var Id = Date.now();
	$("tbody").append(`
	<tr id="row-`+Id+`">
		<td>
			<select class="form-control select2" name="log_data[`+Id+`][item]">
			</select>
			<div class="mt-2 small">
				<a href="javascript:;" class="text-danger" onclick="row_del(`+Id+`)"><i class="fa fa-fw fa-trash"></i> Remove</a>
			</div>
		</td>
		<td><input type="number" class="form-control text-right input-remove_arrow" name="log_data[`+Id+`][stock]" data-input="stock" readonly></td>
		<td><input type="number" class="form-control text-right input-remove_arrow" name="log_data[`+Id+`][adjust]" name=""></td>
		<td><input type="number" class="form-control text-right input-remove_arrow" name="log_data[`+Id+`][in]" name=""></td>
		<td><input type="number" class="form-control text-right input-remove_arrow" name="log_data[`+Id+`][out]" name=""></td>
	</tr>
	`);
	$.ajax({
		url: base_url+"/items/json_list_select2",
		type: "POST",
		dataType: "json",
		
		success: function(data) {
			Loading(0);
			$("#row-"+Id).find('select').select2({
				data: data.result
			})
			init('select2')
			$("#row-"+Id).find('select').attr('onchange', 'row_view('+Id+', this.value)')
		}
	});
}
row_add();

function row_view(row_id, v) {
	Loading(1);
	if(v.length > 0) {
		$.ajax({
			url: base_url+"/items/json_view/"+v,
			type: "POST",
			dataType: "json",
			
			success: function(data) {
				Loading(0);
				row_add();
				$("#row-"+row_id).find('[data-input="stock"]').val(data.result.stock_on_hand);
			}
		});
	} else {
		row_del(row_id)
		Loading(0);
	}
}

/* function init_select2() {
	$(".select2-this").select2({
		minimumInputLength: 1,
		minimumResultsForSearch: 10,
		ajax: {
			url: base_url+"/items/json_list",
			dataType: "json",
			type: "GET",
			data: function (params) {
				var queryParameters = {
					term: params.term
				}
				return queryParameters;
			},
			processResults: function (data) {
				return {
					results: $.map(data.items, function (item) {
						return {
							text: item.text,
							id: item.id
						}
					})
				};
			}
		}

	});
} */