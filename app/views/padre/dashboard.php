<?php ?>
<div class="row mb-4"><div class="col-12">
    <h4 class="fw-bold"><i class="fas fa-hand-point-right me-2 text-warning"></i>Hola, <?= e(Session::userFullName()) ?>!</h4>
    <?php foreach($avisos as $av): ?>
    <div class="alert alert-info py-2 alert-dismissible fade show"><i class="fas fa-comment-dots me-2"></i><?= e($av['titulo']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endforeach; ?>
</div></div>
<div class="row g-4">
    <?php $mods=[['academico','Academico','book','primary','Notas y horarios'],['pagos','Pagos y Cartera','dollar-sign','success','Pensiones y pagos'],['boletines','Boletines','file-pdf','info','Informes academicos'],['matriculas','Matriculas','user-graduate','warning','Estado y circulares']]; foreach($mods as [$url,$title,$icon,$color,$sub]): ?>
    <div class="col-6 col-md-3">
        <a href="<?= url($url) ?>" class="text-decoration-none">
            <div class="card module-card text-center p-4 h-100">
                <div class="module-icon bg-<?= $color ?> mx-auto"><i class="fas fa-<?= $icon ?> text-white fa-2x"></i></div>
                <div class="module-title mt-2"><?= $title ?></div>
                <small class="text-muted"><?= $sub ?></small>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>