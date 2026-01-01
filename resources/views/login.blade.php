<!DOCTYPE html>
<html lang="hi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
  <meta name="description" content="SABSJS SSO Login - Secure Single Sign-On Portal" />
  <title>SSO Login - SABSJS</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

  <style>
    :root {
      /* Premium Color Palette */
      --primary: #6366f1;
      --primary-dark: #4f46e5;
      --primary-light: #818cf8;
      --secondary: #8b5cf6;
      --accent: #06b6d4;
      --success: #10b981;
      --warning: #f59e0b;
      --danger: #ef4444;
      
      /* Background Colors */
      --bg-dark: #0f0f23;
      --bg-darker: #0a0a1a;
      --bg-card: rgba(255, 255, 255, 0.95);
      
      /* Text Colors */
      --text-primary: #1e293b;
      --text-secondary: #64748b;
      --text-light: #f8fafc;
      --text-muted: #94a3b8;
      
      /* Glassmorphism */
      --glass-bg: rgba(255, 255, 255, 0.08);
      --glass-border: rgba(255, 255, 255, 0.12);
      --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      
      /* Shadows */
      --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
      --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.12);
      --shadow-lg: 0 12px 40px rgba(0, 0, 0, 0.2);
      --shadow-xl: 0 25px 60px rgba(0, 0, 0, 0.3);
      --shadow-glow: 0 0 40px rgba(99, 102, 241, 0.3);
      
      /* Border Radius */
      --radius-sm: 8px;
      --radius-md: 12px;
      --radius-lg: 16px;
      --radius-xl: 24px;
      --radius-2xl: 32px;
      
      /* Transitions */
      --transition-fast: 0.15s ease;
      --transition-normal: 0.3s ease;
      --transition-slow: 0.5s ease;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html {
      font-size: 16px;
      scroll-behavior: smooth;
      scrollbar-width: none; /* Firefox */
      -ms-overflow-style: none; /* IE and Edge */
    }

    html::-webkit-scrollbar {
      display: none; /* Chrome, Safari, Opera */
    }

    body {
      font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 15px;
      background: var(--bg-darker);
      position: relative;
    }

    /* Animated Gradient Background */
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: 
        radial-gradient(ellipse 80% 50% at 20% 40%, rgba(99, 102, 241, 0.25), transparent 60%),
        radial-gradient(ellipse 60% 40% at 80% 30%, rgba(139, 92, 246, 0.2), transparent 50%),
        radial-gradient(ellipse 50% 60% at 60% 80%, rgba(6, 182, 212, 0.15), transparent 50%),
        linear-gradient(135deg, var(--bg-darker) 0%, #1a1a2e 50%, var(--bg-dark) 100%);
      z-index: -2;
      animation: gradientShift 15s ease-in-out infinite alternate;
    }

    @keyframes gradientShift {
      0% { opacity: 1; }
      100% { opacity: 0.8; filter: hue-rotate(15deg); }
    }

    /* Floating Particles */
    .particles {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: -1;
      overflow: hidden;
    }

    .particle {
      position: absolute;
      border-radius: 50%;
      animation: float 20s infinite ease-in-out;
      opacity: 0.3;
    }

    .particle:nth-child(1) {
      width: 300px;
      height: 300px;
      background: radial-gradient(circle, rgba(99, 102, 241, 0.2), transparent 70%);
      top: 10%;
      left: 10%;
      animation-delay: 0s;
    }

    .particle:nth-child(2) {
      width: 200px;
      height: 200px;
      background: radial-gradient(circle, rgba(139, 92, 246, 0.2), transparent 70%);
      top: 60%;
      right: 10%;
      animation-delay: -5s;
    }

    .particle:nth-child(3) {
      width: 150px;
      height: 150px;
      background: radial-gradient(circle, rgba(6, 182, 212, 0.2), transparent 70%);
      bottom: 20%;
      left: 25%;
      animation-delay: -10s;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0) translateX(0) scale(1); }
      25% { transform: translateY(-30px) translateX(20px) scale(1.05); }
      50% { transform: translateY(-10px) translateX(-20px) scale(0.95); }
      75% { transform: translateY(-40px) translateX(10px) scale(1.02); }
    }

    /* Main Container */
    .login-container {
      width: 100%;
      max-width: 1000px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      border-radius: var(--radius-2xl);
      overflow: hidden;
      box-shadow: 
        var(--shadow-xl),
        0 0 0 1px var(--glass-border),
        var(--shadow-glow);
      backdrop-filter: blur(20px);
      animation: slideUp 0.8s ease-out;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(40px) scale(0.95);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    /* Left Panel - Branding */
    .branding-panel {
      background: 
        linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(139, 92, 246, 0.1)),
        var(--glass-bg);
      padding: 20px 25px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .branding-panel::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle at 30% 30%, rgba(99, 102, 241, 0.1), transparent 50%);
      animation: rotateBg 30s linear infinite;
    }

    @keyframes rotateBg {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }

    .branding-content {
      position: relative;
      z-index: 1;
    }

    .org-title {
      font-size: 1.2rem;
      font-weight: 700;
      color: var(--text-light);
      margin-bottom: 12px;
      line-height: 1.3;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
      animation: fadeInDown 0.8s ease-out 0.2s both;
    }

    @keyframes fadeInDown {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .logo-wrapper {
      position: relative;
      margin-bottom: 12px;
      animation: fadeIn 0.8s ease-out 0.4s both;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .logo-glow {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 120px;
      height: 120px;
      background: radial-gradient(circle, rgba(99, 102, 241, 0.4), transparent 70%);
      filter: blur(25px);
      animation: pulse 3s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 0.5; transform: translate(-50%, -50%) scale(1); }
      50% { opacity: 0.8; transform: translate(-50%, -50%) scale(1.1); }
    }

    .logo {
      width: 90px;
      height: 90px;
      border-radius: var(--radius-lg);
      object-fit: contain;
      padding: 10px;
      background: rgba(255, 255, 255, 0.9);
      box-shadow: 
        0 15px 30px rgba(0, 0, 0, 0.2),
        0 0 0 1px rgba(255, 255, 255, 0.2),
        inset 0 2px 0 rgba(255, 255, 255, 0.5);
      position: relative;
      z-index: 1;
      transition: var(--transition-normal);
    }

    .logo:hover {
      transform: translateY(-5px) scale(1.02);
      box-shadow: 
        0 30px 50px rgba(0, 0, 0, 0.3),
        0 0 0 1px rgba(255, 255, 255, 0.3),
        0 0 30px rgba(99, 102, 241, 0.3);
    }

    .sso-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      padding: 8px 16px;
      border-radius: 50px;
      margin-bottom: 8px;
      animation: fadeInUp 0.8s ease-out 0.6s both;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .sso-badge i {
      font-size: 20px;
      color: var(--text-light);
    }

    .sso-badge span {
      color: var(--text-light);
      font-weight: 600;
      font-size: 14px;
      letter-spacing: 0.5px;
    }

    .tagline {
      color: var(--text-muted);
      font-size: 14px;
      max-width: 280px;
      line-height: 1.6;
      animation: fadeIn 0.8s ease-out 0.8s both;
    }

    .feature-list {
      margin-top: 15px;
      display: flex;
      flex-direction: column;
      gap: 6px;
      animation: fadeIn 0.8s ease-out 1s both;
    }

    .feature-item {
      display: flex;
      align-items: center;
      gap: 12px;
      color: rgba(255, 255, 255, 0.8);
      font-size: 13px;
    }

    .feature-item i {
      width: 28px;
      height: 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      border-radius: 8px;
      font-size: 12px;
    }

    /* Right Panel - Form */
    .form-panel {
      background: var(--bg-card);
      padding: 25px 35px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .form-header {
      margin-bottom: 18px;
      animation: fadeInRight 0.6s ease-out;
    }

    @keyframes fadeInRight {
      from {
        opacity: 0;
        transform: translateX(20px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .form-title {
      font-size: 2rem;
      font-weight: 700;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 8px;
    }

    .form-subtitle {
      color: var(--text-secondary);
      font-size: 15px;
      font-weight: 400;
    }

    /* Form Styles */
    .login-form {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .alert-error {
      background: linear-gradient(135deg, #fef2f2, #fee2e2);
      border: 1px solid #fecaca;
      border-left: 4px solid var(--danger);
      padding: 14px 18px;
      border-radius: var(--radius-md);
      display: flex;
      align-items: center;
      gap: 12px;
      animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      20%, 60% { transform: translateX(-5px); }
      40%, 80% { transform: translateX(5px); }
    }

    .alert-error i {
      color: var(--danger);
      font-size: 18px;
    }

    .alert-error span {
      color: #b91c1c;
      font-size: 14px;
      font-weight: 500;
    }

    .form-group {
      position: relative;
    }

    .form-group label {
      display: block;
      font-size: 12px;
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 6px;
      transition: var(--transition-fast);
    }

    .form-group label i {
      margin-right: 6px;
      color: var(--primary);
    }

    .input-wrapper {
      position: relative;
    }

    .form-input {
      width: 100%;
      padding: 12px 14px;
      padding-right: 45px;
      font-size: 14px;
      font-family: inherit;
      border: 2px solid #e2e8f0;
      border-radius: var(--radius-md);
      background: #f8fafc;
      color: var(--text-primary);
      outline: none;
      transition: var(--transition-normal);
    }

    .form-input::placeholder {
      color: var(--text-muted);
    }

    .form-input:hover {
      border-color: #cbd5e1;
      background: #fff;
    }

    .form-input:focus {
      border-color: var(--primary);
      background: #fff;
      box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .input-icon {
      position: absolute;
      right: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      font-size: 18px;
      transition: var(--transition-fast);
    }

    .toggle-password {
      position: absolute;
      right: 16px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: var(--text-muted);
      font-size: 18px;
      cursor: pointer;
      padding: 5px;
      transition: var(--transition-fast);
    }

    .toggle-password:hover {
      color: var(--primary);
    }

    .form-actions {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 5px;
    }

    .checkbox-wrapper {
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
    }

    .checkbox-wrapper input[type="checkbox"] {
      display: none;
    }

    .custom-checkbox {
      width: 20px;
      height: 20px;
      border: 2px solid #cbd5e1;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: var(--transition-fast);
    }

    .custom-checkbox i {
      font-size: 12px;
      color: white;
      opacity: 0;
      transform: scale(0);
      transition: var(--transition-fast);
    }

    .checkbox-wrapper input:checked + .custom-checkbox {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      border-color: var(--primary);
    }

    .checkbox-wrapper input:checked + .custom-checkbox i {
      opacity: 1;
      transform: scale(1);
    }

    .checkbox-wrapper span {
      font-size: 14px;
      color: var(--text-secondary);
    }

    .forgot-link {
      font-size: 14px;
      font-weight: 600;
      color: var(--primary);
      text-decoration: none;
      transition: var(--transition-fast);
    }

    .forgot-link:hover {
      color: var(--primary-dark);
      text-decoration: underline;
    }

    .btn-submit {
      width: 100%;
      padding: 12px 20px;
      font-size: 15px;
      font-weight: 600;
      font-family: inherit;
      color: white;
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      border: none;
      border-radius: var(--radius-md);
      cursor: pointer;
      position: relative;
      overflow: hidden;
      transition: var(--transition-normal);
      box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    }

    .btn-submit::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: var(--transition-slow);
    }

    .btn-submit:hover::before {
      left: 100%;
    }

    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
    }

    .btn-submit:active {
      transform: translateY(0);
    }

    .btn-submit.loading {
      pointer-events: none;
      opacity: 0.8;
    }

    .btn-submit .btn-text {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .btn-submit .spinner {
      display: none;
      width: 20px;
      height: 20px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-top-color: white;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    .btn-submit.loading .spinner {
      display: block;
    }

    .btn-submit.loading .btn-icon {
      display: none;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .terms-text {
      text-align: center;
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 10px;
    }

    .terms-text a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
    }

    .terms-text a:hover {
      text-decoration: underline;
    }

    /* Divider */
    .divider {
      display: flex;
      align-items: center;
      gap: 15px;
      margin: 25px 0;
    }

    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
    }

    .divider span {
      font-size: 12px;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    /* IT Department Footer */
    .it-footer {
      margin-top: 15px;
      padding-top: 12px;
      border-top: 1px solid #e2e8f0;
      text-align: center;
    }

    .it-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 16px;
      background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
      border-radius: 20px;
      font-size: 12px;
      color: #0369a1;
    }

    .it-badge i {
      font-size: 14px;
    }

    /* Responsive Styles */
    @media (max-width: 900px) {
      .login-container {
        grid-template-columns: 1fr;
        max-width: 480px;
        min-height: auto;
      }

      .branding-panel {
        padding: 35px 30px;
        order: -1;
      }

      .org-title {
        font-size: 1.25rem;
        margin-bottom: 20px;
      }

      .logo {
        width: 120px;
        height: 120px;
      }

      .logo-glow {
        width: 150px;
        height: 150px;
      }

      .feature-list {
        display: none;
      }

      .form-panel {
        padding: 35px 30px;
      }

      .form-title {
        font-size: 1.5rem;
      }
    }

    @media (max-width: 480px) {
      body {
        padding: 15px;
      }

      .login-container {
        border-radius: var(--radius-xl);
      }

      .branding-panel {
        padding: 30px 20px;
      }

      .org-title {
        font-size: 1.1rem;
      }

      .logo {
        width: 100px;
        height: 100px;
        padding: 10px;
      }

      .sso-badge {
        padding: 10px 18px;
      }

      .sso-badge span {
        font-size: 12px;
      }

      .tagline {
        font-size: 13px;
      }

      .form-panel {
        padding: 30px 20px;
      }

      .form-title {
        font-size: 1.35rem;
      }

      .form-subtitle {
        font-size: 14px;
      }

      .form-input {
        padding: 14px 16px;
        padding-right: 45px;
        font-size: 14px;
      }

      .btn-submit {
        padding: 14px 20px;
        font-size: 15px;
      }

      .form-actions {
        flex-direction: column;
        gap: 12px;
        align-items: flex-start;
      }

      .forgot-link {
        align-self: flex-end;
        margin-top: -5px;
      }
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
      .form-panel {
        background: linear-gradient(135deg, #1e293b, #0f172a);
      }

      .form-title {
        -webkit-text-fill-color: unset;
        color: var(--text-light);
      }

      .form-subtitle {
        color: var(--text-muted);
      }

      .form-group label {
        color: var(--text-light);
      }

      .form-input {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.1);
        color: var(--text-light);
      }

      .form-input:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.15);
      }

      .form-input:focus {
        background: rgba(255, 255, 255, 0.1);
        border-color: var(--primary);
      }

      .checkbox-wrapper span {
        color: var(--text-muted);
      }

      .it-footer {
        border-top-color: rgba(255, 255, 255, 0.1);
      }

      .it-badge {
        background: rgba(99, 102, 241, 0.1);
        color: var(--primary-light);
      }

      .terms-text {
        color: var(--text-muted);
      }

      .divider::before,
      .divider::after {
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
      }
    }

    /* Focus visible for accessibility */
    :focus-visible {
      outline: 2px solid var(--primary);
      outline-offset: 2px;
    }

    /* Print styles */
    @media print {
      body { background: white; }
      .particles { display: none; }
      .login-container { box-shadow: none; border: 1px solid #ccc; }
    }
  </style>
</head>
<body>
  <!-- Floating Particles -->
  <div class="particles" aria-hidden="true">
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
  </div>

  <div class="login-container" role="main">
    <!-- Left Panel - Branding -->
    <div class="branding-panel">
      <div class="branding-content">
        <h1 class="org-title">श्री अखिल भारतवर्षीय<br>साधुमार्गी जैन संघ</h1>
        
        <div class="logo-wrapper">
          <div class="logo-glow"></div>
          <img class="logo" src="{{ asset('images/logo.png') }}" alt="SABSJS Logo">
        </div>

        <div class="sso-badge">
          <i class="bi bi-shield-lock-fill"></i>
          <span>Secure Single Sign-On</span>
        </div>

        <p class="tagline">Access all SABSJS services with a single secure login</p>

        <div class="feature-list">
          <div class="feature-item">
            <i class="bi bi-shield-check"></i>
            <span>Enterprise-grade Security</span>
          </div>
          <div class="feature-item">
            <i class="bi bi-lightning-charge"></i>
            <span>Fast & Reliable Access</span>
          </div>
          <div class="feature-item">
            <i class="bi bi-headset"></i>
            <span>24/7 IT Support</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Panel - Login Form -->
    <div class="form-panel" aria-labelledby="login-title">
      <div class="form-header">
        <h2 class="form-title" id="login-title">Welcome Back!</h2>
        <p class="form-subtitle">Please sign in to continue</p>
      </div>

      <form class="login-form" method="POST" action="/login" novalidate id="loginForm">
        @csrf

        @if(session('error'))
          <div class="alert-error" role="alert">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>{{ session('error') }}</span>
          </div>
        @endif

        <div class="form-group">
          <label for="email">
            <i class="bi bi-envelope"></i>
            Email Address
          </label>
          <div class="input-wrapper">
            <input 
              type="email" 
              id="email" 
              name="email" 
              class="form-input" 
              placeholder="Enter your email"
              autocomplete="email"
              required
              value="{{ old('email') }}"
            >
            <i class="bi bi-at input-icon"></i>
          </div>
        </div>

        <div class="form-group">
          <label for="password">
            <i class="bi bi-lock"></i>
            Password
          </label>
          <div class="input-wrapper">
            <input 
              type="password" 
              id="password" 
              name="password" 
              class="form-input" 
              placeholder="Enter your password"
              autocomplete="current-password"
              required
            >
            <button type="button" class="toggle-password" id="togglePass" aria-label="Toggle password visibility">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>

        <div class="form-actions">
          <label class="checkbox-wrapper">
            <input type="checkbox" name="remember">
            <span class="custom-checkbox">
              <i class="bi bi-check"></i>
            </span>
            <span>Remember me</span>
          </label>
          <a href="/password/reset" class="forgot-link">Forgot Password?</a>
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">
          <span class="btn-text">
            <span class="spinner"></span>
            <i class="bi bi-box-arrow-in-right btn-icon"></i>
            Sign In
          </span>
        </button>

        <p class="terms-text">
          By signing in, you agree to our 
          <a href="/terms">Terms of Service</a> & 
          <a href="/privacy">Privacy Policy</a>
        </p>
      </form>

      <div class="it-footer">
        <div class="it-badge">
          <i class="bi bi-gear-wide-connected"></i>
          <span>SABSJS IT Department</span>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const passwordInput = document.getElementById('password');
      const toggleBtn = document.getElementById('togglePass');
      const emailInput = document.getElementById('email');
      const form = document.getElementById('loginForm');
      const submitBtn = document.getElementById('submitBtn');

      // Toggle password visibility
      toggleBtn.addEventListener('click', function() {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        toggleBtn.querySelector('i').classList.toggle('bi-eye');
        toggleBtn.querySelector('i').classList.toggle('bi-eye-slash');
        toggleBtn.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
      });

      // Auto-focus email on load
      if (emailInput && !emailInput.value) {
        emailInput.focus();
      }

      // Form submission with loading state
      form.addEventListener('submit', function(e) {
        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();

        // Remove existing inline errors
        const existingError = document.querySelector('.inline-error');
        if (existingError) existingError.remove();

        if (!email || !password) {
          e.preventDefault();
          const errorDiv = document.createElement('div');
          errorDiv.className = 'alert-error inline-error';
          errorDiv.innerHTML = `
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>Please enter both email and password</span>
          `;
          form.insertBefore(errorDiv, form.firstChild);
          
          if (!email) emailInput.focus();
          else if (!password) passwordInput.focus();
          
          return;
        }

        // Show loading state
        submitBtn.classList.add('loading');
      });

      // Add input animations
      const inputs = document.querySelectorAll('.form-input');
      inputs.forEach(input => {
        input.addEventListener('focus', function() {
          this.parentElement.classList.add('focused');
        });
        input.addEventListener('blur', function() {
          this.parentElement.classList.remove('focused');
        });
      });

      // Keyboard accessibility for checkbox
      const checkboxWrapper = document.querySelector('.checkbox-wrapper');
      checkboxWrapper.addEventListener('keydown', function(e) {
        if (e.key === ' ' || e.key === 'Enter') {
          e.preventDefault();
          this.querySelector('input').click();
        }
      });
    });
  </script>
</body>
</html>
