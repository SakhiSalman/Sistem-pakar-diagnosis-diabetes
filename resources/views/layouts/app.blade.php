<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Klinik Sehat Bersama')</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root{
    --ink:#132420; --ink-soft:#3E5850; --mist:#F1F8F5; --panel:#FFFFFF; --line:#D8E8E1;
    --teal:#0F6E56; --teal-deep:#085041; --teal-tint:#E1F5EE;
    --coral:#D85A30; --coral-deep:#993C1D; --coral-tint:#FAECE7;
    --amber:#BA7517; --amber-tint:#FAEEDA; --gray-tint:#F1EFE8; --radius:16px;
  }
  *{box-sizing:border-box;}
  body{ margin:0; background:var(--mist); color:var(--ink); font-family:'Inter',sans-serif; line-height:1.7; font-size:16px; }
  h1,h2,h3,.brand{ font-family:'Space Grotesk',sans-serif; }
  header.topbar{ padding:20px 24px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--line); background:var(--panel); flex-wrap:wrap; row-gap:12px; }
  header.topbar > div:last-child{ flex-wrap:wrap; row-gap:8px; }
  .brand{ font-weight:700; font-size:20px; color:var(--teal-deep); display:flex; align-items:center; gap:8px; }
  .brand .dot{ width:11px; height:11px; border-radius:50%; background:var(--coral); display:inline-block; }
  .badge{ font-size:13px; color:var(--teal-deep); background:var(--teal-tint); padding:5px 12px; border-radius:999px; font-weight:600; }
  main{ max-width:720px; margin:0 auto; padding:40px 20px 90px; }
  .hero .eyebrow{ font-size:13px; letter-spacing:.08em; text-transform:uppercase; color:var(--coral-deep); font-weight:700; margin-bottom:10px; }
  .hero h1{ font-size:32px; line-height:1.2; margin:0 0 14px; font-weight:700; }
  .hero p{ color:var(--ink-soft); font-size:16.5px; max-width:58ch; margin:0; }
  .disclaimer{ margin-top:18px; padding:14px 18px; background:var(--amber-tint); border-left:4px solid var(--amber); font-size:14.5px; color:#412402; border-radius:0 8px 8px 0; }
  .alert-error{ margin-bottom:18px; padding:14px 18px; background:var(--coral-tint); border-left:4px solid var(--coral); font-size:14.5px; color:var(--coral-deep); border-radius:0 8px 8px 0; }
  .alert-success{ margin-bottom:18px; padding:14px 18px; background:var(--teal-tint); border-left:4px solid var(--teal); font-size:14.5px; color:var(--teal-deep); border-radius:0 8px 8px 0; }
  .panel{ background:var(--panel); border:1px solid var(--line); border-radius:var(--radius); padding:30px; margin-bottom:22px; }
  .panel h2{ font-size:20px; margin:0 0 6px; }
  .panel .sub{ font-size:14.5px; color:var(--ink-soft); margin:0 0 22px; }
  .gejala-item{ display:flex; align-items:flex-start; gap:14px; padding:16px 0; border-bottom:1px solid var(--line); }
  .gejala-item:last-child{ border-bottom:none; }
  .gejala-item input[type=checkbox]{ margin-top:3px; width:22px; height:22px; accent-color:var(--teal); cursor:pointer; }
  .gejala-item label{ cursor:pointer; font-size:16px; }
  .btn{ appearance:none; border:none; cursor:pointer; font-family:'Inter',sans-serif; font-weight:700; font-size:16.5px; padding:16px 26px; border-radius:12px; }
  .btn-primary{ background:var(--teal); color:#fff; width:100%; }
  .btn-outline{ background:transparent; border:2px solid var(--line); color:var(--ink-soft); width:100%; margin-top:12px; }
  .btn-sm{ padding:9px 16px; font-size:13.5px; width:auto; }
  .btn-forward{ background:var(--coral); color:#fff; }
  .form-row{ margin-bottom:16px; }
  .form-row label{ display:block; font-size:14.5px; font-weight:700; color:var(--ink-soft); margin-bottom:7px; }
  .form-row input:not([type=checkbox]):not([type=radio]), .form-row textarea, .form-row select{ width:100%; padding:14px; border:2px solid var(--line); border-radius:10px; font-family:'Inter',sans-serif; font-size:16px; background:var(--mist); }
  .form-row input[type=checkbox], .form-row input[type=radio]{ width:20px; height:20px; padding:0; border:none; accent-color:var(--teal); cursor:pointer; flex-shrink:0; }
  .form-row input:focus, .form-row textarea:focus, .form-row select:focus{ outline:3px solid var(--teal); outline-offset:1px; background:#fff; }
  .form-row select{ appearance:none; -webkit-appearance:none; background-image:url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233E5850' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e"); background-repeat:no-repeat; background-position:right 14px center; background-size:20px; padding-right:44px; cursor:pointer; }
  .hasil-card{ border:2px solid var(--line); border-radius:14px; padding:20px; margin-bottom:14px; position:relative; }
  .hasil-card.utama{ border-color:var(--teal); background:var(--teal-tint); }
  .hasil-top{ display:flex; justify-content:space-between; align-items:baseline; margin-bottom:9px; }
  .hasil-top .nama{ font-weight:700; font-size:16.5px; }
  .hasil-top .persen{ font-family:'Space Grotesk',sans-serif; font-weight:700; font-size:22px; color:var(--teal-deep); }
  .meter{ height:9px; background:var(--line); border-radius:999px; overflow:hidden; }
  .meter-fill{ height:100%; border-radius:999px; background:var(--teal); }
  .tag-utama{ position:absolute; top:-10px; right:16px; background:var(--coral); color:#fff; font-size:11.5px; font-weight:700; padding:4px 12px; border-radius:999px; }
  .gejala-tags{ display:flex; flex-wrap:wrap; gap:8px; margin-bottom:10px; }
  .gejala-tag{ background:var(--teal-tint); color:var(--teal-deep); font-size:13px; font-weight:600; padding:7px 13px; border-radius:999px; }
  .section-title{ font-size:13.5px; font-weight:700; color:var(--ink-soft); margin:20px 0 12px; text-transform:uppercase; letter-spacing:.04em; }
  .status-pill{ font-size:12px; font-weight:700; padding:5px 11px; border-radius:999px; white-space:nowrap; }
  .status-menunggu_konsultasi{ background:var(--amber-tint); color:#412402; }
  .status-divalidasi{ background:var(--teal-tint); color:var(--teal-deep); }
  .status-selesai{ background:var(--gray-tint); color:#2C2C2A; }
  .queue-item{ background:var(--panel); border:2px solid var(--line); border-radius:14px; padding:18px 20px; margin-bottom:12px; display:flex; align-items:center; justify-content:space-between; text-decoration:none; color:inherit; }
  .queue-item .qi-nama{ font-weight:700; font-size:15.5px; display:block; }
  .queue-item .qi-meta{ font-size:13px; color:var(--ink-soft); }
  .search-panel{ background:var(--panel); border:2px solid var(--line); border-radius:var(--radius); padding:22px; margin-bottom:24px; display:flex; gap:10px; }
  .search-panel input{ flex:1; padding:14px; border:2px solid var(--line); border-radius:10px; font-size:15px; }
  .info-grid{ display:grid; grid-template-columns:1fr 1fr; gap:14px 20px; margin-bottom:8px; padding:18px; background:var(--mist); border-radius:12px; }
  .info-grid .label{ font-size:12px; color:var(--ink-soft); text-transform:uppercase; margin-bottom:3px; display:block; }
  .info-grid .value{ font-size:15px; font-weight:700; }
  .back-link{ font-size:14px; color:var(--teal-deep); text-decoration:none; font-weight:700; margin-bottom:18px; display:inline-block; }
  .dummy-note{ font-size:12px; color:var(--ink-soft); text-align:center; margin-top:28px; opacity:.75; }
  .confirm-box{ text-align:center; padding:36px 20px; }
  .confirm-icon{ width:58px; height:58px; border-radius:50%; background:var(--teal); color:#fff; display:flex; align-items:center; justify-content:center; margin:0 auto 18px; font-size:26px; }

  /* Paginasi (Laravel default) */
  nav[role="navigation"] a, nav[role="navigation"] span{ font-family:'Inter',sans-serif !important; font-size:13.5px !important; }
  nav[role="navigation"] a{ padding:8px 13px; border:1px solid var(--line); border-radius:8px; color:var(--teal-deep) !important; text-decoration:none !important; background:#fff; }
  nav[role="navigation"] a:hover{ background:var(--teal-tint); }
  nav[role="navigation"] span[aria-current="page"] span{ padding:8px 13px; border-radius:8px; background:var(--teal); color:#fff !important; font-weight:700; }
  nav[role="navigation"] span:not([aria-current]) span, nav[role="navigation"] > div > span[aria-disabled]{ padding:8px 13px; color:var(--ink-soft) !important; }

  /* ===== Penyesuaian layar kecil (HP) ===== */
  @media (max-width: 640px){
    header.topbar{ padding:16px; }
    header.topbar > div:last-child{ font-size:13px; gap:10px; width:100%; justify-content:flex-start; }
    main{ padding:26px 16px 90px; }
    .hero h1{ font-size:25px; }
    .hero p{ font-size:15px; }
    .panel{ padding:20px; }
    .info-grid{ grid-template-columns:1fr; }
    .queue-item{ flex-wrap:wrap; gap:8px; }
    .search-panel{ flex-wrap:wrap; }
    .search-panel input{ min-width:100%; }
  }
</style>
</head>
<body>
  @yield('content')
</body>
</html>
