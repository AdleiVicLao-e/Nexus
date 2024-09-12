// Function to fetch artifact options
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

//Function to search artifact information
function searchArtifact() {
    const query = document.querySelector('.search-input').value;

    if (query === '') {
        document.getElementById('search-results').innerHTML = '';
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('GET', `../include/hello.php?query=${encodeURIComponent(query)}`, true);
    xhr.onload = function () {
        if (this.status === 200) {
            const results = JSON.parse(this.responseText);
            displayResults(results);
        }
    };
    xhr.send();
}

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
            <strong>Name:</strong> ${item['Name']}<br>
            <strong>Section:</strong> ${item['Section Name']}<br>
            <strong>Catalogue:</strong> ${item['Catalogue Name']}<br>
            <strong>Subcatalogue:</strong> ${item['Subcatalogue Name']}<br>
            <strong>Description:</strong> ${item['Description']}<br>
            <button onclick="editArtifact(${item['id']}, '${item['Name']}', '${item['Section Name']}', '${item['Catalogue Name']}', '${item['Subcatalogue Name']}', '${item['Description']}')">Edit</button>
        `;
        list.appendChild(listItem);
    });

    resultsContainer.appendChild(list);
}

function editArtifact(id, name, section, catalogue, subcatalogue, description) {
    // Set the values in the form fields
    document.getElementById('artifact-id').value = id;
    document.getElementById('name').value = name;
    document.getElementById('section').value = section;
    document.getElementById('catalogue').value = catalogue;
    document.getElementById('subcatalogue').value = subcatalogue;
    document.getElementById('description').value = description;

    // Display the modal
    document.getElementById('edit-modal').style.display = 'block';
}

function closeModal() {
    document.getElementById('edit-modal').style.display = 'none';
}

function saveChanges() {
    const id = document.getElementById('artifact-id').value;
    const name = document.getElementById('name').value;
    const section = document.getElementById('section').value;
    const catalogue = document.getElementById('catalogue').value;
    const subcatalogue = document.getElementById('subcatalogue').value;
    const description = document.getElementById('description').value;

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../include/update_artifact.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (this.status === 200) {
            alert('Artifact updated successfully');
            closeModal();
            searchArtifact();
        } else {
            alert('Failed to update artifact');
        }
    };
    xhr.send(`id=${encodeURIComponent(id)}&name=${encodeURIComponent(name)}&section=${encodeURIComponent(section)}&catalogue=${encodeURIComponent(catalogue)}&subcatalogue=${encodeURIComponent(subcatalogue)}&description=${encodeURIComponent(description)}`);
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

