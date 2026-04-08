<?php
require_once APP_PATH . '/models/Estudiante.php';

class MatriculasController extends Controller {
    public function index(): void {
        $this->requireAuth('padre');
        $userId = Session::userId();
        $anioId = Session::anioLectivoId();
        $estudiantes = (new Estudiante())->getByPadre($userId);
        $this->view('padre/matriculas/index', [
            'estudiantes' => $estudiantes,
            'pageTitle'   => 'Matriculas'
        ]);
    }

    public function circulares(): void {
        $this->requireAuth('padre');
        $instId = Session::institucionId();
        $anioId = Session::anioLectivoId();
        $circulares = Database::fetchAll("
            SELECT * FROM circulares
            WHERE institucion_id=? AND (anio_lectivo_id=? OR anio_lectivo_id IS NULL) AND activa=1
            ORDER BY created_at DESC
        ", [$instId, $anioId]);
        $this->json(['circulares' => $circulares]);
    }
}