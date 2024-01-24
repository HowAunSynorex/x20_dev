var page_link = new URL(window.location.href),
	json_array = [];

if( page_link.searchParams.get("holiday") != undefined || page_link.searchParams.get("q") == undefined ) {
	json_array.push({
		url: base_url+"admin/json_calendar_list/holiday",
		color: "#009688",
		textColor: "#fff",
	});
}

document.addEventListener("DOMContentLoaded", function() {
	var calendarEl = document.getElementById("calendar");
	var calendar = new FullCalendar.Calendar(calendarEl, {
		plugins: ["dayGrid", "list", "interaction", "dayGrid", "timeGrid"],
		header: {
			left: "prev,next today",
			center: "title",
			right: "dayGridMonth, timeGridWeek, timeGridDay",
			month: "Month"
		},
		buttonText: {
			today: "Today",
			month: "Month",
			week: "Week",
			day: "Day",
		},
		eventTimeFormat: {
			hour: "2-digit",
			minute: "2-digit",
			hour12: true
		},
		
		eventSources: json_array
	});
	calendar.render();
});

function search() {

	var q = "";
	
	if( $("#checkbox-holiday:checked").val() == 1 ) q += "&holiday";

	window.location.href = base_url+"calendar?q"+q;

}

$("#checkbox-same_day").click(function() {
	if( $(this).is(":checked") ) {
		$("#section-date_end").addClass("d-none");
		$('#section-date_start [datal-label="label"]').text("Date");
		$('[name="date_end"]').val("");
	} else {
		$("#section-date_end").removeClass("d-none");
		$('#section-date_start [datal-label="label"]').text("Start");
		$('[name="date_end"]').val( $('[name="date_start"]').attr("data-end") );
	}
});