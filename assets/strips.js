// Home grid: swipe dots on each tile, infinite scroll, and dots on single-post galleries.
(function () {
	var GRID = '.gallery';
	var NAV = '.pagination';

	function addDots(strip, slides, dotsClass) {
		if (strip.dataset.dotsReady) {
			return;
		}
		strip.dataset.dotsReady = '1';

		if (slides.length < 2) {
			return;
		}

		var dots = document.createElement('div');
		dots.className = dotsClass;
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
	}

	function initTiles(root) {
		root.querySelectorAll('.strip').forEach(function (strip) {
			addDots(strip, strip.querySelectorAll('.slide'), 'dots');
		});
	}

	function initSinglePost() {
		document.querySelectorAll('.entry-content .wp-block-gallery').forEach(function (gallery) {
			addDots(gallery, gallery.querySelectorAll('figure.wp-block-image'), 'dots');
		});
	}

	function getNext(root) {
		var link = root.querySelector('a.next');
		return link ? link.href : null;
	}

	function infiniteScroll() {
		var grid = document.querySelector(GRID);
		var nav = document.querySelector(NAV);
		if (!grid || !nav) {
			return;
		}

		var nextUrl = getNext(nav);
		if (!nextUrl) {
			return;
		}

		// Keep the numbered links in the HTML for visitors without JavaScript.
		nav.style.display = 'none';

		var sentinel = document.createElement('div');
		sentinel.className = 'scroll-sentinel';
		grid.insertAdjacentElement('afterend', sentinel);

		var loading = false;

		var observer = new IntersectionObserver(function (entries) {
			if (entries[0].isIntersecting) {
				loadMore();
			}
		}, { rootMargin: '800px 0px' });

		function finish() {
			loading = false;
			sentinel.textContent = '';
			if (!nextUrl) {
				observer.disconnect();
				sentinel.remove();
			}
		}

		function loadMore() {
			if (loading || !nextUrl) {
				return;
			}
			loading = true;
			sentinel.textContent = 'Loading…';

			fetch(nextUrl, { credentials: 'same-origin' })
				.then(function (res) {
					if (!res.ok) {
						throw new Error('HTTP ' + res.status);
					}
					return res.text();
				})
				.then(function (html) {
					var doc = new DOMParser().parseFromString(html, 'text/html');
					var newGrid = doc.querySelector(GRID);

					if (newGrid) {
						Array.prototype.forEach.call(newGrid.children, function (card) {
							var clone = document.importNode(card, true);
							grid.appendChild(clone);
							initTiles(clone);
						});
					}

					var newNav = doc.querySelector(NAV);
					nextUrl = newNav ? getNext(newNav) : null;
				})
				.catch(function () {
					// If a page fails to load, show the normal links again.
					nextUrl = null;
					nav.style.display = '';
				})
				.finally(finish);
		}

		observer.observe(sentinel);
	}

	document.addEventListener('DOMContentLoaded', function () {
		initTiles(document);
		initSinglePost();
		infiniteScroll();
	});
})();