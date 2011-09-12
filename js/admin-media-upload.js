jQuery(function($) {
	$('#sidemenu a').each(function(){
		var href = $(this).attr('href')+'&StormStyles=1';
		$(this).attr('href', href );
	});
});