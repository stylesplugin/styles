jQuery(document).ready(function() {
	var pdm_activePicker = null;
	var pdm_farbtastic = jQuery.farbtastic('#pdm_farbtastic', pdm_colorPicked);

	jQuery(document).mousedown(function(){
		jQuery('#pdm_farbtastic').hide();
		pdm_activePicker = null;
	});

	jQuery('.pdm_colorpicker').bind('click', pdm_popUpFarbtastic);
	jQuery('.pdm_colorpicker_text').bind('change', pdm_color_changeAfterInput);

	function pdm_popUpFarbtastic(event) {
		jQuery(this).prev('input:first').focus();

		var color = new RGBColor(jQuery(this).css('background-color'));
		pdm_farbtastic.setColor(color.toHex());
		jQuery('#pdm_farbtastic').css({ left: (event.pageX+20)+'px', top: (event.pageY-180)+'px' });
		jQuery('#pdm_farbtastic').show();
		pdm_activePicker = jQuery(this);
	}

	function pdm_colorPicked(event) {
		if (pdm_activePicker != null) {
			pdm_activePicker.css("background", pdm_farbtastic.color);
			pdm_activePicker.prev('input:first').val(pdm_farbtastic.color);
			pdm_activePicker.prev('input:first').focus();
		}
	}

	function pdm_color_changeAfterInput(event) {
	
		var color = new RGBColor(document.getElementById(this.name).value);
		
		jQuery(this).next('input:first').focus().css('background-color', color.toHex() );

	}
});


