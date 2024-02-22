document.addEventListener("DOMContentLoaded", function() {
	const url = 'http://exam-2023-1-api.std-900.ist.mospolytech.ru/api/routes';
	const apiKey = 'f7a3b2a8-09c7-4823-93d8-89bb5567db46';
	const itemsPerPage = 5;

	function parseAndFormatMainObjects(mainObject) {
		let objects = mainObject.split(/[,-]\s|\d+\.\s/);
		objects = objects.filter(object => object.trim() !== '');
		return objects;
	}

	function displayRoutesDataPage(routes, page) {
		const routesData = document.getElementById('routesData');
		routesData.innerHTML = '';

		const startIndex = (page - 1) * itemsPerPage;
		const endIndex = startIndex + itemsPerPage;
		const displayRoutes = routes.slice(startIndex, endIndex);

		displayRoutes.forEach(route => {
			let row = document.createElement('tr');
			row.innerHTML = `<td>${route.name}</td><td>${route.description}</td><td>${parseAndFormatMainObjects(route.mainObject).join(', ')}</td>`;
			routesData.appendChild(row);
		});
	}

	function updatePagination(data) {
		let pageCount = Math.ceil(data.length / itemsPerPage);
		let paginationDiv = document.getElementById('paginationButtons');
		paginationDiv.innerHTML = '';

		for (let i = 1; i <= pageCount; i++) {
			let button = document.createElement('button');
			button.textContent = i;
			button.classList.add('bg-red-200', 'mx-2', 'px-3', 'py-1', 'rounded-md');
			button.addEventListener('click', function() {
				displayRoutesDataPage(data, i);
			});
			paginationDiv.appendChild(button);
		}
	}

    fetch(`${url}?api_key=${apiKey}`)
    .then(response => {
        if (!response.ok) {
            throw new Error('API response was not ok');
        }
        return response.json();
    })
    .then(data => {
        updatePagination(data);
        displayRoutesDataPage(data, 1);
    })
    .catch(error => {
        console.error('There was a problem with the fetch operation:', error);
        
    })
    });