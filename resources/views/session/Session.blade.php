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
        --glass-bg: rgba(255, 255, 255, 0.9);
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
        padding: 24px 32px;
        margin-bottom: 24px;
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

    .page-header h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-header h2 i {
        font-size: 1.5rem;
        opacity: 0.9;
    }

    /* ============ PREMIUM BUTTON ============ */
    .btn-premium {
        background: var(--success-gradient);
        border: none;
        color: white;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
        text-decoration: none;
    }

    .btn-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(17, 153, 142, 0.4);
        color: white;
    }

    .btn-premium.danger {
        background: var(--danger-gradient);
        box-shadow: 0 4px 15px rgba(235, 51, 73, 0.3);
    }

    .btn-premium.danger:hover {
        box-shadow: 0 8px 25px rgba(235, 51, 73, 0.4);
    }

    .btn-premium.info {
        background: var(--info-gradient);
        box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
    }

    .btn-premium.info:hover {
        box-shadow: 0 8px 25px rgba(79, 172, 254, 0.4);
    }

    .btn-premium.warning {
        background: var(--warning-gradient);
        box-shadow: 0 4px 15px rgba(240, 147, 251, 0.3);
    }

    .btn-premium.warning:hover {
        box-shadow: 0 8px 25px rgba(240, 147, 251, 0.4);
    }

    /* ============ GLASS CARD ============ */
    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border-radius: var(--border-radius);
        border: 1px solid var(--glass-border);
        box-shadow: var(--shadow-soft);
        padding: 24px;
        margin-bottom: 24px;
        transition: var(--transition);
    }

    .glass-card:hover {
        box-shadow: var(--shadow-hover);
    }

    /* ============ FORM STYLES ============ */
    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        display: block;
        font-size: 14px;
    }

    .form-label i {
        margin-right: 6px;
        color: #667eea;
    }

    .form-control-premium {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 15px;
        transition: var(--transition);
        background: #f9fafb;
    }

    .form-control-premium:focus {
        outline: none;
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .form-control-premium::placeholder {
        color: #9ca3af;
    }

    /* ============ TOGGLE SWITCH ============ */
    .toggle-container {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .toggle-switch {
        position: relative;
        width: 60px;
        height: 32px;
        cursor: pointer;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #cbd5e1;
        border-radius: 32px;
        transition: var(--transition);
    }

    .toggle-slider::before {
        content: '';
        position: absolute;
        width: 26px;
        height: 26px;
        left: 3px;
        bottom: 3px;
        background: white;
        border-radius: 50%;
        transition: var(--transition);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .toggle-switch input:checked + .toggle-slider {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .toggle-switch input:checked + .toggle-slider::before {
        transform: translateX(28px);
    }

    .toggle-label {
        font-weight: 500;
        color: #6b7280;
        font-size: 14px;
    }

    .toggle-label.active {
        color: #10b981;
        font-weight: 600;
    }

    /* ============ PREMIUM TABLE ============ */
    .table-container {
        overflow-x: auto;
        border-radius: var(--border-radius);
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
        padding: 16px 20px;
        color: white;
        font-weight: 600;
        font-size: 14px;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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
        background: rgba(102, 126, 234, 0.03);
    }

    .premium-table tbody tr:hover {
        background: rgba(102, 126, 234, 0.08);
        transform: scale(1.005);
    }

    .premium-table tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: #374151;
    }

    /* ============ BADGES ============ */
    .badge-premium {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .badge-premium.shree-mahila {
        background: linear-gradient(135deg, rgba(244, 114, 182, 0.15) 0%, rgba(251, 146, 60, 0.15) 100%);
        color: #db2777;
        border: 1px solid rgba(244, 114, 182, 0.3);
    }

    .badge-premium.yuva {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(16, 185, 129, 0.15) 100%);
        color: #2563eb;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }

    .badge-premium.active {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(52, 211, 153, 0.15) 100%);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .badge-premium.inactive {
        background: linear-gradient(135deg, rgba(156, 163, 175, 0.15) 0%, rgba(107, 114, 128, 0.15) 100%);
        color: #6b7280;
        border: 1px solid rgba(156, 163, 175, 0.3);
    }

    /* ============ ACTION BUTTONS ============ */
    .action-btn {
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition);
        font-size: 16px;
    }

    .action-btn.edit {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(99, 102, 241, 0.1) 100%);
        color: #3b82f6;
    }

    .action-btn.edit:hover {
        background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
        color: white;
        transform: scale(1.1);
    }

    .action-btn.delete {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%);
        color: #ef4444;
    }

    .action-btn.delete:hover {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        transform: scale(1.1);
    }

    .action-btn.toggle {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(52, 211, 153, 0.1) 100%);
        color: #10b981;
    }

    .action-btn.toggle:hover {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
        transform: scale(1.1);
    }

    .action-btn.toggle.inactive {
        background: linear-gradient(135deg, rgba(156, 163, 175, 0.1) 0%, rgba(107, 114, 128, 0.1) 100%);
        color: #9ca3af;
    }

    .action-btn.toggle.inactive:hover {
        background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
        color: white;
    }

    /* ============ MODAL STYLES ============ */
    .modal-premium .modal-content {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .modal-premium .modal-header {
        background: var(--primary-gradient);
        color: white;
        border-radius: var(--border-radius) var(--border-radius) 0 0;
        padding: 20px 24px;
        border-bottom: none;
    }

    .modal-premium .modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }

    .modal-premium .modal-header .btn-close:hover {
        opacity: 1;
    }

    .modal-premium .modal-title {
        font-weight: 700;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-premium .modal-body {
        padding: 24px;
    }

    .modal-premium .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid #f1f5f9;
    }

    /* ============ TOAST STYLES ============ */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .toast-premium {
        min-width: 320px;
        padding: 16px 20px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: white;
        font-weight: 500;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        animation: slideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .toast-premium.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .toast-premium.error {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .toast-premium.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .toast-premium.info {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    .toast-premium i {
        font-size: 20px;
    }

    .toast-premium .toast-close {
        margin-left: auto;
        background: none;
        border: none;
        color: white;
        opacity: 0.7;
        cursor: pointer;
        font-size: 18px;
        padding: 0;
    }

    .toast-premium .toast-close:hover {
        opacity: 1;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
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
            transform: translateX(100%);
            opacity: 0;
        }
    }

    /* ============ LOADING SPINNER ============ */
    .spinner-premium {
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
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
        padding: 60px 20px;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .empty-state h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 8px;
    }

    .empty-state p {
        font-size: 14px;
    }

    /* ============ LOADING OVERLAY ============ */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9998;
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
        width: 50px;
        height: 50px;
        border: 4px solid rgba(102, 126, 234, 0.2);
        border-top-color: #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 16px;
    }

    .loading-content p {
        color: #6b7280;
        font-weight: 500;
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
    <h2><i class="bi bi-calendar3"></i> Session Management</h2>
    <button class="btn-premium" onclick="openAddModal()">
        <i class="bi bi-plus-lg"></i> नया Session जोड़ें
    </button>
</div>

<!-- Session Form Card -->
<div class="glass-card" id="form-card" style="display: none;">
    <h5 style="font-weight: 700; color: #374151; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
        <i class="bi bi-pencil-square" style="color: #667eea;"></i>
        <span id="form-title">नया Session जोड़ें</span>
    </h5>
    
    <form id="session-form">
        <input type="hidden" id="session-id" value="">
        
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-type"></i> Session का नाम <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" id="session-name" class="form-control-premium" placeholder="Session का नाम दर्ज करें">
                    <small id="name-error" class="text-danger" style="display: none;"></small>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-tag"></i> प्रकार <span style="color: #ef4444;">*</span>
                    </label>
                    <select id="session-type" class="form-control-premium">
                        <option value="">-- प्रकार चुनें --</option>
                        <option value="shree/mahila">श्री / महिला</option>
                        <option value="yuva">युवा</option>
                    </select>
                    <small id="type-error" class="text-danger" style="display: none;"></small>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-toggle-on"></i> स्थिति
                    </label>
                    <div class="toggle-container" style="margin-top: 8px;">
                        <span class="toggle-label" id="toggle-inactive-label">निष्क्रिय</span>
                        <label class="toggle-switch">
                            <input type="checkbox" id="session-active">
                            <span class="toggle-slider"></span>
                        </label>
                        <span class="toggle-label" id="toggle-active-label">सक्रिय</span>
                    </div>
                    <small class="text-muted" style="font-size: 12px;">* प्रति प्रकार केवल एक Session सक्रिय रह सकता है</small>
                </div>
            </div>
        </div>
        
        <div style="display: flex; gap: 12px; margin-top: 10px;">
            <button type="submit" class="btn-premium" id="submit-btn">
                <i class="bi bi-check-lg"></i> <span id="submit-text">Session जोड़ें</span>
            </button>
            <button type="button" class="btn-premium danger" onclick="cancelForm()">
                <i class="bi bi-x-lg"></i> रद्द करें
            </button>
        </div>
    </form>
</div>

<!-- Sessions Table -->
<div class="glass-card">
    <h5 style="font-weight: 700; color: #374151; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
        <i class="bi bi-table" style="color: #667eea;"></i>
        सभी Sessions
    </h5>
    
    <div class="table-container">
        <table class="premium-table" id="sessions-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Session नाम</th>
                    <th>प्रकार</th>
                    <th>स्थिति</th>
                    <th>बनाया गया</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="sessions-tbody">
                <!-- Data will be loaded here -->
            </tbody>
        </table>
    </div>
    
    <div id="empty-state" class="empty-state" style="display: none;">
        <i class="bi bi-inbox"></i>
        <h3>कोई Session नहीं मिला</h3>
        <p>नया Session जोड़ने के लिए ऊपर बटन पर क्लिक करें</p>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade modal-premium" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--danger-gradient);">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle"></i> हटाने की पुष्टि
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p style="font-size: 16px; color: #374151;">
                    क्या आप वाकई <strong id="delete-session-name"></strong> को हटाना चाहते हैं?
                </p>
                <p style="color: #ef4444; font-size: 14px;">
                    <i class="bi bi-info-circle"></i> यह क्रिया पूर्ववत नहीं की जा सकती।
                </p>
                <input type="hidden" id="delete-session-id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-premium" style="background: #9ca3af; box-shadow: none;" data-bs-dismiss="modal">
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
    const API_BASE = '/api/sessions';
    let deleteModal;

    document.addEventListener('DOMContentLoaded', function() {
        deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        loadSessions();
        
        // Toggle label active state
        document.getElementById('session-active').addEventListener('change', function() {
            updateToggleLabels(this.checked);
        });
        
        // Form submit handler
        document.getElementById('session-form').addEventListener('submit', handleFormSubmit);
    });

    function updateToggleLabels(isActive) {
        const inactiveLabel = document.getElementById('toggle-inactive-label');
        const activeLabel = document.getElementById('toggle-active-label');
        
        if (isActive) {
            inactiveLabel.classList.remove('active');
            activeLabel.classList.add('active');
        } else {
            inactiveLabel.classList.add('active');
            activeLabel.classList.remove('active');
        }
    }

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
            <span>${message}</span>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="bi bi-x"></i>
            </button>
        `;
        
        container.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards';
            setTimeout(() => toast.remove(), 400);
        }, 5000);
    }

    // Show/Hide loading overlay
    function showLoading() {
        document.getElementById('loading-overlay').classList.add('show');
    }

    function hideLoading() {
        document.getElementById('loading-overlay').classList.remove('show');
    }

    // Load all sessions
    async function loadSessions() {
        try {
            const response = await fetch(API_BASE);
            const result = await response.json();
            
            if (result.success) {
                renderSessions(result.data);
            } else {
                showToast('Sessions लोड करने में त्रुटि', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Server से कनेक्ट नहीं हो पाया', 'error');
        }
    }

    // Render sessions table
    function renderSessions(sessions) {
        const tbody = document.getElementById('sessions-tbody');
        const emptyState = document.getElementById('empty-state');
        const table = document.getElementById('sessions-table');
        
        if (sessions.length === 0) {
            table.style.display = 'none';
            emptyState.style.display = 'block';
            return;
        }
        
        table.style.display = 'table';
        emptyState.style.display = 'none';
        
        tbody.innerHTML = sessions.map((session, index) => `
            <tr>
                <td><strong>${index + 1}</strong></td>
                <td>
                    <strong style="color: #1f2937;">${session.name}</strong>
                </td>
                <td>
                    <span class="badge-premium ${session.type === 'yuva' ? 'yuva' : 'shree-mahila'}">
                        <i class="bi ${session.type === 'yuva' ? 'bi-people' : 'bi-person-heart'}"></i>
                        ${session.type === 'yuva' ? 'युवा' : 'श्री / महिला'}
                    </span>
                </td>
                <td>
                    <span class="badge-premium ${session.is_active ? 'active' : 'inactive'}">
                        <i class="bi ${session.is_active ? 'bi-check-circle' : 'bi-x-circle'}"></i>
                        ${session.is_active ? 'सक्रिय' : 'निष्क्रिय'}
                    </span>
                </td>
                <td>
                    <span style="color: #6b7280; font-size: 13px;">
                        ${new Date(session.created_at).toLocaleDateString('hi-IN', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        })}
                    </span>
                </td>
                <td>
                    <div style="display: flex; gap: 8px;">
                        <button class="action-btn toggle ${session.is_active ? '' : 'inactive'}" 
                                onclick="toggleActive(${session.id})" 
                                title="${session.is_active ? 'निष्क्रिय करें' : 'सक्रिय करें'}">
                            <i class="bi ${session.is_active ? 'bi-toggle-on' : 'bi-toggle-off'}"></i>
                        </button>
                        <button class="action-btn edit" onclick="editSession(${session.id})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="action-btn delete" onclick="deleteSession(${session.id}, '${session.name}')" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    // Open add form
    function openAddModal() {
        document.getElementById('form-card').style.display = 'block';
        document.getElementById('form-title').textContent = 'नया Session जोड़ें';
        document.getElementById('submit-text').textContent = 'Session जोड़ें';
        document.getElementById('session-id').value = '';
        document.getElementById('session-form').reset();
        updateToggleLabels(false);
        clearErrors();
        
        // Scroll to form
        document.getElementById('form-card').scrollIntoView({ behavior: 'smooth' });
    }

    // Cancel form
    function cancelForm() {
        document.getElementById('form-card').style.display = 'none';
        document.getElementById('session-form').reset();
        clearErrors();
    }

    // Clear errors
    function clearErrors() {
        document.getElementById('name-error').style.display = 'none';
        document.getElementById('type-error').style.display = 'none';
    }

    // Show field error
    function showFieldError(field, message) {
        const errorEl = document.getElementById(`${field}-error`);
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.style.display = 'block';
        }
    }

    // Handle form submit
    async function handleFormSubmit(e) {
        e.preventDefault();
        clearErrors();
        
        const id = document.getElementById('session-id').value;
        const name = document.getElementById('session-name').value.trim();
        const type = document.getElementById('session-type').value;
        const isActive = document.getElementById('session-active').checked;
        
        // Client-side validation
        let hasError = false;
        
        if (!name) {
            showFieldError('name', 'Session का नाम आवश्यक है।');
            showToast('Session का नाम दर्ज करें', 'error');
            hasError = true;
        }
        
        if (!type) {
            showFieldError('type', 'प्रकार चुनना आवश्यक है।');
            showToast('प्रकार चुनें', 'error');
            hasError = true;
        }
        
        if (hasError) return;
        
        showLoading();
        
        const data = { name, type, is_active: isActive };
        const url = id ? `${API_BASE}/${id}` : API_BASE;
        const method = id ? 'PUT' : 'POST';
        
        try {
            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            hideLoading();
            
            if (result.success) {
                showToast(result.message, 'success');
                cancelForm();
                loadSessions();
            } else {
                if (result.errors) {
                    Object.keys(result.errors).forEach(key => {
                        showFieldError(key, result.errors[key][0]);
                        showToast(result.errors[key][0], 'error');
                    });
                } else {
                    showToast(result.message || 'त्रुटि हुई', 'error');
                }
            }
        } catch (error) {
            hideLoading();
            console.error('Error:', error);
            showToast('Server से कनेक्ट नहीं हो पाया', 'error');
        }
    }

    // Edit session
    async function editSession(id) {
        showLoading();
        
        try {
            const response = await fetch(`${API_BASE}/${id}`);
            const result = await response.json();
            
            hideLoading();
            
            if (result.success) {
                const session = result.data;
                
                document.getElementById('form-card').style.display = 'block';
                document.getElementById('form-title').textContent = 'Session अपडेट करें';
                document.getElementById('submit-text').textContent = 'अपडेट करें';
                document.getElementById('session-id').value = session.id;
                document.getElementById('session-name').value = session.name;
                document.getElementById('session-type').value = session.type;
                document.getElementById('session-active').checked = session.is_active;
                updateToggleLabels(session.is_active);
                clearErrors();
                
                document.getElementById('form-card').scrollIntoView({ behavior: 'smooth' });
            } else {
                showToast('Session डेटा लोड नहीं हो पाया', 'error');
            }
        } catch (error) {
            hideLoading();
            console.error('Error:', error);
            showToast('Server से कनेक्ट नहीं हो पाया', 'error');
        }
    }

    // Toggle active status
    async function toggleActive(id) {
        showLoading();
        
        try {
            const response = await fetch(`${API_BASE}/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            
            const result = await response.json();
            
            hideLoading();
            
            if (result.success) {
                showToast(result.message, 'success');
                loadSessions();
            } else {
                showToast(result.message || 'Status बदलने में त्रुटि', 'error');
            }
        } catch (error) {
            hideLoading();
            console.error('Error:', error);
            showToast('Server से कनेक्ट नहीं हो पाया', 'error');
        }
    }

    // Delete session (show modal)
    function deleteSession(id, name) {
        document.getElementById('delete-session-id').value = id;
        document.getElementById('delete-session-name').textContent = name;
        deleteModal.show();
    }

    // Confirm delete
    async function confirmDelete() {
        const id = document.getElementById('delete-session-id').value;
        
        deleteModal.hide();
        showLoading();
        
        try {
            const response = await fetch(`${API_BASE}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            const result = await response.json();
            
            hideLoading();
            
            if (result.success) {
                showToast(result.message, 'success');
                loadSessions();
            } else {
                showToast(result.message || 'हटाने में त्रुटि', 'error');
            }
        } catch (error) {
            hideLoading();
            console.error('Error:', error);
            showToast('Server से कनेक्ट नहीं हो पाया', 'error');
        }
    }
</script>
@endsection
