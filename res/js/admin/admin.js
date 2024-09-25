//Visitor Analytics
function renderChart(data) {
    const ctx = document.getElementById('statisticsChart').getContext('2d'); 
    const chart = new Chart(ctx, {
        type: 'bar', // or 'line', 'pie', etc.
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'], // Monthly labels
            datasets: [
                {
                    label: 'Louisians', // Change as per your requirement
                    data: data.louisians, // Access the louisians array from data
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Out-of-School', // Change as per your requirement
                    data: data.out_of_school, // Access the out_of_school array from data
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function fetchChartData() {
    fetch('/include/chart.php') 
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log('Fetched data:', data); // Log fetched data
            renderChart(data);
        })
        .catch(error => console.error('Error fetching chart data:', error));
}

// Adding Artifact Tab
function fetchArtifactOptions() {
    fetch('/include/get.php')
        .then(response => response.json())
        .then(data => {
            const sectionSelect = document.getElementById('section');
            sectionSelect.innerHTML = '<option value="">Select Section</option>';
            data.sections.forEach(section => {
                const option = document.createElement('option');
                option.value = section.section_id;
                option.textContent = section.section_name;
                sectionSelect.appendChild(option);
            });

            sectionSelect.addEventListener('change', (e) => {
                const sectionId = e.target.value;
                updateCatalogOptions(sectionId);
            });

            const catalogSelect = document.getElementById('catalog');
            const subCatalogSelect = document.getElementById('sub-catalog');
            catalogSelect.innerHTML = '<option value="" selected disabled>Select Catalog</option>';
            catalogSelect.disabled = true;
            subCatalogSelect.innerHTML = '<option value="" selected disabled>Select Sub Catalog</option>';
            subCatalogSelect.disabled = true;
        })
        .catch(error => console.error('Error fetching options:', error));
}

function updateCatalogOptions(sectionId) {
    const catalogSelect = document.getElementById('catalog');
    const subCatalogSelect = document.getElementById('sub-catalog');

    if (sectionId) {
        fetch('/include/get.php?section_id=' + sectionId)
            .then(response => response.json())
            .then(data => {
                catalogSelect.innerHTML = '<option value="" selected disabled>Select Catalog</option>';
                if (data.catalogues.length > 0) {
                    data.catalogues.forEach(catalogue => {
                        const option = document.createElement('option');
                        option.value = catalogue.catalogue_id;
                        option.textContent = catalogue.catalogue_name;
                        catalogSelect.appendChild(option);
                    });
                    catalogSelect.disabled = false;
                } else {
                    catalogSelect.disabled = true;
                }
                subCatalogSelect.innerHTML = '<option value="" selected disabled>Select Sub Catalog</option>';
                subCatalogSelect.disabled = true;
            })
            .catch(error => console.error('Error fetching catalog options:', error));
    } else {
        catalogSelect.innerHTML = '<option value="" selected disabled>Select Catalog</option>';
        catalogSelect.disabled = true;
        subCatalogSelect.innerHTML = '<option value="" selected disabled>Select Sub Catalog</option>';
        subCatalogSelect.disabled = true;
    }
}

document.getElementById('catalog').addEventListener('change', (e) => {
    const catalogId = e.target.value;
    updateSubCatalogOptions(catalogId);
});

function updateSubCatalogOptions(catalogId) {
    const subCatalogSelect = document.getElementById('sub-catalog');

    if (catalogId) {
        fetch('/include/get.php?catalog_id=' + catalogId)
            .then(response => response.json())
            .then(data => {
                subCatalogSelect.innerHTML = '<option value="" selected disabled>Select Sub Catalog</option>';
                if (data.subcatalogues.length > 0) {
                    data.subcatalogues.forEach(subcatalogue => {
                        const option = document.createElement('option');
                        option.value = subcatalogue.subcat_id;
                        option.textContent = subcatalogue.subcat_name;
                        subCatalogSelect.appendChild(option);
                    });
                    subCatalogSelect.disabled = false;
                } else {
                    subCatalogSelect.disabled = true;
                }
            })
            .catch(error => console.error('Error fetching sub-catalog options:', error));
    } else {
        subCatalogSelect.innerHTML = '<option value="" selected disabled>Select Sub Catalog</option>';
        subCatalogSelect.disabled = true;
    }
}

document.addEventListener("DOMContentLoaded", fetchArtifactOptions);


// Searching Artifact Tab

let selectedArtifact = null;
let highlightedItem = null;

function searchArtifact() {
    const query = document.querySelector('.search-input').value;

    if (query === '') {
        document.getElementById('search-results').innerHTML = '';
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('GET', `../include/searchArtifact.php?query=${encodeURIComponent(query)}`, true);
    xhr.onload = function () {
        if (this.status === 200) {
            const results = JSON.parse(this.responseText);
            displayResults(results);
        }
    };
    xhr.send();
}

let isMultiSelectEnabled = false;

function displayResults(data) {
    const resultsContainer = document.getElementById('search-results');
    resultsContainer.innerHTML = '';

    if (data.length === 0) {
        resultsContainer.innerHTML = '<p>No results found</p>';
        return;
    }

    const list = document.createElement('ul');
    data.forEach(item => {
        const listItem = document.createElement('li');

        listItem.innerHTML = `
            <input type="checkbox" class="artifact-checkbox" data-id="${item['ID']}" style="display: ${isMultiSelectEnabled ? 'inline' : 'none'};">
            <strong>Artifact Number:</strong> ${item['ID']}<br>
            <strong>Name:</strong> ${item['Name']}<br>
            <strong>Section:</strong> ${item['Section Name']}<br>
            <strong>Catalogue:</strong> ${item['Catalogue Name']}<br>
            <strong>Subcatalogue:</strong> ${item['Subcatalogue Name']}<br>
            <strong>Description:</strong> ${item['Description']}<br>
        `;

        listItem.onclick = () => toggleEditButton(item, listItem);
        list.appendChild(listItem);
    });

    resultsContainer.appendChild(list);
}

//Multiple Select Artifact Functionalities
function toggleMultiSelect() {
    isMultiSelectEnabled = !isMultiSelectEnabled;
    const checkboxes = document.querySelectorAll('.artifact-checkbox');
    const deleteButton = document.getElementById('delete-selected-button');

    checkboxes.forEach(checkbox => {
        checkbox.style.display = isMultiSelectEnabled ? 'inline' : 'none';
    });

    // Show or hide the delete button
    deleteButton.style.display = isMultiSelectEnabled ? 'inline' : 'none';

    const buttonText = isMultiSelectEnabled ? 'Disable Multi-Select' : 'Enable Multi-Select';
    document.getElementById('toggle-multi-select').textContent = buttonText;
}

function toggleEditButton(item, listItem) {
    if (isMultiSelectEnabled) {
        return; // Don't allow editing
    }
    const editButton = document.getElementById('edit-button');
    const deleteButton = document.getElementById('delete-button');

    if (selectedArtifact && selectedArtifact['ID'] === item['ID']) {
        selectedArtifact = null;
        if (editButton) editButton.remove();
        if (deleteButton) deleteButton.remove();
        if (highlightedItem) highlightedItem.classList.remove('highlight');
        highlightedItem = null;
        return;
    }

    selectedArtifact = item;

    if (highlightedItem) {
        highlightedItem.classList.remove('highlight');
    }
    highlightedItem = listItem;
    highlightedItem.classList.add('highlight');

    if (editButton) {
        editButton.remove();
    }

    const newEditButton = document.createElement('button');
    newEditButton.id = 'edit-button';
    newEditButton.textContent = 'Edit';
    newEditButton.onclick = () => openModal(item);

    highlightedItem.parentNode.insertBefore(newEditButton, highlightedItem.nextSibling);

    if (deleteButton) {
        deleteButton.remove();
    }

    const newDeleteButton = document.createElement('button');
    newDeleteButton.id = 'delete-button';
    newDeleteButton.textContent = 'Delete';
    newDeleteButton.onclick = () => confirmDelete(item['ID']);

    highlightedItem.parentNode.insertBefore(newDeleteButton, highlightedItem.nextSibling);
}

function deleteSelectedArtifacts() {
    const selectedCheckboxes = document.querySelectorAll('.artifact-checkbox:checked');
    if (selectedCheckboxes.length === 0) {
        alert('No artifacts selected.');
        return;
    }

    const selectedIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.getAttribute('data-id'));

    if (confirm(`Are you sure you want to delete ${selectedIds.length} artifacts?`)) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../include/deleteMultipleArtifacts.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onload = function () {
            const response = JSON.parse(this.responseText);
            if (response.success) {
                alert(response.message);
                searchArtifact();
            } else {
                alert(response.message);
            }
        };
        xhr.send(JSON.stringify({ ids: selectedIds }));
    }
}


//Single Selected Artifact Functionalities

function openModal(item) {
    document.getElementById('artifact-id').value = item['ID'];
    document.getElementById('editName').value = item['Name'];

    fetchSections(item['Section ID'], () => {
        fetchCatalogs(item['Section ID'], item['Catalogue ID'], () => {
            fetchSubcatalogs(item['Catalogue ID'], item['Subcatalogue ID']);
        });
    });

    document.getElementById('editDescription').value = item['Description'];

    document.getElementById('edit-modal').style.display = 'block';
}

function deleteArtifact(id) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../include/deleteArtifact.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function () {
        const response = JSON.parse(this.responseText);
        if (response.success) {
            alert(response.message);
            searchArtifact(); // Refresh search results
        } else {
            alert(response.message);
        }
    };
    xhr.send(JSON.stringify({ id: id }));
}

function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this artifact?")) {
        deleteArtifact(id);
    }
}

function fetchSections(selectedSectionId) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '../include/get.php', true);
    xhr.onload = function () {
        if (this.status === 200) {
            const data = JSON.parse(this.responseText);
            const sectionSelect = document.getElementById('editSection');
            sectionSelect.innerHTML = ''; // Clear existing options

            data.sections.forEach(section => {
                const option = document.createElement('option');
                option.value = section.section_id;
                option.textContent = section.section_name;
                if (parseInt(section.section_id) === parseInt(selectedSectionId)) {
                    option.selected = true;
                }
                sectionSelect.appendChild(option);
            });

            sectionSelect.addEventListener('change', function() {
                const catalogSelect = document.getElementById('editCatalog');
                const subcatalogSelect = document.getElementById('editSubcatalog');

                catalogSelect.innerHTML = '';
                subcatalogSelect.innerHTML = '';

                catalogSelect.disabled = true;
                subcatalogSelect.disabled = true;

                fetchCatalogs(this.value, null);
            });

            const selectedCatalogId = document.getElementById('editCatalog').value;
            if (selectedCatalogId) {
                fetchSubcatalogs(selectedCatalogId);
            }
        }
    };
    xhr.send();
}

function fetchCatalogs(selectedSectionId, selectedCatalogId) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `../include/get.php?section_id=${selectedSectionId}`, true);
    xhr.onload = function () {
        if (this.status === 200) {
            const data = JSON.parse(this.responseText);
            const catalogSelect = document.getElementById('editCatalog');
            catalogSelect.innerHTML = '';

            if (data.catalogues.length === 0) {
                // No catalogs available
                const noCatalogOption = document.createElement('option');
                noCatalogOption.textContent = 'No catalogs available under this section';
                noCatalogOption.disabled = true;
                noCatalogOption.selected = true;
                catalogSelect.appendChild(noCatalogOption);
                catalogSelect.disabled = true;
            } else {
                data.catalogues.forEach(catalog => {
                    const option = document.createElement('option');
                    option.value = catalog.catalogue_id;
                    option.textContent = catalog.catalogue_name;
                    if (catalog.catalogue_id === selectedCatalogId) {
                        option.selected = true;
                    }
                    catalogSelect.appendChild(option);
                });
                catalogSelect.disabled = false;

                catalogSelect.addEventListener('change', function() {
                    fetchSubcatalogs(this.value);
                    document.getElementById('editSubcatalog').disabled = false;
                });
            }
        }
    };
    xhr.send();
}

function fetchSubcatalogs(selectedCatalogId, selectedSubcatalogId) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `../include/get.php?catalog_id=${selectedCatalogId}`, true);
    xhr.onload = function () {
        if (this.status === 200) {
            const data = JSON.parse(this.responseText);
            const subcatalogSelect = document.getElementById('editSubcatalog');
            subcatalogSelect.innerHTML = ''; // Clear existing options

            if (data.subcatalogues.length === 0) {
                const noSubcatalogOption = document.createElement('option');
                noSubcatalogOption.textContent = 'No subcatalogs available under this catalog';
                noSubcatalogOption.disabled = true;
                noSubcatalogOption.selected = true;
                subcatalogSelect.appendChild(noSubcatalogOption);
                subcatalogSelect.disabled = true;
            } else {
                // Populate subcatalogs
                data.subcatalogues.forEach(subcatalog => {
                    const option = document.createElement('option');
                    option.value = subcatalog.subcat_id;
                    option.textContent = subcatalog.subcat_name;
                    if (subcatalog.subcat_id === selectedSubcatalogId) {
                        option.selected = true;
                    }
                    subcatalogSelect.appendChild(option);
                });
                subcatalogSelect.disabled = false;
            }
        }
    };
    xhr.send();
}

function saveChanges() {
    const id = document.getElementById('artifact-id').value;
    const name = document.getElementById('editName').value;
    const sectionId = document.getElementById('editSection').value;
    const catalogId = document.getElementById('editCatalog').value || null;
    const subcatalogId = document.getElementById('editSubcatalog').value || null;
    const description = document.getElementById('editDescription').value;

    const data = {
        id: id,
        name: name,
        section_id: sectionId,
        catalog_id: catalogId,
        subcatalog_id: subcatalogId,
        description: description,
    };

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../include/editArtifact.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function () {
        const response = JSON.parse(this.responseText);
        if (response.success) {
            closeModal();
            searchArtifact();
            alert(response.message);
        } else {
            alert(response.message);
        }
    };
    xhr.send(JSON.stringify(data));
}

function closeModal() {
    document.getElementById('edit-modal').style.display = 'none';
}



const sectionBtn = document.getElementById('section-btn');
const catalogBtn = document.getElementById('catalog-btn');
const subcatBtn = document.getElementById('subcat-btn');

const sectionPopup = document.querySelector('.popup-section');
const catalogPopup = document.querySelector('.popup-catalog');
const subcatPopup = document.querySelector('.popup-subcat');

const popupOverlay = document.querySelector('.popup');
const closeIcons = document.querySelectorAll('.close');

function showPopup(popup) {
    popupOverlay.style.display = 'flex';
    popup.style.display = 'block';
}

function hidePopup() {
    popupOverlay.style.display = 'none';
    sectionPopup.style.display = 'none';
    catalogPopup.style.display = 'none';
    subcatPopup.style.display = 'none';
}

sectionBtn.addEventListener('click', () => showPopup(sectionPopup));
catalogBtn.addEventListener('click', () => showPopup(catalogPopup));
subcatBtn.addEventListener('click', () => showPopup(subcatPopup));

closeIcons.forEach(icon => {
    icon.addEventListener('click', hidePopup);
});

// Function to fetch artifact options
// function fetchArtifactOptions() {
//     fetch('include/get.php')
//         .then(response => response.json())
//         .then(data => {
//             // Populate the section select
//             let sectionSelect = document.getElementById('section');
//             sectionSelect.innerHTML = '<option value="create_section">Create Section</option>';
//             data.sections.forEach(section => {
//                 let option = document.createElement('option');
//                 option.value = section.section_id;
//                 option.textContent = section.section_name;
//                 sectionSelect.appendChild(option);
//             });
//
//             // Populate the catalogue select
//             let catalogueSelect = document.getElementById('catalog');
//             catalogueSelect.innerHTML = '<option value="create_catalog">Create Catalog</option>';
//             data.catalogues.forEach(catalogue => {
//                 let option = document.createElement('option');
//                 option.value = catalogue.catalogue_id;
//                 option.textContent = catalogue.catalogue_name;
//                 catalogueSelect.appendChild(option);
//             });
//
//             // Populate the subcatalogue select
//             let subcatalogueSelect = document.getElementById('sub-catalog');
//             subcatalogueSelect.innerHTML = '<option value="create_subcatalog">Create Sub Catalog</option>';
//             data.subcatalogues.forEach(subcatalogue => {
//                 let option = document.createElement('option');
//                 option.value = subcatalogue.subcat_id;
//                 option.textContent = subcatalogue.subcat_name;
//                 subcatalogueSelect.appendChild(option);
//             });
//         })
//         .catch(error => console.error('Error fetching options:', error));
// }
// document.addEventListener("DOMContentLoaded", fetchArtifactOptions);


//Chart

var ctx = document.getElementById('statisticsChart').getContext('2d');

var statisticsChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [ {
            label: "Louisians",
            borderColor: '#fdaf4b',
            pointBackgroundColor: 'rgba(253, 175, 75, 0.6)',
            pointRadius: 0,
            backgroundColor: 'rgba(253, 175, 75, 0.4)',
            legendColor: '#fdaf4b',
            fill: true,
            borderWidth: 2,
            data: [456, 430, 345, 287, 340, 450, 630, 595, 331, 431, 456, 521]
        }, {
            label: "Out-of-School Visitors",
            borderColor: '#177dff',
            pointBackgroundColor: 'rgba(23, 125, 255, 0.6)',
            pointRadius: 0,
            backgroundColor: 'rgba(23, 125, 255, 0.4)',
            legendColor: '#177dff',
            fill: true,
            borderWidth: 2,
            data: [42, 480, 430, 150, 330, 453, 380, 334, 268, 210, 300, 500]
        }]
    },
    options : {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
            display: false
        },
        tooltips: {
            bodySpacing: 4,
            mode:"nearest",
            intersect: 0,
            position:"nearest",
            xPadding:10,
            yPadding:10,
            caretPadding:10
        },
        layout:{
            padding:{left:5,right:5,top:15,bottom:15}
        },
        scales: {
            yAxes: [{
                ticks: {
                    fontStyle: "500",
                    beginAtZero: false,
                    maxTicksLimit: 5,
                    padding: 10
                },
                gridLines: {
                    drawTicks: false,
                    display: false
                }
            }],
            xAxes: [{
                gridLines: {
                    zeroLineColor: "transparent"
                },
                ticks: {
                    padding: 10,
                    fontStyle: "500"
                }
            }]
        },
        legendCallback: function(chart) {
            var text = [];
            text.push('<ul class="' + chart.id + '-legend html-legend">');
            for (var i = 0; i < chart.data.datasets.length; i++) {
                text.push('<li><span style="background-color:' + chart.data.datasets[i].legendColor + '"></span>');
                if (chart.data.datasets[i].label) {
                    text.push(chart.data.datasets[i].label);
                }
                text.push('</li>');
            }
            text.push('</ul>');
            return text.join('');
        }
    }
});

var myLegendContainer = document.getElementById("myChartLegend");

// generate HTML legend
myLegendContainer.innerHTML = statisticsChart.generateLegend();

// bind onClick event to all LI-tags of the legend
var legendItems = myLegendContainer.getElementsByTagName('li');
for (var i = 0; i < legendItems.length; i += 1) {
    legendItems[i].addEventListener("click", legendClickCallback, false);
}

function legendClickCallback(event) {
    event = event || window.event;

    var target = event.target || event.srcElement;
    while (target.nodeName !== 'LI') {
        target = target.parentElement;
    }
    var parent = target.parentElement;
    var chartId = parseInt(parent.classList[0].split("-")[0], 10);
    var chart = Chart.instances[chartId];
    var index = Array.prototype.slice.call(parent.children).indexOf(target);

    chart.legend.options.onClick.call(chart, event, chart.legend.legendItems[index]);
    if (chart.isDatasetVisible(index)) {
        target.classList.remove('hidden');
    } else {
        target.classList.add('hidden');
    }
}

document.getElementById("savePdfBtn").addEventListener("click", function() {
    // Capture the chart div with a higher scale for better resolution
    html2canvas(document.querySelector(".card"), {
        scale: 20 // Increase scale for better resolution
    }).then(canvas => {
        // Convert the canvas to an image
        var imgData = canvas.toDataURL('image/png');

        // Create a jsPDF instance with landscape orientation ('l'), A4 page size
        var pdf = new jsPDF('l', 'mm', 'a4');

        // Image dimensions
        var imgWidth = 200; // Width of A4 paper in landscape in mm
        var imgHeight = canvas.height * imgWidth / canvas.width;

        // Centering calculations
        var pdfWidth = 297; // A4 width in mm
        var pdfHeight = 210; // A4 height in mm
        var x = (pdfWidth - imgWidth) / 2;
        var y = (pdfHeight - imgHeight) / 2;

        // Add the image to the PDF
        pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);

        // Save the PDF
        pdf.save("visitor-analytics.pdf");
    });
});

