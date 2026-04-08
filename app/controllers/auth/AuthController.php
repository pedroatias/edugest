<?php
require_once APP_PATH . '/models/Usuario.php';
require_once APP_PATH . '/models/Estudiante.php';

class AuthController extends Controller {
    public function loginView(): void {
        if (Session::isLoggedIn()) {
            $this->redirect($this->getHomeUrl());
        }
        $this->view('auth/login', [], 'auth');
    }

    public function login(): void {
        $username = trim($this->input('username', ''));
        $password = $this->input('password', '');

        if (empty($username) || empty($password)) {
            Session::flash('error', 'Por favor ingrese usuario y contrasena.');
            $this->redirect('login');
            return;
        }

        $usuarioModel = new Usuario();
        $user = $usuarioModel->findByUsername($username);

        if (!$user || !$usuarioModel->verifyPassword($password, $user['password'])) {
            Session::flash('error', 'Usuario o contrasena incorrectos.');
            $this->redirect('login');
            return;
        }

        if (!$user['activo']) {
            Session::flash('error', 'Su cuenta se encuentra inactiva. Contacte al administrador.');
            $this->redirect('login');
            return;
        }

        // Get active academic year
        $anio = Database::fetchOne(
            "SELECT id FROM anios_lectivos WHERE institucion_id = ? AND activo = 1 LIMIT 1",
            [$user['institucion_id']]
        );
        $anioId = $anio ? $anio['id'] : 0;

        Session::login($user, $anioId);
        $usuarioModel->updateLastAccess($user['id']);

        $this->redirect($this->getHomeUrl($user['rol']));
    }

    public function logout(): void {
        Session::destroy();
        $this->redirect('login');
    }

    public function registerView(): void {
        $this->view('auth/register', [], 'auth');
    }

    public function register(): void {
        $doc      = trim($this->input('documento', ''));
        $username = trim($this->input('username', ''));
        $password = $this->input('password', '');
        $password2 = $this->input('password2', '');

        if ($password !== $password2) {
            Session::flash('error', 'Las contrasenas no coinciden.');
            $this->redirect('registro');
            return;
        }

        $usuarioModel = new Usuario();
        if ($usuarioModel->findByUsername($username)) {
            Session::flash('error', 'El nombre de usuario ya existe.');
            $this->redirect('registro');
            return;
        }

        // Find representante by document
        $rep = Database::fetchOne("SELECT * FROM representantes WHERE numero_documento = ?", [$doc]);
        if (!$rep) {
            Session::flash('error', 'No encontramos su documento en el sistema. Contacte al administrador.');
            $this->redirect('registro');
            return;
        }

        // Get student institution
        $estudiante = Database::fetchOne("SELECT * FROM estudiantes WHERE id = ?", [$rep['estudiante_id']]);
        if (!$estudiante) {
            Session::flash('error', 'Error al vincular su cuenta. Contacte al administrador.');
            $this->redirect('registro');
            return;
        }

        $uid = $usuarioModel->create([
            'institucion_id'   => $estudiante['institucion_id'],
            'numero_documento' => $doc,
            'tipo_documento'   => 'CC',
            'username'         => $username,
            'password'         => $usuarioModel->hashPassword($password),
            'rol'              => 'padre',
            'nombres'          => $rep['nombres'],
            'apellidos'        => $rep['apellidos'],
            'email'            => $rep['email'],
            'telefono'         => $rep['telefono'],
        ]);

        if ($uid) {
            Database::execute("UPDATE representantes SET usuario_id = ? WHERE id = ?", [$uid, $rep['id']]);
            Session::flash('success', 'Cuenta creada exitosamente. Ya puede iniciar sesion.');
        } else {
            Session::flash('error', 'Error al crear la cuenta. Intente nuevamente.');
        }
        $this->redirect('login');
    }

    public function recoverView(): void {
        $this->view('auth/recover', [], 'auth');
    }

    public function recover(): void {
        $doc = trim($this->input('documento', ''));
        $user = (new Usuario())->findByDocumento($doc);
        if ($user) {
            Session::flash('info', 'Si encontramos su cuenta, recibirá un correo con sus credenciales. (Funcion en desarrollo)');
        } else {
            Session::flash('info', 'Si encontramos su cuenta, recibirá un correo con sus credenciales.');
        }
        $this->redirect('recuperar');
    }

    public function inscripcionView(): void {
        $grados = Database::fetchAll("SELECT g.* FROM grados g WHERE g.activa = 1 ORDER BY g.orden");
        $this->view('auth/inscripcion', ['grados' => $grados], 'auth');
    }

    public function inscripcion(): void {
        $data = [
            'nombres'          => $this->sanitize($this->input('nombres', '')),
            'apellidos'        => $this->sanitize($this->input('apellidos', '')),
            'fecha_nacimiento' => $this->input('fecha_nacimiento', ''),
            'genero'           => $this->input('genero', ''),
            'numero_documento' => $this->sanitize($this->input('numero_documento', '')),
            'tipo_documento'   => $this->input('tipo_documento', 'TI'),
            'grado_solicitado_id' => $this->inputInt('grado_id'),
            'rep_nombres'      => $this->sanitize($this->input('rep_nombres', '')),
            'rep_apellidos'    => $this->sanitize($this->input('rep_apellidos', '')),
            'rep_documento'    => $this->sanitize($this->input('rep_documento', '')),
            'rep_parentesco'   => $this->sanitize($this->input('rep_parentesco', '')),
            'rep_email'        => $this->sanitize($this->input('rep_email', '')),
            'rep_telefono'     => $this->sanitize($this->input('rep_telefono', '')),
            'estado'           => 'nueva',
            'codigo_solicitud' => generateCode('INS', 8),
            'institucion_id'   => 1, // Default
            'anio_lectivo_id'  => Database::fetchValue("SELECT id FROM anios_lectivos WHERE activo=1 LIMIT 1") ?: 1,
        ];

        $id = Database::insert("INSERT INTO inscripciones (" . implode(',', array_keys($data)) . ") VALUES (" .
            implode(',', array_fill(0, count($data), '?')) . ")", array_values($data));

        if ($id) {
            Session::flash('success', 'Solicitud enviada exitosamente. Codigo de seguimiento: ' . $data['codigo_solicitud']);
        } else {
            Session::flash('error', 'Error al enviar la solicitud. Intente nuevamente.');
        }
        $this->redirect('inscripcion');
    }

    public function inscripcionEstado(): void {
        $codigo = $this->sanitize($this->input('codigo', ''));
        $inscripcion = null;
        if ($codigo) {
            $inscripcion = Database::fetchOne("SELECT * FROM inscripciones WHERE codigo_solicitud = ?", [$codigo]);
        }
        $this->view('auth/inscripcion_estado', ['inscripcion' => $inscripcion, 'codigo' => $codigo], 'auth');
    }

    private function getHomeUrl(string $rol = ''): string {
        $rol = $rol ?: Session::userRole();
        return match($rol) {
            'admin'   => 'admin',
            'docente' => 'docente',
            default   => 'inicio'
        };
    }
}