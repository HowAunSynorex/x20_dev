$(document).ready(function() {
	startTime();
})

function startTime() {
	 
  // const today = new Date();
  // let h = today.getHours();
  // let ampm = h >= 12 ? ' PM' : ' AM';
  // h = h % 12;
  // h = h ? h : 12;
  // let m = today.getMinutes();
  // let s = today.getSeconds();
  // h = checkTime(h);
  // m = checkTime(m);
  // s = checkTime(s);
  // document.getElementById('clock').innerHTML =  h + ":" + m + ":" + s + ampm;

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