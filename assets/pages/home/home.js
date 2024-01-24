$('#modal-edit').on('show.bs.modal', function(e) {
	let Elem = $(e.relatedTarget),
		Id = Elem.data('id'),
		Reason = Elem.data('reason')
		
	$(this).find('[name="reason"]').val(Reason).trigger('change')
	$(this).find('[name="id"]').val(Id)
})

$(document).ready(function() {
	
	$.ajax({
		url: base_url+"api/outstanding_count",
		type: "POST",
		dataType: "json",
		data: {
			'branch': Branch,
		},
		
		success: function(data) {
			let outstanding_count = data.result.outstanding_count != null ? data.result.outstanding_count : 0;
			setTimeout(function() { 
				$('#outstanding_count').text(outstanding_count)
			}, 5000);
			
		}
	})
	
})


$.ajax({
	url: base_url+"home/json_monthly_joined/",
	type: "GET",
	dataType: "json",
	
	success: function(data) {
		
		if(data.status == "ok") {
			
			// line
			var MONTHS = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
			var config = {
				type: "line",
				data: {
					labels: MONTHS,
					datasets: [
						{
							label: "Students",
							backgroundColor: "#BBDEFB",
							borderColor: "#2196F3",
							data: data.result.student,
							fill: true,
						},
						{
							label: "Parent",
							backgroundColor: "#FFF3E0",
							borderColor: "#FF9800",
							data: data.result.parent,
							fill: true,
						},
						{
							label: "Teacher",
							backgroundColor: "#FFEBEE",
							borderColor: "#F44336",
							data: data.result.teacher,
							fill: true,
						},
						/*{
							label: "Rider",
							backgroundColor: "#E8EAF6",
							borderColor: "#3F51B5",
							data: data.result.riders,
							fill: true,
						},*/
					],
				},
				options: {
					responsive: true,
					title: {
						display: true,
						text: "Growth Analysis",
					},
					tooltips: {
						mode: "index",
						intersect: false,
					},
					hover: {
						mode: "nearest",
						intersect: true,
					},
					scales: {
						xAxes: [
							{
								display: true,
								scaleLabel: {
									display: true,
									labelString: "Month",
								},
								ticks: {
									stepSize: 1
								},
							},
						],
						yAxes: [
							{
								display: true,
								scaleLabel: {
									display: true,
									labelString: "Quantity",
								},
								ticks: {
									stepSize: 1
								},
							},
						],
					},
				},
			};

			/* var ctx = document.getElementById("chart-joined").getContext("2d");
			window.myLine = new Chart(ctx, config);
			
			// pie
			var config = {
				type: "pie",
				data: {
					labels: data.result.courses.label,
					datasets: [
						{
							label: 'Dataset 1',
							data: data.result.courses.data,
							backgroundColor: [
								"#FFCDD2",
								"#FFE0B2",
								"#FFF9C4",
								"#C8E6C9",
								"#BBDEFB",
								"#C5CAE9",
								"#D1C4E9",
								
								"#F8BBD0",
								"#E1BEE7",
								"#B3E5FC",
								"#B2EBF2",
								"#B2DFDB",
								"#DCEDC8",
								"#F0F4C3",
								"#FFECB3",
								"#FFCCBC",
								"#D7CCC8",
								"#F5F5F5",
								"#CFD8DC",
							],
						}
					]
				},
				options: {
					responsive: true,
					plugins: {
						legend: {
							position: 'top',
						},
						/*title: {
							display: true,
							text: 'Chart.js Pie Chart'
						}
					}
				},
			}; */

			// var ctx = document.getElementById("chart-classes").getContext("2d");
			// window.myLine = new Chart(ctx, config);
			
		} else {
			alert("API error");
		}
		
	}
});

intro_index();

$.ajax({
	type: "POST",
	dataType: "json",
	data: {
		"status": true
	},
	
	success: function(data) {
		
		var html = "";
		$.each(data.result, function(k, v) {
			html += `
			<li class="list-group-item">
				<p class="mb-0 font-weight-bold">`+v.title+` <em class="float-right small text-muted">`+v.time+`</em></p>
				<span class="text-muted d-block small text-truncateA" style="white-space: pre-wrap">`+v.desc+`</span>
			</li>
			`;
		});
		if(html=="") html = `<li class="list-group-item">No result found</li>`;
		$("#append-news").html(html);
		
	}
});

function previewStudent(domThis)
{
	
}


$('#modal-student').on('show.bs.modal', function(e) {
	
	let Btn = e.relatedTarget
	let type = $(Btn).data('type')
	
	var student_fullname_ens = $(Btn).closest('td').attr('data-en-'+ type);
	var student_fullname_cns = $(Btn).closest('td').attr('data-cn-'+ type);
	
	if (student_fullname_ens)
	{
		student_fullname_ens = $(Btn).closest('td').attr('data-en-'+ type).split(',');
		student_fullname_cns = $(Btn).closest('td').attr('data-cn-'+ type).split(',');
	}
	else
	{
		student_fullname_ens = [];
		student_fullname_cns = [];
	}
	
	let Modal = $('#modal-student')
	
	Modal.find('.table > tbody').html('');
	
	for(var i =0; i < student_fullname_ens.length; i++)
	{
		let en = student_fullname_ens[i];
		let cn = student_fullname_cns[i];
		 
		row = `<tr>
					<td class="font-weight-bold p-1 pt-2">`+ (i + 1) +`</td>
					<td class="p-1 pt-2">`+ en +`</td>
					<td class="p-1 pt-2">`+ cn +`</td>
				</tr>=`;
		
		Modal.find('.table > tbody').append(row);
	}
})