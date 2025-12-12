CREATE DATABASE  IF NOT EXISTS `monteroa_ferreteria` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `monteroa_ferreteria`;
-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: monteroa_ferreteria
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `detalle_ventas`
--

DROP TABLE IF EXISTS `detalle_ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_ventas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_venta` (`id_venta`),
  KEY `id_producto` (`id_producto`),
  CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`),
  CONSTRAINT `detalle_ventas_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_ventas`
--

LOCK TABLES `detalle_ventas` WRITE;
/*!40000 ALTER TABLE `detalle_ventas` DISABLE KEYS */;
INSERT INTO `detalle_ventas` VALUES (1,1,8,1,4.25,4.25),(2,1,11,2,35.00,70.00),(3,2,11,2,35.00,70.00),(4,2,9,7,22.00,154.00),(5,2,8,2,4.25,8.50),(6,2,10,3,18.75,56.25),(7,2,17,1,1.50,1.50);
/*!40000 ALTER TABLE `detalle_ventas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `imagen` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `stock_minimo` int(11) DEFAULT 5,
  `categoria` varchar(50) DEFAULT 'general',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,'Martillo de carpintero','Martillo con mango de madera y cabeza de acero forjado. Ideal para clavar y extraer clavos.',59.90,47,'martillo.png','2025-12-06 22:14:06',10,'herramientas'),(2,'Destornillador Phillips','Destornillador de punta Phillips con mango ergonómico. Tamaño mediano.',32.50,30,'61OFJLmDGoL._AC_UF894,1000_QL80_.jpg','2025-12-06 22:14:06',5,'herramientas'),(3,'Juego de llaves inglesas','Juego de 5 llaves inglesas de acero cromado, de: 6\", 8\", 10\".',149.90,20,'HADWK031.jpg','2025-12-06 22:14:06',5,'herramientas'),(4,'Taladro percutor','Taladro percutor inalámbrico 18V. Incluye batería y cargador.',389.00,15,'36820773-800-auto.webp','2025-12-06 22:14:06',3,'herramientas'),(5,'Sierra circular','Sierra circular de 7 1/4\" con disco de diamante. Potencia 1200W.',289.00,9,'productos3_5934-300x300.jpg','2025-12-06 22:14:06',2,'herramientas'),(6,'Clavos de acero 2\"','Caja de 100 clavos de acero galvanizado de 2 pulgadas.',19.90,199,'clavo-cemento-1-27-x-25mm-prodac-x-500-gr.jpg','2025-12-06 22:14:06',50,'ferreteria'),(7,'Tornillos madera 3\" x 50 Und.','Caja de 50 tornillos para madera de 3 pulgadas con cabeza hexagonal.',24.90,149,'images (3).jpg','2025-12-06 22:14:06',30,'ferreteria'),(8,'Bisagra de latón','Bisagra de latón de 3\" para puertas. Paquete de 2 unidades.',15.90,58,'D_NQ_NP_986981-MPE76374196791_052024-O.webp','2025-12-06 22:14:06',20,'ferreteria'),(9,'Cerradura de puerta','Cerradura de puerta con llave y pestillo. Incluye 3 llaves.',79.90,15,'images.jpg','2025-12-06 22:14:06',5,'ferreteria'),(10,'Alambre de acero','Rollo de alambre de acero galvanizado calibre 16. 50 metros.',49.90,6,'975747-a1.jpg','2025-12-06 22:14:06',10,'ferreteria'),(11,'Cable eléctrico 12 AWG','Cable eléctrico THHN calibre 12. Color negro. 100 metros.',89.90,23,'CABLE_CU_THWN_12_NG_ROLLO_ELECTROVERA.png','2025-12-06 22:14:06',10,'electricidad'),(12,'Tomacorriente','Tomacorriente estándar 125V 15A. Color blanco.',12.90,99,'images (2).jpg','2025-12-06 22:14:06',20,'electricidad'),(13,'Interruptor de luz','Interruptor de luz simple. Color blanco.',9.90,80,'w=1500,h=1500,fit=pad.webp','2025-12-06 22:14:06',15,'electricidad'),(14,'Cinta aislante','Rollo de cinta aislante eléctrica. Color negro. 19mm x 20m.',14.90,60,'CINTATemflex15518.png','2025-12-06 22:14:06',10,'electricidad'),(15,'Portalámpara','Portalámpara plástico con rosca E27. Para instalaciones interiores.',7.90,120,'portalamparas-con-arandela-e27-4a-250v.jpg','2025-12-06 22:14:06',30,'electricidad'),(16,'Tubo PVC 1/2','Tubo PVC de 1/2 pulgada. Longitud 3 metros.',22.90,49,'Slide12.jpg','2025-12-06 22:14:06',10,'fontanería'),(17,'Codo PVC 90°','Codo PVC de 90° para tubo de 1/2 pulgada.',5.90,99,'images (1).jpg','2025-12-06 22:14:06',20,'fontanería'),(18,'Válvula de paso','Válvula de paso de bronce de 1/2 pulgada.',39.90,29,'valvulacompuertabronce.webp','2025-12-06 22:14:06',5,'fontanería'),(19,'Flexómetro 5m','Flexómetro metálico de 5 metros con bloqueo automático.',39.90,40,'D_NQ_NP_872990-MLU73423366831_122023-O.webp','2025-12-06 22:14:06',10,'herramientas'),(20,'Nivel de burbuja','Nivel de burbuja de aluminio de 24\". Precisión 0.5mm/m.',49.90,25,'nivel burbuja.jpg','2025-12-06 22:14:06',5,'herramientas'),(21,'Llave stillson 10\"','Llave stillson de 10\" con dientes ajustables. Para tuberías.',89.90,15,'llave-stillson-10-15836-truper.webp','2025-12-06 22:14:06',3,'herramientas'),(22,'Soldadora de estaño','Soldadora de estaño eléctrica 40W con punta de cobre.',39.90,20,'1745868623065.jpg','2025-12-06 22:14:06',5,'electricidad'),(23,'Pistola de silicona','Pistola de calafatear para cartuchos de silicona. Metal.',29.90,35,'pistola-de-calafatear-para-cartuchos-silicona.png','2025-12-06 22:14:06',5,'ferreteria'),(24,'Cinta métrica x 10 m','Cinta métrica de fibra de 10 metros. Carcasa de plástico.',24.90,45,'wincha-flexometro-gripper-10-metros-contra-impacto-tpr-cinta-extra-ancha-carcasa-abs-fh-10m-14582-truper.webp','2025-12-06 22:14:06',10,'herramientas'),(25,'Escalera de aluminio','Escalera de aluminio plegable de 3 escalones. Capacidad 150kg.',129.90,10,'imageUrl_1.webp','2025-12-06 22:14:06',2,'herramientas'),(26,'Socket Plano E27 Blanco Pack x 3','Socket Plano Werken E27 Blanco Pack x 3:\r\nDiseño plano de alta calidad.\r\nFabricado en polipropileno y acero.\r\nTipo de rosca E27 y amperaje de 0.16666667 A.\r\nGarantía de 1 año para tu tranquilidad.\r\nIncluye tornillos para una instalación sencilla.',11.90,20,'null.webp','2025-12-12 01:36:31',20,'general'),(27,'Cuchillo Dyangrip retractable Stanley','Cuchilla con mango anti-deslizante aún con el uso de guantes, Cuenta con un mecanismo patentado de nariz superpuesta que mantiene la hoja segura en su posición. Incluye 3 hojas.',49.90,10,'10-779_1.jpg','2025-12-12 01:45:45',10,'general'),(28,'Alicate Pico de Loro 10\" ','Alicate Pico de Loro 10\" 17351 Truper',29.90,9,'X_pex-10x-e16265.jpg','2025-12-12 01:50:03',8,'general');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','cajero') NOT NULL,
  `fecha_registro` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Admin','admin@ferreteria.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin','2025-12-06 22:09:39');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ventas`
--

DROP TABLE IF EXISTS `ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ventas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `fecha` timestamp NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `tipo_documento` enum('boleta','factura') NOT NULL,
  `numero_documento` varchar(20) NOT NULL,
  `nombre_cliente` varchar(100) DEFAULT NULL,
  `tipo_documento_cliente` varchar(20) DEFAULT NULL,
  `numero_documento_cliente` varchar(20) DEFAULT NULL,
  `email_cliente` varchar(100) DEFAULT NULL,
  `telefono_cliente` varchar(20) DEFAULT NULL,
  `direccion_cliente` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas`
--

LOCK TABLES `ventas` WRITE;
/*!40000 ALTER TABLE `ventas` DISABLE KEYS */;
INSERT INTO `ventas` VALUES (1,1,'2025-12-06 22:32:37',74.25,'boleta','B-510760',NULL,NULL,NULL,NULL,NULL,NULL),(2,1,'2025-12-06 23:10:31',290.25,'boleta','B-852874',NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `ventas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-12 16:44:10
