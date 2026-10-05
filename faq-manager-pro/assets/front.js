(function () {
	document.addEventListener('click', function (e) {
		var btn = e.target.closest && e.target.closest('.fmp-btn');
		if (!btn) { return; }
		var item = btn.closest('.fmp-item'), wrap = btn.closest('.fmp-wrap');
		var open = !item.classList.contains('is-open');
		if (open && wrap.dataset.multiple !== '1') {
			wrap.querySelectorAll('.fmp-item.is-open').forEach(function (i) {
				i.classList.remove('is-open');
				i.querySelector('.fmp-btn').setAttribute('aria-expanded', 'false');
			});
		}
		item.classList.toggle('is-open', open);
		btn.setAttribute('aria-expanded', open ? 'true' : 'false');
	});
})();
