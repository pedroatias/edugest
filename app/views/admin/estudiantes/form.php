<?php
// app/views/admin/estudiantes/form.php
$isEdit = isset($data['estudiante']) && $data['estudiante'];
$est    = $isEdit ? $data['estudiante'] : [];
$grados = $data['grados'] ?? [];
$secciones = $data['secciones'] ?? [];
$titulo = $isEdit ? 'Editar Estudiante' : 'Nuevo Estudiante';
?>
<?php $this->layout('layouts/admin', ['title' => $titulo, 'active' => 'estudiantes']) ?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1><?= $titulo ?></h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
            <li class="breadcrumb-item"><a href="/admin/estudiantes">Estudiantes</a></li>
            <li class="breadcrumb-item active"><?= $titulo ?></li>
          </ol>
        </div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
        <div class="card-header"><h3 class="card-title">Datos del Estudiante</h3></div>
        <form action="/admin/estudiantes/<?= $isEdit ? 'update/'.$est['id'] : 'store' ?>" method="POST" enctype="multipart/form-data">
          <?php if($isEdit): ?><input type="hidden" name="id" value="<?= $est['id'] ?>"/><?php endif; ?>
          <div class="card-body">
            <div class="row">
              <div class="col-md-3 text-center mb-3">
                <img id="preview" src="<?= !empty($est['foto']) ? '/uploads/fotos/'.$est['foto'] : '/assets/img/avatar.png' ?>" class="img-circle elevation-2" style="width:120px;height:120px;object-fit:cover;">
                <div class="mt-2"><input type="file" name="foto" id="foto" accept="image/*" class="d-none"><label for="foto" class="btn btn-sm btn-outline-secondary">Cambiar foto</label></div>
              </div>
              <div class="col-md-9">
                <div class="row">
                  <div class="col-md-4 form-group">
                    <label>Nombres *</label>
                    <input type="text" name="nombres" class="form-control" value="<?= htmlspecialchars($est['nombres'] ?? '') ?>" required>
                  </div>
                  <div class="col-md-4 form-group">
                    <label>Apellidos *</label>
                    <input type="text" name="apellidos" class="form-control" value="<?= htmlspecialchars($est['apellidos'] ?? '') ?>" required>
                  </div>
                  <div class="col-md-4 form-group">
                    <label>Cédula / Doc. Identidad</label>
                    <input type="text" name="cedula" class="form-control" value="<?= htmlspecialchars($est['cedula'] ?? '') ?>">
                  </div>
                  <div class="col-md-4 form-group">
                    <label>Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control" value="<?= $est['fecha_nacimiento'] ?? '' ?>">
                  </div>
                  <div class="col-md-4 form-group">
                    <label>Grado *</label>
                    <select name="grado_id" id="grado_id" class="form-control" required>
                      <option value="">-- Seleccione --</option>
                      <?php foreach($grados as $g): ?>
                        <option value="<?= $g['id'] ?>" <?= ($est['grado_id'] ?? '') == $g['id'] ? 'selected' : '' ?>><?= htmlspecialchars($g['nombre']) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-4 form-group">
                    <label>Sección *</label>
                    <select name="seccion_id" id="seccion_id" class="form-control" required>
                      <option value="">-- Seleccione grado primero --</option>
                      <?php foreach($secciones as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= ($est['seccion_id'] ?? '') == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['nombre']) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <hr><h5>Datos del Representante</h5>
            <div class="row">
              <div class="col-md-4 form-group">
                <label>Nombre del Representante *</label>
                <input type="text" name="rep_nombre" class="form-control" value="<?= htmlspecialchars($est['rep_nombre'] ?? '') ?>" required>
              </div>
              <div class="col-md-4 form-group">
                <label>Cédula del Representante</label>
                <input type="text" name="rep_cedula" class="form-control" value="<?= htmlspecialchars($est['rep_cedula'] ?? '') ?>">
              </div>
              <div class="col-md-4 form-group">
                <label>Teléfono</label>
                <input type="text" name="rep_telefono" class="form-control" value="<?= htmlspecialchars($est['rep_telefono'] ?? '') ?>">
              </div>
              <div class="col-md-4 form-group">
                <label>Email</label>
                <input type="email" name="rep_email" class="form-control" value="<?= htmlspecialchars($est['rep_email'] ?? '') ?>">
              </div>
              <div class="col-md-4 form-group">
                <label>Parentesco</label>
                <select name="rep_parentesco" class="form-control">
                  <option value="">-- Seleccione --</option>
                  <?php foreach(['Padre','Madre','Tutor','Otro'] as $p): ?>
                    <option value="<?= $p ?>" <?= ($est['rep_parentesco'] ?? '') == $p ? 'selected':'' ?>><?= $p ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-4 form-group">
                <label>Dirección</label>
                <input type="text" name="rep_direccion" class="form-control" value="<?= htmlspecialchars($est['rep_direccion'] ?? '') ?>">
              </div>
            </div>
            <hr><h5>Acceso al Portal (Padre)</h5>
            <div class="row">
              <div class="col-md-4 form-group">
                <label>Email de acceso</label>
                <input type="email" name="usuario_email" class="form-control" value="<?= htmlspecialchars($est['usuario_email'] ?? '') ?>">
              </div>
              <div class="col-md-4 form-group">
                <label><?= $isEdit ? 'Nueva Contraseña (dejar en blanco para no cambiar)' : 'Contraseña *' ?></label>
                <input type="password" name="password" class="form-control" <?= $isEdit ? '' : 'required' ?>>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
            <a href="/admin/estudiantes" class="btn btn-secondary ml-2">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
<script>
document.getElementById('foto').addEventListener('change', function(){
  const r = new FileReader();
  r.onload = e => document.getElementById('preview').src = e.target.result;
  r.readAsDataURL(this.files[0]);
});
document.getElementById('grado_id').addEventListener('change', function(){
  const gid = this.value;
  fetch('/api/secciones?grado_id='+gid).then(r=>r.json()).then(data=>{
    const sel = document.getElementById('seccion_id');
    sel.innerHTML = '<option value="">-- Seleccione --</option>';
    data.forEach(s => sel.innerHTML += '<option value="'+s.id+'">'+s.nombre+'</option>');
  });
});
</script>