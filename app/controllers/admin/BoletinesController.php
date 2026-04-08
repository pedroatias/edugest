<?php
require_once APP_PATH . '/models/Boletin.php';
require_once APP_PATH . '/helpers/PdfHelper.php';
require_once APP_PATH . '/helpers/NotificationHelper.php';

class BoletinesController extends Controller {
    public function index(): void {
        $this->requireAuth('admin');
        $instId  = Session::institucionId();
        $anioId  = Session::anioLectivoId();
        $periodoId = $this->inputInt('periodo_id');
        $periodos  = Database::fetchAll("SELECT * FROM periodos_academicos WHERE anio_lectivo_id=? ORDER BY numero", [$anioId]);
        $boletines = $periodoId ? (new Boletin())->getPendientesGeneracion($instId, $anioId, $periodoId) : [];
        $this->view('admin/boletines/index', compact('periodos','boletines','periodoId') + ['pageTitle'=>'Gestion de Boletines']);
    }

    public function generar(): void {
        $this->requireAuth('admin');
        $matriculaId = $this->inputInt('matricula_id');
        $periodoId   = $this->inputInt('periodo_id');
        $ok = PdfHelper::generarBoletin($matriculaId, $periodoId);
        $this->json(['success' => $ok]);
    }

    public function publicar(): void {
        $this->requireAuth('admin');
        $periodoId      = $this->inputInt('periodo_id');
        $fechaDisponible= $this->input('fecha_disponible', date('Y-m-d'));
        $instId = Session::institucionId();
        $anioId = Session::anioLectivoId();

        Database::execute("UPDATE boletines b SET b.disponible=1, b.fecha_disponible=? WHERE b.periodo_id=? AND EXISTS (SELECT 1 FROM matriculas m INNER JOIN estudiantes e ON e.id=m.estudiante_id WHERE m.id=b.matricula_id AND e.institucion_id=?)",
            [$fechaDisponible, $periodoId, $instId]);

        // Notify all parents
        $padres = Database::fetchAll("
            SELECT DISTINCT r.usuario_id FROM representantes r
            INNER JOIN matriculas m ON m.estudiante_id = r.estudiante_id
            INNER JOIN estudiantes e ON e.id = m.estudiante_id
            WHERE e.institucion_id=? AND m.anio_lectivo_id=? AND r.usuario_id IS NOT NULL
        ", [$instId, $anioId]);
        $periodo = Database::fetchOne("SELECT nombre FROM periodos_academicos WHERE id=?", [$periodoId]);
        foreach ($padres as $p) {
            NotificationHelper::enviarAUsuario($p['usuario_id'], 'Boletin Disponible',
                'El boletin del ' . $periodo['nombre'] . ' ya esta disponible para descarga.',
                'boletin', url('boletines'));
        }

        $this->json(['success' => true]);
    }
}