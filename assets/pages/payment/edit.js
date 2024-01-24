function show_category() {
	$('.category-sec').removeClass('d-none')
	$('.category').trigger('change')
}

$("#modal-add").on("hide.bs.modal", function (event) {
	$('.category-sec').addClass('d-none')
	$("#modal-add table tbody").html('')
	append_row()
	$.ajax({
		url: base_url+"payment/json_item_list",
		type: "POST",
		dataType: "json",
		
		success: function(data) {
			$('[name="item[]"]').empty() 
			$('[name="item[]"]').select2({
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
			$('[name="item[]"]').empty() 
			$('[name="item[]"]').select2({
				data: data.result
			})
			init('select2')
			Loading(0)
		}
	});
})

function append_row() {
	
	let Id = Date.now(),
		Html = `
		<tr id="`+Id+`">
			<td>
				<select class="form-control select2" name="item[]" required>
					<option value="">-</option>
					`;
					
	$.each(JSON.parse(items_options), function(k, v) {
		Html += `<optgroup label="`+k+`">`;
		$.each(v, function(k2, v2) {
			$.each(v2, function(k3, v3) {
				Html += `<option value="`+v3.pid+`">`+v3.title+`</option>`;
			})
		})
		Html += `</optgroup>`;
	})
	
	Html +=		`</select>
			</td>
			<td>
				<input type="number" class="form-control" name="qty[]" value="1" required>
			</td>
			<td class="align-middle">
				<a href="javascript:;" onclick="del_row(`+Id+`)" class="text-danger"><i class="fa fa-fw fa-times-circle"></i></a>
			</td>
		</tr>
	`;
	
	$('#modal-add table tbody').append(Html)
	
	let Value = $('.category').val()
		
	if(!$('.category-sec').hasClass('d-none')) {
		Loading(1)
		$.ajax({
			url: base_url+"payment/json_filter_item/"+Value,
			type: "POST",
			dataType: "json",
			
			success: function(data) {
				$('#'+Id+' [name="item[]"]').empty() 
				$('#'+Id+' [name="item[]"]').select2({
					data: data.result
				})
				init('select2')
				Loading(0)
			}
		});
	}
	
	init('select2')
	
}

function del_row(Id) {
	
	if($('#modal-add table tbody tr').length > 1) {
		$('#'+Id).remove()
	}
	
}

$('[data-label="return_payment"]').click(function(){
	window.location.href = base_url+'payment/list';
});

function del_ask(id) {
	swal({
		text: "Are you sure want to delete this payment?",
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
				url: base_url+"payment/json_del/"+id,
				type: "POST",
				dataType: "json",
				
				success: function(data) {
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						window.location.href = base_url+"/payment/list";
					});
				}
			});
		}
	});
}

$(document).on("keypress", 'input', function (e) {
    var code = e.keyCode || e.which;
    if (code == 13) {
        e.preventDefault();
        return false;
    }
});

var record = [];
var removedList = [];

$(document).ready(function() {
	
	$("tbody > tr").each(function() {
		record.push(this.className.replace('item', ''));
	});

	$.each(record, function(k, v) {
		$('input[name="old['+v+'][qty]"], input[name="old['+v+'][price_unit]"]').on("change", function() {
			price_unit = $('input[name="old['+v+'][price_unit]"]').val(),
			qty = $('input[name="old['+v+'][qty]"]').val();
			price_unit = parseFloat(price_unit);
			qty = parseFloat(qty);
			$('input[name="old['+v+'][price_unit]"]').val( (price_unit).toFixed(2) );
			$('input[name="old['+v+'][amount]"]').val( (price_unit*qty).toFixed(2) );
			var amount = $('input[name="old['+v+'][amount]"]').val();
			let id = $('input[name="old['+v+'][log_id]"]').val();

			cal();
		});
	});
	cal();

});

function price_format(v) {
	return numeral(v).format('0,0.00');
}

function cal() {
	// input
	var subtotal = $('[name="subtotal"]').val(),
		discount = $('[name="discount"]').val(),
		material_fee = $('[name="material_fee"]').val(),
		childcare_fee = $('[name="childcare_fee"]').val(),
		transport_fee = $('[name="transport_fee"]').val(),
		discount_type = $('[name="discount_type"]').val(),
		adjust = $('[name="adjust"]').val(),
		adjust_label = $('[name="adjust_label"]').val(),
		tax = $('[name="tax"]').val(),
		total = $('[name="total"]').val();

	// initialize value for the elements
	if(subtotal == '') subtotal = 0;
	if(material_fee == '') material_fee = 0;
	if(discount == '') discount = 0;
	if(childcare_fee == '') childcare_fee = 0;
	if(transport_fee == '') transport_fee = 0;
	if(adjust == '') adjust = 0;
	
	// set value of subtotal when there is an item being added
	var subtotal = 0;
	$.each(record, function(k, v) {
		if ($('input[name="old['+v+'][amount]"]').val() != null) {
			subtotal += parseFloat($('input[name="old['+v+'][amount]"]').val())
		}
		if ($('input[name="item['+v+'][amount]"]').val() != null) {
			subtotal += parseFloat($('input[name="item['+v+'][amount]"]').val())
		}
		$('#subtotal').text(price_format(subtotal));
		$('[name="subtotal"]').val(subtotal);
		$('[data-label="total"]').text(price_format(subtotal));	
	});

	// parsing number input
	subtotal = parseFloat(subtotal);
	material_fee = parseFloat(material_fee);
	discount = parseFloat(discount);
	childcare_fee = parseFloat(childcare_fee);
	transport_fee = parseFloat(transport_fee);
	adjust = parseFloat(adjust);

	// display the input value
	if (discount_type == '$'){
		$('[data-label="discount"]').text( price_format(discount) );
		$('[name="discount"]').val(discount);
	}else {
		$('[data-label="discount"]').text( price_format(subtotal*discount/100) );
		$('[name="discount"]').val(discount);
	}
	$('[data-label="material_fee"]').text( price_format(material_fee) );
	$('[data-label="adjust"]').text( price_format(adjust) );
			
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
}

function row_del(k) {
	var old = $('.item'+k).find('[name="old['+k+'][log_id]"]').val();
	removedList.push(old);
	$('[name="removedList"]').val(removedList);
	$('.item'+k).remove();
	record.splice(record.indexOf(k.toString()), 1);
	if(record.length == 0) { $('#subtotal').text('0.00'); }
	cal();
};

$('[name="discount"], [name="discount_type"], [name="adjust"]').on("change", function() {
	cal();
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

$("#modal-add").find("select").on("change", function() {
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
				} else {
					ModalThis.find('[name="'+type+'-price_unit"]').val( parseFloat(result.price_sale).toFixed(2) );
					ModalThis.find('[name="'+type+'-amount"]').val( parseFloat(result.price_sale).toFixed(2) );
				}
				$(`input[name="title"]`).val(result.title);
			} else {
				alert("Data not found");
			}
		}
	});
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

		var i = Date.now();
		var controller = type == "class" ? "classes" : "items";

		$.ajax({
			url: base_url + controller + "/json_view/" + $('select[name="'+type+'"]').val(),
			type: "post",	
			dataType: 'json',
			async: false,
			success: function(data) {
				
				if(period != null){
					title = "["+period+"] "+data.result[0].title;
				} else {
					title = data.result[0].title;
				}
				id = data.result[0].pid;
				
				var html = `
				<tr class="item`+i+`">
					<td>
						<input type="hidden" name="item[`+i+`][type]" value="`+type+`">
						<input type="hidden" name="item[`+i+`][period]" value="`+period+`">
						<input type="hidden" name="item[`+i+`][item]" value="`+id+`">
						<input type="text" onclick="this.select();" class="form-control" name="item[`+i+`][title]" value="`+title+`" readonly>
						<textarea class="form-control mt-2" onclick="this.select();" name="item[`+i+`][remark]" rows="2">`+$('[name="'+type+'-remark"]').val()+`</textarea>
						<div class="mt-2">
							<a href="javascript:;" onclick="row_del(`+i+`)" class="text-danger"><i class="fa fa-fw fa-trash"></i> Remove</a>
						</div>
					</td>
					<td><input type="number" onclick="this.select();" name="item[`+i+`][qty]" class="input-remove_arrow form-control text-right" value="`+ $('input[name="'+type+'-qty"]').val() +`" required></td>
					<td><input type="number" step="0.01" onclick="this.select();" name="item[`+i+`][price_unit]" class="input-remove_arrow form-control text-right" value="`+ $('input[name="'+type+'-price_unit"]').val()  +`" required></td>
					<td><input type="number" step="0.01" onclick="this.select();" name="item[`+i+`][amount]" class="input-remove_arrow form-control text-right" value="`+ $('input[name="'+type+'-amount"]').val() +`" readonly></td>
				</tr>
				`;

				$("tbody").append(html);
				record.push(i);
			}
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
			});
		});
		
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

if( access_denied != undefined ) {
	$('.form-control, [name="save"], [data-target="#modal-add"]').attr("disabled", true);
	$('.select2').select2({ 'disabled': true });
}