jQuery(document).ready(function(){
	    jQuery('.bxslider').bxSlider({
	        pagerCustom: '#bx-pager'
	    });
	
	    jQuery('.slider-small').bxSlider({
	        slideWidth: 120,
	        minSlides: 1,
	        maxSlides: 2,
	        slideMargin: 10
	    });
	});

(function($){
	$(document).ready(function() {
		$('input[type=image]').removeAttr('src');
		$('input[type=image]').insertAfter('.cmbd-paypal-button form select');	
		$('input[type=image]').attr('class', 'btn-blue margin-left');
		$('input[type=image]').attr('value', 'Buy now');
		$('input[type=image]').attr('type', 'submit');
	}); 
})(jQuery);
