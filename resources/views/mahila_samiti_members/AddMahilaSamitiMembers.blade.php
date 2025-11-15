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
        max-width: 100px;
        max-height: 100px;
        object-fit: cover;
    }
    .required {
        color: red;
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
                    <a href="/mahila-samiti-members/add" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Member
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Members Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="membersTable">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Session</th>
                                    <th>Anchal Name</th>
                                    <th>Type</th>
                                    <th>Designation</th>
                                    <th>MID</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>City</th>
                                    <th>State</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="membersTableBody">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let dropdownData = {};

document.addEventListener('DOMContentLoaded', function() {
    loadMembers();
});

// Load dropdown data
function loadDropdownData() {
    // This function can be removed if not needed for the list page
}

// Edit member (redirect to edit page or open in modal if needed later)
function editMember(id) {
    // For now, we can implement this later or create a separate edit page
    showToast('Edit functionality will be implemented soon', 'info');
}

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
                loadMembers();
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error deleting member', 'error');
        });
    }
}

// Load members
function loadMembers() {
    fetch('/api/mahila-samiti-members', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayMembers(data.data);
        } else {
            showToast('Error loading members', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error loading members', 'error');
    });
}

// Display members in table
function displayMembers(members) {
    const tbody = document.getElementById('membersTableBody');
    tbody.innerHTML = '';
    
    if (members.length === 0) {
        tbody.innerHTML = '<tr><td colspan="11" class="text-center">No members found</td></tr>';
        return;
    }
    
    members.forEach((member, index) => {
        tbody.innerHTML += `
            <tr>
                <td>${index + 1}</td>
                <td>${member.session}</td>
                <td>${member.anchal_name}</td>
                <td>${member.type.toUpperCase()}</td>
                <td>${member.designation}</td>
                <td>${member.mid}</td>
                <td>${member.name}</td>
                <td>${member.mobile_number}</td>
                <td>${member.city}</td>
                <td>${member.state}</td>
                <td>
                    <button class="btn btn-sm btn-primary me-1" onclick="editMember(${member.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteMember(${member.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });
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
</script>

@endsection