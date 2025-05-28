const menuBar = document.querySelector('#content nav .bx.bx-menu');
const sidebar = document.getElementById('sidebar');

menuBar.addEventListener('click', function () {
	sidebar.classList.toggle('hide');
});

const searchButton = document.querySelector('#content nav form .form-input button');
const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
const searchForm = document.querySelector('#content nav form');

searchButton.addEventListener('click', function (e) {
	if (window.innerWidth < 576) {
		e.preventDefault();
		searchForm.classList.toggle('show');
		if (searchForm.classList.contains('show')) {
			searchButtonIcon.classList.replace('bx-search', 'bx-x');
		} else {
			searchButtonIcon.classList.replace('bx-x', 'bx-search');
		}
	}
});

if (window.innerWidth < 768) {
	sidebar.classList.add('hide');
} else if (window.innerWidth > 576) {
	searchButtonIcon.classList.replace('bx-x', 'bx-search');
	searchForm.classList.remove('show');
}

window.addEventListener('resize', function () {
	if (this.innerWidth > 576) {
		searchButtonIcon.classList.replace('bx-x', 'bx-search');
		searchForm.classList.remove('show');
	}
});

const switchMode = document.getElementById('switch-mode');

switchMode.addEventListener('change', function () {
	if (this.checked) {
		document.body.classList.add('dark');
	} else {
		document.body.classList.remove('dark');
	}
});

document.addEventListener("DOMContentLoaded", () => {
	const form = document.getElementById("lost-item-form");
	const table = document.getElementById("lost-items-table");
	const countDisplay = document.getElementById("lost-items-count");

	let lostItemCount = 0;

	form.addEventListener("submit", function (e) {
		e.preventDefault();

		const item = document.getElementById("lost-item-name").value;
		const date = document.getElementById("lost-item-date").value;
		const room = document.getElementById("lost-item-room").value;
		const description = document.getElementById("lost-item-description").value;

		const noDataRow = table.querySelector("tr td[colspan]");
		if (noDataRow) {
			noDataRow.parentElement.remove();
		}

		const row = document.createElement("tr");
		row.innerHTML = `
			<td>${item}</td>
			<td>${description}</td>
			<td>${date}</td>
			<td>${room}</td>
			<td><button class="delete-btn">Delete</button></td>
		`;

		const deleteBtn = row.querySelector(".delete-btn");
		deleteBtn.addEventListener("click", () => {
			row.remove();
			lostItemCount--;
			countDisplay.textContent = lostItemCount;


			if (table.querySelectorAll("tr").length === 0) {
				const emptyRow = document.createElement("tr");
				emptyRow.innerHTML = `<td colspan="5" style="text-align:center; color: #888;">No lost items reported yet.</td>`;
				table.appendChild(emptyRow);
			}
		});

		table.appendChild(row);
		lostItemCount++;
		countDisplay.textContent = lostItemCount;

		form.reset();
	});
});