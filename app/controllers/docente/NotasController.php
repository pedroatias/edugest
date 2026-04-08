<?php
require_once APP_PATH . '/models/Calificacion.php';
require_once APP_PATH . '/models/Seccion.php';
require_once APP_PATH . '/models/Estudiante.php';

class NotasController extends Controller {
    public function index(): void {
        $this->requireAuth('docente');
        $userId = Session::userId();
        $anioId = Session::anioLectivoId();

        $asignaciones = Database::fetchAll("
            SELECT ad.id, a.nombre AS asignatura, a.id AS asignatura_id,
                   s.nombre_completo AS seccion, s.id AS seccion_id
            FROM asignacion_docentes ad
            INNER JOIN asignaturas a ON a.id = ad.asignatura_id
            INNER JOIN secciones s ON s.id = ad.seccion_id
            INNER JOIN docentes d ON d.id = ad.docente_id
            WHERE d.usuario_id = ? AND ad.anio_lectivo_id = ?
            ORDER BY s.nombre_completo, a.nombre
        ", [$userId, $anioId]);

        $periodos = Database::fetchAll("SELECT * FROM periodos_academicos WHERE anio_lectivo_id=? ORDER BY numero", [$anioId]);

        $this->view('docente/notas/index', [
            'asignaciones' => $asignaciones,
            'periodos'     => $periodos,
            'pageTitle'    => 'Ingreso de Notas'
        ]);
    }

    public function seccion(): void {
        $this->requireAuth('docente');
        $seccionId   = $this->inputInt('seccion_id');
        $asignaturaId= $this->inputInt('asignatura_id');
        $periodoId   = $this->inputInt('periodo_id');
        $anioId      = Session::anioLectivoId();
        $userId      = Session::userId();

        // Verify docente has this assignment
        $asignacion = Database::fetchOne("
            SELECT ad.id FROM asignacion_docentes ad
            INNER JOIN docentes d ON d.id = ad.docente_id
            WHERE d.usuario_id=? AND ad.seccion_id=? AND ad.asignatura_id=? AND ad.anio_lectivo_id=?
        ", [$userId, $seccionId, $asignaturaId, $anioId]);

        if (!$asignacion) {
            $this->json(['error' => 'No tiene permiso para editar estas notas.'], 403);
            return;
        }

        // Check period is open
        $periodo = Database::fetchOne("SELECT * FROM periodos_academicos WHERE id=?", [$periodoId]);
        if (!$periodo || !$periodo['notas_habilitadas']) {
            $this->json(['error' => 'El acceso a notas esta cerrado para este periodo.'], 403);
            return;
        }

        $estudiantes = (new Estudiante())->getBySeccion($seccionId, $anioId);
        $estructura  = Database::fetchAll("SELECT * FROM estructura_notas WHERE institucion_id=? AND activo=1 ORDER BY orden", [Session::institucionId()]);

        // Get existing grades
        $notas = [];
        foreach ($estudiantes as $est) {
            $calificaciones = Database::fetchAll("
                SELECT estructura_nota_id, nota FROM calificaciones
                WHERE matricula_id=? AND asignatura_id=? AND periodo_id=?
            ", [$est['matricula_id'], $asignaturaId, $periodoId]);
            $notasEst = [];
            foreach ($calificaciones as $c) {
                $notasEst[$c['estructura_nota_id']] = (float)$c['nota'];
            }
            $notas[$est['matricula_id']] = $notasEst;
        }

        $this->json([
            'estudiantes' => $estudiantes,
            'estructura'  => $estructura,
            'notas'       => $notas,
            'periodo'     => $periodo
        ]);
    }

    public function guardar(): void {
        $this->requireAuth('docente');
        $matriculaId    = $this->inputInt('matricula_id');
        $asignaturaId   = $this->inputInt('asignatura_id');
        $periodoId      = $this->inputInt('periodo_id');
        $estructuraId   = $this->inputInt('estructura_id');
        $nota           = $this->inputFloat('nota');
        $userId         = Session::userId();

        $docente = Database::fetchOne("SELECT id FROM docentes WHERE usuario_id=?", [$userId]);
        if (!$docente) { $this->json(['error' => 'Docente no encontrado'], 404); return; }

        $ok = (new Calificacion())->guardarNota($matriculaId, $asignaturaId, $periodoId, $estructuraId, $nota, $docente['id']);
        $this->json(['success' => $ok, 'nota' => $nota]);
    }

    public function guardarMasivo(): void {
        $this->requireAuth('docente');
        $data = json_decode(file_get_contents('php://input'), true);
        $userId = Session::userId();
        $docente = Database::fetchOne("SELECT id FROM docentes WHERE usuario_id=?", [$userId]);
        if (!$docente) { $this->json(['error' => 'Docente no encontrado'], 404); return; }

        $calModel = new Calificacion();
        $saved = 0;
        foreach ($data['notas'] ?? [] as $item) {
            if ($calModel->guardarNota(
                (int)$item['matricula_id'], (int)$item['asignatura_id'],
                (int)$item['periodo_id'], (int)$item['estructura_id'],
                (float)$item['nota'], $docente['id']
            )) $saved++;
        }
        $this->json(['success' => true, 'saved' => $saved]);
    }
}