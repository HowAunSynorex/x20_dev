function googleTranslateElementInit() {
	new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
}

function load_class(course = '')
{
	$.each($('#class-table > tbody > tr'), function (){
		
		if ($(this).attr('id') == 'class-'+course)
		{
			$(this).show();
		}
		else
		{
			$(this).hide();
			$(this).find('.class-check').prop('checked', false);
		}
	});
	cal_class();
}

function cal_class()
{
	var count = 0;
	var course = $('#course').val();
	var total_tuition = 0;
	var total_material = 0;
	var total_discount = 0;
	var total_transport = 0;
	var total_childcare = 0;
	var total = 0;
	
	$.each($('.class-check:checked'), function (){
		count +=1;
		total_tuition += parseFloat($(this).closest('tr').find('.class-fee').text());
	});
	
	total_childcare = $('#childcare').find(':selected').data('price');
	total_transport = $('#transport').find(':selected').data('price');
	
	if (count > 0){
		$.ajax({
		url: base_url+"landing/json_discount/"+course+"/"+count,
		'async': false,
		type: "POST",
		'global': false,
		dataType: "json",		
		success: function(data) {
				if(data.price == "NULL"){
					total_discount = 0;
				}else{
					total_discount = total_tuition-data.price+parseInt(data.subsidy);			
				}
				total_material  = parseInt(data.material)
			}
		});
	}
	
	total = total_tuition+total_childcare+total_transport+total_material-total_discount;
	
	$('#class-total').text(total_tuition);
	$('#total-tuition').text(total_tuition);
	$('#total-material').text(total_material);
	$('#total-transportation').text(total_transport);
	$('#total-discount').text('('+total_discount+')');
	$('#total-childcare').text(total_childcare);
	$('#total').text(total)
}

load_class();

function ic_check(v) {
    var result = v.split("-");
    
    var gender = v.at(-1);
    if(
        gender == 2 || 
        gender == 4 || 
        gender == 6 || 
        gender == 8 || 
        gender == 0
    ) {
        $("#radio-gender-male").prop("checked", false);
        $("#radio-gender-female").prop("checked", true);
    } else {
        $("#radio-gender-male").prop("checked", true);
        $("#radio-gender-female").prop("checked", false);
    }
    
    var yy = result[0].slice(0, 2), mm = result[0].substring(2).slice(0, -2), dd = result[0].substring(4);
    if(yy>30) {
        yy = 19+""+yy;
    } else {
        yy = 20+""+yy;
    }
    console.log(dd);
    
    $('[name="birthday"]').val( yy+"-"+mm+"-"+dd );
}