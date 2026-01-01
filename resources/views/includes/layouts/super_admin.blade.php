<!DOCTYPE html>
<html lang="hi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Admin Dashboard - SABSJS</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    :root {
      /* Layout Dimensions */
      --sidebar-w: 280px;
      --sidebar-collapsed: 80px;
      --header-h: 70px;
      --footer-h: 50px;

      /* Premium Color Palette */
      --primary: #6366f1;
      --primary-dark: #4f46e5;
      --primary-light: #818cf8;
      --secondary: #8b5cf6;
      --accent: #06b6d4;
      --success: #10b981;
      --warning: #f59e0b;
      --danger: #ef4444;

      /* Dark Theme Colors */
      --sidebar-bg-start: #0f0f23;
      --sidebar-bg-end: #1a1a2e;
      --sidebar-accent: rgba(99, 102, 241, 0.15);

      /* Light Theme Colors */
      --bg-main: #f1f5f9;
      --bg-card: #ffffff;
      --text-primary: #1e293b;
      --text-secondary: #64748b;
      --text-muted: #94a3b8;

      /* Shadows & Effects */
      --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
      --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
      --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.12);
      --shadow-xl: 0 20px 40px rgba(0, 0, 0, 0.15);
      --glow-primary: 0 0 20px rgba(99, 102, 241, 0.3);

      /* Border Radius */
      --radius-sm: 8px;
      --radius-md: 12px;
      --radius-lg: 16px;
      --radius-xl: 20px;

      /* Transitions */
      --transition-fast: 0.15s ease;
      --transition-normal: 0.3s ease;
      --transition-slow: 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html, body {
      height: 100%;
      font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: var(--bg-main);
      color: var(--text-primary);
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
      width: 6px;
      height: 6px;
    }

    ::-webkit-scrollbar-track {
      background: transparent;
    }

    ::-webkit-scrollbar-thumb {
      background: rgba(99, 102, 241, 0.3);
      border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: rgba(99, 102, 241, 0.5);
    }

    /* ============ SIDEBAR ============ */
    .sidebar {
      position: fixed;
      left: 0;
      top: 0;
      bottom: 0;
      width: var(--sidebar-w);
      background: linear-gradient(180deg, var(--sidebar-bg-start), var(--sidebar-bg-end));
      display: flex;
      flex-direction: column;
      z-index: 1200;
      transition: all var(--transition-slow);
      overflow: hidden;
    }

    .sidebar::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: 
        radial-gradient(ellipse at top left, rgba(99, 102, 241, 0.1), transparent 50%),
        radial-gradient(ellipse at bottom right, rgba(139, 92, 246, 0.08), transparent 50%);
      pointer-events: none;
    }

    .sidebar.closed {
      transform: translateX(-100%);
    }

    /* Brand Section */
    .brand-section {
      padding: 20px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      position: relative;
      z-index: 1;
    }

    .brand-wrapper {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .brand-logo {
      width: 50px;
      height: 50px;
      border-radius: var(--radius-md);
      object-fit: cover;
      border: 2px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
      transition: var(--transition-normal);
    }

    .brand-logo:hover {
      transform: scale(1.05);
      border-color: var(--primary);
    }

    .brand-info .brand-title {
      font-size: 16px;
      font-weight: 700;
      color: #fff;
      letter-spacing: 0.5px;
    }

    .brand-info .brand-subtitle {
      font-size: 12px;
      color: rgba(255, 255, 255, 0.6);
      font-weight: 400;
    }

    /* Sidebar Body */
    .sidebar-body {
      flex: 1;
      overflow-y: auto;
      padding: 15px;
      position: relative;
      z-index: 1;
    }

    /* Menu Styles */
    .menu-label {
      font-size: 11px;
      font-weight: 700;
      color: rgba(255, 255, 255, 0.4);
      text-transform: uppercase;
      letter-spacing: 1.2px;
      padding: 15px 12px 8px;
      margin-top: 10px;
    }

    .menu-label:first-child {
      margin-top: 0;
      padding-top: 5px;
    }

    .menu-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 12px 14px;
      margin-bottom: 4px;
      border-radius: var(--radius-md);
      color: rgba(255, 255, 255, 0.75);
      font-size: 14px;
      font-weight: 500;
      text-decoration: none;
      background: transparent;
      border: none;
      width: 100%;
      text-align: left;
      cursor: pointer;
      transition: all var(--transition-normal);
      position: relative;
      overflow: hidden;
    }

    .menu-item::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      width: 3px;
      height: 100%;
      background: linear-gradient(180deg, var(--primary), var(--secondary));
      border-radius: 0 4px 4px 0;
      opacity: 0;
      transition: var(--transition-normal);
    }

    .menu-item:hover {
      background: rgba(255, 255, 255, 0.08);
      color: #fff;
      transform: translateX(4px);
    }

    .menu-item:hover::before {
      opacity: 1;
    }

    .menu-item.active {
      background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.15));
      color: #fff;
      font-weight: 600;
    }

    .menu-item.active::before {
      opacity: 1;
    }

    .menu-item[aria-expanded="true"] {
      background: rgba(255, 255, 255, 0.08);
      color: #fff;
    }

    .menu-item .menu-icon {
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.15));
      border-radius: var(--radius-sm);
      font-size: 16px;
      color: var(--primary-light);
      transition: var(--transition-normal);
    }

    .menu-item:hover .menu-icon {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: #fff;
      box-shadow: var(--glow-primary);
    }

    .menu-item .menu-text {
      flex: 1;
    }

    .menu-item .menu-arrow {
      font-size: 12px;
      transition: transform var(--transition-normal);
    }

    .menu-item[aria-expanded="true"] .menu-arrow {
      transform: rotate(180deg);
    }

    /* Submenu */
    .submenu {
      padding: 8px 0 8px 52px;
    }

    .submenu-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 14px;
      margin-bottom: 2px;
      border-radius: var(--radius-sm);
      color: rgba(255, 255, 255, 0.6);
      font-size: 13px;
      text-decoration: none;
      transition: all var(--transition-normal);
    }

    .submenu-item:hover {
      color: #fff;
      background: rgba(255, 255, 255, 0.05);
      padding-left: 18px;
    }

    .submenu-item i {
      font-size: 14px;
    }

    /* Sidebar Footer */
    .sidebar-footer {
      padding: 15px 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      position: relative;
      z-index: 1;
    }

    .logout-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
      padding: 12px 20px;
      border: none;
      border-radius: var(--radius-md);
      background: linear-gradient(135deg, var(--danger), #dc2626);
      color: #fff;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all var(--transition-normal);
      box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    }

    .logout-btn:hover {
      background: linear-gradient(135deg, #dc2626, #b91c1c);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
    }

    .logout-btn i {
      font-size: 18px;
    }

    /* ============ MAIN CONTENT ============ */
    .main {
      margin-left: var(--sidebar-w);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      transition: margin-left var(--transition-slow);
    }

    .main.full {
      margin-left: 0;
    }

    /* ============ TOPBAR ============ */
    .topbar {
      height: var(--header-h);
      display: flex;
      align-items: center;
      gap: 20px;
      padding: 0 25px;
      background: var(--bg-card);
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      box-shadow: var(--shadow-sm);
      position: sticky;
      top: 0;
      z-index: 1100;
    }

    .topbar::before {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
    }

    .toggle-btn {
      width: 44px;
      height: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: none;
      border-radius: var(--radius-md);
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: #fff;
      font-size: 20px;
      cursor: pointer;
      transition: all var(--transition-normal);
      box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
    }

    .toggle-btn:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 18px rgba(99, 102, 241, 0.35);
    }

    .page-heading {
      font-size: 22px;
      font-weight: 700;
      color: var(--text-primary);
      margin: 0;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    /* Topbar Right Section */
    .topbar-right {
      margin-left: auto;
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 8px 16px;
      background: linear-gradient(135deg, rgba(99, 102, 241, 0.08), rgba(139, 92, 246, 0.05));
      border-radius: 50px;
      border: 1px solid rgba(99, 102, 241, 0.15);
    }

    .user-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-weight: 600;
      font-size: 14px;
    }

    .user-details {
      display: none;
    }

    @media (min-width: 768px) {
      .user-details {
        display: block;
      }
      .user-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
      }
      .user-role {
        font-size: 12px;
        color: var(--text-secondary);
      }
    }

    .topbar-logout {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 10px 18px;
      border: 2px solid var(--danger);
      border-radius: var(--radius-md);
      background: transparent;
      color: var(--danger);
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all var(--transition-normal);
    }

    .topbar-logout:hover {
      background: var(--danger);
      color: #fff;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
    }

    /* ============ CONTENT AREA ============ */
    .content-wrap {
      flex: 1;
      padding: 25px;
      background: var(--bg-main);
    }

    .card-surface {
      background: var(--bg-card);
      border-radius: var(--radius-lg);
      padding: 25px;
      box-shadow: var(--shadow-md);
      border: 1px solid rgba(0, 0, 0, 0.03);
      animation: fadeInUp 0.5s ease-out;
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

    /* ============ FOOTER ============ */
    .site-footer {
      height: var(--footer-h);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      background: var(--bg-card);
      border-top: 1px solid rgba(0, 0, 0, 0.05);
      font-size: 13px;
      color: var(--text-secondary);
      position: relative;
    }

    .site-footer::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
    }

    .footer-brand {
      font-weight: 600;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    /* ============ MOBILE OVERLAY ============ */
    .sidebar-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(4px);
      z-index: 1150;
      opacity: 0;
      visibility: hidden;
      transition: var(--transition-normal);
    }

    .sidebar-overlay.active {
      opacity: 1;
      visibility: visible;
    }

    /* ============ RESPONSIVE ============ */
    @media (max-width: 991px) {
      .sidebar {
        width: 85%;
        max-width: 300px;
        transform: translateX(-100%);
      }
      .sidebar.open {
        transform: translateX(0);
      }
      .main {
        margin-left: 0;
      }
      .topbar-logout span {
        display: none;
      }
      .user-info {
        padding: 8px 12px;
      }
    }

    @media (max-width: 576px) {
      .topbar {
        padding: 0 15px;
      }
      .page-heading {
        font-size: 16px;
      }
      .content-wrap {
        padding: 15px;
      }
      .card-surface {
        padding: 15px;
        border-radius: var(--radius-md);
      }
    }

    /* ============ UTILITIES ============ */
    .badge-role {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 4px 12px;
      border-radius: 50px;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .badge-role.super_admin {
      background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(139, 92, 246, 0.1));
      color: var(--primary);
    }

    .badge-role.admin {
      background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(6, 182, 212, 0.1));
      color: var(--success);
    }
  </style>
  @stack('styles')
</head>
<body>
  <!-- Sidebar Overlay for Mobile -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- SIDEBAR -->
  <aside id="sidebar" class="sidebar">
    <!-- Brand Section -->
    <div class="brand-section">
      <div class="brand-wrapper">
        <img src="{{ asset('images/logo.png') }}" alt="SABSJS Logo" class="brand-logo">
        <div class="brand-info">
          <div class="brand-title">SABSJS</div>
          <div class="brand-subtitle">Admin Dashboard</div>
        </div>
      </div>
    </div>

    @php
      $role = auth()->user()->role ?? null;
      $userName = auth()->user()->name ?? 'Admin';
      $can = fn($section) => $role === 'super_admin' || $role === $section;
    @endphp

    <!-- Sidebar Body -->
    <div class="sidebar-body">
      <nav class="menu">
        <!-- Main Menu -->
        <div class="menu-label">Main Menu</div>
        
        <a href="{{ route('dashboard.super_admin') }}" class="menu-item {{ request()->routeIs('dashboard.super_admin') ? 'active' : '' }}">
          <span class="menu-icon"><i class="bi bi-grid-1x2-fill"></i></span>
          <span class="menu-text">Dashboard</span>
        </a>

        @if($role === 'super_admin')
        <a href="{{ route('session.management') }}" class="menu-item {{ request()->routeIs('session.management') ? 'active' : '' }}">
          <span class="menu-icon"><i class="bi bi-calendar3"></i></span>
          <span class="menu-text">Session Management</span>
        </a>

        <a href="{{ route('designation-type.management') }}" class="menu-item {{ request()->routeIs('designation-type.management') ? 'active' : '' }}">
          <span class="menu-icon"><i class="bi bi-person-badge-fill"></i></span>
          <span class="menu-text">Designation Type</span>
        </a>

        <a href="{{ route('designation.management') }}" class="menu-item {{ request()->routeIs('designation.management') ? 'active' : '' }}">
          <span class="menu-icon"><i class="bi bi-award-fill"></i></span>
          <span class="menu-text">Designation</span>
        </a>
        @endif

        @if($can('shramnopasak'))
        <!-- Shramnopasak Section -->
        <div class="menu-label">Shramnopasak</div>
        
        <button class="menu-item" type="button" data-bs-toggle="collapse" data-bs-target="#menuShramnopasak" aria-expanded="false">
          <span class="menu-icon"><i class="bi bi-people-fill"></i></span>
          <span class="menu-text">Total Registered</span>
          <i class="bi bi-chevron-down menu-arrow"></i>
        </button>
        <div id="menuShramnopasak" class="submenu collapse">
          <a href="#" class="submenu-item"><i class="bi bi-dot"></i> Shramnopasak 1</a>
          <a href="#" class="submenu-item"><i class="bi bi-dot"></i> Shramnopasak 2</a>
        </div>
        @endif

        @if($can('sahitya'))
        <!-- Sahitya Section -->
        <div class="menu-label">Sahitya</div>
        
        <button class="menu-item" type="button" data-bs-toggle="collapse" data-bs-target="#menuSahitya" aria-expanded="false">
          <span class="menu-icon"><i class="bi bi-journal-richtext"></i></span>
          <span class="menu-text">Manage Sahitya</span>
          <i class="bi bi-chevron-down menu-arrow"></i>
        </button>
        <div id="menuSahitya" class="submenu collapse">
          <a href="#" class="submenu-item"><i class="bi bi-plus-circle"></i> Add Sahitya</a>
          <a href="#" class="submenu-item"><i class="bi bi-eye"></i> View Sahitya</a>
        </div>
        @endif

        @if($can('dispatch'))
        <!-- Dispatch Section -->
        <div class="menu-label">Dispatch</div>
        
        <button class="menu-item" type="button" data-bs-toggle="collapse" data-bs-target="#menuDispatch" aria-expanded="false">
          <span class="menu-icon"><i class="bi bi-folder2-open"></i></span>
          <span class="menu-text">Dispatch Reports</span>
          <i class="bi bi-chevron-down menu-arrow"></i>
        </button>
        <div id="menuDispatch" class="submenu collapse">
          <a href="#" class="submenu-item"><i class="bi bi-building"></i> All Dispatch Details</a>
          <a href="#" class="submenu-item"><i class="bi bi-file-earmark-excel text-success"></i> Excel Report</a>
          <a href="#" class="submenu-item"><i class="bi bi-file-earmark-pdf text-danger"></i> PDF Report</a>
        </div>
        @endif

        @if($can('mahila_samiti'))
        <!-- Mahila Samiti Section -->
        <div class="menu-label">Mahila Samiti</div>
        
        <a href="{{ route('mahila-samiti-members') }}" class="menu-item {{ request()->routeIs('mahila-samiti-members') ? 'active' : '' }}">
          <span class="menu-icon"><i class="bi bi-list-ul"></i></span>
          <span class="menu-text">View All Members</span>
        </a>

        <a href="{{ route('mahila-samiti-members.add') }}" class="menu-item {{ request()->routeIs('mahila-samiti-members.add') ? 'active' : '' }}">
          <span class="menu-icon"><i class="bi bi-person-plus-fill"></i></span>
          <span class="menu-text">Add New Member</span>
        </a>
        @endif

        @if($can('other'))
        <!-- Others Section -->
        <div class="menu-label">Others</div>
        
        <a href="#" class="menu-item">
          <span class="menu-icon"><i class="bi bi-three-dots"></i></span>
          <span class="menu-text">Other Options</span>
        </a>
        @endif

      </nav>
    </div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
          <i class="bi bi-box-arrow-right"></i>
          <span>Logout</span>
        </button>
      </form>
    </div>
  </aside>

  <!-- MAIN CONTENT -->
  <div id="main" class="main">
    <!-- Top Bar -->
    <header class="topbar">
      <button id="toggleBtn" class="toggle-btn" aria-label="Toggle Sidebar">
        <i id="toggleIcon" class="bi bi-list"></i>
      </button>
      
      <h1 class="page-heading">@yield('page_title', 'Admin Dashboard')</h1>

      <div class="topbar-right">
        <!-- User Info -->
        <div class="user-info">
          <div class="user-avatar">
            {{ strtoupper(substr($userName, 0, 1)) }}
          </div>
          <div class="user-details">
            <div class="user-name">{{ $userName }}</div>
            <div class="user-role">{{ ucfirst(str_replace('_', ' ', $role ?? 'Admin')) }}</div>
          </div>
        </div>

        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}" class="d-none d-md-block">
          @csrf
          <button type="submit" class="topbar-logout">
            <i class="bi bi-power"></i>
            <span>Logout</span>
          </button>
        </form>
      </div>
    </header>

    <!-- Content Area -->
    <main class="content-wrap">
      <div class="card-surface">
        @yield('content')
      </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
      <span>&copy; {{ date('Y') }}</span>
      <span class="footer-brand">श्री अखिल भारतवर्षीय साधुमार्गी जैन संघ</span>
      <span>· IT Department</span>
    </footer>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const sidebar = document.getElementById('sidebar');
      const main = document.getElementById('main');
      const toggleBtn = document.getElementById('toggleBtn');
      const toggleIcon = document.getElementById('toggleIcon');
      const overlay = document.getElementById('sidebarOverlay');
      const isMobile = () => window.innerWidth < 992;

      // Toggle Sidebar
      toggleBtn?.addEventListener('click', () => {
        if (isMobile()) {
          sidebar.classList.toggle('open');
          overlay.classList.toggle('active');
        } else {
          sidebar.classList.toggle('closed');
          main.classList.toggle('full');
        }
        toggleIcon?.classList.toggle('bi-x-lg');
        toggleIcon?.classList.toggle('bi-list');
      });

      // Close sidebar on overlay click (mobile)
      overlay?.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        toggleIcon?.classList.remove('bi-x-lg');
        toggleIcon?.classList.add('bi-list');
      });

      // Handle window resize
      window.addEventListener('resize', () => {
        if (!isMobile()) {
          sidebar.classList.remove('open');
          overlay.classList.remove('active');
        }
      });

      // Add smooth transition for submenu
      document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(btn => {
        btn.addEventListener('click', function() {
          const icon = this.querySelector('.menu-arrow');
          if (icon) {
            icon.style.transition = 'transform 0.3s ease';
          }
        });
      });
    });
  </script>

  @stack('scripts')
</body>
</html>
