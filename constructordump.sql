-- MySQL dump 10.13  Distrib 8.0.40, for Linux (x86_64)
--
-- Host: localhost    Database: constructor
-- ------------------------------------------------------
-- Server version	8.0.40-0ubuntu0.20.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `id_cliente` int NOT NULL AUTO_INCREMENT,
  `codemp` varchar(100) DEFAULT NULL,
  `codsuc` varchar(100) DEFAULT NULL,
  `nombre_cliente` varchar(100) DEFAULT NULL,
  `ruc` varchar(100) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `telefono` varchar(100) DEFAULT NULL,
  `cod_vend` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tipo_cliente` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,'1','1','José Antonio Carrasco Sánchez','1207098169001','Cumbres de Mapasingue','0990751629','1','jacarrasco@bonsai.com.ec','1');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cotizaciones`
--

DROP TABLE IF EXISTS `cotizaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cotizaciones` (
  `id_cotizacion` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int DEFAULT NULL,
  `total_productos` varchar(100) DEFAULT NULL,
  `subtotal` decimal(10,0) DEFAULT NULL,
  `monto_iva` decimal(10,0) DEFAULT NULL,
  `total` decimal(10,0) DEFAULT NULL,
  `codemp` varchar(100) DEFAULT NULL,
  `codsuc` varchar(100) DEFAULT NULL,
  `metodo_pago` varchar(100) DEFAULT '',
  `numero_orden` varchar(100) DEFAULT '',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_cotizacion`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cotizaciones`
--

LOCK TABLES `cotizaciones` WRITE;
/*!40000 ALTER TABLE `cotizaciones` DISABLE KEYS */;
INSERT INTO `cotizaciones` VALUES (16,1,'6',294,44,338,'1','1','tc','RT8Z0YK8','2024-09-12 19:53:27','2024-09-12 19:53:57'),(17,1,'3',192,29,221,'1','1','tc','SER0Q49A','2024-09-12 19:53:27','2024-09-12 19:53:57'),(18,1,'3',49,7,56,'1','1','td','30528KO7','2024-09-12 19:53:27','2024-09-12 19:53:57'),(19,1,'7',305,46,351,'1','1','tc','Q7OA10J6','2024-09-12 22:36:58','2024-09-12 22:36:58'),(20,1,'2',55,8,63,'1','1','tc','3NJXBD69','2024-09-13 15:12:06','2024-09-13 15:12:06'),(21,1,'4',150,23,173,'1','1','tc','1M55FE94','2024-09-13 16:14:40','2024-09-13 16:14:40'),(22,1,'2',27,4,31,'1','1','efectivo','R6915SDA','2024-10-10 14:06:30','2024-10-10 14:06:30'),(23,966646960,'3',226,34,260,'1','1','efectivo','1O66X0SZ','2024-10-15 17:08:06','2024-10-15 17:08:06'),(24,966646960,'2',110,17,127,'1','1','tc','NMCHOZXK','2024-10-22 14:51:20','2024-10-22 14:51:20'),(25,966899254,'1',400,60,460,'1','1','efectivo','FMQJ19BE','2024-10-22 16:22:08','2024-10-22 16:22:08');
/*!40000 ALTER TABLE `cotizaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_cotizacion`
--

DROP TABLE IF EXISTS `detalle_cotizacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_cotizacion` (
  `id_detalle` int NOT NULL AUTO_INCREMENT,
  `id_producto` varchar(100) DEFAULT NULL,
  `cantidad` varchar(100) DEFAULT NULL,
  `precio_unitario` decimal(10,0) DEFAULT NULL,
  `precio_neto` decimal(10,0) DEFAULT NULL,
  `imagen` varchar(300) DEFAULT NULL,
  `id_cotizacion` varchar(100) DEFAULT NULL,
  `nombre_producto` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_detalle`)
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_cotizacion`
--

LOCK TABLES `detalle_cotizacion` WRITE;
/*!40000 ALTER TABLE `detalle_cotizacion` DISABLE KEYS */;
INSERT INTO `detalle_cotizacion` VALUES (91,'5','3',10,30,'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQhp-GGx3NVk6OWxjsr5X2nxxtBj8T4McpffA&s','16','Cemento Chimborazo'),(92,'6','5',11,55,'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRB1Oafr11BRgxmGSnCqHgu41AubCgQMMFMVQ&s','16','Bombilla Ingco'),(93,'7','5',11,55,'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRu7Fl95YDQxnZ6qPrD3MA_ps8IpBXH-f2_zg&s','16','Grifo DeWalt'),(94,'8','4',11,44,'https://www.eprom.com.pe/wp-content/uploads/2019/08/baldosa-monocapa.jpg','16','Baldosa PTK'),(95,'10','4',11,44,'https://m.media-amazon.com/images/I/5132meFGADL._AC_UF894,1000_QL80_.jpg','16','Bombilla Sika'),(96,'9','6',11,66,'https://http2.mlstatic.com/D_NQ_NP_795341-MLU74306894930_022024-O.webp','16','Lámpara Elite'),(97,'5','6',10,60,'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQhp-GGx3NVk6OWxjsr5X2nxxtBj8T4McpffA&s','17','Cemento Chimborazo'),(98,'6','6',11,66,'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRB1Oafr11BRgxmGSnCqHgu41AubCgQMMFMVQ&s','17','Bombilla Ingco'),(99,'1','6',11,66,'https://dolmen.com.ec/wp-content/uploads/2021/08/DGEBA3333TENANOM5B10_-GRES-EXP-BALDOSA-33X33-TERRACOTA-NATURAL-NOR-1.10_1.jpg','17','Baldosa Novacero'),(100,'3','1',15,15,'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQHzat4sQdvh1P3xxrRSpkArdHCmwFufeKPicGa1cZST8KOaJbAVeNN9QtPJXUVb6DR8VA&usqp=CAU','18',' Bombilla Andec'),(101,'4','2',12,24,'https://kywiec.vtexassets.com/arquivos/ids/155757-800-auto?v=638380017790300000&width=800&height=auto&aspect=true','18','Lámpara IdealAlambrec'),(102,'8','1',10,10,'https://www.eprom.com.pe/wp-content/uploads/2019/08/baldosa-monocapa.jpg','18','Baldosa PTK'),(103,'3','4',16,64,'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQHzat4sQdvh1P3xxrRSpkArdHCmwFufeKPicGa1cZST8KOaJbAVeNN9QtPJXUVb6DR8VA&usqp=CAU','19',' Bombilla Andec'),(104,'4','4',12,48,'https://kywiec.vtexassets.com/arquivos/ids/155757-800-auto?v=638380017790300000&width=800&height=auto&aspect=true','19','Lámpara IdealAlambrec'),(105,'7','3',11,33,'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRu7Fl95YDQxnZ6qPrD3MA_ps8IpBXH-f2_zg&s','19','Grifo DeWalt'),(106,'5','5',10,50,'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQhp-GGx3NVk6OWxjsr5X2nxxtBj8T4McpffA&s','19','Cemento Chimborazo'),(107,'9','4',11,44,'https://http2.mlstatic.com/D_NQ_NP_795341-MLU74306894930_022024-O.webp','19','Lámpara Elite'),(108,'10','5',11,55,'https://m.media-amazon.com/images/I/5132meFGADL._AC_UF894,1000_QL80_.jpg','19','Bombilla Sika'),(109,'6','1',11,11,'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRB1Oafr11BRgxmGSnCqHgu41AubCgQMMFMVQ&s','19','Bombilla Ingco'),(110,'2','2',11,22,'https://interalemana.com/wp-content/uploads/2022/01/grival-flamingo-xs.jpg','20','Grifo Acesco'),(111,'1','3',11,33,'https://dolmen.com.ec/wp-content/uploads/2021/08/DGEBA3333TENANOM5B10_-GRES-EXP-BALDOSA-33X33-TERRACOTA-NATURAL-NOR-1.10_1.jpg','20','Baldosa Novacero'),(112,'1','3',11,33,'https://dolmen.com.ec/wp-content/uploads/2021/08/DGEBA3333TENANOM5B10_-GRES-EXP-BALDOSA-33X33-TERRACOTA-NATURAL-NOR-1.10_1.jpg','21','Baldosa Novacero'),(113,'2','3',11,33,'https://interalemana.com/wp-content/uploads/2022/01/grival-flamingo-xs.jpg','21','Grifo Acesco'),(114,'3','3',16,48,'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQHzat4sQdvh1P3xxrRSpkArdHCmwFufeKPicGa1cZST8KOaJbAVeNN9QtPJXUVb6DR8VA&usqp=CAU','21',' Bombilla Andec'),(115,'4','3',12,36,'https://kywiec.vtexassets.com/arquivos/ids/155757-800-auto?v=638380017790300000&width=800&height=auto&aspect=true','21','Lámpara IdealAlambrec'),(116,'3','1',15,15,'http://18.191.120.236/comisariato_constructor/imagen_productos/Correa_01.png','22','Correas de transmisión'),(117,'4','1',12,12,'http://18.191.120.236/comisariato_constructor/imagen_productos/Correa_02.png','22','Correas modulares'),(118,'94484','3',32,95,'undefined','23','BASE PISO/PARED 38.1*1.5'),(119,'94498','4',9,36,'undefined','23','STELL PANEL 0.40 GALVALUM  MT'),(120,'15014','3',32,95,'undefined','23','PT 3/4 X 1/4 (19MM X 6MM)'),(121,'943094461','2',5,11,'../imagen_productos/Correa_02.png','24','FLAPPER C/CADENA EDESA'),(122,'94484','3',33,100,'../imagen_productos/Correa_01.png','24','BASE PISO/PARED 38.1*1.5'),(123,'2590','118',3,400,'../imagen_productos/Angulo_01.png','25','CINTA PELIGRO AMARILLO 75MMX200MTS');
/*!40000 ALTER TABLE `detalle_cotizacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresas`
--

DROP TABLE IF EXISTS `empresas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresas` (
  `id_empresa` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `descripcion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id_empresa`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresas`
--

LOCK TABLES `empresas` WRITE;
/*!40000 ALTER TABLE `empresas` DISABLE KEYS */;
INSERT INTO `empresas` VALUES (1,'Materiales de Construcción','\'\'');
/*!40000 ALTER TABLE `empresas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grupo`
--

DROP TABLE IF EXISTS `grupo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grupo` (
  `id_grupo` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `id_empresa` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id_grupo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grupo`
--

LOCK TABLES `grupo` WRITE;
/*!40000 ALTER TABLE `grupo` DISABLE KEYS */;
INSERT INTO `grupo` VALUES (1,'Materiales de Construcción','1');
/*!40000 ALTER TABLE `grupo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lineas`
--

DROP TABLE IF EXISTS `lineas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lineas` (
  `id_linea` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `id_empresa` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id_linea`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lineas`
--

LOCK TABLES `lineas` WRITE;
/*!40000 ALTER TABLE `lineas` DISABLE KEYS */;
INSERT INTO `lineas` VALUES (1,'Baldosas','1'),(2,'Grifos','1'),(3,'Bombillas','1'),(4,'Lámparas','1');
/*!40000 ALTER TABLE `lineas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marcas`
--

DROP TABLE IF EXISTS `marcas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marcas` (
  `id_marca` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `id_empresa` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id_marca`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marcas`
--

LOCK TABLES `marcas` WRITE;
/*!40000 ALTER TABLE `marcas` DISABLE KEYS */;
INSERT INTO `marcas` VALUES (1,'Novacero','1'),(2,'Acesco','1'),(3,'Andec','1'),(4,'IdealAlambrec','1'),(5,'Cemento Chimborazo','1'),(6,'Ingco','1'),(7,'DeWalt','1'),(8,'PTK','1'),(9,'Elite','1'),(10,'Pedrollo','1'),(11,'Eternit','1'),(12,'Sika','1');
/*!40000 ALTER TABLE `marcas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `precios`
--

DROP TABLE IF EXISTS `precios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `precios` (
  `id_precio` int NOT NULL AUTO_INCREMENT,
  `codemp` varchar(100) DEFAULT NULL,
  `codsuc` varchar(100) DEFAULT NULL,
  `tienda` varchar(100) DEFAULT NULL,
  `cod_prod` varchar(100) DEFAULT NULL,
  `efe_pvp_sin_iva` decimal(10,0) DEFAULT NULL,
  `efe_pvp_con_iva` decimal(10,0) DEFAULT NULL,
  `tc_pvp_sin_iva` decimal(10,0) DEFAULT NULL,
  `tc_pvp_con_iva` decimal(10,0) DEFAULT NULL,
  `td_pvp_sin_iva` decimal(10,0) DEFAULT NULL,
  `td_pvp_con_iva` decimal(10,0) DEFAULT NULL,
  PRIMARY KEY (`id_precio`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `precios`
--

LOCK TABLES `precios` WRITE;
/*!40000 ALTER TABLE `precios` DISABLE KEYS */;
INSERT INTO `precios` VALUES (1,'1','1','1','1',10,11,11,12,10,11),(2,'1','1','1','2',11,12,11,12,11,11),(3,'1','1','1','3',15,16,16,17,15,16),(4,'1','1','1','4',12,12,12,13,12,12),(5,'1','1','1','5',9,10,10,11,9,10),(6,'1','1','1','6',10,11,11,12,10,11),(7,'1','1','1','7',10,11,11,12,10,11),(8,'1','1','1','8',10,11,11,12,10,11),(9,'1','1','1','9',10,11,11,12,10,11),(10,'1','1','1','10',10,11,11,12,10,11);
/*!40000 ALTER TABLE `precios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `id_producto` int NOT NULL AUTO_INCREMENT,
  `codsuc` varchar(100) DEFAULT NULL,
  `codemp` varchar(100) DEFAULT NULL,
  `alterno` varchar(100) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `linea` varchar(100) DEFAULT NULL,
  `grupo` varchar(100) DEFAULT NULL,
  `subgru` varchar(100) DEFAULT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `ancho` decimal(10,0) DEFAULT NULL,
  `espesor` decimal(10,0) DEFAULT NULL,
  `imagen` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,'1','1','NVCR-BLDS','Correas Planas','1','1','1','1',30,5,'http://18.191.120.236/comisariato_constructor/imagen_productos/Angulo_01.png'),(2,'1','1','AC-GRFS','Correas Poly-V (multicanal)','2','1','1','2',15,2,'http://18.191.120.236/comisariato_constructor/imagen_productos/Angulo_02.png'),(3,'1','1','AND-BMB','Correas de transmisión','3','1','1','3',10,2,'http://18.191.120.236/comisariato_constructor/imagen_productos/Correa_01.png'),(4,'1','1',' IA-LMP','Correas modulares','4','1','1','4',20,3,'http://18.191.120.236/comisariato_constructor/imagen_productos/Correa_02.png'),(5,'1','1','CCHMB-50','Cemento Chimborazo','1','1','1','5',50,25,'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQhp-GGx3NVk6OWxjsr5X2nxxtBj8T4McpffA&s'),(6,'1','1','ING-BMB','Bombilla Ingco','3','1','1','6',12,2,'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRB1Oafr11BRgxmGSnCqHgu41AubCgQMMFMVQ&s'),(7,'1','1',' DW-GRFS','Grifo DeWalt','2','1','1','7',14,3,'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRu7Fl95YDQxnZ6qPrD3MA_ps8IpBXH-f2_zg&s'),(8,'1','1',' PTK-BLDS','Baldosa PTK','1','1','1','8',32,6,'https://www.eprom.com.pe/wp-content/uploads/2019/08/baldosa-monocapa.jpg'),(9,'1','1',' ELT-LMP','Lámpara Elite','4','1','1','9',18,4,'https://http2.mlstatic.com/D_NQ_NP_795341-MLU74306894930_022024-O.webp'),(10,'1','1','SIK-BMB','Bombilla Sika','3','1','1','10',10,1,'https://m.media-amazon.com/images/I/5132meFGADL._AC_UF894,1000_QL80_.jpg');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subgrupo`
--

DROP TABLE IF EXISTS `subgrupo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subgrupo` (
  `id_subgrupo` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `id_empresa` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id_subgrupo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subgrupo`
--

LOCK TABLES `subgrupo` WRITE;
/*!40000 ALTER TABLE `subgrupo` DISABLE KEYS */;
INSERT INTO `subgrupo` VALUES (1,'Revestimiento','1'),(2,'Grifería','1'),(3,'Iluminación','1'),(4,'Construcción','1');
/*!40000 ALTER TABLE `subgrupo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sucursales`
--

DROP TABLE IF EXISTS `sucursales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sucursales` (
  `id_sucursal` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `descripcion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `codemp` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sucursales`
--

LOCK TABLES `sucursales` WRITE;
/*!40000 ALTER TABLE `sucursales` DISABLE KEYS */;
INSERT INTO `sucursales` VALUES (1,'Vía Daule','\'\'','1');
/*!40000 ALTER TABLE `sucursales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tiendas`
--

DROP TABLE IF EXISTS `tiendas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tiendas` (
  `id_tienda` int NOT NULL AUTO_INCREMENT,
  `codemp` varchar(100) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_tienda`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tiendas`
--

LOCK TABLES `tiendas` WRITE;
/*!40000 ALTER TABLE `tiendas` DISABLE KEYS */;
/*!40000 ALTER TABLE `tiendas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_cliente`
--

DROP TABLE IF EXISTS `tipo_cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_cliente` (
  `id_tipo` int NOT NULL AUTO_INCREMENT,
  `codemp` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id_tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_cliente`
--

LOCK TABLES `tipo_cliente` WRITE;
/*!40000 ALTER TABLE `tipo_cliente` DISABLE KEYS */;
INSERT INTO `tipo_cliente` VALUES (1,'1','Persona Natural'),(2,'1','Persona Jurídica');
/*!40000 ALTER TABLE `tipo_cliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vendedores`
--

DROP TABLE IF EXISTS `vendedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vendedores` (
  `id_vendedor` int NOT NULL AUTO_INCREMENT,
  `codsuc` varchar(100) DEFAULT '',
  `codemp` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `tienda` varchar(100) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_vendedor`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendedores`
--

LOCK TABLES `vendedores` WRITE;
/*!40000 ALTER TABLE `vendedores` DISABLE KEYS */;
INSERT INTO `vendedores` VALUES (1,'1','1','Alfonso Mora de La Calle','1','alfonso.mora@bonsai.com.ec','$2a$10$5c7d188e5c7738763df6du6bqs3gEl9YtwqBt5UK7zB/RIEc7oY7u');
/*!40000 ALTER TABLE `vendedores` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-11-21 19:16:33
