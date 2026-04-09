<?php
// app/views/auth/inscripcion.php
$grados = $data['grados'] ?? [];
?>
<?php $this->layout('layouts/auth', ['title' => 'Solicitud de Inscripción']) ?>
<div class="register-box">
  <div class="register-logo">
    <a href="/"><b>Edu</b>Gest</a><br>
    <small class="text-muted">Solicitud de Inscripción</small>
  </div>
  <div class="card">
    <div class="card-body register-card-body">
      <?php if(!empty($data['success'])): ?>
        <div class="alert alert-success text-center">
          <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
          <strong>¡Solicitud enviada!</strong><br>
          Hemos recibido tu solicitud. Te notificaremos al email proporcionado sobre el estado de la misma.<br>
          <a href="/inscripcion/estado" class="btn btn-success btn-sm mt-2">Consultar estado de mi solicitud</a>
        </div>
      <?php else: ?>
      <p class="register-box-msg">Complete el formulario para solicitar un cupo en nuestra institución</p>
      <?php if(!empty($data['errors'])): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach($data['errors'] as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      <form action="/inscripcion/solicitar" method="POST" enctype="multipart/form-data">
        <h6 class="text-muted text-uppercase mb-3"><small>Datos del Estudiante</small></h6>
        <div class="input-group mb-2">
          <input type="text" name="nombre_estudiante" class="form-control" placeholder="Nombre completo del estudiante *" required value="<?= htmlspecialchars($_POST['nombre_estudiante'] ?? '') ?>">
          <div class="input-group-append"><div class="input-group-text"><i class="fas fa-user"></i></div></div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-2">
            <input type="date" name="fecha_nacimiento_est" class="form-control" placeholder="Fecha de nacimiento" value="<?= htmlspecialchars($_POST['fecha_nacimiento_est'] ?? '') ?>">
          </div>
          <div class="col-md-6 mb-2">
            <select name="grado_solicitado" class="form-control" required>
              <option value="">Grado solicitado *</option>
              <?php foreach($grados as $g): ?>
                <option value="<?= htmlspecialchars($g['nombre']) ?>" <?= ($_POST['grado_solicitado'] ?? '') === $g['nombre'] ? 'selected':'' ?>><?= htmlspecialchars($g['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="text" name="institucion_anterior" class="form-control" placeholder="Institución anterior (si aplica)" value="<?= htmlspecialchars($_POST['institucion_anterior'] ?? '') ?>">
          <div class="input-group-append"><div class="input-group-text"><i class="fas fa-school"></i></div></div>
        </div>
        <h6 class="text-muted text-uppercase mb-3 mt-2"><small>Datos del Representante</small></h6>
        <div class="input-group mb-2">
          <input type="text" name="rep_nombre" class="form-control" placeholder="Nombre del representante *" required value="<?= htmlspecialchars($_POST['rep_nombre'] ?? '') ?>">
          <div class="input-group-append"><div class="input-group-text"><i class="fas fa-user-tie"></i></div></div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-2">
            <input type="text" name="rep_cedula" class="form-control" placeholder="Cédula / Documento" value="<?= htmlspecialchars($_POST['rep_cedula'] ?? '') ?>">
          </div>
          <div class="col-md-6 mb-2">
            <select name="rep_parentesco" class="form-control">
              <option value="">Parentesco...</option>
              <?php foreach(['Padre','Madre','Tutor','Otro'] as $p): ?>
                <option value="<?= $p ?>" <?= ($_POST['rep_parentesco'] ?? '') === $p ? 'selected':'' ?>><?= $p ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="input-group mb-2">
          <input type="email" name="rep_email" class="form-control" placeholder="Email del representante *" required value="<?= htmlspecialchars($_POST['rep_email'] ?? '') ?>">
          <div class="input-group-append"><div class="input-group-text"><i class="fas fa-envelope"></i></div></div>
        </div>
        <div class="input-group mb-2">
          <input type="text" name="rep_telefono" class="form-control" placeholder="Teléfono / WhatsApp *" required value="<?= htmlspecialchars($_POST['rep_telefono'] ?? '') ?>">
          <div class="input-group-append"><div class="input-group-text"><i class="fas fa-phone"></i></div></div>
        </div>
        <div class="input-group mb-3">
          <input type="text" name="rep_direccion" class="form-control" placeholder="Dirección del representante" value="<?= htmlspecialchars($_POST['rep_direccion'] ?? '') ?>">
          <div class="input-group-append"><div class="input-group-text"><i class="fas fa-map-marker-alt"></i></div></div>
        </div>
        <h6 class="text-muted text-uppercase mb-2"><small>Documentos (opcional, puede enviarlos después)</small></h6>
        <div class="form-group mb-3">
          <label class="small text-muted">Cédula del representante (imagen/PDF)</label>
          <input type="file" name="doc_cedula_rep" class="form-control-file" accept="image/*,.pdf">
        </div>
        <div class="form-group mb-3">
          <label class="small text-muted">Partida de nacimiento del estudiante</label>
          <input type="file" name="doc_partida" class="form-control-file" accept="image/*,.pdf">
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Enviar Solicitud de Inscripción</button>
          </div>
        </div>
      </form>
      <?php endif; ?>
      <p class="text-center mt-3 mb-0">
        <a href="/inscripcion/estado">¿Ya enviaste tu solicitud? Consultar estado</a><br>
        <a href="/">Iniciar sesión</a>
      </p>
    </div>
  </div>
</div>