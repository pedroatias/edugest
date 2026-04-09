<div class="auth-card">
    <div class="auth-logo"><i class="fas fa-graduation-cap fa-3x text-primary"></i></div>
    <h1 class="auth-title">EduGest</h1>
    <p class="auth-subtitle">Plataforma de Gestion Academica</p>
    <?= flashAlert() ?>
    <form method="POST" action="<?= url('login') ?>">
        <?= csrfField() ?>
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-user"></i></span>
            <input type="text" name="username" class="form-control" placeholder="Nombre de usuario" required autofocus>
        </div>
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" name="password" class="form-control" placeholder="Contrasena" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 btn-lg"><i class="fas fa-sign-in-alt me-2"></i>Ingresar</button>
    </form>
    <hr>
    <div class="text-center small">
        <a href="<?= url('recuperar') ?>" class="text-muted d-block mb-1">Olvide mi contrasena</a>
        <a href="<?= url('registro') ?>" class="text-muted">No tengo cuenta - Registrarme</a>
    </div>
    <p class="text-center text-muted mt-3 mb-0" style="font-size:.75rem"><i class="fas fa-copyright"></i> <?= date('Y') ?> EduGest</p>
</div>