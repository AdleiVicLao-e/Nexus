//Testing
function submitForm(action) {
    const formData = new FormData(document.getElementById('category-form'));
    formData.append('action', action);

    fetch('../include/addCategory.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())  // Parse the response as JSON
        .then(data => {
            document.getElementById('overlayMessage').textContent = data.message;
            showOverlay();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
}

function showOverlay() {
    document.getElementById('messageOverlay').style.display = 'block';
}

function closeOverlay() {
    console.log('closeOverlay triggered');
    document.getElementById('messageOverlay').style.display = 'none';
    setTimeout(() => {
        location.reload();  // Using location.reload() as an alternative
    }, 100);  // Small delay to ensure DOM is updated
}

// admin.js
let selectedArtifact = null;
let highlightedItem = null;
let isMultiSelectEnabled = false;
// Edit Media
let selectedMediaId = null;
document.addEventListener('DOMContentLoaded', function() {
    // -----------------------
// Adding Artifact Tab
// -----------------------

    document.getElementById('addArtifactForm').addEventListener('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        fetch('../include/addArtifact.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                try {
                    document.getElementById('overlay-message').innerText = data.message;

                    if (data.success) {
                        // Generate the QR code after successfully adding the artifact
                        generateQRCode(data.artifact_id, formData.get('artifact-name'));

                        // Collect script content to update
                        const scriptContent = document.getElementById('script').value;

                        // Call updateScript function after artifact addition
                        updateScript(data.artifact_id, formData.get('artifact-name'), scriptContent);

                        // Get the media input element directly
                        const mediaInput = document.getElementById('media-select');

                        // Check if there is a media file to upload
                        if (mediaInput.files.length > 0) {
                            uploadArtifactMedia(data.artifact_id, formData);
                        } else {
                            document.getElementById('overlay').style.display = 'block'; // Show overlay if no media
                        }

                        // Reset the form after successful submission
                        this.reset();

                    } else {
                        document.getElementById('overlay').style.display = 'block'; // Show overlay on failure
                    }
                } catch (e) {
                    console.error("Error parsing JSON:", e, data);
                    document.getElementById('overlay').style.display = 'block'; // Show overlay on parsing error
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('overlay').style.display = 'block'; // Show overlay on request error
            });
    });
// Fetch artifact options on initial load

    fetchArtifactOptions();

    document.addEventListener('DOMContentLoaded', () => {
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

        if (sectionBtn) {
            sectionBtn.addEventListener('click', () => showPopup(sectionPopup));
        }

        if (catalogBtn) {
            catalogBtn.addEventListener('click', () => showPopup(catalogPopup));
        }

        if (subcatBtn) {
            subcatBtn.addEventListener('click', () => showPopup(subcatPopup));
        }

        closeIcons.forEach(icon => {
            icon.addEventListener('click', hidePopup);
        });
    });
    document.getElementById('close-overlay').addEventListener('click', function() {
        document.getElementById('overlay').style.display = 'none';
    });
    // Event listener for Catalog change to update Subcatalog options
    document.getElementById('catalog').addEventListener('change', (e) => {
        const catalogId = e.target.value;
        updateSubCatalogOptions(catalogId);
    });


});


function uploadArtifactMedia(artifactId, formData) {
    // Create a FormData object to send the media file along with other parameters
    const mediaData = new FormData();
    mediaData.append('media-select', formData.get('media-select')); // Append the media file
    mediaData.append('newArtifactId', artifactId); // Use the new artifact ID
    mediaData.append('sectionId', formData.get('section')); // Append the section ID
    mediaData.append('catalogId', formData.get('catalog') || 0); // Append catalog ID
    mediaData.append('subCatalogId', formData.get('sub-catalog') || 0); // Append sub-catalog ID
    mediaData.append('artifactName', formData.get('artifact-name')); // Append artifact name

    // Create XMLHttpRequest for Uploading Media
    const xhrMedia = new XMLHttpRequest();
    xhrMedia.open('POST', '../include/addArtifactMedia.php', true);

    xhrMedia.onload = function () {
        if (xhrMedia.status === 200) {
            try {
                const responseMedia = JSON.parse(xhrMedia.responseText);
                if (responseMedia.success) {
                    console.log("Media uploaded successfully:", responseMedia.message);
                    document.getElementById('overlay-message').innerText = responseMedia.message;
                } else {
                    console.error("Error uploading media:", responseMedia.message);
                    document.getElementById('overlay-message').innerText = responseMedia.message;
                }
            } catch (e) {
                console.error("Error parsing media response:", e);
            }
        } else {
            console.error('Media Upload Request Failed. Status Code:', xhrMedia.status);
        }

        // Show overlay regardless of upload success or failure
        document.getElementById('overlay').style.display = 'block';
    };

    xhrMedia.onerror = function () {
        console.error('An error occurred while uploading the media.');
        document.getElementById('overlay').style.display = 'block'; // Show overlay on error
    };

    // Send Media Data
    xhrMedia.send(mediaData);
}


// -----------------------
// QR Code Generate
// -----------------------

function generateQRCode(artifactId, artifactName) {
    var qrCodeContainer = document.getElementById("qrcode");
    qrCodeContainer.innerHTML = "";

    $("#qrcode").qrcode({
        text: String(artifactId),
        width: 200,
        height: 200,
    });

    setTimeout(() => {
        var qrCodeCanvas = document.querySelector("#qrcode canvas");
        if (qrCodeCanvas) {
            var dataURL = qrCodeCanvas.toDataURL("image/png");

            fetch('../include/saveQRCode.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    artifactId: artifactId,
                    artifactName: artifactName,
                    imageData: dataURL
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log("QR code saved successfully:", data.message);
                    } else {
                        console.error("Error saving QR code:", data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    }, 1000);
}

// -----------------------
// Searching Artifact Tab
// -----------------------



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
    const deleteButton = document.getElementById('delete-button');
    const printButton = document.getElementById('print-button');


    if (selectedArtifact && selectedArtifact['ID'] === item['ID']) {
        selectedArtifact = null;
        if (editButton) editButton.remove();
        if (deleteButton) deleteButton.remove(); 
        if (printButton) printButton.remove();
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
    if (deleteButton) {
        deleteButton.remove(); 
    }
    if (printButton) {
        printButton.remove(); 
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

    const newDeleteButton = document.createElement('button');
    newDeleteButton.id = 'delete-button';
    newDeleteButton.classList.add('delete-button'); // Add class for styling
    newDeleteButton.innerHTML = '<i class="fas fa-trash" style="color: red; font-size: 20px; margin-left: -120px;"></i>'; // Font Awesome delete icon
    newDeleteButton.style.background = 'none'; // Remove default button styles
    newDeleteButton.style.border = 'none'; // Remove border
    newDeleteButton.style.cursor = 'pointer'; // Change cursor to pointer
    newDeleteButton.onclick = () => deleteArtifact(item['ID']); // Call the existing delete function


     // Create a new print button with an icon
     const newPrintButton = document.createElement('button');
     newPrintButton.id = 'print-button';
     newPrintButton.classList.add('print-button'); // Add class for styling
     newPrintButton.innerHTML = '<i class="fas fa-print" style="color: #073066; font-size: 20px; margin-left:-210px;"></i>'; // Font Awesome print icon
     newPrintButton.style.background = 'none'; // Remove default button styles
     newPrintButton.style.border = 'none'; // Remove border
     newPrintButton.style.cursor = 'pointer'; // Change cursor to pointer
     newPrintButton.onclick = () => printQRCode(item); // Call the printQRCode function
  
    // Append the edit button to the list item
    listItem.appendChild(newEditButton);
    listItem.appendChild(newDeleteButton);
    listItem.appendChild(newPrintButton);
}
// Function to fetch and populate artifact options (Sections, Catalogs, Subcatalogs)
function fetchArtifactOptions() {
    fetch('../include/get.php')
        .then(response => response.json())
        .then(data => {
            const sectionSelect = document.getElementById('section');
            sectionSelect.innerHTML = '<option value="">Select Section</option>';

            // Check if sections exist in data
            if (data.sections && data.sections.length > 0) {
                data.sections.forEach(section => {
                    const option = document.createElement('option');
                    option.value = section.section_id;
                    option.textContent = section.section_name;
                    sectionSelect.appendChild(option);
                });
            }

            // Event listener for Section change to update Catalog options
            sectionSelect.addEventListener('change', (e) => {
                const sectionId = e.target.value;
                if (sectionId) {
                    console.log(sectionId + " bro");
                    updateCatalogOptions(sectionId);
                    document.getElementById('catalog').disabled = false;
                } else {
                    document.getElementById('catalog').disabled = true;
                }
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
                console.log(data.catalogues);
                catalogSelect.innerHTML = '<option value="" selected disabled>Select Catalog</option>';
                if (data.catalogues) {
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



// Function to update Subcatalog options based on selected Catalog
function updateSubCatalogOptions(catalogId) {
    const subCatalogSelect = document.getElementById('sub-catalog');

    if (catalogId) {
        fetch('/include/get.php?catalog_id=' + catalogId)
            .then(response => response.json())
            .then(data => {
                subCatalogSelect.innerHTML = '<option value="" selected disabled>Select Sub Catalog</option>';
                if (data.subcatalogues) {
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
        xhr.send(JSON.stringify({ ids: selectedIds, deleteMedia: true }));
    }
}

// -----------------------
// Single Selected Artifact Functionalities
// -----------------------

// Function to open the edit modal with artifact details
function openModal(item) {
    // Populate modal fields
    document.getElementById('artifact-id').value = item['ID'];
    document.getElementById('editName').value = item['Name'];
    document.getElementById('editDescription').value = item['Description'];
    document.getElementById('editScript').value = item['Script'] || ''; // Populate script textarea

    // Fetch sections, catalogs, and subcatalogs and set them in the dropdowns
    fetchSections(item['Section Name'],() => {
        fetchCatalogs(item['Section Name'], item['Catalogue Name'], () => {
            fetchSubcatalogs(item['Catalogue Name'], item['Subcatalogue Name']);
        });
    });

    // Display the modal
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
    const data = { id: id, deleteMedia: true }; // Also indicate media should be deleted
    xhr.send(JSON.stringify(data));
}

// Function to confirm deletion (optional, currently integrated within deleteArtifact)
function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this artifact?")) {
        deleteArtifact(id);
    }
}

// Function to fetch Sections for the edit modal
function fetchSections(selectedSectionName, callback) {
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

                if (section.section_name === selectedSectionName) {
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
// Function to fetch Catalogs based on Section Name
// Function to fetch Catalogs based on Section Name
function fetchCatalogs(selectedSectionName, selectedCatalogName, callback) {
    if (!selectedSectionName) {
        return; // Skip if the selected section name is empty
    }

    const data = {
        sectionName: selectedSectionName,
        catalogName: selectedCatalogName && selectedCatalogName !== 'N/A' ? selectedCatalogName : null
    };
    let sectionId;
    let catalogId;
    fetch('/include/getids.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.ok ? response.json() : Promise.resolve(null))
        .then(data => {
            sectionId = data?.sectionId || null;
            catalogId = data?.catalogId || null;

            if (!sectionId) {
                return; // Exit if no section ID is found
            }

            // Fetch catalogs if catalogId exists, otherwise resolve without fetching
            return catalogId ? fetch(`/include/get.php?section_id=${sectionId}`) : Promise.resolve(null);
        })
        .then(response => response?.ok ? response.json() : Promise.resolve(null))
        .then(data => {
            const catalogSelect = document.getElementById('editCatalog');
            catalogSelect.innerHTML = '<option value="" selected disabled>Select Catalog</option>';

            // Check if 'data' and 'data.catalogues' exist
            if (!data || !data.catalogues || data.catalogues.length === 0) {
                // No catalogs available or error fetching catalogs
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
                    if (catalog.catalogue_id === catalogId) {
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
        .catch(error => console.error('Error fetching catalogs:', error));
}

// Handler for Catalog change in edit modal
function handleEditCatalogChange(e) {
    const catalogName = e.target.value;
    fetchSubcatalogs(catalogName, null);
}

// Function to fetch Subcatalogs
function fetchSubcatalogs(selectedCatalogName, selectedSubcatalogName) {
    if (!selectedCatalogName) {
        return; // Skip if no catalog name is provided
    }

    const data = {
        catalogName: selectedCatalogName,
        subcatalogName: selectedSubcatalogName
    };

    let catalogId;
    let subCatalogId;

    fetch('/include/getids.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.ok ? response.json() : Promise.resolve(null))
        .then(ids => {
            const subcatalogSelect = document.getElementById('editSubcatalog');
            subcatalogSelect.innerHTML = '<option value="" selected disabled>Select Sub Catalog</option>';

            catalogId = ids?.catalogId || null;
            subCatalogId = ids?.subCatalogId || null;

            if (!catalogId) {
                subcatalogSelect.disabled = true; // No catalog ID, disable subcatalog
                return;
            }

            return fetch(`/include/get.php?catalog_id=${catalogId}`);
        })
        .then(response => response?.ok ? response.json() : Promise.resolve(null))
        .then(data => {
            const subcatalogSelect = document.getElementById('editSubcatalog');
            subcatalogSelect.innerHTML = '<option value="" selected disabled>Select Sub Catalog</option>';

            console.log(data); // Log the entire data object to see its structure
            if (!data || !data.subCatalogues || data.subCatalogues.length === 0) {
                const noSubcatalogOption = document.createElement('option');
                noSubcatalogOption.textContent = 'No subcatalogs available under this catalog';
                noSubcatalogOption.disabled = true;
                noSubcatalogOption.selected = true;
                subcatalogSelect.appendChild(noSubcatalogOption);
                subcatalogSelect.disabled = true;
            } else if (data.subcatalogues) {
                data.subcatalogues.forEach(subcatalog => {
                    const option = document.createElement('option');
                    option.value = subcatalog.subcat_id;
                    option.textContent = subcatalog.subcat_name;
                    if (subcatalog.subcat_id === subCatalogId) {
                        option.selected = true;
                    }
                    subcatalogSelect.appendChild(option);
                });
                subcatalogSelect.disabled = false;
            } else {
                console.error('subcatalogues is undefined or null');
            }

        })
        .catch(error => console.error('Error fetching subcatalogs:', error));
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
            try {
                const responseArtifact = JSON.parse(xhrArtifact.responseText);
                if (responseArtifact.success) {
                    // Artifact updated successfully, proceed to update the script
                    if (scriptContent && scriptContent.trim() !== "") {
                        updateScript(id, name, scriptContent);
                    }
                    updateArtifactMedia(id, sectionId, catalogId, subcatalogId, name);
                    updateQRCode(id, name);
                } else {
                    // Handle Artifact Update Failure
                    console.log(`Artifact Update Failed: ID=${id}, Response Message=${responseArtifact.message}, Invalid Fields=${JSON.stringify(responseArtifact.invalidFields)}`);
                }
            } catch (e) {
                console.log('Error parsing response: ' + e.message); // Handle JSON parsing error
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
function updateArtifactMedia(artifactId, sectionId, catalogId, subcatalogId, artifactName) {
    // Construct the filename based on the IDs
    let fileName;
    if (catalogId === null && subcatalogId === null) {
        fileName = artifactId + "." + sectionId;
    } else if (subcatalogId === null) {
        fileName = artifactId + "." + sectionId + "." + catalogId;
    } else {
        fileName = artifactId + "." + sectionId + "." + catalogId + "." + subcatalogId;
    }

    const newFileName = fileName + "-" + artifactName;

    // Send the request to update the artifact media
    fetch('../include/updateArtifactMedia.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            artifactId: artifactId,
            newFileName: newFileName,
            fileExt: 'mp4' // Example extension
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log("Artifact media updated successfully:", data.message);
            } else {
                console.log("Searched Path: ", data.searchedPath);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });

}


function updateQRCode(artifactId, newArtifactName) {
    // Send a request to rename the QR code file based on artifactId
    fetch('../include/renameQRCode.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            artifactId: artifactId,       // The artifact ID remains the same
            newArtifactName: newArtifactName  // The new artifact name
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log("QR code filename updated successfully:", data.message);
            } else if (data.message !== 'QR code file not found.') {
                // Only log the error if it's not about the missing QR code
                console.error("Error updating QR code filename:", data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
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
            try {
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
            } catch (e) {
                alert('Error parsing script response: ' + e.message); // Handle JSON parsing error
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
// Printing QR Code
// -----------------------
function printQRCode(item) {
    var artifactId = item['ID'];
    var artifactName = item['Name'];
    
    if (artifactId) {
        // Construct various formats for the QR code image source path
        var formats = [
            `../qr/${artifactId}-${artifactName.replace(/\s+/g, '_')}.png`, // Replace spaces with underscores
            `../qr/${artifactId}-${artifactName}.png`, // Original with spaces
            `../qr/${artifactId}-${artifactName.replace(/_/g, ' ')}.png`, // Replace underscores with spaces
            `../qr/${artifactId}-${artifactName.replace(/\s+/g, '_').replace(/_/g, ' ')}.png`, // Mixed format
        ];

        // Open a new window for printing
        var printWindow = window.open("", "", "width=800,height=600");
        printWindow.document.write(`
          <html>
            <head>
              <title>${artifactName} QR Code</title>
              <style>
                body {
                  display: flex;
                  justify-content: center;
                  align-items: center;
                  height: 100vh;
                  margin: 0;
                }
                img {
                  max-width: 100%;
                  height: auto;
                }
              </style>
            </head>
            <body>
              <img id="qrCodeImage" src="" alt="QR Code" width="400" height="400"/>
            </body>
          </html>
        `);
        
        printWindow.document.close();

        const qrCodeImage = printWindow.document.getElementById('qrCodeImage');

        // Function to try loading the images sequentially
        const tryLoadingImages = (index) => {
            if (index < formats.length) {
                qrCodeImage.src = formats[index]; // Set the image source
                qrCodeImage.onload = function() {
                    printWindow.focus(); // Focus on the print window
                    printWindow.print(); // Open the print dialog
                    printWindow.onafterprint = () => {
                        printWindow.close(); // Close the window after printing
                    };
                };

                // If this image fails to load, try the next one
                qrCodeImage.onerror = function() {
                    tryLoadingImages(index + 1); // Try the next format
                };
            } else {
                // If all attempts fail, show an alert
                alert("Failed to load QR code image.");
                printWindow.close(); // Close the window if all attempts fail
            }
        };

        // Start the loading attempts
        tryLoadingImages(0);
    } else {
        alert("Please enter a value.");
    }
}

function selectMedia(id) {
    // Hide the popup
    var popup = document.getElementById('edit-media-popup');
    popup.style.display = 'none';

    // Display currently selected media ID
    selectedMediaId = id;
    var mediaId = document.getElementById('selected-media-id');
    mediaId.textContent = 'Media ID: ' + selectedMediaId;

    // Display fields and button
    var titleField = document.getElementById('new-title-field');
    var descField = document.getElementById('new-desc-field');
    var updateBtn = document.getElementById('update-media-btn');

    titleField.style.display = 'block';
    descField.style.display = 'block'; 
    updateBtn.style.display = 'block'; 

    // Get the div containing the details by its ID
    var div = document.getElementById(id);
    
    // Get the content inside it
    var titleContent = div.querySelector('#media-title').textContent.replace("Title:", "").trim();
    var descriptionContent = div.querySelector('#media-description').textContent.replace("Description:", "").trim();

    var newTitleField = document.getElementById('new-media-title');
    var newDescField = document.getElementById('new-media-description');

    newTitleField.value = titleContent;
    newDescField.value = descriptionContent;
}

function updateMedia() {
    // Prevent the form from submitting normally
    document.getElementById('media-form').addEventListener('submit', function(event) {
        event.preventDefault();

        // Collect values from the input fields
        const mediaId = selectedMediaId;  // Ensure selectedMediaId is defined
        const newTitle = document.getElementById('new-media-title').value; // Get the title value
        const newDescription = document.getElementById('new-media-description').value; // Get the description value

        // Create a FormData object to send the data
        const formData = new FormData();
        formData.append('media_id', mediaId);
        formData.append('new-media-title', newTitle);
        formData.append('new-media-description', newDescription);

        // Send the data to the server using fetch
        fetch('../include/editMedia.php', {
            method: 'POST',
            body: formData,  // Send the FormData object
        })
        .then(response => response.json())  // Expecting a JSON response from PHP
        .then(data => {
            console.log('Response from server:', data);
            if (data.success) {
                alert("Update successful: " + data.message);
            } else {
                alert("Update failed: " + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
}

function deleteMedia(id) {
    // Send the data to the server using fetch
    fetch('../include/deleteMedia.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded', 
        },
        body: new URLSearchParams({ 'id': id }) 
    })
    .then(response => response.json()) 
    .then(data => {
        if (data.success) {
            // Alert for successful deletion
            alert("Delete successful, ID: " + id);
        } else {
            // Alert for failed deletion
            alert("Delete failed. ID: " + id + ". Error: " + data.message);
        }
        location.reload();
    })
    .catch(error => {
        console.log(error);
    });

    // Hide the popup
    var popup = document.getElementById('edit-media-popup');
    popup.style.display = 'none';
}

// Edit and Delete Section, Catalog, Subcatalog

function saveSectionChanges() {
    var section = document.getElementById('edit-select-section');
    var sectionIdValue = section.options[section.selectedIndex].value;
    var newSection = document.getElementById('editSectionName');
    var newSectionName = newSection.value;

    if (newSectionName !== "") {
        // Send the data to the server using fetch
        fetch('../include/updateSection.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded', 
            },
            body: new URLSearchParams({ 
                'id': sectionIdValue,
                'newName': newSectionName
             }) 
        })
        .then(response => response.json()) 
        .then(data => {
            if (data.success) {
                // Alert for successful deletion
                alert("Rename Successful \nSection ID: " + sectionIdValue + " \nNew Section Name: " + newSectionName);
            } else {
                // Alert for failed deletion
                alert("Rename failed. Section ID: " + sectionIdValue + ". \nError: " + data.message);
            }
            location.reload();
        })
        .catch(error => {
            console.log(error);
        });
    } else {
        alert("Name cannot be empty");
    }
}

function deleteSection() {
    var section = document.getElementById('edit-select-section');
    var sectionIdValue = section.options[section.selectedIndex].value;

    // Send the data to the server using fetch
    fetch('../include/deleteSection.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded', 
        },
        body: new URLSearchParams({ 'id': sectionIdValue }) 
    })
    .then(response => response.json()) 
    .then(data => {
        if (data.success) {
            // Alert for successful deletion
            alert("Delete successful, \nSection ID: " + sectionIdValue);
        } else {
            // Alert for failed deletion
            alert("Delete failed. Section ID: " + sectionIdValue + ". \nError: " + data.message);
        }
        location.reload();
    })
    .catch(error => {
        console.log(error);
    });
}

function saveCatalogChanges() {
    var catalog = document.getElementById('edit-select-catalog');
    var catalogIdValue = catalog.options[catalog.selectedIndex].value;
    var newCatalog = document.getElementById('editCatalogName');
    var newCatalogName = newCatalog.value;

    if (newCatalogName !== "") {
        // Send the data to the server using fetch
        fetch('../include/updateCatalog.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded', 
            },
            body: new URLSearchParams({ 
                'id': catalogIdValue,
                'newName': newCatalogName
             }) 
        })
        .then(response => response.json()) 
        .then(data => {
            if (data.success) {
                // Alert for successful deletion
                alert("Rename Successful \nCatalog ID: " + catalogIdValue + " \nNew Catalog Name: " + newCatalogName);
            } else {
                // Alert for failed deletion
                alert("Rename Failed. Catalog ID: " + catalogIdValue + ". \nError: " + data.message);
            }
            location.reload();
        })
        .catch(error => {
            console.log(error);
        });
    } else {
        alert("Name cannot be empty");
    }
}

function deleteCatalog() {
    var catalog = document.getElementById('edit-select-catalog');
    var catalogIdValue = catalog.options[catalog.selectedIndex].value;

    // Send the data to the server using fetch
    fetch('../include/deleteCatalog.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded', 
        },
        body: new URLSearchParams({ 'id': catalogIdValue }) 
    })
    .then(response => response.json()) 
    .then(data => {
        if (data.success) {
            // Alert for successful deletion
            alert("Delete successful, Section ID: " + catalogIdValue);
        } else {
            // Alert for failed deletion
            alert("Delete failed. Section ID: " + catalogIdValue + ". Error: " + data.message);
        }
        location.reload();
    })
    .catch(error => {
        console.log(error);
    });
}

function saveSubcatalogChanges() {
    var subcatalog = document.getElementById('edit-select-subcatalog');
    var subcatalogIdValue = subcatalog.options[subcatalog.selectedIndex].value;
    var newSubcatalog = document.getElementById('editSubcatalogName');
    var newSubcatalogName = newSubcatalog.value;

    if (newSubcatalogName !== "") {
        // Send the data to the server using fetch
        fetch('../include/updateSubcatalog.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded', 
            },
            body: new URLSearchParams({ 
                'id': subcatalogIdValue,
                'newName': newSubcatalogName
             }) 
        })
        .then(response => response.json()) 
        .then(data => {
            if (data.success) {
                // Alert for successful deletion
                alert("Rename Successful \nSubcatalog ID: " + subcatalogIdValue + " \nNew Subcatalog Name: " + newSubcatalogName);
            } else {
                // Alert for failed deletion
                alert("Rename Failed. Subcatalog ID: " + subcatalogIdValue + ". \nError: " + data.message);
            }
            location.reload();
        })
        .catch(error => {
            console.log(error);
        });
    } else {
        alert("Name cannot be empty");
    }
}

function deleteSubcat() {
    var subcatalog = document.getElementById('edit-select-subcatalog');
    var subcatalogIdValue = subcatalog.options[subcatalog.selectedIndex].value;

    // Send the data to the server using fetch
    fetch('../include/deleteSubcatalog.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded', 
        },
        body: new URLSearchParams({ 'id': subcatalogIdValue }) 
    })
    .then(response => response.json()) 
    .then(data => {
        if (data.success) {
            // Alert for successful deletion
            alert("Delete successful, Section ID: " + subcatalogIdValue);
        } else {
            // Alert for failed deletion
            alert("Delete failed. Section ID: " + subcatalogIdValue + ". Error: " + data.message);
        }
        location.reload();
    })
    .catch(error => {
        console.log(error);
    });
}

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