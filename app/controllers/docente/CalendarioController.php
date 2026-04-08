<?php
require_once APP_PATH . '/models/Evento.php';
require_once APP_PATH . '/models/Notificacion.php';
require_once APP_PATH . '/models/PushSubscription.php';
require_once APP_PATH . '/helpers/NotificationHelper.php';

class CalendarioController extends Controller {
    public function index(): void {
        $this->requireAuth('docente');
        $userId = Session::userId();
        $anioId = Session::anioLectivoId();
        $instId = Session::institucionId();

        $asignaciones = Database::fetchAll("
            SELECT ad.seccion_id, s.nombre_completo AS seccion, a.nombre AS asignatura, a.id AS asignatura_id
            FROM asignacion_docentes ad
            INNER JOIN secciones s ON s.id = ad.seccion_id
            INNER JOIN asignaturas a ON a.id = ad.asignatura_id
            INNER JOIN docentes d ON d.id = ad.docente_id
            WHERE d.usuario_id = ? AND ad.anio_lectivo_id = ?
        ", [$userId, $anioId]);

        $this->view('docente/calendario', [
            'asignaciones' => $asignaciones,
            'pageTitle'    => 'Calendario'
        ]);
    }

    public function eventos(): void {
        $this->requireAuth('docente');
        $userId = Session::userId();
        $anioId = Session::anioLectivoId();

        $eventos = Database::fetchAll("
            SELECT ec.*, a.nombre AS asignatura_nombre, s.nombre_completo AS seccion
            FROM eventos_calendario ec
            LEFT JOIN asignaturas a ON a.id = ec.asignatura_id
            LEFT JOIN secciones s ON s.id = ec.seccion_id
            WHERE ec.anio_lectivo_id = ? AND ec.activo = 1
            AND (ec.creado_por = ? OR ec.audiencia IN ('todos','docentes'))
        ", [$anioId, $userId]);

        $result = array_map(fn($e) => [
            'id'    => $e['id'], 'title' => $e['titulo'],
            'start' => $e['fecha_inicio'], 'end' => $e['fecha_fin'] ?: $e['fecha_inicio'],
            'allDay'=> (bool)$e['todo_el_dia'], 'color' => $e['color'],
            'extendedProps' => ['tipo' => $e['tipo'], 'descripcion' => $e['descripcion'] ?? '', 'asignatura' => $e['asignatura_nombre'] ?? '', 'seccion' => $e['seccion'] ?? '']
        ], $eventos);

        $this->json($result);
    }

    public function crear(): void {
        $this->requireAuth('docente');
        $userId = Session::userId();
        $instId = Session::institucionId();
        $anioId = Session::anioLectivoId();

        $seccionId   = $this->inputInt('seccion_id') ?: null;
        $asignaturaId= $this->inputInt('asignatura_id') ?: null;

        $id = Database::insert("INSERT INTO eventos_calendario
            (institucion_id,creado_por,titulo,descripcion,tipo,fecha_inicio,fecha_fin,todo_el_dia,color,audiencia,seccion_id,asignatura_id,anio_lectivo_id)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)", [
            $instId, $userId,
            $this->sanitize($this->input('titulo','')),
            $this->sanitize($this->input('descripcion','')),
            $this->input('tipo','aviso'),
            $this->input('fecha_inicio',''),
            $this->input('fecha_fin','') ?: null,
            $this->input('todo_el_dia',0) ? 1 : 0,
            $this->input('color','#3498db'),
            $this->input('audiencia','seccion'),
            $seccionId, $asignaturaId, $anioId
        ]);

        if ($id && $seccionId) {
            // Send push notifications to parents of this section
            NotificationHelper::notificarSeccion(
                $seccionId, $anioId,
                'Nuevo evento en el calendario',
                $this->sanitize($this->input('titulo','')),
                'evento', url('academico')
            );
        }

        $this->json(['success' => (bool)$id, 'id' => $id]);
    }

    public function editar(): void {
        $this->requireAuth('docente');
        $id     = $this->inputInt('id');
        $userId = Session::userId();
        $evento = Database::fetchOne("SELECT * FROM eventos_calendario WHERE id=? AND creado_por=?", [$id, $userId]);
        if (!$evento) { $this->json(['error' => 'No autorizado'], 403); return; }
        Database::execute("UPDATE eventos_calendario SET titulo=?,descripcion=?,fecha_inicio=?,fecha_fin=?,color=?,tipo=? WHERE id=?", [
            $this->sanitize($this->input('titulo','')),
            $this->sanitize($this->input('descripcion','')),
            $this->input('fecha_inicio',''),
            $this->input('fecha_fin','') ?: null,
            $this->input('color','#3498db'),
            $this->input('tipo','aviso'), $id
        ]);
        $this->json(['success' => true]);
    }

    public function eliminar(): void {
        $this->requireAuth('docente');
        $id     = $this->inputInt('id');
        $userId = Session::userId();
        $rows   = Database::execute("UPDATE eventos_calendario SET activo=0 WHERE id=? AND creado_por=?", [$id, $userId]);
        $this->json(['success' => $rows > 0]);
    }
}