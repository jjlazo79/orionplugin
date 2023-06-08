jQuery(document).ready(function($) {
	// options you can change
	var deobfuscate_on_right_click = true;
	// function to open link on click
	function akn_ofbuscate_clicked($el,force_blank) {
		if (typeof(force_blank)=='undefined')
			var force_blank = false;
		var link = atob($el.data('o'));
		var _blank = $el.data('b');
		if (_blank || force_blank)
			window.open(link);
		else
			location.href = link;
	}
	// trigger link opening on click
	$(document).on('click','.akn-obf-link',function() {
		var $el = $(this);
		if (!$el.closest('.akn-deobf-link').length)
			akn_ofbuscate_clicked($el);
	});
	// trigger link openin in new tab on mousewheel click
	$(document).on('mousedown','.akn-obf-link',function(e) {
		if (e.which==2) {
			var $el = $(this);
			if (!$el.closest('.akn-deobf-link').length) {
				akn_ofbuscate_clicked($el,true);
				return true;
			}
		}
	});
	// deobfuscate link on right click so the context menu is a legit menu with link options
	$(document).on('contextmenu','.akn-obf-link',function(e) {
		if (deobfuscate_on_right_click) {
			var $el = $(this);
			if (!$el.closest('.akn-deobf-link').length) {
				e.stopPropagation();
				var link = atob($el.data('o'));
				var _blank = $el.data('b');
				$el.wrap('<a class="akn-deobf-link" href="'+link+'"'+(_blank?' target="_BLANK"':'')+'></a>').parent().trigger('contextmenu');
				setTimeout(function() {
					$el.unwrap();
				},10);
			}
		}
	});
});