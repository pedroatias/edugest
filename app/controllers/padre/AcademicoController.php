<?php
require_once APP_PATH . '/models/Estudiante.php';
require_once APP_PATH . '/models/Calificacion.php';
require_once APP_PATH . '/models/Horario.php';
require_once APP_PATH . '/models/Evento.php';

class AcademicoController extends Controller {
    private function getEstudiante(): array {
        $codEst = $this->input('cod_est', '');
        $userId = Session::userId();
        $estudiantes = (new Estudiante())->getByPadre($userId);
        if (empty($estudiantes)) {
            $this->redirect('inicio');
        }
        // If specific student requested, verify it belongs to parent
        foreach ($estudiantes as $est) {
            if ($codEst && $est['matricula_id'] == $codEst) return $est;
        }
        return $estudiantes[0]; // Default first student
    }

    public function index(): void {
        $this->requireAuth('padre');
        $userId = Session::userId();
        $instId = Session::institucionId();
        $anioId = Session::anioLectivoId();

        $estudiantes = (new Estudiante())->getByPadre($userId);
        $selected = $estudiantes[0] ?? null;

        $avisos = [];
        if ($selected) {
            $avisos = Database::fetchAll("
                SELECT av.*, CONCAT(u.nombres,' ',u.apellidos) AS docente_nombre
                FROM avisos av
                LEFT JOIN usuarios u ON u.id = av.creado_por
                WHERE av.anio_lectivo_id = ? AND av.activo = 1
                AND (av.audiencia='todos' OR av.audiencia='padres'
                     OR (av.audiencia='seccion' AND av.seccion_id=?)
                     OR (av.audiencia='padre_especifico' AND av.estudiante_id=?))
                ORDER BY av.created_at DESC LIMIT 10
            ", [$anioId, $selected['seccion_id'], $selected['id']]);
        }

        $this->view('padre/academico/index', [
            'estudiantes' => $estudiantes,
            'selected'    => $selected,
            'avisos'      => $avisos,
            'pageTitle'   => 'Academico'
        ]);
    }

    public function estudiante(): void {
        $this->requireAuth('padre');
        $est = $this->getEstudiante();

        $periodos = Database::fetchAll("SELECT * FROM periodos_academicos WHERE anio_lectivo_id=? ORDER BY numero", [Session::anioLectivoId()]);

        $this->view('padre/academico/estudiante', [
            'estudiante' => $est,
            'periodos'   => $periodos,
            'pageTitle'  => 'Academico - ' . $est['nombres']
        ]);
    }

    public function notas(): void {
        $this->requireAuth('padre');
        $est      = $this->getEstudiante();
        $periodoId = $this->inputInt('periodo_id');
        $anioId   = Session::anioLectivoId();

        // Check if notes access is open
        $periodo = Database::fetchOne("SELECT * FROM periodos_academicos WHERE id=? AND anio_lectivo_id=?", [$periodoId, $anioId]);
        $accesoCerrado = $periodo ? !$periodo['notas_habilitadas'] : true;

        $notas = [];
        $escala = [];
        if (!$accesoCerrado && $est) {
            $notas  = (new Calificacion())->getNotasPorAsignatura($est['matricula_id'], $periodoId);
            $escala = Database::fetchAll("SELECT * FROM escala_valorativa WHERE institucion_id=? ORDER BY nota_minima", [Session::institucionId()]);
        }

        $this->json([
            'acceso_cerrado' => $accesoCerrado,
            'notas'          => $notas,
            'escala'         => $escala
        ]);
    }

    public function acumulado(): void {
        $this->requireAuth('padre');
        $est    = $this->getEstudiante();
        $escala = Database::fetchAll("SELECT * FROM escala_valorativa WHERE institucion_id=? ORDER BY nota_minima", [Session::institucionId()]);

        $acumulado = (new Calificacion())->getAcumulado($est['matricula_id']);

        // Group by asignatura
        $byAsig = [];
        $periodos = [];
        foreach ($acumulado as $row) {
            $pid = $row['periodo_id'];
            $aid = $row['asignatura_id'];
            $periodos[$pid] = ['id' => $pid, 'nombre' => $row['periodo'], 'numero' => $row['numero']];
            if (!isset($byAsig[$aid])) {
                $byAsig[$aid] = ['nombre' => $row['asignatura'], 'color' => $row['color'], 'periodos' => [], 'promedio' => 0];
            }
            $byAsig[$aid]['periodos'][$pid] = (float)$row['nota_periodo'];
        }
        // Calculate annual average
        foreach ($byAsig as &$asig) {
            $sum = array_sum($asig['periodos']);
            $asig['promedio'] = count($asig['periodos']) > 0 ? round($sum / count($asig['periodos']), 2) : 0;
        }

        $this->json(['asignaturas' => array_values($byAsig), 'periodos' => array_values($periodos), 'escala' => $escala]);
    }

    public function horario(): void {
        $this->requireAuth('padre');
        $est    = $this->getEstudiante();
        $grid   = (new Horario())->getHorarioSeccion($est['seccion_id'], Session::anioLectivoId());
        $this->json(['horario' => $grid]);
    }

    public function calendario(): void {
        $this->requireAuth('padre');
        $est    = $this->getEstudiante();
        $eventos = (new Evento())->getForCalendar(Session::institucionId(), Session::anioLectivoId(), $est['seccion_id']);
        $this->json($eventos);
    }
}