<!DOCTYPE html>
<html lang="hi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SSO Login - SABSJS</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

  <style>
    :root{
      --brand: #2458f4;
      --brand-600: #1d48d1;
      --brand-hover: #1e3ad3;
      --glass: rgba(255,255,255,0.95);
      --muted: #6b7280;
      --bg-start: #0f172a;
      --bg-end: #1e40af;
      --card-radius: 20px;
      --shadow: 0 25px 60px rgba(12,24,60,0.35);
      --glow: 0 0 40px rgba(36,88,244,0.15);
    }

    *{ box-sizing:border-box; margin:0; padding:0; font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, Arial; }

    html,body{ height:100%; }

    body{
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:28px;
      background: 
        radial-gradient(ellipse at top left, #1e40af 0%, transparent 50%),
        radial-gradient(ellipse at bottom right, #7c3aed 0%, transparent 50%),
        linear-gradient(135deg, var(--bg-start) 0%, #1e293b  50%, var(--bg-end) 100%);
      color: #eaf0ff;
      animation: backgroundShift 20s ease-in-out infinite;
    }

    @keyframes backgroundShift {
      0%, 100% { filter: hue-rotate(0deg); }
      50% { filter: hue-rotate(10deg); }
    }

    /* outer wrapper */
    .wrap{
      width:min(1200px, 96vw);
      height:min(600px, 90vh);
      border-radius:28px;
      overflow:hidden;
      display:grid;
      grid-template-columns: 1fr 540px;
      box-shadow: 
        var(--shadow),
        0 0 0 1px rgba(255,255,255,0.05),
        inset 0 1px 0 rgba(255,255,255,0.1);
      background:
        linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01));
      backdrop-filter: blur(20px);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .wrap:hover {
      transform: translateY(-2px);
      box-shadow: 
        0 30px 70px rgba(12,24,60,0.4),
        var(--glow),
        0 0 0 1px rgba(255,255,255,0.08);
    }

    /* LEFT PANEL - clean, big centered logo + SSO text */
    .panel-left{
      padding:50px;
      display:flex;
      flex-direction:column;
      justify-content:flex-start;
      gap:15px;
      background:
        radial-gradient(800px 400px at 15% 15%, rgba(56, 189, 248, 0.08), transparent),
        radial-gradient(600px 300px at 85% 85%, rgba(167, 139, 250, 0.06), transparent),
        linear-gradient(135deg, rgba(36,88,244,0.12), rgba(29,72,209,0.06));
      position: relative;
      overflow: hidden;
    }

    .panel-left::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.02)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
      opacity: 0.3;
      pointer-events: none;
    }

    /* center only logo (moved away from top) */
    .center-logo-wrap{
      width:100%;
      display:flex;
      justify-content:center;
      align-items:center;
      margin-top:36px;   /* pushes logo down from very top */
      margin-bottom:18px;
    }

    .center-logo{
      width:160px;
      height:160px;
      border-radius:24px;
      padding:15px;
      background: transparent; /* Ensured no background */
      object-fit:contain; /* Adjusted to fit the logo properly */
      box-shadow: 
        0 20px 40px rgba(3,18,54,0.25),
        0 0 0 1px rgba(255,255,255,0.5),
        inset 0 1px 0 rgba(255,255,255,0.8);
      display:block;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .center-logo:hover {
      transform: translateY(-3px) scale(1.02);
      box-shadow: 
        0 25px 50px rgba(3,18,54,0.35),
        0 0 0 1px rgba(255,255,255,0.6),
        inset 0 1px 0 rgba(255,255,255,0.9);
    }

    /* SSO text block below the centered logo */
    .hero{
      margin-top:6px;
      display:flex;
      align-items:center;
      gap:22px;
      padding-left:8px;
    }

    .hero svg{ width:210px; height:auto; opacity:0.92; }

    .hero-copy h2{
      font-size:28px;
      color:#ffffff;
      font-weight:700;
      margin:0 0 8px 0;
      line-height:1.05;
    }

    .hero-copy p{
      margin:0;
      font-size:14px;
      color:rgba(224,233,255,0.9);
      max-width:360px;
      line-height:1.5;
    }

    /* RIGHT PANEL - login form */
    .panel-right{
      padding:50px;
      background: 
        linear-gradient(135deg, var(--glass), rgba(248, 250, 252, 0.98));
      display:flex;
      flex-direction:column;
      justify-content:center;
      gap:20px;
      backdrop-filter: blur(20px);
      border-left: 1px solid rgba(255,255,255,0.2);
    }

    .heading{
      font-size:28px;
      font-weight:800;
      color:#0b1220;
      background: linear-gradient(135deg, #0b1220, #374151);
      background-clip: text;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .sub{
      font-size:15px;
      color:var(--muted);
      margin-bottom:15px;
      font-weight:500;
    }

    form{ display:flex; flex-direction:column; gap:12px; }

    label{ font-size:13px; color:#0b1220; font-weight:700; }

    input[type="email"], input[type="password"]{
      width:100%;
      padding:16px 18px;
      border-radius:14px;
      border:1.5px solid #e2e8f0;
      background:
        linear-gradient(135deg, #ffffff, #f8fafc);
      font-size:15px;
      outline:none;
      box-shadow: 
        0 1px 3px rgba(16,24,40,0.1),
        inset 0 1px 2px rgba(255,255,255,0.9);
      transition: all 0.2s ease;
      font-weight:500;
    }
    
    input:focus{
      border-color: var(--brand);
      box-shadow: 
        0 0 0 3px rgba(36,88,244,0.1),
        0 4px 14px rgba(36,88,244,0.15),
        inset 0 1px 2px rgba(255,255,255,0.9);
      transform: translateY(-1px);
    }

    input:hover:not(:focus) {
      border-color: #cbd5e1;
      box-shadow: 
        0 2px 8px rgba(16,24,40,0.12),
        inset 0 1px 2px rgba(255,255,255,0.9);
    }

    .input-wrap{ position:relative; display:flex; align-items:center; }
    .toggle-pass{
      position:absolute; right:10px; top:50%; transform:translateY(-50%);
      border:0; background:transparent; cursor:pointer; padding:6px; font-size:14px; color:#475569;
    }

    .actions{
      display:flex; align-items:center; justify-content:space-between; gap:8px;
    }
    .remember{ display:flex; gap:8px; align-items:center; color:#475569; font-size:13px; }
    .remember input{ width:16px; height:16px; accent-color:var(--brand); }

    .btn{
      padding:16px 18px; 
      border-radius:14px; 
      border:0; 
      cursor:pointer;
      background: 
        linear-gradient(135deg, var(--brand) 0%, var(--brand-600) 50%, var(--brand-hover) 100%);
      color:#fff; 
      font-weight:700; 
      font-size:16px;
      box-shadow:
        0 4px 14px rgba(36,88,244,0.25),
        inset 0 1px 0 rgba(255,255,255,0.2);
      transition: all 0.2s ease;
      position: relative;
      overflow: hidden;
    }

    .btn:hover {
      transform: translateY(-2px);
      box-shadow:
        0 8px 25px rgba(36,88,244,0.35),
        inset 0 1px 0 rgba(255,255,255,0.25);
      background: 
        linear-gradient(135deg, var(--brand-hover) 0%, var(--brand-600) 100%);
    }

    .btn:active {
      transform: translateY(0px);
      box-shadow:
        0 4px 14px rgba(36,88,244,0.25),
        inset 0 2px 4px rgba(0,0,0,0.1);
    }

    .small{ font-size:12px; color:#6b7280; text-align:center; margin-top:6px; }

    .error{ color:#d64545; font-size:13px; }

    /* responsive */
    @media (max-width:1000px){
      .wrap{ grid-template-columns: 1fr; height:auto; }
      .panel-left{ order:2; padding:22px; }
      .panel-right{ order:1; padding:22px; }
      .center-logo{ width:140px; height:140px; }
      .hero svg{ display:none; } /* hide illustration on small */
    }
  </style>
</head>
<body>

  <div class="wrap" role="region" aria-label="SSO login panel">
    <!-- LEFT: centered logo and SSO text only (top heading removed) -->
    <div class="panel-left" aria-hidden="false">

      <!-- HEADING ABOVE THE LOGO -->
      <h1 class="text-center" style="font-size: 2rem; font-weight: 700; color: #ffffff; margin-bottom: 20px;">श्री अखिल भारतवर्षीय साधुमार्गी जैन संघ </h1>

      <!-- BIG CENTERED LOGO (moved away from top) -->
      <div class="center-logo-wrap">
        <img class="center-logo" src="{{ asset('images/logo.png') }}" alt="SABSJS Main Logo">
      </div>

      <!-- SSO text block only -->
      <div class="hero" aria-hidden="false">
        <!-- subtle decorative illustration -->
        <svg viewBox="0 0 240 220" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="decor">
          <rect x="12" y="40" width="180" height="96" rx="14" fill="#ffffff" opacity="0.08"/>
          <circle cx="204" cy="64" r="20" fill="#ffffff" opacity="0.9"/>
        </svg>

        <div class="hero-copy">
          <h2>Secure single sign-on</h2>
          <p>SABSJS IT Department</p>
        </div>
      </div>

    </div>

    <!-- RIGHT: Login form -->
    <div class="panel-right" aria-labelledby="loginHeading">
      <div>
        <div class="heading" id="loginHeading">Login</div>
        <div class="sub">Sign in to continue</div>
      </div>

      <form method="POST" action="/login" novalidate>
        @csrf

        <!-- server-side error message -->
        @if(session('error'))
          <div class="error" role="alert">{{ session('error') }}</div>
        @endif

        <label for="email">Email address</label>
        <input id="email" name="email" type="email" placeholder="you@example.com" required autocomplete="email" />

        <label for="password">Password</label>
        <div class="input-wrap">
          <input id="password" name="password" type="password" placeholder="Enter your password" required autocomplete="current-password" />
          <button type="button" class="toggle-pass" aria-label="Show password" id="togglePass" title="Show / hide password" style="background: transparent; border: none; cursor: pointer;">
            <i class="bi bi-eye" style="font-size: 20px; color: #475569;"></i>
          </button>
        </div>

        <div class="actions" style="margin-top:4px;">
          <label class="remember">
            <input type="checkbox" name="remember" />
            Remember me
          </label>
          <a href="/password/reset" style="font-size:13px; color:var(--brand-600); text-decoration:none; font-weight:700;">Forgot?</a>
        </div>

        <button class="btn" type="submit" aria-label="Sign in">Sign In</button>

        <div class="small">By continuing you agree to our <a href="/terms" style="color:var(--brand-600); text-decoration:none; font-weight:700;">Terms</a>.</div>
      </form>
    </div>
  </div>

  <script>
    (function(){
      const pass = document.getElementById('password');
      const btn  = document.getElementById('togglePass');

      btn.addEventListener('click', function(){
        const showing = pass.type === 'text';
        pass.type = showing ? 'password' : 'text';
        btn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
      });

      // focus email on load for better UX
      const email = document.getElementById('email');
      if(email){ email.focus(); }

      // small client-side validation to improve UX only
      const form = document.querySelector('form');
      form.addEventListener('submit', function(e){
        const em = email.value.trim();
        const pw = pass.value.trim();
        if(!em || !pw){
          e.preventDefault();
          if(!document.querySelector('.error-inline')){
            const el = document.createElement('div');
            el.className = 'error error-inline';
            el.textContent = 'Please enter both email and password.';
            form.prepend(el);
            setTimeout(()=> el.remove(), 3600);
          }
        }
      });
    })();
  </script>

</body>
</html>
