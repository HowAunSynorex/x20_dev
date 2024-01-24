var page_link = new URL(window.location.href),
	json_array = [];

if( page_link.searchParams.get("class") != undefined || page_link.searchParams.get("q") == undefined ) {
	
	var teacher_filter = $('select[name="teacher"]').val() ? "?teacher=" + $('select[name="teacher"]').val() : '';
	
	json_array.push({
		url: base_url+"calendar/json_list/class" + teacher_filter,
		color: "#7986CB",
		textColor: "#fff",
	});
}

if( page_link.searchParams.get("birthday") != undefined || page_link.searchParams.get("q") == undefined ) {
	
	json_array.push({
		url: base_url+"calendar/json_list/birthday",
		color: "#EF6C00",
		textColor: "#fff",
	});
}

if( page_link.searchParams.get("event") != undefined || page_link.searchParams.get("q") == undefined ) {
	json_array.push({
		url: base_url+"calendar/json_list/event",
		color: "#3F51B5",
		textColor: "#fff",
	});
}

if( page_link.searchParams.get("holiday") != undefined || page_link.searchParams.get("q") == undefined ) {
	json_array.push({
		url: base_url+"calendar/json_list/holiday",
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

	if( $("#checkbox-class:checked").val() == 1 ) q += "&class";
	if( $("#checkbox-holiday:checked").val() == 1 ) q += "&holiday";
	if( $("#checkbox-birthday:checked").val() == 1 ) q += "&birthday";
	if( $("#checkbox-event:checked").val() == 1 ) q += "&event";
	if( $('select[name="teacher"]').val() ) q += ("&teacher=" + $('select[name="teacher"]').val());

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