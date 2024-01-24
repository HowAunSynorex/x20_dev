function load_row() {

	let Html = '',
		Page = $('[name="page"]').val(),
		Branch = $('[name="branch"]').val(),
		Total = parseFloat($('[name="total_outstanding"]').val()),
		Total_Std = parseFloat($('[name="total_outstanding_std"]').val()),
		Search = window.location.search,
		SearchForm = '',
		Sort = 'asc'

	if(Search.length > 0) {
		SearchParam = new URLSearchParams(Search);
		Search = $('input[name="q"]').val() //SearchParam.get('q')
		SearchForm = $('select[name="form"]').val() //SearchParam.get('form')
		Sort = SearchParam.get('sort')
	}

	Loading(1)

	$.ajax({
		async: true,
		url: base_url+"api/outstanding_reports",
		type: "POST",
		dataType: "json",
		data: {
			'branch': Branch,
			'page': Page,
			'search': Search,
			'search_form': SearchForm,
			'sort': Sort,
		},

		success: function(data) {

			$('[name="page"]').val(data.next_offest)

			$.each(data.result, function(k, v) {

				Total += parseFloat(v.std_unpaid_result.total)
				Total_Std++

				let Phone = v.phone != null && v.phone.length > 0 ? v.phone : '-',
					Email = v.email != null && v.email.length > 0 ? v.email : '-',
					Code = v.code != null && v.code.length > 0 ? v.code : '-',
					FormTitle = v.form_title != null && v.form_title.length > 0 ? v.form_title : '-',
					Parent = v.parent_fullname_phone != null && v.parent_fullname_phone.length > 0 ? v.parent_fullname_phone : '-',
					Btn = ''

				if(v.branch_result.pointoapi_key) {

					Phone2 = v.phone

					if(Phone2.length > 0) {

						if(Phone2[0] != '+') Phone2 = '6'+Phone2;

						let Msg = v.branch_result.send_msg_whatsapp_outstanding

						if(Msg.length > 0) {

							Msg += ' %0a %0a ';
							Msg += '* This message send via '+v.app_title;

							Msg =  Msg.replace('%NAME%', v.fullname_en + ' %0a ')
							Msg =  Msg.replace('%PHONE%', v.branch_result.phone)
							Msg =  Msg.replace('%SUBJECT%', 'Outstanding Payment %0a ')

							let Item = '',
								J = 0,
								I = v.std_unpaid_result.count

							if(typeof v.std_unpaid_result.result.class !== 'undefined') {
								$.each(v.std_unpaid_result.result.class, function(k2, v2) {
									J++;
									if(J < I) {
										Item += v2.title + ' x ' + v2.qty + ', %0a ';
									} else {
										Item += v2.title + ' x ' + v2.qty + ' %0a ';
									}
								})
							}

							if(typeof v.std_unpaid_result.result.item !== 'undefined') {
								$.each(v.std_unpaid_result.result.item, function(k2, v2) {
									J++;
									if(J < I) {
										Item += v2.title + ' x ' + v2.qty + ', %0a ';
									} else {
										Item += v2.title + ' x ' + v2.qty + ' %0a ';
									}
								})
							}

							if(typeof v.std_unpaid_result.result.service !== 'undefined') {
								$.each(v.std_unpaid_result.result.service, function(k2, v2) {
									J++;
									if(J < I) {
										Item += v2.title + ' x ' + v2.qty + ', %0a ';
									} else {
										Item += v2.title + ' x ' + v2.qty + ' %0a ';
									}
								})
							}

							if(typeof v.std_unpaid_result.result.others !== 'undefined') {
								$.each(v.std_unpaid_result.result.others, function(k2, v2) {
									J++;
									if(J < I) {
										Item += v2.title + ' x ' + v2.qty + ', %0a ';
									} else {
										Item += v2.title + ' x ' + v2.qty + ' %0a ';
									}
								})
							}

							Msg =  Msg.replace('%ITEM_NEWLINE%', ' %0a ' + Item + ' %0a ')
							Msg =  Msg.replace('%TOTALOUTSTANDINGAMOUNT%', parseFloat(v.std_unpaid_result.total).toFixed(2))

							Btn += `
								<a href="https://wa.me/`+Phone2+`?text=`+Msg+`" target="_blank" class="btn btn-secondary btn-sm" data-toggle="tooltip" title="Send WhatsApp"><i class="fab fa-fw fa-whatsapp py-1"></i></a>
							`

						} else {

							Btn += `
								<a href="javascript:;" class="btn btn-secondary btn-sm" style="opacity: .5" data-toggle="tooltip" title="WhatsApp content haven't been set"><i class="fab fa-fw fa-whatsapp py-1"></i></a>
							`

						}

					} else {

						Btn += `
							<a href="javascript:;" class="btn btn-secondary btn-sm disabled" data-toggle="tooltip" title="Send WhatsApp"><i class="fab fa-fw fa-whatsapp py-1"></i></a>
						`

					}

					let Disabled_Phone = '',
						Disabled_Email = ''

					if(v.phone.length == 0) {
						Disabled_Phone = 'disabled'
					}

					if(v.email.length == 0) {
						Disabled_Email = 'disabled'
					}

					Btn += `
					
						<a href="javascript:;" onclick="send_email(`+v.pid+`)" class="btn btn-secondary btn-sm d-lg-inline-blockA d-noneA `+Disabled_Email+`" data-toggle="tooltip" title="Send Email"><i class="fa fa-fw fa-envelope py-1"></i></a>
										
						<a href="javascript:;" onclick="send_sms(`+v.pid+`)" class="btn btn-secondary btn-sm d-lg-inline-blockA d-noneA `+Disabled_Phone+`" data-toggle="tooltip" title="Send SMS"><i class="fa fa-fw fa-comments py-1"></i></a>
					
					`

				}

				if(v.fullname_cn == "" || v.fullname_cn == null)
					display_name = v.fullname_en;
				else
					display_name = v.fullname_cn;
		
				Html += `
					<tr class="table-danger main-tr" data-toggle="collapse" data-target="#collapse`+v.pid+`" aria-expanded="true" aria-controls="collapse`+v.pid+`">
						<td>`+(parseInt(k)+1)+`</td>
						<td>`+Code+`</td>
						<td><a href="`+base_url+'students/edit/'+v.pid+`">`+display_name+`</a></td>
						<td>`+Phone+`</td>
						<td>`+Parent+`</td>
						<td>`+FormTitle+`</td>
						<td class="text-right">`+parseFloat(v.std_unpaid_result.total).toFixed(2)+`</td>
						<td class="font-weight-bold"><a href="`+base_url+'payment/add/'+v.pid+`"><i class="fa fa-fw fa-file-invoice"></i> Make a Payment</a></td>
						<td>`+Btn+`</td>
					</tr>
				
				`;

				if(typeof v.std_unpaid_result.result.item !== 'undefined') {
					$.each(v.std_unpaid_result.result.item, function(k2, v2) {
						Html += `
							<tr class="collapse p-0 table-light" id="collapse`+v.pid+`">
								<td></td>
								<td></td>
								<td>`+v2.title+`</td>
								<td>x `+v2.qty+`</td>
								<td></td>
								<td class="text-right">`+parseFloat(v2.amount).toFixed(2)+`</td>
								<td>
									<a href="javascript:;" onclick="del_ask_item(`+v2.id+`)" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-times py-1"></i></a>
								</td>
								<td></td>
							</tr>
						
						`
					})
				}

				if(typeof v.std_unpaid_result.result.class !== 'undefined') {

					var classes = v.std_unpaid_result.result.class;
					var sort_classes = classes.sort((a, b) => {
										  if (a.period < b.period) {
											return -1;
										  }
										});

					var group_sort_classes = sort_classes.reduce(function(result, current) {
												result[current.period] = result[current.period] || [];
												result[current.period].push(current);
												return result;
											}, {});

					console.log(group_sort_classes);

					$.each(group_sort_classes, function(k, arr) {

						periodTotal = arr.reduce(function (sum, period) {
							return sum + parseFloat(period.amount);
						}, 0);

						Html += `
							<tr class="collapse p-0 table-light" id="collapse`+v.pid+`">
								<td></td>
								<td></td>
								<td>`+k+`</td>
								<td></td>
								<td></td>
								<td></td>
								<td class="text-right">`+parseFloat(periodTotal).toFixed(2)+`</td>
								<td></td>
								<td></td>
							</tr>
						`

						$.each(arr, function(k2, v2) {

							let Title = typeof v2.period !== 'undefined' ? '['+v2.period+'] ' + v2.title : v2.title,
								Btn = typeof v2.period !== 'undefined' ? `<a href="javascript:;" onclick="del_ask_class(`+v.pid+`, `+v2.class+`, '`+v2.period+`')" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-times py-1"></i></a>` : `<a href="javascript:;" onclick="del_ask_class(`+v.pid+`, `+v2.class+`)" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-times py-1"></i></a>`

							Html += `
								<tr class="collapse p-0 table-light" id="collapse`+v.pid+`">
									<td></td>
									<td></td>
									<td>`+Title+`</td>
									<td>x `+v2.qty+`</td>
									<td></td>
									<td></td>
									<td class="text-right">`+parseFloat(v2.amount).toFixed(2)+`</td>
									<td>`+Btn+`</td>
									<td></td>
								</tr>
							
							`
						})
					})
					/*
					$.each(sort_classes, function(k2, v2) {

						let Title = typeof v2.period !== 'undefined' ? '['+v2.period+'] ' + v2.title : v2.title,
							Btn = typeof v2.period !== 'undefined' ? `<a href="javascript:;" onclick="del_ask_class(`+v.pid+`, `+v2.class+`, '`+v2.period+`')" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-times py-1"></i></a>` : `<a href="javascript:;" onclick="del_ask_class(`+v.pid+`, `+v2.class+`)" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-times py-1"></i></a>`

						Html += `
							<tr class="collapse p-0 table-light" id="collapse`+v.pid+`">
								<td></td>
								<td></td>
								<td>`+Title+`</td>
								<td>x `+v2.qty+`</td>
								<td></td>
								<td></td>
								<td class="text-right">`+parseFloat(v2.amount).toFixed(2)+`</td>
								<td>`+Btn+`</td>
								<td></td>
							</tr>

						`
					})*/
				}

				if(typeof v.std_unpaid_result.result.service !== 'undefined') {
					$.each(v.std_unpaid_result.result.service, function(k2, v2) {

						let Title = typeof v2.period !== 'undefined' ? '['+v2.period+'] ' + v2.title : v2.title,
							Btn = typeof v2.period !== 'undefined' ? `<a href="javascript:;" onclick="del_ask_class(`+v.pid+`, `+v2.class+`, '`+v2.period+`')" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-times py-1"></i></a>` : `<a href="javascript:;" onclick="del_ask_class(`+v.pid+`, `+v2.class+`)" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-times py-1"></i></a>`

						Html += `
							<tr class="collapse p-0 table-light" id="collapse`+v.pid+`">
								<td></td>
								<td></td>
								<td>`+Title+`</td>
								<td>x `+v2.qty+`</td>
								<td></td>
								<td></td>
								<td class="text-right">`+parseFloat(v2.amount).toFixed(2)+`</td>
								<td>`+Btn+`</td>
								<td></td>
							</tr>
						
						`
					})
				}

				if(typeof v.std_unpaid_result.result.others !== 'undefined') {
					$.each(v.std_unpaid_result.result.others, function(k2, v2) {

						let Title = typeof v2.period !== 'undefined' ? '['+v2.period+'] ' + v2.title : v2.title,
							Btn = typeof v2.period !== 'undefined' ? `<a href="javascript:;" onclick="del_ask_class(`+v.pid+`, `+v2.class+`, '`+v2.period+`')" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-times py-1"></i></a>` : `<a href="javascript:;" onclick="del_ask_class(`+v.pid+`, `+v2.class+`)" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-times py-1"></i></a>`

						Html += `
							<tr class="collapse p-0 table-light" id="collapse`+v.pid+`">
								<td></td>
								<td></td>
								<td>`+Title+`</td>
								<td>x `+v2.qty+`</td>
								<td></td>
								<td></td>
								<td class="text-right">`+parseFloat(v2.amount).toFixed(2)+`</td>
								<td>`+Btn+`</td>
								<td></td>
							</tr>
						
						`
					})
				}

			})




			Html += `
				<tr id="load_more">
					<td colspan="6" class="text-center">
						<a href="javascript:;" onclick="load_row()">Load More</a>
					</td>
				</tr>
			`

			$('#load_more').remove()
			$('table').append(Html)
			$('.main-tr').each(function(k, v) {
				$(v).find('td:first-child').text(k+1)
			})
			$('[name="total_outstanding"]').val(Total)
			$('#total_outstanding').text(Total.toFixed(2))
			$('[name="total_outstanding_std"]').val(Total_Std)
			$('#total_outstanding_std').text(Total_Std)

			$.ajax({
				url: base_url+"api/update_count",
				type: "POST",
				dataType: "json",
				data: {
					'branch': Branch,
					'outstanding_count': $('.main-tr').length,
				},

				success: function(data) {
				}
			})

			Loading(0)

		}
	});

}

$(document).ready(function() {
	
	if ($('select[name="form"]').val())
	{
		load_row();
	}
	//

	// $.each($("#hidden-pagination").find("strong, a"), function(k, v) {
		// let html;
		// if($(v).attr("href") == undefined) {
			// html = `<li class="page-item active"><a class="page-link" href="#">`+$(v).text()+`</a></li>`;
		// } else {
			// html = `<li class="page-item"><a class="page-link" href="`+$(v).attr("href")+`">`+$(v).text()+`</a></li>`;
		// }

		// $(".pagination").append(html);
	// })
})

function send_email(id) {
	swal({
		text: "Are you sure want to send notification via email to this student?",
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
		text: "Are you sure want to send notification via SMS to this student?",
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

/* function filter() {

	let filter, tr, td, txtValue, hasTr, display;

	filter = $('[name="q"]').val().toUpperCase();
	tr = $("table").find(".main-tr")

	for (let i = 0; i < tr.length; i++) {

		td = tr[i].getElementsByTagName("td")[1];

		if (td) {

			txtValue = td.textContent || td.innerText;

			if (txtValue.toUpperCase().indexOf(filter) > -1) {

				tr[i].style.display = "";

			} else {

				tr[i].style.display = "none";
			}

		}

	}

	hasTr = false;

	$.each($("table").find(".main-tr"), function(k, v) {

		if($(v).css("display") != "none") hasTr = true;

	})

	display = (hasTr) ? "none" : "";

	$("#no_result_found").css("display", display)

} */

function del_ask_item(id) {
	swal({
		text: "Are you sure want to delete this unpaid item?",
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
				url: base_url+"students/json_del_join/"+id,
				type: "POST",
				dataType: "json",

				success: function(data) {
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						window.location.reload()
					});
				}
			});
		}
	});
}

function del_ask_class(user_id, class_id, period) {
	swal({
		text: "Are you sure want to delete this unpaid class?",
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
				url: base_url+"reports/json_del_unpaid_class/"+user_id+"/"+class_id+"/"+period,
				type: "POST",
				dataType: "json",

				success: function(data) {
					swal({
						"title": data.message,
						"icon": "success"
					}).then((e) => {
						window.location.reload()
					});
				}
			});
		}
	});
}