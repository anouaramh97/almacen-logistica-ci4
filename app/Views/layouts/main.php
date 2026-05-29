<?php // Layout principal: estructura comun, menu lateral, barra superior y estilos compartidos.
$uri = trim(service('uri')->getPath(), '/');
$currentUser = $currentUser ?? null;
$role = $currentUser['role_name'] ?? null;
$title = $title ?? 'Almacen Logistica CI4';
$isGuestPage = in_array($uri, ['', 'login', 'register'], true);
$unreadMessageCount = $currentUser ? model(\App\Models\MessageModel::class)->countUnreadForUser((int) $currentUser['id']) : 0;
$initials = '';
if (! empty($currentUser['name'])) {
    foreach (preg_split('/\s+/', trim($currentUser['name'])) as $part) {
        $initials .= strtoupper(substr($part, 0, 1));
    }
    $initials = substr($initials, 0, 2);
}
$navItems = [];
if ($role === 'administrador') {
    $navItems = [
        ['label' => lang('App.users'), 'path' => 'admin/users', 'icon' => 'U'],
        ['label' => lang('App.products'), 'path' => 'admin/products', 'icon' => 'P'],
        ['label' => lang('App.categories'), 'path' => 'admin/categories', 'icon' => 'C'],
        ['label' => lang('App.warehouses'), 'path' => 'admin/warehouses', 'icon' => 'A'],
        ['label' => lang('App.stock'), 'path' => 'admin/stocks', 'icon' => 'S'],
        ['label' => lang('App.orders'), 'path' => 'admin/orders', 'icon' => 'O'],
        ['label' => lang('App.invoices'), 'path' => 'admin/invoices', 'icon' => 'F'],
        ['label' => lang('App.routes'), 'path' => 'admin/routes', 'icon' => 'R'],
        ['label' => lang('App.messages'), 'path' => 'messages', 'icon' => 'M', 'badge' => $unreadMessageCount],
        ['label' => lang('App.profile'), 'path' => 'profile', 'icon' => 'PR'],
    ];
} elseif ($role === 'cliente') {
    $navItems = [
        ['label' => lang('App.orders'), 'path' => 'client/orders', 'icon' => 'P'],
        ['label' => 'Nuevo pedido', 'path' => 'client/orders/create', 'icon' => 'N'],
        ['label' => 'Mis entregas', 'path' => 'client/deliveries', 'icon' => 'E'],
        ['label' => lang('App.messages'), 'path' => 'messages', 'icon' => 'M', 'badge' => $unreadMessageCount],
        ['label' => lang('App.profile'), 'path' => 'profile', 'icon' => 'PR'],
    ];
} elseif ($role === 'conductor') {
    $navItems = [
        ['label' => lang('App.dashboard'), 'path' => 'driver/dashboard', 'icon' => 'D'],
        ['label' => 'Mis rutas', 'path' => 'driver/routes', 'icon' => 'R'],
        ['label' => lang('App.messages'), 'path' => 'messages', 'icon' => 'M', 'badge' => $unreadMessageCount],
        ['label' => lang('App.profile'), 'path' => 'profile', 'icon' => 'PR'],
    ];
} elseif ($role === 'logistica') {
    $navItems = [
        ['label' => lang('App.dashboard'), 'path' => 'logistics/dashboard', 'icon' => 'D'],
        ['label' => lang('App.routes'), 'path' => 'logistics/routes', 'icon' => 'R'],
        ['label' => 'Nueva ruta', 'path' => 'logistics/routes/create', 'icon' => 'N'],
        ['label' => lang('App.orders'), 'path' => 'logistics/orders', 'icon' => 'P'],
        ['label' => lang('App.messages'), 'path' => 'messages', 'icon' => 'M', 'badge' => $unreadMessageCount],
        ['label' => lang('App.profile'), 'path' => 'profile', 'icon' => 'PR'],
    ];
}
?>
<!doctype html>
<html lang="<?= esc($currentLocale ?? 'es') ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('media/brand/logisticaprologo.png') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('media/brand/logisticaprologo.png') ?>">
    <style>
        :root { --bg:#f4f7fb; --surface:rgba(255,255,255,.96); --line:rgba(15,23,42,.08); --ink:#142033; --muted:#6e7c90; --brand:#0f62fe; --nav-start:#1d2733; --nav-end:#101922; --danger:#b42318; --shadow:0 20px 48px rgba(15,23,42,.08); --font:"Segoe UI","Trebuchet MS",Arial,sans-serif; }
        * { box-sizing:border-box; } html { overflow-x:hidden; } body { margin:0; overflow-x:hidden; font-family:var(--font); color:var(--ink); background:radial-gradient(circle at top right, rgba(15,98,254,.08), transparent 24%), radial-gradient(circle at left bottom, rgba(67,187,255,.12), transparent 28%), linear-gradient(180deg,#eef4ff 0%,#f6f8fc 38%,#ffffff 100%); } a { color:inherit; text-decoration:none; } img { max-width:100%; height:auto; }
        .shell { min-height:100vh; } .auth-header { max-width:1120px; margin:0 auto; padding:1.25rem 1rem 0; display:flex; justify-content:space-between; align-items:center; gap:1rem; }
        .auth-brand { display:inline-flex; align-items:center; gap:.85rem; font-weight:800; } .auth-mark,.brand-mark,.nav-badge,.avatar-fallback { display:grid; place-items:center; } .auth-mark,.brand-mark { width:44px; height:44px; border-radius:14px; background:#07182d; color:#fff; box-shadow:0 14px 28px rgba(15,98,254,.24); overflow:hidden; } .auth-mark img,.brand-mark img { width:100%; height:100%; object-fit:cover; display:block; }
        .auth-nav,.top-links,.hero-actions,.toolbar { display:flex; align-items:center; gap:.8rem; flex-wrap:wrap; } .btn { display:inline-flex; align-items:center; justify-content:center; gap:.5rem; border:1px solid transparent; border-radius:999px; padding:.85rem 1.2rem; font-weight:700; cursor:pointer; transition:transform .18s ease, box-shadow .18s ease, background .18s ease; } .btn:hover { transform:translateY(-1px); } .btn-primary { background:var(--brand); color:#fff; box-shadow:0 14px 30px rgba(15,98,254,.24); } .btn-outline { background:rgba(255,255,255,.86); color:var(--brand); border-color:rgba(15,98,254,.18); } .btn-danger { background:rgba(255,255,255,.92); border-color:rgba(180,35,24,.14); color:var(--danger); }
        .app-shell { min-height:100vh; display:grid; grid-template-columns:260px 1fr; } .sidebar { background:linear-gradient(180deg,var(--nav-start) 0%,var(--nav-end) 100%); color:#dce4ec; padding:1.25rem 1rem; display:flex; flex-direction:column; gap:1rem; box-shadow:16px 0 40px rgba(15,23,42,.14); }
        .brand { display:flex; align-items:center; gap:.85rem; padding:.4rem .35rem 1rem; border-bottom:1px solid rgba(255,255,255,.08); } .brand strong,.sidebar-user strong,.top-user strong { display:block; color:#fff; } .brand span,.sidebar-user span,.top-user span,.muted { color:var(--muted); }
        .sidebar-user { display:flex; align-items:center; gap:.75rem; padding:.9rem; border-radius:18px; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.06); } .avatar-fallback { width:46px; height:46px; border-radius:50%; background:linear-gradient(135deg, rgba(49,176,255,.85), rgba(15,98,254,.92)); color:#fff; font-weight:800; overflow:hidden; }
        .avatar-fallback img { width:100%; height:100%; object-fit:cover; display:block; }
        .nav-list { display:grid; gap:.35rem; } .nav-link { display:flex; align-items:center; gap:.8rem; padding:.86rem .95rem; border-radius:16px; color:#cfdae6; font-weight:700; transition:background .18s ease, transform .18s ease, color .18s ease; } .nav-link:hover,.nav-link.is-active { background:rgba(255,255,255,.1); color:#fff; transform:translateX(2px); }
        .nav-badge { width:24px; height:24px; border-radius:8px; background:rgba(255,255,255,.08); font-size:.72rem; font-weight:800; } .nav-alert { margin-left:auto; min-width:22px; height:22px; padding:0 .4rem; border-radius:999px; display:inline-flex; align-items:center; justify-content:center; background:#dc2626; color:#fff; font-size:.72rem; font-weight:900; box-shadow:0 8px 18px rgba(220,38,38,.28); } .sidebar-footer { margin-top:auto; } .logout-form { margin:0; }
        .content { min-width:0; padding:1rem; } .topbar { display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1rem; padding:1rem 1.2rem; border-radius:22px; background:var(--surface); border:1px solid var(--line); box-shadow:var(--shadow); }
        .top-intro h1,.section-title,.auth-card h2,.hero-title,.dashboard-hero h2 { margin:0; color:var(--ink); } .top-intro p,.dashboard-hero p,.hero-text,.helper,.section-copy { color:var(--muted); }
        .top-user { display:flex; align-items:center; gap:.85rem; min-width:0; position:relative; } .top-user-menu { position:relative; } .top-user-trigger { display:flex; align-items:center; gap:.85rem; background:transparent; border:none; padding:0; cursor:pointer; color:inherit; font:inherit; } .top-user-dropdown { position:absolute; top:calc(100% + .75rem); right:0; min-width:220px; padding:.55rem; border-radius:18px; background:rgba(255,255,255,.98); border:1px solid rgba(15,23,42,.08); box-shadow:0 18px 42px rgba(15,23,42,.12); display:none; z-index:30; } .top-user-menu.is-open .top-user-dropdown { display:block; } .top-user-link { display:flex; align-items:center; width:100%; padding:.85rem 1rem; border:none; background:transparent; border-radius:14px; color:var(--ink); text-align:left; font:inherit; cursor:pointer; } .top-user-link:hover { background:#f4f7fb; } .top-user-link.is-danger { color:var(--danger); } .top-user-link.is-danger:hover { background:#fff1f2; } .page-body,.home-shell,.auth-shell { width:min(1180px, 100%); margin:0 auto; padding-bottom:2rem; } .guest-shell { padding:0 0 2.5rem; }
        .hero { position:relative; overflow:hidden; border-radius:32px; padding:3rem; background:radial-gradient(circle at top right, rgba(106,227,255,.22), transparent 22%), radial-gradient(circle at bottom left, rgba(15,98,254,.18), transparent 28%), linear-gradient(135deg,#07182d 0%,#0a2748 52%,#123d6b 100%); color:#fff; box-shadow:0 34px 70px rgba(8,25,46,.2); } .hero::after { content:""; position:absolute; inset:auto -80px -120px auto; width:280px; height:280px; border-radius:50%; background:rgba(255,255,255,.07); }
        .hero-kicker { display:inline-flex; align-items:center; gap:.5rem; padding:.45rem .85rem; border-radius:999px; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.14); font-size:.82rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; margin-bottom:1rem; }
        .hero-title { max-width:760px; font-size:clamp(2.5rem,5vw,4.8rem); line-height:.98; font-weight:900; letter-spacing:-.04em; color:#fff; } .hero-text { max-width:620px; margin:1.2rem 0 0; color:rgba(255,255,255,.82); font-size:1.05rem; line-height:1.75; } .hero-actions { margin-top:1.7rem; } .hero-actions .btn-outline { background:rgba(255,255,255,.08); color:#fff; border-color:rgba(255,255,255,.14); }
        .home-grid,.dashboard-grid,.auth-grid,.panel-grid,.grid-2,.grid-3,.grid-4,.quick-grid { display:grid; gap:1rem; } .home-grid,.grid-3 { grid-template-columns:repeat(3,minmax(0,1fr)); } .grid-2,.auth-grid,.panel-grid,.dashboard-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } .grid-4,.quick-grid { grid-template-columns:repeat(4,minmax(0,1fr)); }
        .feature-card,.summary-card,.table-card,.note-card,.auth-card,.form-card { background:rgba(255,255,255,.96); border:1px solid rgba(15,23,42,.08); border-radius:24px; box-shadow:0 18px 42px rgba(15,23,42,.08); padding:1.35rem; } .feature-card strong { display:block; margin-bottom:.45rem; color:var(--ink); } .feature-card p,.helper,.section-copy { line-height:1.65; }
        .strip { display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-top:1rem; padding:1rem 1.15rem; border-radius:22px; background:rgba(255,255,255,.82); border:1px solid rgba(255,255,255,.55); box-shadow:0 16px 36px rgba(15,23,42,.06); }
        .dashboard-hero { padding:1.5rem; border-radius:28px; background:radial-gradient(circle at top right, rgba(106,227,255,.16), transparent 22%), linear-gradient(135deg,#081b33 0%,#103760 52%,#194f85 100%); color:#fff; } .dashboard-hero h2,.dashboard-hero p,.dashboard-hero .hero-kicker { color:#fff; } .dashboard-hero p { max-width:700px; color:rgba(255,255,255,.8); }
        .summary-strip { margin-top:1.25rem; display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:.85rem; } .metric { padding:1rem; border-radius:20px; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.08); } .metric span { display:block; font-size:.8rem; text-transform:uppercase; letter-spacing:.05em; color:rgba(255,255,255,.72); margin-bottom:.45rem; } .metric strong { font-size:2rem; line-height:1; color:#fff; }
        .summary-line { display:flex; justify-content:space-between; align-items:center; gap:1rem; padding:.95rem 1rem; border:1px solid var(--line); border-radius:16px; background:#fbfcfe; } .summary-line span { font-size:.84rem; text-transform:uppercase; letter-spacing:.05em; font-weight:700; color:var(--muted); } .summary-line strong { font-size:1.5rem; line-height:1; color:var(--ink); }
        .table-wrap { width:100%; overflow:auto hidden; -webkit-overflow-scrolling:touch; } table { width:100%; min-width:720px; border-collapse:collapse; } th,td { padding:.9rem .75rem; text-align:left; border-bottom:1px solid rgba(15,23,42,.08); } th { font-size:.76rem; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); }
        .pill { display:inline-flex; align-items:center; padding:.34rem .7rem; border-radius:999px; font-size:.8rem; font-weight:800; background:rgba(15,98,254,.12); color:var(--brand); } .pill.is-warning { background:rgba(255,177,66,.18); color:#925200; } .pill.is-success-soft { background:rgba(110,231,183,.18); color:#0f8a5f; } .pill.is-success-dark { background:rgba(20,120,93,.18); color:#0b5d48; } .pill.is-info { background:rgba(56,189,248,.16); color:#0c6e91; } .pill.is-primary { background:rgba(15,98,254,.12); color:#0f62fe; } .pill.is-danger { background:rgba(215,76,88,.14); color:#a41e2b; } .note-card { background:linear-gradient(135deg,#fff8dc 0%,#ffeab1 100%); border-color:rgba(186,132,18,.18); color:#7a5100; }
        .auth-shell { padding:1.1rem 1rem 2.5rem; } .auth-showcase { position:relative; overflow:hidden; border-radius:30px; padding:2.2rem; background:radial-gradient(circle at top left, rgba(110,227,255,.2), transparent 22%), linear-gradient(135deg,#07182d 0%,#0a2748 55%,#143c68 100%); color:#fff; box-shadow:0 28px 64px rgba(8,25,46,.2); }
        .auth-showcase h2,.auth-showcase p { color:#fff; } .auth-showcase ul { margin:1.25rem 0 0; padding-left:1.1rem; color:rgba(255,255,255,.82); line-height:1.8; } .auth-card { padding:2rem; }
        .field { margin-bottom:1rem; } .field label { display:block; margin-bottom:.45rem; font-weight:700; color:#1d2733; } .field input,.field select,.field textarea, .toolbar input, .toolbar select { width:100%; max-width:100%; padding:.9rem 1rem; border-radius:16px; border:1px solid rgba(15,23,42,.1); background:#f8fbff; outline:none; transition:border-color .18s ease, box-shadow .18s ease; } .field input:focus,.field select:focus,.field textarea:focus,.toolbar input:focus,.toolbar select:focus { border-color:rgba(15,98,254,.34); box-shadow:0 0 0 4px rgba(15,98,254,.08); }
        .password-field { position:relative; } .password-field input { padding-right:3.6rem; } .password-toggle { position:absolute; top:50%; right:.45rem; transform:translateY(-50%); display:inline-flex; align-items:center; justify-content:center; width:2.55rem; height:2.55rem; padding:0; border:none; border-radius:12px; background:rgba(15,98,254,.1); color:var(--brand); cursor:pointer; transition:background .18s ease, color .18s ease; } .password-toggle:hover,.password-toggle:focus { background:rgba(15,98,254,.16); color:#08357e; outline:none; } .password-toggle:focus-visible { box-shadow:0 0 0 3px rgba(15,98,254,.18); } .password-icon { width:1.25rem; height:1.25rem; } .password-icon-hide { display:none; } .password-toggle.is-visible .password-icon-show { display:none; } .password-toggle.is-visible .password-icon-hide { display:block; }
        .flash { padding:.95rem 1rem; border-radius:18px; margin-bottom:1rem; border:1px solid transparent; } .flash-error { background:#fff1f2; color:#9f1239; border-color:#fecdd3; } .flash-success { background:#ecfdf3; color:#166534; border-color:#bbf7d0; } .empty { padding:1rem; border-radius:16px; background:#f8fbff; color:var(--muted); }
        .mobile-brand { display:none; align-items:center; justify-content:space-between; gap:1rem; padding:.95rem 1rem; margin-bottom:1rem; border-radius:18px; background:rgba(255,255,255,.96); border:1px solid var(--line); box-shadow:var(--shadow); }
        .mobile-brand-actions { display:flex; align-items:center; gap:.65rem; }
        .mobile-menu-toggle { display:inline-flex; align-items:center; justify-content:center; gap:.5rem; min-width:48px; height:48px; padding:0 .95rem; border:none; border-radius:16px; background:linear-gradient(135deg,#0f62fe 0%,#1649c8 100%); color:#fff; font-weight:800; box-shadow:0 14px 26px rgba(15,98,254,.22); cursor:pointer; }
        .mobile-menu-toggle span { font-size:.92rem; }
        .mobile-menu-backdrop { position:fixed; inset:0; background:rgba(7,24,45,.38); backdrop-filter:blur(8px); opacity:0; pointer-events:none; transition:opacity .2s ease; z-index:40; }
        .mobile-drawer { position:fixed; left:12px; right:12px; top:12px; max-height:calc(100vh - 24px); overflow-y:auto; padding:1rem; border-radius:24px; background:linear-gradient(180deg,#182331 0%,#0f1823 100%); color:#dbe5ef; box-shadow:0 28px 60px rgba(15,23,42,.28); transform:translateY(-16px) scale(.98); opacity:0; pointer-events:none; transition:transform .22s ease, opacity .22s ease; z-index:50; scrollbar-width:thin; scrollbar-color:rgba(255,255,255,.28) rgba(255,255,255,.08); }
        .mobile-drawer::-webkit-scrollbar { width:8px; }
        .mobile-drawer::-webkit-scrollbar-track { background:rgba(255,255,255,.06); border-radius:999px; }
        .mobile-drawer::-webkit-scrollbar-thumb { background:rgba(255,255,255,.24); border-radius:999px; }
        .mobile-drawer-head { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:1rem; padding-bottom:1rem; border-bottom:1px solid rgba(255,255,255,.08); }
        .mobile-drawer-user { display:flex; align-items:center; gap:.8rem; min-width:0; }
        .mobile-drawer-user strong { display:block; color:#fff; }
        .mobile-drawer-close { display:inline-flex; align-items:center; justify-content:center; width:42px; height:42px; border:none; border-radius:14px; background:rgba(255,255,255,.08); color:#fff; font-size:1.25rem; cursor:pointer; }
        .mobile-drawer-grid { display:grid; grid-template-columns:1fr 1fr; gap:.7rem; margin-bottom:1rem; }
        .mobile-drawer .nav-link { min-height:72px; align-items:flex-start; border:1px solid rgba(255,255,255,.08); background:rgba(255,255,255,.05); color:#dbe5ef; box-shadow:none; }
        .mobile-drawer .nav-link.is-active { background:rgba(15,98,254,.18); border-color:rgba(79,139,255,.35); color:#fff; transform:none; }
        .mobile-drawer .nav-badge { background:rgba(255,255,255,.1); }
        .mobile-drawer-footer { display:grid; gap:.75rem; }
        .mobile-drawer-footer .btn-danger { width:100%; background:rgba(255,255,255,.06); color:#ffd6d0; border-color:rgba(255,255,255,.08); }
        body.mobile-menu-open .mobile-menu-backdrop { opacity:1; pointer-events:auto; }
        body.mobile-menu-open .mobile-drawer { opacity:1; transform:translateY(0) scale(1); pointer-events:auto; }
        @media (max-width:1100px) { .summary-strip,.grid-4,.quick-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
        @media (max-width:980px) { .app-shell { grid-template-columns:1fr; } .sidebar { display:none; } .mobile-brand { display:flex; } .topbar { flex-direction:column; align-items:stretch; } .toolbar, .auth-nav { width:100%; justify-content:flex-start; } .toolbar form { width:100%; } .dashboard-grid,.grid-2,.auth-grid,.panel-grid,.home-grid,.grid-3,.grid-4,.quick-grid,.summary-strip { grid-template-columns:1fr; } }
        @media (max-width:640px) { .page-body,.home-shell,.auth-shell { width:min(100% - 24px,1180px); } .auth-header,.strip,.summary-line,.top-user { flex-direction:column; align-items:stretch; } .mobile-brand { align-items:center; flex-direction:row; } .hero,.auth-showcase,.auth-card,.dashboard-hero,.summary-card,.table-card,.form-card,.note-card,.feature-card { padding:1.1rem; border-radius:20px; } .hero-title { font-size:2.35rem; } .hero-actions .btn,.auth-nav .btn,.toolbar .btn,.btn-danger,.btn-primary,.btn-outline { width:100%; } .auth-nav form,.toolbar form { width:100%; } .top-intro h1 { font-size:1.5rem; } .mobile-drawer { left:10px; right:10px; top:10px; padding:.9rem; border-radius:20px; } .mobile-drawer-grid { grid-template-columns:1fr; } .mobile-brand-actions { width:auto; } .mobile-menu-toggle span { display:none; } .mobile-menu-toggle { width:48px; padding:0; } .table-wrap.is-mobile-cards { overflow:visible; } .table-wrap.is-mobile-cards table, .table-wrap.is-mobile-cards tbody, .table-wrap.is-mobile-cards tr, .table-wrap.is-mobile-cards td { display:block; width:100%; min-width:0; } .table-wrap.is-mobile-cards table { min-width:0; } .table-wrap.is-mobile-cards thead { display:none; } .table-wrap.is-mobile-cards tr { margin-bottom:.9rem; padding:1rem; border:1px solid rgba(15,23,42,.08); border-radius:18px; background:#fbfcfe; box-shadow:0 10px 24px rgba(15,23,42,.05); } .table-wrap.is-mobile-cards td { padding:.7rem 0; border-bottom:1px solid rgba(15,23,42,.08); text-align:left; } .table-wrap.is-mobile-cards td:last-child { border-bottom:none; padding-bottom:0; } .table-wrap.is-mobile-cards td::before { content:attr(data-label); display:block; margin-bottom:.28rem; font-size:.72rem; font-weight:800; letter-spacing:.06em; text-transform:uppercase; color:var(--muted); } .table-wrap.is-mobile-cards td[colspan]::before { content:none; } .table-wrap.is-mobile-cards td .toolbar { width:100%; flex-direction:column; align-items:stretch; } }
    </style>
</head>
<body>
<div class="shell">
<?php if ($currentUser): ?>
    <div class="app-shell">
        <aside class="sidebar">
            <a href="<?= site_url('/') ?>" class="brand"><div class="brand-mark"><img src="<?= base_url('media/brand/logisticaprologo.png') ?>" alt="<?= esc(lang('App.brand_name')) ?>"></div><div><strong><?= esc(lang('App.brand_name')) ?></strong><span><?= esc($role ? role_label($role) : lang('App.operational_panel')) ?></span></div></a>
            <div class="sidebar-user"><div class="avatar-fallback"><?php $avatar = avatar_url($currentUser['avatar_path'] ?? null, $currentUser['name'] ?? 'Usuario'); ?><img src="<?= esc($avatar) ?>" alt="<?= esc($currentUser['name'] ?? 'Usuario') ?>"></div><div><strong><?= esc($currentUser['name'] ?? 'Usuario') ?></strong><span><?= esc($currentUser['email'] ?? '') ?></span></div></div>
            <nav class="nav-list"><?php foreach ($navItems as $item): ?><?php $active = $uri === $item['path'] || str_starts_with($uri, $item['path'] . '/'); $badge = (int) ($item['badge'] ?? 0); ?><a class="nav-link <?= $active ? 'is-active' : '' ?>" href="<?= site_url($item['path']) ?>"><span class="nav-badge"><?= esc($item['icon']) ?></span><span><?= esc($item['label']) ?></span><?php if ($badge > 0): ?><span class="nav-alert"><?= esc($badge > 99 ? '99+' : (string) $badge) ?></span><?php endif; ?></a><?php endforeach; ?></nav>
            <div class="sidebar-footer"><form method="post" action="<?= site_url('logout') ?>" class="logout-form"><?= csrf_field() ?><button class="btn btn-danger" type="submit"><?= esc(lang('App.logout')) ?></button></form></div>
        </aside>
        <main class="content">
            <div class="mobile-menu-backdrop" data-mobile-menu-close></div>
            <div class="mobile-drawer" id="mobileDrawer">
                <div class="mobile-drawer-head">
                    <div class="mobile-drawer-user">
                        <div class="avatar-fallback"><img src="<?= esc($avatar) ?>" alt="<?= esc($currentUser['name'] ?? 'Usuario') ?>"></div>
                        <div><strong><?= esc($currentUser['name'] ?? 'Usuario') ?></strong><span><?= esc(role_label($currentUser['role_name'] ?? '')) ?></span></div>
                    </div>
                    <button type="button" class="mobile-drawer-close" data-mobile-menu-close aria-label="Cerrar menu">×</button>
                </div>
                <?php if ($navItems): ?>
                    <nav class="mobile-drawer-grid">
                        <?php foreach ($navItems as $item): ?>
                            <?php $active = $uri === $item['path'] || str_starts_with($uri, $item['path'] . '/'); ?>
                            <a class="nav-link <?= $active ? 'is-active' : '' ?>" href="<?= site_url($item['path']) ?>">
                                <span class="nav-badge"><?= esc($item['icon']) ?></span>
                                <span><?= esc($item['label']) ?></span>
                                <?php $badge = (int) ($item['badge'] ?? 0); ?>
                                <?php if ($badge > 0): ?><span class="nav-alert"><?= esc($badge > 99 ? '99+' : (string) $badge) ?></span><?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                <?php endif; ?>
                <div class="mobile-drawer-footer">
                    <form method="post" action="<?= site_url('language/update') ?>" class="toolbar">
                        <?= csrf_field() ?>
                        <select name="locale" onchange="this.form.submit()">
                            <option value="es" <?= ($currentLocale ?? 'es') === 'es' ? 'selected' : '' ?>><?= esc(lang('App.spanish')) ?></option>
                            <option value="en" <?= ($currentLocale ?? 'es') === 'en' ? 'selected' : '' ?>><?= esc(lang('App.english')) ?></option>
                        </select>
                    </form>
                    <form method="post" action="<?= site_url('logout') ?>" class="logout-form">
                        <?= csrf_field() ?>
                        <button class="btn btn-danger" type="submit"><?= esc(lang('App.logout')) ?></button>
                    </form>
                </div>
            </div>
            <div class="mobile-brand"><a href="<?= site_url('/') ?>" class="auth-brand"><div class="auth-mark"><img src="<?= base_url('media/brand/logisticaprologo.png') ?>" alt="<?= esc(lang('App.brand_name')) ?>"></div><div><strong><?= esc(lang('App.brand_name')) ?></strong><div class="muted"><?= esc(lang('App.operational_panel')) ?></div></div></a><div class="mobile-brand-actions"><button type="button" class="mobile-menu-toggle" data-mobile-menu-open aria-controls="mobileDrawer" aria-expanded="false">☰ <span>Menu</span></button></div></div>
            <div class="topbar"><div class="top-intro"><h1><?= esc(lang('App.role_operational', ['role' => $role ? role_label($role) : 'Panel'])) ?></h1><p><?= esc(lang('App.layout_subtitle')) ?></p></div><div class="toolbar"><form method="post" action="<?= site_url('language/update') ?>" class="toolbar"><?= csrf_field() ?><select name="locale" onchange="this.form.submit()" style="padding:.75rem .9rem; border-radius:14px; border:1px solid rgba(15,23,42,.1); background:#f8fbff;"><option value="es" <?= ($currentLocale ?? 'es') === 'es' ? 'selected' : '' ?>><?= esc(lang('App.spanish')) ?></option><option value="en" <?= ($currentLocale ?? 'es') === 'en' ? 'selected' : '' ?>><?= esc(lang('App.english')) ?></option></select></form><div class="top-user-menu" data-user-menu><button type="button" class="top-user-trigger" data-user-menu-toggle aria-expanded="false"><div class="top-user"><div><strong><?= esc($currentUser['name'] ?? 'Usuario') ?></strong><span><?= esc(role_label($currentUser['role_name'] ?? '')) ?></span></div><div class="avatar-fallback"><img src="<?= esc($avatar) ?>" alt="<?= esc($currentUser['name'] ?? 'Usuario') ?>"></div></div></button><div class="top-user-dropdown"><a href="<?= site_url('profile') ?>" class="top-user-link">Perfil</a><form method="post" action="<?= site_url('logout') ?>" class="logout-form"><?= csrf_field() ?><button type="submit" class="top-user-link is-danger">Cerrar sesión</button></form></div></div></div></div>
            <div class="page-body"><?php if (session('error')): ?><div class="flash flash-error"><?= esc(session('error')) ?></div><?php endif; ?><?php if (session('success')): ?><div class="flash flash-success"><?= esc(session('success')) ?></div><?php endif; ?><?= $this->renderSection('content') ?></div>
        </main>
    </div>
<?php else: ?>
    <div class="guest-shell">
        <header class="auth-header"><a href="<?= site_url('/') ?>" class="auth-brand"><div class="auth-mark"><img src="<?= base_url('media/brand/logisticaprologo.png') ?>" alt="<?= esc(lang('App.brand_name')) ?>"></div><div><strong><?= esc(lang('App.brand_name')) ?></strong><div class="muted"><?= esc(lang('App.brand_tagline')) ?></div></div></a><nav class="auth-nav"><form method="post" action="<?= site_url('language/update') ?>" class="toolbar"><?= csrf_field() ?><select name="locale" onchange="this.form.submit()" style="padding:.75rem .9rem; border-radius:14px; border:1px solid rgba(15,23,42,.1); background:#f8fbff;"><option value="es" <?= ($currentLocale ?? 'es') === 'es' ? 'selected' : '' ?>><?= esc(lang('App.spanish')) ?></option><option value="en" <?= ($currentLocale ?? 'es') === 'en' ? 'selected' : '' ?>><?= esc(lang('App.english')) ?></option></select></form><?php if ($uri !== 'login'): ?><a href="<?= site_url('login') ?>" class="btn btn-outline"><?= esc(lang('App.login')) ?></a><?php endif; ?><?php if ($uri !== 'register'): ?><a href="<?= site_url('register') ?>" class="btn btn-primary"><?= esc(lang('App.register')) ?></a><?php endif; ?></nav></header>
        <div class="auth-shell <?= $isGuestPage ? 'is-compact' : '' ?>"><?php if (session('error')): ?><div class="flash flash-error"><?= esc(session('error')) ?></div><?php endif; ?><?php if (session('success')): ?><div class="flash flash-success"><?= esc(session('success')) ?></div><?php endif; ?><?= $this->renderSection('content') ?></div>
    </div>
<?php endif; ?>
</div>
<script>
    (() => {
        const body = document.body;
        const openButton = document.querySelector('[data-mobile-menu-open]');
        const closeButtons = document.querySelectorAll('[data-mobile-menu-close]');
        if (!openButton) return;
        const closeMenu = () => {
            body.classList.remove('mobile-menu-open');
            openButton.setAttribute('aria-expanded', 'false');
        };
        openButton.addEventListener('click', () => {
            body.classList.add('mobile-menu-open');
            openButton.setAttribute('aria-expanded', 'true');
        });
        closeButtons.forEach((button) => button.addEventListener('click', closeMenu));
        document.querySelectorAll('.mobile-drawer .nav-link').forEach((link) => link.addEventListener('click', closeMenu));
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeMenu();
        });
    })();

    (() => {
        const wraps = document.querySelectorAll('.table-wrap');
        wraps.forEach((wrap) => {
            const table = wrap.querySelector('table');
            if (!table) return;
            const headers = Array.from(table.querySelectorAll('thead th')).map((th) => th.textContent.trim());
            if (!headers.length) return;
            wrap.classList.add('is-mobile-cards');
            table.querySelectorAll('tbody tr').forEach((row) => {
                Array.from(row.children).forEach((cell, index) => {
                    if (cell.hasAttribute('colspan')) return;
                    cell.setAttribute('data-label', headers[index] || '');
                });
            });
        });
    })();

    (() => {
        const menu = document.querySelector('[data-user-menu]');
        const toggle = document.querySelector('[data-user-menu-toggle]');

        if (!menu || !toggle) return;

        const closeMenu = () => {
            menu.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        };

        toggle.addEventListener('click', (event) => {
            event.stopPropagation();
            const isOpen = menu.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        document.addEventListener('click', (event) => {
            if (!menu.contains(event.target)) {
                closeMenu();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeMenu();
            }
        });
    })();
</script>
</body>
</html>

