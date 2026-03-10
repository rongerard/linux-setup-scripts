<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contacts — My Database</title>

  <!-- Elegant serif + refined mono pairing -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300&family=DM+Mono:wght@300;400&display=swap" rel="stylesheet" />

  <style>
    /* ── Design tokens ───────────────────────────────────── */
    :root {
      --bg:        #0d0d10;
      --surface:   #13131a;
      --border:    #2a2a38;
      --accent:    #c8a96e;       /* warm gold */
      --accent-lt: #e8d5a8;
      --text:      #e8e4dc;
      --muted:     #7a7870;
      --success:   #5fbf8a;
      --error:     #e07070;

      --radius:    6px;
      --transition: 260ms cubic-bezier(.4,0,.2,1);
    }

    /* ── Reset ───────────────────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    /* ── Base ────────────────────────────────────────────── */
    html { font-size: 16px; scroll-behavior: smooth; }

    body {
      background: var(--bg);
      color: var(--text);
      font-family: 'DM Mono', monospace;
      font-weight: 300;
      min-height: 100vh;
      display: grid;
      grid-template-rows: auto 1fr auto;
    }

    /* ── Grain overlay ───────────────────────────────────── */
    body::before {
      content: '';
      position: fixed; inset: 0;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='200' height='200' filter='url(%23n)' opacity='0.035'/%3E%3C/svg%3E");
      pointer-events: none;
      z-index: 0;
    }

    /* ── Glow backdrop ───────────────────────────────────── */
    body::after {
      content: '';
      position: fixed;
      top: -20%; left: 50%;
      transform: translateX(-50%);
      width: 60vw; height: 60vw;
      background: radial-gradient(ellipse, rgba(200,169,110,.06) 0%, transparent 70%);
      pointer-events: none;
      z-index: 0;
    }

    /* ── Header ──────────────────────────────────────────── */
    header {
      position: relative; z-index: 1;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 2rem 4rem;
      border-bottom: 1px solid var(--border);
    }

    .logo {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.5rem;
      font-weight: 300;
      letter-spacing: .12em;
      color: var(--accent-lt);
    }

    .logo span { color: var(--accent); font-style: italic; }

    nav {
      font-size: .7rem;
      letter-spacing: .18em;
      text-transform: uppercase;
      color: var(--muted);
    }

    /* ── Main layout ─────────────────────────────────────── */
    main {
      position: relative; z-index: 1;
      max-width: 860px;
      width: 100%;
      margin: 0 auto;
      padding: 5rem 2rem 6rem;
    }

    /* ── Hero text ───────────────────────────────────────── */
    .hero {
      margin-bottom: 4rem;
      animation: fadeUp .7s ease both;
    }

    .hero-eyebrow {
      font-size: .65rem;
      letter-spacing: .3em;
      text-transform: uppercase;
      color: var(--accent);
      margin-bottom: 1.2rem;
    }

    .hero h1 {
      font-family: 'Cormorant Garamond', serif;
      font-size: clamp(2.4rem, 5vw, 3.8rem);
      font-weight: 300;
      line-height: 1.1;
      color: var(--text);
      letter-spacing: -.01em;
    }

    .hero h1 em {
      font-style: italic;
      color: var(--accent-lt);
    }

    .hero p {
      margin-top: 1.2rem;
      font-size: .82rem;
      line-height: 1.8;
      color: var(--muted);
      max-width: 48ch;
    }

    /* ── Card ────────────────────────────────────────────── */
    .card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 2.8rem 3rem;
      animation: fadeUp .7s .15s ease both;
      position: relative;
      overflow: hidden;
    }

    .card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, var(--accent), transparent);
      opacity: .6;
    }

    .card-header {
      display: flex;
      align-items: baseline;
      gap: 1rem;
      margin-bottom: 2.4rem;
      padding-bottom: 1.4rem;
      border-bottom: 1px solid var(--border);
    }

    .card-header h2 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.4rem;
      font-weight: 400;
      color: var(--text);
    }

    .badge {
      font-size: .62rem;
      letter-spacing: .2em;
      text-transform: uppercase;
      color: var(--accent);
      border: 1px solid rgba(200,169,110,.35);
      border-radius: 2px;
      padding: .2em .55em;
    }

    /* ── Form ────────────────────────────────────────────── */
    form { display: grid; gap: 1.6rem; }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.6rem; }

    .field { display: flex; flex-direction: column; gap: .5rem; }

    label {
      font-size: .62rem;
      letter-spacing: .22em;
      text-transform: uppercase;
      color: var(--muted);
    }

    label .req {
      color: var(--accent);
      margin-left: .2em;
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"] {
      background: rgba(255,255,255,.03);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      color: var(--text);
      font-family: 'DM Mono', monospace;
      font-size: .82rem;
      font-weight: 300;
      padding: .78rem 1rem;
      outline: none;
      transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
      width: 100%;
    }

    input:focus {
      border-color: var(--accent);
      background: rgba(200,169,110,.04);
      box-shadow: 0 0 0 3px rgba(200,169,110,.1);
    }

    input::placeholder { color: rgba(122,120,112,.5); }

    /* ── Submit button ───────────────────────────────────── */
    .btn-row { display: flex; justify-content: flex-end; }

    button[type="submit"] {
      background: var(--accent);
      border: none;
      border-radius: var(--radius);
      color: #0d0d10;
      cursor: pointer;
      font-family: 'DM Mono', monospace;
      font-size: .72rem;
      font-weight: 400;
      letter-spacing: .18em;
      text-transform: uppercase;
      padding: .85rem 2.2rem;
      transition: background var(--transition), transform var(--transition), box-shadow var(--transition);
      position: relative;
      overflow: hidden;
    }

    button[type="submit"]:hover {
      background: var(--accent-lt);
      box-shadow: 0 4px 20px rgba(200,169,110,.3);
      transform: translateY(-1px);
    }

    button[type="submit"]:active { transform: translateY(0); }

    button[type="submit"]:disabled {
      opacity: .5;
      cursor: not-allowed;
      transform: none;
    }

    /* ── Toast notification ──────────────────────────────── */
    #toast {
      position: fixed;
      bottom: 2.2rem;
      right: 2.2rem;
      z-index: 100;
      min-width: 280px;
      max-width: 400px;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 1rem 1.4rem;
      display: flex;
      align-items: flex-start;
      gap: .9rem;
      box-shadow: 0 8px 32px rgba(0,0,0,.5);
      opacity: 0;
      transform: translateY(12px);
      transition: opacity .3s ease, transform .3s ease;
      pointer-events: none;
    }

    #toast.show {
      opacity: 1;
      transform: translateY(0);
      pointer-events: auto;
    }

    .toast-icon {
      flex-shrink: 0;
      width: 18px; height: 18px;
      border-radius: 50%;
      display: grid; place-items: center;
      font-size: .65rem;
      margin-top: .1rem;
    }

    .toast-icon.ok  { background: rgba(95,191,138,.15); color: var(--success); border: 1px solid var(--success); }
    .toast-icon.err { background: rgba(224,112,112,.15); color: var(--error);   border: 1px solid var(--error);   }

    .toast-body { flex: 1; }

    .toast-title {
      font-size: .7rem;
      letter-spacing: .18em;
      text-transform: uppercase;
      margin-bottom: .25rem;
    }

    .toast-title.ok  { color: var(--success); }
    .toast-title.err { color: var(--error); }

    .toast-msg { font-size: .78rem; color: var(--muted); line-height: 1.5; }

    /* ── Footer ──────────────────────────────────────────── */
    footer {
      position: relative; z-index: 1;
      text-align: center;
      padding: 1.6rem;
      font-size: .65rem;
      letter-spacing: .15em;
      color: #3a3a48;
      border-top: 1px solid var(--border);
    }

    /* ── Animations ──────────────────────────────────────── */
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(18px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Responsive ──────────────────────────────────────── */
    @media (max-width: 640px) {
      header { padding: 1.4rem 1.4rem; }
      nav    { display: none; }
      main   { padding: 3rem 1.4rem 4rem; }
      .card  { padding: 1.8rem 1.4rem; }
      .form-row { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

  <!-- ── Header ──────────────────────────────────────────── -->
  <header>
    <div class="logo">my<span>_</span>database</div>
    <nav>Contacts Manager</nav>
  </header>

  <!-- ── Main ────────────────────────────────────────────── -->
  <main>

    <!-- Hero -->
    <div class="hero">
      <p class="hero-eyebrow">Contacts — v1.0</p>
      <h1>Manage your<br/><em>connections</em></h1>
      <p>Store and organise contact details in a secure MariaDB database. Fill in the form below to add a new entry.</p>
    </div>

    <!-- Add contact card -->
    <div class="card">
      <div class="card-header">
        <h2>Add a Contact</h2>
        <span class="badge">New entry</span>
      </div>

      <form id="contactForm" novalidate>
        <div class="form-row">
          <div class="field">
            <label for="name">Full name <span class="req">*</span></label>
            <input type="text" id="name" name="name"
                   placeholder="Jane Smith"
                   maxlength="100" autocomplete="off" required />
          </div>
          <div class="field">
            <label for="email">Email address <span class="req">*</span></label>
            <input type="email" id="email" name="email"
                   placeholder="jane@example.com"
                   maxlength="150" autocomplete="off" required />
          </div>
        </div>

        <div class="field">
          <label for="phone">Phone number <span class="req">*</span></label>
          <input type="tel" id="phone" name="phone"
                 placeholder="+1 (555) 000-0000"
                 maxlength="30" autocomplete="off" required />
        </div>

        <div class="btn-row">
          <button type="submit" id="submitBtn">Save Contact</button>
        </div>
      </form>
    </div>

  </main>

  <!-- ── Footer ───────────────────────────────────────────── -->
  <footer>© <?= date('Y') ?> my_database · Built with PHP &amp; MariaDB</footer>

  <!-- ── Toast ────────────────────────────────────────────── -->
  <div id="toast" role="alert" aria-live="polite">
    <div class="toast-icon" id="toastIcon"></div>
    <div class="toast-body">
      <div class="toast-title" id="toastTitle"></div>
      <div class="toast-msg"   id="toastMsg"></div>
    </div>
  </div>

  <!-- ── Script ───────────────────────────────────────────── -->
  <script>
    const form      = document.getElementById('contactForm');
    const submitBtn = document.getElementById('submitBtn');
    const toast     = document.getElementById('toast');
    let toastTimer;

    function showToast(ok, title, msg) {
      const icon = document.getElementById('toastIcon');
      icon.className      = 'toast-icon ' + (ok ? 'ok' : 'err');
      icon.textContent    = ok ? '✓' : '✕';
      document.getElementById('toastTitle').className   = 'toast-title ' + (ok ? 'ok' : 'err');
      document.getElementById('toastTitle').textContent = title;
      document.getElementById('toastMsg').textContent   = msg;

      toast.classList.add('show');
      clearTimeout(toastTimer);
      toastTimer = setTimeout(() => toast.classList.remove('show'), 5000);
    }

    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      // ── Client-side validation ────────────────────────────
      const name  = form.name.value.trim();
      const email = form.email.value.trim();
      const phone = form.phone.value.trim();

      if (!name || !email || !phone) {
        showToast(false, 'Missing fields', 'Please fill in all required fields.');
        return;
      }

      const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRe.test(email)) {
        showToast(false, 'Invalid email', 'Please enter a valid email address.');
        return;
      }

      // ── Submit ────────────────────────────────────────────
      submitBtn.disabled    = true;
      submitBtn.textContent = 'Saving…';

      try {
        const body = new URLSearchParams({ name, email, phone });
        const res  = await fetch('add_contact.php', { method: 'POST', body });
        const data = await res.json();

        if (data.success) {
          showToast(true, 'Contact saved', `Entry #${data.id} has been stored successfully.`);
          form.reset();
        } else {
          showToast(false, 'Could not save', data.message || 'An unexpected error occurred.');
        }
      } catch (err) {
        showToast(false, 'Network error', 'Could not reach the server. Please try again.');
      } finally {
        submitBtn.disabled    = false;
        submitBtn.textContent = 'Save Contact';
      }
    });
  </script>

</body>
</html>
