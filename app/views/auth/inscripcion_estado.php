<?php
// app/views/auth/inscripcion_estado.php
$solicitud = $data['solicitud'] ?? null;
?>
<?php $this->layout('layouts/auth', ['title' => 'Estado de Inscripción']) ?>
<div class="login-box" style="max-width:480px">
  <div class="login-logo">
    <a href="/"><b>Edu</b>Gest</a>
  </div>
  <div class="card">
    <div class="card-body">
      <p class="login-box-msg">Consulta el estado de tu solicitud de inscripción</p>
      <form action="/inscripcion/consultar" method="POST">
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email del representante" required value="<?= htmlspecialchars($data['email'] ?? '') ?>">
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-envelope"></span></div></div>
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Consultar Estado</button>
          </div>
        </div>
      </form>
      <?php if($solicitud): ?>
      <hr>
      <div class="alert alert-<?= ['pendiente'=>'secondary','en_revision'=>'info','aprobada'=>'success','rechazada'=>'danger'][$solicitud['estado']] ?? 'secondary' ?> mt-3">
        <h5>Solicitud encontrada</h5>
        <p><strong>Estudiante:</strong> <?= htmlspecialchars($solicitud['nombre_estudiante']) ?></p>
        <p><strong>Estado:</strong>
          <span class="badge badge-<?= ['pendiente'=>'secondary','en_revision'=>'info','aprobada'=>'success','rechazada'=>'danger'][$solicitud['estado']] ?? 'secondary' ?>">
            <?= ucfirst(str_replace('_',' ',$solicitud['estado'])) ?>
          </span>
        </p>
        <p><strong>Fecha de solicitud:</strong> <?= date('d/m/Y', strtotime($solicitud['created_at'])) ?></p>
        <?php if($solicitud['estado'] === 'aprobada'): ?>
          <p class="mb-0"><i class="fas fa-check-circle"></i> Su solicitud fue aprobada. Recibirá comunicación del colegio para completar el proceso de matrícula.</p>
        <?php elseif($solicitud['estado'] === 'rechazada'): ?>
          <p class="mb-0"><i class="fas fa-times-circle"></i> Su solicitud no fue aprobada en este proceso. Puede contactar a la institución para más información.</p>
        <?php else: ?>
          <p class="mb-0"><i class="fas fa-clock"></i> Su solicitud está siendo revisada. Le notificaremos por email cuando haya una actualización.</p>
        <?php endif; ?>
      </div>
      <?php elseif(isset($data['not_found'])): ?>
      <div class="alert alert-warning mt-3">
        No se encontró ninguna solicitud con ese email.
      </div>
      <?php endif; ?>
      <p class="text-center mt-3 mb-0">
        <a href="/inscripcion">Nueva solicitud de inscripción</a> &middot;
        <a href="/">Iniciar sesión</a>
      </p>
    </div>
  </div>
</div>