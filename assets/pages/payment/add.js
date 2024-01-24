$('[name="ewallet"]').on('change', function() {
	let Total = parseFloat($('[name="total"]').val()),
		Ewallet = parseFloat($('[name="ewallet_value"]').val()),
		Deduction = Ewallet

	if(Ewallet > Total) {
		Deduction = Total
	}

	if (Ewallet > 0)
	{
		if($(this).prop('checked')) {
			$('[name="adjust_label"]').val('Deduction from Ewallet')
			$('[name="adjust"]').val(-Math.abs(Deduction))
			//$('[name="adjust"]').prop('readonly','readonly');
		} else {
			$('[name="adjust_label"]').val('')
			$('[name="adjust"]').val('')
			//$('[name="adjust"]').removeProp('readonly');
		}

		cal()
	}
	else
	{
		$('[name="ewallet"]').prop('checked', false);
	}
})

/*
$('.item-selected').on('click', function() {
	alert('a');
	update_material_fee();
	cal()
})
*/

function show_category() {
	$('.category-sec').removeClass('d-none')
	$('.category').trigger('change')
}

$("#modal-add").on("hide.bs.modal", function (event) {
	$('.category-sec').addClass('d-none')
	$.ajax({
		url: base_url+"payment/json_item_list",
		type: "POST",
		dataType: "json",

		success: function(data) {
			$('[name="item"]').empty()
			$('[name="item"]').select2({
				data: data.result
			})
			init('select2')
			Loading(0)
		}
	});
});

$('.category').on('change', function() {
	let Value = $(this).val()

	Loading(1)
	$.ajax({
		url: base_url+"payment/json_filter_item/"+Value,
		type: "POST",
		dataType: "json",

		success: function(data) {
			$('[name="item"]').empty()
			$('[name="item"]').select2({
				data: data.result
			})
			init('select2')
			Loading(0)
		}
	});
})

$(document).on("keypress", 'input', function (e) {
    var code = e.keyCode || e.which;
    if (code == 13) {
        e.preventDefault();
        return false;
    }
});

var record = [];
var class_count = 0;

$(document).ready(function() {

	$("tbody > tr").each(function() {
		record.push(this.className.replace('item', ''));
	});

	$.each(record, function(k, v) {
		$('input[name="unpaid['+v+'][qty]"], input[name="unpaid['+v+'][price_unit]"]').on("change", function() {
			price_unit = $('input[name="unpaid['+v+'][price_unit]"]').val(),
			qty = $('input[name="unpaid['+v+'][qty]"]').val();
			price_unit = parseFloat(price_unit);
			qty = parseFloat(qty);
			$('input[name="unpaid['+v+'][price_unit]"]').val( (price_unit).toFixed(2) );
			$('input[name="unpaid['+v+'][amount]"]').val( (price_unit*qty).toFixed(2) );
			var amount = $('input[name="unpaid['+v+'][amount]"]').val();
			let id = $('input[name="unpaid['+v+'][log_id]"]').val();

			cal();
			var log_join_id = $('input[name="unpaid['+v+'][id]"]').val();
			item_edit(log_join_id,'qty',qty);
			item_edit(log_join_id,'amount',amount);
			item_edit(log_join_id,'amount_unit',price_unit);
		});
	});

	$.each(record, function(k, v) {
		$('input[name="item['+v+'][qty]"], input[name="item['+v+'][price_unit]"]').on("change", function() {
			price_unit = $('input[name="item['+v+'][price_unit]"]').val(),
			qty = $('input[name="item['+v+'][qty]"]').val();
			price_unit = parseFloat(price_unit);
			qty = parseFloat(qty);
			$('input[name="item['+v+'][price_unit]"]').val( (price_unit).toFixed(2) );
			$('input[name="item['+v+'][amount]"]').val( (price_unit*qty).toFixed(2) );
			var amount = $('input[name="item['+v+'][amount]"]').val();
			let id = $('input[name="item['+v+'][log_id]"]').val();

			cal();
			var log_join_id = $('input[name="item['+v+'][id]"]').val();
			item_edit(log_join_id,'qty',qty);
			item_edit(log_join_id,'amount',amount);
			item_edit(log_join_id,'amount_unit',price_unit);
		});
	});


	$('.class_select_checkbox').click(function(){
		is_check = $(this).prop('checked');

		select_class_period = $(this).closest('tr').find('.class_period').val();

		$('.class_select_checkbox').each(function(index,row){
			this_class_period = $(row).closest('tr').find('.class_period').val();

			if(this_class_period == select_class_period)
			{
				if(is_check == true)
					$(row).prop('checked','checked');
				else
					$(row).prop('checked','');
			}

		});

		update_material_fee();
	});

	reinit_item_selected();
	cal();

});

function reinit_item_selected()
{
	$('.item-selected').on('click', function() {
		update_material_fee();
		cal()
	});
}

function update_material_fee()
{
	let next = false;
	const urlParams = new URLSearchParams(window.location.search);
	if (urlParams.has('next')) {
		next = true;
	}
	
	material_period_list = {};
	childcare_period_list = {};
	transport_period_list = {};
	var all_period_material = 0;
	var material_fee = 0;
	$('.class_select_checkbox').each(function(index,row){
		is_check = $(row).prop('checked');

		if(is_check == true)
		{
			period = $(row).closest('tr').find('.class_period').val();
			transport_fee = $(row).closest('tr').find('.transport_fee').val();
			childcare_fee = $(row).closest('tr').find('.childcare_fee').val();
			var type = $(row).closest('tr').find('.class_type').val();
			childcare_period_list[period] = childcare_fee;
			transport_period_list[period] = transport_fee;

			if(type == 'class') {
				if(period in material_period_list) {
					class_count++;
				} else {
					class_count = 1;
				}

				material_period_list[period] = String(class_count);
			}
		}
	});

	for(period in material_period_list) {
		for(class_bundle_price of class_bundle_price_list) {
			if(class_bundle_price['qty'] === material_period_list[period]) {
				all_period_material += parseFloat(class_bundle_price['material']);
			}
		}
	}

	if(all_period_material > 0) {
		material_fee = all_period_material;
	}

	if(next) material_fee *= 2;
	$('.material_fee_txt').val(material_fee);

	transport_fee = 0;
	$.each(transport_period_list,function(index,row){
		transport_fee = transport_fee + parseFloat(row);
	});
	if(next) transport_fee *= 2;
	$('.transport_fee_text').val(transport_fee);

	childcare_fee = 0;
	$.each(childcare_period_list,function(index,row){
		childcare_fee = childcare_fee + parseFloat(row);
	});
	if(next) childcare_fee *= 2;
	$('.childcare_fee_text').val(childcare_fee);


	cal();
}



function price_format(v) {
	return numeral(v).format('0,0.00');
}


function cal_change(){
	total_amount = 	parseFloat($(document).find('[data-label="total"]').text());
	receive = parseFloat($('[name="receive"]').val());

	change = receive - total_amount;
	$('[name="change"]').val( price_format(change));
}
function cal() {
	class_count = 0;

	// input
	var subtotal = $('[name="subtotal"]').val(),
		discount = 0,
		all_period_discount = 0,
		discount_type = $('[name="discount_type"]').val(),
		adjust = $('[name="adjust"]').val(),
		material_fee = $('[name="material_fee"]').val(),
		childcare_fee = $('[name="childcare_fee"]').val(),
		transport_fee = $('[name="transport_fee"]').val(),
		adjust_label = $('[name="adjust_label"]').val(),
		tax = $('[name="tax"]').val(),
		total = $('[name="total"]').val();

	// initialize value for the elements
	if(subtotal == '') subtotal = 0;
	if(adjust == '') adjust = 0;
	if(material_fee == '') material_fee = 0;
	if(childcare_fee == '') childcare_fee = 0;
	if(transport_fee == '') transport_fee = 0;

	// set value of subtotal when there is an item being added
	var subtotal = 0;
	$.each(record, function(k, v) {
		if ($('input[name="unpaid['+v+'][selected]"]').is(':checked'))
		{
			if ($('input[name="unpaid['+v+'][amount]"]').val().length > 0) {
				subtotal += parseFloat($('input[name="unpaid['+v+'][amount]"]').val())
			}
			if ($('input[name="unpaid['+v+'][dis_amount]"]').val().length > 0) {
				discount_type = '$'
			}
		}
		if ($('input[name="item['+v+'][selected]"]').is(':checked'))
		{
			if ($('input[name="item['+v+'][amount]"]').val().length > 0) {
				subtotal += parseFloat($('input[name="item['+v+'][amount]"]').val())
			}
			if ($('input[name="item['+v+'][dis_amount]"]').val().length > 0) {
				discount_type = '$'
			}
		}
		$('#subtotal').text(price_format(subtotal));
		$('[name="subtotal"]').val(subtotal);
		$('[data-label="total"]').text(price_format(subtotal));
	});

	//count subsidy
	discount_period_list = {};
	$('.class_select_checkbox').each(function(index,row){
		is_check = $(row).prop('checked');

		if(is_check == true)
		{
			var period = $(row).closest('tr').find('.class_period').val();
			var type = $(row).closest('tr').find('.class_type').val();

			if(type == 'class') {
				if(period in discount_period_list) {
					class_count++;
				} else {
					class_count = 1;
				}

				discount_period_list[period] = String(class_count);
			}
		}
	});

	for(period in discount_period_list) {
		for(class_bundle_price of class_bundle_price_list) {
			if(class_bundle_price['qty'] === discount_period_list[period]) {
				all_period_discount += parseFloat(class_bundle_price['amount']);
			}

			if(class_bundle_price['subsidy'] > 0 && class_bundle_price['qty'] <= discount_period_list[period]) {
				all_period_discount -= parseFloat(class_bundle_price['subsidy']);
			}
		}
	}

	if(all_period_discount > 0) {
		discount += subtotal - all_period_discount;
	}

	$('[name="discount_type"]').val(discount_type)

	// parsing number input
	subtotal = parseFloat(subtotal);
	discount = parseFloat(discount).toFixed(2);
	adjust = parseFloat(adjust);
	material_fee = parseFloat(material_fee);
	transport_fee = parseFloat(transport_fee);
	childcare_fee = parseFloat(childcare_fee);

	// display the input value
	if (discount_type == '$'){
		$('[data-label="discount"]').text( price_format(discount) );
		$('[name="discount"]').val(discount);
	}else {
		$('[data-label="discount"]').text( price_format(subtotal*discount/100) );
		$('[name="discount"]').val(discount);
	}
	$('[data-label="adjust"]').text( price_format(adjust) )
	$('[data-label="material_fee"]').text( price_format(material_fee) );
	$('[data-label="transport_fee"]').text( price_format(transport_fee) );
	$('[data-label="childcare_fee"]').text( price_format(childcare_fee) );

	// display the total amount
	if (discount_type == '$'){

		total = (subtotal-discount+adjust+material_fee+childcare_fee+transport_fee);
	}
	else{
		total = (subtotal-(subtotal*discount/100)+adjust+material_fee+childcare_fee+transport_fee);
	}

	var tax_amount = total * $('[name="tax-percentage"]').val() / 100;
	$('[name="tax"]').val(tax_amount);
	$('[data-label="tax"]').text( price_format(tax_amount));

	total = total + tax_amount;
	$('[data-label="total"]').text( price_format(total));
	$('[name="total"]').val(total);
	$('[name="price-amount"]').val(total);

	cal_change();
}

function row_del(k) {
	$('.item'+k).remove();
	record.splice(record.indexOf(k.toString()), 1);
	if(record.length == 0) { $('#subtotal').text('0.00'); }
	cal();
};

$('[name="discount"], [name="discount_type"], [name="adjust"],[name="material_fee"],[name="childcare_fee"],[name="transport_fee"]').on("change", function() {
	cal();
});

$('[name="receive"]').on("change", function() {
	cal_change();
});

// modal
$("#modal-add").on("show.bs.modal", function (event) {
	var Modal = $(this);
	Modal.find(".custom-control-input").attr("checked", false);
});

$("#modal-add").find('[name="type"]').on("change", function() {
	$(".section-dynamic").addClass("d-none");
	$(".section-"+$(this).val()).removeClass("d-none");
	$("#modal-add").find('.modal-footer').find('button[name="add"]').val($(this).val());

	if (($(".section-item").hasClass("d-none"))) {
		$("#modal-add").find('[name="item"]').val(null).trigger('change');
		$("#modal-add").find('[name="item-qty"]').val('1');
		$("#modal-add").find('[name="item-price_unit"]').val('');
		$("#modal-add").find('[name="item-amount"]').val('');
	} else {
		$("#modal-add").find('[name="class"]').val(null).trigger('change');
		$("#modal-add").find('[name="class-qty"]').val('1');
		$("#modal-add").find('[name="class-price_unit"]').val('');
		$("#modal-add").find('[name="class-amount"]').val('');
	}

});

function clearForm() {
	$("#modal-add").find('[name="item"]').val(null).trigger('change');
	$("#modal-add").find('[name="item-qty"]').val('1');
	$("#modal-add").find('[name="item-price_unit"]').val('');
	$("#modal-add").find('[name="item-amount"]').val('');
	$("#modal-add").find('[name="class"]').val(null).trigger('change');
	$("#modal-add").find('[name="class-qty"]').val('1');
	$("#modal-add").find('[name="class-price_unit"]').val('');
	$("#modal-add").find('[name="class-amount"]').val('');
	$("#modal-add").find('[name="type"]').prop('checked', false);
	$(".section-class").addClass("d-none");
	$(".section-item").addClass("d-none");
}


$('.close').on("click", function() {
	clearForm();
});

$("#modal-add").find('[name="item"], [name="class"]').on("change", function() {
	if($(this).val() != "") {
		Loading(1);
		var type = $(this).attr("name");
		var controller = type == "class" ? "classes" : "items";
		$.ajax({
			url: base_url+controller+"/json_view/"+$(this).val(),
			type: "POST",
			dataType: "json",

			success: function(data) {
				Loading(0);

				if(data.status == "ok") {
					var ModalThis = $("#modal-add"), result = data.result[0];
					if(type == "class") {
						ModalThis.find('[name="'+type+'-price_unit"]').val( parseFloat(result.fee).toFixed(2) );
						ModalThis.find('[name="'+type+'-amount"]').val( parseFloat(result.fee).toFixed(2) );
					} else if(type == "item"){
						ModalThis.find('[name="'+type+'-price_unit"]').val( parseFloat(result.price_sale).toFixed(2) );
						ModalThis.find('[name="'+type+'-amount"]').val( parseFloat(result.price_sale).toFixed(2) );
					}
					$(`input[name="title"]`).val(result.title);
				} else {
					alert("Data not found");
				}
			}
		});
	}
});

$("#modal-add").find('[name="class-qty"], [name="class-price_unit"], [name="item-qty"], [name="item-price_unit"]').on("change", function() {

	var Modal = $("#modal-add");
	if($(this).attr("name").indexOf("class") >= 0) {
		price_unit = Modal.find('[name="class-price_unit"]').val(),
		qty = Modal.find('[name="class-qty"]').val();
	} else {
		price_unit = Modal.find('[name="item-price_unit"]').val(),
		qty = Modal.find('[name="item-qty"]').val();
	}

	price_unit = parseFloat(price_unit);
	qty = parseFloat(qty);

	if($(this).attr("name").indexOf("class") >= 0) {
		Modal.find('[name="class-price_unit"]').val( parseFloat(price_unit).toFixed(2) );
		Modal.find('[name="class-amount"]').val( parseFloat(price_unit*qty).toFixed(2) );
	} else {
		Modal.find('[name="item-price_unit"]').val( parseFloat(price_unit).toFixed(2) );
		Modal.find('[name="item-amount"]').val( parseFloat(price_unit*qty).toFixed(2) );
	}

});

var guid = () => {
    // let s4 = () => {
        return Math.floor((1 + Math.random()) * 0x10000)
            .toString(16)
            .substring(1);
    // }
    //return id of format 'aaaaaaaa'-'aaaa'-'aaaa'-'aaaa'-'aaaaaaaaaaaa'
    // return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
}

$("#modal-add").find('[name="add"]').click(function(event) {

	Loading(1);
	var type = $(this).val();
	var period = null;

	if (type == "class") {
		period = $("#modal-add").find('[name="period"]').val();
	}

	var isEmpty = 0;

	$(".section-"+type).find('[data-required="true"]').each(function (k, v) {
		if ($(v).val() == "") {
			isEmpty++;
		}
	});

	if(isEmpty > 0){

		$('.alert').removeClass('d-none');

	} else {

		// Tan Jing Suan
		// if(type == 'package') {
		// 	$.ajax({
		// 		url: base_url + "package/json_view/" + $('select[name="'+type+'"]').val(),
		// 		type: "post",
		// 		dataType: 'json',
		// 		async: false,
		// 		success: function(data) {
		// 			$.each(data.result, function(k, v) {
		// 				var i = guid();
		// 				title = v.title;
		// 				id = v.pid;

		// 				var html = `
		// 				<tr class="item`+i+`">
		// 					<td>
		// 						<div class="custom-control custom-checkbox mt-1">
		// 							<input type="checkbox" class="custom-control-input item-selected" id="checkbox-`+i+`" name="item[`+i+`][selected]" value="1">
		// 							<label class="custom-control-label" for="checkbox-`+i+`"></label>
		// 						</div>
		// 					</td>
		// 					<td>
		// 						<input type="hidden" name="user" value="`+$('input[name="user"]').val()+`">
		// 						<input type="hidden" name="item[`+i+`][id]" value="`+id+`">
		// 						<input type="hidden" name="item[`+i+`][type]" value="item">
		// 						<input type="hidden" name="item[`+i+`][period]" value="`+period+`">
		// 						<input type="hidden" name="item[`+i+`][item]" value="`+id+`">
		// 						<input type="hidden" name="item[`+i+`][dis_amount]" value="0">
		// 						<input type="text" onclick="this.select();" class="form-control" name="item[`+i+`][title]" value="`+title+`" readonly>
		// 						<textarea class="form-control mt-2" onclick="this.select();" name="item[`+i+`][remark]" rows="1"></textarea>
		// 						<div class="mt-2">
		// 							<a href="javascript:;" onclick="row_del(`+i+`)" class="text-danger"><i class="fa fa-fw fa-trash"></i> Remove</a>
		// 						</div>
		// 					</td>
		// 					<td><input type="number" onclick="this.select();" name="item[`+i+`][qty]" class="input-remove_arrow form-control text-right" value="1" required></td>
		// 					<td><input type="number" step="0.01" onclick="this.select();" name="item[`+i+`][price_unit]" class="input-remove_arrow form-control text-right" value="`+ parseFloat(v.price_sale).toFixed(2) +`" required></td>
		// 					<td><input type="number" step="0.01" onclick="this.select();" name="item[`+i+`][amount]" class="input-remove_arrow form-control text-right" value="`+ parseFloat(v.price_sale).toFixed(2) +`" readonly></td>
		// 				</tr>
		// 				`;

		// 				$('#item-table').append(html);
		// 				record.push(i);
		// 			})

		// 		}
		// 	});

		// } else {
		// 	var i = Date.now();
		// 	var controller = type == "class" ? "classes" : "items";
		// 	$.ajax({
		// 		url: base_url + controller + "/json_view/" + $('select[name="'+type+'"]').val(),
		// 		type: "post",
		// 		dataType: 'json',
		// 		async: false,
		// 		success: function(data) {

		// 			if(period != null){
		// 				title = "["+period+"] "+data.result[0].title;
		// 			} else {
		// 				title = data.result[0].title;
		// 			}
		// 			id = data.result[0].pid;

		// 			var html = `
		// 			<tr class="item`+i+`">
		// 				<td>
		// 					<div class="custom-control custom-checkbox mt-1">
		// 						<input type="checkbox" class="custom-control-input item-selected " id="checkbox-`+i+`" name="item[`+i+`][selected]">
		// 						<label class="custom-control-label" for="checkbox-`+i+`"></label>
		// 					</div>
		// 				</td>
		// 				<td>
		// 					<input type="hidden" name="user" value="`+$('input[name="user"]').val()+`">
		// 					<input type="hidden" name="item[`+i+`][id]" value="`+id+`">
		// 					<input type="hidden" name="item[`+i+`][type]" value="`+type+`">
		// 					<input type="hidden" name="item[`+i+`][period]" value="`+period+`">
		// 					<input type="hidden" name="item[`+i+`][item]" value="`+id+`">
		// 					<input type="hidden" name="item[`+i+`][dis_amount]" value="0">
							
		// 					<input type="text" onclick="this.select();" class="form-control" name="item[`+i+`][title]" value="`+title+`" readonly>
		// 					<textarea class="form-control mt-2" onclick="this.select();" name="item[`+i+`][remark]" rows="1">`+$('[name="'+type+'-remark"]').val()+`</textarea>
		// 					<div class="mt-2">
		// 						<a href="javascript:;" onclick="row_del(`+i+`)" class="text-danger"><i class="fa fa-fw fa-trash"></i> Remove</a>
		// 					</div>
		// 				</td>
		// 				<td><input type="number" onclick="this.select();" name="item[`+i+`][qty]" class="input-remove_arrow form-control text-right" value="`+ $('input[name="'+type+'-qty"]').val() +`" required></td>
		// 				<td><input type="number" step="0.01" onclick="this.select();" name="item[`+i+`][price_unit]" class="input-remove_arrow form-control text-right" value="`+ $('input[name="'+type+'-price_unit"]').val() +`" required></td>
		// 				<td><input type="number" step="0.01" onclick="this.select();" name="item[`+i+`][amount]" class="input-remove_arrow form-control text-right" value="`+ $('input[name="'+type+'-amount"]').val() +`" readonly></td>
		// 			</tr>
		// 			`;

		// 			$('#item-table').find("tbody").append(html);
		// 			record.push(i);
		// 		}
		// 	});

		// }
		var i = $('#item-table >tbody >tr').length + 1;
		var url = base_url + "payment/json_additem/" + $('input[name="user"]').val() + 
			"?type=" + type +
			"&classpid=" + $('select[name="class"]').val() +
			"&classqty=" + $('input[name="class-qty"]').val() +
			"&classamount=" + $('input[name="class-amount"]').val() + 
			"&classprice_unit=" + $('input[name="class-price_unit"]').val() + 
			"&classremark=" + $('textarea[name="class-remark"]').val() +
			"&itempid=" + $('select[name="item"]').val() +
			"&itemqty=" + $('input[name="item-qty"]').val() +
			"&itemamount=" + $('input[name="item-amount"]').val() + 
			"&itemprice_unit=" + $('input[name="item-price_unit"]').val() + 
			"&itemremark=" + $('textarea[name="item-remark"]').val() + 
			"&packagepid=" + $('select[name="package"]').val();
		var log_join_id = "";
		var id = "";
		var remark = "";
		var itemname = "";
		var qty = 0;
		var price_unit = 0.00;
		var amount = 0.00;
		$.ajax({
			url: url,
			type: "post",
			dataType: 'json',
			async: false,
			success: function(data) {
				// id = data.result[0].pid;
				// qty = $('input[name="'+type+'-qty"]').val();
				// price_unit = $('input[name="'+type+'-price_unit"]').val();
				// amount = $('input[name="'+type+'-amount"]').val();
				$.each(data.result, function(k, v) {
					log_join_id = v.id;
					if(period != null) {
						title = "["+period+"] "+v.title;
					} else {
						title = v.title;
					}
					qty = v.qty;
					price_unit = v.amount_unit;
					amount = v.amount;
					switch(type) {
						case "package":
							// id = v.secondary;
							id = v.item;
							itemname = "item";
							break;
						case "class":
							id = v.class;
							remark = v.remark;
							itemname = "item";
							break;
						case "item":
							id = v.item;
							remark = v.remark;
							itemname = "unpaid";
							break;
						default:
							break;
					}
					var html = `
					<tr class="item`+i+`">
						<td>
							<div class="custom-control custom-checkbox mt-1">
								<input type="checkbox" class="custom-control-input item-selected " id="checkbox-`+i+`" name="item[`+i+`][selected]">
								<label class="custom-control-label" for="checkbox-`+i+`"></label>
							</div>
						</td>
						<td>
							<input type="hidden" name="user" value="`+$('input[name="user"]').val()+`">
							<input type="hidden" name="`+itemname+`[`+i+`][id]" value="`+log_join_id+`">
							<input type="hidden" name="`+itemname+`[`+i+`][type]" value="`+type+`">
							<input type="hidden" name="`+itemname+`[`+i+`][period]" value="`+period+`">
							<input type="hidden" name="`+itemname+`[`+i+`][item]" value="`+id+`">
							<input type="hidden" name="`+itemname+`[`+i+`][dis_amount]" value="0">
							
							<input type="text" onclick="this.select();" class="form-control" name="`+itemname+`[`+i+`][title]" value="`+title+`" readonly>
							<textarea class="form-control mt-2" onclick="this.select();" name="`+itemname+`[`+i+`][remark]"  
								onchange="item_edit(
	                            `+log_join_id+`,
	                            'remark',
	                            this.value)"
								rows="1">`+remark+`</textarea>
							<div class="mt-2">
								<a href="javascript:;" onclick="item_del(`+log_join_id+`)" class="text-danger"><i class="fa fa-fw fa-trash"></i> Remove</a>
							</div>
						</td>
						<td><input type="number" onclick="this.select();" name="`+itemname+`[`+i+`][qty]"
							class="input-remove_arrow form-control text-right" value="`+ qty +`" required></td>
						<td><input type="number" step="0.01" onclick="this.select();" name="`+itemname+`[`+i+`][price_unit]" 
							class="input-remove_arrow form-control text-right" value="`+ parseFloat(price_unit).toFixed(2) +`" required></td>
						<td><input type="number" step="0.01" onclick="this.select();" name="`+itemname+`[`+i+`][amount]"
							class="input-remove_arrow form-control text-right" value="`+ parseFloat(amount).toFixed(2) +`" readonly></td>
					</tr>
					`;
					$('#item-table').find("tbody").append(html);
					record.push(i);
				})
			},
			error: function(err) {
				console.log(err);
			}
		});
		if ( log_join_id == "" ) {
			return;
		}
		$('input[name="'+itemname+'['+i+'][qty]"], input[name="'+itemname+'['+i+'][price_unit]"]').on("change", function() {
			price_unit = $('input[name="'+itemname+'['+i+'][price_unit]"]').val(),
			qty = $('input[name="'+itemname+'['+i+'][qty]"]').val();
			price_unit = parseFloat(price_unit);
			qty = parseFloat(qty);
			$('input[name="'+itemname+'['+i+'][price_unit]"]').val( (price_unit).toFixed(2) );
			$('input[name="'+itemname+'['+i+'][amount]"]').val( (price_unit*qty).toFixed(2) );
			var amount = $('input[name="'+itemname+'['+i+'][amount]"]').val();
			cal();
			item_edit(log_join_id,'qty',qty);
			item_edit(log_join_id,'amount',amount);
			item_edit(log_join_id,'amount_unit',price_unit);
		});

		reinit_item_selected();
		cal();
		clearForm();
		Loading(0);

		//reset modal
		var Modal = $("#modal-add");

		Modal.modal("hide");
		$.each(Modal.find(".form-control"), function(k, v) {
			var reset_v = $(k).attr("data-reset") == undefined ? "" : $(k).attr("data-reset") ;
			$(k).val( reset_v );
		});

	}

});

$("#checkAll").click(function(){
    $('input.item-selected:checkbox').not(this).prop('checked', this.checked);
});

// function add_next_month(id) {
	// let next = document.getElementById('next_month');
	// if(next === null) return;
	
	// $.ajax({
		// url: base_url+"payment/json_add/"+id,
		// type: "GET",

		// success: function(data) {
			// let res = JSON.parse(data)['result'];
			
			// let total_default_fee = 0;
			// let total_default_discount = 0;

			// let html = '';
			
			// let i = 0;
			// for (const key in res) {
				// let e = res[key];
				
				// for (const key2 in e['data']) {
					// let e2 = e[key2];
					// i++;
					
					// if(e2['amount'] !== null) e2['amount'] = 0;
					// if(e2['discount'] !== null) e2['discount'] = 0;
					
					// $total_default_fee += ((int)$e2['amount']);
					// $total_default_discount += ((int)$e2['discount']);
				// }
				
			// }
			
			// foreach ($new_default_unpaid_class as $k => $e)
			// {
				// foreach ($e['data'] as $e2)
				// {
					// $i++;
					//verbose($e2);
					// if(!isset($e2['amount'])) $e2['amount'] = 0;
					// if(!isset($e2['discount'])) $e2['discount'] = 0;
					
					// $total_default_fee += ((int)$e2['amount']);
					// $total_default_discount += ((int)$e2['discount']);
					// ?>
					// <tr>
						// <td><?php echo $i; ?></td>
						// <td><?php echo $e2['title']; ?></td>
						// <td class="text-right"><?php echo number_format($e2['amount'], 2, '.', ',');; ?></td>
					// </tr>
					// <?php
				// }
			// }
			
			// next.outerHTML = html;
			
			// Loading(0)
		// }
	// });
	
// }

// Tan Jing Suan
function item_edit(id,key,value) {
	url = base_url + "payment/json_edititem/" + id + "?key=" + key + "&value=" + value;
	$.ajax({
		url: url,
		type: "post",
		dataType: 'json',
		async: false,
		success: function(data) {
			
		},
		error: function(err) {
			console.log(err);
		}
	});
}

function item_del(k,id) {
	// console.log( $('.item'+k).find('[name="user"]').val() );
	// console.log( $('.item'+k).find('[name="item['+k+'][id]"]').val() );
	// console.log( $('.item'+k).find('[name="item['+k+'][item]"]').val() );
	// console.log( $('.item'+k).find('[name="item['+k+'][type]"]').val() );
	// url = base_url + "payment/json_delitem/" + 
	// 		$('.item'+k).find('[name="item['+k+'][id]"]').val() + 
	// 		"?type=" + $('.item'+k).find('[name="item['+k+'][type]"]').val() + 
	// 		"&item=" + $('.item'+k).find('[name="item['+k+'][item]"]').val();
	url = base_url + "payment/json_delitem/" + id;
	$.ajax({
		url: url,
		type: "post",
		dataType: 'json',
		async: false,
		success: function(data) {
			$('.item'+k).remove();
			record.splice(record.indexOf(k.toString()), 1);
			if(record.length == 0) { $('#subtotal').text('0.00'); }
			cal();
		},
		error: function(err) {
			console.log(err);
		}
	});
};

function item_cal(k,id,type) {
	price_unit = $('input[name="'+type+'['+k+'][price_unit]"]').val(),
	qty = $('input[name="'+type+'['+k+'][qty]"]').val();
	price_unit = parseFloat(price_unit);
	qty = parseFloat(qty);
	$('input[name="'+type+'['+k+'][price_unit]"]').val( (price_unit).toFixed(2) );
	$('input[name="'+type+'['+k+'][amount]"]').val( (price_unit*qty).toFixed(2) );
	var amount = $('input[name="'+type+'['+k+'][amount]"]').val();
	item_edit(id,"amount",amount);
	cal();
}

function autosave_edit(pid,key,value) {
	url = base_url + "payment/json_autosave/" + pid + "?key=" + key + "&value=" + value;
	console.log(url);
	$.ajax({
		url: url,
		type: "post",
		dataType: 'json',
		async: false,
		success: function(data) {
			
		},
		error: function(err) {
			console.log(err);
		}
	});
}