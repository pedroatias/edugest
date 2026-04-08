<?php
/**
 * EduGest - Sistema de Gestión Académica
 * Entry Point & Router
 */

define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('VERSION', '1.0.0');

// Load configuration
require_once APP_PATH . '/config/config.php';
require_once APP_PATH . '/config/database.php';

// Load core classes
require_once APP_PATH . '/core/Helpers.php';
require_once APP_PATH . '/core/Session.php';
require_once APP_PATH . '/core/Model.php';
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Router.php';

// Start session
Session::start();

// Initialize router
$router = new Router();

// ============================================================
// AUTH ROUTES (public)
// ============================================================
$router->get('', 'auth/AuthController', 'loginView');
$router->get('login', 'auth/AuthController', 'loginView');
$router->post('login', 'auth/AuthController', 'login');
$router->get('logout', 'auth/AuthController', 'logout');
$router->get('registro', 'auth/AuthController', 'registerView');
$router->post('registro', 'auth/AuthController', 'register');
$router->get('recuperar', 'auth/AuthController', 'recoverView');
$router->post('recuperar', 'auth/AuthController', 'recover');

// Inscripcion publica
$router->get('inscripcion', 'auth/AuthController', 'inscripcionView');
$router->post('inscripcion', 'auth/AuthController', 'inscripcion');
$router->get('inscripcion/estado', 'auth/AuthController', 'inscripcionEstado');

// ============================================================
// PARENT PORTAL ROUTES
// ============================================================
$router->get('inicio', 'padre/DashboardController', 'index');
$router->get('academico', 'padre/AcademicoController', 'index');
$router->get('academico/estudiante', 'padre/AcademicoController', 'estudiante');
$router->get('academico/notas', 'padre/AcademicoController', 'notas');
$router->get('academico/acumulado', 'padre/AcademicoController', 'acumulado');
$router->get('academico/horario', 'padre/AcademicoController', 'horario');
$router->get('academico/calendario', 'padre/AcademicoController', 'calendario');
$router->get('pagos', 'padre/PagosController', 'index');
$router->get('pagos/historial', 'padre/PagosController', 'historial');
$router->post('pagos/iniciar', 'padre/PagosController', 'iniciarPago');
$router->get('pagos/confirmacion', 'padre/PagosController', 'confirmacion');
$router->get('boletines', 'padre/BoletinesController', 'index');
$router->get('boletines/descargar', 'padre/BoletinesController', 'descargar');
$router->get('matriculas', 'padre/MatriculasController', 'index');
$router->get('matriculas/circulares', 'padre/MatriculasController', 'circulares');

// ============================================================
// TEACHER PORTAL ROUTES
// ============================================================
$router->get('docente', 'docente/DashboardController', 'index');
$router->get('docente/notas', 'docente/NotasController', 'index');
$router->get('docente/notas/seccion', 'docente/NotasController', 'seccion');
$router->post('docente/notas/guardar', 'docente/NotasController', 'guardar');
$router->post('docente/notas/guardar-masivo', 'docente/NotasController', 'guardarMasivo');
$router->get('docente/calendario', 'docente/CalendarioController', 'index');
$router->post('docente/calendario/crear', 'docente/CalendarioController', 'crear');
$router->post('docente/calendario/editar', 'docente/CalendarioController', 'editar');
$router->post('docente/calendario/eliminar', 'docente/CalendarioController', 'eliminar');
$router->get('docente/calendario/eventos', 'docente/CalendarioController', 'eventos');
$router->get('docente/avisos', 'docente/AvisosController', 'index');
$router->post('docente/avisos/enviar', 'docente/AvisosController', 'enviar');
$router->get('docente/mis-grupos', 'docente/DashboardController', 'misGrupos');
$router->get('docente/horario', 'docente/DashboardController', 'horario');

// ============================================================
// ADMIN PANEL ROUTES
// ============================================================
$router->get('admin', 'admin/DashboardController', 'index');
$router->get('admin/estadisticas', 'admin/DashboardController', 'estadisticas');

// Estudiantes
$router->get('admin/estudiantes', 'admin/EstudiantesController', 'index');
$router->get('admin/estudiantes/nuevo', 'admin/EstudiantesController', 'nuevo');
$router->post('admin/estudiantes/crear', 'admin/EstudiantesController', 'crear');
$router->get('admin/estudiantes/editar', 'admin/EstudiantesController', 'editar');
$router->post('admin/estudiantes/actualizar', 'admin/EstudiantesController', 'actualizar');
$router->post('admin/estudiantes/eliminar', 'admin/EstudiantesController', 'eliminar');
$router->get('admin/estudiantes/ficha', 'admin/EstudiantesController', 'ficha');
$router->post('admin/estudiantes/importar', 'admin/EstudiantesController', 'importar');

// Docentes
$router->get('admin/docentes', 'admin/DocentesController', 'index');
$router->get('admin/docentes/nuevo', 'admin/DocentesController', 'nuevo');
$router->post('admin/docentes/crear', 'admin/DocentesController', 'crear');
$router->get('admin/docentes/editar', 'admin/DocentesController', 'editar');
$router->post('admin/docentes/actualizar', 'admin/DocentesController', 'actualizar');

// Grupos y Secciones
$router->get('admin/grados', 'admin/GruposController', 'grados');
$router->post('admin/grados/crear', 'admin/GruposController', 'crearGrado');
$router->post('admin/grados/actualizar', 'admin/GruposController', 'actualizarGrado');
$router->get('admin/secciones', 'admin/GruposController', 'secciones');
$router->post('admin/secciones/crear', 'admin/GruposController', 'crearSeccion');
$router->post('admin/secciones/actualizar', 'admin/GruposController', 'actualizarSeccion');

// Asignaturas y Horarios
$router->get('admin/asignaturas', 'admin/AsignaturasController', 'index');
$router->post('admin/asignaturas/crear', 'admin/AsignaturasController', 'crear');
$router->post('admin/asignaturas/actualizar', 'admin/AsignaturasController', 'actualizar');
$router->get('admin/horarios', 'admin/HorariosController', 'index');
$router->post('admin/horarios/guardar', 'admin/HorariosController', 'guardar');
$router->get('admin/asignaciones', 'admin/HorariosController', 'asignaciones');
$router->post('admin/asignaciones/crear', 'admin/HorariosController', 'crearAsignacion');

// Períodos y estructura de notas
$router->get('admin/periodos', 'admin/PeriodosController', 'index');
$router->post('admin/periodos/crear', 'admin/PeriodosController', 'crear');
$router->post('admin/periodos/actualizar', 'admin/PeriodosController', 'actualizar');
$router->post('admin/periodos/toggle-notas', 'admin/PeriodosController', 'toggleNotas');
$router->get('admin/estructura-notas', 'admin/PeriodosController', 'estructuraNotas');
$router->post('admin/estructura-notas/guardar', 'admin/PeriodosController', 'guardarEstructura');

// Financiero
$router->get('admin/conceptos-cobro', 'admin/FinancieroController', 'conceptos');
$router->post('admin/conceptos-cobro/crear', 'admin/FinancieroController', 'crearConcepto');
$router->post('admin/conceptos-cobro/actualizar', 'admin/FinancieroController', 'actualizarConcepto');
$router->get('admin/cobros', 'admin/FinancieroController', 'cobros');
$router->post('admin/cobros/generar-masivo', 'admin/FinancieroController', 'generarMasivo');
$router->post('admin/cobros/generar-individual', 'admin/FinancieroController', 'generarIndividual');
$router->get('admin/cobros/estado-cuenta', 'admin/FinancieroController', 'estadoCuenta');
$router->get('admin/cobros/estado-cuenta/pdf', 'admin/FinancieroController', 'estadoCuentaPdf');

// Pagos
$router->get('admin/pagos', 'admin/PagosController', 'index');
$router->get('admin/pagos/nuevo', 'admin/PagosController', 'nuevo');
$router->post('admin/pagos/registrar', 'admin/PagosController', 'registrar');
$router->get('admin/pagos/validacion', 'admin/PagosController', 'validacion');
$router->post('admin/pagos/verificar', 'admin/PagosController', 'verificar');
$router->post('admin/pagos/rechazar', 'admin/PagosController', 'rechazar');
$router->post('admin/pagos/anular', 'admin/PagosController', 'anular');
$router->get('admin/reportes/morosidad', 'admin/ReportesController', 'morosidad');
$router->get('admin/reportes/morosidad/pdf', 'admin/ReportesController', 'morosidadPdf');
$router->get('admin/reportes/morosidad/excel', 'admin/ReportesController', 'morosidadExcel');
$router->post('admin/reportes/notificar-morosos', 'admin/ReportesController', 'notificarMorosos');
$router->get('admin/reportes/ingresos', 'admin/ReportesController', 'ingresos');

// Boletines
$router->get('admin/boletines', 'admin/BoletinesController', 'index');
$router->post('admin/boletines/generar', 'admin/BoletinesController', 'generar');
$router->post('admin/boletines/publicar', 'admin/BoletinesController', 'publicar');
$router->get('admin/boletines/preview', 'admin/BoletinesController', 'preview');

// Circulares
$router->get('admin/circulares', 'admin/CircularesController', 'index');
$router->post('admin/circulares/crear', 'admin/CircularesController', 'crear');
$router->post('admin/circulares/actualizar', 'admin/CircularesController', 'actualizar');
$router->post('admin/circulares/eliminar', 'admin/CircularesController', 'eliminar');

// Inscripciones
$router->get('admin/inscripciones', 'admin/InscripcionesController', 'index');
$router->get('admin/inscripciones/ver', 'admin/InscripcionesController', 'ver');
$router->post('admin/inscripciones/cambiar-estado', 'admin/InscripcionesController', 'cambiarEstado');
$router->post('admin/inscripciones/convertir', 'admin/InscripcionesController', 'convertirEstudiante');

// Notificaciones
$router->get('admin/notificaciones', 'admin/NotificacionesController', 'index');
$router->post('admin/notificaciones/enviar', 'admin/NotificacionesController', 'enviar');

// Configuración
$router->get('admin/configuracion', 'admin/ConfiguracionController', 'index');
$router->post('admin/configuracion/actualizar', 'admin/ConfiguracionController', 'actualizar');
$router->get('admin/anios-lectivos', 'admin/ConfiguracionController', 'aniosLectivos');
$router->post('admin/anios-lectivos/crear', 'admin/ConfiguracionController', 'crearAnio');
$router->post('admin/anios-lectivos/activar', 'admin/ConfiguracionController', 'activarAnio');
$router->get('admin/usuarios', 'admin/ConfiguracionController', 'usuarios');
$router->post('admin/usuarios/crear', 'admin/ConfiguracionController', 'crearUsuario');
$router->post('admin/usuarios/actualizar', 'admin/ConfiguracionController', 'actualizarUsuario');

// ============================================================
// API / AJAX ROUTES
// ============================================================
$router->post('api/notificaciones/suscribir', 'api/NotificacionesController', 'suscribir');
$router->get('api/notificaciones/listar', 'api/NotificacionesController', 'listar');
$router->post('api/notificaciones/marcar-leida', 'api/NotificacionesController', 'marcarLeida');
$router->post('api/pagos/webhook', 'api/PagosController', 'webhook');
$router->get('api/sesion/verificar', 'api/SesionController', 'verificar');
$router->get('api/calendario/eventos', 'api/CalendarioController', 'eventos');
$router->get('api/estudiantes/buscar', 'api/EstudiantesController', 'buscar');

// Dispatch the request
$router->dispatch();
