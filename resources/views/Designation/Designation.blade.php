@extends('includes.layouts.super_admin')

@section('content')
<style>
    /* ============ PREMIUM VARIABLES ============ */
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        --danger-gradient: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --dark-gradient: linear-gradient(135deg, #232526 0%, #414345 100%);
        --shree-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --mahila-gradient: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
        --yuva-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        --all-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --glass-bg: rgba(255, 255, 255, 0.95);
        --glass-border: rgba(255, 255, 255, 0.2);
        --shadow-soft: 0 8px 32px rgba(0, 0, 0, 0.1);
        --shadow-hover: 0 12px 40px rgba(102, 126, 234, 0.3);
        --border-radius: 16px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ============ PAGE HEADER ============ */
    .page-header {
        background: var(--primary-gradient);
        border-radius: var(--border-radius);
        padding: 28px 36px;
        margin-bottom: 28px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .page-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
    }

    .page-header h2 {
        font-size: 1.85rem;
        font-weight: 800;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 14px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .page-header h2 i {
        font-size: 1.6rem;
        opacity: 0.95;
        background: rgba(255,255,255,0.2);
        padding: 10px;
        border-radius: 12px;
    }

    /* ============ PREMIUM BUTTON ============ */
    .btn-premium {
        background: var(--success-gradient);
        border: none;
        color: white;
        padding: 14px 28px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 15px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4);
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }

    .btn-premium::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: 0.5s;
    }

    .btn-premium:hover::before {
        left: 100%;
    }

    .btn-premium:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(17, 153, 142, 0.5);
        color: white;
    }

    .btn-premium.danger {
        background: var(--danger-gradient);
        box-shadow: 0 4px 15px rgba(235, 51, 73, 0.4);
    }

    .btn-premium.danger:hover {
        box-shadow: 0 8px 25px rgba(235, 51, 73, 0.5);
    }

    .btn-premium.info {
        background: var(--info-gradient);
        box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
    }

    .btn-premium.info:hover {
        box-shadow: 0 8px 25px rgba(79, 172, 254, 0.5);
    }

    .btn-premium.secondary {
        background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
        box-shadow: 0 4px 15px rgba(156, 163, 175, 0.4);
    }

    .btn-premium.secondary:hover {
        box-shadow: 0 8px 25px rgba(156, 163, 175, 0.5);
    }

    /* ============ GLASS CARD ============ */
    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border-radius: var(--border-radius);
        border: 1px solid var(--glass-border);
        box-shadow: var(--shadow-soft);
        padding: 28px;
        margin-bottom: 28px;
        transition: var(--transition);
    }

    .glass-card:hover {
        box-shadow: var(--shadow-hover);
        transform: translateY(-2px);
    }

    .card-title {
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.15rem;
    }

    .card-title i {
        color: #667eea;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        padding: 8px;
        border-radius: 10px;
    }

    /* ============ FORM STYLES ============ */
    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        font-weight: 700;
        color: #374151;
        margin-bottom: 10px;
        display: block;
        font-size: 14px;
        letter-spacing: 0.3px;
    }

    .form-label i {
        margin-right: 8px;
        color: #667eea;
    }

    .form-label .required {
        color: #ef4444;
        font-weight: 800;
    }

    .form-control-premium {
        width: 100%;
        padding: 16px 20px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 15px;
        transition: var(--transition);
        background: #f9fafb;
        font-weight: 500;
    }

    .form-control-premium:focus {
        outline: none;
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
    }

    .form-control-premium::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }

    .form-control-premium.error {
        border-color: #ef4444;
        background: #fef2f2;
    }

    .form-control-premium.error:focus {
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15);
    }

    .error-message {
        color: #ef4444;
        font-size: 13px;
        margin-top: 6px;
        display: none;
        font-weight: 500;
    }

    .error-message i {
        margin-right: 4px;
    }

    /* ============ PREMIUM TABLE ============ */
    .table-container {
        overflow-x: auto;
        border-radius: var(--border-radius);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .premium-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .premium-table thead {
        background: var(--primary-gradient);
    }

    .premium-table thead th {
        padding: 18px 22px;
        color: white;
        font-weight: 700;
        font-size: 14px;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        border: none;
    }

    .premium-table thead th:first-child {
        border-radius: 12px 0 0 0;
    }

    .premium-table thead th:last-child {
        border-radius: 0 12px 0 0;
    }

    .premium-table tbody tr {
        transition: var(--transition);
    }

    .premium-table tbody tr:nth-child(even) {
        background: rgba(102, 126, 234, 0.04);
    }

    .premium-table tbody tr:hover {
        background: rgba(102, 126, 234, 0.1);
        transform: scale(1.002);
    }

    .premium-table tbody td {
        padding: 18px 22px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: #374151;
        font-weight: 500;
    }

    /* ============ BADGES ============ */
    .badge-premium {
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        letter-spacing: 0.3px;
    }

    .badge-premium.shree_sangh {
        background: linear-gradient(135deg, rgba(244, 114, 182, 0.2) 0%, rgba(251, 146, 60, 0.2) 100%);
        color: #be185d;
        border: 1px solid rgba(244, 114, 182, 0.4);
    }

    .badge-premium.mahila_samiti {
        background: linear-gradient(135deg, rgba(236, 72, 153, 0.15) 0%, rgba(219, 39, 119, 0.15) 100%);
        color: #db2777;
        border: 1px solid rgba(236, 72, 153, 0.3);
    }

    .badge-premium.yuva_sangh {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(16, 185, 129, 0.15) 100%);
        color: #2563eb;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }

    .badge-premium.for_all {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
        color: #6366f1;
        border: 1px solid rgba(102, 126, 234, 0.3);
    }

    /* ============ ACTION BUTTONS ============ */
    .action-btn {
        width: 40px;
        height: 40px;
        border: none;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition);
        font-size: 17px;
        margin: 0 3px;
    }

    .action-btn.edit {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(99, 102, 241, 0.15) 100%);
        color: #3b82f6;
    }

    .action-btn.edit:hover {
        background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
        color: white;
        transform: scale(1.15) rotate(-5deg);
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
    }

    .action-btn.delete {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(220, 38, 38, 0.15) 100%);
        color: #ef4444;
    }

    .action-btn.delete:hover {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        transform: scale(1.15) rotate(5deg);
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
    }

    /* ============ MODAL STYLES ============ */
    .modal-premium .modal-content {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: 0 25px 60px -12px rgba(0, 0, 0, 0.35);
        overflow: hidden;
    }

    .modal-premium .modal-header {
        background: var(--primary-gradient);
        color: white;
        padding: 22px 28px;
        border-bottom: none;
    }

    .modal-premium .modal-header.danger {
        background: var(--danger-gradient);
    }

    .modal-premium .modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.85;
        transition: var(--transition);
    }

    .modal-premium .modal-header .btn-close:hover {
        opacity: 1;
        transform: rotate(90deg);
    }

    .modal-premium .modal-title {
        font-weight: 800;
        font-size: 1.3rem;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .modal-premium .modal-body {
        padding: 28px;
    }

    .modal-premium .modal-footer {
        padding: 18px 28px;
        border-top: 1px solid #f1f5f9;
        background: #fafbfc;
    }

    /* ============ TOAST STYLES ============ */
    .toast-container {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .toast-premium {
        min-width: 340px;
        padding: 18px 24px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        gap: 14px;
        color: white;
        font-weight: 600;
        box-shadow: 0 12px 45px rgba(0, 0, 0, 0.25);
        animation: slideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
    }

    .toast-premium.success {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.95) 0%, rgba(5, 150, 105, 0.95) 100%);
    }

    .toast-premium.error {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.95) 0%, rgba(220, 38, 38, 0.95) 100%);
    }

    .toast-premium.warning {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.95) 0%, rgba(217, 119, 6, 0.95) 100%);
    }

    .toast-premium.info {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.95) 0%, rgba(37, 99, 235, 0.95) 100%);
    }

    .toast-premium i {
        font-size: 22px;
    }

    .toast-premium .toast-message {
        flex: 1;
        font-size: 14px;
    }

    .toast-premium .toast-close {
        background: none;
        border: none;
        color: white;
        opacity: 0.8;
        cursor: pointer;
        font-size: 20px;
        padding: 0;
        transition: var(--transition);
    }

    .toast-premium .toast-close:hover {
        opacity: 1;
        transform: rotate(90deg);
    }

    @keyframes slideIn {
        from {
            transform: translateX(120%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(120%);
            opacity: 0;
        }
    }

    /* ============ LOADING SPINNER ============ */
    .spinner-premium {
        width: 22px;
        height: 22px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* ============ EMPTY STATE ============ */
    .empty-state {
        text-align: center;
        padding: 70px 20px;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 80px;
        margin-bottom: 20px;
        opacity: 0.4;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .empty-state h3 {
        font-size: 1.4rem;
        font-weight: 700;
        color: #6b7280;
        margin-bottom: 10px;
    }

    .empty-state p {
        font-size: 15px;
    }

    /* ============ LOADING OVERLAY ============ */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(6px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 99998;
        opacity: 0;
        visibility: hidden;
        transition: var(--transition);
    }

    .loading-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .loading-content {
        text-align: center;
    }

    .loading-content .spinner-large {
        width: 60px;
        height: 60px;
        border: 5px solid rgba(102, 126, 234, 0.2);
        border-top-color: #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }

    .loading-content p {
        color: #6b7280;
        font-weight: 600;
        font-size: 16px;
    }

    /* ============ RESPONSIVE ============ */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 16px;
            text-align: center;
            padding: 20px;
        }

        .page-header h2 {
            font-size: 1.4rem;
        }

        .btn-premium {
            width: 100%;
            justify-content: center;
        }

        .glass-card {
            padding: 20px;
        }

        .premium-table thead th,
        .premium-table tbody td {
            padding: 12px 14px;
            font-size: 13px;
        }
    }
</style>

<!-- Toast Container -->
<div id="toast-container" class="toast-container"></div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="loading-overlay">
    <div class="loading-content">
        <div class="spinner-large"></div>
        <p>कृपया प्रतीक्षा करें...</p>
    </div>
</div>

<!-- Page Header -->
<div class="page-header">
    <h2><i class="bi bi-award"></i> Designation Management</h2>
    <button class="btn-premium" onclick="openAddModal()">
        <i class="bi bi-plus-lg"></i> नया Designation जोड़ें
    </button>
</div>

<!-- Designation Form Card -->
<div class="glass-card" id="form-card" style="display: none;">
    <h5 class="card-title">
        <i class="bi bi-pencil-square"></i>
        <span id="form-title">नया Designation जोड़ें</span>
    </h5>
    
    <form id="designation-form">
        <input type="hidden" id="designation-id" value="">
        
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-type"></i> Designation नाम <span class="required">*</span>
                    </label>
                    <input type="text" id="designation-name" class="form-control-premium" placeholder="Designation का नाम दर्ज करें">
                    <div id="name-error" class="error-message"><i class="bi bi-exclamation-circle"></i> <span></span></div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-tag"></i> Designation Type <span class="required">*</span>
                    </label>
                    <select id="designation-type-id" class="form-control-premium">
                        <option value="">-- Designation Type चुनें --</option>
                        <!-- Options will be loaded dynamically -->
                    </select>
                    <div id="designation_type_id-error" class="error-message"><i class="bi bi-exclamation-circle"></i> <span></span></div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-building"></i> Designation Department <span class="required">*</span>
                    </label>
                    <select id="designation-dept" class="form-control-premium">
                        <option value="">-- Department चुनें --</option>
                        <option value="shree_sangh">श्री संघ</option>
                        <option value="mahila_samiti">महिला समिति</option>
                        <option value="yuva_sangh">युवा संघ</option>
                        <option value="all">सभी (All)</option>
                    </select>
                    <div id="designation_dept-error" class="error-message"><i class="bi bi-exclamation-circle"></i> <span></span></div>
                </div>
            </div>
        </div>
        
        <div style="display: flex; gap: 14px; margin-top: 12px;">
            <button type="submit" class="btn-premium" id="submit-btn">
                <i class="bi bi-check-lg"></i> <span id="submit-text">Designation जोड़ें</span>
            </button>
            <button type="button" class="btn-premium secondary" onclick="cancelForm()">
                <i class="bi bi-x-lg"></i> रद्द करें
            </button>
        </div>
    </form>
</div>

<!-- Designations Table -->
<div class="glass-card">
    <h5 class="card-title">
        <i class="bi bi-table"></i>
        सभी Designations
    </h5>
    
    <div class="table-container">
        <table class="premium-table" id="designation-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Designation नाम</th>
                    <th>Designation Type</th>
                    <th>Department</th>
                    <th>बनाया गया</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="designation-tbody">
                <!-- Data will be loaded here -->
            </tbody>
        </table>
    </div>
    
    <div id="empty-state" class="empty-state" style="display: none;">
        <i class="bi bi-inbox"></i>
        <h3>कोई Designation नहीं मिला</h3>
        <p>नया Designation जोड़ने के लिए ऊपर बटन पर क्लिक करें</p>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade modal-premium" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header danger">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle"></i> हटाने की पुष्टि
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p style="font-size: 16px; color: #374151; margin-bottom: 12px;">
                    क्या आप वाकई <strong id="delete-designation-name" style="color: #667eea;"></strong> को हटाना चाहते हैं?
                </p>
                <p style="color: #ef4444; font-size: 14px; background: #fef2f2; padding: 12px; border-radius: 10px; margin: 0;">
                    <i class="bi bi-info-circle"></i> यह क्रिया पूर्ववत नहीं की जा सकती।
                </p>
                <input type="hidden" id="delete-designation-id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-premium secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i> रद्द करें
                </button>
                <button type="button" class="btn-premium danger" onclick="confirmDelete()">
                    <i class="bi bi-trash"></i> हटाएं
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const API_BASE = '/api/designations';
    const API_DESIGNATION_TYPES = '/api/designations/get-designation-types';
    let deleteModal;
    let designationTypesCache = [];

    // Type labels for display
    const typeLabels = {
        'shree_sangh': 'श्री संघ',
        'mahila_samiti': 'महिला समिति',
        'yuva_sangh': 'युवा संघ',
        'for_all': 'सभी के लिए'
    };

    // Department labels for display
    const deptLabels = {
        'shree_sangh': 'श्री संघ',
        'mahila_samiti': 'महिला समिति',
        'yuva_sangh': 'युवा संघ',
        'all': 'सभी (All)'
    };

    document.addEventListener('DOMContentLoaded', function() {
        deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        loadDesignationTypes();
        loadDesignations();
        
        // Form submit handler
        document.getElementById('designation-form').addEventListener('submit', handleFormSubmit);
    });

    // Toast notification function
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast-premium ${type}`;
        
        const icons = {
            success: 'bi-check-circle-fill',
            error: 'bi-x-circle-fill',
            warning: 'bi-exclamation-circle-fill',
            info: 'bi-info-circle-fill'
        };
        
        toast.innerHTML = `
            <i class="bi ${icons[type]}"></i>
            <span class="toast-message">${message}</span>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="bi bi-x"></i>
            </button>
        `;
        
        container.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards';
            setTimeout(() => toast.remove(), 500);
        }, 5000);
    }

    // Show/Hide loading overlay
    function showLoading() {
        document.getElementById('loading-overlay').classList.add('show');
    }

    function hideLoading() {
        document.getElementById('loading-overlay').classList.remove('show');
    }

    // Clear form errors
    function clearErrors() {
        document.querySelectorAll('.form-control-premium').forEach(el => el.classList.remove('error'));
        document.querySelectorAll('.error-message').forEach(el => {
            el.style.display = 'none';
            el.querySelector('span').textContent = '';
        });
    }

    // Show field error
    function showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorEl = document.getElementById(fieldId.replace('designation-', '') + '-error');
        
        if (field) field.classList.add('error');
        if (errorEl) {
            errorEl.style.display = 'block';
            errorEl.querySelector('span').textContent = message;
        }
    }

    // Load designation types for dropdown
    async function loadDesignationTypes() {
        try {
            const response = await fetch(API_DESIGNATION_TYPES);
            const result = await response.json();
            
            if (result.success) {
                designationTypesCache = result.data;
                populateDesignationTypeDropdown(result.data);
            } else {
                showToast('Designation Types लोड करने में त्रुटि हुई।', 'error');
            }
        } catch (error) {
            console.error('Error loading designation types:', error);
            showToast('सर्वर से कनेक्ट नहीं हो पाया।', 'error');
        }
    }

    // Populate designation type dropdown
    function populateDesignationTypeDropdown(types) {
        const select = document.getElementById('designation-type-id');
        select.innerHTML = '<option value="">-- Designation Type चुनें --</option>';
        
        types.forEach(type => {
            const option = document.createElement('option');
            option.value = type.id;
            option.textContent = `${type.name} (${typeLabels[type.type] || type.type})`;
            select.appendChild(option);
        });
    }

    // Load all designations
    async function loadDesignations() {
        try {
            const response = await fetch(API_BASE);
            const result = await response.json();
            
            if (result.success) {
                renderDesignations(result.data);
            } else {
                showToast('डेटा लोड करने में त्रुटि हुई।', 'error');
            }
        } catch (error) {
            console.error('Error loading designations:', error);
            showToast('सर्वर से कनेक्ट नहीं हो पाया।', 'error');
        }
    }

    // Render designations in table
    function renderDesignations(designations) {
        const tbody = document.getElementById('designation-tbody');
        const emptyState = document.getElementById('empty-state');
        const table = document.getElementById('designation-table');
        
        if (designations.length === 0) {
            table.style.display = 'none';
            emptyState.style.display = 'block';
            return;
        }
        
        table.style.display = 'table';
        emptyState.style.display = 'none';
        
        tbody.innerHTML = designations.map((item, index) => `
            <tr>
                <td><strong>${index + 1}</strong></td>
                <td style="font-weight: 600;">${item.name}</td>
                <td>${item.designation_type ? item.designation_type.name : 'N/A'}</td>
                <td>
                    <span class="badge-premium ${item.designation_dept === 'all' ? 'for_all' : item.designation_dept}">
                        <i class="bi bi-building"></i>
                        ${deptLabels[item.designation_dept] || item.designation_dept || 'N/A'}
                    </span>
                </td>
                <td>${formatDate(item.created_at)}</td>
                <td>
                    <button class="action-btn edit" onclick="editDesignation(${item.id})" title="संपादित करें">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button class="action-btn delete" onclick="deleteDesignation(${item.id}, '${item.name}')" title="हटाएं">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    // Format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('hi-IN', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }

    // Open add modal
    function openAddModal() {
        clearErrors();
        document.getElementById('form-card').style.display = 'block';
        document.getElementById('form-title').textContent = 'नया Designation जोड़ें';
        document.getElementById('submit-text').textContent = 'Designation जोड़ें';
        document.getElementById('designation-id').value = '';
        document.getElementById('designation-name').value = '';
        document.getElementById('designation-type-id').value = '';
        document.getElementById('designation-dept').value = '';
        
        // Scroll to form
        document.getElementById('form-card').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Cancel form
    function cancelForm() {
        document.getElementById('form-card').style.display = 'none';
        clearErrors();
    }

    // Edit designation
    async function editDesignation(id) {
        showLoading();
        try {
            const response = await fetch(`${API_BASE}/${id}`);
            const result = await response.json();
            
            if (result.success) {
                const data = result.data;
                clearErrors();
                document.getElementById('form-card').style.display = 'block';
                document.getElementById('form-title').textContent = 'Designation संपादित करें';
                document.getElementById('submit-text').textContent = 'अपडेट करें';
                document.getElementById('designation-id').value = data.id;
                document.getElementById('designation-name').value = data.name;
                document.getElementById('designation-type-id').value = data.designation_type_id;
                document.getElementById('designation-dept').value = data.designation_dept || 'all';
                
                // Scroll to form
                document.getElementById('form-card').scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                showToast(result.message || 'डेटा लोड करने में त्रुटि।', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('सर्वर से कनेक्ट नहीं हो पाया।', 'error');
        } finally {
            hideLoading();
        }
    }

    // Handle form submit
    async function handleFormSubmit(e) {
        e.preventDefault();
        clearErrors();
        
        const id = document.getElementById('designation-id').value;
        const name = document.getElementById('designation-name').value.trim();
        const designation_type_id = document.getElementById('designation-type-id').value;
        const designation_dept = document.getElementById('designation-dept').value;
        
        // Client-side validation
        let hasError = false;
        
        if (!name) {
            showFieldError('designation-name', 'Designation का नाम आवश्यक है।');
            hasError = true;
        } else if (name.length > 255) {
            showFieldError('designation-name', 'Designation का नाम 255 characters से अधिक नहीं हो सकता।');
            hasError = true;
        }
        
        if (!designation_type_id) {
            showFieldError('designation-type-id', 'Designation Type चुनना आवश्यक है।');
            hasError = true;
        }
        
        if (!designation_dept) {
            showFieldError('designation-dept', 'Designation Department चुनना आवश्यक है।');
            hasError = true;
        }
        
        if (hasError) {
            showToast('कृपया सभी आवश्यक फ़ील्ड भरें।', 'error');
            return;
        }
        
        const submitBtn = document.getElementById('submit-btn');
        const originalContent = submitBtn.innerHTML;
        submitBtn.innerHTML = '<div class="spinner-premium"></div> प्रतीक्षा करें...';
        submitBtn.disabled = true;
        
        try {
            const url = id ? `${API_BASE}/${id}` : API_BASE;
            const method = id ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ name, designation_type_id, designation_dept })
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast(result.message, 'success');
                cancelForm();
                loadDesignations();
            } else {
                if (result.errors) {
                    Object.keys(result.errors).forEach(key => {
                        showFieldError(`designation-${key}`, result.errors[key][0]);
                    });
                }
                showToast(result.message || 'त्रुटि हुई।', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('सर्वर से कनेक्ट नहीं हो पाया।', 'error');
        } finally {
            submitBtn.innerHTML = originalContent;
            submitBtn.disabled = false;
        }
    }

    // Delete designation
    function deleteDesignation(id, name) {
        document.getElementById('delete-designation-id').value = id;
        document.getElementById('delete-designation-name').textContent = name;
        deleteModal.show();
    }

    // Confirm delete
    async function confirmDelete() {
        const id = document.getElementById('delete-designation-id').value;
        
        showLoading();
        deleteModal.hide();
        
        try {
            const response = await fetch(`${API_BASE}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast(result.message, 'success');
                loadDesignations();
            } else {
                showToast(result.message || 'हटाने में त्रुटि।', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('सर्वर से कनेक्ट नहीं हो पाया।', 'error');
        } finally {
            hideLoading();
        }
    }
</script>
@endsection
