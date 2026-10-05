jQuery(function ($) {
	var $list = $('#fmp-list'), n = 1000, t;
	var I = FMP.i;
	$list.sortable({ handle: '.fmp-handle', axis: 'y' });
	$list.on('click', '.fmp-remove', function () { $(this).closest('li').remove(); });
	function esc(s) { return $('<div>').text(s).html(); }

	$('#fmp-new').on('click', function () {
		var i = ++n, opts = '<option value="">' + I.nocat + '</option>';
		FMP.cats.forEach(function (c) { opts += '<option value="' + c[0] + '">' + esc(c[1]) + '</option>'; });
		$list.append('<li class="fmp-row fmp-newrow"><div><span class="dashicons dashicons-menu fmp-handle"></span> <em>' + I.new + '</em> <button type="button" class="button-link fmp-remove">&times; ' + I.remove + '</button></div>' +
			'<input type="text" name="fmp_items[n' + i + '][question]" placeholder="' + I.question + '">' +
			'<textarea rows="3" name="fmp_items[n' + i + '][answer]" placeholder="' + I.answer + '"></textarea>' +
			'<select name="fmp_items[n' + i + '][cat]">' + opts + '</select></li>');
	});

	function search() {
		$.get(FMP.ajax, { action: 'fmp_search', nonce: FMP.nonce, q: $('#fmp-q').val(), cat: $('#fmp-cat').val() > 0 ? $('#fmp-cat').val() : '', status: $('#fmp-status').val() }, function (r) {
			var $r = $('#fmp-results').empty();
			if (!r.success || !r.data.length) { $r.append('<li>' + I.none + '</li>'); return; }
			r.data.forEach(function (f) {
				$r.append($('<li>').append($('<span>').text(f.title + (f.archived ? ' (' + I.archived + ')' : '')))
					.append($('<button type="button" class="button button-small">').text(I.add).on('click', function () {
						if ($list.find('[data-id="' + f.id + '"]').length) { return; }
						$list.append($('<li class="fmp-row">').attr('data-id', f.id)
							.append('<span class="dashicons dashicons-menu fmp-handle"></span>')
							.append($('<input type="hidden">').attr('name', 'fmp_items[' + f.id + '][id]').val(f.id))
							.append($('<span class="fmp-title">').text(f.title))
							.append('<button type="button" class="button-link fmp-remove">&times;</button>'));
					})));
			});
		});
	}
	$('#fmp-q').on('input', function () { clearTimeout(t); t = setTimeout(search, 250); });
	$('#fmp-cat, #fmp-status').on('change', search);
	$('#fmp-q').on('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); search(); } });
	search();
});
