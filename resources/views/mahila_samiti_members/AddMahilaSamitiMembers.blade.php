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
                        <small class="text-muted ms-3">Data is loaded per selected session only.</small>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="membersTable">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Session</th>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Designation</th>
                                    <th>MID</th>
                                    <th>Anchal Name</th>
                                    <th>Phone</th>
                                    <th>City</th>
                                    <th>State</th>
                                    <th>Actions</th>
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
let dropdownData = {};

document.addEventListener('DOMContentLoaded', function() {
    // Load available sessions into the dropdown
    loadSessions();
    document.getElementById('loadSessionBtn').addEventListener('click', function() {
        const session = document.getElementById('sessionSelect').value;
        if (!session) {
            showToast('Please select a session first', 'info');
            return;
        }
        loadMembersBySession(session);
    });
});

function loadSessions() {
    // Use existing dropdown-data endpoint which returns sessions, anchals, cities, states
    fetch('/api/mahila-samiti-members-dropdown-data', {
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
            data.data.sessions.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s;
                opt.textContent = s;
                sel.appendChild(opt);
            });
        } else {
            console.warn('Dropdown data endpoint did not return sessions as expected:', data);
        }
    })
    .catch(err => {
        console.warn('Could not load dropdown data for sessions.', err);
    });
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

// Load members for a specific session only
function loadMembersBySession(session) {
    const tbody = document.getElementById('membersTableBody');
    tbody.innerHTML = '<tr><td colspan="11" class="text-center">Loading...</td></tr>';

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
            displayMembersGrouped(res.data, session);
        } else {
            tbody.innerHTML = '<tr><td colspan="11" class="text-center">No members found for this session</td></tr>';
        }
    })
    .catch(err => {
        console.error('Error loading members by session:', err);
        tbody.innerHTML = '<tr><td colspan="11" class="text-center text-danger">Error loading members</td></tr>';
    });
}

// Renders members by requested order and grouping
function displayMembersGrouped(members, session) {
    const tbody = document.getElementById('membersTableBody');
    tbody.innerHTML = '';

    if (!members || members.length === 0) {
        tbody.innerHTML = '<tr><td colspan="11" class="text-center">No members found for the selected session</td></tr>';
        return;
    }

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
        rows.push(`<tr class="table-active"><td colspan="11"><strong>PST</strong></td></tr>`);

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
        rows.push(`<tr class="table-active"><td colspan="11"><strong>VP-SEC (Anchal wise)</strong></td></tr>`);
        const grouped = groupByAnchal(vpsec);
        const anchalKeys = Object.keys(grouped);
        const orderedAnchals = sortAnchals(anchalKeys);
        orderedAnchals.forEach(anchal => {
            rows.push(`<tr class="table-secondary"><td colspan="11"><em>Anchal: ${anchal}</em></td></tr>`);
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

    // 3) Sanyojika
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
        rows.push(`<tr class="table-active"><td colspan="11"><strong>Sanyojika</strong></td></tr>`);
        sanyojika.forEach(m => rows.push(renderRow(m, session)));
    }

    // 4) KSM members (anchal wise)
    const ksm = byType('ksm').concat(byType('ksm-member')).filter((v, i, a) => a.indexOf(v) === i);
    if (ksm.length) {
        rows.push(`<tr class="table-active"><td colspan="11"><strong>KSM Members (Anchal wise)</strong></td></tr>`);
        const groupedK = groupByAnchal(ksm);
        const anchalKeysK = Object.keys(groupedK);
        const orderedAnchalsK = sortAnchals(anchalKeysK);
        orderedAnchalsK.forEach(anchal => {
            rows.push(`<tr class="table-secondary"><td colspan="11"><em>Anchal: ${anchal}</em></td></tr>`);
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
    const photoUrl = getPhotoUrl(m);
    const photoHtml = photoUrl ? ('<a href="' + photoUrl + '" target="_blank" rel="noopener"><img src="' + photoUrl + '" class="photo-preview rounded" style="max-width:70px; max-height:70px; object-fit:cover;"></a>') : '<i class="fas fa-user fa-2x text-muted"></i>';
    const html = '\n        <tr>\n            <td>' + (idx || '') + '</td>\n            <td>' + (session || (m.session || '')) + '</td>\n            <td>' + photoHtml + '</td>\n            <td>' + (m.name || '') + '</td>\n            <td>' + ((m.type || '').toString().toUpperCase()) + '</td>\n            <td>' + (m.designation || '') + '</td>\n            <td>' + (m.mid || '') + '</td>\n            <td>' + (m.anchal_name || '') + '</td>\n            <td>' + (m.mobile_number || '') + '</td>\n            <td>' + (m.city || '') + '</td>\n            <td>' + (m.state || '') + '</td>\n            <td>\n                <button class="btn btn-sm btn-info me-1" onclick="viewMember(' + m.id + ')" title="View">\n                    <i class="fas fa-eye"></i>\n                </button>\n                <button class="btn btn-sm btn-primary me-1" onclick="editMember(' + m.id + ')" title="Edit">\n                    <i class="fas fa-edit"></i>\n                </button>\n                <button class="btn btn-sm btn-danger" onclick="deleteMember(' + m.id + ')" title="Delete">\n                    <i class="fas fa-trash"></i>\n                </button>\n            </td>\n        </tr>\n    ';
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