<?php
// app/views/admin/configuracion/index.php
$cfg = $data['config'] ?? [];
?>
<?php $this->layout('layouts/admin', ['title' => 'Configuración del Sistema', 'active' => 'configuracion']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Configuración del Sistema</h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
          <li class="breadcrumb-item active">Configuración</li>
        </ol></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <?php if(!empty($data['flash'])): ?>
        <div class="alert alert-<?= $data['flash']['type'] ?> alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <?= htmlspecialchars($data['flash']['msg']) ?>
        </div>
      <?php endif; ?>
      <div class="card">
        <div class="card-header p-2">
          <ul class="nav nav-pills">
            <li class="nav-item"><a class="nav-link active" href="#general" data-toggle="tab"><i class="fas fa-cog"></i> General</a></li>
            <li class="nav-item"><a class="nav-link" href="#pagos" data-toggle="tab"><i class="fas fa-credit-card"></i> Pasarela de Pagos</a></li>
            <li class="nav-item"><a class="nav-link" href="#notif" data-toggle="tab"><i class="fas fa-bell"></i> Notificaciones</a></li>
            <li class="nav-item"><a class="nav-link" href="#academico" data-toggle="tab"><i class="fas fa-graduation-cap"></i> Académico</a></li>
          </ul>
        </div>
        <form action="/admin/configuracion/guardar" method="POST" enctype="multipart/form-data">
          <div class="card-body">
            <div class="tab-content">
              <!-- GENERAL -->
              <div class="tab-pane active" id="general">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Nombre de la Institución</label>
                      <input type="text" name="inst_nombre" class="form-control" value="<?= htmlspecialchars($cfg['inst_nombre'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                      <label>Eslogan / Subtítulo</label>
                      <input type="text" name="inst_eslogan" class="form-control" value="<?= htmlspecialchars($cfg['inst_eslogan'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                      <label>Dirección</label>
                      <input type="text" name="inst_direccion" class="form-control" value="<?= htmlspecialchars($cfg['inst_direccion'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                      <label>Teléfono</label>
                      <input type="text" name="inst_telefono" class="form-control" value="<?= htmlspecialchars($cfg['inst_telefono'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                      <label>Email institucional</label>
                      <input type="email" name="inst_email" class="form-control" value="<?= htmlspecialchars($cfg['inst_email'] ?? '') ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Logo de la institución</label><br>
                      <?php if(!empty($cfg['inst_logo'])): ?>
                        <img src="/uploads/config/<?= htmlspecialchars($cfg['inst_logo']) ?>" height="80" class="mb-2 d-block">
                      <?php endif; ?>
                      <input type="file" name="inst_logo" accept="image/*" class="form-control-file">
                    </div>
                    <div class="form-group">
                      <label>Año Escolar Activo</label>
                      <input type="text" name="anio_escolar" class="form-control" value="<?= htmlspecialchars($cfg['anio_escolar'] ?? date('Y')) ?>">
                    </div>
                    <div class="form-group">
                      <label>Color Principal</label>
                      <input type="color" name="color_principal" class="form-control" value="<?= htmlspecialchars($cfg['color_principal'] ?? '#007bff') ?>">
                    </div>
                  </div>
                </div>
              </div>
              <!-- PAGOS -->
              <div class="tab-pane" id="pagos">
                <div class="form-group">
                  <label>Ambiente Wompi</label>
                  <select name="wompi_env" class="form-control col-md-4">
                    <option value="sandbox" <?= ($cfg['wompi_env'] ?? 'sandbox') === 'sandbox' ? 'selected':'' ?>>Sandbox (pruebas)</option>
                    <option value="production" <?= ($cfg['wompi_env'] ?? '') === 'production' ? 'selected':'' ?>>Producción</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Wompi Public Key</label>
                  <input type="text" name="wompi_pub_key" class="form-control col-md-8" value="<?= htmlspecialchars($cfg['wompi_pub_key'] ?? '') ?>" placeholder="pub_...">
                </div>
                <div class="form-group">
                  <label>Wompi Private Key</label>
                  <input type="password" name="wompi_priv_key" class="form-control col-md-8" value="<?= htmlspecialchars($cfg['wompi_priv_key'] ?? '') ?>" placeholder="prv_...">
                </div>
                <div class="form-group">
                  <label>Wompi Events Secret</label>
                  <input type="password" name="wompi_events_secret" class="form-control col-md-8" value="<?= htmlspecialchars($cfg['wompi_events_secret'] ?? '') ?>">
                </div>
              </div>
              <!-- NOTIFICACIONES -->
              <div class="tab-pane" id="notif">
                <div class="form-group">
                  <label>VAPID Public Key</label>
                  <input type="text" name="vapid_public" class="form-control" value="<?= htmlspecialchars($cfg['vapid_public'] ?? '') ?>" placeholder="Se genera automáticamente...">
                </div>
                <div class="form-group">
                  <label>VAPID Private Key</label>
                  <input type="password" name="vapid_private" class="form-control" value="<?= htmlspecialchars($cfg['vapid_private'] ?? '') ?>">
                </div>
                <a href="/admin/configuracion/generar_vapid" class="btn btn-warning"><i class="fas fa-key"></i> Generar nuevas claves VAPID</a>
              </div>
              <!-- ACADEMICO -->
              <div class="tab-pane" id="academico">
                <div class="form-group">
                  <label>Escala de Calificación (Mínima)</label>
                  <input type="number" name="nota_minima" class="form-control col-md-2" value="<?= htmlspecialchars($cfg['nota_minima'] ?? '1') ?>" min="1" max="10" step="0.1">
                </div>
                <div class="form-group">
                  <label>Escala de Calificación (Máxima)</label>
                  <input type="number" name="nota_maxima" class="form-control col-md-2" value="<?= htmlspecialchars($cfg['nota_maxima'] ?? '10') ?>" min="1" max="100" step="0.1">
                </div>
                <div class="form-group">
                  <label>Nota Mínima de Aprobación</label>
                  <input type="number" name="nota_aprobacion" class="form-control col-md-2" value="<?= htmlspecialchars($cfg['nota_aprobacion'] ?? '6') ?>" step="0.1">
                </div>
                <div class="form-group">
                  <label>Número de Períodos por Año</label>
                  <select name="num_periodos" class="form-control col-md-3">
                    <?php foreach([2,3,4] as $n): ?>
                      <option value="<?= $n ?>" <?= ($cfg['num_periodos'] ?? 3) == $n ? 'selected':'' ?>><?= $n ?> períodos</option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Configuración</button>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>