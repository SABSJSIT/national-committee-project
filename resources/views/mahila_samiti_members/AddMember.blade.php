@extends('includes.layouts.super_admin')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Bootstrap 5 CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery for AJAX support -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    .toast-container {
        position: fixed;
        top: 100px;
        right: 20px;
        z-index: 9999;
    }
    
    .card {
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px 10px 0 0 !important;
    }
    
    .form-label {
        font-weight: bold;
        color: #333;
    }
    
    .required {
        color: #dc3545;
        font-weight: bold;
    }
    
    .form-control {
        border-radius: 10px;
        border: 1px solid #ddd;
        padding: 10px;
        transition: border-color 0.3s;
    }
    
    .form-control:focus {
        border-color: #6c63ff;
        box-shadow: 0 0 5px rgba(108, 99, 255, 0.5);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #6c63ff, #3f3dff);
        border: none;
        border-radius: 30px;
        padding: 10px 20px;
        font-weight: bold;
        transition: background 0.3s;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #3f3dff, #6c63ff);
        box-shadow: 0 4px 10px rgba(108, 99, 255, 0.5);
    }
    
    .btn-secondary {
        border-radius: 25px;
        padding: 10px 30px;
        font-weight: 600;
    }
    
    .photo-preview {
        max-width: 150px;
        max-height: 150px;
        object-fit: cover;
    }
    
    /* Modal z-index fix to appear above header/sidebar */
    .modal {
        z-index: 9999 !important;
    }
    
    .modal-backdrop {
        z-index: 9998 !important;
        border-radius: 10px;
        border: 3px solid #e9ecef;
    }
    
    .photo-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .photo-upload-area:hover {
        border-color: #667eea;
        background-color: #f8f9ff;
    }
    
    .photo-upload-area.dragover {
        border-color: #667eea;
        background-color: #f0f4ff;
    }
    
    .invalid-feedback {
        display: block;
        font-size: 0.875em;
    }
    
    .form-section {
        background: #f9f9f9;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        border-left: 5px solid #6c63ff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .section-title {
        color: #495057;
        font-weight: 700;
        margin-bottom: 15px;
        font-size: 1.1em;
    }
    
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 10000;
    }
    
    .spinner-border {
        color: #667eea;
    }
    
    /* Styles for disabled dropdown options */
    .form-select option:disabled {
        color: #6c757d !important;
        background-color: #f8f9fa !important;
        font-style: italic;
    }
    
    .form-select:disabled {
        background-color: #f8f9fa;
        opacity: 0.8;
    }
    
    .designation-info {
        font-size: 0.875em;
        color: #6c757d;
        margin-top: 5px;
    }
    
    .designation-warning {
        color: #dc3545;
        font-weight: 600;
    }
</style>

<div class="container-fluid mt-4">
    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-border spinner-border-lg" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-user-plus"></i> Add New Mahila Samiti Member
                    </h4>
                    <a href="/mahila-samiti-members" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Member Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="memberForm" enctype="multipart/form-data" novalidate>
                                    <input type="hidden" id="edit_member_id" name="edit_member_id" value="">
                                    <input type="hidden" id="edit_member_original_session" name="edit_member_original_session" value="">
                                    <input type="hidden" id="edit_member_original_type" name="edit_member_original_type" value="">
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-info-circle"></i> Basic Information
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="session" class="form-label">
                                            Session <span class="required">*</span>
                                        </label>
                                        <select class="form-select" id="session" name="session" required>
                                            <option value="">Select Session</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="anchal_name" class="form-label">
                                            Anchal Name <span class="required">*</span>
                                        </label>
                                        <select class="form-select" id="anchal_name" name="anchal_name" required>
                                            <option value="">Select Anchal</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="anchal_code" class="form-label">
                                            Anchal Code <span class="required">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="anchal_code" name="anchal_code" 
                                               placeholder="Auto-filled" readonly required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">
                                            Type <span class="required">*</span>
                                        </label>
                                        <select class="form-select" id="type" name="type" required>
                                            <option value="">Select Type</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="designation" class="form-label">
                                            Designation <span class="required">*</span>
                                        </label>
                                        <select class="form-select" id="designation" name="designation" required>
                                            <option value="">Select Designation</option>
                                        </select>
                                        <div class="designation-info" id="designationInfo"></div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information Section -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-user"></i> Personal Information
                            </h5>
                            
                            <!-- Contact Details Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="mobile_number" class="form-label">
                                            <i class="fas fa-mobile-alt text-primary"></i> Mobile Number <span class="required">*</span>
                                        </label>
                                        <input type="tel" class="form-control" id="mobile_number" name="mobile_number" 
                                               placeholder="Enter 10-digit Mobile Number" required pattern="[0-9]{10}" maxlength="10">
                                        <small class="form-text text-success">
                                            <i class="fas fa-info-circle"></i> Profile data will auto-fill when you enter a valid 10-digit mobile number
                                        </small>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="mid" class="form-label">
                                            <i class="fas fa-id-card text-primary"></i> MID <span class="required">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="mid" name="mid" 
                                               placeholder="Enter Member ID (max 6 digits)" required maxlength="6">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Name and Guardian Type Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">
                                            <i class="fas fa-signature text-primary"></i> Full Name <span class="required">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               placeholder="Enter Full Name" required maxlength="100">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                               <div class="col-md-6">
    <div class="mb-3">
        <label for="name_hindi" class="form-label">
            <i class="fas fa-signature text-success"></i> पूरा नाम (हिंदी में)
        </label>
        <!-- Note: we keep same id/name so backend unchanged -->
        <input type="text" class="form-control" id="name_hindi" name="name_hindi"
               placeholder="पूरा नाम दर्ज करें (latin से auto-convert होगा)" maxlength="100" autocomplete="off">
        <div class="form-text hint-small">आप English में लिखें — यह स्वतः हिंदी में बदल जाएगा।</div>
        <div class="invalid-feedback"></div>
    </div>
</div>

                            </div>
                            
                            <!-- Guardian Type Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Guardian Type <span class="required">*</span></label>
                                        <div class="btn-group w-100" role="group" aria-label="Guardian Type">
                                            <input type="radio" class="btn-check" name="guardian_type" id="husband_type" value="husband" checked>
                                            <label class="btn btn-outline-primary" for="husband_type">
                                                <i class="fas fa-heart"></i> Husband
                                            </label>
                                            
                                            <input type="radio" class="btn-check" name="guardian_type" id="father_type" value="father">
                                            <label class="btn btn-outline-primary" for="father_type">
                                                <i class="fas fa-male"></i> Father
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Guardian Name Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3" id="husband_field">
                                        <label for="husband_name" class="form-label">
                                            <i class="fas fa-heart text-danger"></i> Husband Name <span class="required">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="husband_name" name="husband_name" 
                                               placeholder="Enter Husband Name" maxlength="100" required>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="mb-3 d-none" id="father_field">
                                        <label for="father_name" class="form-label">
                                            <i class="fas fa-male text-info"></i> Father Name <span class="required">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="father_name" name="father_name" 
                                               placeholder="Enter Father Name" maxlength="100">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 d-none" id="father_field_hindi">
                                        <label for="father_name_hindi" class="form-label">
                                            <i class="fas fa-user text-success"></i> पिता का नाम (हिंदी में) <span class="required">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="father_name_hindi" name="father_name_hindi" 
                                               placeholder="पिता का नाम दर्ज करें" maxlength="100">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="mb-3" id="husband_field_hindi">
                                        <label for="husband_name_hindi" class="form-label">
                                            <i class="fas fa-heart text-danger"></i> पति का नाम (हिंदी में)
                                        </label>
                                        <input type="text" class="form-control" id="husband_name_hindi" name="husband_name_hindi" 
                                               placeholder="पति का नाम दर्ज करें" maxlength="100">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Section (fixed layout) -->
<div class="form-section">
    <h5 class="section-title">
        <i class="fas fa-address-book"></i> Contact Information
    </h5>

    <!-- Address rows: two columns (English / Hindi) -->
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3"
                          placeholder="Enter Complete Address"></textarea>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="address_hindi" class="form-label">Address (हिंदी में)</label>
                <textarea class="form-control" id="address_hindi" name="address_hindi" rows="3"
                          placeholder="पूरा पता हिंदी में दर्ज करें (देवनागरी में)"></textarea>
                <div class="invalid-feedback"></div>
            </div>
        </div>
    </div>

    <!-- City / State / Pincode / WhatsApp in one row -->
    <div class="row g-3 align-items-end"> 
        <div class="col-md-4">
            <div class="mb-0">
                <label for="city" class="form-label">City <span class="required">*</span></label>
                <select class="form-select" id="city" name="city" required>
                    <option value="">Select City</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="mb-0">
                <label for="state" class="form-label">State <span class="required">*</span></label>
                <select class="form-select" id="state" name="state" required>
                    <option value="">Select State</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="mb-0">
                <label for="pincode" class="form-label">Pincode</label>
                <input type="text" inputmode="numeric" pattern="[0-9]*" class="form-control" id="pincode" name="pincode"
                       placeholder="Enter Pincode" maxlength="6" aria-describedby="pincodeHelp">
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="mb-0">
                <label for="wtp_number" class="form-label">WhatsApp Number</label>
                <input type="tel" inputmode="numeric" pattern="[0-9]*" class="form-control" id="wtp_number" name="wtp_number"
                       placeholder="Enter 10-digit" maxlength="10" aria-describedby="wtpHelp">
                <div class="invalid-feedback"></div>
            </div>
        </div>
    </div>
</div>


                      

                        <!-- Photo Upload Section -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-camera"></i> Photo Upload
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="photo" class="form-label">Member Photo</label>
                                        <div class="photo-upload-area" onclick="document.getElementById('photo_file').click()">
                                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-1">Click to upload photo</p>
                                            <small class="text-muted">Maximum size: 200KB | Supported formats: JPG, PNG</small>
                                        </div>
                                        <input type="file" class="form-control d-none" id="photo_file" name="photo_file" 
                                               accept="image/*" onchange="handlePhotoSelection(this)">
                                        <input type="hidden" id="photo" name="photo">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Photo Preview</label>
                                        <div id="photoPreview" class="text-center">
                                            <img id="previewImage" src="" alt="Preview" class="photo-preview d-none">
                                            <div id="noPhotoText" class="text-muted">
                                                <i class="fas fa-image fa-3x mb-2"></i>
                                                <p>No photo selected</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information Section -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-clipboard"></i> Additional Information
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="ex_post" class="form-label">Ex Post</label>
                                        <input type="text" class="form-control" id="ex_post" name="ex_post" 
                                               placeholder="Enter Previous Post" maxlength="100">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="remarks" class="form-label">Remarks</label>
                                        <textarea class="form-control" id="remarks" name="remarks" rows="3" 
                                                  placeholder="Enter any additional remarks"></textarea>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button type="button" class="btn btn-secondary me-3" onclick="resetForm()">
                                    <i class="fas fa-redo"></i> Reset Form
                                </button>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i> Add Member
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const apiBase = "https://apiv1.sadhumargi.com/api";
const token = "vPW6doIdkAdf"; // 🛡️ Bearer Token for API authentication
let dropdownData = {};
let isSubmitting = false;

// Setup jQuery AJAX with authentication headers
$.ajaxSetup({
    headers: {
        "Authorization": `Bearer ${token}`,
        "Accept": "application/json"
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const editId = params.get('edit_id');

    // Prevent Enter key from submitting form
    document.getElementById('memberForm').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
            return false;
        }
    });

    // Load dropdowns first, then initialize listeners and possibly prefill for edit
    loadDropdownData().then(() => {
        setupEventListeners();
        setupFormValidation();
        // Ensure initial guardian toggle state
        const guardianChecked = document.querySelector('input[name="guardian_type"]:checked');
        if (guardianChecked) toggleGuardianFields(guardianChecked.value);

        if (editId) {
            // fetch member and prefill form
            fetch(`/api/mahila-samiti-members/${encodeURIComponent(editId)}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success && res.data) {
                    fillFormWithMember(res.data);
                    document.getElementById('edit_member_id').value = res.data.id;
                    // change submit button text to Update
                    const submitBtn = document.getElementById('submitBtn');
                    if (submitBtn) submitBtn.innerHTML = '<i class="fas fa-save"></i> Update Member';
                } else {
                    showToast('Unable to load member for editing', 'error');
                }
            })
            .catch(err => {
                console.error('Error loading member for edit:', err);
                showToast('Error loading member for edit', 'error');
            });
        }
    });
});

// Load dropdown data
function loadDropdownData() {
    showLoading(true);
    
    return fetch('/api/mahila-samiti-members-dropdown-data', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            dropdownData = data.data;
            populateDropdowns();
        } else {
            showToast('Error loading form data', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error loading form data', 'error');
    })
    .finally(() => {
        showLoading(false);
    });
}

// Load cities by anchal (lazy loading for better performance)
// Instead of loading all 1700+ cities at once, we load only for selected anchal
function loadCitiesByAnchal(anchalId, preselectedCity = null) {
    const citySelect = document.getElementById('city');
    citySelect.innerHTML = '<option value="">Loading cities...</option>';
    citySelect.disabled = true;
    
    fetch(`/api/mahila-samiti-members-cities-by-anchal?anchal_id=${encodeURIComponent(anchalId)}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        citySelect.innerHTML = '<option value="">Select City</option>';
        if (data.success && data.cities) {
            data.cities.forEach(city => {
                const selected = preselectedCity && city.name === preselectedCity ? 'selected' : '';
                citySelect.innerHTML += `<option value="${city.name}" ${selected}>${city.name}</option>`;
            });
            citySelect.disabled = false;
        } else {
            citySelect.innerHTML = '<option value="">No cities found</option>';
        }
    })
    .catch(error => {
        console.error('Error loading cities:', error);
        citySelect.innerHTML = '<option value="">Error loading cities</option>';
    });
}

// Populate dropdowns
function populateDropdowns() {
    // Populate sessions from database (now returns only active sessions)
    const sessionSelect = document.getElementById('session');
    sessionSelect.innerHTML = '<option value="">Select Session</option>';
    if (dropdownData.sessions && dropdownData.sessions.length > 0) {
        
        dropdownData.sessions.forEach(session => {
            // session is now an object: { name: "...", is_active: true }
            // Since only active sessions are fetched, no need to show "(Active)" text
            sessionSelect.innerHTML += `<option value="${session.name}">${session.name}</option>`;
        });
        
        // Auto-select the first session if only one active session exists
        if (dropdownData.sessions.length === 1) {
            sessionSelect.value = dropdownData.sessions[0].name;
        }
    } else {
        sessionSelect.innerHTML = '<option value="">No active sessions available</option>';
    }
    
    // Populate designation types (Type dropdown)
    const typeSelect = document.getElementById('type');
    typeSelect.innerHTML = '<option value="">Select Type</option>';
    if (dropdownData.designationTypes) {
        dropdownData.designationTypes.forEach(designationType => {
            typeSelect.innerHTML += `<option value="${designationType.id}" data-name="${designationType.name}">${designationType.name}</option>`;
        });
    }
    
    // Populate anchals
    const anchalSelect = document.getElementById('anchal_name');
    anchalSelect.innerHTML = '<option value="">Select Anchal</option>';
    if (dropdownData.anchals) {
        dropdownData.anchals.forEach(anchal => {
            anchalSelect.innerHTML += `<option value="${anchal.name}" data-code="${anchal.id}">${anchal.name}</option>`;
        });
    }

    // Cities are NOT loaded here anymore - they load when user selects anchal
    // This improves page load time significantly (1700+ cities to 0 on initial load)
    const citySelect = document.getElementById('city');
    citySelect.innerHTML = '<option value="">Select Anchal First</option>';
    citySelect.disabled = true;

    // Populate states
    const stateSelect = document.getElementById('state');
    stateSelect.innerHTML = '<option value="">Select State</option>';
    if (dropdownData.states) {
        dropdownData.states.forEach(state => {
            stateSelect.innerHTML += `<option value="${state.name}">${state.name}</option>`;
        });
    }

    // Populate designations based on type (empty initially)
    updateDesignations();
}

// Update designations based on selected type (designation type id)
function updateDesignations() {
    const typeSelect = document.getElementById('type');
    const designationTypeId = typeSelect.value;
    const designationTypeName = typeSelect.options[typeSelect.selectedIndex]?.dataset?.name || '';
    const session = document.getElementById('session').value;
    const anchalName = document.getElementById('anchal_name').value;
    const designationSelect = document.getElementById('designation');
    const designationInfo = document.getElementById('designationInfo');
    
    designationSelect.innerHTML = '<option value="">Select Designation</option>';
    designationInfo.innerHTML = '';
    
    if (!designationTypeId) return;
    
    // Fetch designations from database based on designation type
    showLoading(true);
    
    fetch(`/api/mahila-samiti-members-designations-by-type?designation_type_id=${encodeURIComponent(designationTypeId)}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.designations) {
            // Store designations for later use
            const availableDesignations = data.designations;
            
            // If session selected, check for existing combinations
            if (session) {
                checkExistingCombinations(session, anchalName, designationTypeName, availableDesignations);
            } else {
                // If session not selected, show all designations
                populateDesignationOptions(availableDesignations, [], designationTypeName, anchalName);
                showLoading(false);
            }
        } else {
            designationSelect.innerHTML = '<option value="">No designations available</option>';
            showLoading(false);
        }
    })
    .catch(error => {
        console.error('Error fetching designations:', error);
        designationSelect.innerHTML = '<option value="">Error loading designations</option>';
        showLoading(false);
    });
}

// Check existing combinations for the selected session, anchal, and type
function checkExistingCombinations(session, anchalName, typeName, availableDesignations) {
    // Build API URL with type name (stored in type field in database)
    let apiUrl = `/api/mahila-samiti-members-existing-combinations?session=${encodeURIComponent(session)}&type=${encodeURIComponent(typeName)}`;
    
    // For PST, SANYOJAK, SANYOJIKA types, don't include anchal in check - these posts should be unique across all anchals (per session only)
    // For VP SEC and other types, include anchal in the check if provided (per anchal per designation restriction)
    // NOTE: Backend now returns ALL designations for the session+type so we can do local vs global filtering here.
    // We just pass the session and type to get the full list.
    
    fetch(apiUrl, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const usedDesignations = data.combinations || [];
            populateDesignationOptions(availableDesignations, usedDesignations, typeName, anchalName);
        } else {
            populateDesignationOptions(availableDesignations, [], typeName, anchalName);
            console.error('Error fetching existing combinations:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        populateDesignationOptions(availableDesignations, [], typeName, anchalName);
    })
    .finally(() => {
        showLoading(false);
    });
}

// Populate designation options with disabled state for used ones
function populateDesignationOptions(availableDesignations, usedDesignations, typeName, currentAnchalName) {
    const designationSelect = document.getElementById('designation');
    const designationInfo = document.getElementById('designationInfo');
    
    designationSelect.innerHTML = '<option value="">Select Designation</option>';
    designationInfo.innerHTML = '';
    
    if (!availableDesignations || availableDesignations.length === 0) {
        designationSelect.innerHTML = '<option value="">No designations available for this type</option>';
        return;
    }
    
    let currentCount = 0; // Helper to count how many inputs are unavailable

    // Edit-mode context: allow the current member's designation to remain selectable
    const editId = (document.getElementById('edit_member_id') || {}).value || '';
    const origSession = (document.getElementById('edit_member_original_session') || {}).value || '';
    const origType = (document.getElementById('edit_member_original_type') || {}).value || '';
    const currentSession = (document.getElementById('session') || {}).value || '';
    const currentDesignationStored = (document.getElementById('designation') || {}).dataset ? (document.getElementById('designation').dataset.current || '') : '';
    const isExemptContext = editId && origSession && origType && (origSession === currentSession) && (origType === typeName);

    // Helper to normalize designation strings for comparison
    const normalize = (s) => {
        if (!s) return '';
        return s.toString().toLowerCase().trim().replace(/\s+/g, ' ').replace(/[-–—]/g, ' ');
    };
    
    const currentDesigNorm = normalize(currentDesignationStored || '');
    
    // We'll always populate options but may disable them
    designationSelect.disabled = false;
    
    // Populate designation options from database
    let availableCount = 0;
    
    availableDesignations.forEach(designation => {
        const desName = designation.name;
        const desNorm = normalize(desName);
        
        // Logic to determine if this designation is "Global" (Unique per Session+Type) 
        // or "Local" (Unique per Session+Type+Anchal)
        // Check the TYPE NAME (not designation name) to determine if it's global
        const typeNorm = normalize(typeName);
        const isGlobalPost = typeNorm.includes('sanyojak') || typeNorm.includes('sanyojika') || typeNorm.includes('संयोजक') || typeNorm.includes('संयोजिका') || typeNorm === 'pst';
        
        // KSM members can have multiple entries with same designation (up to 12 total per anchal)
        // So we don't mark individual designations as "used" for KSM type
        const isKSMType = typeNorm.includes('ksm') || typeNorm.includes('केएसएम');
        
        let isUsed = false;
        
        if (isKSMType) {
            // For KSM members, don't mark individual designations as used
            // The limit is on total count (12 per anchal), not per designation
            isUsed = false;
        } else if (isGlobalPost) {
            // Global check: Is it used ANYWHERE in this Session+Type?
            // usedDesignations is array of {designation: "...", anchal_name: "..."}
            isUsed = usedDesignations.some(u => normalize(u.designation) === desNorm);
        } else {
            // Local check: Is it used in THIS Anchal?
            if (currentAnchalName) {
                isUsed = usedDesignations.some(u => normalize(u.designation) === desNorm && u.anchal_name === currentAnchalName);
            }
        }

        // If in edit mode and original session/type match current, allow the member's own designation
        const exemptThis = isExemptContext && desNorm === currentDesigNorm && currentDesigNorm !== '';
        
        const disabled = isUsed && !exemptThis;
        const disabledAttr = disabled ? 'disabled' : '';
        const usedText = (isUsed && !exemptThis) ? ' (Already Added)' : '';
        const styleAttr = (isUsed && !exemptThis) ? 'style="color: #6c757d; background-color: #f8f9fa;"' : '';

        if (disabled) {
            currentCount++;
        } else {
            availableCount++;
        }

        designationSelect.innerHTML += `<option value="${designation.name}" ${disabledAttr} ${styleAttr}>${designation.name}${usedText}</option>`;
    });
    
    // Show info about available vs used designations
    const maxEntries = availableDesignations.length;
    
    // Special handling for KSM members - check if limit of 12 per anchal is reached
    const typeNorm = normalize(typeName);
    const isKSMType = typeNorm.includes('ksm') || typeNorm.includes('केएसएम');
    
    if (isKSMType && currentAnchalName) {
        // Count how many KSM members already exist for this anchal
        const ksmMembersCount = usedDesignations.filter(u => u.anchal_name === currentAnchalName).length;
        const maxKSMMembers = 12;
        const remainingSlots = maxKSMMembers - ksmMembersCount;
        
        if (ksmMembersCount >= maxKSMMembers) {
            // Limit reached - disable form
            if (isExemptContext && currentDesigNorm) {
                designationInfo.innerHTML = `<span class="text-info">ℹ️ Current designation is retained for this member. Maximum limit of ${maxKSMMembers} KSM members per anchal has been reached.</span>`;
            } else {
                designationInfo.innerHTML = `<span class="designation-warning">❌ Maximum limit of ${maxKSMMembers} KSM members per anchal has been reached for ${currentAnchalName}. (प्रति अंचल अधिकतम ${maxKSMMembers} KSM सदस्यों की सीमा पूर्ण हो चुकी है।)</span>`;
                designationSelect.disabled = true;
            }
        } else if (remainingSlots <= 3) {
            // Warning when approaching limit
            designationInfo.innerHTML = `<span class="text-warning">⚠️ ${ksmMembersCount} of ${maxKSMMembers} KSM members added for ${currentAnchalName}. Only ${remainingSlots} slot(s) remaining. (${currentAnchalName} के लिए ${maxKSMMembers} में से ${ksmMembersCount} KSM सदस्य जोड़े गए। केवल ${remainingSlots} स्लॉट शेष हैं।)</span>`;
        } else {
            designationInfo.innerHTML = `<span class="text-info">ℹ️ ${ksmMembersCount} of ${maxKSMMembers} KSM members added for ${currentAnchalName}. ${remainingSlots} slot(s) available.</span>`;
        }
    } else if (availableCount === 0) {
        // If editing and exempt context, allow the current designation even if others are full
        if (isExemptContext && currentDesigNorm) {
            designationInfo.innerHTML = `<span class="text-info">ℹ️ Current designation is retained for this member; other designations are filled.</span>`;
        } else {
            designationInfo.innerHTML = `<span class="designation-warning">❌ All designations for this type have been filled</span>`;
            // If not editing or nothing to exempt, disable the select to prevent new entries
            designationSelect.disabled = true;
        }
    } else if (currentCount > 0) {
         designationInfo.innerHTML = `<span class="text-warning">⚠️ ${currentCount} filled, ${availableCount} available for this type</span>`;
    }
}

// Setup event listeners
function setupEventListeners() {
    // Type change event
    document.getElementById('type').addEventListener('change', updateDesignations);
    
    // Session change event - update designations when session changes
    document.getElementById('session').addEventListener('change', updateDesignations);
    
    // Anchal change event - Also loads cities for that anchal (lazy loading)
    document.getElementById('anchal_name').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const anchalCode = selectedOption.getAttribute('data-code') || '';
        document.getElementById('anchal_code').value = anchalCode;
        
        // Load cities for selected anchal (lazy loading for performance)
        if (anchalCode) {
            loadCitiesByAnchal(anchalCode);
        } else {
            // Reset city dropdown if no anchal selected
            const citySelect = document.getElementById('city');
            citySelect.innerHTML = '<option value="">Select Anchal First</option>';
            citySelect.disabled = true;
        }
        
        // Update designations when anchal changes - but only for types that depend on anchal
        const typeSelect = document.getElementById('type');
        const typeName = typeSelect.options[typeSelect.selectedIndex]?.dataset?.name || '';
        const typeNameNorm = typeName.toLowerCase().trim();
        
        // Only update designations for types that have per-anchal restrictions
        // PST, SANYOJAK, SANYOJIKA (including Hindi variations) have global restrictions and don't need anchal-based updates
        const isGlobalType = typeNameNorm.includes('pst') || 
                            typeNameNorm.includes('sanyojak') || 
                            typeNameNorm.includes('sanyojika') ||
                            typeNameNorm.includes('संयोजक') || 
                            typeNameNorm.includes('संयोजिका');
        
        if (!isGlobalType) {
            updateDesignations();
        }
    });
    
    // Mobile number input event - auto-fetch profile when valid number is entered
    document.getElementById('mobile_number').addEventListener('input', function() {
        clearTimeout(this.fetchTimeout);
        const mobileNumber = this.value.trim();
        
        if (mobileNumber.length === 10 && /^[0-9]{10}$/.test(mobileNumber)) {
            // Add small delay to avoid too many API calls while typing
            this.fetchTimeout = setTimeout(() => {
                fetchProfile();
            }, 1000);
        }
    });
    
    // Guardian type change events
    document.querySelectorAll('input[name="guardian_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            toggleGuardianFields(this.value);
        });
    });
    
    // Form submission
    document.getElementById('memberForm').addEventListener('submit', handleFormSubmit);
    
    // Photo upload drag and drop
    const photoUploadArea = document.querySelector('.photo-upload-area');
    
    photoUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    
    photoUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });
    
    photoUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            document.getElementById('photo_file').files = files;
            handlePhotoSelection(document.getElementById('photo_file'));
        }
    });
}

// Setup form validation
function setupFormValidation() {
    const form = document.getElementById('memberForm');
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            clearFieldError(this);
        });
    });
}

// Validate individual field
function validateField(field) {
    const value = field.value.trim();
    const isRequired = field.hasAttribute('required');
    let isValid = true;
    let errorMessage = '';
    
    // Required field validation
    if (isRequired && !value) {
        isValid = false;
        errorMessage = `${getFieldLabel(field)} is required`;
    }
    
    // Specific field validations
    if (value) {
        switch (field.name) {
            case 'mobile_number':
            case 'wtp_number':
                if (!/^[0-9]{10}$/.test(value)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid 10-digit number';
                }
                break;
            case 'pincode':
                if (value && !/^[0-9]{6}$/.test(value)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid 6-digit pincode';
                }
                break;
            case 'mid':
                if (value.length < 3) {
                    isValid = false;
                    errorMessage = 'MID must be at least 3 characters';
                }
                break;
        }
    }
    
    // Show/hide error
    if (isValid) {
        clearFieldError(field);
    } else {
        showFieldError(field, errorMessage);
    }
    
    return isValid;
}

// Get field label
function getFieldLabel(field) {
    const label = document.querySelector(`label[for="${field.id}"]`);
    return label ? label.textContent.replace('*', '').trim() : field.name;
}

// Show field error
function showFieldError(field, message) {
    field.classList.add('is-invalid');
    const feedback = field.parentNode.querySelector('.invalid-feedback');
    if (feedback) {
        feedback.textContent = message;
    }
}

// Clear field error
function clearFieldError(field) {
    field.classList.remove('is-invalid');
    const feedback = field.parentNode.querySelector('.invalid-feedback');
    if (feedback) {
        feedback.textContent = '';
    }
}

// Handle photo selection
function handlePhotoSelection(input) {
    const file = input.files[0];
    const previewImage = document.getElementById('previewImage');
    const noPhotoText = document.getElementById('noPhotoText');
    const photoField = document.getElementById('photo');
    
    if (file) {
        // Check file size (200KB)
        if (file.size > 200 * 1024) {
            showFieldError(input, 'Photo size must be less than 200KB');
            input.value = '';
            photoField.value = '';
            return;
        }
        
        // Check file type
        if (!file || !file.type || !file.type.startsWith('image/')) {
            showFieldError(input, 'Please select a valid image file');
            input.value = '';
            photoField.value = '';
            return;
        }
        
        clearFieldError(input);
        
        // Convert file to base64 and set in photo field
        const reader = new FileReader();
        reader.onload = function(e) {
            const base64String = e.target.result;
            photoField.value = base64String; // Set base64 string in photo field
            previewImage.src = base64String;
            previewImage.classList.remove('d-none');
            noPhotoText.classList.add('d-none');
        };
        reader.readAsDataURL(file);
    } else {
        // Hide preview and clear photo field
        previewImage.classList.add('d-none');
        noPhotoText.classList.remove('d-none');
        previewImage.src = '';
        photoField.value = '';
    }
}

// Fetch profile from external source using mobile number
function fetchProfile() {
    const mobileNumber = document.getElementById('mobile_number').value.trim();
    
    if (!mobileNumber) {
        showToast('Please enter mobile number first', 'error');
        return;
    }
    
    if (mobileNumber.length !== 10 || !/^[0-9]{10}$/.test(mobileNumber)) {
        showToast('Please enter a valid 10-digit mobile number', 'error');
        return;
    }
    
    showLoading(true);
    
    // Try multiple possible endpoints
    const endpoints = [
        `${apiBase}/fetch-profiles`,
        `${apiBase}/members/search`,
        `${apiBase}/profiles/search`,
        `${apiBase}/member/fetch-by-mobile`,
        `${apiBase}/fetch-member-profile`
    ];
    
    let currentEndpoint = 0;
    
    function tryNextEndpoint() {
        if (currentEndpoint >= endpoints.length) {
            showLoading(false);
            showToast('Unable to connect to profile service. Please check the API endpoint or try again later.', 'error');
            return;
        }
        
        const endpoint = endpoints[currentEndpoint];
        console.log(`Trying endpoint: ${endpoint}`);
        
        $.ajax({
            url: endpoint,
            type: 'POST',
            data: {
                mobile: mobileNumber,
                phone: mobileNumber,
                mobile_number: mobileNumber
            },
            success: function(data) {
                console.log('API Response:', data);
                
                if (data.profiles && data.profiles.length > 0) {
                    if (data.profiles.length === 1) {
                        // Single profile - auto fill
                        fillFormWithProfile(data.profiles[0]);
                        showToast('Profile data fetched and filled successfully', 'success');
                    } else {
                        // Multiple profiles - show selection modal
                        showProfileSelectionModal(data.profiles, mobileNumber);
                    }
                    showLoading(false);
                } else if (data.data && Array.isArray(data.data) && data.data.length > 0) {
                    // Handle different response format
                    if (data.data.length === 1) {
                        fillFormWithProfile(data.data[0]);
                        showToast('Profile data fetched and filled successfully', 'success');
                    } else {
                        showProfileSelectionModal(data.data, mobileNumber);
                    }
                    showLoading(false);
                } else if (data.member || data.profile) {
                    // Single member object response
                    fillFormWithProfile(data.member || data.profile);
                    showToast('Profile data fetched and filled successfully', 'success');
                    showLoading(false);
                } else {
                    // No data found, try next endpoint
                    console.log(`No data in response from ${endpoint}, trying next...`);
                    currentEndpoint++;
                    tryNextEndpoint();
                }
            },
            error: function(xhr, status, error) {
                console.error(`Error from ${endpoint}:`, xhr.status, xhr.responseText);
                
                if (xhr.status === 404) {
                    console.log(`Endpoint ${endpoint} not found, trying next...`);
                    currentEndpoint++;
                    tryNextEndpoint();
                } else if (xhr.status === 401) {
                    showLoading(false);
                    showToast('Authentication failed. Please check API credentials.', 'error');
                } else if (xhr.status === 422) {
                    showLoading(false);
                    showToast('Invalid mobile number format for API.', 'error');
                } else {
                    // For other errors, try next endpoint
                    console.log(`Error ${xhr.status} from ${endpoint}, trying next...`);
                    currentEndpoint++;
                    tryNextEndpoint();
                }
            }
        });
    }
    
    // Start trying endpoints
    tryNextEndpoint();
}

// Show profile selection modal when multiple profiles found
function showProfileSelectionModal(profiles, mobileNumber) {
    const modalHtml = `
        <div class="modal fade" id="profileSelectionModal" tabindex="-1" aria-labelledby="profileSelectionModalLabel" aria-hidden="true" style="z-index: 9999;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="profileSelectionModalLabel">
                            <i class="fas fa-users"></i> Multiple Profiles Found for ${mobileNumber}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-3">Found ${profiles.length} profiles associated with this mobile number. Please select one:</p>
                        <div class="row">
                            ${profiles.map((profile, index) => `
                                <div class="col-12 mb-3">
                                    <div class="card profile-card" style="cursor: pointer; transition: all 0.3s;" onclick="selectProfile(${index})">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-2 text-center">
                                                    <div class="profile-avatar">
                                                        ${profile.profile_pic ? 
                                                            `<img src="https://sadhumargi.com/uploads/profiles/${profile.profile_pic}" alt="Profile" class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">` : 
                                                            `<div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px;">${(profile.first_name||'').charAt(0)}</div>`
                                                        }
                                                    </div>
                                                </div>
                                                <div class="col-md-10">
                                                    <h6 class="mb-1">
                                                        <strong>${profile.salution || ''} ${profile.first_name || ''} ${profile.last_name || ''}</strong>
                                                        <small class="text-muted">(ID: ${profile.member_id || 'N/A'})</small>
                                                    </h6>
                                                    <p class="mb-1">
                                                        <i class="fas fa-user-friends text-muted"></i> ${profile.guardian_type || ''}: ${profile.guardian_name || ''}
                                                    </p>
                                                    <p class="mb-1">
                                                        <i class="fas fa-map-marker-alt text-muted"></i> ${profile.city || ''}, ${profile.state || ''}
                                                    </p>
                                                    <p class="mb-0">
                                                        <i class="fas fa-birthday-cake text-muted"></i> ${profile.age ? profile.age + ' years' : 'Age not specified'}
                                                        <span class="ms-3"><i class="fas fa-venus-mars text-muted"></i> ${profile.gender || ''}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('profileSelectionModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Store profiles data for selection
    window.profilesData = profiles;
    
    // Add hover effects (delegated after insert)
    document.querySelectorAll('.profile-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.boxShadow = '0 4px 15px rgba(0,0,0,0.2)';
            this.style.transform = 'translateY(-2px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.boxShadow = '';
            this.style.transform = '';
        });
    });
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('profileSelectionModal'));
    modal.show();
}

// Select profile from modal
function selectProfile(index) {
    const selectedProfile = window.profilesData[index];
    fillFormWithProfile(selectedProfile);
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('profileSelectionModal'));
    modal.hide();
    
    showToast(`Profile selected: ${selectedProfile.first_name || ''} ${selectedProfile.last_name || ''}`, 'success');
}

// Fill form with selected profile data
function fillFormWithProfile(profile) {
    try {
        // Fill MID
        if (profile.member_id) {
            document.getElementById('mid').value = profile.member_id;
        }
        
        // Fill Name
        const fullName = `${profile.salution || ''} ${profile.first_name || ''} ${profile.last_name || ''}`.trim();
        if (fullName) {
            document.getElementById('name').value = fullName;
        }
        // Fill Hindi name if provided
        if (profile.name_hindi) {
            document.getElementById('name_hindi').value = profile.name_hindi;
        }

        // Fill Guardian information
        if (profile.guardian_type && profile.guardian_name) {
            const guardianType = profile.guardian_type.toLowerCase();
            if (guardianType.includes('father')) {
                document.getElementById('father_type').checked = true;
                document.getElementById('husband_type').checked = false;
                toggleGuardianFields('father');
                document.getElementById('father_name').value = profile.guardian_name;
                if (profile.guardian_name_hindi) {
                    document.getElementById('father_name_hindi').value = profile.guardian_name_hindi;
                }
            } else if (guardianType.includes('husband') || guardianType.includes('wife') || guardianType.includes('spouse')) {
                document.getElementById('husband_type').checked = true;
                document.getElementById('father_type').checked = false;
                toggleGuardianFields('husband');
                document.getElementById('husband_name').value = profile.guardian_name;
                if (profile.guardian_name_hindi) {
                    document.getElementById('husband_name_hindi').value = profile.guardian_name_hindi;
                }
            }
        }
        
        // Fill Address
        const address = [profile.address, profile.address2, profile.post].filter(Boolean).join(', ');
        if (address) {
            document.getElementById('address').value = address;
        }
        if (profile.address_hindi) {
            document.getElementById('address_hindi').value = profile.address_hindi;
        }
        
        // Fill City
        if (profile.city) {
            const citySelect = document.getElementById('city');
            // Try to select the city from dropdown
            for (let option of citySelect.options) {
                if (option.text.toLowerCase() === profile.city.toLowerCase()) {
                    option.selected = true;
                    break;
                }
            }
            // If not found in dropdown, set as text (for input field)
            if (!citySelect.value) {
                citySelect.value = profile.city;
            }
        }
        
        // Fill State
        if (profile.state) {
            const stateSelect = document.getElementById('state');
            // Try to select the state from dropdown
            for (let option of stateSelect.options) {
                if (option.text.toLowerCase() === profile.state.toLowerCase()) {
                    option.selected = true;
                    break;
                }
            }
            // If not found in dropdown, set as text (for input field)
            if (!stateSelect.value) {
                stateSelect.value = profile.state;
            }
        }
        
        // Fill Pincode
        if (profile.pincode) {
            document.getElementById('pincode').value = profile.pincode;
        }
        
        // Fill WhatsApp Number
        if (profile.whatsapp_number) {
            document.getElementById('wtp_number').value = profile.whatsapp_number;
        }
        
        // Handle Photo URL
        if (profile.photo || profile.image || profile.photo_url) {
            const photoUrl = profile.photo || profile.image || profile.photo_url;
            if (photoUrl && typeof photoUrl === 'string') {
                document.getElementById('photo').value = photoUrl;
                displayPhotoPreview(photoUrl);
            }
        }
        
        // Clear any validation errors
        document.querySelectorAll('.is-invalid').forEach(element => {
            clearFieldError(element);
        });
        
    } catch (error) {
        console.error('Error filling form with profile data:', error);
        showToast('Error filling form with profile data', 'error');
    }
}

// Fill the AddMember form with an existing member record (API shape)
function fillFormWithMember(member) {
    try {
        if (!member) return;

        // store original session/type early for exemption logic
        try {
            const origSessEl = document.getElementById('edit_member_original_session');
            const origTypeEl = document.getElementById('edit_member_original_type');
            if (origSessEl) origSessEl.value = member.session || '';
            if (origTypeEl) origTypeEl.value = member.type || '';
        } catch(e) {}

        // Basic fields
        if (member.mid) document.getElementById('mid').value = member.mid;
        if (member.name) document.getElementById('name').value = member.name;
        if (member.name_hindi) document.getElementById('name_hindi').value = member.name_hindi;

        // Guardian
        const guardian = (member.guardian_type || '').toLowerCase();
        if (guardian.includes('father')) {
            document.getElementById('father_type').checked = true;
            toggleGuardianFields('father');
            if (member.father_name) document.getElementById('father_name').value = member.father_name;
            if (member.father_name_hindi) document.getElementById('father_name_hindi').value = member.father_name_hindi;
        } else {
            document.getElementById('husband_type').checked = true;
            toggleGuardianFields('husband');
            if (member.husband_name) document.getElementById('husband_name').value = member.husband_name;
            if (member.husband_name_hindi) document.getElementById('husband_name_hindi').value = member.husband_name_hindi;
        }

        // Contact & address
        if (member.address) document.getElementById('address').value = member.address;
        if (member.address_hindi) document.getElementById('address_hindi').value = member.address_hindi;
        if (member.city) {
            const citySelect = document.getElementById('city');
            for (let option of citySelect.options) {
                if (option.text.toLowerCase() === (member.city || '').toLowerCase()) { option.selected = true; break; }
            }
            if (!document.getElementById('city').value) document.getElementById('city').value = member.city;
        }
        if (member.state) {
            const stateSelect = document.getElementById('state');
            for (let option of stateSelect.options) {
                if (option.text.toLowerCase() === (member.state || '').toLowerCase()) { option.selected = true; break; }
            }
            if (!document.getElementById('state').value) document.getElementById('state').value = member.state;
        }
        if (member.pincode) document.getElementById('pincode').value = member.pincode;
        if (member.wtp_number) document.getElementById('wtp_number').value = member.wtp_number;
        if (member.mobile_number) document.getElementById('mobile_number').value = member.mobile_number;

        // Photo - accept URL or stored base64
        if (member.photo) {
            document.getElementById('photo').value = member.photo;
            displayPhotoPreview(member.photo);
        }

        // Additional
        if (member.ex_post) document.getElementById('ex_post').value = member.ex_post;
        if (member.remarks) document.getElementById('remarks').value = member.remarks;

        // Type / Designation / Anchal / Session
        if (member.type) {
            const t = document.getElementById('type');
            // set current designation into dataset so populateDesignationOptions can exempt it
            const desEl = document.getElementById('designation');
            if (desEl) desEl.dataset.current = member.designation || '';
            
            // Find and select the option by matching the data-name attribute (type name)
            if (t) {
                for (let option of t.options) {
                    if (option.dataset.name && option.dataset.name.toLowerCase() === (member.type || '').toLowerCase()) {
                        option.selected = true;
                        break;
                    }
                }
                t.dispatchEvent(new Event('change'));
            }
        }

        // Wait a tick to allow updateDesignations to repopulate options
        setTimeout(() => {
            if (member.designation) {
                const des = document.getElementById('designation');
                if (des) des.dataset.current = member.designation || '';
                if (des) {
                    for (let option of des.options) {
                        if (option.value.toString().toLowerCase() === (member.designation || '').toString().toLowerCase()) {
                            option.selected = true; break;
                        }
                    }
                    if (!des.value) des.value = member.designation;
                }
            }

            if (member.anchal_name) {
                const anch = document.getElementById('anchal_name');
                for (let option of anch.options) {
                    if (option.value.toLowerCase() === (member.anchal_name || '').toLowerCase()) { option.selected = true; break; }
                }
                if (!anch.value) anch.value = member.anchal_name;
                // Also set the anchal_code field from the selected option's data-code attribute
                try {
                    const sel = anch.options[anch.selectedIndex];
                    if (sel) {
                        const code = sel.getAttribute('data-code') || sel.dataset.code || '';
                        const codeEl = document.getElementById('anchal_code');
                        if (codeEl) codeEl.value = code;
                        
                        // Load cities for this anchal (lazy loading) with member's city preselected
                        if (code) {
                            loadCitiesByAnchal(code, member.city || null);
                        }
                    }
                } catch (e) {
                    // ignore
                }
            }

            if (member.session) {
                const sess = document.getElementById('session');
                if (sess) {
                    for (let option of sess.options) {
                        if (option.value === member.session) { option.selected = true; break; }
                    }
                    if (!sess.value) sess.value = member.session;
                }
            }
            // store original session/type for exemption logic
            try {
                const origSessEl = document.getElementById('edit_member_original_session');
                const origTypeEl = document.getElementById('edit_member_original_type');
                if (origSessEl) origSessEl.value = member.session || '';
                if (origTypeEl) origTypeEl.value = member.type || '';
            } catch(e) {}
            // Now that fields are populated, set limited edit mode (create hidden inputs and lock fields)
            setLimitedEditMode();
        }, 250);

        // Clear any validation errors
        document.querySelectorAll('.is-invalid').forEach(el => clearFieldError(el));
    } catch (err) {
        console.error('Error filling form from member:', err);
    }
}

// When editing, restrict which fields are editable. Create hidden fields for values
// that must still be submitted but should not be changed by the user.
function setLimitedEditMode() {
    try {
        const form = document.getElementById('memberForm');
        if (!form) return;

        // Fields to lock from editing but still submit: session, anchal_name, anchal_code, type, designation
        const lockFields = ['session', 'anchal_name', 'anchal_code', 'type', 'designation'];
        lockFields.forEach(name => {
            const el = document.getElementById(name);
            if (!el) return;
            // Create or update hidden input to carry value
            let hidden = document.getElementById(name + '_hidden_submit');
            if (!hidden) {
                hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.id = name + '_hidden_submit';
                hidden.name = name; // same name so backend receives value
                form.appendChild(hidden);
            }
            hidden.value = (el.value || '');

            // Make visible field readonly/disabled in UI
            if (el.tagName.toLowerCase() === 'select') {
                el.disabled = true;
            } else {
                el.readOnly = true;
            }
        });

        // Additionally, disable many other inputs to avoid changing related data
        const otherDisable = ['mid', 'name', 'mobile_number', 'address', 'city', 'state', 'ex_post', 'remarks', 'photo'];
        // We allow editing only name, mid, mobile_number, address, city, state, ex_post, remarks, photo
        // So ensure these remain enabled; everything else already locked above

        // Show an edit-mode banner so user knows fields are limited
        let banner = document.getElementById('editModeBanner');
        if (!banner) {
            banner = document.createElement('div');
            banner.id = 'editModeBanner';
            banner.className = 'alert alert-info';
            banner.style.marginBottom = '15px';
            banner.innerHTML = '<strong>Edit mode:</strong> Only Name, MID, Mobile, Address, City, State, Ex Post, Remarks and Photo are editable.';
            const container = document.querySelector('.container-fluid');
            if (container) container.insertBefore(banner, container.firstChild.nextSibling);
        }

        // Update hidden submits if user changes any locked values via scripts (unlikely)
        // e.g., if anchal select change triggers, keep hidden in sync
        ['session','anchal_name','anchal_code','type','designation'].forEach(name => {
            const el = document.getElementById(name);
            const hidden = document.getElementById(name + '_hidden_submit');
            if (!el || !hidden) return;
            el.addEventListener('change', function() { hidden.value = el.value || ''; });
        });
    } catch (e) {
        console.error('Error setting limited edit mode:', e);
    }
}

// Display photo preview from URL
function displayPhotoPreview(photoUrl) {
    try {
        const previewImage = document.getElementById('previewImage');
        const noPhotoText = document.getElementById('noPhotoText');
        
        if (photoUrl) {
            previewImage.src = photoUrl;
            previewImage.classList.remove('d-none');
            noPhotoText.classList.add('d-none');
        } else {
            previewImage.classList.add('d-none');
            noPhotoText.classList.remove('d-none');
        }
    } catch (error) {
        console.error('Error displaying photo preview:', error);
    }
}

// Handle form submission
async function handleFormSubmit(e) {
    e.preventDefault();
    
    if (isSubmitting) return;
    
    // Validate all fields
    const form = document.getElementById('memberForm');
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    // Additional validation for type-specific entry limits
    if (!validateEntryLimits()) {
        isValid = false;
    }
    
    if (!isValid) {
        showToast('Please fix all validation errors before submitting', 'error');
        return;
    }

    // Pre-submit duplicate check: removed since same MID can exist with different types/designations
    // Server will validate type+designation uniqueness
    
    isSubmitting = true;
    showLoading(true);
    
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    const editId = document.getElementById('edit_member_id').value;
    if (editId) {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating Member...';
    } else {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding Member...';
    }
    submitBtn.disabled = true;

    const formData = new FormData(form);
    
    // Replace type ID with type name (from data-name attribute)
    // This maintains backward compatibility with the database schema
    const typeSelect = document.getElementById('type');
    const selectedOption = typeSelect.options[typeSelect.selectedIndex];
    if (selectedOption && selectedOption.dataset.name) {
        formData.set('type', selectedOption.dataset.name);
    }
    
    let url = '/api/mahila-samiti-members';
    let fetchOptions = {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    };

    if (editId) {
        url = `/api/mahila-samiti-members/${encodeURIComponent(editId)}`;
        // Send a true PUT request for updates
        fetchOptions.method = 'PUT';
        // Do not append _method; send FormData directly with PUT
        fetchOptions.body = formData;
    }

    try {
        // Try primary request (PUT for edit, POST for create)
        let response = await fetch(url, fetchOptions);

        // If edit and primary failed (some servers don't accept multipart PUT), try fallback POST with _method=PUT
        if (editId && !response.ok) {
            // attempt fallback
            const fallbackForm = new FormData(form);
            fallbackForm.append('_method', 'PUT');
            
            // Replace type ID with type name for fallback too
            if (selectedOption && selectedOption.dataset.name) {
                fallbackForm.set('type', selectedOption.dataset.name);
            }
            
            const fallbackOptions = {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: fallbackForm
            };
            const fallbackResp = await fetch(url, fallbackOptions);
            response = fallbackResp;
        }

        const data = await response.json();

        if (response.ok && data.success) {
            showToast(editId ? 'Member updated successfully! Redirecting...' : 'Member added successfully! Redirecting...', 'success');
            setTimeout(() => { window.location.href = '/mahila-samiti-members'; }, 1200);
        } else {
            // Handle specific duplicate entry error
            if (data && data.message && data.message.includes('already exists')) {
                const session = document.getElementById('session').value;
                const mid = document.getElementById('mid').value;
                const errorMessage = `
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Duplicate Entry Detected</h6>
                        <p><strong>Session:</strong> ${session}</p>
                        <p><strong>MID:</strong> ${mid}</p>
                        <p class="mb-2">${data.message}</p>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-info" onclick="checkExistingEntry('${session}', '${mid}')">
                                <i class="fas fa-search"></i> Check Existing Entry
                            </button>
                            <button class="btn btn-sm btn-warning" onclick="clearFormFields()">
                                <i class="fas fa-eraser"></i> Clear Form
                            </button>
                        </div>
                    </div>
                `;
                showDetailedErrorModal('Duplicate Entry Error', errorMessage);
            }

            if (data && data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const input = document.getElementById(field) || document.querySelector(`[name="${field}"]`);
                    if (input) showFieldError(input, data.errors[field][0]);
                });
            }

            showToast((data && data.message) ? data.message : 'Error adding/updating member', 'error');
        }
    } catch (error) {
        console.error('Error submitting form:', error);
        showToast('Error adding/updating member. Please try again.', 'error');
    } finally {
        isSubmitting = false;
        showLoading(false);
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

// Toggle guardian fields based on selection
function toggleGuardianFields(guardianType) {
    const husbandField = document.getElementById('husband_field');
    const fatherField = document.getElementById('father_field');
    const husbandInput = document.getElementById('husband_name');
    const fatherInput = document.getElementById('father_name');

    const husbandHindiField = document.getElementById('husband_field_hindi');
    const husbandHindiInput = document.getElementById('husband_name_hindi');
    const fatherHindiField = document.getElementById('father_field_hindi');
    const fatherHindiInput = document.getElementById('father_name_hindi');

    if (guardianType === 'husband') {
        // Show husband fields
        husbandField.classList.remove('d-none');
        husbandHindiField.classList.remove('d-none');

        // Hide father fields
        fatherField.classList.add('d-none');
        fatherHindiField.classList.add('d-none');

        // Set required attributes appropriately
        husbandInput.setAttribute('required', 'required');
        husbandHindiInput.removeAttribute('required'); // optional; change to setAttribute if you want required
        fatherInput.removeAttribute('required');
        fatherHindiInput.removeAttribute('required');

        // Clear father inputs when switching
        fatherInput.value = '';
        fatherHindiInput.value = '';
        clearFieldError(fatherInput);
        clearFieldError(fatherHindiInput);
    } else {
        // guardianType = father
        fatherField.classList.remove('d-none');
        fatherHindiField.classList.remove('d-none');

        husbandField.classList.add('d-none');
        husbandHindiField.classList.add('d-none');

        // Set required attributes appropriately
        fatherInput.setAttribute('required', 'required');
        fatherHindiInput.removeAttribute('required'); // optional; change to setAttribute if you want required
        husbandInput.removeAttribute('required');
        husbandHindiInput.removeAttribute('required');

        // Clear husband inputs when switching
        husbandInput.value = '';
        husbandHindiInput.value = '';
        clearFieldError(husbandInput);
        clearFieldError(husbandHindiInput);
    }
}

// Reset form
function resetForm() {
    if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
        document.getElementById('memberForm').reset();
        
        // Clear photo preview
        document.getElementById('previewImage').classList.add('d-none');
        document.getElementById('noPhotoText').classList.remove('d-none');
        
        // Clear all errors
        document.querySelectorAll('.is-invalid').forEach(element => {
            element.classList.remove('is-invalid');
        });
        
        // Reset designations
        updateDesignations();
        
        // Reset guardian fields to default (husband)
        document.getElementById('husband_type').checked = true;
        toggleGuardianFields('husband');
        
        showToast('Form has been reset', 'info');
    }
}

// Show/hide loading overlay
function showLoading(show) {
    const overlay = document.getElementById('loadingOverlay');
    overlay.style.display = show ? 'flex' : 'none';
}

// Validate entry limits based on type and session
function validateEntryLimits() {
    const session = document.getElementById('session').value;
    const anchalName = document.getElementById('anchal_name').value;
    const typeSelect = document.getElementById('type');
    const typeName = typeSelect.options[typeSelect.selectedIndex]?.dataset?.name || '';
    const designation = document.getElementById('designation').value;
    
    if (!session || !anchalName || !typeName || !designation) {
        return true; // Let other validation handle required fields
    }
    
    // Special validation for PST, SANYOJAK, SANYOJIKA posts - check if trying to add posts that should be unique per session
    const typeNameLower = typeName.toLowerCase();
    if (['pst', 'sanyojak', 'sanyojika'].includes(typeNameLower)) {
        const designationSelect = document.getElementById('designation');
        const selectedOption = designationSelect.options[designationSelect.selectedIndex];
        
        // Check if the option is disabled (meaning it's already used)
        if (selectedOption && selectedOption.disabled) {
            let typeDisplayName = typeName;
            if (typeNameLower === 'sanyojak') typeDisplayName = 'संयोजक';
            else if (typeNameLower === 'sanyojika') typeDisplayName = 'संयोजिका';
            else if (typeNameLower === 'pst') typeDisplayName = 'PST';
            
            showToast(`${typeDisplayName} में "${designation}" पद इस सत्र के लिए पहले से भरा है। कृपया कोई दूसरा पद चुनें।`, 'error');
            return false;
        }
    }
    
    return true;
}

// Show toast notification
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toastContainer');
    const toastId = 'toast_' + Date.now();
    
    const bgClass = type === 'success' ? 'bg-success' : 
                   type === 'info' ? 'bg-info' : 'bg-danger';
    const icon = type === 'success' ? 'fas fa-check-circle' : 
                type === 'info' ? 'fas fa-info-circle' : 'fas fa-exclamation-circle';
    
    const toastHtml = `
        <div class="toast ${bgClass} text-white" role="alert" aria-live="assertive" aria-atomic="true" id="${toastId}" data-bs-delay="5000">
            <div class="toast-header ${bgClass} text-white">
                <i class="${icon} me-2"></i>
                <strong class="me-auto">${type === 'success' ? 'Success' : type === 'info' ? 'Info' : 'Error'}</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message.replace(/\n/g, '<br>')}
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    const toast = new bootstrap.Toast(document.getElementById(toastId));
    toast.show();
    
    // Remove toast element after it's hidden
    document.getElementById(toastId).addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

// Check if entry exists with given session and MID
function checkExistingEntry(session, mid) {
    console.log('Checking existing entry for session:', session, 'and MID:', mid); // Debugging log
    showLoading(true);
    
    fetch(`/api/mahila-samiti-members/check-duplicate?session=${encodeURIComponent(session)}&mid=${encodeURIComponent(mid)}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Server response:', data); // Debugging log
        if (data.exists) {
            const member = data.member;
            const existingMemberHtml = `
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0"><i class="fas fa-user-times"></i> Existing Member Found</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6"><strong>Name:</strong> ${member.name || 'N/A'}</div>
                            <div class="col-6"><strong>MID:</strong> ${member.mid || 'N/A'}</div>
                            <div class="col-6"><strong>Session:</strong> ${member.session || 'N/A'}</div>
                            <div class="col-6"><strong>Mobile:</strong> ${member.mobile_number || 'N/A'}</div>
                            <div class="col-6"><strong>Type:</strong> ${member.type || 'N/A'}</div>
                            <div class="col-6"><strong>Designation:</strong> ${member.designation || 'N/A'}</div>
                            <div class="col-12 mt-2"><strong>Added:</strong> ${member.created_at || 'N/A'}</div>
                        </div>
                        <div class="mt-3">
                            <a href="/mahila-samiti-members/${member.id}/edit" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit This Member
                            </a>
                            <a href="/mahila-samiti-members" class="btn btn-info btn-sm">
                                <i class="fas fa-list"></i> View All Members
                            </a>
                        </div>
                    </div>
                </div>
            `;
            showDetailedErrorModal('Existing Member Details', existingMemberHtml);
        } else {
            showToast('No existing entry found. There might be a validation issue.', 'warning');
        }
    })
    .catch(error => {
        console.error('Error checking existing entry:', error);
        showToast('Error checking existing entry', 'error');
    })
    .finally(() => {
        showLoading(false);
    });
}

// Clear all form fields
function clearFormFields() {
    if (confirm('Are you sure you want to clear all form fields?')) {
        document.getElementById('memberForm').reset();
        
        // Clear photo preview
        document.getElementById('previewImage').classList.add('d-none');
        document.getElementById('noPhotoText').classList.remove('d-none');
        document.getElementById('photo').value = '';
        
        // Reset dropdowns
        updateDesignations();
        
        // Reset guardian to default
        document.getElementById('husband_type').checked = true;
        toggleGuardianFields('husband');
        
        showToast('Form cleared successfully', 'info');
    }
}

// Show detailed error modal
function showDetailedErrorModal(title, content) {
    const modalHtml = `
        <div class="modal fade" id="detailedErrorModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ${content}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal
    const existingModal = document.getElementById('detailedErrorModal');
    if (existingModal) existingModal.remove();
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('detailedErrorModal'));
    modal.show();
}

// Hindi character validation
document.addEventListener('DOMContentLoaded', function () {
    const hindiFields = ['husband_name_hindi', 'address_hindi'];
    
    hindiFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function () {
                const hindiRegex = /^[\u0900-\u097F\s]+$/;
                if (!hindiRegex.test(this.value)) {
                    this.setCustomValidity('Please enter only Hindi characters.');
                } else {
                    this.setCustomValidity('');
                }
            });
        }
    });

    // --------- Transliteration for name_hindi (English -> Devanagari) -------------
// Debounce helper
// --------- Transliteration (English -> Devanagari) for multiple Hindi fields -------------
// Debounce helper
function debounce(fn, delay) {
    let t;
    return function(...args) {
        clearTimeout(t);
        t = setTimeout(() => fn.apply(this, args), delay);
    };
}

// Quick latin char check
function hasLatinChars(s) {
    return /[A-Za-z]/.test(s);
}

// Call Google Input Tools transliteration endpoint
async function transliterateToHindi(text) {
    if (!text || !hasLatinChars(text)) return text;
    try {
        const url = `https://inputtools.google.com/request?itc=hi-t-i0-und&num=1&text=${encodeURIComponent(text)}`;
        const resp = await fetch(url, { method: 'GET', cache: 'no-store' });
        if (!resp.ok) return text;
        const data = await resp.json();
        if (Array.isArray(data) && data[0] === 'SUCCESS' && Array.isArray(data[1]) && data[1][0] && Array.isArray(data[1][0][1])) {
            const candidates = data[1][0][1];
            if (candidates.length > 0) {
                return candidates[0];
            }
        }
        return text;
    } catch (err) {
        console.error('Transliteration error:', err);
        return text;
    }
}

// Helper to attach transliteration behavior to a field ID
function attachTransliteration(fieldId, options = {}) {
    const el = document.getElementById(fieldId);
    if (!el) return;

    let latinBuffer = '';
    let isApplying = false;

    const applyTranslit = debounce(async () => {
        if (!latinBuffer) return;
        const translit = await transliterateToHindi(latinBuffer);
        if (translit && translit !== el.value) {
            isApplying = true;
            // If options.append is true, append transliteration; otherwise replace
            if (options.append) {
                el.value = (el.value ? el.value + ' ' : '') + translit;
            } else {
                el.value = translit;
            }
            el.dispatchEvent(new Event('input', { bubbles: true }));
            isApplying = false;
        }
    }, options.debounce || 450);

    el.addEventListener('input', function (e) {
        if (isApplying) return;

        const raw = this.value || '';
        // If field currently contains no Latin, treat it as native Hindi input
        if (!hasLatinChars(raw)) {
            latinBuffer = raw; // keep in sync
            return;
        }

        // Store full field text as buffer (could be optimized to last token)
        latinBuffer = raw;
        applyTranslit();
    });

    el.addEventListener('keydown', function(e){
        if (e.key === 'Backspace' || e.key === 'Delete' || e.key.startsWith('Arrow')) {
            latinBuffer = '';
        }
    });

    el.addEventListener('paste', function (e) {
        const pasted = (e.clipboardData || window.clipboardData).getData('text') || '';
        if (!pasted) return;
        if (hasLatinChars(pasted)) {
            e.preventDefault();
            latinBuffer = pasted;
            (async () => {
                const translit = await transliterateToHindi(latinBuffer);
                if (translit) {
                    isApplying = true;
                    if (options.append) {
                        el.value = (el.value ? el.value + ' ' : '') + translit;
                    } else {
                        el.value = translit;
                    }
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                    isApplying = false;
                }
            })();
        }
    });
}

// Attach to the fields you asked for
attachTransliteration('name_hindi', { debounce: 450 });
attachTransliteration('husband_name_hindi', { debounce: 450 });
attachTransliteration('father_name_hindi', { debounce: 450 });
// For address, it's often multi-word; append mode may be nicer (keeps previous text)
attachTransliteration('address_hindi', { debounce: 450, append: false });

// Also ensure Hindi-only validation covers the added fields
const hindiValidationFields = ['husband_name_hindi', 'address_hindi', 'father_name_hindi', 'name_hindi'];
hindiValidationFields.forEach(fieldId => {
    const field = document.getElementById(fieldId);
    if (field) {
        field.addEventListener('input', function () {
            const hindiRegex = /^[\u0900-\u097F\s.,\-()\/]*$/; // allow basic punctuation often used in addresses
            if (this.value && !hindiRegex.test(this.value)) {
                this.setCustomValidity('Please enter only Hindi (Devanagari) characters or common punctuation.');
            } else {
                this.setCustomValidity('');
            }
        });
    }
});

});
</script>

@endsection
