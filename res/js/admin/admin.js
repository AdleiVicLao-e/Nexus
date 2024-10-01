
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
        resultsContainer.style.display = 'none'; // Hide the container if no results
        resultsContainer.innerHTML = '<p>No results found</p>';
        return;
    }
    resultsContainer.style.display = 'block'; // Show the container if there are results
    const list = document.createElement('ul');
    data.forEach(item => {
        const listItem = document.createElement('li');

        listItem.innerHTML = `
            <input type="checkbox" class="artifact-checkbox" data-id="${item['ID']}" style="display: ${isMultiSelectEnabled ? 'inline' : 'none'};">
            ${item['Name']}<br>
        `;
        
        listItem.onclick = () => toggleEditButton(item, listItem);
        list.appendChild(listItem);
    });
    

    resultsContainer.appendChild(list);
}

function toggleMultiSelect() {
    isMultiSelectEnabled = !isMultiSelectEnabled;
    const checkboxes = document.querySelectorAll('.artifact-checkbox');
    const deleteButton = document.getElementById('delete-selected-button');
    const editButton = document.getElementById('edit-button');
    const delButton = document.getElementById('delete-button');

    checkboxes.forEach(checkbox => {
        checkbox.style.display = isMultiSelectEnabled ? 'inline' : 'none';
    });

    // Show or hide the delete button
    deleteButton.style.display = isMultiSelectEnabled ? 'inline' : 'none';

    // Hide or disable the edit button when multi-select is enabled
    if (isMultiSelectEnabled) {
        editButton.style.display = 'none'; // Hide the edit button
        editButtonVisible = false; // Update flag
        delButton.style.display = 'none';
    } else {
        editButton.style.display = 'inline'; // Show the edit button if multi-select is disabled
        editButtonVisible = true; // Update flag
    }

    const buttonText = isMultiSelectEnabled ? 'Disable Multi-Select' : 'Enable Multi-Select';
    document.getElementById('toggle-multi-select').textContent = buttonText;
}

function toggleEditButton(item, listItem) {
    if (isMultiSelectEnabled) {
        return; // Don't allow editing
    }
    const editButton = document.getElementById('edit-button');

    if (selectedArtifact && selectedArtifact['ID'] === item['ID']) {
        selectedArtifact = null;
        if (editButton) editButton.remove();
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

    // Create a new edit button with an icon
    const newEditButton = document.createElement('button');
    newEditButton.id = 'edit-button';
    newEditButton.classList.add('edit-button'); // Add class for styling
    newEditButton.innerHTML = '<i class="fas fa-edit" style="color: #f6c500; font-size: 20px;"></i>'; // Font Awesome icon
    newEditButton.style.background = 'none'; // Remove default button styles
    newEditButton.style.border = 'none'; // Remove border
    newEditButton.style.cursor = 'pointer'; // Change cursor to pointer
    newEditButton.onclick = () => openModal(item);

    // Append the edit button to the list item
    listItem.appendChild(newEditButton);
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
    // Confirm deletion with the user
    const confirmDelete = confirm("Are you sure you want to delete this artifact?");
    if (!confirmDelete) return; // Exit if the user cancels

    const xhr = new XMLHttpRequest();
    xhr.open('DELETE', '../include/deleteArtifact.php', true); // Ensure this is the correct endpoint
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function () {
        const response = JSON.parse(this.responseText);
        if (response.success) {
            alert(response.message); // Show success message
            closeModal(); // Close the modal
            searchArtifact(); // Refresh the search results
        } else {
            alert(response.message); // Show error message
        }
    };

    // Send the ID of the artifact to delete
    const data = { id: id };
    xhr.send(JSON.stringify(data));
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

function deleteArtifact() {
    document.getElementById('artifact-id').value = item['ID'];
    const deleteButton = document.getElementById('delete-button');
    if (selectedArtifact && selectedArtifact['ID'] === item['ID']) {
        selectedArtifact = null;
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


    if (deleteButton) {
        deleteButton.remove();
    }

    const newDeleteButton = document.createElement('button');
    newDeleteButton.id = 'delete-button';
    newDeleteButton.textContent = 'Delete';
    newDeleteButton.onclick = () => confirmDelete(item['ID']);

    highlightedItem.parentNode.insertBefore(newDeleteButton, highlightedItem.nextSibling);

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

