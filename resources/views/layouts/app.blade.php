<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', 'ZENTASK')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    :root{
      --bg:#f7fafc;
      --card:#ffffff;
      --muted:#6b7280;
      --accent:#2563eb;
      --radius:10px;
    }
    html,body{height:100%;margin:0;font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial;background:var(--bg);color:#111827}
    .container{max-width:1200px;margin:24px auto;padding:0 16px}
    header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
    .brand{font-weight:700;font-size:22px}
    .card{background:var(--card);border-radius:var(--radius);padding:18px;box-shadow:0 4px 16px rgba(2,6,23,0.06)}
    .grid{display:grid;gap:16px}
    @media(min-width:900px){ .grid.cols-3{grid-template-columns:repeat(3,1fr)} .grid.cols-2{grid-template-columns:repeat(2,1fr)} }
    .small{font-size:13px;color:var(--muted)}
    .btn{background:var(--accent);color:#fff;padding:8px 12px;border-radius:8px;border:0;cursor:pointer}
    a.btn{display:inline-block;text-decoration:none}
    .stat-number{font-size:28px;font-weight:700}
    .list-item{display:flex;align-items:flex-start;gap:12px;padding:10px;border-radius:8px;border:1px solid #f1f5f9;background:#fff}
    .muted{color:var(--muted)}
  </style>
  @stack('head')
</head>
<body>
  <div class="container">
    <header>
      <div class="brand">ZENTASK</div>
      <div>
        <a class="btn" href="{{ route('calendar.index') }}">Open Calendar</a>
      </div>
    </header>

    @yield('content')
  </div>

  @stack('scripts')
</body>
</html>
