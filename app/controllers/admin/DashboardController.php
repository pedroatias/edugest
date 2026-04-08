<?php
require_once APP_PATH . '/models/Estudiante.php';
require_once APP_PATH . '/models/Pago.php';
require_once APP_PATH . '/models/CuentaPorCobrar.php';
require_once APP_PATH . '/models/Notificacion.php';

class DashboardController extends Controller {
    public function index(): void {
        $this->requireAuth('admin');
        $instId = Session::institucionId();
        $anioId = Session::anioLectivoId();

        $stats = [
            'total_estudiantes' => Database::fetchValue("SELECT COUNT(*) FROM estudiantes WHERE institucion_id=? AND estado='activo'", [$instId]),
            'total_docentes'    => Database::fetchValue("SELECT COUNT(*) FROM docentes d INNER JOIN usuarios u ON u.id=d.usuario_id WHERE u.institucion_id=? AND u.activo=1", [$instId]),
            'total_secciones'   => Database::fetchValue("SELECT COUNT(*) FROM secciones s INNER JOIN grados g ON g.id=s.grado_id WHERE g.institucion_id=? AND s.anio_lectivo_id=?", [$instId, $anioId]),
            'recaudo_mes'       => Database::fetchValue("SELECT COALESCE(SUM(p.valor_pagado),0) FROM pagos p INNER JOIN matriculas m ON m.id=p.matricula_id INNER JOIN estudiantes e ON e.id=m.estudiante_id WHERE e.institucion_id=? AND p.estado='verificado' AND MONTH(p.fecha_pago)=MONTH(CURDATE()) AND YEAR(p.fecha_pago)=YEAR(CURDATE())", [$instId]),
            'pagos_pendientes'  => Database::fetchValue("SELECT COUNT(*) FROM pagos p INNER JOIN matriculas m ON m.id=p.matricula_id INNER JOIN estudiantes e ON e.id=m.estudiante_id WHERE e.institucion_id=? AND p.estado='pendiente'", [$instId]),
            'morosos'           => Database::fetchValue("SELECT COUNT(DISTINCT m.estudiante_id) FROM cuentas_por_cobrar cpc INNER JOIN matriculas m ON m.id=cpc.matricula_id INNER JOIN estudiantes e ON e.id=m.estudiante_id WHERE e.institucion_id=? AND m.anio_lectivo_id=? AND cpc.estado IN ('pendiente','parcial') AND cpc.fecha_vencimiento < CURDATE()", [$instId, $anioId]),
            'inscripciones_nuevas' => Database::fetchValue("SELECT COUNT(*) FROM inscripciones WHERE institucion_id=? AND estado='nueva'", [$instId]),
        ];

        $ingresosMes = (new Pago())->getResumenMensual($instId, $anioId);
        $ultimosPagos = Database::fetchAll("
            SELECT p.*, CONCAT(e.nombres,' ',e.apellidos) AS estudiante, cc.nombre AS concepto
            FROM pagos p
            INNER JOIN cuentas_por_cobrar cpc ON cpc.id=p.cuenta_id
            INNER JOIN conceptos_cobro cc ON cc.id=cpc.concepto_id
            INNER JOIN matriculas m ON m.id=p.matricula_id
            INNER JOIN estudiantes e ON e.id=m.estudiante_id
            WHERE e.institucion_id=? ORDER BY p.created_at DESC LIMIT 8
        ", [$instId]);

        $noLeidas = (new Notificacion())->countNoLeidas(Session::userId());

        $this->view('admin/dashboard', [
            'stats'       => $stats,
            'ingresosMes' => $ingresosMes,
            'ultimosPagos'=> $ultimosPagos,
            'noLeidas'    => $noLeidas,
            'pageTitle'   => 'Panel Administrativo'
        ]);
    }
}