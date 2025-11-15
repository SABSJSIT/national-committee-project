<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>SSO Login - SABSJS</title>
  <style>
    :root{
      --brand: #2f6df6;      /* primary blue */
      --brand-dark:#2156d9;
      --bg: #2f6df6;
      --card-radius: 22px;
    }
    *{ box-sizing:border-box; margin:0; padding:0; font-family: 'Segoe UI', Roboto, Arial, sans-serif; }

    body{
      min-height:100vh; display:grid; place-items:center;
      background:
        radial-gradient(40rem 40rem at 15% 25%, rgba(255,255,255,0.08), transparent 40%),
        radial-gradient(22rem 22rem at 85% 30%, rgba(255,255,255,0.08), transparent 55%),
        radial-gradient(18rem 18rem at 30% 80%, rgba(255,255,255,0.06), transparent 60%),
        linear-gradient(180deg, #3b73ff, #2f6df6);
      overflow:hidden;
    }

    .rings::before,
    .rings::after{
      content:""; position:fixed; inset:auto; border-radius:50%;
      pointer-events:none; filter: blur(2px);
    }
    .rings::before{
      width:320px; height:320px; left:5vw; top:10vh;
      box-shadow:0 0 0 18px rgba(255,255,255,0.12) inset;
      opacity:.55;
    }
    .rings::after{
      width:220px; height:220px; right:8vw; top:28vh;
      box-shadow:0 0 0 14px rgba(255,255,255,0.12) inset;
      opacity:.45;
    }

    .card{
      width:min(92vw, 420px);
      background:#fff; border-radius: var(--card-radius);
      box-shadow: 0 30px 70px rgba(17,38,146,0.35);
      padding:28px 28px 26px;
    }

    .card .title{
      text-align:center; font-weight:800; font-size:26px; color:#14213d;
      margin-bottom:18px;
    }
    .card .subtitle{
      text-align:center; font-size:13.5px; color:#667085; margin-bottom:20px;
    }

    .brand-line{
      width:60px; height:6px; border-radius:6px; background:var(--brand);
      margin:0 auto 18px;
    }

    .logo{
      width:120px; height:90px; border-radius:14px; object-fit:cover;
      display:block; margin:0 auto 12px; box-shadow:0 4px 10px rgba(0,0,0,.08);
    }

    form{ display:flex; flex-direction:column; gap:14px; }

    .input{
      width:100%; border:0; outline:none;
      background:#f3f5f9; color:#0f172a;
      border-radius:14px; padding:14px 14px;
      font-size:14.5px; box-shadow: inset 0 1px 2px rgba(16,24,40,.06);
    }

    /* password field wrapper */
    .input-wrap{ position:relative; }
    .toggle-pass{
      position:absolute; right:10px; top:50%; transform:translateY(-50%);
      border:0; background:transparent; cursor:pointer; padding:6px; border-radius:8px;
    }
    .toggle-pass svg{ width:20px; height:20px; display:block; color:#475569; }

    .error{
      color:#e03131; text-align:center; font-size:13.5px; margin-top:-2px;
    }

    .actions{
      display:flex; align-items:center; justify-content:space-between; gap:10px;
      margin-top:4px;
    }
    .helper{
      font-size:13px; color:#475569; text-decoration:none;
    }
    .helper:hover{ text-decoration:underline; }

    .btn{
      width:100%; border:0; cursor:pointer;
      padding:12px 16px; border-radius:12px;
      background: var(--brand); color:#fff; font-weight:700; font-size:15px;
      box-shadow: 0 10px 18px rgba(47,109,246,.32);
      transition: transform .08s ease, background .15s ease, box-shadow .15s ease;
    }
    .btn:hover{ background: var(--brand-dark); box-shadow: 0 10px 20px rgba(33,86,217,.36); }
    .btn:active{ transform: translateY(1px); }

    .footnote{
      text-align:center; color:#475569; font-size:12.5px; margin-top:14px;
    }

    .page-footer{
      position:fixed; bottom:14px; left:0; right:0; text-align:center;
      color:#e6eeff; font-size:12.5px; opacity:.9;
      text-shadow:0 1px 0 rgba(0,0,0,.08);
      pointer-events:none;
    }
  </style>
</head>
<body class="rings">

  <main class="card">
    <img class="logo" src="{{ asset('images/logo.jpeg') }}" alt="Logo">
    <div class="brand-line"></div>
    <h1 class="title">Login</h1>
    <p class="subtitle">Sign in to continue</p>

    <form method="POST" action="/login">
      @csrf
      @if(session('error'))
        <div class="error">{{ session('error') }}</div>
      @endif

      <input class="input" type="email" name="email" placeholder="Email address" required />

      <!-- password input with eye toggle -->
      <div class="input-wrap">
        <input class="input" id="password" type="password" name="password" placeholder="Password" required />
        <button type="button" class="toggle-pass" aria-label="Show password" id="togglePass">
          <!-- eye (show) -->
          <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" stroke="currentColor" stroke-width="2"/>
            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
          </svg>
          <!-- eye-off (hide) -->
          <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display:none">
            <path d="M3 3l18 18" stroke="currentColor" stroke-width="2"/>
            <path d="M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42M6.4 6.55C3.9 8.08 2 12 2 12s3.5 7 10 7a11.7 11.7 0 005.62-1.4M17.6 6.4A11.7 11.7 0 0012 5C5.5 5 2 12 2 12" stroke="currentColor" stroke-width="2"/>
          </svg>
        </button>
      </div>

      <div class="actions">
        <label style="display:flex;align-items:center;gap:8px;color:#475569;font-size:13px;user-select:none;">
          <input type="checkbox" name="remember" style="accent-color:var(--brand);" />
          Remember me
        </label>
      </div>

      <button class="btn" type="submit">SIGN IN</button>
      <p class="footnote">By continuing you agree to our Terms.</p>
    </form>
  </main>

  <div class="page-footer">
    &copy; {{ date('Y') }} श्री अखिल भारतवर्षीय साधुमार्गी जैन संघ ! IT DEPARTMENT
  </div>

  <!-- JS -->
  <script>
    (function(){
      const pass = document.getElementById('password');
      const btn  = document.getElementById('togglePass');
      const open = document.getElementById('eyeOpen');
      const shut = document.getElementById('eyeClosed');

      function toggle(){
        const showing = pass.type === 'text';
        pass.type = showing ? 'password' : 'text';
        btn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
        open.style.display = showing ? '' : 'none';
        shut.style.display = showing ? 'none' : '';
      }

      btn.addEventListener('click', toggle);
    })();
  </script>

</body>
</html>
