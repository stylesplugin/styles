jQuery(function($) {
	$('#sidemenu a').each(function(){
		var href = $(this).attr('href')+'&styles=1';
		$(this).attr('href', href );
	});

	$('td.savesend input[value="Insert into Post"]').val('Select Image').addClass('button-primary');
});