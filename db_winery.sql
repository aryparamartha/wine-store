/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 5.5.5-10.1.30-MariaDB : Database - db_winery
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`db_winery` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `db_winery`;

/*Table structure for table `breakages` */

DROP TABLE IF EXISTS `breakages`;

CREATE TABLE `breakages` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `goods_id` int(10) DEFAULT NULL,
  `reason` text,
  `qty` int(20) DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `breakages_ibfk_1` FOREIGN KEY (`goods_id`) REFERENCES `goods` (`id`),
  CONSTRAINT `breakages_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `breakages` */

LOCK TABLES `breakages` WRITE;

UNLOCK TABLES;

/*Table structure for table `company_profile` */

DROP TABLE IF EXISTS `company_profile`;

CREATE TABLE `company_profile` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `address` text,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `website` varchar(50) DEFAULT NULL,
  `logo` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `company_profile` */

LOCK TABLES `company_profile` WRITE;

UNLOCK TABLES;

/*Table structure for table `company_profiles` */

DROP TABLE IF EXISTS `company_profiles`;

CREATE TABLE `company_profiles` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `address` text,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `website` varchar(50) DEFAULT NULL,
  `logo` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `company_profiles` */

LOCK TABLES `company_profiles` WRITE;

insert  into `company_profiles`(`id`,`name`,`address`,`phone`,`email`,`website`,`logo`) values (1,'Wine Project','Jl. Sunset Road No XX','(0361)-412-423','wineproj@email.com','wineproj.co.id','assets\\images\\logo\\wine-logo.png');

UNLOCK TABLES;

/*Table structure for table `compliment_txes` */

DROP TABLE IF EXISTS `compliment_txes`;

CREATE TABLE `compliment_txes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `invoice_id` varchar(20) DEFAULT NULL,
  `employee_id` int(10) DEFAULT NULL,
  `customer_id` int(10) DEFAULT NULL,
  `seller_id` int(10) DEFAULT NULL,
  `total` double DEFAULT NULL,
  `tax` double DEFAULT NULL,
  `grand_total` double DEFAULT NULL,
  `status` enum('paid','unpaid') DEFAULT NULL,
  `payment_date` timestamp NULL DEFAULT NULL,
  `payment_type` enum('cash','transfer') DEFAULT NULL,
  `transfer_proof` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_id` (`seller_id`),
  KEY `employee_id` (`employee_id`),
  KEY `client_id` (`customer_id`),
  CONSTRAINT `compliment_txes_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `compliment_txes_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `compliment_txes` */

LOCK TABLES `compliment_txes` WRITE;

insert  into `compliment_txes`(`id`,`invoice_id`,`employee_id`,`customer_id`,`seller_id`,`total`,`tax`,`grand_total`,`status`,`payment_date`,`payment_type`,`transfer_proof`,`created_at`,`updated_at`) values (1,'C21072002190001',1,1,NULL,45000,4500,49500,'unpaid',NULL,NULL,NULL,'2021-07-19 18:19:09','2021-07-19 18:19:09'),(2,'C21072002550002',1,1,NULL,47000,4700,51700,'unpaid',NULL,NULL,NULL,'2021-07-19 18:55:25','2021-07-19 18:55:25'),(3,'C21072002570003',1,1,NULL,49000,4900,53900,'unpaid',NULL,NULL,NULL,'2021-07-19 18:57:17','2021-07-19 18:57:17');

UNLOCK TABLES;

/*Table structure for table `customers` */

DROP TABLE IF EXISTS `customers`;

CREATE TABLE `customers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `number` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `pic` int(10) DEFAULT NULL,
  `type` enum('company','person') DEFAULT NULL,
  `note` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `PIC` (`pic`),
  CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`PIC`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `customers` */

LOCK TABLES `customers` WRITE;

insert  into `customers`(`id`,`name`,`address`,`number`,`email`,`pic`,`type`,`note`,`created_at`,`updated_at`,`deleted_at`) values (1,'Personal1','Denpasar','123214','default@example.com',1,'person','Trio Cust','2021-06-29 17:17:24','2021-06-29 17:17:24',NULL),(2,'Comp1','Denpasar','421412','comp1@example.com',2,'company','Comp by Trio','2021-07-17 18:11:57','2021-07-17 18:11:57',NULL);

UNLOCK TABLES;

/*Table structure for table `det_compl_txes` */

DROP TABLE IF EXISTS `det_compl_txes`;

CREATE TABLE `det_compl_txes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `compliment_tx_id` int(10) DEFAULT NULL,
  `goods_id` int(10) DEFAULT NULL,
  `qty` int(20) DEFAULT NULL,
  `unit_id` int(10) DEFAULT NULL,
  `disc` int(11) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `sub_total` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tcom_id` (`compliment_tx_id`),
  KEY `goods_id` (`goods_id`),
  KEY `unit_id` (`unit_id`),
  CONSTRAINT `det_compl_txes_ibfk_1` FOREIGN KEY (`compliment_tx_id`) REFERENCES `compliment_txes` (`id`),
  CONSTRAINT `det_compl_txes_ibfk_2` FOREIGN KEY (`goods_id`) REFERENCES `goods` (`id`),
  CONSTRAINT `det_compl_txes_ibfk_3` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

/*Data for the table `det_compl_txes` */

LOCK TABLES `det_compl_txes` WRITE;

insert  into `det_compl_txes`(`id`,`compliment_tx_id`,`goods_id`,`qty`,`unit_id`,`disc`,`price`,`sub_total`,`created_at`,`updated_at`) values (1,1,25,1,2,10,10000,9000,'2021-07-19 18:19:09','2021-07-19 18:19:09'),(2,1,26,2,1,10,20000,36000,'2021-07-19 18:19:09','2021-07-19 18:19:09'),(3,2,25,1,2,10,10000,9000,'2021-07-19 18:55:25','2021-07-19 18:55:25'),(4,2,26,2,1,5,20000,38000,'2021-07-19 18:55:25','2021-07-19 18:55:25'),(5,3,25,1,2,10,10000,9000,'2021-07-19 18:57:17','2021-07-19 18:57:17'),(6,3,26,2,1,0,20000,40000,'2021-07-19 18:57:17','2021-07-19 18:57:17');

UNLOCK TABLES;

/*Table structure for table `det_receivings` */

DROP TABLE IF EXISTS `det_receivings`;

CREATE TABLE `det_receivings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `receiving_id` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `sub_total` int(11) DEFAULT NULL,
  `tax` float DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`),
  KEY `receiving_id` (`receiving_id`),
  CONSTRAINT `det_receivings_ibfk_2` FOREIGN KEY (`goods_id`) REFERENCES `goods` (`id`),
  CONSTRAINT `det_receivings_ibfk_3` FOREIGN KEY (`receiving_id`) REFERENCES `receivings` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

/*Data for the table `det_receivings` */

LOCK TABLES `det_receivings` WRITE;

insert  into `det_receivings`(`id`,`receiving_id`,`goods_id`,`qty`,`unit_id`,`price`,`sub_total`,`tax`,`created_at`,`updated_at`) values (21,6,25,1,2,123,123,NULL,'2021-07-18 15:53:29','2021-07-18 15:53:29'),(22,6,26,3,1,123,369,NULL,'2021-07-18 15:53:29','2021-07-18 15:53:29');

UNLOCK TABLES;

/*Table structure for table `det_reg_txes` */

DROP TABLE IF EXISTS `det_reg_txes`;

CREATE TABLE `det_reg_txes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `regular_tx_id` int(10) DEFAULT NULL,
  `goods_id` int(10) DEFAULT NULL,
  `qty` int(20) DEFAULT NULL,
  `unit_id` int(10) DEFAULT NULL,
  `disc` float DEFAULT NULL,
  `price` double DEFAULT NULL,
  `sub_total` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `treg_id` (`regular_tx_id`),
  KEY `goods_id` (`goods_id`),
  KEY `unit_id` (`unit_id`),
  CONSTRAINT `det_reg_txes_ibfk_1` FOREIGN KEY (`regular_tx_id`) REFERENCES `regular_txes` (`id`),
  CONSTRAINT `det_reg_txes_ibfk_2` FOREIGN KEY (`goods_id`) REFERENCES `goods` (`id`),
  CONSTRAINT `det_reg_txes_ibfk_3` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

/*Data for the table `det_reg_txes` */

LOCK TABLES `det_reg_txes` WRITE;

insert  into `det_reg_txes`(`id`,`regular_tx_id`,`goods_id`,`qty`,`unit_id`,`disc`,`price`,`sub_total`,`created_at`,`updated_at`) values (5,16,25,1,2,NULL,15000,15000,'2021-07-18 07:38:50','2021-07-18 07:38:50'),(6,16,26,2,1,NULL,30000,60000,'2021-07-18 07:38:50','2021-07-18 07:38:50'),(7,18,25,10,2,NULL,15000,150000,'2021-07-18 07:57:21','2021-07-18 07:57:21'),(8,18,26,20,1,NULL,30000,600000,'2021-07-18 07:57:21','2021-07-18 07:57:21'),(9,19,25,1,2,NULL,15000,15000,'2021-07-19 16:24:04','2021-07-19 16:24:04'),(10,19,25,2,2,NULL,15000,30000,'2021-07-19 16:24:04','2021-07-19 16:24:04'),(11,20,25,1,2,10,10000,9000,'2021-07-19 18:45:38','2021-07-19 18:45:38'),(12,20,26,2,1,20,20000,32000,'2021-07-19 18:45:38','2021-07-19 18:45:38'),(15,21,25,1,2,10,10000,9000,'2021-07-19 18:51:51','2021-07-19 18:51:51'),(16,21,26,2,1,20,20000,32000,'2021-07-19 18:51:51','2021-07-19 18:51:51');

UNLOCK TABLES;

/*Table structure for table `employees` */

DROP TABLE IF EXISTS `employees`;

CREATE TABLE `employees` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `birthpl` varchar(50) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `employees` */

LOCK TABLES `employees` WRITE;

insert  into `employees`(`id`,`name`,`address`,`birthpl`,`birthdate`,`email`,`password`,`created_at`,`updated_at`,`deleted_at`) values (1,'Employee1','Bali',NULL,NULL,'default@example.com','$2y$12$cdDclnc23UQDLZQlQ9GzMuCqRgmv9V5S1ph3dTHtrxcfVxxAjSemW',NULL,NULL,NULL),(2,'Employee2','Denpasar','2021-06-30','2021-06-30','default2@example.com',NULL,'2021-06-29 17:45:46','2021-06-29 18:27:59',NULL);

UNLOCK TABLES;

/*Table structure for table `goods` */

DROP TABLE IF EXISTS `goods`;

CREATE TABLE `goods` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `unit_id` int(10) DEFAULT NULL,
  `amount` int(50) DEFAULT NULL,
  `purchase_price` int(11) DEFAULT NULL,
  `selling_price` int(11) DEFAULT NULL,
  `tax_price` double DEFAULT NULL,
  `added_by` int(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `added_by` (`added_by`),
  KEY `unit_id` (`unit_id`),
  CONSTRAINT `goods_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `employees` (`id`),
  CONSTRAINT `goods_ibfk_2` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

/*Data for the table `goods` */

LOCK TABLES `goods` WRITE;

insert  into `goods`(`id`,`code`,`name`,`unit_id`,`amount`,`purchase_price`,`selling_price`,`tax_price`,`added_by`,`created_at`,`updated_at`,`deleted_at`) values (25,'WP001','Wine Premium 1',2,89,10000,15000,13636.37,1,'2021-07-18 05:46:56','2021-07-19 18:45:38',NULL),(26,'WP002','Wine Premium 2',1,29,20000,30000,27272.73,1,'2021-07-18 07:19:45','2021-07-19 18:45:38',NULL);

UNLOCK TABLES;

/*Table structure for table `goods_logs` */

DROP TABLE IF EXISTS `goods_logs`;

CREATE TABLE `goods_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) DEFAULT NULL,
  `status` enum('IN','OUT') DEFAULT NULL,
  `date` timestamp NULL DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `post_amount` int(11) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `source` text,
  `note` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;

/*Data for the table `goods_logs` */

LOCK TABLES `goods_logs` WRITE;

insert  into `goods_logs`(`id`,`goods_id`,`status`,`date`,`qty`,`post_amount`,`price`,`source`,`note`,`created_at`,`updated_at`) values (2,25,'IN','2021-07-18 05:46:56',100,100,0,'First Register',NULL,'2021-07-18 05:46:56','2021-07-18 05:46:56'),(3,26,'IN','2021-07-18 07:19:45',50,50,0,'First Register',NULL,'2021-07-18 07:19:45','2021-07-18 07:19:45'),(6,25,'OUT','2021-07-18 07:38:50',1,99,15000,'Comp1',NULL,'2021-07-18 07:38:50','2021-07-18 07:38:50'),(7,26,'OUT','2021-07-18 07:38:50',2,48,60000,'Comp1',NULL,'2021-07-18 07:38:50','2021-07-18 07:38:50'),(8,25,'OUT','2021-07-18 07:57:21',10,89,150000,'Personal1',NULL,'2021-07-18 07:57:21','2021-07-18 07:57:21'),(9,26,'OUT','2021-07-18 07:57:21',20,28,600000,'Personal1',NULL,'2021-07-18 07:57:21','2021-07-18 07:57:21'),(24,25,'IN','2021-07-18 15:35:46',11,100,1,'Supplier1',NULL,'2021-07-18 15:35:46','2021-07-18 15:35:46'),(25,26,'IN','2021-07-18 15:35:46',12,40,1,'Supplier1',NULL,'2021-07-18 15:35:46','2021-07-18 15:35:46'),(26,25,'OUT','2021-07-18 15:39:07',11,89,1,'Supplier1',NULL,'2021-07-18 15:39:07','2021-07-18 15:39:07'),(27,26,'OUT','2021-07-18 15:39:07',12,28,1,'Supplier1',NULL,'2021-07-18 15:39:07','2021-07-18 15:39:07'),(28,25,'IN','2021-07-18 15:39:07',6,95,1,'Supplier1',NULL,'2021-07-18 15:39:07','2021-07-18 15:39:07'),(29,26,'IN','2021-07-18 15:39:07',7,35,1,'Supplier1',NULL,'2021-07-18 15:39:07','2021-07-18 15:39:07'),(30,25,'OUT','2021-07-18 15:43:35',6,89,1,'Supplier1','Delete receiving','2021-07-18 15:43:35','2021-07-18 15:43:35'),(31,26,'OUT','2021-07-18 15:43:35',7,28,1,'Supplier1','Delete receiving','2021-07-18 15:43:35','2021-07-18 15:43:35'),(32,25,'IN','2021-07-18 15:43:35',1,90,1,'Supplier1',NULL,'2021-07-18 15:43:35','2021-07-18 15:43:35'),(33,26,'IN','2021-07-18 15:43:35',2,30,1,'Supplier1',NULL,'2021-07-18 15:43:35','2021-07-18 15:43:35'),(36,25,'OUT','2021-07-18 15:45:27',1,89,1,'Supplier1','Delete receiving','2021-07-18 15:45:27','2021-07-18 15:45:27'),(37,26,'OUT','2021-07-18 15:45:27',2,28,1,'Supplier1','Delete receiving','2021-07-18 15:45:27','2021-07-18 15:45:27'),(38,25,'IN','2021-07-18 15:52:50',1,90,123,'Supplier1',NULL,'2021-07-18 15:52:50','2021-07-18 15:52:50'),(39,26,'IN','2021-07-18 15:52:50',2,30,246,'Supplier1',NULL,'2021-07-18 15:52:50','2021-07-18 15:52:50'),(40,25,'OUT','2021-07-18 15:53:29',1,89,123,'Supplier1','Delete receiving','2021-07-18 15:53:29','2021-07-18 15:53:29'),(41,26,'OUT','2021-07-18 15:53:29',2,28,246,'Supplier1','Delete receiving','2021-07-18 15:53:29','2021-07-18 15:53:29'),(42,25,'IN','2021-07-18 15:53:29',1,90,123,'Supplier1',NULL,'2021-07-18 15:53:29','2021-07-18 15:53:29'),(43,26,'IN','2021-07-18 15:53:29',3,31,369,'Supplier1',NULL,'2021-07-18 15:53:29','2021-07-18 15:53:29'),(44,25,'OUT','2021-07-19 18:45:38',1,89,9000,'Personal1',NULL,'2021-07-19 18:45:38','2021-07-19 18:45:38'),(45,26,'OUT','2021-07-19 18:45:38',2,29,32000,'Personal1',NULL,'2021-07-19 18:45:38','2021-07-19 18:45:38');

UNLOCK TABLES;

/*Table structure for table `receivings` */

DROP TABLE IF EXISTS `receivings`;

CREATE TABLE `receivings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` varchar(20) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `grand_total` int(11) DEFAULT NULL,
  `receiving_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `receivings_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  CONSTRAINT `receivings_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

/*Data for the table `receivings` */

LOCK TABLES `receivings` WRITE;

insert  into `receivings`(`id`,`invoice_id`,`employee_id`,`supplier_id`,`grand_total`,`receiving_date`,`created_at`,`updated_at`) values (6,'RCV21071201040001',1,1,492,'2021-07-18','2021-07-18 15:52:50','2021-07-18 15:53:29');

UNLOCK TABLES;

/*Table structure for table `regular_txes` */

DROP TABLE IF EXISTS `regular_txes`;

CREATE TABLE `regular_txes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `invoice_id` varchar(20) DEFAULT NULL,
  `employee_id` int(10) DEFAULT NULL,
  `customer_id` int(10) DEFAULT NULL,
  `seller_id` int(10) DEFAULT NULL,
  `total` double DEFAULT NULL,
  `tax` double DEFAULT NULL,
  `grand_total` double DEFAULT NULL,
  `status` enum('paid','unpaid') DEFAULT NULL,
  `payment_date` timestamp NULL DEFAULT NULL,
  `payment_type` enum('cash','transfer') DEFAULT NULL,
  `transfer_proof` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  KEY `sales_id` (`seller_id`),
  KEY `company_id` (`customer_id`),
  CONSTRAINT `regular_txes_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `regular_txes_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

/*Data for the table `regular_txes` */

LOCK TABLES `regular_txes` WRITE;

insert  into `regular_txes`(`id`,`invoice_id`,`employee_id`,`customer_id`,`seller_id`,`total`,`tax`,`grand_total`,`status`,`payment_date`,`payment_type`,`transfer_proof`,`created_at`,`updated_at`) values (16,'R21071815380001',1,2,NULL,75000,6818.18,75000,'paid','2021-07-18 07:38:50','cash',NULL,'2021-07-18 07:38:50','2021-07-18 07:38:50'),(18,'R21071815570002',1,1,NULL,750000,68181.82,750000,'paid','2021-07-18 07:57:21','cash',NULL,'2021-07-18 07:57:21','2021-07-18 07:57:21'),(19,'R21072000240001',1,1,1,45000,4090.91,45000,'unpaid',NULL,NULL,NULL,'2021-07-19 16:24:04','2021-07-19 16:24:04'),(20,'R21072002450002',1,1,NULL,41000,4100,45100,'paid','2021-07-19 18:45:38','cash',NULL,'2021-07-19 18:45:38','2021-07-19 18:45:38'),(21,'R21072002510003',1,1,NULL,41000,4100,45100,'unpaid',NULL,NULL,NULL,'2021-07-19 18:51:21','2021-07-19 18:51:51');

UNLOCK TABLES;

/*Table structure for table `sellers` */

DROP TABLE IF EXISTS `sellers`;

CREATE TABLE `sellers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `number` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `sellers` */

LOCK TABLES `sellers` WRITE;

insert  into `sellers`(`id`,`name`,`address`,`number`,`email`,`created_at`,`updated_at`,`deleted_at`) values (1,'Pandika','Anyelir','214214','pandikap@gmail.com','2021-06-30 17:05:58','2021-06-30 17:05:58',NULL);

UNLOCK TABLES;

/*Table structure for table `suppliers` */

DROP TABLE IF EXISTS `suppliers`;

CREATE TABLE `suppliers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `number` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `pic` int(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `suppliers` */

LOCK TABLES `suppliers` WRITE;

insert  into `suppliers`(`id`,`name`,`address`,`number`,`email`,`pic`,`created_at`,`updated_at`) values (1,'Supplier1','Jln. Nangka Utara 1','442135','supplier@default.com',1,'2021-07-04 15:40:40','2021-07-04 15:40:40');

UNLOCK TABLES;

/*Table structure for table `units` */

DROP TABLE IF EXISTS `units`;

CREATE TABLE `units` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `units` */

LOCK TABLES `units` WRITE;

insert  into `units`(`id`,`name`,`created_at`,`updated_at`,`deleted_at`) values (1,'Pcs','2021-07-02 01:00:00','2021-06-29 15:35:57',NULL),(2,'Bottle','2021-07-02 02:00:00','2021-06-29 15:35:52',NULL),(3,'test','2021-07-02 23:00:00','2021-07-03 04:52:09',NULL);

UNLOCK TABLES;

/* Trigger structure for table `breakages` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `after_insert_breakages` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `after_insert_breakages` AFTER INSERT ON `breakages` FOR EACH ROW BEGIN
	UPDATE goods SET amount = amount - NEW.qty WHERE id = NEW.goods_id;
    END */$$


DELIMITER ;

/* Trigger structure for table `breakages` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `after_update_breakages` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `after_update_breakages` AFTER UPDATE ON `breakages` FOR EACH ROW BEGIN
	UPDATE goods SET amount = amount + OLD.qty WHERE id = OLD.goods_id;
	UPDATE goods SET amount = amount - NEW.qty WHERE id = NEW.goods_id;
    END */$$


DELIMITER ;

/* Trigger structure for table `breakages` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `after_delete_breakages` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `after_delete_breakages` AFTER DELETE ON `breakages` FOR EACH ROW BEGIN
	UPDATE goods SET amount = amount + OLD.qty WHERE id = OLD.goods_id;
    END */$$


DELIMITER ;

/* Trigger structure for table `det_receivings` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `after_insert_det_receivings` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `after_insert_det_receivings` AFTER INSERT ON `det_receivings` FOR EACH ROW BEGIN
	UPDATE goods SET amount = amount + NEW.qty WHERE id = NEW.goods_id;
    END */$$


DELIMITER ;

/* Trigger structure for table `det_receivings` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `after_delete_det_receivings` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `after_delete_det_receivings` AFTER DELETE ON `det_receivings` FOR EACH ROW BEGIN
	UPDATE goods SET amount = amount - OLD.qty WHERE id = OLD.goods_id;
    END */$$


DELIMITER ;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
