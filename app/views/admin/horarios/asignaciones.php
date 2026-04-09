<?php
// app/views/admin/horarios/asignaciones.php
$secciones = $data['secciones'] ?? [];
$asignaturas = $data['asignaturas'] ?? [];
$docentes = $data['docentes'] ?? [];
$seccion_id = $data['seccion_id'] ?? '';
?>
<?php $this->layout('layouts/admin', ['title' => 'Asignar Clase al Horario', 'active' => 'horarios']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Asignar Clase al Horario</h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
          <li class="breadcrumb-item"><a href="/admin/horarios">Horarios</a></li>
          <li class="breadcrumb-item active">Asignar Clase</li>
        </ol></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary col-md-7">
        <div class="card-header"><h3 class="card-title">Nueva Asignación</h3></div>
        <form action="/admin/horarios/store" method="POST">
          <div class="card-body">
            <div class="form-group">
              <label>Sección *</label>
              <select name="seccion_id" class="form-control" required>
                <option value="">-- Seleccione --</option>
                <?php foreach($secciones as $s): ?>
                  <option value="<?= $s['id'] ?>" <?= $seccion_id == $s['id'] ? 'selected':'' ?>><?= htmlspecialchars($s['grado_nombre'].' '.$s['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Asignatura *</label>
              <select name="asignatura_id" class="form-control" required>
                <option value="">-- Seleccione --</option>
                <?php foreach($asignaturas as $a): ?>
                  <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Docente *</label>
              <select name="docente_id" class="form-control" required>
                <option value="">-- Seleccione --</option>
                <?php foreach($docentes as $d): ?>
                  <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Día de la Semana *</label>
              <select name="dia_semana" class="form-control" required>
                <?php foreach([1=>'Lunes',2=>'Martes',3=>'Miércoles',4=>'Jueves',5=>'Viernes'] as $v=>$n): ?>
                  <option value="<?= $v ?>"><?= $n ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Hora de Inicio *</label>
                  <input type="time" name="hora_inicio" class="form-control" required value="07:00">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Hora de Fin *</label>
                  <input type="time" name="hora_fin" class="form-control" required value="08:00">
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Aula / Salón</label>
              <input type="text" name="aula" class="form-control" placeholder="Ej: Aula 101">
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
            <a href="/admin/horarios<?= $seccion_id ? '?seccion_id='.$seccion_id : '' ?>" class="btn btn-secondary ml-2">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>