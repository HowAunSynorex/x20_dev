$(document).ready(function() {
	startTime();
})

function startTime() {

	setTimeout(startTime, 1000);

	var max = $('[name="stopper"]').val()
	if(max == 0) {
		$(".timer").text('0:15')
		$('[name="stopper"]').val(15)
		$(".progress-bar").css('width', '100%')
	} else {
		var cur = max - 1;
		$(".timer").text('0:'+checkTime(cur))
		$('[name="stopper"]').val(cur)
		$(".progress-bar").css('width', (100/15*(cur))+'%')
	}
  
};

function checkTime(i) {
  if (i < 10) {i = "0" + i};
  return i;
};