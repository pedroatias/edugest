<?php
// app/controllers/admin/EstudiantesController.php
class EstudiantesController extends Controller {
  public function index() {
    $m = new Estudiante();
    $q = $_GET['q'] ?? '';
    $gid = $_GET['grado_id'] ?? '';
    $sid = $_GET['seccion_id'] ?? '';
    $estudiantes = $m->listar($q, $gid, $sid);
    $grados = (new Grado())->all();
    $this->view('admin/estudiantes/index', compact('estudiantes','grados'));
  }
  public function nuevo() {
    $grados = (new Grado())->all();
    $secciones = (new Seccion())->all();
    $this->view('admin/estudiantes/form', compact('grados','secciones'));
  }
  public function edit($id) {
    $m = new Estudiante();
    $est = $m->find($id);
    $grados = (new Grado())->all();
    $secciones = (new Seccion())->byGrado($est['grado_id']);
    $this->view('admin/estudiantes/form', compact('est','grados','secciones'));
  }
  public function store() {
    if($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('/admin/estudiantes'); return; }
    $m = new Estudiante();
    $foto = null;
    if (!empty($_FILES['foto']['name'])) {
      $foto = uploadFile($_FILES['foto'], 'fotos');
    }
    $data = [
      'nombres'         => sanitize($_POST['nombres']),
      'apellidos'       => sanitize($_POST['apellidos']),
      'cedula'          => sanitize($_POST['cedula'] ?? ''),
      'fecha_nacimiento'=> $_POST['fecha_nacimiento'] ?? null,
      'grado_id'        => (int)$_POST['grado_id'],
      'seccion_id'      => (int)$_POST['seccion_id'],
      'rep_nombre'      => sanitize($_POST['rep_nombre']),
      'rep_cedula'      => sanitize($_POST['rep_cedula'] ?? ''),
      'rep_telefono'    => sanitize($_POST['rep_telefono'] ?? ''),
      'rep_email'       => sanitize($_POST['rep_email'] ?? ''),
      'rep_parentesco'  => sanitize($_POST['rep_parentesco'] ?? ''),
      'rep_direccion'   => sanitize($_POST['rep_direccion'] ?? ''),
      'foto'            => $foto,
      'activo'          => 1
    ];
    $estId = $m->create($data);
    // Crear usuario padre si se proporcionó email
    if (!empty($_POST['usuario_email']) && !empty($_POST['password'])) {
      $u = new Usuario();
      $u->create([
        'nombre'      => $data['rep_nombre'],
        'email'       => sanitize($_POST['usuario_email']),
        'password'    => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'rol'         => 'padre',
        'estudiante_id'=> $estId,
        'activo'      => 1
      ]);
    }
    Session::setFlash('success', 'Estudiante registrado correctamente.');
    redirect('/admin/estudiantes');
  }
  public function update($id) {
    if($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('/admin/estudiantes'); return; }
    $m = new Estudiante();
    $foto = null;
    if (!empty($_FILES['foto']['name'])) $foto = uploadFile($_FILES['foto'], 'fotos');
    $data = [
      'nombres'         => sanitize($_POST['nombres']),
      'apellidos'       => sanitize($_POST['apellidos']),
      'cedula'          => sanitize($_POST['cedula'] ?? ''),
      'fecha_nacimiento'=> $_POST['fecha_nacimiento'] ?? null,
      'grado_id'        => (int)$_POST['grado_id'],
      'seccion_id'      => (int)$_POST['seccion_id'],
      'rep_nombre'      => sanitize($_POST['rep_nombre']),
      'rep_cedula'      => sanitize($_POST['rep_cedula'] ?? ''),
      'rep_telefono'    => sanitize($_POST['rep_telefono'] ?? ''),
      'rep_email'       => sanitize($_POST['rep_email'] ?? ''),
      'rep_parentesco'  => sanitize($_POST['rep_parentesco'] ?? ''),
      'rep_direccion'   => sanitize($_POST['rep_direccion'] ?? '')
    ];
    if ($foto) $data['foto'] = $foto;
    $m->update($id, $data);
    if (!empty($_POST['password'])) {
      (new Usuario())->updateByEstudiante($id, ['password' => password_hash($_POST['password'], PASSWORD_DEFAULT)]);
    }
    Session::setFlash('success', 'Estudiante actualizado.');
    redirect('/admin/estudiantes');
  }
  public function ficha($id) {
    $m = new Estudiante();
    $est = $m->find($id);
    $pagos = (new Pago())->byEstudiante($id);
    $calificaciones = (new Calificacion())->byEstudiante($id);
    $this->view('admin/estudiantes/ficha', compact('est','pagos','calificaciones'));
  }
}