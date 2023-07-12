const gap = 16;

const carousel = document.getElementById("carousel"),
	content = document.getElementById("content"),
	next = document.getElementById("next"),
	prev = document.getElementById("prev");

if (typeof carousel !== "undefined" && null !== carousel) {
	let width = carousel.offsetWidth;

	next.addEventListener("click", (e) => {
		carousel.scrollBy(width + gap, 0);
		if (carousel.scrollWidth !== 0) {
			prev.style.display = "flex";
		}
		if (content.scrollWidth - width - gap <= carousel.scrollLeft + width) {
			next.style.display = "none";
		}
	});

	prev.addEventListener("click", (e) => {
		carousel.scrollBy(-(width + gap), 0);
		if (carousel.scrollLeft - width - gap <= 0) {
			prev.style.display = "none";
		}
		if (!content.scrollWidth - width - gap <= carousel.scrollLeft + width) {
			next.style.display = "flex";
		}
	});

	window.addEventListener("resize", (e) => (width = carousel.offsetWidth));

	document.querySelectorAll(".am-select-service-name").forEach((el) => {
		el.click();
		console.log(el);
	});

	document.querySelectorAll(".am-oit__data-label").forEach((el) => {
		el.click();
		console.log(el);
	});
}
