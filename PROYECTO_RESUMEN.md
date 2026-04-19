# EduGest — Resumen del Proyecto
**Repositorio:** https://github.com/pedroatias/edugest | **Rama:** main | **Actualizado:** 18/04/2026

---

## Contexto y Origen

EduGest es un sistema de gestion academica escolar desarrollado como replica funcional del sistema **PorEduCar** (poreducar.angesoft.com.co). Fue construido por IA copiando la funcionalidad del sitio web PorEduCar. El modulo de boletines y notas quedo pendiente porque estaba deshabilitado en ese momento. En la sesion del 18/04/2026 se completo dicho modulo.

---

## Arquitectura del Proyecto

Patron **MVC propio en PHP puro** (sin framework externo). El router central `index.php` define todas las rutas. Los controladores pasan variables directamente a las vistas via `extract()` — NO como `$data['xxx']`.

```
/
├── index.php                  Router central (todas las rutas)
├── install.php                Instalador inicial
├── app/
│   ├── config/                Configuracion DB, constantes
│   ├── core/                  Controller base, Database, Session, Router
│   ├── models/                ORM ligero: Calificacion, Boletin, Estudiante, etc.
│   ├── controllers/
│   │   ├── admin/             Panel administrativo (usa AdminLTE)
│   │   ├── docente/           Portal docentes (Bootstrap 5)
│   │   ├── padre/             Portal padres (Bootstrap 5)
│   │   ├── auth/              Login/logout
│   │   └── api/               Endpoints auxiliares JSON
│   ├── views/
│   │   ├── layouts/           layouts/admin.php, docente.php, padre.php
│   │   ├── admin/             Vistas panel admin (AdminLTE)
│   │   ├── docente/           Vistas portal docente
│   │   └── padre/             Vistas portal padre
│   └── helpers/               PdfHelper.php, NotificationHelper.php
├── sql/                       Schema SQL completo (~507 lineas)
├── assets/                    CSS, JS, Bootstrap, AdminLTE, FontAwesome
└── uploads/boletines/         PDFs de boletines generados
```

---

## Modulos Implementados

### Autenticacion (`auth/`)

Login con roles: `admin`, `docente`, `padre`. Session via `Session::userId()`, `Session::anioLectivoId()`, `Session::institucionId()`.

### Panel Admin (`admin/`)

Controladores: Dashboard, Estudiantes, Inscripciones, Grupos, Grados, Secciones, Horarios, Docentes, Asignaturas, **Periodos**, Financiero, **Boletines**, Configuracion, Reportes, Notificaciones, Circulares.

**Boletines Admin** (`BoletinesController`):
- `index()` — lista estudiantes filtrables por grado/periodo
- `generar()` — genera PDF con `PdfHelper::generarBoletin()`, guarda en `uploads/boletines/`
- `publicar()` — marca `boletines.disponible = 1` + notifica padres via `NotificationHelper`

**Periodos Admin** (`PeriodosController`): incluye `toggleNotas()` para habilitar/deshabilitar acceso de docentes a notas por periodo.

### Portal Docente (`docente/`)

**Notas** (`NotasController`):
- `index()` — muestra las asignaciones del docente (seccion + asignatura)
- `seccion()` — carga estudiantes + notas existentes en JSON (AJAX)
- `guardar()` — guarda una nota individual (auto-save cada 800ms al escribir)
- `guardarMasivo()` — guarda todas las notas del listado a la vez

Vista `docente/notas/index.php`: tabla interactiva con calculo de definitiva en tiempo real (ponderacion por porcentaje de cada componente).

**Calendario** (`CalendarioController`): gestion de eventos academicos con CRUD completo.

### Portal Padre (`padre/`)

- **Academico**: resumen de notas del estudiante por periodo con desglose por asignatura
- **Boletines**: timeline de boletines por periodo, descarga de PDF cuando el admin lo publica
- **Financiero/Pagos**: estado de cuenta, historial de pagos y cuentas por cobrar
- **Calendario**: visualizacion de eventos del colegio
- Soporte **multi-hijo**: selector cuando el padre tiene mas de un hijo matriculado

---

## Modelos y Tablas SQL Clave

| Modelo | Tabla | Descripcion |
|---|---|---|
| `Calificacion` | `calificaciones` | Notas por componente, matricula, asignatura y periodo |
| `Boletin` | `boletines` | Boletines generados por matricula/periodo (disponible, archivo PDF) |
| `Estudiante` | `estudiantes` + `matriculas` | Datos del estudiante y su matricula activa |
| `Seccion` | `secciones` | Secciones/grupos escolares |
| `Notificacion` | `notificaciones` | Notificaciones para padres (via NotificationHelper) |
| `Pago` | `pagos` | Registro de pagos realizados |
| `CuentaPorCobrar` | `cuentas_por_cobrar` | Deudas pendientes de las familias |
| `Evento` | `eventos` | Eventos del calendario escolar |
| `Usuario` | `usuarios` | Usuarios del sistema (admin/docente/padre) |

**Tablas de configuracion importantes:**
- `estructura_notas` — Componentes de evaluacion (Ser, Saber, Hacer, etc.) con porcentajes por institucion
- `escala_valorativa` — Escala de calificacion de la institucion
- `periodos_academicos` — Periodos con campo `notas_habilitadas` (controla acceso docente)
- `anios_lectivos` — Ano lectivo activo de la institucion
- `asignacion_docentes` — Relacion docente <-> seccion <-> asignatura

---

## Helpers

- **`PdfHelper::generarBoletin($matriculaId, $periodoId)`** — Genera PDF con TCPDF. Guarda en `uploads/boletines/{matriculaId}_{periodoId}.pdf`
- **`NotificationHelper::notificarPadres($matriculaIds, $titulo, $mensaje)`** — Inserta notificaciones en tabla `notificaciones` para los padres de los estudiantes indicados

---

## Trabajo Realizado — Sesion 18/04/2026 (Completar Notas y Boletines)

Se crearon/corrigieron 4 archivos en 4 commits:

| Commit | Archivo | Accion |
|---|---|---|
| `7bcba24` | `app/controllers/padre/BoletinesController.php` | CREADO — metodos index() y descargar(), soporte multi-hijo |
| `d961419` | `app/views/padre/boletines/index.php` | CREADO — timeline de boletines por periodo con descarga PDF |
| `d13d623` | `app/views/admin/boletines/index.php` | CORREGIDO — usaba $data['xxx'] pero el controller pasa variables directas; reescrito completo |
| `8a2c60f` | `app/helpers/NotificationHelper.php` | CREADO — requerido por BoletinesController::publicar() |

---

## Flujo Completo del Modulo Notas + Boletines

```
[ADMIN] Configura estructura_notas (componentes + porcentajes)
    |
    [ADMIN] Habilita periodo -> periodos_academicos.notas_habilitadas = 1
        |
        [DOCENTE] Ingresa notas por seccion/asignatura/periodo (auto-save AJAX)
            -> calificaciones (matricula_id, asignatura_id, periodo_id, estructura_nota_id, nota)
                |
                [ADMIN] Genera boletin PDF -> PdfHelper::generarBoletin() -> uploads/boletines/
                    -> boletines (matricula_id, periodo_id, archivo_pdf, generado_en)
                        |
                        [ADMIN] Publica boletin -> boletines.disponible = 1 + NotificationHelper
                            |
                            [PADRE] Ve boletines disponibles -> descarga PDF
                            ```

                            ---

                            ## Consideraciones para Despliegue

                            1. **PHP 8.0+** con PDO MySQL habilitado
                            2. **MySQL** con el schema importado desde `sql/edugest_schema.sql`
                            3. **TCPDF** instalado (via Composer o manualmente) para generacion de PDFs de boletines
                            4. Permisos de escritura en `uploads/boletines/`
                            5. Servidor web con `mod_rewrite` (Apache) o configuracion equivalente para rutas limpias
                            6. Ejecutar `install.php` para la configuracion inicial de la institucion

                            ---

                            *Documento generado el 19/04/2026 como referencia de trayectoria del proyecto EduGest.*
