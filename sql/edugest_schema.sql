-- ============================================================
-- EduGest - Schema de Base de Datos
-- Compatible con MySQL 5.7+ / MariaDB 10.3+
-- Ejecutar en cPanel: phpMyAdmin o MySQL Databases
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- instituciones
CREATE TABLE IF NOT EXISTS instituciones (
  id int(11) NOT NULL AUTO_INCREMENT,
  nombre varchar(200) NOT NULL,
  nit varchar(20) DEFAULT NULL,
  direccion varchar(300) DEFAULT NULL,
  telefono varchar(30) DEFAULT NULL,
  email varchar(100) DEFAULT NULL,
  logo varchar(255) DEFAULT NULL,
  color_primario varchar(10) DEFAULT '#1a73e8',
  color_secundario varchar(10) DEFAULT '#28a745',
  slogan varchar(255) DEFAULT NULL,
  activa tinyint(1) NOT NULL DEFAULT 1,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- anios_lectivos
CREATE TABLE IF NOT EXISTS anios_lectivos (
  id int(11) NOT NULL AUTO_INCREMENT,
  institucion_id int(11) NOT NULL,
  anio year(4) NOT NULL,
  nombre varchar(100) NOT NULL,
  fecha_inicio date DEFAULT NULL,
  fecha_fin date DEFAULT NULL,
  activo tinyint(1) NOT NULL DEFAULT 0,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY fk_al_inst (institucion_id),
  CONSTRAINT fk_al_inst FOREIGN KEY (institucion_id) REFERENCES instituciones (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- periodos_academicos
CREATE TABLE IF NOT EXISTS periodos_academicos (
  id int(11) NOT NULL AUTO_INCREMENT,
  anio_lectivo_id int(11) NOT NULL,
  nombre varchar(50) NOT NULL,
  numero tinyint(2) NOT NULL,
  fecha_inicio date DEFAULT NULL,
  fecha_fin date DEFAULT NULL,
  porcentaje decimal(5,2) DEFAULT 25.00,
  notas_habilitadas tinyint(1) NOT NULL DEFAULT 1,
  activo tinyint(1) NOT NULL DEFAULT 1,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY fk_pa_al (anio_lectivo_id),
  CONSTRAINT fk_pa_al FOREIGN KEY (anio_lectivo_id) REFERENCES anios_lectivos (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- grados
CREATE TABLE IF NOT EXISTS grados (
  id int(11) NOT NULL AUTO_INCREMENT,
  institucion_id int(11) NOT NULL,
  nombre varchar(50) NOT NULL,
  nivel varchar(50) DEFAULT NULL,
  orden int(3) DEFAULT 0,
  activo tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  KEY fk_gr_inst (institucion_id),
  CONSTRAINT fk_gr_inst FOREIGN KEY (institucion_id) REFERENCES instituciones (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- secciones
CREATE TABLE IF NOT EXISTS secciones (
  id int(11) NOT NULL AUTO_INCREMENT,
  grado_id int(11) NOT NULL,
  anio_lectivo_id int(11) NOT NULL,
  nombre varchar(10) NOT NULL,
  nombre_completo varchar(30) DEFAULT NULL,
  capacidad_max int(3) DEFAULT 35,
  docente_director_id int(11) DEFAULT NULL,
  activa tinyint(1) NOT NULL DEFAULT 1,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY fk_sec_grado (grado_id),
  KEY fk_sec_al (anio_lectivo_id),
  CONSTRAINT fk_sec_grado FOREIGN KEY (grado_id) REFERENCES grados (id),
  CONSTRAINT fk_sec_al FOREIGN KEY (anio_lectivo_id) REFERENCES anios_lectivos (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- usuarios
CREATE TABLE IF NOT EXISTS usuarios (
  id int(11) NOT NULL AUTO_INCREMENT,
  institucion_id int(11) NOT NULL,
  numero_documento varchar(30) NOT NULL,
  tipo_documento enum('CC','TI','CE','PASAPORTE','NIT') DEFAULT 'CC',
  username varchar(80) NOT NULL,
  password varchar(255) NOT NULL,
  rol enum('admin','docente','padre','estudiante') NOT NULL,
  nombres varchar(100) NOT NULL,
  apellidos varchar(100) NOT NULL,
  email varchar(150) DEFAULT NULL,
  telefono varchar(30) DEFAULT NULL,
  foto varchar(255) DEFAULT NULL,
  activo tinyint(1) NOT NULL DEFAULT 1,
  ultimo_acceso datetime DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_username (username),
  KEY fk_usr_inst (institucion_id),
  CONSTRAINT fk_usr_inst FOREIGN KEY (institucion_id) REFERENCES instituciones (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- estudiantes
CREATE TABLE IF NOT EXISTS estudiantes (
  id int(11) NOT NULL AUTO_INCREMENT,
  usuario_id int(11) DEFAULT NULL,
  institucion_id int(11) NOT NULL,
  codigo varchar(20) NOT NULL,
  numero_documento varchar(30) DEFAULT NULL,
  tipo_documento enum('CC','TI','CE','PASAPORTE','RC') DEFAULT 'TI',
  nombres varchar(100) NOT NULL,
  apellidos varchar(100) NOT NULL,
  fecha_nacimiento date DEFAULT NULL,
  genero enum('M','F','O') DEFAULT NULL,
  foto varchar(255) DEFAULT NULL,
  email varchar(150) DEFAULT NULL,
  telefono varchar(30) DEFAULT NULL,
  direccion varchar(300) DEFAULT NULL,
  ciudad varchar(100) DEFAULT NULL,
  estado enum('activo','retirado','egresado','inscrito') DEFAULT 'activo',
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY fk_est_inst (institucion_id),
  CONSTRAINT fk_est_inst FOREIGN KEY (institucion_id) REFERENCES instituciones (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- representantes
CREATE TABLE IF NOT EXISTS representantes (
  id int(11) NOT NULL AUTO_INCREMENT,
  usuario_id int(11) DEFAULT NULL,
  estudiante_id int(11) NOT NULL,
  nombres varchar(100) NOT NULL,
  apellidos varchar(100) NOT NULL,
  numero_documento varchar(30) DEFAULT NULL,
  tipo_documento enum('CC','CE','PASAPORTE') DEFAULT 'CC',
  parentesco varchar(50) DEFAULT NULL,
  email varchar(150) DEFAULT NULL,
  telefono varchar(30) DEFAULT NULL,
  telefono2 varchar(30) DEFAULT NULL,
  ocupacion varchar(100) DEFAULT NULL,
  empresa varchar(150) DEFAULT NULL,
  direccion varchar(300) DEFAULT NULL,
  es_acudiente_principal tinyint(1) DEFAULT 0,
  activo tinyint(1) DEFAULT 1,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY fk_rep_est (estudiante_id),
  CONSTRAINT fk_rep_est FOREIGN KEY (estudiante_id) REFERENCES estudiantes (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- matriculas
CREATE TABLE IF NOT EXISTS matriculas (
  id int(11) NOT NULL AUTO_INCREMENT,
  estudiante_id int(11) NOT NULL,
  seccion_id int(11) NOT NULL,
  anio_lectivo_id int(11) NOT NULL,
  fecha_matricula date DEFAULT NULL,
  estado enum('matriculado','retirado','promovido','reprobado') DEFAULT 'matriculado',
  observaciones text DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_matricula (estudiante_id, anio_lectivo_id),
  KEY fk_mat_est (estudiante_id),
  KEY fk_mat_sec (seccion_id),
  CONSTRAINT fk_mat_est FOREIGN KEY (estudiante_id) REFERENCES estudiantes (id),
  CONSTRAINT fk_mat_sec FOREIGN KEY (seccion_id) REFERENCES secciones (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- asignaturas
CREATE TABLE IF NOT EXISTS asignaturas (
  id int(11) NOT NULL AUTO_INCREMENT,
  institucion_id int(11) NOT NULL,
  nombre varchar(100) NOT NULL,
  codigo varchar(20) DEFAULT NULL,
  color varchar(10) DEFAULT '#3498db',
  activa tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- docentes
CREATE TABLE IF NOT EXISTS docentes (
  id int(11) NOT NULL AUTO_INCREMENT,
  usuario_id int(11) NOT NULL,
  especialidad varchar(150) DEFAULT NULL,
  titulo varchar(150) DEFAULT NULL,
  fecha_ingreso date DEFAULT NULL,
  activo tinyint(1) DEFAULT 1,
  PRIMARY KEY (id),
  KEY fk_doc_usr (usuario_id),
  CONSTRAINT fk_doc_usr FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- asignacion_docentes
CREATE TABLE IF NOT EXISTS asignacion_docentes (
  id int(11) NOT NULL AUTO_INCREMENT,
  docente_id int(11) NOT NULL,
  asignatura_id int(11) NOT NULL,
  seccion_id int(11) NOT NULL,
  anio_lectivo_id int(11) NOT NULL,
  activa tinyint(1) DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uk_asignacion (docente_id, asignatura_id, seccion_id, anio_lectivo_id),
  KEY fk_ad_doc (docente_id),
  CONSTRAINT fk_ad_doc FOREIGN KEY (docente_id) REFERENCES docentes (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- horarios
CREATE TABLE IF NOT EXISTS horarios (
  id int(11) NOT NULL AUTO_INCREMENT,
  asignacion_id int(11) NOT NULL,
  dia_semana tinyint(1) NOT NULL COMMENT '1=Lunes...5=Viernes',
  bloque tinyint(2) NOT NULL,
  hora_inicio time DEFAULT NULL,
  hora_fin time DEFAULT NULL,
  PRIMARY KEY (id),
  KEY fk_hor_asig (asignacion_id),
  CONSTRAINT fk_hor_asig FOREIGN KEY (asignacion_id) REFERENCES asignacion_docentes (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- estructura_notas
CREATE TABLE IF NOT EXISTS estructura_notas (
  id int(11) NOT NULL AUTO_INCREMENT,
  institucion_id int(11) NOT NULL,
  nombre varchar(80) NOT NULL,
  porcentaje decimal(5,2) NOT NULL,
  orden tinyint(2) DEFAULT 1,
  activo tinyint(1) DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- escala_valorativa
CREATE TABLE IF NOT EXISTS escala_valorativa (
  id int(11) NOT NULL AUTO_INCREMENT,
  institucion_id int(11) NOT NULL,
  nombre varchar(50) NOT NULL,
  nota_minima decimal(4,2) NOT NULL,
  nota_maxima decimal(4,2) NOT NULL,
  color varchar(10) DEFAULT '#999999',
  orden tinyint(2) DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- calificaciones
CREATE TABLE IF NOT EXISTS calificaciones (
  id int(11) NOT NULL AUTO_INCREMENT,
  matricula_id int(11) NOT NULL,
  asignatura_id int(11) NOT NULL,
  periodo_id int(11) NOT NULL,
  estructura_nota_id int(11) NOT NULL,
  nota decimal(4,2) NOT NULL DEFAULT 0.00,
  observacion text DEFAULT NULL,
  docente_id int(11) DEFAULT NULL,
  fecha_registro datetime DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_calificacion (matricula_id, asignatura_id, periodo_id, estructura_nota_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- eventos_calendario
CREATE TABLE IF NOT EXISTS eventos_calendario (
  id int(11) NOT NULL AUTO_INCREMENT,
  institucion_id int(11) NOT NULL,
  creado_por int(11) NOT NULL,
  titulo varchar(200) NOT NULL,
  descripcion text DEFAULT NULL,
  tipo enum('aviso','tarea','evaluacion','evento','festivo','reunion') DEFAULT 'aviso',
  fecha_inicio datetime NOT NULL,
  fecha_fin datetime DEFAULT NULL,
  todo_el_dia tinyint(1) DEFAULT 0,
  color varchar(10) DEFAULT '#3498db',
  audiencia enum('todos','seccion','grado','docentes','padres') DEFAULT 'todos',
  seccion_id int(11) DEFAULT NULL,
  grado_id int(11) DEFAULT NULL,
  asignatura_id int(11) DEFAULT NULL,
  anio_lectivo_id int(11) NOT NULL,
  activo tinyint(1) DEFAULT 1,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- avisos
CREATE TABLE IF NOT EXISTS avisos (
  id int(11) NOT NULL AUTO_INCREMENT,
  institucion_id int(11) NOT NULL,
  creado_por int(11) NOT NULL,
  titulo varchar(200) NOT NULL,
  contenido text NOT NULL,
  tipo enum('general','urgente','informativo') DEFAULT 'general',
  audiencia enum('todos','seccion','grado','padre_especifico') DEFAULT 'todos',
  seccion_id int(11) DEFAULT NULL,
  grado_id int(11) DEFAULT NULL,
  estudiante_id int(11) DEFAULT NULL,
  anio_lectivo_id int(11) NOT NULL,
  activo tinyint(1) DEFAULT 1,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- conceptos_cobro
CREATE TABLE IF NOT EXISTS conceptos_cobro (
  id int(11) NOT NULL AUTO_INCREMENT,
  institucion_id int(11) NOT NULL,
  nombre varchar(150) NOT NULL,
  descripcion text DEFAULT NULL,
  tipo enum('pension','matricula','otro') DEFAULT 'otro',
  valor decimal(12,2) NOT NULL DEFAULT 0.00,
  aplica_interes_mora tinyint(1) DEFAULT 0,
  porcentaje_mora decimal(5,2) DEFAULT 0.00,
  activo tinyint(1) DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- cuentas_por_cobrar
CREATE TABLE IF NOT EXISTS cuentas_por_cobrar (
  id int(11) NOT NULL AUTO_INCREMENT,
  matricula_id int(11) NOT NULL,
  concepto_id int(11) NOT NULL,
  anio_lectivo_id int(11) NOT NULL,
  descripcion varchar(255) DEFAULT NULL,
  valor decimal(12,2) NOT NULL,
  descuento decimal(12,2) DEFAULT 0.00,
  interes_mora decimal(12,2) DEFAULT 0.00,
  total decimal(12,2) NOT NULL,
  fecha_vencimiento date NOT NULL,
  estado enum('pendiente','pagado','parcial','anulado') DEFAULT 'pendiente',
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- pagos
CREATE TABLE IF NOT EXISTS pagos (
  id int(11) NOT NULL AUTO_INCREMENT,
  cuenta_id int(11) NOT NULL,
  matricula_id int(11) NOT NULL,
  referencia varchar(100) DEFAULT NULL,
  metodo_pago enum('efectivo','transferencia','pasarela','otro') DEFAULT 'efectivo',
  pasarela varchar(50) DEFAULT NULL,
  referencia_pasarela varchar(200) DEFAULT NULL,
  valor_pagado decimal(12,2) NOT NULL,
  fecha_pago datetime NOT NULL,
  estado enum('pendiente','verificado','rechazado','anulado') DEFAULT 'pendiente',
  comprobante varchar(255) DEFAULT NULL,
  registrado_por int(11) DEFAULT NULL,
  observaciones text DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- push_subscriptions
CREATE TABLE IF NOT EXISTS push_subscriptions (
  id int(11) NOT NULL AUTO_INCREMENT,
  usuario_id int(11) NOT NULL,
  endpoint text NOT NULL,
  p256dh text NOT NULL,
  auth varchar(255) NOT NULL,
  user_agent varchar(300) DEFAULT NULL,
  activa tinyint(1) DEFAULT 1,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- notificaciones
CREATE TABLE IF NOT EXISTS notificaciones (
  id int(11) NOT NULL AUTO_INCREMENT,
  usuario_id int(11) NOT NULL,
  titulo varchar(200) NOT NULL,
  mensaje text NOT NULL,
  tipo enum('evento','pago','boletin','circular','aviso','sistema') DEFAULT 'sistema',
  url varchar(300) DEFAULT NULL,
  leida tinyint(1) DEFAULT 0,
  push_enviado tinyint(1) DEFAULT 0,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY fk_noti_usr (usuario_id),
  CONSTRAINT fk_noti_usr FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- circulares
CREATE TABLE IF NOT EXISTS circulares (
  id int(11) NOT NULL AUTO_INCREMENT,
  institucion_id int(11) NOT NULL,
  titulo varchar(200) NOT NULL,
  descripcion text DEFAULT NULL,
  archivo varchar(255) DEFAULT NULL,
  tipo enum('matricula','academica','financiera','general') DEFAULT 'general',
  anio_lectivo_id int(11) DEFAULT NULL,
  activa tinyint(1) DEFAULT 1,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- boletines
CREATE TABLE IF NOT EXISTS boletines (
  id int(11) NOT NULL AUTO_INCREMENT,
  matricula_id int(11) NOT NULL,
  periodo_id int(11) NOT NULL,
  archivo_pdf varchar(255) DEFAULT NULL,
  fecha_generacion datetime DEFAULT NULL,
  fecha_disponible date NOT NULL DEFAULT (CURDATE()),
  disponible tinyint(1) DEFAULT 0,
  generado tinyint(1) DEFAULT 0,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_boletin (matricula_id, periodo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- inscripciones
CREATE TABLE IF NOT EXISTS inscripciones (
  id int(11) NOT NULL AUTO_INCREMENT,
  institucion_id int(11) NOT NULL,
  anio_lectivo_id int(11) NOT NULL,
  codigo_solicitud varchar(20) NOT NULL,
  nombres varchar(100) NOT NULL,
  apellidos varchar(100) NOT NULL,
  fecha_nacimiento date DEFAULT NULL,
  genero enum('M','F','O') DEFAULT NULL,
  numero_documento varchar(30) DEFAULT NULL,
  tipo_documento enum('CC','TI','CE','PASAPORTE','RC') DEFAULT 'TI',
  grado_solicitado_id int(11) DEFAULT NULL,
  rep_nombres varchar(100) DEFAULT NULL,
  rep_apellidos varchar(100) DEFAULT NULL,
  rep_documento varchar(30) DEFAULT NULL,
  rep_parentesco varchar(50) DEFAULT NULL,
  rep_email varchar(150) DEFAULT NULL,
  rep_telefono varchar(30) DEFAULT NULL,
  estado enum('nueva','en_revision','aprobada','rechazada','matriculado') DEFAULT 'nueva',
  observaciones text DEFAULT NULL,
  revisado_por int(11) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_codigo_solicitud (codigo_solicitud)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- documentos_inscripcion
CREATE TABLE IF NOT EXISTS documentos_inscripcion (
  id int(11) NOT NULL AUTO_INCREMENT,
  inscripcion_id int(11) NOT NULL,
  tipo_documento varchar(100) NOT NULL,
  archivo varchar(255) NOT NULL,
  nombre_original varchar(255) DEFAULT NULL,
  estado enum('pendiente','verificado','rechazado') DEFAULT 'pendiente',
  uploaded_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY fk_di_ins (inscripcion_id),
  CONSTRAINT fk_di_ins FOREIGN KEY (inscripcion_id) REFERENCES inscripciones (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DATOS INICIALES DE EJEMPLO
-- ============================================================

INSERT IGNORE INTO instituciones (id, nombre, nit, email, color_primario, color_secundario) VALUES
(1, 'Mi Institucion Educativa', '900.000.000-0', 'admin@miinstituto.edu', '#1a73e8', '#28a745');

INSERT IGNORE INTO anios_lectivos (id, institucion_id, anio, nombre, fecha_inicio, fecha_fin, activo) VALUES
(1, 1, 2026, 'Ano Lectivo 2026', '2026-01-15', '2026-11-30', 1);

INSERT IGNORE INTO periodos_academicos (id, anio_lectivo_id, nombre, numero, fecha_inicio, fecha_fin, porcentaje) VALUES
(1, 1, '1 Periodo', 1, '2026-01-15', '2026-03-28', 25.00),
(2, 1, '2 Periodo', 2, '2026-04-01', '2026-06-13', 25.00),
(3, 1, '3 Periodo', 3, '2026-07-13', '2026-09-18', 25.00),
(4, 1, '4 Periodo', 4, '2026-09-21', '2026-11-30', 25.00);

INSERT IGNORE INTO escala_valorativa (institucion_id, nombre, nota_minima, nota_maxima, color, orden) VALUES
(1, 'Bajo',     0.00, 2.99, '#e74c3c', 1),
(1, 'Basico',   3.00, 3.59, '#f39c12', 2),
(1, 'Alto',     3.60, 4.49, '#27ae60', 3),
(1, 'Superior', 4.50, 5.00, '#2980b9', 4);

INSERT IGNORE INTO estructura_notas (institucion_id, nombre, porcentaje, orden) VALUES
(1, 'Ser',      20.00, 1),
(1, 'Saber',    40.00, 2),
(1, 'Hacer',    30.00, 3),
(1, 'Convivir', 10.00, 4);

INSERT IGNORE INTO grados (id, institucion_id, nombre, nivel, orden) VALUES
(1,  1, 'Preescolar', 'Preescolar', 0),
(2,  1, '1', 'Primaria', 1),
(3,  1, '2', 'Primaria', 2),
(4,  1, '3', 'Primaria', 3),
(5,  1, '4', 'Primaria', 4),
(6,  1, '5', 'Primaria', 5),
(7,  1, '6', 'Secundaria', 6),
(8,  1, '7', 'Secundaria', 7),
(9,  1, '8', 'Secundaria', 8),
(10, 1, '9', 'Secundaria', 9),
(11, 1, '10', 'Media', 10),
(12, 1, '11', 'Media', 11);

-- Admin user (password: Admin123!)
INSERT IGNORE INTO usuarios (id, institucion_id, numero_documento, username, password, rol, nombres, apellidos, email) VALUES
(1, 1, '00000001', 'admin', '$2y$12$XdERHYUvC6DYaREWqv9E8O1Xm3N8BpCYhiCIjW7w2mD0Uq3vMKiWe', 'admin', 'Administrador', 'Sistema', 'admin@miinstituto.edu');