$(function(){
	$('input[type="submit"]').prop('disabled', true);
});
$(window).on('load', function() {
	$.ajaxSetup({cache: false});
	$.get(getTokenUrl, function(result) {
		$('input[name="data[_Token][key]"]').val(result);
		$('input[type="submit"]').removeAttr('disabled');
	});
});
