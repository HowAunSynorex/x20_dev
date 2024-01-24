
$('.student-card').each(function(k, v) {
	$(v).find('input[type="radio"]').on('change', function(e) {
		
		let Input = e.target
		let CContainer = Input.closest('.checkbox-container')
		
		let None = $('.student-card .checkbox-none input:checked').length
		$('.none-count').text(None)
		
		let Present = $('.student-card .checkbox-present input:checked').length
		$('.present-count').text(Present)
		
		let Absent = $('.student-card .checkbox-absent input:checked').length
		$('.absent-count').text(Absent)
		
		$(v).find('.reason-div').addClass('d-none')
		
		if($(CContainer).hasClass('checkbox-none')) {
			$(v).find('.blink').css('backgroundColor', '#eee')
		} else if ($(CContainer).hasClass('checkbox-present')) {
			$(v).find('.blink').css('backgroundColor', '#54c67e')
		} else {
			$(v).find('.blink').css('backgroundColor', '#ec5b78')
			$(v).find('.reason-div').removeClass('d-none')
		}
		
	})
})

function myFunction() {
	let Search = $('#search').val().toUpperCase()
	$('.student-card').each(function(k, v) {
		let Info = $(v).find('.student-info').text().toUpperCase()
		if(Info.includes(Search)) {
			$(v).removeClass('d-none')
		} else {
			$(v).addClass('d-none')
		}
	})
}

$('#modal-student').on('show.bs.modal', function(e) {
	let Btn = e.relatedTarget
	let Pid = $(Btn).data('value')
	let Modal = $('#modal-student')
		
	$.ajax({
		type: "POST",
		dataType: "json",
		data: {
			'view_student': Pid,
		},
		
		success: function(data) {
			
		    var html_option = `<option value="">- Keep same -</option>`;
		    $.each(class_dropdown, function(k,v) {
		        html_option += `<option value="`+v.id+`">`+v.title+`</option>`;
		    });
		    
			
			if(data.result.length == 1) {
				let Result = data.result[0]
				Modal.find('.name').text(Result.name)
				Modal.find('.code').text(Result.code)
				Modal.find('.school').text(Result.school)
				Modal.find('.form').text(Result.form)
			    var enroll = Result.enroll > 0 ? "checked" : "" ;
				
				var extraFunction = `<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" id="customCheck1-`+Result.pid+`" `+enroll+` onclick="active(`+Result.pid+`)">
										<label class="custom-control-label" for="customCheck1-`+Result.pid+`">Enroll</label>
									</div>
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input my-checkbox" onclick="checkbox_modal(this, `+Result.pid+`)" id="modal-customCheck2-`+Result.pid+`">
										<label class="custom-control-label" for="modal-customCheck2-`+Result.pid+`">Change Class</label>
									</div>
									<select class="form-control form-control-sm d-none" id="modal-dropdown-class-`+Result.pid+`" style="width: 50%" onchange="change_class_timetable(`+class_id+`, `+Result.pid+`, this.value)">
										`+html_option+`
									</select>`;
				
				var extraFunctionChild = `<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" id="customCheck1-`+Result.pid+`" `+enroll+` onclick="activechild(`+Result.pid+`)">
										<label class="custom-control-label" for="customCheck1-`+Result.pid+`">Enroll</label>
									</div>`;
				
				Modal.find('.extra-function').html(extraFunction);
				Modal.find('.extra-function-child').html(extraFunctionChild);
			} else {
				Modal.find('.name').text('-')
				Modal.find('.code').text('-')
				Modal.find('.school').text('-')
				Modal.find('.form').text('-')
				var extraFunction = `-`;
				
				Modal.find('.extra-function').html(extraFunction);
			}
		}
	});
})


$('#modal-class').on('show.bs.modal', function(e) {
	let Btn = e.relatedTarget
	let jsonData = $(Btn).data('json')
	let Modal = $('#modal-class')
	
	Modal.find('.table > tbody').html('');
	
	for(var i =0; i < jsonData.length; i++)
	{
		let obj = jsonData[i];
		 
		row = `<tr>
					<td class="font-weight-bold p-1 pt-2">`+ (i + 1) +`</td>
					<td class="font-weight-bold p-1 pt-2">Day</td>
					<td class="p-1 pt-2">`+ obj.class_day +`</td>
				</tr>
				<tr>
					<td class="p-1"></td>
					<td class="font-weight-bold p-1">Time</td>
					<td class="p-1">`+ obj.time_range +`</td>
				</tr>
				<tr>
					<td class="p-1"></td>
					<td class="font-weight-bold p-1">Title</td>
					<td class="p-1">`+ obj.class_title +`</td>
				</tr>
				<tr>
					<td style="border-bottom: 1px solid #000 !important;" class="p-1"></td>
					<td style="border-bottom: 1px solid #000 !important;" class="font-weight-bold p-1">Teacher</td>
					<td style="border-bottom: 1px solid #000 !important;" class="p-1">`+ obj.teacher_name +`</td>
				</tr>`;
		
		Modal.find('.table > tbody').append(row);
	}
})

function search(v) {
    Loading(1);
    $.ajax({
		type: "GET",
		dataType: "json",
		data: {
			'q': v,
		},
		
		success: function(data) {
		    Loading(0);
		    
		    var html_option = `<option value="">- Keep same -</option>`;
		    $.each(class_dropdown, function(k,v) {
		        html_option += `<option value="`+v.id+`">`+v.title+`</option>`;
		    });
		    
		    var html = "";
			var html2 = "";
			$.each(data.result, function(k, v) {
			    if(v.fullname_cn==null) v.fullname_cn = '';
			    if(v.code==null) v.code = '';
			    if(v.active==0) v.fullname_en += " (Inactive)";
			    
			    var enroll = v.enroll>0 ? "checked" : "" ;
			    
			    html += `
			        <li class="list-group-item">
                        <b class="d-block">`+v.fullname_cn+` `+v.fullname_en+`</b>
                        <span class="text-muted d-block">Student Code: `+v.code+`</span>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck1-`+v.pid+`" `+enroll+` onclick="active(`+v.pid+`)">
                            <label class="custom-control-label" for="customCheck1-`+v.pid+`">Enroll</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input my-checkbox" onclick="checkbox(this, `+v.pid+`)" id="customCheck2-`+v.pid+`">
                            <label class="custom-control-label" for="customCheck2-`+v.pid+`">Change Class</label>
                        </div>
                        <select class="form-control form-control-sm d-none" id="dropdown-class-`+v.pid+`" style="width: 50%" onchange="change_class_timetable(`+class_id+`, `+v.pid+`, this.value)">
                            `+html_option+`
                        </select>
                    </li>
			    `;
				
				html2 += `
			        <li class="list-group-item">
                        <b class="d-block">`+v.fullname_cn+` `+v.fullname_en+`</b>
                        <span class="text-muted d-block">Student Code: `+v.code+`</span>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck1-`+v.pid+`" `+enroll+` onclick="activechild(`+v.pid+`)">
                            <label class="custom-control-label" for="customCheck1-`+v.pid+`">Enroll</label>
                        </div>
                    </li>
			    `;
			});
			$("#append").html(html);
			$("#append2").html(html2);
		}
	});
}


function active(student_id) {
    Loading(1);
    $.ajax({
		type: "POST",
		dataType: "json",
		data: {
			'active': student_id,
		},
		
		success: function(data) {
		    Loading(0);
		    alert("Saved!");
		}
	});
}

function activechild(student_id) {
	if ($('#customCheck1-'+student_id).is(':checked')) {
		var child = 1;
	}else{
		var child = '';
	}
    Loading(1);
    $.ajax({
		type: "POST",
		dataType: "json",
		data: {
			'active_child': student_id,
			'child': child,
		},
		
		success: function(data) {
		    Loading(0);
		    alert("Saved!");
			location.reload();
		}
	});
}

function checkbox(v, k) {
    const isChecked = $(v).prop('checked');
    if(isChecked) {
        $("#dropdown-class-"+k).removeClass("d-none");
    } else {
        $("#dropdown-class-"+k).addClass("d-none");
    }
}

function checkbox_modal(v, k) {
    const isChecked = $(v).prop('checked');
    if(isChecked) {
        $("#modal-dropdown-class-"+k).removeClass("d-none");
    } else {
        $("#modal-dropdown-class-"+k).addClass("d-none");
    }
}

// function change_class_timetable(user_id, timetable) {
	
// 	$.ajax({
// 		type: "GET",
// 		dataType: "json",
// 		data: {
// 		    "save_class": true,
// 			'timetable': timetable,
// 			"user": user_id,
// 		},
		
// 		success: function(data) {
// 		    alert("Saved!");
// 		}
// 	});
	
// }

function change_class_timetable(class_id, user_id, timetable) {
	
	$.ajax({
		type: "GET",
		dataType: "json",
		data: {
		    "save_class": true,
			'timetable': timetable,
			'class2': class_id,
			"user": user_id,
		},
		
		success: function(data) {
			toastr["success"]("Date has been updated");
		}
	});
	
}

// Tan Jing Suan
function save_attendance(pid, sub_class, date, active='') {
	reason = '';
	if ( document.getElementById('remark_'+pid).value !== '-' ) {
		reason = document.getElementById('remark_'+pid).value;
	}
	$.ajax({
		url: base_url+"webapp_teacher/json_save_attendance",
		type: "POST",
		dataType: "json",
		data: {
			'pid': pid,
			'sub_class': sub_class,
			'date' : date,
			'active': active,
			'reason': reason
		},	
		success: function(data) {
			console.log(data);
			document.getElementById('color_'+pid).style.backgroundColor = data.color;
			document.getElementById('full_bg_'+pid).style.backgroundColor = data.full_bg;
			document.getElementById('alert_'+pid).style.display = "block";
			document.getElementById('alertmsg_'+pid).style.display = "block";
			document.getElementById('alertmsg_'+pid).innerHTML = data.message;
		},
		error: function(err) {
			console.log(err);
			document.getElementById('alert_'+pid).style.display = "block";
			document.getElementById('alertmsg_'+pid).style.display = "block";
			document.getElementById('alertmsg_'+pid).innerHTML = err;
			document.getElementById('alertmsg_'+pid).className = "alert alert-danger small";
		}
	})
	
}