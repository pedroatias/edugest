<?php
// app/views/admin/horarios/index.php
$horarios = $data['horarios'] ?? [];
$secciones = $data['secciones'] ?? [];
?>
<?php $this->layout('layouts/admin', ['title' => 'Horarios', 'active' => 'horarios']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Horarios de Clases</h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
          <li class="breadcrumb-item active">Horarios</li>
        </ol></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col-md-6">
              <form class="form-inline" method="GET">
                <label class="mr-2">Sección:</label>
                <select name="seccion_id" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                  <option value="">-- Seleccione --</option>
                  <?php foreach($secciones as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= ($data['seccion_id'] ?? '') == $s['id'] ? 'selected':'' ?>><?= htmlspecialchars($s['grado_nombre'].' '.$s['nombre']) ?></option>
                  <?php endforeach; ?>
                </select>
              </form>
            </div>
            <div class="col-md-6 text-right">
              <?php if(!empty($data['seccion_id'])): ?>
                <a href="/admin/horarios/asignaciones?seccion_id=<?= $data['seccion_id'] ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Asignar Clase</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="card-body">
          <?php if(!empty($data['seccion_id']) && $horarios): ?>
            <?php
            $dias = ['Lunes','Martes','Miércoles','Jueves','Viernes'];
            $por_dia = [];
            foreach($horarios as $h) $por_dia[$h['dia_semana']][] = $h;
            ?>
            <div class="table-responsive">
              <table class="table table-bordered table-sm text-center">
                <thead class="thead-dark">
                  <tr>
                    <th style="width:100px">Hora</th>
                    <?php foreach($dias as $d): ?><th><?= $d ?></th><?php endforeach; ?>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($data['franjas'] ?? [] as $franja): ?>
                  <tr>
                    <td class="text-muted small"><?= $franja['inicio'] ?> - <?= $franja['fin'] ?></td>
                    <?php foreach($dias as $idx => $d):
                      $clase = null;
                      foreach($por_dia[$idx+1] ?? [] as $h) {
                        if($h['hora_inicio'] === $franja['inicio']) { $clase = $h; break; }
                      }
                    ?>
                    <td class="<?= $clase ? 'table-info' : '' ?>">
                      <?php if($clase): ?>
                        <strong><?= htmlspecialchars($clase['asignatura_nombre']) ?></strong><br>
                        <small class="text-muted"><?= htmlspecialchars($clase['docente_nombre']) ?></small>
                        <br><a href="/admin/horarios/delete/<?= $clase['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Quitar esta clase?')"><i class="fas fa-times"></i></a>
                      <?php endif; ?>
                    </td>
                    <?php endforeach; ?>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php elseif(!empty($data['seccion_id'])): ?>
            <div class="text-center text-muted py-4">
              <i class="fas fa-calendar-times fa-3x mb-3 d-block"></i>
              No hay horarios asignados para esta sección. <a href="/admin/horarios/asignaciones?seccion_id=<?= $data['seccion_id'] ?>">Agregar ahora</a>
            </div>
          <?php else: ?>
            <div class="text-center text-muted py-4">
              <i class="fas fa-arrow-up fa-2x mb-2 d-block"></i>
              Seleccione una sección para ver su horario
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
</div>