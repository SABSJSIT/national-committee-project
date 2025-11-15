@extends('includes.layouts.sahitya')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4">🔐 Change Password</h3>

    {{-- Server-side validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="passwordForm" action="{{ route('password.update') }}" method="POST" novalidate>
        @csrf

        {{-- Current Password --}}
        <div class="mb-3">
            <label for="current_password" class="form-label">Current Password</label>
            <div class="input-group">
                <input autocomplete="current-password" type="password" id="current_password" name="current_password" class="form-control" required>
                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="current_password" title="Show / Hide">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        {{-- New Password --}}
        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <div class="input-group">
                <input autocomplete="new-password" type="password" id="new_password" name="new_password" class="form-control"
                    required minlength="8"
                    pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$"
                    title="Password must have at least 8 characters, one uppercase, one lowercase, one number, and one special character">
                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="new_password" title="Show / Hide">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            <small class="form-text text-muted d-block mt-1">
                ✅ At least 8 characters • One uppercase • One lowercase • One number • One special character (@$!%*?&)
            </small>
        </div>

        {{-- Confirm Password --}}
        <div class="mb-3">
            <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
            <div class="input-group">
                <input autocomplete="new-password" type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" required>
                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="new_password_confirmation" title="Show / Hide">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <button type="button" id="confirmBtn" class="btn btn-primary">Update Password</button>
    </form>
</div>

{{-- Confirmation Modal --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Password Change</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>⚠️ क्या आप वाकई अपना पासवर्ड बदलना चाहते हैं?</p>
        <p class="mb-0"><strong>New Password:</strong> <span id="previewPassword"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="finalSubmitBtn" class="btn btn-danger">Yes, Change</button>
      </div>
    </div>
  </div>
</div>

{{-- Toast container --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 10800;">
    <div id="passwordToast" class="toast align-items-center text-white border-0" role="alert" aria-live="polite" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

{{-- Bootstrap Icons (for eye icon). If your layout already includes it, you can remove this line. --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<script>
/*
  Full JS: ensures Bootstrap is present, attaches toggle-eye, modal confirm, client validation,
  submits form, shows server flash messages via JSON-encoded session variables.
*/
(function () {
    // Dynamically load a script
    function loadScript(src, cb) {
        var s = document.createElement('script');
        s.src = src;
        s.async = true;
        s.onload = function() { cb(null); };
        s.onerror = function() { cb(new Error('Failed to load ' + src)); };
        document.head.appendChild(s);
    }

    // Ensure bootstrap.bundle is available (Modal & Toast)
    function ensureBootstrap(cb) {
        if (window.bootstrap && typeof window.bootstrap.Modal === 'function' && typeof window.bootstrap.Toast === 'function') {
            return cb();
        }
        loadScript('https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', function (err) {
            // proceed even if error; modal/toast calls will be guarded
            setTimeout(cb, 50);
        });
    }

    function initApp() {
        // Helper: show toast
        function showToast(message, type) {
            var toastEl = document.getElementById('passwordToast');
            if (!toastEl) return console.warn('Toast element missing');

            var toastBody = toastEl.querySelector('.toast-body') || toastEl;
            var allowed = ['success','danger','info','warning'];
            if (allowed.indexOf(type) === -1) type = 'info';

            // remove any previous bg classes and add requested one
            toastEl.classList.remove('text-bg-success','text-bg-danger','text-bg-info','text-bg-warning');
            toastEl.classList.add('text-bg-' + type);
            toastBody.textContent = message;

            try {
                if (window.bootstrap && typeof window.bootstrap.Toast === 'function') {
                    var t = new bootstrap.Toast(toastEl, { autohide: true, delay: 4000 });
                    t.show();
                } else {
                    // fallback: simple alert
                    console.log('Toast:', message);
                }
            } catch (e) {
                console.error('Toast show error', e);
            }
        }

        // Toggle password visibility
        function togglePasswordField(button) {
            var targetId = button.getAttribute('data-target');
            var input = document.getElementById(targetId);
            if (!input) return;
            var icon = button.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                if (icon) { icon.classList.remove('bi-eye'); icon.classList.add('bi-eye-slash'); }
            } else {
                input.type = 'password';
                if (icon) { icon.classList.remove('bi-eye-slash'); icon.classList.add('bi-eye'); }
            }
        }

        // Attach toggle handlers (works even if DOM already loaded)
        var toggleButtons = document.querySelectorAll('.toggle-password');
        toggleButtons.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                togglePasswordField(btn);
            });
        });

        // Modal & form logic
        var confirmBtn = document.getElementById('confirmBtn');
        var finalSubmitBtn = document.getElementById('finalSubmitBtn');
        var passwordForm = document.getElementById('passwordForm');

        function safeGet(elId) {
            var el = document.getElementById(elId);
            return el ? el.value.trim() : '';
        }

        if (confirmBtn) {
            confirmBtn.addEventListener('click', function () {
                var newPassword = safeGet('new_password');
                var confirmPassword = safeGet('new_password_confirmation');

                var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;

                if (!regex.test(newPassword)) {
                    showToast("❌ Password must have at least 8 characters, one uppercase, one lowercase, one number, and one special character.", 'danger');
                    return;
                }
                if (newPassword !== confirmPassword) {
                    showToast("❌ New password and confirmation do not match.", 'danger');
                    return;
                }

                // preview masked password
                var masked;
                if (newPassword.length <= 2) masked = '*'.repeat(newPassword.length);
                else masked = newPassword[0] + '*'.repeat(newPassword.length - 2) + newPassword.slice(-1);

                var previewEl = document.getElementById('previewPassword');
                if (previewEl) previewEl.textContent = masked;

                // show modal if bootstrap exists, otherwise submit directly
                if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
                    try {
                        var modalEl = document.getElementById('confirmModal');
                        var modal = new bootstrap.Modal(modalEl);
                        modal.show();
                    } catch (e) {
                        console.error('Modal show failed', e);
                        showToast('Cannot show confirmation. Submitting...', 'warning');
                        if (passwordForm) passwordForm.submit();
                    }
                } else {
                    // no modal -> submit
                    if (passwordForm) passwordForm.submit();
                }
            });
        }

        if (finalSubmitBtn) {
            finalSubmitBtn.addEventListener('click', function () {
                finalSubmitBtn.disabled = true;
                if (passwordForm) passwordForm.submit();
            });
        }

        // Server-side flash messages (blade-safe JSON)

        if (successMessage) {
            setTimeout(function () { showToast(successMessage, 'success'); }, 120);
        }
        if (errorMessage) {
            setTimeout(function () { showToast(errorMessage, 'danger'); }, 120);
        }
    } // initApp end

    // Ensure bootstrap then init
    ensureBootstrap(initApp);
})();
</script>
@endsection
