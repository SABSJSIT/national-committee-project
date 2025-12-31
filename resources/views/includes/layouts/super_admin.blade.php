<!DOCTYPE html>
<html lang="hi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    :root{
      --sidebar-w: 260px;
      --header-h: 64px;
      --footer-h: 52px;
      --navy-1: #000000;
      --navy-2: #141414;
      --muted-bg: #f3f7fb;
      --accent: #0d6efd;
    }
    *{ box-sizing:border-box }
    html,body{ height:100%; margin:0; font-family: Inter,"Segoe UI",Roboto,Arial,sans-serif; background:var(--muted-bg); color:#102a43; }

    /* ============ SIDEBAR ============ */
    .sidebar{
      position: fixed; left:0; top:0; bottom:0;
      width: var(--sidebar-w);
      background: linear-gradient(180deg, var(--navy-1), var(--navy-2));
      color:#e6eef8;
      padding:18px 14px;
      display:flex; flex-direction:column;
      box-shadow:6px 0 20px rgba(3,12,27,0.25);
      z-index:1200;
      transition: transform .28s ease;
    }
    .sidebar.closed{ transform: translateX(-110%); }

    .brand-compact{ display:flex; align-items:center; gap:10px; padding-bottom:12px; }
    .brand-compact img{ width:44px; height:44px; border-radius:8px; object-fit:cover; border:2px solid rgba(255,255,255,.06); }
    .brand-compact .title{ font-weight:700; font-size:14px; color:#fff; }
    .brand-compact .sub{ font-size:12px; color:rgba(230,238,248,.8); }

    .sidebar-body{ flex:1; overflow-y:auto; padding-right:6px; padding-bottom:100px; }
    .sidebar-body::-webkit-scrollbar{ width:10px; }
    .sidebar-body::-webkit-scrollbar-thumb{ background:rgba(255,255,255,.12); border-radius:8px; }

    .menu-section{ margin-top:12px; padding-top:6px; border-top:1px solid rgba(255,255,255,.08); }

    /* MENU look/spacing */
    .menu .menu-item{
      display:flex; align-items:center; justify-content:space-between; gap:12px;
      padding:12px 14px;
      border-radius:10px;
      background: rgba(255,255,255,.06);
      color:#eaf2ff;
      font-weight:700; font-size:14px;
      margin-bottom:10px;
      text-decoration:none;
      transition: background .2s ease, transform .15s ease, color .2s ease;
      cursor:pointer;
    }
    .menu .menu-item:hover,
    .menu .menu-item.active,
    .menu .menu-item[aria-expanded="true"]{
      background: rgba(255,255,255,.16);
      color:#fff; text-decoration:none;
      transform: translateY(-1px);
    }
    .menu .menu-item i{ width:22px; text-align:center; font-size:18px; color:#d7e9ff; }

    .submenu{ padding:6px 6px 6px 44px; }
    .submenu a{
      display:block; padding:8px 0;
      color:rgba(234,235,236,.92); font-size:13.5px; text-decoration:none;
    }
    .submenu a:hover{ color:#fff; }

    /* Footer area inside sidebar (pinned logout) */
    .sidebar-footer{
      position:absolute; left:14px; right:14px; bottom:14px;
      border-top:none; margin-top:0; padding-top:0;
    }
   .logout-btn{
  display:flex; align-items:center; gap:12px;
  width:95%;                  /* 🔹 पूरी width के बजाय 85% */  
  height:45px;
  padding:0 14px;
  border:0; border-radius:10px;
  background:#dc3545;          /* solid red */
  color:#fff;
  font-weight:800; font-size:15px; letter-spacing:.2px;
  box-shadow:0 2px 8px rgba(0,0,0,.3);
  text-align:left;
}

    .logout-btn i{ font-size:20px; }
    .logout-btn:hover{ background:#b02a37; color:#fff; }

    /* ============ MAIN ============ */
    .main{ margin-left:var(--sidebar-w); display:flex; flex-direction:column; min-height:100vh; transition: margin-left .32s ease; }
    .main.full{ margin-left:0; }

    /* Topbar */
    .topbar{
      height:var(--header-h);
      display:flex; align-items:center; gap:14px; padding:0 20px;
      background: linear-gradient(180deg,#fff,#f3f6fd);
      border-bottom:3px solid var(--accent);
      box-shadow:0 4px 10px rgba(13,110,253,.12);
      position:sticky; top:0; z-index:1100;
    }
    .toggle-btn{ width:42px; height:42px; border-radius:8px; border:none; background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:18px; box-shadow:0 2px 6px rgba(0,0,0,.06); }
    .page-heading{ font-size:20px; font-weight:700; color:#0b2a44; margin:0; }

    /* Content */
    .content-wrap{ flex:1; padding:20px; background:var(--muted-bg); overflow:auto; }
    .card-surface{ background:#fff; border-radius:12px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.06); }

    /* Footer */
    .site-footer{
      height:var(--footer-h);
      display:flex; align-items:center; justify-content:center; gap:8px;
      background: linear-gradient(180deg,#fff,#f3f6fd);
      border-top:3px solid var(--accent);
      box-shadow:0 -4px 12px rgba(13,110,253,.08);
      font-size:13px; font-weight:500; color:#333;
      position:sticky; bottom:0;
    }

    @media (max-width:780px){
      .sidebar{ width:82%; }
      .main{ margin-left:0; }
    }
  </style>
</head>
<body>

  <!-- SIDEBAR -->
  <aside id="sidebar" class="sidebar">
    <div class="brand-compact">
      <img src="{{ asset('images/logo.jpeg') }}" alt="logo">
      <div>
        <div class="title">SABSJS</div>
        <div class="sub">Admin Dashboard</div>
      </div>
    </div>

    @php
      $role = auth()->user()->role ?? null;
      $can = fn($section)=> $role==='super_admin' || $role===$section;
    @endphp

    <div class="sidebar-body">
      <nav class="menu">

        <div class="menu-section">
          <a href="{{ route('dashboard.super_admin') }}" class="menu-item active">
            <span><i class="bi bi-grid-fill"></i> Dashboard</span>
          </a>
          <a href="{{ route('session.management') }}" class="menu-item">
            <span class="d-flex align-items-center gap-2"><i class="bi bi-calendar3"></i> Session Management</span>
          </a>
          <a href="{{ route('designation-type.management') }}" class="menu-item">
            <span class="d-flex align-items-center gap-2"><i class="bi bi-person-badge"></i> Designation Type</span>
          </a>
        </div>

        @if($can('shramnopasak'))
        <div class="menu-section">
          <div style="font-size:12px;color:rgba(230,238,248,0.6);font-weight:700;margin-bottom:8px;">SHRAMNOPASAK</div>
          <button class="menu-item w-100 text-start" type="button" data-bs-toggle="collapse" data-bs-target="#menuShramnopasak">
            <span class="d-flex align-items-center gap-2"><i class="bi bi-person-badge"></i> Total Registered</span>
            <i class="bi bi-caret-down-fill"></i>
          </button>
          <div id="menuShramnopasak" class="submenu collapse">
            <a href="#">Shramnopasak 1</a>
            <a href="#">Shramnopasak 2</a>
          </div>
        </div>
        @endif

        @if($can('sahitya'))
        <div class="menu-section">
          <div style="font-size:12px;color:rgba(230,238,248,0.6);font-weight:700;margin-bottom:8px;">SAHITYA</div>
          <button class="menu-item w-100 text-start" type="button" data-bs-toggle="collapse" data-bs-target="#menuSahitya">
            <span class="d-flex align-items-center gap-2"><i class="bi bi-journal"></i> Manage Sahitya</span>
            <i class="bi bi-caret-down-fill"></i>
          </button>
          <div id="menuSahitya" class="submenu collapse">
            <a href="#"><i class="bi bi-plus-square"></i> Add Sahitya</a>
            <a href="#"><i class="bi bi-eye"></i> View Sahitya</a>
          </div>
        </div>
        @endif

        @if($can('dispatch'))
        <div class="menu-section">
          <div style="font-size:12px;color:rgba(230,238,248,0.6);font-weight:700;margin-bottom:8px;">DISPATCH</div>
          <a class="menu-item d-flex align-items-center text-start" data-bs-toggle="collapse" href="#menuDispatch">
            <span class="d-flex align-items-center gap-2"><i class="bi bi-folder2-open"></i> Dispatch Reports</span>
            <i class="bi bi-caret-down-fill"></i>
          </a>
          <div id="menuDispatch" class="submenu collapse ps-3">
            <a href="#" class="d-flex align-items-center gap-2"><i class="bi bi-building"></i> All Dispatch Details</a>
            <a href="#" class="d-flex align-items-center gap-2"><i class="bi bi-file-earmark-excel text-success"></i> Excel Report</a>
            <a href="#" class="d-flex align-items-center gap-2"><i class="bi bi-file-earmark-pdf text-danger"></i> PDF Report</a>
          </div>
        </div>
        @endif

        @if($can('mahila_samiti'))
        <div class="menu-section">
          <div style="font-size:12px;color:rgba(230,238,248,0.6);font-weight:700;margin-bottom:8px;">MAHILA SAMITI</div>
        
          <!-- Added new links -->
          <a href="{{ route('mahila-samiti-members') }}" class="menu-item"><span class="d-flex align-items-center gap-2"><i class="bi bi-list-ul"></i> View All Members</span></a>
          <a href="{{ route('mahila-samiti-members.add') }}" class="menu-item"><span class="d-flex align-items-center gap-2"><i class="bi bi-person-plus"></i> Add New Member</span></a>
        </div>
        @endif

        @if($can('other'))
        <div class="menu-section">
          <div style="font-size:12px;color:rgba(230,238,248,0.6);font-weight:700;margin-bottom:8px;">OTHERS</div>
          <a href="#" class="menu-item"><span class="d-flex align-items-center gap-2"><i class="bi bi-list-task"></i> Others</span></a>
        </div>
        @endif

      </nav>
    </div>

    <!-- 🔻 Logout button pinned at bottom -->
    <div class="sidebar-footer">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
          <i class="bi bi-box-arrow-right"></i> Logout
        </button>
      </form>
    </div>
  </aside>

  <!-- MAIN -->
  <div id="main" class="main">
    <header class="topbar">
      <!-- 🔹 Toggle button -->
      <button id="toggleBtn" class="toggle-btn"><i id="toggleIcon" class="bi bi-list"></i></button>
      <h3 class="page-heading">Admin Dashboard</h3>
      <div class="ms-auto d-none d-sm-block">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="btn btn-sm btn-outline-danger">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
          </button>
        </form>
      </div>
    </header>

    <div class="content-wrap">
      <div class="card-surface">
        @yield('content')
      </div>
    </div>

    <footer class="site-footer">
      &copy; 2025 श्री अखिल भारतवर्षीय साधुमार्गी जैन संघ · IT DEPARTMENT
    </footer>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    (function(){
      const sidebar = document.getElementById('sidebar');
      const main = document.getElementById('main');
      const toggleBtn = document.getElementById('toggleBtn');
      const toggleIcon = document.getElementById('toggleIcon');

      toggleBtn?.addEventListener('click', () => {
        sidebar.classList.toggle('closed');
        main.classList.toggle('full');
        toggleIcon?.classList.toggle('bi-x-lg');
        toggleIcon?.classList.toggle('bi-list');
      });
    })();
  </script>
</body>
</html>
