-- MySQL dump 10.13  Distrib 5.7.24, for Win64 (x86_64)
--
-- Host: localhost    Database: biblioteca
-- ------------------------------------------------------
-- Server version	5.7.24

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `maquinagrupo`
--

/*!40000 ALTER TABLE `maquinagrupo` DISABLE KEYS */;
INSERT INTO `maquinagrupo` (`id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (1,'Extrusora','Extrusora',1,NULL,'2025-03-20 18:32:09','2025-03-20 18:32:09',NULL);
INSERT INTO `maquinagrupo` (`id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (2,'Impresora','Impresora',1,NULL,'2025-03-20 18:32:22','2025-03-20 18:32:22',NULL);
INSERT INTO `maquinagrupo` (`id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (3,'Selladora','Selladora',1,NULL,'2025-03-20 18:32:32','2025-03-20 18:33:31',NULL);
/*!40000 ALTER TABLE `maquinagrupo` ENABLE KEYS */;

--
-- Dumping data for table `etapaprod`
--

/*!40000 ALTER TABLE `etapaprod` DISABLE KEYS */;
INSERT INTO `etapaprod` (`id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `deleted_at`, `created_at`, `updated_at`) VALUES (1,'Materias primas','Programacion',1,NULL,NULL,'2025-06-11 14:50:44','2026-05-28 13:19:25');
INSERT INTO `etapaprod` (`id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `deleted_at`, `created_at`, `updated_at`) VALUES (2,'Mezclado','Mezclas',1,NULL,NULL,'2025-06-11 14:50:56','2025-06-11 14:50:56');
INSERT INTO `etapaprod` (`id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `deleted_at`, `created_at`, `updated_at`) VALUES (3,'Extrusion','Mezclado',1,NULL,NULL,'2025-06-11 14:51:06','2025-06-11 14:51:06');
INSERT INTO `etapaprod` (`id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `deleted_at`, `created_at`, `updated_at`) VALUES (4,'Impresion','Extrusion',1,NULL,NULL,'2025-06-11 14:51:18','2025-06-11 14:51:18');
INSERT INTO `etapaprod` (`id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `deleted_at`, `created_at`, `updated_at`) VALUES (5,'Sellado','Sellado',1,NULL,NULL,'2025-06-11 14:51:29','2025-06-11 14:51:29');
INSERT INTO `etapaprod` (`id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `deleted_at`, `created_at`, `updated_at`) VALUES (6,'x','x',1,NULL,NULL,'2025-06-11 14:51:39','2025-06-11 14:51:39');
INSERT INTO `etapaprod` (`id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `deleted_at`, `created_at`, `updated_at`) VALUES (8,'Corte','Corte',1,NULL,NULL,'2025-10-02 15:10:28','2025-10-02 15:10:28');
INSERT INTO `etapaprod` (`id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `deleted_at`, `created_at`, `updated_at`) VALUES (9,'Rebobinado','Rebobinado',1,NULL,NULL,'2026-05-28 13:22:18','2026-05-28 13:22:18');
INSERT INTO `etapaprod` (`id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `deleted_at`, `created_at`, `updated_at`) VALUES (10,'Etiquetado','Etiquetado',1,NULL,NULL,'2026-05-28 19:24:21','2026-05-28 19:24:21');
/*!40000 ALTER TABLE `etapaprod` ENABLE KEYS */;

--
-- Dumping data for table `atributo`
--

/*!40000 ALTER TABLE `atributo` DISABLE KEYS */;
INSERT INTO `atributo` (`id`, `nombre`, `desc`, `tipodato`, `longitud`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (1,'Ancho','Ancho',1,'10',1,NULL,'2025-03-20 20:45:04','2025-03-20 20:45:04',NULL);
INSERT INTO `atributo` (`id`, `nombre`, `desc`, `tipodato`, `longitud`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (2,'Largo','Largo',2,'10,2',1,NULL,'2025-03-21 12:29:32','2025-03-21 12:29:32',NULL);
/*!40000 ALTER TABLE `atributo` ENABLE KEYS */;

--
-- Dumping data for table `operario`
--

/*!40000 ALTER TABLE `operario` DISABLE KEYS */;
INSERT INTO `operario` (`id`, `nombre`, `desc`, `usuario_id`, `activo`, `usuariodel_id`, `deleted_at`, `created_at`, `updated_at`) VALUES (1,'Pato','Pato',1,0,NULL,NULL,'2025-04-02 20:32:59','2025-10-23 17:46:49');
INSERT INTO `operario` (`id`, `nombre`, `desc`, `usuario_id`, `activo`, `usuariodel_id`, `deleted_at`, `created_at`, `updated_at`) VALUES (2,'Fernando','Fernando',1,1,NULL,NULL,'2025-04-02 20:53:37','2025-04-02 20:53:37');
INSERT INTO `operario` (`id`, `nombre`, `desc`, `usuario_id`, `activo`, `usuariodel_id`, `deleted_at`, `created_at`, `updated_at`) VALUES (3,'Pedro','Pedro',1,1,NULL,NULL,'2025-04-03 16:00:43','2025-04-03 16:00:43');
INSERT INTO `operario` (`id`, `nombre`, `desc`, `usuario_id`, `activo`, `usuariodel_id`, `deleted_at`, `created_at`, `updated_at`) VALUES (4,'Rodolfo','Prepadador de Mezclas',1,1,NULL,NULL,'2025-10-20 18:58:45','2025-10-20 18:58:45');
INSERT INTO `operario` (`id`, `nombre`, `desc`, `usuario_id`, `activo`, `usuariodel_id`, `deleted_at`, `created_at`, `updated_at`) VALUES (5,'Operario Mezclado','Operario Mezclado',1,1,NULL,NULL,'2025-10-22 19:26:19','2025-10-22 19:26:19');
INSERT INTO `operario` (`id`, `nombre`, `desc`, `usuario_id`, `activo`, `usuariodel_id`, `deleted_at`, `created_at`, `updated_at`) VALUES (6,'Alberto Morales','Extrusion',1,1,NULL,NULL,'2025-11-03 18:20:32','2026-04-23 16:21:00');
INSERT INTO `operario` (`id`, `nombre`, `desc`, `usuario_id`, `activo`, `usuariodel_id`, `deleted_at`, `created_at`, `updated_at`) VALUES (7,'Erwin','Erwin',1,1,NULL,NULL,'2025-11-03 18:39:27','2025-11-03 18:39:27');
INSERT INTO `operario` (`id`, `nombre`, `desc`, `usuario_id`, `activo`, `usuariodel_id`, `deleted_at`, `created_at`, `updated_at`) VALUES (8,'Sellado','Sellado',1,1,NULL,NULL,'2025-11-03 20:01:31','2025-11-03 20:01:31');
INSERT INTO `operario` (`id`, `nombre`, `desc`, `usuario_id`, `activo`, `usuariodel_id`, `deleted_at`, `created_at`, `updated_at`) VALUES (9,'Juan Perez','Juan Perez',1,1,NULL,NULL,'2026-04-23 16:20:02','2026-04-23 16:20:02');
/*!40000 ALTER TABLE `operario` ENABLE KEYS */;

--
-- Dumping data for table `bin`
--

/*!40000 ALTER TABLE `bin` DISABLE KEYS */;
/*!40000 ALTER TABLE `bin` ENABLE KEYS */;

--
-- Dumping data for table `maquina`
--

/*!40000 ALTER TABLE `maquina` DISABLE KEYS */;
INSERT INTO `maquina` (`id`, `sucursal_id`, `maquinagrupo_id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (1,1,1,'E01','E01',1,NULL,'2025-03-20 19:32:48','2025-06-23 13:08:51',NULL);
INSERT INTO `maquina` (`id`, `sucursal_id`, `maquinagrupo_id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (2,1,1,'E02','E02',1,NULL,'2025-03-20 20:05:42','2025-03-20 20:05:42',NULL);
INSERT INTO `maquina` (`id`, `sucursal_id`, `maquinagrupo_id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (3,1,1,'E03','E03',1,NULL,'2025-03-21 14:59:50','2025-03-21 14:59:50',NULL);
INSERT INTO `maquina` (`id`, `sucursal_id`, `maquinagrupo_id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (4,1,1,'E04','E04',1,NULL,'2025-03-21 15:00:06','2025-03-21 15:00:06',NULL);
INSERT INTO `maquina` (`id`, `sucursal_id`, `maquinagrupo_id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (5,1,1,'E05','E05',1,NULL,'2025-03-21 15:00:22','2025-03-21 15:00:22',NULL);
INSERT INTO `maquina` (`id`, `sucursal_id`, `maquinagrupo_id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (6,1,1,'E06','E06',1,NULL,'2025-03-21 15:00:34','2025-03-21 15:00:34',NULL);
INSERT INTO `maquina` (`id`, `sucursal_id`, `maquinagrupo_id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (7,1,1,'E07','E07',1,NULL,'2025-03-21 15:00:47','2025-03-21 15:00:47',NULL);
INSERT INTO `maquina` (`id`, `sucursal_id`, `maquinagrupo_id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (8,1,1,'E08','E08',1,NULL,'2025-03-21 15:01:03','2025-03-21 15:01:03',NULL);
INSERT INTO `maquina` (`id`, `sucursal_id`, `maquinagrupo_id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (9,1,1,'E09','E09',1,NULL,'2025-03-21 15:01:22','2025-03-21 15:01:22',NULL);
INSERT INTO `maquina` (`id`, `sucursal_id`, `maquinagrupo_id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (10,1,1,'E10','E10',1,NULL,'2025-03-21 15:01:34','2025-03-21 15:01:34',NULL);
INSERT INTO `maquina` (`id`, `sucursal_id`, `maquinagrupo_id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (11,1,1,'E11','E11',1,NULL,'2025-03-21 15:01:49','2025-03-21 15:01:49',NULL);
INSERT INTO `maquina` (`id`, `sucursal_id`, `maquinagrupo_id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (12,1,2,'I01','I01',1,NULL,'2025-03-21 18:21:28','2025-03-21 18:21:28',NULL);
INSERT INTO `maquina` (`id`, `sucursal_id`, `maquinagrupo_id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (13,1,2,'I02','I02',1,NULL,'2025-03-21 18:21:42','2025-03-21 18:21:42',NULL);
INSERT INTO `maquina` (`id`, `sucursal_id`, `maquinagrupo_id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (14,1,3,'S01','S01',1,NULL,'2025-03-21 18:33:48','2025-03-21 18:33:48',NULL);
INSERT INTO `maquina` (`id`, `sucursal_id`, `maquinagrupo_id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (15,1,3,'S02','S02',1,NULL,'2025-03-21 18:34:03','2025-03-21 18:34:03',NULL);
INSERT INTO `maquina` (`id`, `sucursal_id`, `maquinagrupo_id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (16,1,3,'S03','S03',1,NULL,'2025-03-21 18:34:18','2025-03-21 18:34:18',NULL);
INSERT INTO `maquina` (`id`, `sucursal_id`, `maquinagrupo_id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (17,1,3,'S04','S04',1,NULL,'2025-03-21 18:34:32','2025-03-21 18:34:32',NULL);
INSERT INTO `maquina` (`id`, `sucursal_id`, `maquinagrupo_id`, `nombre`, `desc`, `usuario_id`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (18,1,1,'E12','E12',1,NULL,'2025-06-23 14:15:34','2025-06-23 14:15:34',NULL);
/*!40000 ALTER TABLE `maquina` ENABLE KEYS */;

--
-- Dumping data for table `maquinaetapaprod`
--

/*!40000 ALTER TABLE `maquinaetapaprod` DISABLE KEYS */;
INSERT INTO `maquinaetapaprod` (`id`, `maquina_id`, `etapaprod_id`, `created_at`, `updated_at`) VALUES (4,5,3,'2025-06-23 14:12:10','2025-06-23 14:12:10');
INSERT INTO `maquinaetapaprod` (`id`, `maquina_id`, `etapaprod_id`, `created_at`, `updated_at`) VALUES (5,6,3,'2025-06-23 14:12:21','2025-06-23 14:12:21');
INSERT INTO `maquinaetapaprod` (`id`, `maquina_id`, `etapaprod_id`, `created_at`, `updated_at`) VALUES (6,7,3,'2025-06-23 14:12:31','2025-06-23 14:12:31');
INSERT INTO `maquinaetapaprod` (`id`, `maquina_id`, `etapaprod_id`, `created_at`, `updated_at`) VALUES (7,8,3,'2025-06-23 14:12:40','2025-06-23 14:12:40');
INSERT INTO `maquinaetapaprod` (`id`, `maquina_id`, `etapaprod_id`, `created_at`, `updated_at`) VALUES (8,9,3,'2025-06-23 14:12:52','2025-06-23 14:12:52');
INSERT INTO `maquinaetapaprod` (`id`, `maquina_id`, `etapaprod_id`, `created_at`, `updated_at`) VALUES (9,10,3,'2025-06-23 14:13:02','2025-06-23 14:13:02');
INSERT INTO `maquinaetapaprod` (`id`, `maquina_id`, `etapaprod_id`, `created_at`, `updated_at`) VALUES (10,11,3,'2025-06-23 14:13:15','2025-06-23 14:13:15');
INSERT INTO `maquinaetapaprod` (`id`, `maquina_id`, `etapaprod_id`, `created_at`, `updated_at`) VALUES (18,14,5,'2025-06-23 14:37:43','2025-06-23 14:37:43');
INSERT INTO `maquinaetapaprod` (`id`, `maquina_id`, `etapaprod_id`, `created_at`, `updated_at`) VALUES (19,15,5,'2025-06-23 14:37:57','2025-06-23 14:37:57');
INSERT INTO `maquinaetapaprod` (`id`, `maquina_id`, `etapaprod_id`, `created_at`, `updated_at`) VALUES (20,16,5,'2025-06-23 14:38:17','2025-06-23 14:38:17');
INSERT INTO `maquinaetapaprod` (`id`, `maquina_id`, `etapaprod_id`, `created_at`, `updated_at`) VALUES (21,12,4,'2025-06-23 14:38:34','2025-06-23 14:38:34');
INSERT INTO `maquinaetapaprod` (`id`, `maquina_id`, `etapaprod_id`, `created_at`, `updated_at`) VALUES (22,4,3,'2025-06-23 14:38:46','2025-06-23 14:38:46');
INSERT INTO `maquinaetapaprod` (`id`, `maquina_id`, `etapaprod_id`, `created_at`, `updated_at`) VALUES (24,13,4,'2025-06-23 14:39:11','2025-06-23 14:39:11');
INSERT INTO `maquinaetapaprod` (`id`, `maquina_id`, `etapaprod_id`, `created_at`, `updated_at`) VALUES (25,17,5,'2025-06-23 14:39:30','2025-06-23 14:39:30');
INSERT INTO `maquinaetapaprod` (`id`, `maquina_id`, `etapaprod_id`, `created_at`, `updated_at`) VALUES (27,18,3,'2025-06-23 20:31:50','2025-06-23 20:31:50');
INSERT INTO `maquinaetapaprod` (`id`, `maquina_id`, `etapaprod_id`, `created_at`, `updated_at`) VALUES (28,1,3,'2025-09-23 15:49:13','2025-09-23 15:49:13');
INSERT INTO `maquinaetapaprod` (`id`, `maquina_id`, `etapaprod_id`, `created_at`, `updated_at`) VALUES (29,2,3,'2025-09-23 15:49:34','2025-09-23 15:49:34');
INSERT INTO `maquinaetapaprod` (`id`, `maquina_id`, `etapaprod_id`, `created_at`, `updated_at`) VALUES (30,3,3,'2025-09-23 15:49:44','2025-09-23 15:49:44');
/*!40000 ALTER TABLE `maquinaetapaprod` ENABLE KEYS */;

--
-- Dumping data for table `maquinaatributo`
--

/*!40000 ALTER TABLE `maquinaatributo` DISABLE KEYS */;
/*!40000 ALTER TABLE `maquinaatributo` ENABLE KEYS */;

--
-- Dumping data for table `areaproduccionsucetapaprod`
--

/*!40000 ALTER TABLE `areaproduccionsucetapaprod` DISABLE KEYS */;
INSERT INTO `areaproduccionsucetapaprod` (`id`, `areaproduccionsuc_id`, `etapaprod_id`, `unidadmedida_id`, `requiere_kg`, `requiere_cc`, `usa_matprima`, `orden`, `created_at`, `updated_at`) VALUES (1,1,1,NULL,1,0,0,1,'2025-10-01 18:13:01','2025-10-01 18:13:23');
INSERT INTO `areaproduccionsucetapaprod` (`id`, `areaproduccionsuc_id`, `etapaprod_id`, `unidadmedida_id`, `requiere_kg`, `requiere_cc`, `usa_matprima`, `orden`, `created_at`, `updated_at`) VALUES (2,1,2,NULL,1,0,0,2,'2025-10-01 18:13:01','2025-10-01 18:13:26');
INSERT INTO `areaproduccionsucetapaprod` (`id`, `areaproduccionsuc_id`, `etapaprod_id`, `unidadmedida_id`, `requiere_kg`, `requiere_cc`, `usa_matprima`, `orden`, `created_at`, `updated_at`) VALUES (3,1,3,NULL,1,0,0,3,'2025-10-01 18:13:01','2025-10-01 18:13:29');
INSERT INTO `areaproduccionsucetapaprod` (`id`, `areaproduccionsuc_id`, `etapaprod_id`, `unidadmedida_id`, `requiere_kg`, `requiere_cc`, `usa_matprima`, `orden`, `created_at`, `updated_at`) VALUES (4,1,4,NULL,1,0,0,4,'2025-10-01 18:13:01','2025-10-01 18:13:32');
INSERT INTO `areaproduccionsucetapaprod` (`id`, `areaproduccionsuc_id`, `etapaprod_id`, `unidadmedida_id`, `requiere_kg`, `requiere_cc`, `usa_matprima`, `orden`, `created_at`, `updated_at`) VALUES (5,1,5,NULL,1,0,0,5,'2025-10-01 18:13:01','2025-10-01 18:13:35');
INSERT INTO `areaproduccionsucetapaprod` (`id`, `areaproduccionsuc_id`, `etapaprod_id`, `unidadmedida_id`, `requiere_kg`, `requiere_cc`, `usa_matprima`, `orden`, `created_at`, `updated_at`) VALUES (6,4,1,7,1,0,1,1,'2025-10-02 15:08:55','2026-04-20 20:21:38');
INSERT INTO `areaproduccionsucetapaprod` (`id`, `areaproduccionsuc_id`, `etapaprod_id`, `unidadmedida_id`, `requiere_kg`, `requiere_cc`, `usa_matprima`, `orden`, `created_at`, `updated_at`) VALUES (7,4,2,7,1,0,0,2,'2025-10-02 15:08:55','2025-11-11 19:29:19');
INSERT INTO `areaproduccionsucetapaprod` (`id`, `areaproduccionsuc_id`, `etapaprod_id`, `unidadmedida_id`, `requiere_kg`, `requiere_cc`, `usa_matprima`, `orden`, `created_at`, `updated_at`) VALUES (8,4,3,10,1,1,0,3,'2025-10-02 15:08:55','2026-07-15 21:50:03');
INSERT INTO `areaproduccionsucetapaprod` (`id`, `areaproduccionsuc_id`, `etapaprod_id`, `unidadmedida_id`, `requiere_kg`, `requiere_cc`, `usa_matprima`, `orden`, `created_at`, `updated_at`) VALUES (9,4,4,5,1,1,0,5,'2025-10-02 15:08:55','2026-07-15 21:50:14');
INSERT INTO `areaproduccionsucetapaprod` (`id`, `areaproduccionsuc_id`, `etapaprod_id`, `unidadmedida_id`, `requiere_kg`, `requiere_cc`, `usa_matprima`, `orden`, `created_at`, `updated_at`) VALUES (10,4,5,5,1,1,0,6,'2025-10-02 15:08:55','2026-07-15 21:50:16');
INSERT INTO `areaproduccionsucetapaprod` (`id`, `areaproduccionsuc_id`, `etapaprod_id`, `unidadmedida_id`, `requiere_kg`, `requiere_cc`, `usa_matprima`, `orden`, `created_at`, `updated_at`) VALUES (11,4,6,NULL,1,0,0,7,'2025-10-02 15:08:55','2025-10-02 15:11:44');
INSERT INTO `areaproduccionsucetapaprod` (`id`, `areaproduccionsuc_id`, `etapaprod_id`, `unidadmedida_id`, `requiere_kg`, `requiere_cc`, `usa_matprima`, `orden`, `created_at`, `updated_at`) VALUES (12,11,1,NULL,1,0,0,1,'2025-10-02 15:10:07','2025-10-03 13:28:03');
INSERT INTO `areaproduccionsucetapaprod` (`id`, `areaproduccionsuc_id`, `etapaprod_id`, `unidadmedida_id`, `requiere_kg`, `requiere_cc`, `usa_matprima`, `orden`, `created_at`, `updated_at`) VALUES (13,11,2,NULL,1,0,0,2,'2025-10-02 15:10:07','2025-10-03 13:28:06');
INSERT INTO `areaproduccionsucetapaprod` (`id`, `areaproduccionsuc_id`, `etapaprod_id`, `unidadmedida_id`, `requiere_kg`, `requiere_cc`, `usa_matprima`, `orden`, `created_at`, `updated_at`) VALUES (14,11,3,NULL,1,0,0,3,'2025-10-02 15:10:07','2025-10-03 13:28:11');
INSERT INTO `areaproduccionsucetapaprod` (`id`, `areaproduccionsuc_id`, `etapaprod_id`, `unidadmedida_id`, `requiere_kg`, `requiere_cc`, `usa_matprima`, `orden`, `created_at`, `updated_at`) VALUES (15,4,8,5,1,0,0,4,'2025-10-02 15:10:49','2026-04-20 20:08:38');
INSERT INTO `areaproduccionsucetapaprod` (`id`, `areaproduccionsuc_id`, `etapaprod_id`, `unidadmedida_id`, `requiere_kg`, `requiere_cc`, `usa_matprima`, `orden`, `created_at`, `updated_at`) VALUES (16,4,9,10,1,0,0,3.1,'2026-05-28 19:15:14','2026-05-28 19:23:22');
INSERT INTO `areaproduccionsucetapaprod` (`id`, `areaproduccionsuc_id`, `etapaprod_id`, `unidadmedida_id`, `requiere_kg`, `requiere_cc`, `usa_matprima`, `orden`, `created_at`, `updated_at`) VALUES (17,4,10,9,1,0,0,5.5,'2026-05-28 19:24:44','2026-05-28 19:46:09');
/*!40000 ALTER TABLE `areaproduccionsucetapaprod` ENABLE KEYS */;

--
-- Dumping data for table `operario_areaproduccionsucep`
--

/*!40000 ALTER TABLE `operario_areaproduccionsucep` DISABLE KEYS */;
INSERT INTO `operario_areaproduccionsucep` (`id`, `operario_id`, `areaproduccionsucep_id`, `created_at`, `updated_at`) VALUES (2,4,6,'2025-10-20 20:42:47','2025-10-20 20:42:47');
INSERT INTO `operario_areaproduccionsucep` (`id`, `operario_id`, `areaproduccionsucep_id`, `created_at`, `updated_at`) VALUES (5,5,7,'2025-10-22 19:26:19','2025-10-22 19:26:19');
INSERT INTO `operario_areaproduccionsucep` (`id`, `operario_id`, `areaproduccionsucep_id`, `created_at`, `updated_at`) VALUES (6,1,6,'2025-10-22 19:27:06','2025-10-22 19:27:06');
INSERT INTO `operario_areaproduccionsucep` (`id`, `operario_id`, `areaproduccionsucep_id`, `created_at`, `updated_at`) VALUES (7,1,8,'2025-10-23 17:25:25','2025-10-23 17:25:25');
INSERT INTO `operario_areaproduccionsucep` (`id`, `operario_id`, `areaproduccionsucep_id`, `created_at`, `updated_at`) VALUES (8,1,10,'2025-10-23 17:25:25','2025-10-23 17:25:25');
INSERT INTO `operario_areaproduccionsucep` (`id`, `operario_id`, `areaproduccionsucep_id`, `created_at`, `updated_at`) VALUES (9,6,8,'2025-11-03 18:20:32','2025-11-03 18:20:32');
INSERT INTO `operario_areaproduccionsucep` (`id`, `operario_id`, `areaproduccionsucep_id`, `created_at`, `updated_at`) VALUES (10,7,9,'2025-11-03 18:39:27','2025-11-03 18:39:27');
INSERT INTO `operario_areaproduccionsucep` (`id`, `operario_id`, `areaproduccionsucep_id`, `created_at`, `updated_at`) VALUES (11,8,10,'2025-11-03 20:01:31','2025-11-03 20:01:31');
INSERT INTO `operario_areaproduccionsucep` (`id`, `operario_id`, `areaproduccionsucep_id`, `created_at`, `updated_at`) VALUES (12,9,8,'2026-04-23 16:20:02','2026-04-23 16:20:02');
INSERT INTO `operario_areaproduccionsucep` (`id`, `operario_id`, `areaproduccionsucep_id`, `created_at`, `updated_at`) VALUES (13,2,16,'2026-06-05 13:31:11','2026-06-05 13:31:11');
INSERT INTO `operario_areaproduccionsucep` (`id`, `operario_id`, `areaproduccionsucep_id`, `created_at`, `updated_at`) VALUES (14,2,17,'2026-06-05 14:58:12','2026-06-05 14:58:12');
/*!40000 ALTER TABLE `operario_areaproduccionsucep` ENABLE KEYS */;

--
-- Dumping data for table `personaetapaprod`
--

/*!40000 ALTER TABLE `personaetapaprod` DISABLE KEYS */;
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (5,25,6,'2025-10-03 14:57:10','2025-10-03 14:57:10');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (6,25,7,'2025-10-03 14:57:10','2025-10-03 14:57:10');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (7,25,8,'2025-10-03 14:57:10','2025-10-03 14:57:10');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (8,25,15,'2025-10-03 14:57:10','2025-10-03 14:57:10');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (9,25,9,'2025-10-03 14:57:10','2025-10-03 14:57:10');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (10,25,10,'2025-10-03 14:57:10','2025-10-03 14:57:10');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (11,41,8,'2025-10-03 15:18:02','2025-10-03 15:18:02');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (12,21,6,'2025-10-03 15:25:24','2025-10-03 15:25:24');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (14,21,7,'2025-10-03 15:26:21','2025-10-03 15:26:21');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (16,75,6,'2025-10-23 17:26:05','2025-10-23 17:26:05');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (17,21,8,'2025-11-03 18:06:13','2025-11-03 18:06:13');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (18,76,9,'2025-11-03 18:38:35','2025-11-03 18:38:35');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (19,21,9,'2025-11-03 18:40:37','2025-11-03 18:40:37');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (20,21,10,'2025-11-03 18:40:37','2025-11-03 18:40:37');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (21,77,10,'2025-11-03 19:42:13','2025-11-03 19:42:13');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (22,78,7,'2025-11-10 19:09:09','2025-11-10 19:09:09');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (23,79,8,'2025-11-10 19:56:02','2025-11-10 19:56:02');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (27,58,6,'2026-04-17 16:04:27','2026-04-17 16:04:27');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (28,58,7,'2026-04-17 16:04:27','2026-04-17 16:04:27');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (29,58,8,'2026-04-17 16:04:27','2026-04-17 16:04:27');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (30,58,15,'2026-04-17 16:04:27','2026-04-17 16:04:27');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (31,58,9,'2026-04-17 16:04:27','2026-04-17 16:04:27');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (32,58,10,'2026-04-17 16:04:27','2026-04-17 16:04:27');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (33,58,11,'2026-04-17 16:04:27','2026-04-17 16:04:27');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (34,21,16,'2026-06-02 20:51:44','2026-06-02 20:51:44');
INSERT INTO `personaetapaprod` (`id`, `persona_id`, `areaproduccionsucetapaprod_id`, `created_at`, `updated_at`) VALUES (35,21,17,'2026-06-02 20:51:44','2026-06-02 20:51:44');
/*!40000 ALTER TABLE `personaetapaprod` ENABLE KEYS */;

--
-- Dumping data for table `etapaprod_campo`
--

/*!40000 ALTER TABLE `etapaprod_campo` DISABLE KEYS */;
INSERT INTO `etapaprod_campo` (`id`, `apsucetapaprod_id`, `nombre`, `etiqueta`, `tipo`, `formula`, `unidad`, `decimales`, `requerido`, `orden`, `mapea_campo`, `created_at`, `updated_at`, `deleted_at`) VALUES (1,10,'peso_neto_pallet','Peso Neto Pallet','number',NULL,'kg',2,1,1,NULL,'2026-06-03 17:37:36','2026-06-03 17:37:36',NULL);
INSERT INTO `etapaprod_campo` (`id`, `apsucetapaprod_id`, `nombre`, `etiqueta`, `tipo`, `formula`, `unidad`, `decimales`, `requerido`, `orden`, `mapea_campo`, `created_at`, `updated_at`, `deleted_at`) VALUES (2,10,'peso_scrap','Peso Scrap','number',NULL,'kg',2,0,2,NULL,'2026-06-03 17:37:36','2026-06-03 17:37:36',NULL);
INSERT INTO `etapaprod_campo` (`id`, `apsucetapaprod_id`, `nombre`, `etiqueta`, `tipo`, `formula`, `unidad`, `decimales`, `requerido`, `orden`, `mapea_campo`, `created_at`, `updated_at`, `deleted_at`) VALUES (3,10,'cantidad_sacos','Cantidad Sacos','number',NULL,'un',0,1,3,NULL,'2026-06-03 17:37:36','2026-06-03 17:37:36',NULL);
INSERT INTO `etapaprod_campo` (`id`, `apsucetapaprod_id`, `nombre`, `etiqueta`, `tipo`, `formula`, `unidad`, `decimales`, `requerido`, `orden`, `mapea_campo`, `created_at`, `updated_at`, `deleted_at`) VALUES (4,10,'unidades_por_saco','Unidades por Saco','number',NULL,'un',0,1,4,NULL,'2026-06-03 17:37:36','2026-06-03 17:37:36',NULL);
INSERT INTO `etapaprod_campo` (`id`, `apsucetapaprod_id`, `nombre`, `etiqueta`, `tipo`, `formula`, `unidad`, `decimales`, `requerido`, `orden`, `mapea_campo`, `created_at`, `updated_at`, `deleted_at`) VALUES (5,10,'total_unidades','Total Unidades','calculated','cantidad_sacos * unidades_por_saco','un',0,0,5,'cantprod','2026-06-03 17:37:36','2026-06-03 17:37:36',NULL);
INSERT INTO `etapaprod_campo` (`id`, `apsucetapaprod_id`, `nombre`, `etiqueta`, `tipo`, `formula`, `unidad`, `decimales`, `requerido`, `orden`, `mapea_campo`, `created_at`, `updated_at`, `deleted_at`) VALUES (6,17,'peso_neto_pallet','Peso pallet','number',NULL,'Kg',2,1,1,NULL,'2026-06-05 16:53:45','2026-06-05 16:53:45',NULL);
INSERT INTO `etapaprod_campo` (`id`, `apsucetapaprod_id`, `nombre`, `etiqueta`, `tipo`, `formula`, `unidad`, `decimales`, `requerido`, `orden`, `mapea_campo`, `created_at`, `updated_at`, `deleted_at`) VALUES (7,17,'cant_etiquetas','Cantidad etiquetas','number',NULL,'Unid',0,1,2,NULL,'2026-06-05 17:16:50','2026-06-05 17:16:50',NULL);
INSERT INTO `etapaprod_campo` (`id`, `apsucetapaprod_id`, `nombre`, `etiqueta`, `tipo`, `formula`, `unidad`, `decimales`, `requerido`, `orden`, `mapea_campo`, `created_at`, `updated_at`, `deleted_at`) VALUES (8,16,'peso','Peso','number',NULL,'kg',2,1,1,'cantprod','2026-06-17 15:20:11','2026-06-17 15:20:11',NULL);
INSERT INTO `etapaprod_campo` (`id`, `apsucetapaprod_id`, `nombre`, `etiqueta`, `tipo`, `formula`, `unidad`, `decimales`, `requerido`, `orden`, `mapea_campo`, `created_at`, `updated_at`, `deleted_at`) VALUES (9,9,'lotetinta','Lotetinta','text',NULL,'Lote',2,1,1,NULL,'2026-06-18 16:00:26','2026-06-18 16:00:26',NULL);
INSERT INTO `etapaprod_campo` (`id`, `apsucetapaprod_id`, `nombre`, `etiqueta`, `tipo`, `formula`, `unidad`, `decimales`, `requerido`, `orden`, `mapea_campo`, `created_at`, `updated_at`, `deleted_at`) VALUES (10,9,'etiqueta','etiqueta','number',NULL,'kg',2,1,2,NULL,'2026-06-18 16:06:33','2026-06-18 16:06:33',NULL);
INSERT INTO `etapaprod_campo` (`id`, `apsucetapaprod_id`, `nombre`, `etiqueta`, `tipo`, `formula`, `unidad`, `decimales`, `requerido`, `orden`, `mapea_campo`, `created_at`, `updated_at`, `deleted_at`) VALUES (11,7,'olor','Olor','text',NULL,'olor',2,1,1,NULL,'2026-07-02 18:05:56','2026-07-02 18:05:56',NULL);
INSERT INTO `etapaprod_campo` (`id`, `apsucetapaprod_id`, `nombre`, `etiqueta`, `tipo`, `formula`, `unidad`, `decimales`, `requerido`, `orden`, `mapea_campo`, `created_at`, `updated_at`, `deleted_at`) VALUES (12,7,'color','Color','text',NULL,'color',2,1,2,NULL,'2026-07-02 18:06:24','2026-07-02 18:06:24',NULL);
/*!40000 ALTER TABLE `etapaprod_campo` ENABLE KEYS */;

--
-- Dumping data for table `ccparam`
--

/*!40000 ALTER TABLE `ccparam` DISABLE KEYS */;
INSERT INTO `ccparam` (`id`, `nombre`, `etiqueta`, `tipo`, `unidad`, `decimales`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (1,'Espesor','Espesor','number','Unidad',2,1,NULL,'2026-06-16 15:05:54','2026-06-16 15:05:54',NULL);
INSERT INTO `ccparam` (`id`, `nombre`, `etiqueta`, `tipo`, `unidad`, `decimales`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (2,'espesor','Espesor','number','µm',2,1,NULL,'2026-06-16 15:21:21','2026-06-16 16:28:56',NULL);
INSERT INTO `ccparam` (`id`, `nombre`, `etiqueta`, `tipo`, `unidad`, `decimales`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (3,'ancho','Ancho','number','Cm',2,2,NULL,'2026-06-16 16:27:25','2026-06-16 16:27:25',NULL);
INSERT INTO `ccparam` (`id`, `nombre`, `etiqueta`, `tipo`, `unidad`, `decimales`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (4,'elasticidad','elasticidad','number','f',2,3,NULL,'2026-06-16 16:27:52','2026-06-16 16:27:52',NULL);
INSERT INTO `ccparam` (`id`, `nombre`, `etiqueta`, `tipo`, `unidad`, `decimales`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (5,'resistencia','resistencia','number','Res',2,0,NULL,'2026-06-16 16:28:13','2026-06-16 16:28:13',NULL);
INSERT INTO `ccparam` (`id`, `nombre`, `etiqueta`, `tipo`, `unidad`, `decimales`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (6,'color','Color','text',NULL,2,5,NULL,'2026-06-18 16:29:14','2026-06-18 16:29:14',NULL);
/*!40000 ALTER TABLE `ccparam` ENABLE KEYS */;

--
-- Dumping data for table `ccparam_apsucetapaprod`
--

/*!40000 ALTER TABLE `ccparam_apsucetapaprod` DISABLE KEYS */;
INSERT INTO `ccparam_apsucetapaprod` (`id`, `apsucetapaprod_id`, `ccparam_id`, `valor_min`, `valor_max`, `requerido`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (1,11,2,50.0000,150.0000,1,1,NULL,'2026-06-16 15:24:03','2026-06-16 15:24:03',NULL);
INSERT INTO `ccparam_apsucetapaprod` (`id`, `apsucetapaprod_id`, `ccparam_id`, `valor_min`, `valor_max`, `requerido`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (2,10,1,1.0000,60.0000,1,1,NULL,'2026-06-16 15:36:01','2026-06-16 15:36:01',NULL);
INSERT INTO `ccparam_apsucetapaprod` (`id`, `apsucetapaprod_id`, `ccparam_id`, `valor_min`, `valor_max`, `requerido`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (3,10,5,50.0000,100.0000,1,2,NULL,'2026-06-16 16:29:10','2026-06-16 16:29:10',NULL);
INSERT INTO `ccparam_apsucetapaprod` (`id`, `apsucetapaprod_id`, `ccparam_id`, `valor_min`, `valor_max`, `requerido`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (4,10,3,50.0000,400.0000,1,3,NULL,'2026-06-16 16:29:27','2026-06-16 16:29:27',NULL);
INSERT INTO `ccparam_apsucetapaprod` (`id`, `apsucetapaprod_id`, `ccparam_id`, `valor_min`, `valor_max`, `requerido`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (5,10,4,5.0000,10.0000,1,0,NULL,'2026-06-16 16:29:38','2026-06-16 16:29:38',NULL);
INSERT INTO `ccparam_apsucetapaprod` (`id`, `apsucetapaprod_id`, `ccparam_id`, `valor_min`, `valor_max`, `requerido`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (6,16,5,60.0000,80.0000,1,0,NULL,'2026-06-16 20:39:50','2026-06-16 20:39:50',NULL);
INSERT INTO `ccparam_apsucetapaprod` (`id`, `apsucetapaprod_id`, `ccparam_id`, `valor_min`, `valor_max`, `requerido`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (7,16,3,50.0000,90.0000,1,1,NULL,'2026-06-16 20:40:05','2026-06-16 20:40:05',NULL);
INSERT INTO `ccparam_apsucetapaprod` (`id`, `apsucetapaprod_id`, `ccparam_id`, `valor_min`, `valor_max`, `requerido`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (8,10,6,NULL,NULL,1,4,NULL,'2026-06-18 16:29:54','2026-06-18 16:29:54',NULL);
INSERT INTO `ccparam_apsucetapaprod` (`id`, `apsucetapaprod_id`, `ccparam_id`, `valor_min`, `valor_max`, `requerido`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (9,7,6,0.0000,100.0000,1,1,NULL,'2026-07-02 18:08:17','2026-07-02 18:08:17',NULL);
INSERT INTO `ccparam_apsucetapaprod` (`id`, `apsucetapaprod_id`, `ccparam_id`, `valor_min`, `valor_max`, `requerido`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (10,7,5,1.0000,20.0000,1,2,NULL,'2026-07-02 18:08:29','2026-07-02 18:08:29',NULL);
INSERT INTO `ccparam_apsucetapaprod` (`id`, `apsucetapaprod_id`, `ccparam_id`, `valor_min`, `valor_max`, `requerido`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (11,8,5,20.0000,500.0000,1,1,NULL,'2026-07-07 20:24:53','2026-07-07 20:24:53',NULL);
INSERT INTO `ccparam_apsucetapaprod` (`id`, `apsucetapaprod_id`, `ccparam_id`, `valor_min`, `valor_max`, `requerido`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (12,8,4,500.0000,600.0000,1,0,NULL,'2026-07-07 20:25:07','2026-07-07 20:25:07',NULL);
INSERT INTO `ccparam_apsucetapaprod` (`id`, `apsucetapaprod_id`, `ccparam_id`, `valor_min`, `valor_max`, `requerido`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (13,8,6,NULL,NULL,1,0,NULL,'2026-07-07 20:25:18','2026-07-07 20:25:18',NULL);
INSERT INTO `ccparam_apsucetapaprod` (`id`, `apsucetapaprod_id`, `ccparam_id`, `valor_min`, `valor_max`, `requerido`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (14,17,5,10.0000,50.0000,1,0,NULL,'2026-07-13 13:19:57','2026-07-13 13:19:57',NULL);
INSERT INTO `ccparam_apsucetapaprod` (`id`, `apsucetapaprod_id`, `ccparam_id`, `valor_min`, `valor_max`, `requerido`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (15,17,4,50.0000,600.0000,1,0,NULL,'2026-07-13 13:20:11','2026-07-13 13:20:11',NULL);
INSERT INTO `ccparam_apsucetapaprod` (`id`, `apsucetapaprod_id`, `ccparam_id`, `valor_min`, `valor_max`, `requerido`, `orden`, `usuariodel_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (16,9,6,1.0000,100.0000,1,2,NULL,'2026-07-15 22:01:44','2026-07-15 22:01:44',NULL);
/*!40000 ALTER TABLE `ccparam_apsucetapaprod` ENABLE KEYS */;

--
-- Dumping data for table `apsucetapaprod_bodega`
--

/*!40000 ALTER TABLE `apsucetapaprod_bodega` DISABLE KEYS */;
INSERT INTO `apsucetapaprod_bodega` (`id`, `apsucetapaprod_id`, `invbodega_id`, `created_at`, `updated_at`) VALUES (1,6,24,'2026-06-24 16:31:11','2026-06-24 16:31:11');
INSERT INTO `apsucetapaprod_bodega` (`id`, `apsucetapaprod_id`, `invbodega_id`, `created_at`, `updated_at`) VALUES (2,7,25,'2026-06-24 16:31:23','2026-06-24 16:31:23');
INSERT INTO `apsucetapaprod_bodega` (`id`, `apsucetapaprod_id`, `invbodega_id`, `created_at`, `updated_at`) VALUES (3,8,26,'2026-06-24 16:31:34','2026-06-24 16:31:34');
INSERT INTO `apsucetapaprod_bodega` (`id`, `apsucetapaprod_id`, `invbodega_id`, `created_at`, `updated_at`) VALUES (4,16,26,'2026-06-24 16:31:48','2026-06-24 16:31:48');
INSERT INTO `apsucetapaprod_bodega` (`id`, `apsucetapaprod_id`, `invbodega_id`, `created_at`, `updated_at`) VALUES (5,15,26,'2026-06-24 16:32:00','2026-06-24 16:32:00');
INSERT INTO `apsucetapaprod_bodega` (`id`, `apsucetapaprod_id`, `invbodega_id`, `created_at`, `updated_at`) VALUES (6,9,27,'2026-06-24 16:32:10','2026-06-24 16:32:10');
INSERT INTO `apsucetapaprod_bodega` (`id`, `apsucetapaprod_id`, `invbodega_id`, `created_at`, `updated_at`) VALUES (7,17,25,'2026-06-24 16:32:20','2026-06-24 16:32:20');
INSERT INTO `apsucetapaprod_bodega` (`id`, `apsucetapaprod_id`, `invbodega_id`, `created_at`, `updated_at`) VALUES (8,10,28,'2026-06-24 16:32:30','2026-06-24 16:32:30');
INSERT INTO `apsucetapaprod_bodega` (`id`, `apsucetapaprod_id`, `invbodega_id`, `created_at`, `updated_at`) VALUES (9,11,28,'2026-06-24 16:33:29','2026-06-24 16:33:29');
INSERT INTO `apsucetapaprod_bodega` (`id`, `apsucetapaprod_id`, `invbodega_id`, `created_at`, `updated_at`) VALUES (11,7,30,'2026-06-25 20:48:31','2026-06-25 20:48:31');
INSERT INTO `apsucetapaprod_bodega` (`id`, `apsucetapaprod_id`, `invbodega_id`, `created_at`, `updated_at`) VALUES (12,6,29,'2026-06-26 00:49:30','2026-06-26 00:49:30');
/*!40000 ALTER TABLE `apsucetapaprod_bodega` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-28 11:24:10
