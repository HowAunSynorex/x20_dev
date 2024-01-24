$(document).ready(function(){
	$("#flowModal").modal('show');
});

function next()
{
	$("#flowModal").modal('hide');
	$("#agreeModal").modal('show');
}
function back()
{
	$("#flowModal").modal('show');
	$("#agreeModal").modal('hide');
}