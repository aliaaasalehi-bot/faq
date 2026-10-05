jQuery(function ($) {
	var $form = $('.fmp-form'), $prev = $('#fmp-preview'), $wrap = $prev.find('.fmp-wrap');
	function apply($el) {
		var k = $el.data('key'), f = FMPA.fields[k], v = $el.val();
		if (!f) { return; }
		if (f.var) { $prev[0].style.setProperty(f.var, v + (f.unit || '')); }
		if (k === 'icon') { $wrap.attr('data-icon', v); }
		if (k === 'icon_pos') { $wrap.attr('data-pos', v); }
		if (k === 'google_font' && v) {
			var id = 'fmp-live-font';
			$('#' + id).remove();
			$('<link rel="stylesheet" id="' + id + '">').attr('href', 'https://fonts.googleapis.com/css2?family=' + encodeURIComponent(v) + ':wght@400;500;600;700;800&display=swap').appendTo('head');
		}
	}
	$form.on('input change', '[data-key]', function () { apply($(this)); });
	$('#fmp-custom-css').on('input', function () { $('#fmp-preview-css').text($(this).val().replace(/<\/?style/gi, '')); }).trigger('input');
	function setValues(vals) {
		$.each(vals, function (k, v) { var $e = $form.find('[data-key="' + k + '"]'); if ($e.length) { $e.val(v); apply($e); } });
	}
	$('.fmp-preset').on('click', function () { setValues(FMPA.presets[$(this).data('preset')].values); });
	$('#fmp-reset').on('click', function () { setValues(FMPA.defaults); });
	$form.find('[data-key]').each(function () { apply($(this)); });
});
