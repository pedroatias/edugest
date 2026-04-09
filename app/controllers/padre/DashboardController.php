<?php
require_once APP_PATH.'/models/Estudiante.php';
require_once APP_PATH.'/models/Notificacion.php';
class DashboardController extends Controller {
    public function index(): void {
        $this->requireAuth('padre');
        $uid=Session::userId();$iid=Session::institucionId();$aid=Session::anioLectivoId();
        $estudiantes=(new Estudiante())->getByPadre($uid);
        $avisos=Database::fetchAll("SELECT * FROM avisos WHERE institucion_id=? AND anio_lectivo_id=? AND activo=1 AND audiencia IN ('todos','padres') ORDER BY created_at DESC LIMIT 5",[$iid,$aid]);
        $this->view('padre/dashboard',['estudiantes'=>$estudiantes,'avisos'=>$avisos,'noLeidas'=>(new Notificacion())->countNoLeidas($uid),'pageTitle'=>'Inicio']);
    }
}