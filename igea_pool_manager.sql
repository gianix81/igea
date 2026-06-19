-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: igea_pool_manager
-- ------------------------------------------------------
-- Server version	8.4.3

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
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `action` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` int unsigned DEFAULT NULL,
  `old_value` json DEFAULT NULL,
  `new_value` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_audit_user` (`user_id`),
  KEY `idx_audit_entity` (`entity_type`,`entity_id`),
  KEY `idx_audit_created` (`created_at`),
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,1,'create','reservation',1,NULL,'{\"_csrf\": \"a1630e6a98aba7fcdb516d01c2b726c915f515600ea51625fa5aff1855834eb4\", \"notes\": \"\", \"status\": \"confermata\", \"time_slot\": \"intera giornata\", \"usage_date\": \"2026-06-18\", \"customer_id\": \"2\", \"paid_amount\": \"0\", \"people_count\": \"1\", \"total_amount\": \"0\", \"deposit_amount\": \"0\", \"payment_method\": \"contanti\"}','::1','2026-06-18 09:13:43'),(2,1,'create','reservation',2,NULL,'{\"_csrf\": \"b31aab79a3662236a89a7ba768a318569420933601663cf2573dc9c11796f0d4\", \"notes\": \"\", \"_action\": \"new_reservation\", \"time_slot\": \"intera giornata\", \"res_status\": \"confermata\", \"usage_date\": \"2026-06-18\", \"customer_id\": \"2\", \"people_count\": \"1\", \"total_amount\": \"0\"}','::1','2026-06-18 16:08:48'),(3,1,'cancel_reservation','reservation',2,NULL,NULL,'::1','2026-06-18 16:24:29'),(4,1,'cancel_reservation','reservation',1,NULL,NULL,'::1','2026-06-18 16:24:31'),(5,1,'book_places','reservation',3,NULL,'{\"date\": \"2026-06-18\", \"place_ids\": [1, 2, 3, 4, 5, 6]}','::1','2026-06-18 16:27:59'),(6,1,'create','reservation',4,NULL,'{\"_csrf\": \"b31aab79a3662236a89a7ba768a318569420933601663cf2573dc9c11796f0d4\", \"notes\": \"\", \"_action\": \"new_reservation\", \"time_slot\": \"intera giornata\", \"res_status\": \"confermata\", \"usage_date\": \"2026-06-18\", \"customer_id\": \"1\", \"people_count\": \"1\", \"total_amount\": \"0\", \"usage_date_to\": \"2026-06-03\"}','::1','2026-06-18 16:28:09'),(7,1,'book_places','reservation',5,NULL,'{\"date\": \"2026-06-18\", \"place_ids\": [7, 8, 9]}','::1','2026-06-18 16:28:17'),(8,1,'cancel_reservation','reservation',4,NULL,NULL,'::1','2026-06-18 16:28:23'),(9,1,'cancel_reservation','reservation',5,NULL,NULL,'::1','2026-06-18 16:44:58'),(10,1,'cancel_reservation','reservation',3,NULL,NULL,'::1','2026-06-18 16:45:01'),(11,1,'book_places','reservation',6,NULL,'{\"date\": \"2026-06-18\", \"place_ids\": [1, 2, 3, 4]}','::1','2026-06-18 16:45:11'),(12,1,'cancel_reservation','reservation',6,NULL,NULL,'::1','2026-06-18 16:45:22'),(13,1,'book_places','reservation',7,NULL,'{\"date\": \"2026-06-18\", \"place_ids\": [1, 2, 3, 4]}','::1','2026-06-18 16:45:35'),(14,1,'create','reservation',8,NULL,'{\"_csrf\": \"b31aab79a3662236a89a7ba768a318569420933601663cf2573dc9c11796f0d4\", \"notes\": \"\", \"_action\": \"new_reservation\", \"time_slot\": \"pomeriggio\", \"res_status\": \"in attesa\", \"usage_date\": \"2026-06-18\", \"customer_id\": \"1\", \"people_count\": \"1\", \"total_amount\": \"0\", \"usage_date_to\": \"2026-06-18\"}','::1','2026-06-18 16:45:46'),(15,1,'create','reservation',9,NULL,'{\"_csrf\": \"b31aab79a3662236a89a7ba768a318569420933601663cf2573dc9c11796f0d4\", \"notes\": \"\", \"_action\": \"new_reservation\", \"time_slot\": \"pomeriggio\", \"res_status\": \"in attesa\", \"usage_date\": \"2026-06-18\", \"customer_id\": \"1\", \"people_count\": \"1\", \"total_amount\": \"0\", \"usage_date_to\": \"2026-06-18\"}','::1','2026-06-18 16:45:56'),(16,1,'create','reservation',10,NULL,'{\"_csrf\": \"b31aab79a3662236a89a7ba768a318569420933601663cf2573dc9c11796f0d4\", \"notes\": \"\", \"_action\": \"new_reservation\", \"time_slot\": \"pomeriggio\", \"res_status\": \"in attesa\", \"usage_date\": \"2026-06-18\", \"customer_id\": \"1\", \"people_count\": \"1\", \"total_amount\": \"0\", \"usage_date_to\": \"2026-06-18\"}','::1','2026-06-18 16:48:13'),(17,1,'cancel_reservation','reservation',10,NULL,NULL,'::1','2026-06-18 16:48:18'),(18,1,'cancel_reservation','reservation',9,NULL,NULL,'::1','2026-06-18 16:48:20'),(19,1,'cancel_reservation','reservation',8,NULL,NULL,'::1','2026-06-18 16:48:22'),(20,1,'cancel_reservation','reservation',7,NULL,NULL,'::1','2026-06-18 16:48:24'),(21,1,'book_places','reservation',11,NULL,'{\"date\": \"2026-06-18\", \"place_ids\": [1, 2, 3]}','::1','2026-06-18 17:12:02'),(22,1,'book_places','reservation',12,NULL,'{\"date\": \"2026-06-18\", \"place_ids\": [4, 5, 6, 7]}','::1','2026-06-18 18:21:16'),(23,1,'create','reservation',13,NULL,'{\"_csrf\": \"b31aab79a3662236a89a7ba768a318569420933601663cf2573dc9c11796f0d4\", \"notes\": \"\", \"_action\": \"new_reservation\", \"time_slot\": \"intera giornata\", \"res_status\": \"in attesa\", \"usage_date\": \"2026-06-18\", \"customer_id\": \"1\", \"people_count\": \"1\", \"total_amount\": \"0\", \"usage_date_to\": \"2026-06-24\"}','::1','2026-06-18 18:21:27'),(24,1,'book_places','reservation',14,NULL,'{\"date\": \"2026-06-18\", \"place_ids\": [8, 9, 10, 11, 12]}','::1','2026-06-18 18:21:44'),(25,1,'book_places','reservation',15,NULL,'{\"date\": \"2026-06-18\", \"place_ids\": [13]}','::1','2026-06-18 18:22:00'),(26,1,'edit_reservation','reservation',14,NULL,'{\"date\": \"2026-06-18\", \"status\": \"in attesa\", \"customer_id\": 1}','::1','2026-06-18 18:22:18'),(27,1,'create_extra_place','pool_place',106,NULL,'{\"code\": \"EX01\", \"area_id\": 1}','::1','2026-06-18 18:31:40'),(28,1,'move_place','pool_place',106,NULL,'{\"pos_col\": 9, \"pos_row\": 1}','::1','2026-06-18 18:38:14'),(29,1,'move_place','pool_place',106,NULL,'{\"pos_col\": 9, \"pos_row\": 1}','::1','2026-06-18 18:38:21'),(30,1,'move_place','pool_place',28,NULL,'{\"pos_col\": 9, \"pos_row\": 2}','::1','2026-06-18 18:41:20'),(31,1,'move_place','pool_place',28,NULL,'{\"pos_col\": 8, \"pos_row\": 2}','::1','2026-06-18 18:41:21'),(32,1,'delete_place','pool_place',106,NULL,'{\"code\": \"EX01\"}','::1','2026-06-18 18:52:12'),(33,1,'move_place','pool_place',10,NULL,'{\"pos_col\": 10, \"pos_row\": 0}','::1','2026-06-18 18:52:48'),(34,1,'move_place','pool_place',10,NULL,'{\"pos_col\": 11, \"pos_row\": 0}','::1','2026-06-18 18:52:49'),(35,1,'move_place','pool_place',10,NULL,'{\"pos_col\": 9, \"pos_row\": 0}','::1','2026-06-18 18:52:50'),(36,1,'create_extra_place','pool_place',107,NULL,'{\"code\": \"A11\", \"area_id\": 1}','::1','2026-06-18 18:53:04'),(37,1,'move_place','pool_place',107,NULL,'{\"pos_col\": 10, \"pos_row\": 0}','::1','2026-06-18 18:53:09'),(38,1,'delete_place','pool_place',107,NULL,'{\"code\": \"A11\"}','::1','2026-06-18 19:00:45'),(39,1,'checkin','entry',1,NULL,'{\"_csrf\": \"42dea4769dfb34185d37d5695637b3fea993591a57fed1784c6a6168494e8d24\", \"notes\": \"\", \"card_code\": \"CARD002\", \"entry_fee\": \"0\", \"place_ids\": [\"8\"], \"paid_amount\": \"0\", \"people_count\": \"1\", \"payment_method\": \"\"}','::1','2026-06-19 03:59:46'),(40,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 1, \"product\": \"Acqua\"}','::1','2026-06-19 04:02:17'),(41,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 2.5, \"product\": \"Coca Cola\"}','::1','2026-06-19 04:02:19'),(42,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 1.2, \"product\": \"Caffe\"}','::1','2026-06-19 04:02:19'),(43,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 1.5, \"product\": \"Cornetto\"}','::1','2026-06-19 04:02:20'),(44,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 2, \"product\": \"Gelato\"}','::1','2026-06-19 04:02:21'),(45,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 4.5, \"product\": \"Panino\"}','::1','2026-06-19 04:02:22'),(46,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 6, \"product\": \"Insalata\"}','::1','2026-06-19 04:02:23'),(47,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 8, \"product\": \"Primo piatto\"}','::1','2026-06-19 04:02:24'),(48,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 10, \"product\": \"Secondo piatto\"}','::1','2026-06-19 04:02:25'),(49,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 1, \"product\": \"Acqua\"}','::1','2026-06-19 04:02:33'),(50,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 1.2, \"product\": \"Caffe\"}','::1','2026-06-19 04:02:34'),(51,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 1.5, \"product\": \"Cornetto\"}','::1','2026-06-19 04:02:35'),(52,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 2, \"product\": \"Gelato\"}','::1','2026-06-19 04:02:36'),(53,1,'payment','card',2,NULL,'{\"amount\": 42.4, \"method\": \"contanti\", \"reason\": \"saldo finale\"}','::1','2026-06-19 04:08:14'),(54,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 1, \"product\": \"Acqua\"}','::1','2026-06-19 04:09:25'),(55,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 2.5, \"product\": \"Coca Cola\"}','::1','2026-06-19 04:09:26'),(56,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 1, \"product\": \"Acqua\"}','::1','2026-06-19 04:09:26'),(57,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 1, \"product\": \"Acqua\"}','::1','2026-06-19 04:09:27'),(58,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 1, \"product\": \"Acqua\"}','::1','2026-06-19 04:09:27'),(59,1,'charge','card',1,NULL,'{\"qty\": 1, \"total\": 1, \"product\": \"Acqua\"}','::1','2026-06-19 04:10:33'),(60,1,'charge','card',1,NULL,'{\"qty\": 1, \"total\": 2.5, \"product\": \"Coca Cola\"}','::1','2026-06-19 04:10:39'),(61,1,'payment','card',2,NULL,'{\"amount\": 6.5, \"method\": \"contanti\", \"reason\": \"saldo finale\"}','::1','2026-06-19 04:16:10'),(62,1,'charge','card',1,NULL,'{\"qty\": 1, \"total\": 1, \"product\": \"Acqua\"}','::1','2026-06-19 04:21:09'),(63,1,'payment','card',2,NULL,'{\"amount\": 10, \"method\": \"contanti\", \"reason\": \"saldo finale\"}','::1','2026-06-19 04:22:53'),(64,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 1, \"product\": \"Acqua\"}','::1','2026-06-19 04:23:28'),(65,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 2.5, \"product\": \"Coca Cola\"}','::1','2026-06-19 04:23:29'),(66,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 1.2, \"product\": \"Caffe\"}','::1','2026-06-19 04:23:29'),(67,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 1.5, \"product\": \"Cornetto\"}','::1','2026-06-19 04:23:30'),(68,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 2, \"product\": \"Gelato\"}','::1','2026-06-19 04:23:30'),(69,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 4.5, \"product\": \"Panino\"}','::1','2026-06-19 04:23:31'),(70,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 6, \"product\": \"Insalata\"}','::1','2026-06-19 04:23:31'),(71,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 1, \"product\": \"Acqua\"}','::1','2026-06-19 04:24:56'),(72,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 2.5, \"product\": \"Coca Cola\"}','::1','2026-06-19 04:24:56'),(73,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 1.2, \"product\": \"Caffe\"}','::1','2026-06-19 04:24:58'),(74,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 1.5, \"product\": \"Cornetto\"}','::1','2026-06-19 04:24:59'),(75,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 2, \"product\": \"Gelato\"}','::1','2026-06-19 04:24:59'),(76,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 4.5, \"product\": \"Panino\"}','::1','2026-06-19 04:25:00'),(77,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 6, \"product\": \"Insalata\"}','::1','2026-06-19 04:25:01'),(78,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 8, \"product\": \"Primo piatto\"}','::1','2026-06-19 04:25:02'),(79,1,'charge','card',2,NULL,'{\"qty\": 1, \"total\": 10, \"product\": \"Secondo piatto\"}','::1','2026-06-19 04:25:02'),(80,1,'charge_batch','card',2,NULL,'{\"items\": 6}','::1','2026-06-19 04:48:18'),(81,1,'payment','card',2,NULL,'{\"amount\": 58.1, \"method\": \"contanti\", \"reason\": \"saldo finale\"}','::1','2026-06-19 05:06:57'),(82,1,'checkout','entry',1,NULL,'{\"forced\": false, \"balance\": 0}','::1','2026-06-19 05:19:04'),(83,1,'create_product','product',10,NULL,'{\"name\": \"Patatine\", \"price\": 2}','::1','2026-06-19 06:10:00');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `card_movements`
--

DROP TABLE IF EXISTS `card_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `card_movements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `card_id` int unsigned NOT NULL,
  `customer_id` int unsigned NOT NULL,
  `entry_id` int unsigned DEFAULT NULL,
  `product_id` int unsigned DEFAULT NULL,
  `movement_type` enum('charge','payment','refund','adjustment') COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` enum('bar','ristorante','reception','extra','cassa') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT '1.00',
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('open','paid','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `operator_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `cancelled_at` datetime DEFAULT NULL,
  `cancelled_by` int unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `fk_mov_customer` (`customer_id`),
  KEY `fk_mov_entry` (`entry_id`),
  KEY `fk_mov_product` (`product_id`),
  KEY `fk_mov_operator` (`operator_id`),
  KEY `fk_mov_cancelled_by` (`cancelled_by`),
  KEY `idx_mov_card` (`card_id`),
  KEY `idx_mov_status` (`status`),
  KEY `idx_mov_created` (`created_at`),
  CONSTRAINT `fk_mov_cancelled_by` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_mov_card` FOREIGN KEY (`card_id`) REFERENCES `cards` (`id`),
  CONSTRAINT `fk_mov_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `fk_mov_entry` FOREIGN KEY (`entry_id`) REFERENCES `entries` (`id`),
  CONSTRAINT `fk_mov_operator` FOREIGN KEY (`operator_id`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_mov_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `card_movements`
--

LOCK TABLES `card_movements` WRITE;
/*!40000 ALTER TABLE `card_movements` DISABLE KEYS */;
INSERT INTO `card_movements` VALUES (1,2,2,1,2,'charge','bar','Acqua',1.00,1.00,1.00,'open',1,'2026-06-19 04:02:17',NULL,NULL,NULL),(2,2,2,1,3,'charge','bar','Coca Cola',1.00,2.50,2.50,'open',1,'2026-06-19 04:02:19',NULL,NULL,NULL),(3,2,2,1,1,'charge','bar','Caffe',1.00,1.20,1.20,'open',1,'2026-06-19 04:02:19',NULL,NULL,NULL),(4,2,2,1,4,'charge','bar','Cornetto',1.00,1.50,1.50,'open',1,'2026-06-19 04:02:20',NULL,NULL,NULL),(5,2,2,1,9,'charge','bar','Gelato',1.00,2.00,2.00,'open',1,'2026-06-19 04:02:21',NULL,NULL,NULL),(6,2,2,1,5,'charge','bar','Panino',1.00,4.50,4.50,'open',1,'2026-06-19 04:02:22',NULL,NULL,NULL),(7,2,2,1,6,'charge','ristorante','Insalata',1.00,6.00,6.00,'open',1,'2026-06-19 04:02:23',NULL,NULL,NULL),(8,2,2,1,7,'charge','ristorante','Primo piatto',1.00,8.00,8.00,'open',1,'2026-06-19 04:02:24',NULL,NULL,NULL),(9,2,2,1,8,'charge','ristorante','Secondo piatto',1.00,10.00,10.00,'open',1,'2026-06-19 04:02:25',NULL,NULL,NULL),(10,2,2,1,2,'charge','bar','Acqua',1.00,1.00,1.00,'open',1,'2026-06-19 04:02:33',NULL,NULL,NULL),(11,2,2,1,1,'charge','bar','Caffe',1.00,1.20,1.20,'open',1,'2026-06-19 04:02:34',NULL,NULL,NULL),(12,2,2,1,4,'charge','bar','Cornetto',1.00,1.50,1.50,'open',1,'2026-06-19 04:02:35',NULL,NULL,NULL),(13,2,2,1,9,'charge','bar','Gelato',1.00,2.00,2.00,'open',1,'2026-06-19 04:02:36',NULL,NULL,NULL),(14,2,2,1,NULL,'payment','cassa','Pagamento saldo finale',1.00,42.40,42.40,'paid',1,'2026-06-19 04:08:14',NULL,NULL,''),(15,2,2,1,2,'charge','bar','Acqua',1.00,1.00,1.00,'open',1,'2026-06-19 04:09:25',NULL,NULL,NULL),(16,2,2,1,3,'charge','bar','Coca Cola',1.00,2.50,2.50,'open',1,'2026-06-19 04:09:26',NULL,NULL,NULL),(17,2,2,1,2,'charge','bar','Acqua',1.00,1.00,1.00,'open',1,'2026-06-19 04:09:26',NULL,NULL,NULL),(18,2,2,1,2,'charge','bar','Acqua',1.00,1.00,1.00,'open',1,'2026-06-19 04:09:27',NULL,NULL,NULL),(19,2,2,1,2,'charge','bar','Acqua',1.00,1.00,1.00,'open',1,'2026-06-19 04:09:27',NULL,NULL,NULL),(20,1,1,NULL,2,'charge','bar','Acqua',1.00,1.00,1.00,'open',1,'2026-06-19 04:10:33',NULL,NULL,NULL),(21,1,1,NULL,3,'charge','bar','Coca Cola',1.00,2.50,2.50,'open',1,'2026-06-19 04:10:39',NULL,NULL,NULL),(22,2,2,1,NULL,'payment','cassa','Pagamento saldo finale',1.00,6.50,6.50,'paid',1,'2026-06-19 04:16:10',NULL,NULL,NULL),(23,1,1,NULL,2,'charge','bar','Acqua',1.00,1.00,1.00,'open',1,'2026-06-19 04:21:09',NULL,NULL,NULL),(24,2,2,1,NULL,'payment','cassa','Pagamento saldo finale',1.00,10.00,10.00,'paid',1,'2026-06-19 04:22:53',NULL,NULL,NULL),(25,2,2,1,2,'charge','bar','Acqua',1.00,1.00,1.00,'open',1,'2026-06-19 04:23:28',NULL,NULL,NULL),(26,2,2,1,3,'charge','bar','Coca Cola',1.00,2.50,2.50,'open',1,'2026-06-19 04:23:29',NULL,NULL,NULL),(27,2,2,1,1,'charge','bar','Caffe',1.00,1.20,1.20,'open',1,'2026-06-19 04:23:29',NULL,NULL,NULL),(28,2,2,1,4,'charge','bar','Cornetto',1.00,1.50,1.50,'open',1,'2026-06-19 04:23:30',NULL,NULL,NULL),(29,2,2,1,9,'charge','bar','Gelato',1.00,2.00,2.00,'open',1,'2026-06-19 04:23:30',NULL,NULL,NULL),(30,2,2,1,5,'charge','bar','Panino',1.00,4.50,4.50,'open',1,'2026-06-19 04:23:31',NULL,NULL,NULL),(31,2,2,1,6,'charge','ristorante','Insalata',1.00,6.00,6.00,'open',1,'2026-06-19 04:23:31',NULL,NULL,NULL),(32,2,2,1,2,'charge','bar','Acqua',1.00,1.00,1.00,'open',1,'2026-06-19 04:24:56',NULL,NULL,NULL),(33,2,2,1,3,'charge','bar','Coca Cola',1.00,2.50,2.50,'open',1,'2026-06-19 04:24:56',NULL,NULL,NULL),(34,2,2,1,1,'charge','bar','Caffe',1.00,1.20,1.20,'open',1,'2026-06-19 04:24:58',NULL,NULL,NULL),(35,2,2,1,4,'charge','bar','Cornetto',1.00,1.50,1.50,'open',1,'2026-06-19 04:24:59',NULL,NULL,NULL),(36,2,2,1,9,'charge','bar','Gelato',1.00,2.00,2.00,'open',1,'2026-06-19 04:24:59',NULL,NULL,NULL),(37,2,2,1,5,'charge','bar','Panino',1.00,4.50,4.50,'open',1,'2026-06-19 04:25:00',NULL,NULL,NULL),(38,2,2,1,6,'charge','ristorante','Insalata',1.00,6.00,6.00,'open',1,'2026-06-19 04:25:01',NULL,NULL,NULL),(39,2,2,1,7,'charge','ristorante','Primo piatto',1.00,8.00,8.00,'open',1,'2026-06-19 04:25:02',NULL,NULL,NULL),(40,2,2,1,8,'charge','ristorante','Secondo piatto',1.00,10.00,10.00,'open',1,'2026-06-19 04:25:02',NULL,NULL,NULL),(41,2,2,1,2,'charge','bar','Acqua',1.00,1.00,1.00,'open',1,'2026-06-19 04:48:18',NULL,NULL,NULL),(42,2,2,1,3,'charge','bar','Coca Cola',1.00,2.50,2.50,'open',1,'2026-06-19 04:48:18',NULL,NULL,NULL),(43,2,2,1,1,'charge','bar','Caffe',1.00,1.20,1.20,'open',1,'2026-06-19 04:48:18',NULL,NULL,NULL),(44,2,2,1,4,'charge','bar','Cornetto',1.00,1.50,1.50,'open',1,'2026-06-19 04:48:18',NULL,NULL,NULL),(45,2,2,1,9,'charge','bar','Gelato',1.00,2.00,2.00,'open',1,'2026-06-19 04:48:18',NULL,NULL,NULL),(46,2,2,1,5,'charge','bar','Panino',1.00,4.50,4.50,'open',1,'2026-06-19 04:48:18',NULL,NULL,NULL),(47,2,2,1,NULL,'payment','cassa','Pagamento saldo finale',1.00,58.10,58.10,'paid',1,'2026-06-19 05:06:57',NULL,NULL,NULL);
/*!40000 ALTER TABLE `card_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cards`
--

DROP TABLE IF EXISTS `cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cards` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `card_code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nfc_uid` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_id` int unsigned NOT NULL,
  `card_type` enum('nominale','abbonamento','ospite','staff') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'nominale',
  `status` enum('attiva','chiusa','bloccata','smarrita') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'attiva',
  `activated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime DEFAULT NULL,
  `current_balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_paid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_inside` tinyint(1) NOT NULL DEFAULT '0',
  `active_entry_id` int unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `card_code` (`card_code`),
  UNIQUE KEY `idx_cards_nfc` (`nfc_uid`),
  KEY `fk_cards_customer` (`customer_id`),
  KEY `idx_cards_code` (`card_code`),
  KEY `idx_cards_status` (`status`),
  KEY `fk_cards_active_entry` (`active_entry_id`),
  CONSTRAINT `fk_cards_active_entry` FOREIGN KEY (`active_entry_id`) REFERENCES `entries` (`id`),
  CONSTRAINT `fk_cards_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cards`
--

LOCK TABLES `cards` WRITE;
/*!40000 ALTER TABLE `cards` DISABLE KEYS */;
INSERT INTO `cards` VALUES (1,'CARD001',NULL,1,'nominale','attiva','2026-06-16 22:09:48',NULL,4.50,0.00,0,NULL,NULL,'2026-06-16 20:09:48','2026-06-19 05:32:11'),(2,'CARD002',NULL,2,'nominale','attiva','2026-06-16 22:09:48',NULL,0.00,117.00,0,NULL,NULL,'2026-06-16 20:09:48','2026-06-19 05:32:11');
/*!40000 ALTER TABLE `cards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fiscal_code` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `privacy_consent` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('attivo','sospeso','blacklist') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'attivo',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `doc_type` enum('carta_identita','passaporto','patente','permesso_soggiorno') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_number` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_expiry` date DEFAULT NULL,
  `doc_issuer` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `privacy_signed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_customers_last_name` (`last_name`),
  KEY `idx_customers_phone` (`phone`),
  KEY `idx_customers_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'Mario','Rossi','3331112222','mario.rossi@example.test',NULL,NULL,NULL,1,'attivo',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-06-16 20:09:48','2026-06-16 20:09:48'),(2,'Laura','Bianchi','3332223333','laura.bianchi@example.test',NULL,NULL,NULL,1,'attivo',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-06-16 20:09:48','2026-06-16 20:09:48');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `daily_closures`
--

DROP TABLE IF EXISTS `daily_closures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_closures` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `closure_date` date NOT NULL,
  `total_entries` int unsigned NOT NULL DEFAULT '0',
  `total_charges` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_payments` decimal(10,2) NOT NULL DEFAULT '0.00',
  `open_balances` decimal(10,2) NOT NULL DEFAULT '0.00',
  `closed_by` int unsigned NOT NULL,
  `closed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `snapshot_json` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `closure_date` (`closure_date`),
  KEY `fk_closures_user` (`closed_by`),
  CONSTRAINT `fk_closures_user` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_closures`
--

LOCK TABLES `daily_closures` WRITE;
/*!40000 ALTER TABLE `daily_closures` DISABLE KEYS */;
/*!40000 ALTER TABLE `daily_closures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entries`
--

DROP TABLE IF EXISTS `entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `entries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int unsigned NOT NULL,
  `card_id` int unsigned NOT NULL,
  `reservation_id` int unsigned DEFAULT NULL,
  `entry_date` date NOT NULL,
  `checkin_at` datetime NOT NULL,
  `checkout_at` datetime DEFAULT NULL,
  `status` enum('dentro','uscito','bloccato','annullato') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dentro',
  `people_count` int unsigned NOT NULL DEFAULT '1',
  `entry_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_method` enum('contanti','carta','bonifico','satispay','altro') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` int unsigned DEFAULT NULL,
  `closed_by` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_entries_customer` (`customer_id`),
  KEY `fk_entries_card` (`card_id`),
  KEY `fk_entries_res` (`reservation_id`),
  KEY `fk_entries_created_by` (`created_by`),
  KEY `fk_entries_closed_by` (`closed_by`),
  KEY `idx_entries_date` (`entry_date`),
  KEY `idx_entries_status` (`status`),
  CONSTRAINT `fk_entries_card` FOREIGN KEY (`card_id`) REFERENCES `cards` (`id`),
  CONSTRAINT `fk_entries_closed_by` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_entries_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_entries_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `fk_entries_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entries`
--

LOCK TABLES `entries` WRITE;
/*!40000 ALTER TABLE `entries` DISABLE KEYS */;
INSERT INTO `entries` VALUES (1,2,2,NULL,'2026-06-19','2026-06-19 05:59:46','2026-06-19 07:19:04','uscito',1,0.00,0.00,NULL,'',1,1,'2026-06-19 03:59:46','2026-06-19 05:19:04');
/*!40000 ALTER TABLE `entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entry_places`
--

DROP TABLE IF EXISTS `entry_places`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `entry_places` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `entry_id` int unsigned NOT NULL,
  `place_id` int unsigned NOT NULL,
  `assigned_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `released_at` datetime DEFAULT NULL,
  `status` enum('assegnato','liberato','annullato') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'assegnato',
  PRIMARY KEY (`id`),
  KEY `fk_entry_places_entry` (`entry_id`),
  KEY `fk_entry_places_place` (`place_id`),
  KEY `idx_entry_places_status` (`status`),
  CONSTRAINT `fk_entry_places_entry` FOREIGN KEY (`entry_id`) REFERENCES `entries` (`id`),
  CONSTRAINT `fk_entry_places_place` FOREIGN KEY (`place_id`) REFERENCES `pool_places` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entry_places`
--

LOCK TABLES `entry_places` WRITE;
/*!40000 ALTER TABLE `entry_places` DISABLE KEYS */;
INSERT INTO `entry_places` VALUES (1,1,8,'2026-06-19 05:59:46','2026-06-19 07:19:04','liberato');
/*!40000 ALTER TABLE `entry_places` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `card_id` int unsigned NOT NULL,
  `customer_id` int unsigned NOT NULL,
  `entry_id` int unsigned DEFAULT NULL,
  `reservation_id` int unsigned DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('contanti','carta','bonifico','satispay','altro') COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` enum('ingresso','consumazioni','saldo finale','acconto prenotazione','extra') COLLATE utf8mb4_unicode_ci NOT NULL,
  `operator_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `fk_pay_customer` (`customer_id`),
  KEY `fk_pay_entry` (`entry_id`),
  KEY `fk_pay_res` (`reservation_id`),
  KEY `fk_pay_operator` (`operator_id`),
  KEY `idx_pay_card` (`card_id`),
  KEY `idx_pay_created` (`created_at`),
  CONSTRAINT `fk_pay_card` FOREIGN KEY (`card_id`) REFERENCES `cards` (`id`),
  CONSTRAINT `fk_pay_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `fk_pay_entry` FOREIGN KEY (`entry_id`) REFERENCES `entries` (`id`),
  CONSTRAINT `fk_pay_operator` FOREIGN KEY (`operator_id`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_pay_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,2,2,1,NULL,42.40,'contanti','saldo finale',1,'2026-06-19 04:08:14',''),(2,2,2,1,NULL,6.50,'contanti','saldo finale',1,'2026-06-19 04:16:10',NULL),(3,2,2,1,NULL,10.00,'contanti','saldo finale',1,'2026-06-19 04:22:53',NULL),(4,2,2,1,NULL,58.10,'contanti','saldo finale',1,'2026-06-19 05:06:57',NULL);
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pool_areas`
--

DROP TABLE IF EXISTS `pool_areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pool_areas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pool_areas`
--

LOCK TABLES `pool_areas` WRITE;
/*!40000 ALTER TABLE `pool_areas` DISABLE KEYS */;
INSERT INTO `pool_areas` VALUES (1,'Solarium','File SAÔÇôSE vicino alla piscina',1),(2,'Zona Ombrelloni','Blocco sinistro (colonne OAÔÇôOD) + blocco destro (file ORAÔÇôORD)',1);
/*!40000 ALTER TABLE `pool_areas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pool_places`
--

DROP TABLE IF EXISTS `pool_places`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pool_places` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `area_id` int unsigned NOT NULL,
  `code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `row_label` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number` int unsigned NOT NULL,
  `type` enum('lettino','sdraio','ombrellone','tavolo','cabana') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'lettino',
  `base_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('disponibile','prenotato','occupato','manutenzione','bloccato','liberato') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disponibile',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `pos_row` tinyint unsigned DEFAULT NULL,
  `pos_col` tinyint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `fk_places_area` (`area_id`),
  KEY `idx_pool_places_status` (`status`),
  KEY `idx_pool_places_pos` (`pos_row`,`pos_col`),
  CONSTRAINT `fk_places_area` FOREIGN KEY (`area_id`) REFERENCES `pool_areas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pool_places`
--

LOCK TABLES `pool_places` WRITE;
/*!40000 ALTER TABLE `pool_places` DISABLE KEYS */;
INSERT INTO `pool_places` VALUES (1,1,'SA01','SA',1,'lettino',8.00,'prenotato',NULL,0,0,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(2,1,'SA02','SA',2,'lettino',8.00,'prenotato',NULL,0,1,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(3,1,'SA03','SA',3,'lettino',8.00,'prenotato',NULL,0,2,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(4,1,'SA04','SA',4,'lettino',8.00,'prenotato',NULL,0,3,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(5,1,'SA05','SA',5,'lettino',8.00,'prenotato',NULL,0,4,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(6,1,'SA06','SA',6,'lettino',8.00,'prenotato',NULL,0,5,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(7,1,'SA07','SA',7,'lettino',8.00,'prenotato',NULL,0,6,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(8,1,'SA08','SA',8,'lettino',8.00,'disponibile',NULL,0,7,'2026-06-18 15:42:28','2026-06-19 05:19:04'),(9,1,'SA09','SA',9,'lettino',8.00,'disponibile',NULL,0,8,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(10,1,'SA10','SA',10,'lettino',8.00,'disponibile',NULL,0,9,'2026-06-18 15:42:28','2026-06-18 18:52:50'),(11,1,'SB01','SB',1,'lettino',8.00,'disponibile',NULL,1,0,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(12,1,'SB02','SB',2,'lettino',8.00,'disponibile',NULL,1,1,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(13,1,'SB03','SB',3,'lettino',8.00,'prenotato',NULL,1,2,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(14,1,'SB04','SB',4,'lettino',8.00,'prenotato',NULL,1,3,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(15,1,'SB05','SB',5,'lettino',8.00,'prenotato',NULL,1,4,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(16,1,'SB06','SB',6,'lettino',8.00,'prenotato',NULL,1,5,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(17,1,'SB07','SB',7,'lettino',8.00,'prenotato',NULL,1,6,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(18,1,'SB08','SB',8,'lettino',8.00,'disponibile',NULL,1,7,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(19,1,'SB09','SB',9,'lettino',8.00,'disponibile',NULL,1,8,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(20,1,'SC01','SC',1,'lettino',8.00,'disponibile',NULL,2,0,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(21,1,'SC02','SC',2,'lettino',8.00,'disponibile',NULL,2,1,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(22,1,'SC03','SC',3,'lettino',8.00,'disponibile',NULL,2,2,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(23,1,'SC04','SC',4,'lettino',8.00,'disponibile',NULL,2,3,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(24,1,'SC05','SC',5,'lettino',8.00,'disponibile',NULL,2,4,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(25,1,'SC06','SC',6,'lettino',8.00,'disponibile',NULL,2,5,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(26,1,'SC07','SC',7,'lettino',8.00,'disponibile',NULL,2,6,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(27,1,'SC08','SC',8,'lettino',8.00,'disponibile',NULL,2,7,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(28,1,'SC09','SC',9,'lettino',8.00,'disponibile',NULL,2,8,'2026-06-18 15:42:28','2026-06-18 18:41:21'),(29,1,'SD01','SD',1,'lettino',8.00,'disponibile',NULL,3,0,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(30,1,'SD02','SD',2,'lettino',8.00,'disponibile',NULL,3,1,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(31,1,'SD03','SD',3,'lettino',8.00,'disponibile',NULL,3,2,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(32,1,'SD04','SD',4,'lettino',8.00,'disponibile',NULL,3,3,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(33,1,'SD05','SD',5,'lettino',8.00,'disponibile',NULL,3,4,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(34,1,'SD06','SD',6,'lettino',8.00,'disponibile',NULL,3,5,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(35,1,'SD07','SD',7,'lettino',8.00,'disponibile',NULL,3,6,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(36,1,'SD08','SD',8,'lettino',8.00,'disponibile',NULL,3,7,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(37,1,'SD09','SD',9,'lettino',8.00,'disponibile',NULL,3,8,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(38,1,'SE01','SE',1,'lettino',8.00,'disponibile',NULL,4,0,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(39,1,'SE02','SE',2,'lettino',8.00,'disponibile',NULL,4,1,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(40,1,'SE03','SE',3,'lettino',8.00,'disponibile',NULL,4,2,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(41,1,'SE04','SE',4,'lettino',8.00,'disponibile',NULL,4,3,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(42,1,'SE05','SE',5,'lettino',8.00,'disponibile',NULL,4,4,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(43,1,'SE06','SE',6,'lettino',8.00,'disponibile',NULL,4,5,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(44,1,'SE07','SE',7,'lettino',8.00,'disponibile',NULL,4,6,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(45,1,'SE08','SE',8,'lettino',8.00,'disponibile',NULL,4,7,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(46,1,'SE09','SE',9,'lettino',8.00,'disponibile',NULL,4,8,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(47,2,'OA01','OA',1,'lettino',7.00,'disponibile',NULL,6,0,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(48,2,'OA02','OA',2,'lettino',7.00,'disponibile',NULL,6,1,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(49,2,'OA03','OA',3,'lettino',7.00,'disponibile',NULL,6,2,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(50,2,'OA04','OA',4,'lettino',7.00,'disponibile',NULL,6,3,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(51,2,'OA05','OA',5,'lettino',7.00,'disponibile',NULL,6,4,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(52,2,'OA06','OA',6,'lettino',7.00,'disponibile',NULL,6,5,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(53,2,'OA07','OA',7,'lettino',7.00,'disponibile',NULL,6,6,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(54,2,'OA08','OA',8,'lettino',7.00,'disponibile',NULL,6,7,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(55,2,'OB01','OB',1,'lettino',7.00,'disponibile',NULL,7,0,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(56,2,'OB02','OB',2,'lettino',7.00,'disponibile',NULL,7,1,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(57,2,'OB03','OB',3,'lettino',7.00,'disponibile',NULL,7,2,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(58,2,'OB04','OB',4,'lettino',7.00,'disponibile',NULL,7,3,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(59,2,'OB05','OB',5,'lettino',7.00,'disponibile',NULL,7,4,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(60,2,'OB06','OB',6,'lettino',7.00,'disponibile',NULL,7,5,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(61,2,'OB07','OB',7,'lettino',7.00,'disponibile',NULL,7,6,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(62,2,'OB08','OB',8,'lettino',7.00,'disponibile',NULL,7,7,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(63,2,'OC01','OC',1,'lettino',7.00,'disponibile',NULL,8,0,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(64,2,'OC02','OC',2,'lettino',7.00,'disponibile',NULL,8,1,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(65,2,'OC03','OC',3,'lettino',7.00,'disponibile',NULL,8,2,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(66,2,'OC04','OC',4,'lettino',7.00,'disponibile',NULL,8,3,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(67,2,'OC05','OC',5,'lettino',7.00,'disponibile',NULL,8,4,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(68,2,'OC06','OC',6,'lettino',7.00,'disponibile',NULL,8,5,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(69,2,'OC07','OC',7,'lettino',7.00,'disponibile',NULL,8,6,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(70,2,'OC08','OC',8,'lettino',7.00,'disponibile',NULL,8,7,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(71,2,'OD01','OD',1,'lettino',7.00,'disponibile',NULL,9,0,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(72,2,'OD02','OD',2,'lettino',7.00,'disponibile',NULL,9,1,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(73,2,'OD03','OD',3,'lettino',7.00,'disponibile',NULL,9,2,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(74,2,'OD04','OD',4,'lettino',7.00,'disponibile',NULL,9,3,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(75,2,'OD05','OD',5,'lettino',7.00,'disponibile',NULL,9,4,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(76,2,'OD06','OD',6,'lettino',7.00,'disponibile',NULL,9,5,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(77,2,'OD07','OD',7,'lettino',7.00,'disponibile',NULL,9,6,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(78,2,'OD08','OD',8,'lettino',7.00,'disponibile',NULL,9,7,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(79,2,'OE01','OE',1,'lettino',7.00,'disponibile',NULL,10,0,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(80,2,'OE02','OE',2,'lettino',7.00,'disponibile',NULL,10,1,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(81,2,'OP01','OP',1,'lettino',7.00,'disponibile',NULL,10,13,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(82,2,'OP02','OP',2,'lettino',7.00,'disponibile',NULL,10,14,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(83,2,'ORA01','ORA',1,'lettino',7.00,'disponibile',NULL,6,10,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(84,2,'ORA02','ORA',2,'lettino',7.00,'disponibile',NULL,6,11,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(85,2,'ORA03','ORA',3,'lettino',7.00,'disponibile',NULL,6,12,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(86,2,'ORA04','ORA',4,'lettino',7.00,'disponibile',NULL,6,13,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(87,2,'ORA05','ORA',5,'lettino',7.00,'disponibile',NULL,6,14,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(88,2,'ORA06','ORA',6,'lettino',7.00,'disponibile',NULL,6,15,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(89,2,'ORB01','ORB',1,'lettino',7.00,'disponibile',NULL,7,10,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(90,2,'ORB02','ORB',2,'lettino',7.00,'disponibile',NULL,7,11,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(91,2,'ORB03','ORB',3,'lettino',7.00,'disponibile',NULL,7,12,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(92,2,'ORB04','ORB',4,'lettino',7.00,'disponibile',NULL,7,13,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(93,2,'ORB05','ORB',5,'lettino',7.00,'disponibile',NULL,7,14,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(94,2,'ORB06','ORB',6,'lettino',7.00,'disponibile',NULL,7,15,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(95,2,'ORC01','ORC',1,'lettino',7.00,'disponibile',NULL,8,10,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(96,2,'ORC02','ORC',2,'lettino',7.00,'disponibile',NULL,8,11,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(97,2,'ORC03','ORC',3,'lettino',7.00,'disponibile',NULL,8,12,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(98,2,'ORC04','ORC',4,'lettino',7.00,'disponibile',NULL,8,13,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(99,2,'ORC05','ORC',5,'lettino',7.00,'disponibile',NULL,8,14,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(100,2,'ORC06','ORC',6,'lettino',7.00,'disponibile',NULL,8,15,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(101,2,'ORD01','ORD',1,'lettino',7.00,'disponibile',NULL,9,10,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(102,2,'ORD02','ORD',2,'lettino',7.00,'disponibile',NULL,9,11,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(103,2,'ORD03','ORD',3,'lettino',7.00,'disponibile',NULL,9,12,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(104,2,'ORD04','ORD',4,'lettino',7.00,'disponibile',NULL,9,13,'2026-06-18 15:42:28','2026-06-18 18:37:44'),(105,2,'ORD05','ORD',5,'lettino',7.00,'disponibile',NULL,9,14,'2026-06-18 15:42:28','2026-06-18 18:37:44');
/*!40000 ALTER TABLE `pool_places` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_categories`
--

DROP TABLE IF EXISTS `product_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` enum('bar','ristorante','reception','extra') COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_categories`
--

LOCK TABLES `product_categories` WRITE;
/*!40000 ALTER TABLE `product_categories` DISABLE KEYS */;
INSERT INTO `product_categories` VALUES (1,'Caffetteria','bar',1),(2,'Bibite','bar',1),(3,'Snack','bar',1),(4,'Ristorazione','ristorante',1),(5,'Extra piscina','extra',1);
/*!40000 ALTER TABLE `product_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int unsigned NOT NULL,
  `name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `vat_rate` decimal(5,2) DEFAULT NULL,
  `stock_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `stock_qty` decimal(10,2) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_products_category` (`category_id`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,'Caffe',1.20,10.00,0,NULL,1,NULL,'2026-06-16 20:09:48','2026-06-16 20:09:48'),(2,2,'Acqua',1.00,10.00,0,NULL,1,NULL,'2026-06-16 20:09:48','2026-06-16 20:09:48'),(3,2,'Coca Cola',2.50,22.00,0,NULL,1,NULL,'2026-06-16 20:09:48','2026-06-16 20:09:48'),(4,3,'Cornetto',1.50,10.00,0,NULL,1,NULL,'2026-06-16 20:09:48','2026-06-16 20:09:48'),(5,3,'Panino',4.50,10.00,0,NULL,1,NULL,'2026-06-16 20:09:48','2026-06-16 20:09:48'),(6,4,'Insalata',6.00,10.00,0,NULL,1,NULL,'2026-06-16 20:09:48','2026-06-16 20:09:48'),(7,4,'Primo piatto',8.00,10.00,0,NULL,1,NULL,'2026-06-16 20:09:48','2026-06-16 20:09:48'),(8,4,'Secondo piatto',10.00,10.00,0,NULL,1,NULL,'2026-06-16 20:09:48','2026-06-16 20:09:48'),(9,3,'Gelato',2.00,10.00,0,NULL,1,NULL,'2026-06-16 20:09:48','2026-06-16 20:09:48'),(10,3,'Patatine',2.00,10.00,0,NULL,1,NULL,'2026-06-19 06:10:00','2026-06-19 06:10:00');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservation_places`
--

DROP TABLE IF EXISTS `reservation_places`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservation_places` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reservation_id` int unsigned NOT NULL,
  `place_id` int unsigned NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('prenotato','occupato','liberato','cancellato') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'prenotato',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_res_place` (`reservation_id`,`place_id`),
  KEY `fk_res_places_place` (`place_id`),
  CONSTRAINT `fk_res_places_place` FOREIGN KEY (`place_id`) REFERENCES `pool_places` (`id`),
  CONSTRAINT `fk_res_places_res` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservation_places`
--

LOCK TABLES `reservation_places` WRITE;
/*!40000 ALTER TABLE `reservation_places` DISABLE KEYS */;
INSERT INTO `reservation_places` VALUES (1,3,1,8.00,'cancellato'),(2,3,2,8.00,'cancellato'),(3,3,3,8.00,'cancellato'),(4,3,4,8.00,'cancellato'),(5,3,5,8.00,'cancellato'),(6,3,6,8.00,'cancellato'),(7,5,7,8.00,'cancellato'),(8,5,8,8.00,'cancellato'),(9,5,9,8.00,'cancellato'),(10,6,1,8.00,'cancellato'),(11,6,2,8.00,'cancellato'),(12,6,3,8.00,'cancellato'),(13,6,4,8.00,'cancellato'),(14,7,1,8.00,'cancellato'),(15,7,2,8.00,'cancellato'),(16,7,3,8.00,'cancellato'),(17,7,4,8.00,'cancellato'),(18,11,1,8.00,'prenotato'),(19,11,2,8.00,'prenotato'),(20,11,3,8.00,'prenotato'),(21,12,4,8.00,'prenotato'),(22,12,5,8.00,'prenotato'),(23,12,6,8.00,'prenotato'),(24,12,7,8.00,'prenotato'),(30,15,13,8.00,'prenotato'),(31,14,14,8.00,'prenotato'),(32,14,15,8.00,'prenotato'),(33,14,16,8.00,'prenotato'),(34,14,17,8.00,'prenotato');
/*!40000 ALTER TABLE `reservation_places` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reservation_code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` int unsigned NOT NULL,
  `reservation_date` date NOT NULL,
  `usage_date` date NOT NULL,
  `time_slot` enum('intera giornata','mattina','pomeriggio') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'intera giornata',
  `people_count` int unsigned NOT NULL DEFAULT '1',
  `status` enum('confermata','in attesa','cancellata','no-show','completata') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in attesa',
  `deposit_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_method` enum('contanti','carta','bonifico','satispay','altro') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reservation_code` (`reservation_code`),
  KEY `fk_res_customer` (`customer_id`),
  KEY `fk_res_user` (`created_by`),
  KEY `idx_res_usage_date` (`usage_date`),
  KEY `idx_res_status` (`status`),
  CONSTRAINT `fk_res_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `fk_res_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES (1,'PRE20260618111343',2,'2026-06-18','2026-06-18','intera giornata',1,'cancellata',0.00,0.00,0.00,'contanti','',1,'2026-06-18 09:13:43','2026-06-18 16:24:31'),(2,'PRE2026061818084895',2,'2026-06-18','2026-06-18','intera giornata',1,'cancellata',0.00,0.00,0.00,NULL,'',1,'2026-06-18 16:08:48','2026-06-18 16:24:29'),(3,'PRE2026061818275927',2,'2026-06-18','2026-06-18','intera giornata',1,'cancellata',0.00,0.00,0.00,NULL,'',1,'2026-06-18 16:27:59','2026-06-18 16:45:01'),(4,'PRE2026061818280991',1,'2026-06-18','2026-06-18','intera giornata',1,'cancellata',0.00,0.00,0.00,NULL,'Periodo: 2026-06-18 → 2026-06-03',1,'2026-06-18 16:28:09','2026-06-18 16:28:23'),(5,'PRE2026061818281747',1,'2026-06-18','2026-06-18','intera giornata',1,'cancellata',0.00,0.00,0.00,NULL,'',1,'2026-06-18 16:28:17','2026-06-18 16:44:58'),(6,'PRE2026061818451143',2,'2026-06-18','2026-06-18','mattina',1,'cancellata',0.00,0.00,0.00,NULL,'',1,'2026-06-18 16:45:11','2026-06-18 16:45:22'),(7,'PRE2026061818453532',1,'2026-06-18','2026-06-18','intera giornata',1,'cancellata',0.00,0.00,0.00,NULL,'',1,'2026-06-18 16:45:35','2026-06-18 16:48:24'),(8,'PRE2026061818454693',1,'2026-06-18','2026-06-18','pomeriggio',1,'cancellata',0.00,0.00,0.00,NULL,'',1,'2026-06-18 16:45:46','2026-06-18 16:48:22'),(9,'PRE2026061818455614',1,'2026-06-18','2026-06-18','pomeriggio',1,'cancellata',0.00,0.00,0.00,NULL,'',1,'2026-06-18 16:45:56','2026-06-18 16:48:20'),(10,'PRE2026061818481368',1,'2026-06-18','2026-06-18','pomeriggio',1,'cancellata',0.00,0.00,0.00,NULL,'',1,'2026-06-18 16:48:13','2026-06-18 16:48:18'),(11,'PRE2026061819120229',2,'2026-06-18','2026-06-18','intera giornata',1,'confermata',0.00,0.00,0.00,NULL,'',1,'2026-06-18 17:12:02','2026-06-18 17:12:02'),(12,'PRE2026061820211654',2,'2026-06-18','2026-06-18','intera giornata',1,'confermata',0.00,0.00,0.00,NULL,'',1,'2026-06-18 18:21:16','2026-06-18 18:21:16'),(13,'PRE2026061820212750',1,'2026-06-18','2026-06-18','intera giornata',1,'in attesa',0.00,0.00,0.00,NULL,'Periodo: 2026-06-18 → 2026-06-24',1,'2026-06-18 18:21:27','2026-06-18 18:21:27'),(14,'PRE2026061820214411',1,'2026-06-18','2026-06-18','intera giornata',1,'in attesa',0.00,0.00,0.00,NULL,'',1,'2026-06-18 18:21:44','2026-06-18 18:21:44'),(15,'PRE2026061820220091',2,'2026-06-18','2026-06-18','mattina',1,'confermata',0.00,0.00,0.00,NULL,'',1,'2026-06-18 18:22:00','2026-06-18 18:22:00');
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','reception','bar','ristorazione','cassa') COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Amministratore','admin@igeaclub.it','$2y$10$F/ijMVAUjhRL9buTgRTag.1zZTu.Fx2t5vCRmATVCE/fZP/y9bBTy','admin',1,'2026-06-16 20:09:48','2026-06-16 20:09:48'),(2,'Reception','reception@igeaclub.it','$2y$10$F/ijMVAUjhRL9buTgRTag.1zZTu.Fx2t5vCRmATVCE/fZP/y9bBTy','reception',1,'2026-06-16 20:09:48','2026-06-16 20:09:48'),(3,'Bar','bar@igeaclub.it','$2y$10$F/ijMVAUjhRL9buTgRTag.1zZTu.Fx2t5vCRmATVCE/fZP/y9bBTy','bar',1,'2026-06-16 20:09:48','2026-06-16 20:09:48'),(4,'Cassa','cassa@igeaclub.it','$2y$10$F/ijMVAUjhRL9buTgRTag.1zZTu.Fx2t5vCRmATVCE/fZP/y9bBTy','cassa',1,'2026-06-16 20:09:48','2026-06-16 20:09:48');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-19  9:57:39
