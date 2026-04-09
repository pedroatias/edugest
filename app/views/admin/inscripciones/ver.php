<?php
// app/views/admin/inscripciones/ver.php
$insc = $data['inscripcion'];
?>
<?php $this->layout('layouts/admin', ['title' => 'Ver Solicitud de Inscripción', 'active' => 'inscripciones']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Solicitud #<?= $insc['id'] ?></h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
          <li class="breadcrumb-item"><a href="/admin/inscripciones">Inscripciones</a></li>
          <li class="breadcrumb-item active">Ver</li>
        </ol></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header"><h3 class="card-title">Datos del Estudiante</h3></div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6"><strong>Nombre:</strong> <?= htmlspecialchars($insc['nombre_estudiante']) ?></div>
                <div class="col-md-6"><strong>Fecha de Nac.:</strong> <?= $insc['fecha_nacimiento_est'] ?? '-' ?></div>
                <div class="col-md-6"><strong>Grado Solicitado:</strong> <?= htmlspecialchars($insc['grado_solicitado']) ?></div>
                <div class="col-md-6"><strong>Institución anterior:</strong> <?= htmlspecialchars($insc['institucion_anterior'] ?? '-') ?></div>
              </div>
            </div>
          </div>
          <div class="card">
            <div class="card-header"><h3 class="card-title">Datos del Representante</h3></div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6"><strong>Nombre:</strong> <?= htmlspecialchars($insc['rep_nombre']) ?></div>
                <div class="col-md-6"><strong>Cédula:</strong> <?= htmlspecialchars($insc['rep_cedula'] ?? '-') ?></div>
                <div class="col-md-6"><strong>Teléfono:</strong> <?= htmlspecialchars($insc['rep_telefono'] ?? '-') ?></div>
                <div class="col-md-6"><strong>Email:</strong> <?= htmlspecialchars($insc['rep_email'] ?? '-') ?></div>
                <div class="col-md-6"><strong>Parentesco:</strong> <?= htmlspecialchars($insc['rep_parentesco'] ?? '-') ?></div>
                <div class="col-md-6"><strong>Dirección:</strong> <?= htmlspecialchars($insc['rep_direccion'] ?? '-') ?></div>
              </div>
            </div>
          </div>
          <?php if(!empty($insc['documentos'])): ?>
          <div class="card">
            <div class="card-header"><h3 class="card-title">Documentos Adjuntos</h3></div>
            <div class="card-body">
              <?php foreach(json_decode($insc['documentos'], true) ?? [] as $tipo => $archivo): ?>
              <a href="/uploads/inscripciones/<?= htmlspecialchars($archivo) ?>" target="_blank" class="btn btn-outline-secondary btn-sm mr-2 mb-2">
                <i class="fas fa-file"></i> <?= ucfirst($tipo) ?>
              </a>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>
          <div class="card">
            <div class="card-header"><h3 class="card-title">Observaciones</h3></div>
            <div class="card-body">
              <textarea name="observaciones" id="obs" class="form-control" rows="3"><?= htmlspecialchars($insc['observaciones'] ?? '') ?></textarea>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-header"><h3 class="card-title">Estado de la Solicitud</h3></div>
            <div class="card-body">
              <p>Estado actual:
                <span class="badge badge-<?= ['pendiente'=>'secondary','en_revision'=>'info','aprobada'=>'success','rechazada'=>'danger'][$insc['estado']] ?? 'secondary' ?> ml-2">
                  <?= ucfirst(str_replace('_',' ',$insc['estado'])) ?>
                </span>
              </p>
              <p>Fecha de solicitud: <?= date('d/m/Y H:i', strtotime($insc['created_at'])) ?></p>
              <?php if($insc['estado'] !== 'aprobada' && $insc['estado'] !== 'rechazada'): ?>
              <div class="btn-group-vertical w-100">
                <a href="/admin/inscripciones/aprobar/<?= $insc['id'] ?>" class="btn btn-success mb-2" onclick="return confirm('Aprobar esta inscripción?')">
                  <i class="fas fa-check"></i> Aprobar Inscripción
                </a>
                <a href="/admin/inscripciones/rechazar/<?= $insc['id'] ?>" class="btn btn-danger" onclick="return confirm('Rechazar esta inscripción?')">
                  <i class="fas fa-times"></i> Rechazar Inscripción
                </a>
              </div>
              <?php endif; ?>
            </div>
          </div>
          <a href="/admin/inscripciones" class="btn btn-secondary btn-block"><i class="fas fa-arrow-left"></i> Volver al listado</a>
        </div>
      </div>
    </div>
  </section>
</div>