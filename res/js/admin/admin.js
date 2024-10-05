// admin.js

// -----------------------
// Adding Artifact Tab
// -----------------------

// Function to fetch and populate artifact options (Sections, Catalogs, Subcatalogs)
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

            // Event listener for Section change to update Catalog options
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

// Function to update Catalog options based on selected Section
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

// Event listener for Catalog change to update Subcatalog options
document.getElementById('catalog').addEventListener('change', (e) => {
    const catalogId = e.target.value;
    updateSubCatalogOptions(catalogId);
});

// Function to update Subcatalog options based on selected Catalog
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

// Fetch artifact options on initial load
document.addEventListener("DOMContentLoaded", fetchArtifactOptions);

// -----------------------
// Searching Artifact Tab
// -----------------------

let selectedArtifact = null;
let highlightedItem = null;
let editButtonVisible = true; // Flag to track edit button visibility

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

// Function to display search results
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

        listItem.onclick = (event) => {
            // Prevent triggering when clicking the checkbox
            if (event.target.tagName.toLowerCase() === 'input') return;
            toggleEditButton(item, listItem);
        };
        list.appendChild(listItem);
    });

    resultsContainer.appendChild(list);
}

// Function to toggle Multi-Select mode
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
        if (editButton) editButton.style.display = 'none'; // Hide the edit button
        if (delButton) delButton.style.display = 'none';
    } else {
        if (editButton) editButton.style.display = 'inline'; // Show the edit button if multi-select is disabled
        if (delButton) delButton.style.display = 'inline';
    }

    const buttonText = isMultiSelectEnabled ? 'Disable Multi-Select' : 'Enable Multi-Select';
    document.getElementById('toggle-multi-select').textContent = buttonText;
}

// Function to handle artifact selection and editing
function toggleEditButton(item, listItem) {
    if (isMultiSelectEnabled) {
        return; // Don't allow editing in multi-select mode
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

// Function to delete selected artifacts (multi-select)
function deleteSelectedArtifacts() {
    const selectedCheckboxes = document.querySelectorAll('.artifact-checkbox:checked');
    if (selectedCheckboxes.length === 0) {
        alert('No artifacts selected.');
        return;
    }

    const selectedIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.getAttribute('data-id'));

    if (confirm(`Are you sure you want to delete ${selectedIds.length} artifact(s)?`)) {
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
        xhr.onerror = function () {
            alert('An error occurred while deleting artifacts.');
        };
        xhr.send(JSON.stringify({ ids: selectedIds }));
    }
}

// -----------------------
// Single Selected Artifact Functionalities
// -----------------------

// Function to open the edit modal with artifact details
function openModal(item) {
    document.getElementById('artifact-id').value = item['ID'];
    document.getElementById('editName').value = item['Name'];
    document.getElementById('editDescription').value = item['Description'];
    document.getElementById('editScript').value = item['Script'] || ''; // Populate script textarea

    fetchSections(item['Section ID'], () => {
        fetchCatalogs(item['Section ID'], item['Catalogue ID'], () => {
            fetchSubcatalogs(item['Catalogue ID'], item['Subcatalogue ID']);
        });
    });

    document.getElementById('edit-modal').style.display = 'block';
}

// Function to delete a single artifact
function deleteArtifact(id) {
    // Confirm deletion with the user
    confirmDelete = confirm("Are you sure you want to delete this artifact?");
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
    xhr.onerror = function () {
        alert('An error occurred while deleting the artifact.');
    };

    // Send the ID of the artifact to delete
    const data = { id: id };
    xhr.send(JSON.stringify(data));
}

// Function to confirm deletion (optional, currently integrated within deleteArtifact)
function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this artifact?")) {
        deleteArtifact(id);
    }
}

// Function to fetch Sections for the edit modal
function fetchSections(selectedSectionId, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '../include/get.php', true);
    xhr.onload = function () {
        if (this.status === 200) {
            const data = JSON.parse(this.responseText);
            const sectionSelect = document.getElementById('editSection');
            sectionSelect.innerHTML = '<option value="">Select Section</option>'; // Reset options

            data.sections.forEach(section => {
                const option = document.createElement('option');
                option.value = section.section_id;
                option.textContent = section.section_name;
                if (parseInt(section.section_id) === parseInt(selectedSectionId)) {
                    option.selected = true;
                }
                sectionSelect.appendChild(option);
            });

            // Event listener for Section change to update Catalog options
            sectionSelect.removeEventListener('change', handleEditSectionChange);
            sectionSelect.addEventListener('change', handleEditSectionChange);

            if (typeof callback === 'function') callback();
        }
    };
    xhr.onerror = function () {
        alert('An error occurred while fetching sections.');
    };
    xhr.send();
}

// Handler for Section change in edit modal
function handleEditSectionChange(e) {
    const sectionId = e.target.value;
    const catalogSelect = document.getElementById('editCatalog');
    const subcatalogSelect = document.getElementById('editSubcatalog');

    if (sectionId) {
        fetch('/include/get.php?section_id=' + sectionId)
            .then(response => response.json())
            .then(data => {
                catalogSelect.innerHTML = '<option value="" selected disabled>Select Catalog</option>';
                if (data.catalogues.length > 0) {
                    data.catalogues.forEach(catalog => {
                        const option = document.createElement('option');
                        option.value = catalog.catalogue_id;
                        option.textContent = catalog.catalogue_name;
                        catalogSelect.appendChild(option);
                    });
                    catalogSelect.disabled = false;
                } else {
                    catalogSelect.disabled = true;
                }
                subcatalogSelect.innerHTML = '<option value="" selected disabled>Select Sub Catalog</option>';
                subcatalogSelect.disabled = true;
            })
            .catch(error => console.error('Error fetching catalog options:', error));
    } else {
        catalogSelect.innerHTML = '<option value="" selected disabled>Select Catalog</option>';
        catalogSelect.disabled = true;
        subcatalogSelect.innerHTML = '<option value="" selected disabled>Select Sub Catalog</option>';
        subcatalogSelect.disabled = true;
    }
}

// Function to fetch Catalogs for the edit modal
function fetchCatalogs(selectedSectionId, selectedCatalogId, callback) {
    if (!selectedSectionId) return;

    fetch('/include/get.php?section_id=' + selectedSectionId)
        .then(response => response.json())
        .then(data => {
            const catalogSelect = document.getElementById('editCatalog');
            catalogSelect.innerHTML = '<option value="" selected disabled>Select Catalog</option>';

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

                // Event listener for Catalog change to update Subcatalog options
                catalogSelect.removeEventListener('change', handleEditCatalogChange);
                catalogSelect.addEventListener('change', handleEditCatalogChange);
            }

            if (typeof callback === 'function') callback();
        })
        .catch(error => console.error('Error fetching catalogues:', error));
}

// Handler for Catalog change in edit modal
function handleEditCatalogChange(e) {
    const catalogId = e.target.value;
    fetchSubcatalogs(catalogId, null);
}

// Function to fetch Subcatalogs for the edit modal
function fetchSubcatalogs(selectedCatalogId, selectedSubcatalogId) {
    if (!selectedCatalogId) return;

    fetch('/include/get.php?catalog_id=' + selectedCatalogId)
        .then(response => response.json())
        .then(data => {
            const subcatalogSelect = document.getElementById('editSubcatalog');
            subcatalogSelect.innerHTML = '<option value="" selected disabled>Select Sub Catalog</option>';

            if (data.subcatalogues.length === 0) {
                const noSubcatalogOption = document.createElement('option');
                noSubcatalogOption.textContent = 'No subcatalogs available under this catalog';
                noSubcatalogOption.disabled = true;
                noSubcatalogOption.selected = true;
                subcatalogSelect.appendChild(noSubcatalogOption);
                subcatalogSelect.disabled = true;
            } else {
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
        })
        .catch(error => console.error('Error fetching subcatalogues:', error));
}

// -----------------------
// Save Changes Functionality (Including Script Update)
// -----------------------

// Function to save changes to an artifact and update the script.json
function saveChanges() {
    // Collect Artifact Data
    const id = document.getElementById('artifact-id').value;
    const name = document.getElementById('editName').value;
    const sectionId = document.getElementById('editSection').value;
    const catalogId = document.getElementById('editCatalog').value || null;
    const subcatalogId = document.getElementById('editSubcatalog').value || null;
    const description = document.getElementById('editDescription').value;

    // Collect Script Data
    const scriptContent = document.getElementById('editScript').value;

    // Prepare Artifact Data Payload
    const artifactData = {
        id: id,
        name: name,
        section_id: sectionId,
        catalog_id: catalogId,
        subcatalog_id: subcatalogId,
        description: description,
    };

    // Create XMLHttpRequest for Updating Artifact
    const xhrArtifact = new XMLHttpRequest();
    xhrArtifact.open('POST', '../include/editArtifact.php', true);
    xhrArtifact.setRequestHeader('Content-Type', 'application/json');

    xhrArtifact.onload = function () {
        if (xhrArtifact.status === 200) {
            const responseArtifact = JSON.parse(xhrArtifact.responseText);
            if (responseArtifact.success) {
                // Artifact updated successfully, proceed to update the script
                updateScript(id, name, scriptContent);
            } else {
                // Handle Artifact Update Failure
                alert('Artifact Update Failed: ' + responseArtifact.message);
            }
        } else {
            // Handle HTTP Errors for Artifact Update
            alert('Artifact Update Request Failed. Status Code: ' + xhrArtifact.status);
        }
    };

    xhrArtifact.onerror = function () {
        // Handle Network Errors for Artifact Update
        alert('An error occurred while updating the artifact.');
    };

    // Send Artifact Data
    xhrArtifact.send(JSON.stringify(artifactData));
}

// Function to update the script.json file via updateScript.php
function updateScript(artifactId, artifactName, scriptContent) {
    // Prepare Script Data Payload
    const scriptData = `artifact_id=${encodeURIComponent(artifactId)}&artifact_name=${encodeURIComponent(artifactName)}&script=${encodeURIComponent(scriptContent)}`;

    // Create XMLHttpRequest for Updating Script
    const xhrScript = new XMLHttpRequest();
    xhrScript.open('POST', '../include/updateScript.php', true);
    xhrScript.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhrScript.onload = function () {
        if (xhrScript.status === 200) {
            const responseScript = JSON.parse(xhrScript.responseText);
            if (responseScript.success) {
                // Both Artifact and Script Updated Successfully
                closeModal();
                searchArtifact();
                alert('Artifact and script updated successfully.');
            } else {
                // Handle Script Update Failure
                alert('Artifact updated, but failed to update script: ' + responseScript.error);
            }
        } else {
            // Handle HTTP Errors for Script Update
            alert('Script Update Request Failed. Status Code: ' + xhrScript.status);
        }
    };

    xhrScript.onerror = function () {
        // Handle Network Errors for Script Update
        alert('An error occurred while updating the script.');
    };

    // Send Script Data
    xhrScript.send(scriptData);
}

// -----------------------
// Modal Management
// -----------------------

// Function to close the edit modal
function closeModal() {
    document.getElementById('edit-modal').style.display = 'none';
}

// -----------------------
// Popup Management (Add Category, Section, etc.)
// -----------------------

const sectionBtn = document.getElementById('section-btn');
const catalogBtn = document.getElementById('catalog-btn');
const subcatBtn = document.getElementById('subcat-btn');

const sectionPopup = document.querySelector('.popup-section');
const catalogPopup = document.querySelector('.popup-catalog');
const subcatPopup = document.querySelector('.popup-subcat');

const popupOverlay = document.querySelector('.popup');
const closeIcons = document.querySelectorAll('.close');

// Function to show a specific popup
function showPopup(popup) {
    popupOverlay.style.display = 'flex';
    popup.style.display = 'block';
}

// Function to hide all popups
function hidePopup() {
    popupOverlay.style.display = 'none';
    sectionPopup.style.display = 'none';
    catalogPopup.style.display = 'none';
    subcatPopup.style.display = 'none';
}

// Event listeners to show popups
sectionBtn.addEventListener('click', () => showPopup(sectionPopup));
catalogBtn.addEventListener('click', () => showPopup(catalogPopup));
subcatBtn.addEventListener('click', () => showPopup(subcatPopup));

// Event listeners to close popups
closeIcons.forEach(icon => {
    icon.addEventListener('click', hidePopup);
});

// -----------------------
// Additional Event Listeners or Functions
// -----------------------

// You can add more functions here as needed for other tabs or functionalities

// -----------------------
// End of admin.js
// -----------------------


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

