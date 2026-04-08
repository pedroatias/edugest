<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - Admin EduGest' : 'Admin EduGest' ?></title>
    <?= csrfMeta() ?>
    <meta name="vapid-key" content="<?= VAPID_PUBLIC_KEY ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <script>const APP_URL = '<?= APP_URL ?>';</script>
</head>
<body>
<nav class="sidebar" id="sidebar">
    <div class="logo-area"><h5><i class="fas fa-graduation-cap me-2"></i>EduGest Admin</h5></div>
    <ul class="nav flex-column mt-2">
        <li class="nav-item"><a href="<?= url('admin') ?>" class="nav-link <?= isActive('admin') && !isActive('admin/') ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <div class="nav-section">Academico</div>
        <li class="nav-item"><a href="<?= url('admin/estudiantes') ?>" class="nav-link <?= isActive('admin/estudiantes') ?>"><i class="fas fa-user-graduate"></i> Estudiantes</a></li>
        <li class="nav-item"><a href="<?= url('admin/docentes') ?>" class="nav-link <?= isActive('admin/docentes') ?>"><i class="fas fa-chalkboard-teacher"></i> Docentes</a></li>
        <li class="nav-item"><a href="<?= url('admin/secciones') ?>" class="nav-link <?= isActive('admin/secciones') ?>"><i class="fas fa-layer-group"></i> Grados y Secciones</a></li>
        <li class="nav-item"><a href="<?= url('admin/asignaturas') ?>" class="nav-link <?= isActive('admin/asignaturas') ?>"><i class="fas fa-book-open"></i> Asignaturas</a></li>
        <li class="nav-item"><a href="<?= url('admin/horarios') ?>" class="nav-link <?= isActive('admin/horarios') ?>"><i class="fas fa-calendar-week"></i> Horarios</a></li>
        <li class="nav-item"><a href="<?= url('admin/periodos') ?>" class="nav-link <?= isActive('admin/periodos') ?>"><i class="fas fa-clock"></i> Periodos</a></li>
        <li class="nav-item"><a href="<?= url('admin/boletines') ?>" class="nav-link <?= isActive('admin/boletines') ?>"><i class="fas fa-file-pdf"></i> Boletines</a></li>
        <div class="nav-section">Finanzas</div>
        <li class="nav-item"><a href="<?= url('admin/pagos') ?>" class="nav-link <?= isActive('admin/pagos') ?>"><i class="fas fa-credit-card"></i> Pagos</a></li>
        <li class="nav-item"><a href="<?= url('admin/pagos/validacion') ?>" class="nav-link <?= isActive('admin/pagos/validacion') ?>"><i class="fas fa-check-circle"></i> Validar Pagos</a></li>
        <li class="nav-item"><a href="<?= url('admin/cobros') ?>" class="nav-link <?= isActive('admin/cobros') ?>"><i class="fas fa-file-invoice-dollar"></i> Cobros</a></li>
        <li class="nav-item"><a href="<?= url('admin/reportes/morosidad') ?>" class="nav-link <?= isActive('admin/reportes/morosidad') ?>"><i class="fas fa-exclamation-triangle"></i> Morosidad</a></li>
        <div class="nav-section">Comunicaciones</div>
        <li class="nav-item"><a href="<?= url('admin/circulares') ?>" class="nav-link <?= isActive('admin/circulares') ?>"><i class="fas fa-paperclip"></i> Circulares</a></li>
        <li class="nav-item"><a href="<?= url('admin/inscripciones') ?>" class="nav-link <?= isActive('admin/inscripciones') ?>"><i class="fas fa-file-alt"></i> Inscripciones</a></li>
        <li class="nav-item"><a href="<?= url('admin/notificaciones') ?>" class="nav-link <?= isActive('admin/notificaciones') ?>"><i class="fas fa-bell"></i> Notificaciones</a></li>
        <div class="nav-section">Sistema</div>
        <li class="nav-item"><a href="<?= url('admin/configuracion') ?>" class="nav-link <?= isActive('admin/configuracion') ?>"><i class="fas fa-cog"></i> Configuracion</a></li>
    </ul>
</nav>
<div class="main-content">
    <div class="topbar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-secondary" id="sidebarToggle"><i class="fas fa-bars"></i></button>
            <small class="text-muted"><?= e(Session::get('inst_nombre','EduGest')) ?></small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="anio-badge"><?= date('Y') ?></span>
            <div class="dropdown position-relative">
                <button class="btn btn-sm btn-outline-secondary position-relative" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i><span class="badge bg-danger notif-badge" id="notif-badge" style="display:none">0</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end notif-dropdown" id="notif-list"></ul>
            </div>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-user-shield me-1"></i><?= e(Session::userFullName()) ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item text-danger" href="<?= url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesion</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="p-4">
        <?= flashAlert() ?>
        <?php $content(); ?>
    </div>
</div>
<div class="modal fade" id="SessionTimeOut" tabindex="-1"><div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-header bg-warning"><h6 class="modal-title">Sesion por vencer</h6></div><div class="modal-body small">Mueva el mouse para mantener la sesion.</div></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
<script src="<?= asset('js/app.js') ?>"></script>
<script>$('#sidebarToggle').click(() => { $('#sidebar').toggleClass('show'); });</script>
</body>
</html>