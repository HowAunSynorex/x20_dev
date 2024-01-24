$('.receipt-switch').on('change', function() {
	let Modal = $('#modal-apply'),
		Language = $('[name="language"]:checked').val()
		
	if($(this).prop('checked')) {
		if(Language == 'english') {
			Modal.find('a').attr('href', base_url+'/landing/apply_form/'+branch+'?upload_receipt')
			Modal.find('img').attr('src', 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='+base_url+'/landing/apply_form/'+branch+'?upload_receipt&choe=UTF-8')
		} else {
			Modal.find('a').attr('href', base_url+'/landing/apply_form_cn/'+branch+'?upload_receipt')
			Modal.find('img').attr('src', 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='+base_url+'/landing/apply_form_cn/'+branch+'?upload_receipt&choe=UTF-8')
		}
	} else {
		if(Language == 'english') {
			Modal.find('a').attr('href', base_url+'/landing/apply_form/'+branch)
			Modal.find('img').attr('src', 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='+base_url+'/landing/apply_form/'+branch+'&choe=UTF-8')
		} else {
			Modal.find('a').attr('href', base_url+'/landing/apply_form_cn/'+branch)
			Modal.find('img').attr('src', 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='+base_url+'/landing/apply_form_cn/'+branch+'&choe=UTF-8')
		}
	}
})

$('[name="language"]').on('change', function() {
	let Modal = $('#modal-apply'),
		Receipt = $('.receipt-switch').prop('checked')
	if(Receipt) {
		if($(this).val() == 'english') {
			Modal.find('a').attr('href', base_url+'/landing/apply_form/'+branch+'?upload_receipt')
			Modal.find('img').attr('src', 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='+base_url+'/landing/apply_form/'+branch+'?upload_receipt&choe=UTF-8')
		} else {
			Modal.find('a').attr('href', base_url+'/landing/apply_form_cn/'+branch+'?upload_receipt')
			Modal.find('img').attr('src', 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='+base_url+'/landing/apply_form_cn/'+branch+'?upload_receipt&choe=UTF-8')
		}
	} else {
		if($(this).val() == 'english') {
			Modal.find('a').attr('href', base_url+'/landing/apply_form/'+branch)
			Modal.find('img').attr('src', 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='+base_url+'/landing/apply_form/'+branch+'&choe=UTF-8')
		} else {
			Modal.find('a').attr('href', base_url+'/landing/apply_form_cn/'+branch)
			Modal.find('img').attr('src', 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='+base_url+'/landing/apply_form_cn/'+branch+'&choe=UTF-8')
		}
	}
})

function approve(id) {
	swal({
		text: "Are you sure want to approve this student?",
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
				type: "POST",
				dataType: "json",
				data: {
					'approve': id,
				},
				
				success: function(data) {
					if(data.status == 'ok') {
						swal({
							"title": data.message,
							"icon": "success"
						}).then((e) => {
							window.location.href = base_url+"students/list";
						});
					} else {
						swal({
							"title": data.message,
							"icon": "warning"
						}).then((e) => {
						});
					}
				}
			});
		}
	});
}

function reject(id) {
	swal({
		text: "Are you sure want to reject this student?",
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
				type: "POST",
				dataType: "json",
				data: {
					'reject': id,
				},
				
				success: function(data) {
					if(data.status == 'ok') {
						swal({
							"title": data.message,
							"icon": "success"
						}).then((e) => {
							window.location.href = base_url+"students/list";
						});
					} else {
						swal({
							"title": data.message,
							"icon": "warning"
						}).then((e) => {
						});
					}
				}
			});
		}
	});
}

function check_all() {
	if($("#bulk_check").prop("checked")) {
		$.each($(".student"), function(k, v) {
			$(v).prop("checked", true)
			$(".action-sec").removeClass("d-none")
		})
	} else {
		$.each($(".student"), function(k, v) {
			$(v).prop("checked", false)
			$(".action-sec").addClass("d-none")
		})
	}
}

function check() {
	
	let is_checked = false,
		is_all_checked = true
	
	$.each($(".student"), function(k, v) {
		if($(v).prop("checked")) {
			is_checked = true
		} else {
			is_all_checked = false
		}
	})
	
	if(is_checked) {
		$(".action-sec").removeClass("d-none")
	} else {
		$(".action-sec").addClass("d-none")
	}
	
	if(is_all_checked) {
		$("#bulk_check").prop("checked", true)
	} else {
		$("#bulk_check").prop("checked", false)
	}
	
}

function del_ask() {
	swal({
		text: "Are you sure want to delete these students?",
		icon: "warning",
		buttons: true,
		dangerMode: true,
		buttons: {
			cancel: "No",
			text: "Yes"
		}
	}).then((willDelete) => {
		if(willDelete) {
			$('[name="del"]').attr("type", "submit").attr("onclick", "").click()
		}
	});
}

function active_ask() {
	swal({
		text: "Are you sure want to activate these students?",
		icon: "warning",
		buttons: true,
		dangerMode: true,
		buttons: {
			cancel: "No",
			text: "Yes"
		}
	}).then((willDelete) => {
		if(willDelete) {
			$('[name="active"]').attr("type", "submit").attr("onclick", "").click()
		}
	});
}

function inactive_ask() {
	swal({
		text: "Are you sure want to inactivate these students?",
		icon: "warning",
		buttons: true,
		dangerMode: true,
		buttons: {
			cancel: "No",
			text: "Yes"
		}
	}).then((willDelete) => {
		if(willDelete) {
			$('[name="inactive"]').attr("type", "submit").attr("onclick", "").click()
		}
	});
}

/* $.fn.dataTable.pipeline = function ( opts ) {
    // Configuration options
    var conf = $.extend( {
        pages: 5,     // number of pages to cache
        url: '',      // script url
        data: null,   // function or object with parameters to send to the server
                      // matching how `ajax.data` works in DataTables
        method: 'GET' // Ajax HTTP method
    }, opts );
 
    // Private variables for storing the cache
    var cacheLower = -1;
    var cacheUpper = null;
    var cacheLastRequest = null;
    var cacheLastJson = null;
 
    return function ( request, drawCallback, settings ) {
        var ajax          = false;
        var requestStart  = request.start;
        var drawStart     = request.start;
        var requestLength = request.length;
        var requestEnd    = requestStart + requestLength;
         
        if ( settings.clearCache ) {
            // API requested that the cache be cleared
            ajax = true;
            settings.clearCache = false;
        }
        else if ( cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper ) {
            // outside cached data - need to make a request
            ajax = true;
        }
        else if ( JSON.stringify( request.order )   !== JSON.stringify( cacheLastRequest.order ) ||
                  JSON.stringify( request.columns ) !== JSON.stringify( cacheLastRequest.columns ) ||
                  JSON.stringify( request.search )  !== JSON.stringify( cacheLastRequest.search )
        ) {
            // properties changed (ordering, columns, searching)
            ajax = true;
        }
         
        // Store the request for checking next time around
        cacheLastRequest = $.extend( true, {}, request );
 
        if ( ajax ) {
            // Need data from the server
            if ( requestStart < cacheLower ) {
                requestStart = requestStart - (requestLength*(conf.pages-1));
 
                if ( requestStart < 0 ) {
                    requestStart = 0;
                }
            }
             
            cacheLower = requestStart;
            cacheUpper = requestStart + (requestLength * conf.pages);
 
            request.start = requestStart;
            request.length = requestLength*conf.pages;
 
            // Provide the same `data` options as DataTables.
            if ( typeof conf.data === 'function' ) {
                // As a function it is executed with the data object as an arg
                // for manipulation. If an object is returned, it is used as the
                // data object to submit
                var d = conf.data( request );
                if ( d ) {
                    $.extend( request, d );
                }
            }
            else if ( $.isPlainObject( conf.data ) ) {
                // As an object, the data given extends the default
                $.extend( request, conf.data );
            }
 
            return $.ajax( {
                "type":     conf.method,
                "url":      conf.url,
                "data":     request,
                "dataType": "json",
                "cache":    false,
                "success":  function ( json ) {
                    cacheLastJson = $.extend(true, {}, json);
 
                    if ( cacheLower != drawStart ) {
                        json.data.splice( 0, drawStart-cacheLower );
                    }
                    if ( requestLength >= -1 ) {
                        json.data.splice( requestLength, json.data.length );
                    }
                     
                    drawCallback( json );
                }
            } );
        }
        else {
            json = $.extend( true, {}, cacheLastJson );
            json.draw = request.draw; // Update the echo for each response
            json.data.splice( 0, requestStart-cacheLower );
            json.data.splice( requestLength, json.data.length );
 
            drawCallback(json);
        }
    }
};
 
// Register an API method that will empty the pipelined data, forcing an Ajax
// fetch on the next draw (i.e. `table.clearPipeline().draw()`)
$.fn.dataTable.Api.register( 'clearPipeline()', function () {
    return this.iterator( 'table', function ( settings ) {
        settings.clearCache = true;
    } );
} ); */

/* $(document).ready(function() {
	
	let deferLoading;
	
	$.ajax({
		url: base_url+"students/json_list_count",
		type: "POST",
		dataType: "json",
		async: "false",
		
		success: function(data) {
			deferLoading = data.result;
		}
	});
	
	const queryString = window.location.search;
	const urlParams = new URLSearchParams(queryString);
	
	const parent = urlParams.get('parent')
	const fullname_en = urlParams.get('fullname_en')
	const fullname_cn = urlParams.get('fullname_cn')
	const rfid_cardid = urlParams.get('rfid_cardid')
	const phone = urlParams.get('phone')
	const email = urlParams.get('email')	
	const search = urlParams.get('search')	
	
    $("#table").DataTable({
		"pageLength": 100,
        "processing": true,
        "serverSide": true,
        "ajax": {
			url: base_url+"students/json_list2",
			method: 'GET',
			data: {
				'parent': parent,
				'fullname_en': fullname_en,
				'fullname_cn': fullname_cn,
				'rfid_cardid': rfid_cardid,
				'phone': phone,
				'email': email,
				'filter_search': search,
			}
		},
		"deferLoading": deferLoading,
    });
	
}); */

$('[data-label="return_student"]').click(function(){
	window.location.href = base_url+'students/list';
});

$(".checkbox-bulk").on("change", function() {
	var count_all = $(".checkbox-bulk:checked").length;
	if( count_all > 0 ) {
		$(".navbar-bulk").removeClass("d-none");
	} else {
		$(".navbar-bulk").addClass("d-none");
	}
	$(".count-checkbox").text( count_all );
});

// Tan Jing Suan
$("#checkAll").click(function(){
    $('input.item-selected:checkbox').not(this).prop('checked', this.checked);
});
function pdf_studentcardlist() {
	listuser = "";
	listform = "";
	$('.class_select_checkbox').each(function(index,row){
		is_check = $(row).prop('checked');
		if(is_check == true)
		{
			listuser = listuser + $('[name="user['+(index+1)+']"]').val() + "--";
			// if ($(row).closest('tr').find('.class_user').val() !== undefined) {
			// 	listuser = listuser + "," + $(row).closest('tr').find('.class_user').val();
			// 	listform = listform + "," + $(row).closest('tr').find('.class_form').val();
			// }
		}
	});
	if ( listuser === "" ) {
		return;
	}
	// url = base_url + "export/pdf_studentcardlist/" + listuser;
	url = "https://system.synorex.space/highpeakedu/"+ "export/pdf_studentcardlist/" + listuser;
	window.open(url, '_blank');
}

function excel_studentlist() {
	var tbl_students = [];
    // // Header
	var header = ['No', 'Code', 'Form', 'Name', 'Status', 'Gender', 'Join Date', 'Phone', 'School', 'Joined Class', 'Parent', 'Total Payment', 'Total Discount', 'Total Receivable'];
	tbl_students.push(header);
    // Body
	$("#tbl_students tbody tr").each(function(rowIndex) {
		var body = [];
		$(this).find("td").each(function(cellIndex) {
			var td = $(this);
			if ( cellIndex <= 0 ) {
				return;	
			}
			switch(cellIndex) {
				case 1:
					// body['No'] = td[0].children[0].innerHTML.trim();
					body[0] = td[0].children[0].innerHTML.trim();
					break;
				case 2:
					// body['Code'] = td[0].innerHTML.trim();
					body[1] = td[0].innerHTML.trim();
					break;
				case 3:
					// body['Form'] = td[0].innerHTML.trim();
					body[2] = td[0].innerHTML.trim();
					break;
				// case 4:
					// body['Name'] = td[0].children[0].children[1].children[0].innerHTML.trim();
					body[3] = td[0].children[0].children[1].children[0].innerHTML.trim();
					break;
				case 5:
					// body['Status'] = td[0].children[0].innerHTML.trim();
					body[4] = td[0].children[0].innerHTML.trim();
					break;
				case 6:
					// body['Gender'] = td[0].innerHTML.trim();
					body[5] = td[0].innerHTML.trim();
					break;
				case 7:
					// body['Join Date'] = td[0].innerHTML.trim();
					body[6] = td[0].innerHTML.trim();
					break;
				case 8:
					// body['Phone'] = td[0].innerHTML.trim().replace('<br>', '\n');
					body[7] = td[0].innerHTML.trim().replace('<br>', ' ');
					break;
				case 9:
					// body['School'] = td[0].innerHTML.trim();
					body[8] = td[0].innerHTML.trim();
					break;
				case 10:
					// body['Joined Class'] = td[0].innerHTML.trim();
					body[9] = td[0].innerHTML.trim();
					break;
				case 11:
					// body['Parent'] = td[0].innerHTML.trim().replace('<br>', '\n');
					body[10] = td[0].innerHTML.trim().replace('<br>', ' ');
					break;
				case 12:
					// body['Total Payment'] = td[0].innerHTML.trim();
					body[11] = td[0].innerHTML.trim();
					break;
				case 13:
					// body['Total Discount'] = td[0].innerHTML.trim();
					body[12] = td[0].innerHTML.trim();
					break;
				case 14:
					// body['Total Receivable'] = td[0].innerHTML.trim();
					body[13] = td[0].innerHTML.trim();
					break;
				default:
					break;
			}
		});
		tbl_students.push(body);
	});
	// console.log(tbl_students);
	exportToCSV(tbl_students, "students.csv");

}

function exportToCSV(data, filename) {
    // Convert data to CSV format
    const csvContent = "data:text/csv;charset=utf-8," + data.map(row => row.join(",")).join("\n");
    // Create a link element
    const link = document.createElement("a");
    link.setAttribute("href", encodeURI(csvContent));
    link.setAttribute("download", filename);
    // Trigger the download
    link.click();
}