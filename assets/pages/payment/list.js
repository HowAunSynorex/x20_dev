$(document).ready(function() {
	$.each($("#hidden-pagination").find("strong, a"), function(k, v) {
		let html;
		if($(v).attr("href") == undefined) {
			html = `<li class="page-item active"><a class="page-link" href="#">`+$(v).text()+`</a></li>`;
		} else {
			html = `<li class="page-item"><a class="page-link" href="`+$(v).attr("href")+window.location.search+`">`+$(v).text()+`</a></li>`;
		}
		
		$(".pagination").append(html);
	})
})

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

function send_email(id) {
	swal({
		text: "Are you sure want to send invoice via email to this student?",
		icon: "warning",
		buttons: true,
		dangerMode: true,
		buttons: {
			cancel: "No",
			text: "Yes"
		}
	}).then((willDelete) => {
		if(willDelete) {
			Loading(1);
			$.ajax({
				type: "POST",
				dataType: "json",
				data: {
					"send_email": id,
				},
				success: function(data) {
					Loading(0);
					if( data.status == "ok" ) {
						swal({
							"title": "Sent Successfully",
							"text": data.message,
							"icon": "success"
						});
					} else {
						swal({
							"title": "Error",
							"text": data.message,
							"icon": "error"
						});
					}
				}
			});
			
		}
	});
}

function send_sms(id) {
	swal({
		text: "Are you sure want to send invoice via SMS to this student?",
		icon: "warning",
		buttons: true,
		dangerMode: true,
		buttons: {
			cancel: "No",
			text: "Yes"
		}
	}).then((willDelete) => {
		if(willDelete) {
			Loading(1);
			$.ajax({
				type: "POST",
				dataType: "json",
				data: {
					"send_sms": id,
				},
				success: function(data) {
					Loading(0);
					swal({
						"title": data.message,
						"icon": data.status == "ok" ? "success" : "error"
					});
				}
			});
			
		}
	});
}

$(".DTable2").DataTable({pageLength:100,bAutoWidth:!1,"order": [[ 0, "desc" ]]});

let d = "";
function print_recp(id) {
    
    Loading(1);
    $.ajax({
		url: base_url+"/export/pdf_receipt/"+id,
		type: "GET",
// 		dataType: "binary",
// 		xhrFields: {
//             responseType: 'blob' // Set the response type to 'blob' to handle file response
//         },
		success: function(data, status, xhr) {
console.log(data.payment.student_info);
// 			var url = window.URL.createObjectURL(data);
// 			var fileName = xhr.getResponseHeader('Content-Disposition').split('filename=')[1];

			// Create a link element and click it programmatically to trigger the download
            // var link = document.createElement('a');
            // link.href = url;
            // link.download = fileName;
            // link.click();
			
// 			sendRequest(data);


            // Print auto
            
            // let objFra = document.createElement('iframe');     // Create an IFrame.
            // objFra.style.visibility = 'hidden';                // Hide the frame.
            // objFra.src = url;                   // Set source.
    
            // document.body.appendChild(objFra);  // Add the frame to the web page.
    
            // objFra.contentWindow.focus();       // Set focus.
            // objFra.contentWindow.print();
			
			// Clean up the temporary URL
            // window.URL.revokeObjectURL(url);
            
            
            // Epson
            connect();
            d = data;
		}
	});
    
}

var ePosDev = new epson.ePOSDevice();
var printer = null;


// Init
function connect() {
    //Connects to a device
    ePosDev.connect('192.168.1.71', '8043', callback_connect);
    console.log('CONNECTED');
}

function callback_connect(resultConnect) {
    if ((resultConnect == 'OK') || (resultConnect == 'SSL_CONNECT_OK')) {
        //Retrieves the Printer object
        ePosDev.createDevice('local_printer', ePosDev.DEVICE_TYPE_PRINTER, {
            'crypto': false,
            'buffer': false
        }, callback_createDevice);
    }
    else {
        //Displays error messages
        console.log(resultConnect);
    }
}

function callback_createDevice(deviceObj, retcode) {
    printer = deviceObj;
    if (retcode == 'OK') {
        printer.timeout = 60000;
        //Registers an event
        printer.onstatuschange = function (res) { alert(res.success); };
        printer.onbatterystatuschange = function (res) { alert(res.success); };
        send();
    } else {
        alert(retcode);
    }
}

// Send data

let wh = 170;

async function send(){

    if(d === "") return;
    // let d = JSON.parse(data);

    let payment = d.payment;
	let branch = d.branch;
	let student = d.student;
	
	let log_payment = d.log_payment;
	let cashier = d.cashier;
	let client = d.client;
	let client_phone = d.client_phone;
	let client_address = d.client_address;
	let client_email = d.client_email;
	let owner = d.owner;
// 	let image = d.image;

    // let image = await get_img(branch.image);
    get_img(branch.image, function(image) {
        
        if(image) {
            
            printer.addTextAlign(printer.ALIGN_CENTER);
            
            printer.brightness = 2.0;
                // printer.halftone = printer.HALFTONE_THRESHOLD;
            printer.halftone = printer.HALFTONE_ERROR_DIFFUSION;
            printer.addImage(image, 0, 0, wh, wh, printer.COLOR_1, printer.MODE_MONO);
            printer.addFeed();
            
            printer.addText(branch.title+'\n');
            printer.addFeed();
            printer.addText(branch.address+'\n');
            printer.addText('Tel: '+branch.phone+'\n');
            printer.addFeedLine(3);
            printer.addTextSize(2, 2);
            printer.addText('RECEIPT\n');
            printer.addFeed();
            printer.addTextAlign(printer.ALIGN_LEFT);
            printer.addTextSize(1, 1);
            printer.addTextLang('zh-cn');
            printer.addText('Code/单号: '+payment.payment_no+'\n');
            printer.addText('Date/日期: '+payment.date+'\n');
            printer.addText('Student/学生: '+payment.student_info+'\n');
            printer.addText('Created By/开单老师: '+payment.created_by_teacher+'\n');
            printer.addFeed();
            printer.addTextStyle(false, false, true, printer.COLOR_1);
            printer.addText('Knock-off Charges(s)/付款项目\n');
            printer.addText('------------------------------------------\n');
            
            log_payment.forEach((item, index) => {
                printer.addTextStyle(false, false, false, printer.COLOR_1);
                printer.addText((index+1)+') '+item.title+'\n');
                printer.addTextStyle(false, false, true, printer.COLOR_1);
                printer.addText('RM '+parseInt(item.price_unit).toFixed(2)+' x '+item.qty+' ');
                printer.addTextPosition(350);
                printer.addText('RM '+parseInt(item.price_amount).toFixed(2)+'\n');
                printer.addFeed();
            });
            
            printer.addText('Material Fee');
            printer.addTextPosition(350);
            printer.addText('RM '+payment.material_fee+'\n');
            printer.addFeed();
            
            // printer.addTextStyle(false, false, false, printer.COLOR_1);
            // printer.addText('1) [2023-06] 英文 (Y2) [Class Bundle: Y1 - Y3 2023]\n');
            // printer.addTextStyle(false, false, true, printer.COLOR_1);
            // printer.addText('RM 50.00 x 2 ');
            // printer.addTextPosition(350);
            // printer.addText('RM 10.00\n');
            // printer.addFeed();
            
            // printer.addTextStyle(false, false, false, printer.COLOR_1);
            // printer.addText('1) [2023-06] 英文 (Y2) [Class Bundle: Y1 - Y3 2023]\n');
            // printer.addTextStyle(false, false, true, printer.COLOR_1);
            // printer.addText('RM 50.00 x 2 ');
            // printer.addTextPosition(350);
            // printer.addText('RM 100.00\n');
            // printer.addFeed();
            
            printer.addText('TOTAL PAYABLE 总数');
            printer.addTextPosition(350);
            printer.addText('RM '+parseInt(payment.total).toFixed(2)+'\n');
            printer.addFeed();
            printer.addText('Payment(s)/付费\n');
            printer.addText('------------------------------------------\n');
            printer.addTextStyle(false, false, false, printer.COLOR_1);
            printer.addText('1) '+payment.payment_method_title+'\n');
            printer.addTextStyle(false, false, true, printer.COLOR_1);
            printer.addTextPosition(350);
            printer.addText('RM '+parseInt(payment.total).toFixed(2)+'\n');
            printer.addFeedLine(3);
            printer.addText('TENDER 收到');
            printer.addTextPosition(350);
            printer.addText('RM '+payment.receive+'\n');
            printer.addText('CHANGE 找钱');
            printer.addTextPosition(350);
            printer.addText('RM '+payment.change+'\n');
            printer.addText('ADV. PAYMENT 预付');
            printer.addTextPosition(350);
            printer.addText('RM 0.00\n');
            printer.addText('OUTSTANDING 尚欠款项');
            printer.addTextPosition(350);
            printer.addText('RM 0.00\n');
            printer.addFeed();
            
            printer.addTextStyle(false, false, false, printer.COLOR_1);
            printer.addText('Remark/注: BANK IN 3609806374');
            printer.addFeedLine(2);
            printer.addTextAlign(printer.ALIGN_CENTER);
            printer.addTextSize(2, 2);
            printer.addText('Thank You 谢谢\n');
            printer.addCut(printer.CUT_FEED);
        
            if (ePosDev.isConnected) {
                printer.send();
                console.log('DONE');
                Loading(0);
            }
        }
    });
}

function get_img(image_id, callback){
    $.ajax({
		url: base_url+"/export/json_get_image/"+image_id,
		type: "GET",
		xhrFields: {
            responseType: 'blob' // Set the response type to 'blob' to handle file response
        },
		success: function(data, status, xhr) {
		    blobToBitmap(data)
            .then((bitmap) => {
                // Pass the bitmap object to the printer for printing

                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.width = wh;
                canvas.height = wh;
            
                context.drawImage(bitmap, 0, 0, wh, wh);
                
                callback(context);
            });
                
		}
	});
}

function blobToBitmap(blob) {
  return new Promise((resolve, reject) => {
    if (!('createImageBitmap' in window)) {
      reject(new Error('createImageBitmap is not supported.'));
      return;
    }

    createImageBitmap(blob)
      .then((bitmap) => {
        resolve(bitmap);
      })
      .catch((error) => {
        reject(error);
      });
  });
}

// Monitor

function startMonitor() {
    //Starts the status monitoring process
    printer.startMonitor();
}

//Opens the printer cover
function stopMonitor() {
    //Stops the status monitoring process
    printer.stopMonitor();
}

function disconnect() {
    //Discards the Printer object
    ePosDev.deleteDevice(printer, callback_deleteDevice);
}

function callback_deleteDevice(errorCode) {
    //Terminates connection with device
    ePosDev.disconnect();
}
