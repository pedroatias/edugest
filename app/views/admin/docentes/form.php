<?php
// app/views/admin/docentes/form.php
$isEdit = isset($data['docente']) && $data['docente'];
$doc = $isEdit ? $data['docente'] : [];
$titulo = $isEdit ? 'Editar Docente' : 'Nuevo Docente';
?>
<?php $this->layout('layouts/admin', ['title' => $titulo, 'active' => 'docentes']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1><?= $titulo ?></h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
          <li class="breadcrumb-item"><a href="/admin/docentes">Docentes</a></li>
          <li class="breadcrumb-item active"><?= $titulo ?></li>
        </ol></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary col-md-8">
        <div class="card-header"><h3 class="card-title"><?= $titulo ?></h3></div>
        <form action="/admin/docentes/<?= $isEdit ? 'update/'.$doc['id'] : 'store' ?>" method="POST" enctype="multipart/form-data">
          <?php if($isEdit): ?><input type="hidden" name="id" value="<?= $doc['id'] ?>"/><?php endif; ?>
          <div class="card-body">
            <div class="row">
              <div class="col-md-3 text-center mb-3">
                <img id="preview" src="<?= !empty($doc['foto']) ? '/uploads/fotos/'.$doc['foto'] : '/assets/img/avatar.png' ?>" class="img-circle elevation-2" style="width:110px;height:110px;object-fit:cover;">
                <div class="mt-2"><input type="file" name="foto" id="foto" accept="image/*" class="d-none"><label for="foto" class="btn btn-sm btn-outline-secondary">Cambiar foto</label></div>
              </div>
              <div class="col-md-9">
                <div class="row">
                  <div class="col-md-6 form-group">
                    <label>Nombres *</label>
                    <input type="text" name="nombres" class="form-control" value="<?= htmlspecialchars($doc['nombres'] ?? '') ?>" required>
                  </div>
                  <div class="col-md-6 form-group">
                    <label>Apellidos *</label>
                    <input type="text" name="apellidos" class="form-control" value="<?= htmlspecialchars($doc['apellidos'] ?? '') ?>" required>
                  </div>
                  <div class="col-md-6 form-group">
                    <label>Cédula</label>
                    <input type="text" name="cedula" class="form-control" value="<?= htmlspecialchars($doc['cedula'] ?? '') ?>">
                  </div>
                  <div class="col-md-6 form-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($doc['telefono'] ?? '') ?>">
                  </div>
                  <div class="col-md-6 form-group">
                    <label>Especialidad</label>
                    <input type="text" name="especialidad" class="form-control" value="<?= htmlspecialchars($doc['especialidad'] ?? '') ?>" placeholder="Ej: Ciencias, Matemáticas">
                  </div>
                  <div class="col-md-6 form-group">
                    <label>Título</label>
                    <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($doc['titulo'] ?? '') ?>" placeholder="Ej: Lic. en Educación">
                  </div>
                </div>
              </div>
            </div>
            <hr><h5>Credenciales de Acceso</h5>
            <div class="row">
              <div class="col-md-6 form-group">
                <label>Email de acceso *</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($doc['email'] ?? '') ?>" required>
              </div>
              <div class="col-md-6 form-group">
                <label><?= $isEdit ? 'Nueva Contraseña (dejar en blanco para no cambiar)' : 'Contraseña *' ?></label>
                <input type="password" name="password" class="form-control" <?= $isEdit ? '' : 'required' ?> minlength="6">
              </div>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
            <a href="/admin/docentes" class="btn btn-secondary ml-2">Cancelar</a>
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
</script>