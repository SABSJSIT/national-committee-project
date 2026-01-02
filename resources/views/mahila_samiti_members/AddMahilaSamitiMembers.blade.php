@extends('includes.layouts.super_admin')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Bootstrap 5 CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    .toast-container {
        position: fixed;
        top: 100px; /* Ensure it appears below header */
        right: 20px;
        z-index: 9999;
    }
    .card {
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }
    .table th {
        background-color: #f8f9fa;
        white-space: nowrap;
        font-size: 0.875rem;
        padding: 0.5rem 0.25rem;
    }
    .table td {
        font-size: 0.875rem;
        padding: 0.5rem 0.25rem;
        vertical-align: middle;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .photo-preview {
        max-width: 50px;
        max-height: 50px;
        object-fit: cover;
    }
    .required {
        color: red;
    }
    /* Responsive table without horizontal scroll */
    .table-responsive {
        overflow-x: hidden !important;
    }
    
    /* Compact table styling */
    .table-compact th,
    .table-compact td {
        padding: 0.375rem 0.25rem;
        font-size: 0.8rem;
        line-height: 1.2;
    }
    
    /* Hide less important columns on smaller screens */
    @media (max-width: 1200px) {
        .hide-lg {
            display: none !important;
        }
    }
    
    @media (max-width: 992px) {
        .hide-md {
            display: none !important;
        }
        .table th, .table td {
            padding: 0.375rem 0.2rem;
            font-size: 0.75rem;
        }
    }
    
    @media (max-width: 768px) {
        .hide-sm {
            display: none !important;
        }
        .table th, .table td {
            padding: 0.25rem 0.15rem;
            font-size: 0.7rem;
        }
        .photo-preview {
            max-width: 35px;
            max-height: 35px;
        }
    }
    
    /* Action buttons styling */
    .action-buttons {
        display: flex;
        gap: 0.25rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .action-buttons .btn {
        padding: 0.2rem 0.4rem;
        font-size: 0.7rem;
        line-height: 1;
    }
    
    /* Text truncation for long content */
    .text-truncate-custom {
        max-width: 120px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    /* Ensure modals are always visible */
    .modal {
        z-index: 2050 !important;
    }
    .modal-backdrop {
        z-index: 2040 !important;
    }
    .modal-header {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        padding: 1rem !important;
        border-bottom: 1px solid #dee2e6 !important;
        background-color: #ffffff !important;
        position: relative !important;
        z-index: 2051 !important;
    }
    .modal-title {
        font-size: 1.25rem;
        font-weight: 500;
        line-height: 1.5;
        color: #212529;
        z-index: 2051 !important;
    }
    .modal-dialog {
        z-index: 2050 !important;
    }
</style>

<div class="container-fluid mt-4">
    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-users"></i> Mahila Samiti Members</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Members Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <label for="sessionSelect" class="mb-0">Select Session:</label>
                        <select id="sessionSelect" class="form-select w-auto">
                            <option value="">-- Select Session --</option>
                        </select>
                        <button id="loadSessionBtn" class="btn btn-secondary btn-sm">Load</button>
                        <small class="text-muted ms-3">Data is being loaded as per selected session only.</small>
                    </div>
                    
                    <!-- Search Bar and Filters -->
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-6"> 
                                <div class="input-group mb-2">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="searchInput" placeholder="Search by name, designation, MID, phone, city, or anchal..." autocomplete="off">
                                    <button class="btn btn-outline-secondary" type="button" id="clearSearchBtn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Search works across all visible data in the table</small>
                            </div>
                            <div class="col-md-6 text-end">
                                <span id="searchResults" class="text-muted small"></span>
                            </div>
                        </div>
                        
                        <!-- Filter Dropdowns -->
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label for="anchalFilter" class="form-label small">Filter by Anchal:</label>
                                <select id="anchalFilter" class="form-select form-select-sm">
                                    <option value="">All Anchals</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="stateFilter" class="form-label small">Filter by State:</label>
                                <select id="stateFilter" class="form-select form-select-sm">
                                    <option value="">All States</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button id="clearFiltersBtn" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-filter"></i> Clear Filters
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3 d-flex gap-2">
                        <button id="exportExcelBtn" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#excelFieldsModal"><i class="fas fa-file-excel"></i> Export Excel</button>
                        <button id="exportPdfBtn" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#pdfFieldsModal"><i class="fas fa-file-pdf"></i> Export PDF</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-compact" id="membersTable">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">S.No</th>
                                    <th style="width: 15%;">Name</th>
                                    <th style="width: 10%;" class="hide-sm">Type</th>
                                    <th style="width: 12%;">Designation</th>
                                    <th style="width: 10%;" class="hide-lg">MID</th>
                                    <th style="width: 12%;" class="hide-md">Anchal</th>
                                    <th style="width: 10%;">Phone</th>
                                    <th style="width: 15%;">Address</th>
                                    <th style="width: 11%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="membersTableBody">
                                <!-- Data will be loaded here for the selected session -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

// SheetJS CDN
const sheetJsScript = document.createElement('script');
sheetJsScript.src = 'https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js';
document.head.appendChild(sheetJsScript);

let dropdownData = {};
let isDownloading = false; // Prevent multiple downloads

document.addEventListener('DOMContentLoaded', function() {
    // Load available sessions into the dropdown
    loadSessions();
    
    // Initialize search functionality
    initializeSearch();
    
    document.getElementById('loadSessionBtn').addEventListener('click', function() {
        const session = document.getElementById('sessionSelect').value;
        if (!session) {
            showToast('Please select a session first', 'info');
            return;
        }
        loadMembersBySession(session);
    });

    // Excel Export
    document.getElementById('exportExcelBtn').addEventListener('click', function() {
        // Open the modal to select fields
        const modal = new bootstrap.Modal(document.getElementById('excelFieldsModal'));
        modal.show();
    });

    document.getElementById('generateExcelBtn').addEventListener('click', function() {
        if (isDownloading) {
            showToast('Download already in progress', 'info');
            return;
        }

        const selectedFields = [];
        document.querySelectorAll('#excelFieldsForm input[type="checkbox"]:checked').forEach(checkbox => {
            selectedFields.push(checkbox.value);
        });

        if (selectedFields.length === 0) {
            showToast('Please select at least one field', 'info');
            return;
        }

        // Close the modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('excelFieldsModal'));
        if (modal) modal.hide();

        const session = document.getElementById('sessionSelect').value;
        if (!session) {
            showToast('Please select a session first', 'info');
            return;
        }

        isDownloading = true; // Set downloading flag

        // Fetch all members data for selected session
        fetch(`/api/mahila-samiti-members?session=${encodeURIComponent(session)}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success && Array.isArray(res.data)) {
                const members = res.data;

                // Define the sequence of columns
                const columnSequence = [
                    'session',
                    'name',
                    'name_hindi',
                    'husband_name',
                    'husband_name_hindi',
                    'father_name',
                    'father_name_hindi',
                    'type',
                    'designation',
                    'anchal_name',
                    'anchal_code',
                    'mid',
                    'address',
                    'address_hindi',
                    'city',
                    'state',
                    'pincode',
                    'mobile_number',
                    'wtp_number',
                    'ex_post',
                    'remarks'
                ];

                // Prepare data for Excel based on selected fields and sequence
                const excelData = members.map(m => {
                    const row = {};
                    columnSequence.forEach(field => {
                        if (selectedFields.includes(field)) {
                            row[field] = m[field] || '';
                        }
                    });
                    return row;
                });

                // Create workbook and worksheet
                const ws = XLSX.utils.json_to_sheet(excelData);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Members');

                // Write file
                XLSX.writeFile(wb, `mahila_samiti_members_${session}.xlsx`);
                showToast('Excel file exported successfully', 'success');
            } else {
                showToast('No data found for selected session', 'info');
            }
        })
        .catch(err => {
            console.error('Error exporting Excel:', err);
            showToast('Error exporting Excel file', 'error');
        })
        .finally(() => {
            isDownloading = false; // Reset downloading flag
        });
    });

    // PDF Export with field selection and separate tables by category
    document.getElementById('generatePdfBtn').addEventListener('click', function() {
        const selectedFields = [];
        document.querySelectorAll('#pdfFieldsForm input[type="checkbox"][name="field"]:checked').forEach(checkbox => {
            selectedFields.push(checkbox.value);
        });

        if (selectedFields.length === 0) {
            showToast('Please select at least one field', 'info');
            return;
        }

        // Check if separate tables option is selected
        const separateTablesCheckbox = document.getElementById('pdfSeparateTables');
        const useSeparateTables = separateTablesCheckbox ? separateTablesCheckbox.checked : true;

        // Close the modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('pdfFieldsModal'));
        if (modal) modal.hide();

        const session = document.getElementById('sessionSelect').value;
        if (!session) {
            showToast('Please select a session first', 'info');
            return;
        }

        // Check if we have filtered/displayed data
        if (!currentDisplayedData || currentDisplayedData.length === 0) {
            showToast('No data to export', 'info');
            return;
        }

        // Generate PDF with currently displayed/filtered data
        const params = new URLSearchParams();
        
        // Send member IDs of currently displayed data
        const memberIds = currentDisplayedData.map(m => m.id);
        memberIds.forEach(id => params.append('member_ids[]', id));
        
        params.append('session', session);
        if (useSeparateTables) {
            params.append('separate_tables', '1');
        }
        
        // Show info about what's being exported
        const exportCount = memberIds.length;
        const totalCount = currentMembersData.length;
        if (exportCount < totalCount) {
            showToast(`Exporting ${exportCount} filtered members out of ${totalCount} total`, 'info');
        }
        
        window.open('/mahila-samiti-members/export-fpdf?' + params.toString(), '_blank');
    });

    document.getElementById('sessionSelect').addEventListener('change', function() {
        const session = this.value;
        const exportExcelBtn = document.getElementById('exportExcelBtn');
        const exportPdfBtn = document.getElementById('exportPdfBtn');

        if (session) {
            exportExcelBtn.style.display = 'inline-block';
            exportPdfBtn.style.display = 'inline-block';
        } else {
            exportExcelBtn.style.display = 'none';
            exportPdfBtn.style.display = 'none';
        }
    });

    // Initially hide the export buttons
    document.addEventListener('DOMContentLoaded', function() {
        const exportExcelBtn = document.getElementById('exportExcelBtn');
        const exportPdfBtn = document.getElementById('exportPdfBtn');
        exportExcelBtn.style.display = 'none';
        exportPdfBtn.style.display = 'none';
    });
});

function loadSessions() {
    // Use new dropdown-data-all endpoint which returns all sessions for list page
    fetch('/api/mahila-samiti-members-dropdown-data-all', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.data && Array.isArray(data.data.sessions)) {
            const sel = document.getElementById('sessionSelect');
            let activeSessionValue = null;
            
            data.data.sessions.forEach(s => {
                const opt = document.createElement('option');
                // s is now an object with 'name' and 'is_active' properties
                opt.value = s.name;
                opt.textContent = s.name + (s.is_active ? ' (Active)' : '');
                
                // If this session is active, mark it for selection
                if (s.is_active) {
                    activeSessionValue = s.name;
                }
                
                sel.appendChild(opt);
            });
            
            // Auto-select the active session
            if (activeSessionValue) {
                sel.value = activeSessionValue;
                // Trigger change event to show export buttons
                sel.dispatchEvent(new Event('change'));
                // Auto-load the active session data
                loadMembersBySession(activeSessionValue);
            }
        } else {
            console.warn('Dropdown data endpoint did not return sessions as expected:', data);
        }
    })
    .catch(err => {
        console.warn('Could not load dropdown data for sessions.', err);
    });
    
    // Load filter dropdown data from database
    loadFilterDropdownsFromDatabase();
}

// Load filter dropdowns from database
function loadFilterDropdownsFromDatabase() {
    // Fetch unique anchals and states from database
    fetch('/api/mahila-samiti-members-filter-options', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.data) {
            populateFilterDropdownsFromDatabase(data.data);
        } else {
            console.warn('Could not load filter options from database:', data);
        }
    })
    .catch(err => {
        console.error('Error loading filter options:', err);
    });
}

// Populate filter dropdowns with database data
function populateFilterDropdownsFromDatabase(data) {
    // Populate Anchal dropdown
    const anchalFilter = document.getElementById('anchalFilter');
    const currentAnchal = anchalFilter.value;
    anchalFilter.innerHTML = '<option value="">All Anchals</option>';
    
    if (data.anchals && Array.isArray(data.anchals)) {
        data.anchals.forEach(anchal => {
            if (anchal) { // Only add non-empty values
                const option = document.createElement('option');
                option.value = anchal;
                option.textContent = anchal;
                if (anchal === currentAnchal) option.selected = true;
                anchalFilter.appendChild(option);
            }
        });
    }

    // Populate State dropdown
    const stateFilter = document.getElementById('stateFilter');
    const currentState = stateFilter.value;
    stateFilter.innerHTML = '<option value="">All States</option>';
    
    if (data.states && Array.isArray(data.states)) {
        data.states.forEach(state => {
            if (state) { // Only add non-empty values
                const option = document.createElement('option');
                option.value = state;
                option.textContent = state;
                if (state === currentState) option.selected = true;
                stateFilter.appendChild(option);
            }
        });
    }
}

// Edit member (redirect to edit page or open in modal if needed later)
function editMember(id) {
    // Redirect to the Add Member page with edit_id so the form loads prefilled for editing
    if (!id) return showToast('No member selected for edit', 'error');
    const url = new URL(window.location.origin + '/mahila-samiti-members/add');
    url.searchParams.set('edit_id', id);
    window.location.href = url.toString();
}

// View member details in modal
function viewMember(id) {
    fetch(`/api/mahila-samiti-members/${id}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(res => {
        if (res.success && res.data) {
            const m = res.data;
            // Photo: attempt to build storage url if photo field exists
            const photoEl = document.getElementById('viewMemberPhoto');
            const photoContainer = document.getElementById('viewPhotoContainer');
            const photoPlaceholder = document.getElementById('viewPhotoPlaceholder');
            const photoLink = document.getElementById('viewPhotoLink');
            if (m.photo) {
                // If photo is already a full URL, use it; otherwise assume stored under storage/mahila_samiti/
                let photoUrl = '';
                if (typeof m.photo === 'string' && (m.photo.startsWith('http://') || m.photo.startsWith('https://') || m.photo.startsWith('/'))) {
                    photoUrl = m.photo.startsWith('/') ? m.photo : m.photo;
                } else {
                    photoUrl = `/storage/mahila_samiti/${m.photo}`;
                }

                // Set image and link
                photoEl.src = photoUrl;
                photoEl.classList.remove('d-none');
                photoPlaceholder.classList.add('d-none');
                photoLink.href = photoUrl;

                // If image fails to load, hide and show placeholder
                photoEl.onerror = function() {
                    photoEl.classList.add('d-none');
                    photoPlaceholder.classList.remove('d-none');
                    photoLink.href = '#';
                };
            } else {
                photoEl.classList.add('d-none');
                photoPlaceholder.classList.remove('d-none');
                photoLink.href = '#';
            }

            document.getElementById('viewMemberName').textContent = m.name || '';
            // Summary: Name / Type / Designation / Anchal / Phone / City / State
            const summary = `${m.name || ''} / ${m.type || ''} / ${m.designation || ''} / ${m.anchal_name || ''} / ${m.mobile_number || ''} / ${m.city || ''} / ${m.state || ''}`;
            document.getElementById('viewMemberSummary').textContent = summary;

            // Build detail list
            const details = [];
            details.push(`<strong>MID:</strong> ${m.mid || ''}`);
            details.push(`<strong>Husband / Father:</strong> ${m.husband_name || m.father_name || ''}`);
            details.push(`<strong>Address:</strong> ${m.address || ''}`);
            details.push(`<strong>WTP:</strong> ${m.wtp_number || ''}`);
            details.push(`<strong>Ex Post:</strong> ${m.ex_post || ''}`);
            details.push(`<strong>Remarks:</strong> ${m.remarks || ''}`);

            document.getElementById('viewMemberDetails').innerHTML = details.map(d => `<p class="mb-1">${d}</p>`).join('');

            // show modal
            const viewModal = new bootstrap.Modal(document.getElementById('viewMemberModal'));
            viewModal.show();

            // open edit from view
            document.getElementById('openEditFromViewBtn').onclick = function() {
                viewModal.hide();
                editMember(id);
            };
        } else {
            showToast('Member not found', 'error');
        }
    })
    .catch(err => {
        console.error('Error fetching member:', err);
        showToast('Error loading member details', 'error');
    });
}

// Save edit modal changes
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('saveEditMemberBtn').addEventListener('click', function() {
        const id = document.getElementById('editMemberId').value;
        if (!id) return showToast('No member selected', 'error');

        const form = document.getElementById('editMemberForm');
        const formData = new FormData(form);

        fetch(`/api/mahila-samiti-members/${id}`, {
            method: 'POST', // use POST with _method=PUT for compatibility
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: (() => { formData.append('_method', 'PUT'); return formData; })()
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                showToast('Member updated successfully', 'success');
                // hide modal and reload current session
                const modalEl = document.getElementById('editMemberModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
                const session = document.getElementById('sessionSelect').value;
                if (session) loadMembersBySession(session);
            } else {
                showToast(res.message || 'Error updating member', 'error');
            }
        })
        .catch(err => {
            console.error('Error updating member:', err);
            showToast('Error updating member', 'error');
        });
    });
});

// Delete member
function deleteMember(id) {
    if (confirm('Are you sure you want to delete this member?')) {
        fetch(`/api/mahila-samiti-members/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Member deleted successfully', 'success');
                // reload current session if any
                const session = document.getElementById('sessionSelect').value;
                if (session) loadMembersBySession(session);
            } else {
                showToast(data.message || 'Error deleting member', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error deleting member', 'error');
        });
    }
}

// Global variable to store current members data for search
let currentMembersData = [];
let currentSession = '';
let currentDisplayedData = []; // Tracks currently visible/filtered data for export

// Search functionality
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    const searchResults = document.getElementById('searchResults');
    const anchalFilter = document.getElementById('anchalFilter');
    const stateFilter = document.getElementById('stateFilter');

    // Real-time search as user types
    searchInput.addEventListener('input', function() {
        applyFiltersAndSearch();
    });

    // Filter change events
    anchalFilter.addEventListener('change', applyFiltersAndSearch);
    stateFilter.addEventListener('change', applyFiltersAndSearch);

    // Clear search functionality
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        applyFiltersAndSearch();
        searchInput.focus();
    });

    // Clear all filters functionality
    clearFiltersBtn.addEventListener('click', function() {
        searchInput.value = '';
        anchalFilter.value = '';
        stateFilter.value = '';
        applyFiltersAndSearch();
        searchInput.focus();
    });

    // Enter key to search
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            applyFiltersAndSearch();
        }
    });
}

// Combined filter and search function
function applyFiltersAndSearch() {
    const searchTerm = document.getElementById('searchInput').value.trim().toLowerCase();
    const selectedAnchal = document.getElementById('anchalFilter').value;
    const selectedState = document.getElementById('stateFilter').value;
    const searchResults = document.getElementById('searchResults');
    
    if (!currentMembersData || currentMembersData.length === 0) {
        searchResults.textContent = 'No data to filter';
        return;
    }

    let filteredMembers = currentMembersData;

    // Apply dropdown filters first
    if (selectedAnchal) {
        filteredMembers = filteredMembers.filter(member => 
            (member.anchal_name || '').toLowerCase() === selectedAnchal.toLowerCase()
        );
    }

    if (selectedState) {
        filteredMembers = filteredMembers.filter(member => 
            (member.state || '').toLowerCase() === selectedState.toLowerCase()
        );
    }

    // Apply text search
    if (searchTerm.length >= 2) {
        filteredMembers = filteredMembers.filter(member => {
            return (
                (member.name && member.name.toLowerCase().includes(searchTerm)) ||
                (member.name_hindi && member.name_hindi.toLowerCase().includes(searchTerm)) ||
                (member.designation && member.designation.toLowerCase().includes(searchTerm)) ||
                (member.mid && member.mid.toString().toLowerCase().includes(searchTerm)) ||
                (member.mobile_number && member.mobile_number.toString().includes(searchTerm)) ||
                (member.city && member.city.toLowerCase().includes(searchTerm)) ||
                (member.state && member.state.toLowerCase().includes(searchTerm)) ||
                (member.anchal_name && member.anchal_name.toLowerCase().includes(searchTerm)) ||
                (member.type && member.type.toLowerCase().includes(searchTerm)) ||
                (member.husband_name && member.husband_name.toLowerCase().includes(searchTerm)) ||
                (member.father_name && member.father_name.toLowerCase().includes(searchTerm))
            );
        });
    } else if (searchTerm.length === 0 && !selectedAnchal && !selectedState) {
        // Show all data when no filters applied
        filteredMembers = currentMembersData;
        searchResults.textContent = '';
        currentDisplayedData = filteredMembers;
        displayMembersGrouped(filteredMembers, currentSession);
        return;
    }

    // Update search results count
    let resultsText = '';
    if (searchTerm.length >= 2 || selectedAnchal || selectedState) {
        resultsText = `Found ${filteredMembers.length} of ${currentMembersData.length} members`;
        
        // Add active filter info
        const activeFilters = [];
        if (selectedAnchal) activeFilters.push(`Anchal: ${selectedAnchal}`);
        if (selectedState) activeFilters.push(`State: ${selectedState}`);
        if (searchTerm.length >= 2) activeFilters.push(`Search: "${searchTerm}"`);
        
        if (activeFilters.length > 0) {
            resultsText += ` (Filters: ${activeFilters.join(', ')})`;
        }
    }
    
    searchResults.textContent = resultsText;

    // Store filtered data for export
    currentDisplayedData = filteredMembers;

    // Display filtered results
    if (filteredMembers.length === 0) {
        const tbody = document.getElementById('membersTableBody');
        tbody.innerHTML = `<tr><td colspan="9" class="text-center">No members found matching the selected criteria</td></tr>`;
    } else {
        displayMembersGrouped(filteredMembers, currentSession, true);
    }
}

// Populate filter dropdowns
function populateFilterDropdowns(members) {
    // This function is now only used as fallback
    // Primary filter population is done via database API
    if (!members || !Array.isArray(members)) return;

    // Only populate type filter from session data if not already populated
    const typeFilter = document.getElementById('typeFilter');
    if (typeFilter.children.length <= 1) { // Only has "All Types" option
        populateTypeFilterFromSessionData(members);
    }
}

// Load members for a specific session only
function loadMembersBySession(session) {
    const tbody = document.getElementById('membersTableBody');
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    
    tbody.innerHTML = '<tr><td colspan="9" class="text-center">Loading...</td></tr>';
    
    // Clear search when loading new session
    if (searchInput) {
        searchInput.value = '';
    }
    if (searchResults) {
        searchResults.textContent = '';
    }

    fetch(`/api/mahila-samiti-members?session=${encodeURIComponent(session)}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(res => {
        if (res.success && Array.isArray(res.data)) {
            // Store data globally for search functionality
            currentMembersData = res.data;
            currentDisplayedData = res.data; // Initially all data is displayed
            currentSession = session;
            
            // Display the data
            displayMembersGrouped(res.data, session);
        } else {
            currentMembersData = [];
            currentDisplayedData = [];
            currentSession = '';
            tbody.innerHTML = '<tr><td colspan="12" class="text-center">No members found for this session</td></tr>';
        }
    })
    .catch(err => {
        console.error('Error loading members by session:', err);
        tbody.innerHTML = '<tr><td colspan="12" class="text-center text-danger">Error loading members</td></tr>';
    });
}

// Populate only type filter from session data
function populateTypeFilterFromSessionData(members) {
    if (!members || !Array.isArray(members)) return;

    // Get unique types from current session data
    const types = [...new Set(members.map(m => m.type).filter(Boolean))].sort();

    // Populate Type dropdown
    const typeFilter = document.getElementById('typeFilter');
    const currentType = typeFilter.value;
    typeFilter.innerHTML = '<option value="">All Types</option>';
    types.forEach(type => {
        const option = document.createElement('option');
        option.value = type;
        option.textContent = type.toUpperCase();
        if (type === currentType) option.selected = true;
        typeFilter.appendChild(option);
    });
}

// Renders members by requested order and grouping
function displayMembersGrouped(members, session, isSearchResult = false) {
    const tbody = document.getElementById('membersTableBody');
    tbody.innerHTML = '';

    if (!members || members.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center">No members found for the selected session</td></tr>';
        return;
    }

    // If this is a search result, display in simple list format without grouping
    if (isSearchResult) {
        const rows = [];
        members.forEach((member, index) => {
            rows.push(renderRow(member, session, index));
        });
        tbody.innerHTML = rows.join('\n');
        return;
    }

    // Normal grouped display for full data
    // Helpers
    const byType = (typeKey) => members.filter(m => (m.type || '').toString().toLowerCase().includes(typeKey));
    const groupByAnchal = (arr) => arr.reduce((acc, cur) => {
        const key = cur.anchal_name || 'Unknown';
        (acc[key] = acc[key] || []).push(cur);
        return acc;
    }, {});

    // Preferred anchal ordering as requested by user
    const preferredAnchals = [
        'Mewar',
        'Bikaner Marwar',
        'Jaipur Beawar',
        'Madhya Pradesh',
        'Chattisgarh Odisha',
        'Karnataka Andhra Pradesh',
        'Tamil Nadu',
        'Mumbai-Gujarat-UAE',
        'Maharashtra Vidarbha Khandesh',
        'Bengal-Bihar-Nepal-Bhutan-Jharkhand-Aanshik Orissa',
        'Purvottar',
        'Delhi-Punjab-Hariyana-Uttari'
    ];

    const sortAnchals = (keys) => {
        const preferred = [];
        const others = [];
        const normalizedPref = preferredAnchals.map(p => p.toString().toLowerCase());
        keys.forEach(k => {
            const kn = (k || '').toString().toLowerCase();
            const idx = normalizedPref.indexOf(kn);
            if (idx !== -1) preferred[idx] = k; // place in same index to preserve pref order
            else others.push(k);
        });
        // collapse preferred array (it may have undefined holes)
        const prefCollapsed = preferred.filter(Boolean);
        // sort remaining alphabetically
        others.sort((a, b) => a.localeCompare(b));
        return prefCollapsed.concat(others);
    };

    let rows = [];

    // 1) PST (order: president, secretary, treasurer, co-treasurer)
    const pst = byType('pst');
    if (pst.length) {
        rows.push(`<tr class="table-active"><td colspan="9"><strong>PST</strong></td></tr>`);

        // helper to normalize designation strings for comparison
        const normalizeDesig = (d) => {
            if (!d) return '';
            return d.toString().toLowerCase().trim()
                .replace(/\s+/g, ' ')
                .replace(/\-/g, ' ') // treat hyphen same as space
                .replace(/co ?treasurer|cotreasure?r|co ?treasure?r/gi, 'co treasurer')
                .replace(/vice[- ]?president/gi, 'vice president');
        };

        const order = ['president', 'secretary', 'treasurer', 'co treasurer'];
        const renderedDesigs = new Set();

        // For each ordered designation, render only the first exact-match and mark it rendered
        order.forEach(desigKey => {
            const matched = pst.filter(m => normalizeDesig(m.designation) === desigKey);
            if (matched.length) {
                rows.push(renderRow(matched[0], session));
                renderedDesigs.add(desigKey);
            }
        });

        // Render remaining PST members whose normalized designation hasn't been rendered yet
        pst.forEach(m => {
            const nd = normalizeDesig(m.designation);
            const already = Array.from(renderedDesigs).some(r => nd === r);
            if (!already) rows.push(renderRow(m, session));
        });
    }

    // 2) VP-SEC (anchal wise) -> within anchal: vice president then secretary
    const vpsec = byType('vp') .concat(byType('vp-sec')).filter((v, i, a) => a.indexOf(v) === i);
    if (vpsec.length) {
        rows.push(`<tr class="table-active"><td colspan="9"><strong>VP-SEC (Anchal wise)</strong></td></tr>`);
        const grouped = groupByAnchal(vpsec);
        const anchalKeys = Object.keys(grouped);
        const orderedAnchals = sortAnchals(anchalKeys);
        orderedAnchals.forEach(anchal => {
            rows.push(`<tr class="table-secondary"><td colspan="9"><em>Anchal: ${anchal}</em></td></tr>`);
            const list = grouped[anchal];
            // vice president first
            list.filter(m => (m.designation || '').toString().toLowerCase().includes('vice')).forEach(m => rows.push(renderRow(m, session)));
            // then secretary
            list.filter(m => (m.designation || '').toString().toLowerCase().includes('secretary')).forEach(m => rows.push(renderRow(m, session)));
            // remaining
            list.forEach(m => {
                const isVice = (m.designation || '').toString().toLowerCase().includes('vice');
                const isSec = (m.designation || '').toString().toLowerCase().includes('secretary');
                if (!isVice && !isSec) rows.push(renderRow(m, session));
            });
        });
    }

    // 3) Sanyojika/Sanyojak (grouped by designation)
    // Combine possible type variants and deduplicate by id to avoid duplicate rendering
    const sanyojikaCandidates = byType('sanyojika').concat(byType('sanyoj'));
    const sanyojika = [];
    const seenSanyojIds = new Set();
    sanyojikaCandidates.forEach(m => {
        if (!m || !m.id) return;
        if (!seenSanyojIds.has(m.id)) {
            seenSanyojIds.add(m.id);
            sanyojika.push(m);
        }
    });
    if (sanyojika.length) {
        rows.push(`<tr class="table-active"><td colspan="9"><strong>Sanyojika</strong></td></tr>`);
        
        // Group by designation
        const groupByDesignation = (arr) => arr.reduce((acc, cur) => {
            const key = cur.designation || 'Unknown';
            (acc[key] = acc[key] || []).push(cur);
            return acc;
        }, {});
        
        const groupedByDesig = groupByDesignation(sanyojika);
        const designationKeys = Object.keys(groupedByDesig);
        
        designationKeys.forEach(designation => {
            const members = groupedByDesig[designation];
            
            // Collect all unique anchals for this designation
            const anchals = [...new Set(members.map(m => m.anchal_name || 'Unknown'))];
            const anchalList = anchals.join('-');
            
            // Show designation header (bold)
            rows.push(`<tr class="table-info"><td colspan="9"><strong>${designation}</strong></td></tr>`);
            
            // Show anchal list (italic)
            rows.push(`<tr class="table-secondary"><td colspan="9"><em>Anchal: ${anchalList}</em></td></tr>`);
            
            // Show members
            members.forEach(m => rows.push(renderRow(m, session)));
        });
    }

    // 4) KSM members (anchal wise)
    const ksm = byType('ksm').concat(byType('ksm-member')).filter((v, i, a) => a.indexOf(v) === i);
    if (ksm.length) {
        rows.push(`<tr class="table-active"><td colspan="9"><strong>KSM Members (Anchal wise)</strong></td></tr>`);
        const groupedK = groupByAnchal(ksm);
        const anchalKeysK = Object.keys(groupedK);
        const orderedAnchalsK = sortAnchals(anchalKeysK);
        orderedAnchalsK.forEach(anchal => {
            rows.push(`<tr class="table-secondary"><td colspan="9"><em>Anchal: ${anchal}</em></td></tr>`);
            groupedK[anchal].forEach(m => rows.push(renderRow(m, session)));
        });
    }

    // If no rows built (no recognized groups), render flat table
    if (rows.length === 0) {
        members.forEach((m, i) => rows.push(renderRow(m, session, i)));
    }

    tbody.innerHTML = rows.join('\n');
}

// Helper to compute photo URL
function getPhotoUrl(m) {
    if (!m || !m.photo) return null;
    if (typeof m.photo === 'string' && (m.photo.startsWith('http://') || m.photo.startsWith('https://') || m.photo.startsWith('/'))) {
        return m.photo;
    }
    return '/storage/mahila_samiti/' + m.photo;
}

function renderRow(m, session, indexFallback) {
    const idx = typeof indexFallback === 'number' ? indexFallback + 1 : '';
    
    // Truncate long text for better responsive display
    const nameDisplay = (m.name || '').length > 15 ? (m.name || '').substring(0, 15) + '...' : (m.name || '');
    const anchalDisplay = (m.anchal_name || '').length > 12 ? (m.anchal_name || '').substring(0, 12) + '...' : (m.anchal_name || '');
    const designationDisplay = (m.designation || '').length > 12 ? (m.designation || '').substring(0, 12) + '...' : (m.designation || '');
    
    // Combine address, city, state
    let fullAddress = '';
    const addressParts = [];
    if (m.address) addressParts.push(m.address);
    if (m.city) addressParts.push(m.city);
    if (m.state) addressParts.push(m.state);
    fullAddress = addressParts.join(', ');
    const addressDisplay = fullAddress.length > 20 ? fullAddress.substring(0, 20) + '...' : fullAddress;
    
    const html = `
        <tr>
            <td>${idx || ''}</td>
            <td class="text-truncate-custom" title="${m.name || ''}">${nameDisplay}</td>
            <td class="hide-sm">${((m.type || '').toString().toUpperCase())}</td>
            <td title="${m.designation || ''}">${designationDisplay}</td>
            <td class="hide-lg">${m.mid || ''}</td>
            <td class="hide-md text-truncate-custom" title="${m.anchal_name || ''}">${anchalDisplay}</td>
            <td>${m.mobile_number || ''}</td>
            <td class="text-truncate-custom" title="${fullAddress}">${addressDisplay}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn btn-sm btn-info" onclick="viewMember(${m.id})" title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-primary" onclick="editMember(${m.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteMember(${m.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `;
    return html;
}

function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) return;
    const toastId = 'toast_' + Date.now();
    const bgClass = type === 'success' ? 'bg-success' : type === 'info' ? 'bg-info' : 'bg-danger';
    const icon = type === 'success' ? 'fas fa-check-circle' : type === 'info' ? 'fas fa-info-circle' : 'fas fa-exclamation-circle';
    const toastHtml = '\n        <div class="toast ' + bgClass + ' text-white" role="alert" aria-live="assertive" aria-atomic="true" id="' + toastId + '" data-bs-delay="5000">\n            <div class="toast-header ' + bgClass + ' text-white">\n                <i class="' + icon + ' me-2"></i>\n                <strong class="me-auto">' + (type === 'success' ? 'Success' : type === 'info' ? 'Info' : 'Error') + '</strong>\n                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>\n            </div>\n            <div class="toast-body">\n                ' + (message || '').toString().replace(/\n/g, '<br>') + '\n            </div>\n        </div>\n    ';
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    const toastEl = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
    toastEl.addEventListener('hidden.bs.toast', function() { this.remove(); });
}
</script>

<!-- View Member Modal -->
<div class="modal fade" id="viewMemberModal" tabindex="-1" aria-labelledby="viewMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewMemberModalLabel">Member Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div id="viewPhotoContainer" class="mb-3">
                            <a id="viewPhotoLink" href="#" target="_blank" rel="noopener">
                                <img id="viewMemberPhoto" src="" alt="Photo" class="img-fluid rounded shadow-sm d-none" style="max-height:320px; object-fit:cover;">
                            </a>
                            <div id="viewPhotoPlaceholder" class="border rounded p-4 d-none" style="background:#f8f9fa;">
                                <i class="fas fa-user fa-3x text-muted"></i>
                                <div class="mt-2 text-muted">No photo</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h5 id="viewMemberName" class="mb-1"></h5>
                        <p id="viewMemberSummary" class="text-muted mb-2"></p>
                        <hr>
                        <div id="viewMemberDetails"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="openEditFromViewBtn">Edit</button>
            </div>
        </div>
    </div>
</div>

<!-- PDF Fields Selection Modal -->
<div class="modal fade" id="pdfFieldsModal" tabindex="-1" aria-labelledby="pdfFieldsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfFieldsModalLabel">Select Fields for PDF Export</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pdfFieldsForm">
                    <div class="mb-3">
                        <div class="alert alert-info">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pdfSeparateTables" name="separate_tables" value="1" checked>
                                <label class="form-check-label" for="pdfSeparateTables">
                                    <strong>Generate separate tables by category (PST, VP-SEC Anchal wise, KSM Members Anchal wise, Sanyojak/Sanyojika)</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>Select Fields to Include:</strong>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pdfFieldSession" name="field" value="session" checked>
                            <label class="form-check-label" for="pdfFieldSession">
                                Session
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pdfFieldName" name="field" value="name" checked>
                            <label class="form-check-label" for="pdfFieldName">
                                Name
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pdfFieldFatherHusband" name="field" value="father_husband" checked>
                            <label class="form-check-label" for="pdfFieldFatherHusband">
                                Father/Husband Name
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pdfFieldType" name="field" value="type" checked>
                            <label class="form-check-label" for="pdfFieldType">
                                Type
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pdfFieldDesignation" name="field" value="designation" checked>
                            <label class="form-check-label" for="pdfFieldDesignation">
                                Designation
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pdfFieldAnchal" name="field" value="anchal" checked>
                            <label class="form-check-label" for="pdfFieldAnchal">
                                Anchal
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pdfFieldMID" name="field" value="mid" checked>
                            <label class="form-check-label" for="pdfFieldMID">
                                MID
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pdfFieldPhone" name="field" value="phone" checked>
                            <label class="form-check-label" for="pdfFieldPhone">
                                Phone
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pdfFieldAddress" name="field" value="address" checked>
                            <label class="form-check-label" for="pdfFieldAddress">
                                Address
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="generatePdfBtn"><i class="fas fa-file-pdf"></i> Generate PDF</button>
            </div>
        </div>
    </div>
</div>

<!-- Excel Fields Selection Modal -->
<div class="modal fade" id="excelFieldsModal" tabindex="-1" aria-labelledby="excelFieldsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="excelFieldsModalLabel">Select Fields for Excel Export</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="excelFieldsForm">
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldSession" name="field" value="session" checked>
                            <label class="form-check-label" for="excelFieldSession">
                                Session
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldAnchalName" name="field" value="anchal_name" checked>
                            <label class="form-check-label" for="excelFieldAnchalName">
                                Anchal Name
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldAnchalCode" name="field" value="anchal_code" checked>
                            <label class="form-check-label" for="excelFieldAnchalCode">
                                Anchal Code
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldType" name="field" value="type" checked>
                            <label class="form-check-label" for="excelFieldType">
                                Type
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldDesignation" name="field" value="designation" checked>
                            <label class="form-check-label" for="excelFieldDesignation">
                                Designation
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldMID" name="field" value="mid" checked>
                            <label class="form-check-label" for="excelFieldMID">
                                MID
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldName" name="field" value="name" checked>
                            <label class="form-check-label" for="excelFieldName">
                                Name
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldNameHindi" name="field" value="name_hindi" checked>
                            <label class="form-check-label" for="excelFieldNameHindi">
                                Name (Hindi)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldHusbandName" name="field" value="husband_name" checked>
                            <label class="form-check-label" for="excelFieldHusbandName">
                                Husband Name
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldHusbandNameHindi" name="field" value="husband_name_hindi" checked>
                            <label class="form-check-label" for="excelFieldHusbandNameHindi">
                                Husband Name (Hindi)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldFatherName" name="field" value="father_name" checked>
                            <label class="form-check-label" for="excelFieldFatherName">
                                Father Name
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldFatherNameHindi" name="field" value="father_name_hindi" checked>
                            <label class="form-check-label" for="excelFieldFatherNameHindi">
                                Father Name (Hindi)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldAddress" name="field" value="address" checked>
                            <label class="form-check-label" for="excelFieldAddress">
                                Address
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldAddressHindi" name="field" value="address_hindi" checked>
                            <label class="form-check-label" for="excelFieldAddressHindi">
                                Address (Hindi)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldCity" name="field" value="city" checked>
                            <label class="form-check-label" for="excelFieldCity">
                                City
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldState" name="field" value="state" checked>
                            <label class="form-check-label" for="excelFieldState">
                                State
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldPincode" name="field" value="pincode" checked>
                            <label class="form-check-label" for="excelFieldPincode">
                                Pincode
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldMobileNumber" name="field" value="mobile_number" checked>
                            <label class="form-check-label" for="excelFieldMobileNumber">
                                Mobile Number
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldWTPNumber" name="field" value="wtp_number" checked>
                            <label class="form-check-label" for="excelFieldWTPNumber">
                                WTP Number
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldPhoto" name="field" value="photo" checked>
                            <label class="form-check-label" for="excelFieldPhoto">
                                Photo
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldExPost" name="field" value="ex_post" checked>
                            <label class="form-check-label" for="excelFieldExPost">
                                Ex Post
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excelFieldRemarks" name="field" value="remarks" checked>
                            <label class="form-check-label" for="excelFieldRemarks">
                                Remarks
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="generateExcelBtn"><i class="fas fa-file-excel"></i> Generate Excel</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Member Modal -->
<div class="modal fade" id="editMemberModal" tabindex="-1" aria-labelledby="editMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMemberModalLabel">Edit Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editMemberForm">
                    <input type="hidden" id="editMemberId" name="id">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" id="edit_name" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mobile</label>
                            <input type="text" id="edit_mobile_number" name="mobile_number" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <input type="text" id="edit_type" name="type" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Designation</label>
                            <input type="text" id="edit_designation" name="designation" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Anchal</label>
                            <input type="text" id="edit_anchal_name" name="anchal_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" id="edit_city" name="city" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">State</label>
                            <input type="text" id="edit_state" name="state" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea id="edit_address" name="address" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveEditMemberBtn">Save</button>
            </div>
        </div>
    </div>
</div>

@endsection