<?php
// app/views/admin/periodos/estructura_notas.php
$periodo = $data['periodo'];
$estructura = $data['estructura'] ?? [];
?>
<?php $this->layout('layouts/admin', ['title' => 'Estructura de Notas', 'active' => 'periodos']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Estructura de Notas - <?= htmlspecialchars($periodo['nombre']) ?></h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
          <li class="breadcrumb-item"><a href="/admin/periodos">Períodos</a></li>
          <li class="breadcrumb-item active">Estructura</li>
        </ol></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-4">
          <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Agregar Componente</h3></div>
            <form action="/admin/periodos/estructura/store" method="POST">
              <input type="hidden" name="periodo_id" value="<?= $periodo['id'] ?>">
              <div class="card-body">
                <div class="form-group">
                  <label>Nombre del Componente *</label>
                  <input type="text" name="nombre" class="form-control" placeholder="Ej: Evaluación, Participación..." required>
                </div>
                <div class="form-group">
                  <label>Porcentaje (%) *</label>
                  <input type="number" name="porcentaje" class="form-control" min="1" max="100" required>
                  <small class="text-muted">La suma de todos los componentes debe dar 100%</small>
                </div>
                <div class="form-group">
                  <label>Descripción</label>
                  <input type="text" name="descripcion" class="form-control" placeholder="Descripción opcional">
                </div>
              </div>
              <div class="card-footer"><button type="submit" class="btn btn-primary btn-block"><i class="fas fa-plus"></i> Agregar</button></div>
            </form>
          </div>
          <div class="card bg-light">
            <div class="card-body text-center">
              <?php $total_pct = array_sum(array_column($estructura, 'porcentaje')); ?>
              <h4 class="<?= $total_pct == 100 ? 'text-success' : 'text-danger' ?>"><?= $total_pct ?>%</h4>
              <p class="mb-0 text-muted">Total asignado <?= $total_pct == 100 ? '✓' : '(debe ser 100%)' ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-8">
          <div class="card">
            <div class="card-header"><h3 class="card-title">Componentes de Evaluación</h3></div>
            <div class="card-body p-0">
              <table class="table table-hover table-sm">
                <thead class="thead-light">
                  <tr><th>Componente</th><th>Porcentaje</th><th>Descripción</th><th>Acc.</th></tr>
                </thead>
                <tbody>
                  <?php foreach($estructura as $e): ?>
                  <tr>
                    <td><strong><?= htmlspecialchars($e['nombre']) ?></strong></td>
                    <td>
                      <div class="progress" style="height:20px">
                        <div class="progress-bar bg-primary" style="width:<?= $e['porcentaje'] ?>%"><?= $e['porcentaje'] ?>%</div>
                      </div>
                    </td>
                    <td><?= htmlspecialchars($e['descripcion'] ?? '-') ?></td>
                    <td>
                      <a href="/admin/periodos/estructura/delete/<?= $e['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Eliminar?')"><i class="fas fa-trash"></i></a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                  <?php if(!$estructura): ?><tr><td colspan="4" class="text-center text-muted py-3">Sin componentes definidos</td></tr><?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Los docentes verán estos componentes al ingresar las calificaciones de cada estudiante en este período.
          </div>
        </div>
      </div>
    </div>
  </section>
</div>