// Adds position dots under each swipeable strip:
// - photo strips on home grid tiles (.strip > .slide)
// - multi-photo galleries on single posts (.entry-content .wp-block-gallery)
document.addEventListener('DOMContentLoaded', function () {
	var strips = document.querySelectorAll('.strip, .entry-content .wp-block-gallery');

	strips.forEach(function (strip) {
		var isHomeTile = strip.classList.contains('strip');
		var slides = isHomeTile
			? strip.querySelectorAll('.slide')
			: strip.querySelectorAll('figure.wp-block-image');

		if (slides.length < 2) {
			return;
		}

		var dots = document.createElement('div');
		dots.className = 'dots';
		dots.setAttribute('aria-hidden', 'true');
		slides.forEach(function () {
			dots.appendChild(document.createElement('span'));
		});
		strip.insertAdjacentElement('afterend', dots);

		function update() {
			var index = Math.round(strip.scrollLeft / strip.clientWidth);
			Array.prototype.forEach.call(dots.children, function (dot, n) {
				dot.classList.toggle('is-active', n === index);
			});
		}

		strip.addEventListener('scroll', function () {
			window.requestAnimationFrame(update);
		}, { passive: true });
		update();
	});
});
