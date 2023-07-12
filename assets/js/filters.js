/**
 * Coaches filter
 */
document.addEventListener("DOMContentLoaded", function (event) {
	const filtro = document.getElementById("coaches_filter");
	if (typeof filtro !== "undefined" && null !== filtro) {
		// get URL parameters
		const queryString = window.location.search;
		const urlParams = new URLSearchParams(queryString);

		urlParams.forEach((value, key) => {
			document.getElementById("form-field-" + key).value = value;
		});
	}
});

/**
 * Ask for date
 */
document.addEventListener("DOMContentLoaded", function (event) {
	function capitalizeFirstLetter(string) {
		return string.charAt(0).toUpperCase() + string.slice(1);
	}
	setTimeout(() => {
		const coach_selector = document.querySelector(
			"form.el-form.el-form--default.el-form--label-top.am-fs__init-form"
		);
		if (typeof coach_selector !== "undefined" && null !== coach_selector) {
			// get URL parameters
			const queryString = window.location.search;
			const urlParams = new URLSearchParams(queryString);
			const inputs = coach_selector.querySelectorAll(".el-input__inner");

			const tipocoaching = urlParams.get("tipocoaching");
			if (typeof tipocoaching !== "undefined" && null !== tipocoaching) {
				inputs[0].value = capitalizeFirstLetter(tipocoaching);
				const data_labels = document.querySelectorAll(
					".am-adv-select__item-label"
				);

				var found_type;
				var event = new MouseEvent("mouseover", {
					view: window,
					bubbles: true,
					cancelable: true,
				});

				for (var i = 0; i < data_labels.length; i++) {
					data_labels[i].dispatchEvent(event);
					data_labels[
						i
					].parentNode.parentNode.parentNode.dispatchEvent(event);
					if (
						data_labels[i].textContent ==
						"Proceso Coaching " +
							capitalizeFirstLetter(tipocoaching)
					) {
						found_type = data_labels[i];
						break;
					}
				}
				if (typeof found_type !== "undefined") {
					// Simulate click
					found_type.parentElement.click();
				}
			}

			const coach_name = urlParams.get("coach");
			if (typeof coach_name !== "undefined" && null !== coach_name) {
				inputs[1].value = coach_name;

				const data_labels = document.querySelectorAll(
					".am-oit__data-label"
				);

				var found_coach;

				for (var i = 0; i < data_labels.length; i++) {
					if (data_labels[i].textContent == coach_name) {
						found_coach = data_labels[i];
						break;
					}
				}

				// Simulate click
				found_coach.parentElement.parentElement.parentElement.parentElement.parentElement.click();
			}
		}
	}, 2000);
});
