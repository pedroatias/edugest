<?php
// app/views/admin/configuracion/usuarios.php
$usuarios = $data['usuarios'] ?? [];
?>
<?php $this->layout('layouts/admin', ['title' => 'Gestión de Usuarios', 'active' => 'configuracion']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Gestión de Usuarios del Sistema</h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
          <li class="breadcrumb-item"><a href="/admin/configuracion">Configuración</a></li>
          <li class="breadcrumb-item active">Usuarios</li>
        </ol></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-4">
          <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Nuevo Usuario Admin/Docente</h3></div>
            <form action="/admin/configuracion/usuarios/store" method="POST">
              <div class="card-body">
                <div class="form-group">
                  <label>Nombre Completo *</label>
                  <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="form-group">
                  <label>Email *</label>
                  <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                  <label>Contraseña *</label>
                  <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="form-group">
                  <label>Rol *</label>
                  <select name="rol" class="form-control" required>
                    <option value="admin">Administrador</option>
                    <option value="docente">Docente</option>
                  </select>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-user-plus"></i> Crear Usuario</button>
              </div>
            </form>
          </div>
        </div>
        <div class="col-md-8">
          <div class="card">
            <div class="card-header"><h3 class="card-title">Usuarios del Sistema</h3></div>
            <div class="card-body p-0">
              <table class="table table-hover table-sm">
                <thead class="thead-light">
                  <tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th><th>Último acceso</th><th>Acc.</th></tr>
                </thead>
                <tbody>
                  <?php foreach($usuarios as $u): ?>
                  <tr>
                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="badge badge-<?= $u['rol'] === 'admin' ? 'danger' : 'info' ?>"><?= ucfirst($u['rol']) ?></span></td>
                    <td><span class="badge badge-<?= $u['activo'] ? 'success':'secondary' ?>"><?= $u['activo'] ? 'Activo':'Inactivo' ?></span></td>
                    <td><small><?= $u['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($u['ultimo_acceso'])) : 'Nunca' ?></small></td>
                    <td>
                      <a href="/admin/configuracion/usuarios/edit/<?= $u['id'] ?>" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                      <?php if($u['activo']): ?>
                        <a href="/admin/configuracion/usuarios/desactivar/<?= $u['id'] ?>" class="btn btn-xs btn-secondary" onclick="return confirm('Desactivar usuario?')"><i class="fas fa-ban"></i></a>
                      <?php else: ?>
                        <a href="/admin/configuracion/usuarios/activar/<?= $u['id'] ?>" class="btn btn-xs btn-success"><i class="fas fa-check"></i></a>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                  <?php if(!$usuarios): ?><tr><td colspan="6" class="text-center text-muted py-3">Sin usuarios</td></tr><?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>