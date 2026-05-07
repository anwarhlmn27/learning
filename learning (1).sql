-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2026 at 10:44 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `learning`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-30222734557911fe58cdaa8896d0b2a5f1883169', 'i:1;', 1777365815),
('laravel-cache-30222734557911fe58cdaa8896d0b2a5f1883169:timer', 'i:1777365815;', 1777365815),
('laravel-cache-392bd6835064d676957ede00e8cd7e4f1bb3a598', 'i:1;', 1777347448),
('laravel-cache-392bd6835064d676957ede00e8cd7e4f1bb3a598:timer', 'i:1777347448;', 1777347448),
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba', 'i:1;', 1777365828),
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1777365828;', 1777365828);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clos`
--

CREATE TABLE `clos` (
  `id` char(36) NOT NULL,
  `id_subject` char(36) NOT NULL,
  `id_plo` char(36) DEFAULT NULL,
  `clo` varchar(255) NOT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clos`
--

INSERT INTO `clos` (`id`, `id_subject`, `id_plo`, `clo`, `deskripsi`, `created_at`, `updated_at`) VALUES
('019dce25-3cce-700b-8cb3-8541f1b6462d', '019dbe46-cafc-736d-a255-df0124ca92bb', '019dbe3d-d53d-73a9-8605-e24e07238016', 'CLO1', 'Test CLO', '2026-04-27 01:54:00', '2026-04-27 01:54:00');

-- --------------------------------------------------------

--
-- Table structure for table `course_mapings`
--

CREATE TABLE `course_mapings` (
  `id` char(36) NOT NULL,
  `id_prodi` char(36) NOT NULL,
  `id_subject` char(36) NOT NULL,
  `level_maping` varchar(1) NOT NULL,
  `id_plo` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_mapings`
--

INSERT INTO `course_mapings` (`id`, `id_prodi`, `id_subject`, `level_maping`, `id_plo`, `created_at`, `updated_at`) VALUES
('019dbebf-c211-7295-8338-5e2dcc8a53df', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe46-cafc-736d-a255-df0124ca92bb', 'I', '019dbe3d-d53d-73a9-8605-e24e07238016', '2026-04-24 02:08:51', '2026-04-24 02:08:51'),
('019dbec0-0544-72fa-912f-c9afc5a6acd4', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe46-cafc-736d-a255-df0124ca92bb', 'I', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 02:09:08', '2026-04-24 02:09:08'),
('019dbec0-400f-707e-b795-d39159abdab8', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe46-cafc-736d-a255-df0124ca92bb', 'I', '019dbe3f-36fc-7305-9052-8aca6f17ba74', '2026-04-24 02:09:23', '2026-04-24 02:09:23'),
('019dbec0-74fb-73da-b55a-858eeb32793a', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe46-cafc-736d-a255-df0124ca92bb', 'I', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:09:37', '2026-04-24 02:09:37'),
('019dbec1-084b-73c2-9c5c-df8aca8648e0', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe47-ca6c-723e-bbc7-2b5f9632f32b', 'R', '019dbe3d-d53d-73a9-8605-e24e07238016', '2026-04-24 02:10:14', '2026-04-24 02:10:14'),
('019dbec1-4748-7029-8165-72b061b04ab9', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe47-ca6c-723e-bbc7-2b5f9632f32b', 'I', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 02:10:30', '2026-04-24 02:10:30'),
('019dbec1-ecbf-725e-bb9e-0c1b7ae2f670', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe47-ca6c-723e-bbc7-2b5f9632f32b', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:11:13', '2026-04-24 02:11:13'),
('019dbec2-3d31-722e-bc50-cf68c7c4b63d', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe48-76e3-715a-a9c5-3cc5b1ef10a4', 'R', '019dbe3d-d53d-73a9-8605-e24e07238016', '2026-04-24 02:11:33', '2026-04-24 02:11:48'),
('019dbec2-bab3-7192-a422-3c0ad91f8c64', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe48-76e3-715a-a9c5-3cc5b1ef10a4', 'I', '019dbe3e-b7aa-719b-bcda-0abc1c673ae5', '2026-04-24 02:12:06', '2026-04-24 02:12:06'),
('019dbec3-0f4b-73f4-8962-71b7444dc524', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe48-76e3-715a-a9c5-3cc5b1ef10a4', 'I', '019dbe3f-36fc-7305-9052-8aca6f17ba74', '2026-04-24 02:12:27', '2026-04-24 02:12:27'),
('019dbec4-209e-72cd-b992-1649bb962cd8', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe4d-6d53-7241-85f3-9b185261c92b', 'R', '019dbe3d-d53d-73a9-8605-e24e07238016', '2026-04-24 02:13:37', '2026-04-24 02:13:37'),
('019dbec4-fe98-722b-91db-1b8eb9c4e1f8', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe4d-6d53-7241-85f3-9b185261c92b', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:14:34', '2026-04-24 02:14:34'),
('019dbec5-0005-73be-a68a-b11e711de620', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe4d-6d53-7241-85f3-9b185261c92b', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:14:34', '2026-04-24 02:14:34'),
('019dbec5-0156-7083-bbab-d56a1e07cbf3', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe4d-6d53-7241-85f3-9b185261c92b', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:14:35', '2026-04-24 02:14:35'),
('019dbec5-02ad-7001-8326-97eaa6717a4e', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe4d-6d53-7241-85f3-9b185261c92b', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:14:35', '2026-04-24 02:14:35'),
('019dbec5-040a-70f6-a036-3d0a74931047', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe4d-6d53-7241-85f3-9b185261c92b', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:14:35', '2026-04-24 02:14:35'),
('019dbec5-054b-735e-b22e-34ba52cc74ac', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe4d-6d53-7241-85f3-9b185261c92b', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:14:36', '2026-04-24 02:14:36'),
('019dbec5-069a-702a-89c2-382b6b24e97d', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe4d-6d53-7241-85f3-9b185261c92b', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:14:36', '2026-04-24 02:14:36'),
('019dbec5-07ee-71dd-9b0c-eb745f2c2376', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe4d-6d53-7241-85f3-9b185261c92b', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:14:36', '2026-04-24 02:14:36'),
('019dbec5-0935-710a-827f-c2b88da39042', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe4d-6d53-7241-85f3-9b185261c92b', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:14:37', '2026-04-24 02:14:37'),
('019dbec5-0a8b-73e9-9d08-9da842926f01', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe4d-6d53-7241-85f3-9b185261c92b', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:14:37', '2026-04-24 02:14:37'),
('019dbec5-0bda-73f2-996d-83c09bf42168', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe4d-6d53-7241-85f3-9b185261c92b', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:14:37', '2026-04-24 02:14:37'),
('019dbec5-e27b-727b-8471-573b5c07a693', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe4b-1bc0-701d-832e-449ae0868925', 'R', '019dbe3d-d53d-73a9-8605-e24e07238016', '2026-04-24 02:15:32', '2026-04-24 02:15:32'),
('019dbec6-13f9-70d9-b3bd-16fd91e6ff10', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe4b-1bc0-701d-832e-449ae0868925', 'R', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 02:15:45', '2026-04-24 02:15:45'),
('019dbec6-46c9-706f-ade7-c0c43e28c553', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe4b-1bc0-701d-832e-449ae0868925', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:15:58', '2026-04-24 02:15:58'),
('019dbec6-9fb2-7251-9047-15ec4624a724', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe4a-15fe-73a1-aeb6-9a601e8fc920', 'I', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 02:16:21', '2026-04-24 02:16:35'),
('019dbec7-47bf-7099-af76-23facbb02684', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe4a-7c5e-733d-8f3b-1b8cba284d0b', 'I', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 02:17:04', '2026-04-24 02:17:04'),
('019dbec8-4e3f-7094-b338-c4103922e212', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe49-1460-733c-9905-4e7f1b1cf125', 'I', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 02:18:11', '2026-04-24 02:18:11'),
('019dbec8-e651-73b7-b611-89c9ff433a29', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe4f-1209-72a5-bc2d-475b51a73f12', 'I', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 02:18:50', '2026-04-24 02:18:50'),
('019dbec9-1890-719a-8b1a-4577e416a7a9', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe4f-1209-72a5-bc2d-475b51a73f12', 'I', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:19:03', '2026-04-24 02:19:03'),
('019dbec9-539f-71c4-aefb-9ec072181fcd', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe4f-1209-72a5-bc2d-475b51a73f12', 'I', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 02:19:18', '2026-04-24 02:19:18'),
('019dbec9-ce96-7341-8138-b9f88b8820e9', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe50-8d2a-73a7-86cb-14e93152a199', 'R', '019dbe3d-d53d-73a9-8605-e24e07238016', '2026-04-24 02:19:49', '2026-04-24 02:19:49'),
('019dbeca-00a8-720b-9fa1-f3731247f5f8', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe50-8d2a-73a7-86cb-14e93152a199', 'R', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 02:20:02', '2026-04-24 02:20:02'),
('019dbeca-2ea6-7206-9cd2-b76df64cb909', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe50-8d2a-73a7-86cb-14e93152a199', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:20:14', '2026-04-24 02:20:14'),
('019dbecb-0003-73d2-8895-009ed841b0af', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe51-04bd-73a0-ab3e-b8d72b9e2cd4', 'I', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 02:21:08', '2026-04-24 02:21:08'),
('019dbecb-69b9-729d-9dd8-484e2e2b4d82', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe51-04bd-73a0-ab3e-b8d72b9e2cd4', 'I', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 02:21:35', '2026-04-24 02:21:35'),
('019dbecc-72d9-7241-b3af-708fb422222d', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe52-dbba-710a-ba86-9442fb2df1e3', 'R', '019dbe3d-d53d-73a9-8605-e24e07238016', '2026-04-24 02:22:43', '2026-04-24 02:22:43'),
('019dbecc-f59e-7070-ae28-6b7f2a300579', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe52-dbba-710a-ba86-9442fb2df1e3', 'I', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 02:23:16', '2026-04-24 02:23:16'),
('019dbecd-ad7c-7065-95eb-c7c0f5a7acad', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe52-dbba-710a-ba86-9442fb2df1e3', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:24:03', '2026-04-24 02:24:03'),
('019dbece-08a5-7202-bbe4-a341a7c036df', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe52-50e8-70e6-ad8a-26bcbaedf657', 'R', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 02:24:26', '2026-04-24 02:24:26'),
('019dbece-7651-71c0-ade3-98e14bf6286a', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe52-50e8-70e6-ad8a-26bcbaedf657', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:24:54', '2026-04-24 02:24:54'),
('019dbece-a9da-7308-a457-f125ad3fb4e4', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe52-50e8-70e6-ad8a-26bcbaedf657', 'I', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 02:25:08', '2026-04-24 02:25:08'),
('019dbecf-213d-7261-a977-dab6dc2fe5d4', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe51-6ace-73c3-bf94-47a134e12020', 'I', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 02:25:38', '2026-04-24 02:25:38'),
('019dbecf-e788-71d5-ae70-943ed2c9da53', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe51-bf4b-7056-a1ce-7f3b6d0215b3', 'R', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 02:26:29', '2026-04-24 02:26:29'),
('019dbed1-b39e-7030-b360-87dac7af6190', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe54-3145-7312-b88b-44062d057c7d', 'R', '019dbe3f-36fc-7305-9052-8aca6f17ba74', '2026-04-24 02:28:27', '2026-04-24 02:28:27'),
('019dbed2-0b19-71cd-a0ad-ac69af1dc2b6', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe54-3145-7312-b88b-44062d057c7d', 'I', '019dbe3f-a3df-70cf-8c75-945c72dd1964', '2026-04-24 02:28:49', '2026-04-24 02:28:49'),
('019dbed2-637a-735e-9459-a320cac8176d', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe54-a643-730a-aae6-28b64c4fd280', 'R', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 02:29:12', '2026-04-24 02:29:12'),
('019dbed2-fd94-7362-a049-6444952e375b', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe54-a643-730a-aae6-28b64c4fd280', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:29:51', '2026-04-24 02:29:51'),
('019dbed3-3cf5-73a3-9eb4-9e90c2781991', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe54-a643-730a-aae6-28b64c4fd280', 'R', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 02:30:07', '2026-04-24 02:30:07'),
('019dbed3-ff5e-7032-89ea-0ea0a150be7a', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe55-0f6b-71b8-8472-a5f10aae0e62', 'R', '019dbe3d-d53d-73a9-8605-e24e07238016', '2026-04-24 02:30:57', '2026-04-24 02:30:57'),
('019dbed4-318c-713f-b1b8-3a41d3daf8c5', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe55-0f6b-71b8-8472-a5f10aae0e62', 'R', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 02:31:10', '2026-04-24 02:31:10'),
('019dbed4-75ee-739f-9d1c-4bfae37bacec', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe55-0f6b-71b8-8472-a5f10aae0e62', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:31:28', '2026-04-24 02:31:28'),
('019dbed4-d1b2-715b-b29e-39226e610e0d', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe55-69a6-7393-8916-d9998ede883c', 'R', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 02:31:51', '2026-04-24 02:31:51'),
('019dbed6-728d-73b2-8482-63166c47d02b', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe58-c3c7-7225-8ec2-cce00fd687a6', 'I', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 02:33:38', '2026-04-24 02:33:38'),
('019dbed7-cda2-7196-81db-28751b371546', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe59-54cb-738e-868f-2bd9c4ac4803', 'R', '019dbe3f-36fc-7305-9052-8aca6f17ba74', '2026-04-24 02:35:07', '2026-04-24 02:35:07'),
('019dbed8-27d5-71b7-928c-381caa7ada1c', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe59-54cb-738e-868f-2bd9c4ac4803', 'R', '019dbe3f-a3df-70cf-8c75-945c72dd1964', '2026-04-24 02:35:30', '2026-04-24 02:35:30'),
('019dbed8-9eb0-730e-b5a3-581b23b6b877', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe5d-c794-73a1-a1c7-025a74a67d2f', 'R', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 02:36:00', '2026-04-24 02:36:00'),
('019dbed8-e3f0-72d1-a183-484e17045af5', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe5d-c794-73a1-a1c7-025a74a67d2f', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:36:18', '2026-04-24 02:36:18'),
('019dbed9-4e36-7167-ab2c-391fbe9750b6', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe5e-7b29-71ae-a2b3-69f3536f0f36', 'R', '019dbe3d-d53d-73a9-8605-e24e07238016', '2026-04-24 02:36:45', '2026-04-24 02:36:45'),
('019dbed9-87a6-72b6-a948-e0b7db771741', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe5e-7b29-71ae-a2b3-69f3536f0f36', 'R', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 02:37:00', '2026-04-24 02:37:00'),
('019dbed9-c0ca-73b4-8074-91ccdf794ca6', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe5e-7b29-71ae-a2b3-69f3536f0f36', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:37:14', '2026-04-24 02:37:14'),
('019dbeda-4c86-72aa-8663-b8f6b4e19450', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe5f-4504-701a-87ec-96eef5168e60', 'R', '019dbe3f-a3df-70cf-8c75-945c72dd1964', '2026-04-24 02:37:50', '2026-04-24 02:37:50'),
('019dbeda-aafb-729f-bea0-0d87d8d81aba', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe60-2c7e-717c-9c90-70d93c5c87bc', 'R', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 02:38:14', '2026-04-24 02:38:14'),
('019dbeda-f5a2-7330-9839-31a09888f6c3', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe60-2c7e-717c-9c90-70d93c5c87bc', 'R', '019dbe3e-b7aa-719b-bcda-0abc1c673ae5', '2026-04-24 02:38:34', '2026-04-24 02:38:34'),
('019dbedb-462c-7109-b97b-36474457226d', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe60-2c7e-717c-9c90-70d93c5c87bc', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:38:54', '2026-04-24 02:38:54'),
('019dbedb-a45c-7379-91c8-6bf5920b5fef', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe5f-c055-72a0-bb94-a97eaeeb66a6', 'R', '019dbe3d-d53d-73a9-8605-e24e07238016', '2026-04-24 02:39:18', '2026-04-24 02:39:18'),
('019dbedb-e232-7005-85b6-73eee9be7ab0', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe5f-c055-72a0-bb94-a97eaeeb66a6', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:39:34', '2026-04-24 02:39:34'),
('019dbedc-5059-704d-8c4f-48e195cd648f', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe61-46e7-715a-aa18-6717bdc19d3c', 'R', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 02:40:02', '2026-04-24 02:40:02'),
('019dbedc-9f99-70f0-8835-b1719bf2fd67', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe61-46e7-715a-aa18-6717bdc19d3c', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:40:23', '2026-04-24 02:40:23'),
('019dbedc-eb9c-73f2-8a1b-f802f4fe26c1', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe61-46e7-715a-aa18-6717bdc19d3c', 'I', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 02:40:42', '2026-04-24 02:40:42'),
('019dbedd-8679-7371-b869-864f0b9933af', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe5e-dc14-736d-a372-8378331d88f2', 'R', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 02:41:22', '2026-04-24 02:41:22'),
('019dbede-2814-71fd-9b13-252a9ceb3ae6', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe61-c7c4-7140-91c5-d986f2497336', 'R', '019dbe3e-b7aa-719b-bcda-0abc1c673ae5', '2026-04-24 02:42:03', '2026-04-24 02:42:03'),
('019dbede-6de6-7030-8d15-c4b7681f54ff', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe61-c7c4-7140-91c5-d986f2497336', 'R', '019dbe3f-36fc-7305-9052-8aca6f17ba74', '2026-04-24 02:42:21', '2026-04-24 02:42:21'),
('019dbede-aaea-73db-87d9-6dafc66294df', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe61-c7c4-7140-91c5-d986f2497336', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:42:37', '2026-04-24 02:42:37'),
('019dbedf-2216-73ef-8c76-7dfdff7b21e3', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe61-c7c4-7140-91c5-d986f2497336', 'R', '019dbe40-621b-7284-9c71-d2cfd1247e4f', '2026-04-24 02:43:07', '2026-04-24 02:43:07'),
('019dbedf-b88e-73aa-bdd7-338188f1f9d0', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe62-349c-71f2-a173-c81e7c0e2342', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:43:46', '2026-04-24 02:43:46'),
('019dbee0-061e-7308-830a-d2f8e44953a6', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe62-349c-71f2-a173-c81e7c0e2342', 'R', '019dbe40-621b-7284-9c71-d2cfd1247e4f', '2026-04-24 02:44:05', '2026-04-24 02:44:05'),
('019dbee0-4307-71eb-9be4-595db1ed1773', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe62-349c-71f2-a173-c81e7c0e2342', 'R', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 02:44:21', '2026-04-24 02:44:21'),
('019dbee1-3323-7190-b887-645553d6ded2', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe63-6134-71a8-b76c-154f4929629b', 'R', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 02:45:22', '2026-04-24 02:45:22'),
('019dbee1-8975-7069-8993-9bac15a6eb9e', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe63-6134-71a8-b76c-154f4929629b', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:45:45', '2026-04-24 02:45:45'),
('019dbee1-eb83-71d5-b9f3-308253f2580e', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe63-6134-71a8-b76c-154f4929629b', 'R', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 02:46:10', '2026-04-24 02:46:10'),
('019dbee2-a0a5-7041-b6e8-ecd4d616ed1a', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe63-bc81-71d4-898d-5a5539b93f42', 'R', '019dbe3f-36fc-7305-9052-8aca6f17ba74', '2026-04-24 02:46:56', '2026-04-24 02:46:56'),
('019dbee2-d4df-71ad-ba65-f67d4ad1037e', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe63-bc81-71d4-898d-5a5539b93f42', 'R', '019dbe3f-a3df-70cf-8c75-945c72dd1964', '2026-04-24 02:47:09', '2026-04-24 02:47:09'),
('019dbee3-165c-70bf-b08d-6ba42301f046', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe63-bc81-71d4-898d-5a5539b93f42', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:47:26', '2026-04-24 02:47:26'),
('019dbee3-78cf-7045-a12b-4e263334c2f2', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe63-bc81-71d4-898d-5a5539b93f42', 'R', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 02:47:51', '2026-04-24 02:47:51'),
('019dbee4-1ec5-726f-8092-65427038a8ee', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe6d-2a9b-7272-adcb-1452d344eaa8', 'R', '019dbe3f-a3df-70cf-8c75-945c72dd1964', '2026-04-24 02:48:34', '2026-04-24 02:48:34'),
('019dbee4-6c05-71d9-943d-d8989b062b88', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe6d-2a9b-7272-adcb-1452d344eaa8', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:48:54', '2026-04-24 02:48:54'),
('019dbee5-4da1-703b-a492-6369f77c9042', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe74-3346-71df-889c-c62b17b663d6', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:49:51', '2026-04-24 02:49:51'),
('019dbee5-8dbf-721b-bea7-c9bdae25c3de', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe74-3346-71df-889c-c62b17b663d6', 'R', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 02:50:08', '2026-04-24 02:50:08'),
('019dbee6-947f-72c2-9655-82bb1bd4e304', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe64-2873-726a-b705-ec5b214b3fd7', 'R', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 02:51:15', '2026-04-24 02:51:15'),
('019dbee7-4801-737c-b98f-485d0fced8d4', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe75-4d9a-7018-89b6-ab57bd193c2c', 'R', '019dbe3f-a3df-70cf-8c75-945c72dd1964', '2026-04-24 02:52:01', '2026-04-24 02:52:01'),
('019dbee7-ac0b-7152-83e6-7766095c67c5', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe75-4d9a-7018-89b6-ab57bd193c2c', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:52:27', '2026-04-24 02:52:27'),
('019dbee8-5904-72be-8b6f-1f5a11328619', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe80-7745-7335-b7e7-46eedb5cec1a', 'R', '019dbe3f-a3df-70cf-8c75-945c72dd1964', '2026-04-24 02:53:11', '2026-04-24 02:53:11'),
('019dbee8-9184-7086-a5e5-3cbc07a48265', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe80-7745-7335-b7e7-46eedb5cec1a', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:53:25', '2026-04-24 02:53:25'),
('019dbee8-ec0d-7131-8519-e39415031ba6', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe80-e53c-704d-acb6-a0a39b67d3a4', 'R', '019dbe3d-d53d-73a9-8605-e24e07238016', '2026-04-24 02:53:49', '2026-04-24 02:53:49'),
('019dbee9-3865-709f-be24-061a018bc025', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe80-e53c-704d-acb6-a0a39b67d3a4', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:54:08', '2026-04-24 02:54:08'),
('019dbeea-63b4-73a2-9faa-155a91d7ea23', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe81-4f8e-7045-a2b6-d990d89ee510', 'R', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 02:55:25', '2026-04-24 02:55:25'),
('019dbeea-9bf8-731a-9a0e-0b7709737e12', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe81-4f8e-7045-a2b6-d990d89ee510', 'R', '019dbe3e-b7aa-719b-bcda-0abc1c673ae5', '2026-04-24 02:55:39', '2026-04-24 02:55:39'),
('019dbeea-d4f5-704e-8fd8-879bd18c2bbe', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe81-4f8e-7045-a2b6-d990d89ee510', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:55:54', '2026-04-24 02:55:54'),
('019dbeeb-0ddd-70ca-b226-6de31d91a033', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe81-4f8e-7045-a2b6-d990d89ee510', 'R', '019dbe40-621b-7284-9c71-d2cfd1247e4f', '2026-04-24 02:56:08', '2026-04-24 02:56:08'),
('019dbeeb-6cb9-7127-a1b5-7807a9d514b6', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe81-b9b7-7048-af26-a7482da47a33', 'R', '019dbe3d-d53d-73a9-8605-e24e07238016', '2026-04-24 02:56:33', '2026-04-24 02:56:33'),
('019dbeeb-a630-7258-b659-d04cbc0e88ab', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe81-b9b7-7048-af26-a7482da47a33', 'R', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 02:56:47', '2026-04-24 02:56:47'),
('019dbeec-0209-7164-9432-4a75df739400', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe81-b9b7-7048-af26-a7482da47a33', 'R', '019dbe3e-b7aa-719b-bcda-0abc1c673ae5', '2026-04-24 02:57:11', '2026-04-24 02:57:11'),
('019dbeec-3695-70a4-8feb-6a71031341f8', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe81-b9b7-7048-af26-a7482da47a33', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:57:24', '2026-04-24 02:57:24'),
('019dbeec-c646-715b-8d84-5731b46b231e', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe82-1319-7053-87e1-728ab55ac0c3', 'R', '019dbe3d-d53d-73a9-8605-e24e07238016', '2026-04-24 02:58:01', '2026-04-24 02:58:01'),
('019dbeec-ff95-7113-82a7-4fea3ea28ee5', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe82-1319-7053-87e1-728ab55ac0c3', 'R', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 02:58:16', '2026-04-24 02:58:16'),
('019dbeed-3874-7229-85b9-3bb19aa861f3', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe82-1319-7053-87e1-728ab55ac0c3', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 02:58:30', '2026-04-24 02:58:30'),
('019dbeed-8c51-7348-938c-f0e968578605', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe7f-db85-7354-9c01-3cabddd553d9', 'R', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 02:58:52', '2026-04-24 02:58:52'),
('019dbeee-1d46-71d4-8034-dc21b6d98bbf', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe82-a994-710f-b534-2ca1546bebcc', 'M', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 02:59:29', '2026-04-24 02:59:29'),
('019dbeee-567e-722f-beb5-57c6527c42d2', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe82-a994-710f-b534-2ca1546bebcc', 'M', '019dbe3e-b7aa-719b-bcda-0abc1c673ae5', '2026-04-24 02:59:43', '2026-04-24 02:59:43'),
('019dbeee-96d9-733b-805e-bbbb3703a29a', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe82-a994-710f-b534-2ca1546bebcc', 'M', '019dbe3f-36fc-7305-9052-8aca6f17ba74', '2026-04-24 03:00:00', '2026-04-24 03:00:00'),
('019dbeee-cc15-71db-963c-b92a71e9802c', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe82-a994-710f-b534-2ca1546bebcc', 'M', '019dbe3f-a3df-70cf-8c75-945c72dd1964', '2026-04-24 03:00:14', '2026-04-24 03:00:14'),
('019dbeef-06fd-7143-a202-fa4bd0652bc5', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe82-a994-710f-b534-2ca1546bebcc', 'M', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 03:00:29', '2026-04-24 03:00:29'),
('019dbeef-4230-713a-93e6-f3b5edfd1880', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe82-a994-710f-b534-2ca1546bebcc', 'M', '019dbe40-621b-7284-9c71-d2cfd1247e4f', '2026-04-24 03:00:44', '2026-04-24 03:00:44'),
('019dbeef-78fa-72a0-82d9-c230753edace', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe82-a994-710f-b534-2ca1546bebcc', 'M', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 03:00:58', '2026-04-24 03:00:58'),
('019dbef0-28cf-7263-bab5-6395325c00d6', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe83-223f-70cf-8e92-736550a0cbf9', 'M', '019dbe3d-d53d-73a9-8605-e24e07238016', '2026-04-24 03:01:43', '2026-04-24 03:01:43'),
('019dbef0-5ab8-7173-9de8-aec96a6f163a', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe83-223f-70cf-8e92-736550a0cbf9', 'M', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 03:01:56', '2026-04-24 03:01:56'),
('019dbef0-9efa-7180-ad75-10cdeeccf20d', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe83-223f-70cf-8e92-736550a0cbf9', 'M', '019dbe3e-b7aa-719b-bcda-0abc1c673ae5', '2026-04-24 03:02:13', '2026-04-24 03:02:13'),
('019dbef0-cd2a-7000-b75b-d365d05ef8a6', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe83-223f-70cf-8e92-736550a0cbf9', 'M', '019dbe3f-36fc-7305-9052-8aca6f17ba74', '2026-04-24 03:02:25', '2026-04-24 03:02:25'),
('019dbef1-92b7-73af-884e-1846645bebb7', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe83-223f-70cf-8e92-736550a0cbf9', 'M', '019dbe3f-a3df-70cf-8c75-945c72dd1964', '2026-04-24 03:03:16', '2026-04-24 03:03:16'),
('019dbef1-dcea-728d-b7b8-5f34f94607de', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe83-223f-70cf-8e92-736550a0cbf9', 'M', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 03:03:35', '2026-04-24 03:03:35'),
('019dbef2-1a03-73c8-adb5-39d9dc4ef029', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe83-223f-70cf-8e92-736550a0cbf9', 'M', '019dbe40-621b-7284-9c71-d2cfd1247e4f', '2026-04-24 03:03:50', '2026-04-24 03:03:50'),
('019dbef2-6626-718b-8a14-4f9a3bcb3f06', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe83-223f-70cf-8e92-736550a0cbf9', 'M', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 03:04:10', '2026-04-24 03:04:10'),
('019dbef4-f7f0-70d0-b544-51b7233d501d', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe53-a879-731d-9dc8-92564301f206', 'R', '019dbe3d-d53d-73a9-8605-e24e07238016', '2026-04-24 03:06:58', '2026-04-24 03:06:58'),
('019dbef5-7084-726d-a2ca-d9ab0e2f4ff8', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe53-a879-731d-9dc8-92564301f206', 'R', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 03:07:29', '2026-04-24 03:07:29'),
('019dbef5-aa21-7375-8ca0-00e3e7b45b71', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe53-a879-731d-9dc8-92564301f206', 'I', '019dbe3e-b7aa-719b-bcda-0abc1c673ae5', '2026-04-24 03:07:44', '2026-04-24 03:07:44'),
('019dbef7-199e-72b2-92d4-679eb0e3f99c', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe53-a879-731d-9dc8-92564301f206', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 03:09:18', '2026-04-24 03:09:18'),
('019dbef7-b259-7016-bbbf-2a45c2d172fb', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe53-4313-7138-9292-3499dd3ecf9d', 'R', '019dbe3e-5402-7288-a1de-1dbb065192c1', '2026-04-24 03:09:57', '2026-04-24 03:09:57'),
('019dbef7-ee7b-70fe-83ae-565c2a41a7cb', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe53-4313-7138-9292-3499dd3ecf9d', 'R', '019dbe40-01c4-7003-b4b8-279b7d38747f', '2026-04-24 03:10:12', '2026-04-24 03:10:12'),
('019dbef8-2f3e-7000-9d5a-43c0e586e7e7', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe53-4313-7138-9292-3499dd3ecf9d', 'R', '019dbe41-4bab-7309-805e-934f6c1fe67d', '2026-04-24 03:10:29', '2026-04-24 03:10:29');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fakultas`
--

CREATE TABLE `fakultas` (
  `id` char(36) NOT NULL,
  `id_univs` char(36) NOT NULL,
  `kode_fakultas` varchar(255) NOT NULL,
  `nama_fakultas` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `nama_pimpinan` varchar(255) NOT NULL,
  `sign` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fakultas`
--

INSERT INTO `fakultas` (`id`, `id_univs`, `kode_fakultas`, `nama_fakultas`, `short_name`, `nama_pimpinan`, `sign`, `created_at`, `updated_at`) VALUES
('019db37a-cca9-7252-a0b4-345fd2001ff5', '019db379-079c-7017-9ddb-95ea4dc9cd85', 'F002', 'Faculty of Information and Computer Technology', 'FICT', 'Lila Setiyani', 'signatures/sig_fak_1776832662.png', '2026-04-21 21:37:42', '2026-04-21 21:37:42');

-- --------------------------------------------------------

--
-- Table structure for table `gps`
--

CREATE TABLE `gps` (
  `id` char(36) NOT NULL,
  `id_prodi` char(36) NOT NULL,
  `nm_profil` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `expertise` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gps`
--

INSERT INTO `gps` (`id`, `id_prodi`, `nm_profil`, `deskripsi`, `expertise`, `created_at`, `updated_at`) VALUES
('019dbe3a-1c2e-73b0-b3a2-5902747f8ca6', '019db37b-b4fe-730a-a029-1f52c236bf9a', 'GP_1', 'Cloud & DevOps Specialist', 'Cloud Developer\r\nDevOps Engineer', '2026-04-23 23:42:52', '2026-04-23 23:42:52'),
('019dbe3a-a26a-72c5-86e4-968b0afe5381', '019db37b-b4fe-730a-a029-1f52c236bf9a', 'GP_2', 'Network & Security Specialist', 'Network Administrator\r\nNetwork Security Professional\r\nNetwork Designer', '2026-04-23 23:43:26', '2026-04-23 23:43:26'),
('019dbe3a-f38d-7359-b2d4-3c372004e3fb', '019db37b-b4fe-730a-a029-1f52c236bf9a', 'GP_3', 'System & IT Support Specialist', 'System Administrator\r\nIT Support\r\nWeb Administrator', '2026-04-23 23:43:47', '2026-04-23 23:43:47');

-- --------------------------------------------------------

--
-- Table structure for table `gp_attachments`
--

CREATE TABLE `gp_attachments` (
  `id` char(36) NOT NULL,
  `id_prodi` char(36) NOT NULL,
  `nm_dokumen` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kurikulums`
--

CREATE TABLE `kurikulums` (
  `id` char(36) NOT NULL,
  `nm_kurikulum` varchar(255) NOT NULL,
  `id_prodi` char(36) NOT NULL,
  `tahun_akademik` int(11) NOT NULL,
  `berita_acara_fgd` varchar(255) DEFAULT NULL,
  `daftar_hadir` varchar(255) DEFAULT NULL,
  `notulensi_diskusi` varchar(255) DEFAULT NULL,
  `laporan_penyusunan` varchar(255) DEFAULT NULL,
  `laporan_sosialisasi` varchar(255) DEFAULT NULL,
  `dokumentasi` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kurikulums`
--

INSERT INTO `kurikulums` (`id`, `nm_kurikulum`, `id_prodi`, `tahun_akademik`, `berita_acara_fgd`, `daftar_hadir`, `notulensi_diskusi`, `laporan_penyusunan`, `laporan_sosialisasi`, `dokumentasi`, `created_at`, `updated_at`) VALUES
('019dba92-6b74-704b-afd6-162cc5c29634', 'Kurikulum TI 2025', '019db37b-b4fe-730a-a029-1f52c236bf9a', 2025, 'docs/kurikulum/KRcx4E44gq6bwZ2UhnpK1leGghoBzmkSDMBfw3gj.pdf', 'docs/kurikulum/c7j2MPusSMx5yCDtI5OwdwBJgkpoSll3QKmOZsdM.pdf', 'docs/kurikulum/2QqOIWVDUFr9jAh00q3ya2veGUvOJLBQ0uuIe0CC.pdf', 'docs/kurikulum/vqpOOEnJX2xw2gMIy7ytNXLwF6ngVyvJHS5eR9WP.pdf', 'docs/kurikulum/4EZYpNxca5uQ9hcDrDbQEk99uSwBcXsm8V88zZp3.pdf', 'docs/kurikulum/Ig5MpxbiCkqb0DvwB7RdwgzA4S4BSnBCud2xjbTM.pdf', '2026-04-23 06:40:51', '2026-04-23 23:54:22');

-- --------------------------------------------------------

--
-- Table structure for table `kurikulum_subjects`
--

CREATE TABLE `kurikulum_subjects` (
  `id` char(36) NOT NULL,
  `id_kurikulum` char(36) NOT NULL,
  `id_subject` char(36) NOT NULL,
  `semester` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kurikulum_subjects`
--

INSERT INTO `kurikulum_subjects` (`id`, `id_kurikulum`, `id_subject`, `semester`, `created_at`, `updated_at`) VALUES
('019dce7b-ff71-72da-bf06-24df587ab80d', '019dba92-6b74-704b-afd6-162cc5c29634', '019dbe46-cafc-736d-a255-df0124ca92bb', 1, '2026-04-27 03:28:46', '2026-04-27 03:28:46'),
('019dce7c-1716-737b-8b5f-a31c29542200', '019dba92-6b74-704b-afd6-162cc5c29634', '019dbe48-76e3-715a-a9c5-3cc5b1ef10a4', 1, '2026-04-27 03:28:52', '2026-04-27 03:28:52'),
('019dce7c-3834-7319-acef-319eac2548ba', '019dba92-6b74-704b-afd6-162cc5c29634', '019dbe47-ca6c-723e-bbc7-2b5f9632f32b', 1, '2026-04-27 03:29:00', '2026-04-27 03:29:00'),
('019dce7c-4fba-7083-ba67-eba2472928f5', '019dba92-6b74-704b-afd6-162cc5c29634', '019dbe49-1460-733c-9905-4e7f1b1cf125', 1, '2026-04-27 03:29:06', '2026-04-27 03:29:06'),
('019dce7c-715f-71cc-84e8-c687d810e2c2', '019dba92-6b74-704b-afd6-162cc5c29634', '019dbe4a-15fe-73a1-aeb6-9a601e8fc920', 1, '2026-04-27 03:29:15', '2026-04-27 03:29:15'),
('019dce7c-8754-711d-98df-66b669efa96f', '019dba92-6b74-704b-afd6-162cc5c29634', '019dbe4a-7c5e-733d-8f3b-1b8cba284d0b', 1, '2026-04-27 03:29:20', '2026-04-27 03:29:20'),
('019dce7c-b689-7092-83d8-ca18bdb40152', '019dba92-6b74-704b-afd6-162cc5c29634', '019dbe4b-1bc0-701d-832e-449ae0868925', 2, '2026-04-27 03:29:32', '2026-04-27 03:29:32'),
('019dce7d-3a23-720b-83e5-35930a093950', '019dba92-6b74-704b-afd6-162cc5c29634', '019dbe4d-6d53-7241-85f3-9b185261c92b', 2, '2026-04-27 03:30:06', '2026-04-27 03:30:06'),
('019dce7d-8eb1-7046-b107-b1d38db5ab64', '019dba92-6b74-704b-afd6-162cc5c29634', '019dbe4f-1209-72a5-bc2d-475b51a73f12', 2, '2026-04-27 03:30:28', '2026-04-27 03:30:28'),
('019dce7d-aba6-734b-88b5-02a22e9139fc', '019dba92-6b74-704b-afd6-162cc5c29634', '019dbe50-8d2a-73a7-86cb-14e93152a199', 2, '2026-04-27 03:30:35', '2026-04-27 03:30:35'),
('019dce7d-d70e-7372-93c4-31ce3c332589', '019dba92-6b74-704b-afd6-162cc5c29634', '019dbe51-04bd-73a0-ab3e-b8d72b9e2cd4', 2, '2026-04-27 03:30:46', '2026-04-27 03:30:46'),
('019dce7e-07b2-7339-bc5d-c4079d84799d', '019dba92-6b74-704b-afd6-162cc5c29634', '019dbe51-6ace-73c3-bf94-47a134e12020', 2, '2026-04-27 03:30:59', '2026-04-27 03:30:59'),
('019dce7e-3633-7211-adb8-2d2cd4f5abda', '019dba92-6b74-704b-afd6-162cc5c29634', '019dbe51-bf4b-7056-a1ce-7f3b6d0215b3', 2, '2026-04-27 03:31:11', '2026-04-27 03:31:11'),
('019dce7e-5f0c-72c7-9bab-d6a4e04e1e57', '019dba92-6b74-704b-afd6-162cc5c29634', '019dbe52-50e8-70e6-ad8a-26bcbaedf657', 2, '2026-04-27 03:31:21', '2026-04-27 03:31:21'),
('019dce7e-8c02-7028-9dac-48f514a751b7', '019dba92-6b74-704b-afd6-162cc5c29634', '019dbe52-dbba-710a-ba86-9442fb2df1e3', 2, '2026-04-27 03:31:33', '2026-04-27 03:31:33'),
('019dce7e-aeb5-715e-a111-0188861c3dc6', '019dba92-6b74-704b-afd6-162cc5c29634', '019dbe53-4313-7138-9292-3499dd3ecf9d', 3, '2026-04-27 03:31:42', '2026-04-27 03:31:42'),
('019dce7e-d4e2-7188-824c-fa57bd2ccddf', '019dba92-6b74-704b-afd6-162cc5c29634', '019dbe53-a879-731d-9dc8-92564301f206', 3, '2026-04-27 03:31:51', '2026-04-27 03:31:51'),
('019dce7e-fc1f-7185-b46b-08cb96be7b01', '019dba92-6b74-704b-afd6-162cc5c29634', '019dbe54-3145-7312-b88b-44062d057c7d', 3, '2026-04-27 03:32:01', '2026-04-27 03:32:01'),
('019dce7f-20ac-7174-ab41-15114444b78c', '019dba92-6b74-704b-afd6-162cc5c29634', '019dbe54-a643-730a-aae6-28b64c4fd280', 3, '2026-04-27 03:32:11', '2026-04-27 03:32:11');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_04_17_063553_create_univs_table', 1),
(5, '2026_04_17_064236_create_fakultas_table', 1),
(6, '2026_04_17_064524_create_prodis_table', 1),
(7, '2026_04_17_064839_create_visis_table', 1),
(8, '2026_04_17_065245_create_gps_table', 1),
(9, '2026_04_17_065556_create_plos_table', 1),
(10, '2026_04_17_070112_create_subjects_table', 1),
(11, '2026_04_17_070518_create_kurikulums_table', 1),
(12, '2026_04_17_070821_create_course_mapings_table', 1),
(13, '2026_04_17_084202_create_user_table', 1),
(14, '2026_04_17_084356_create_clos_table', 1),
(15, '2026_04_21_030417_unify_signature_columns_and_add_prodi_pimpinan', 1),
(16, '2026_04_21_042410_modify_visis_table_polymorphic', 1),
(17, '2026_04_21_045426_add_docs_to_visis_table', 1),
(18, '2026_04_21_082809_refactor_gp_system_tables', 1),
(19, '2026_04_21_101041_modify_plos_table_fields', 1),
(20, '2026_04_22_041400_create_kurikulum_subjects_table', 1),
(21, '2026_04_23_143200_add_id_plo_to_clos_table', 2),
(22, '2026_04_24_045033_change_level_maping_type_in_course_mapings_table', 3),
(23, '2026_04_24_074135_change_assesment_type_to_text_in_subjects_table', 4),
(24, '2026_04_28_021709_add_otp_to_password_reset_tokens_table', 5),
(25, '2026_04_28_023722_modify_user_table_for_rbac', 6),
(26, '2026_04_28_023724_create_rbac_tables', 6),
(27, '2026_04_28_033226_change_user_status_to_enum', 7);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `otp` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plos`
--

CREATE TABLE `plos` (
  `id` char(36) NOT NULL,
  `id_prodi` char(36) NOT NULL,
  `id_gp` char(36) NOT NULL,
  `title_plo` text NOT NULL,
  `plo` text NOT NULL,
  `detail` text DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plos`
--

INSERT INTO `plos` (`id`, `id_prodi`, `id_gp`, `title_plo`, `plo`, `detail`, `deskripsi`, `created_at`, `updated_at`) VALUES
('019dbe3d-d53d-73a9-8605-e24e07238016', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe3a-1c2e-73b0-b3a2-5902747f8ca6', 'PLO1', 'Computing Fundamentals', 'Apply fundamental knowledge of computing, including algorithms, programming, databases, operating systems, and networks.', NULL, '2026-04-23 23:46:56', '2026-04-23 23:46:56'),
('019dbe3e-5402-7288-a1de-1dbb065192c1', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe3a-1c2e-73b0-b3a2-5902747f8ca6', 'PLO2', 'Software & System Development', 'Design and develop software applications and IT systems using appropriate tools, methodologies, and programming languages.', NULL, '2026-04-23 23:47:29', '2026-04-23 23:47:29'),
('019dbe3e-b7aa-719b-bcda-0abc1c673ae5', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe3a-1c2e-73b0-b3a2-5902747f8ca6', 'PLO3', 'Cloud & DevOps Implementation', 'Design, deploy, and manage cloud-based systems and implement DevOps practices for automation, scalability, and reliability.', NULL, '2026-04-23 23:47:54', '2026-04-23 23:47:54'),
('019dbe3f-36fc-7305-9052-8aca6f17ba74', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe3a-a26a-72c5-86e4-968b0afe5381', 'PLO4', 'Network & Infrastructure', 'Design, implement, and manage computer networks and IT infrastructure based on organizational requirements.', NULL, '2026-04-23 23:48:27', '2026-04-23 23:48:27'),
('019dbe3f-a3df-70cf-8c75-945c72dd1964', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe3a-a26a-72c5-86e4-968b0afe5381', 'PLO5', 'Cybersecurity', 'Apply cybersecurity principles to protect systems, networks, and data against threats and vulnerabilities.', NULL, '2026-04-23 23:48:55', '2026-04-23 23:48:55'),
('019dbe40-01c4-7003-b4b8-279b7d38747f', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe3a-a26a-72c5-86e4-968b0afe5381', 'PLO6', 'Problem Solving', 'Analyze complex problems and propose effective IT-based solutions using analytical and critical thinking skills.', NULL, '2026-04-23 23:49:19', '2026-04-23 23:49:19'),
('019dbe40-621b-7284-9c71-d2cfd1247e4f', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe3a-f38d-7359-b2d4-3c372004e3fb', 'PLO7', 'IT System Management', 'Manage and maintain IT systems, including system administration, troubleshooting, and technical support.', NULL, '2026-04-23 23:49:43', '2026-04-23 23:49:43'),
('019dbe41-4bab-7309-805e-934f6c1fe67d', '019db37b-b4fe-730a-a029-1f52c236bf9a', '019dbe3a-f38d-7359-b2d4-3c372004e3fb', 'PLO8', 'Communication & Teamwork', 'Communicate effectively and work collaboratively in multidisciplinary teams in professional environments.', NULL, '2026-04-23 23:50:43', '2026-04-23 23:50:43');

-- --------------------------------------------------------

--
-- Table structure for table `prodis`
--

CREATE TABLE `prodis` (
  `id` char(36) NOT NULL,
  `id_fakultas` char(36) NOT NULL,
  `kode_prodi` int(11) NOT NULL,
  `nama_prodi` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `nama_pimpinan` varchar(255) DEFAULT NULL,
  `sign` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prodis`
--

INSERT INTO `prodis` (`id`, `id_fakultas`, `kode_prodi`, `nama_prodi`, `short_name`, `nama_pimpinan`, `sign`, `created_at`, `updated_at`) VALUES
('019db37b-b4fe-730a-a029-1f52c236bf9a', '019db37a-cca9-7252-a0b4-345fd2001ff5', 59201, 'Teknologi Informasi', 'TI', 'Head Program', 'signatures/sig_prodi_1776832722.png', '2026-04-21 21:38:42', '2026-04-21 21:38:42');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
('019dd1f5-0e37-736e-bd93-f5a544f28e4d', 'admin', '2026-04-27 19:39:51', '2026-04-27 19:39:51'),
('019dd1f5-0e47-73dd-bb30-cf25fa0eee38', 'rektor', '2026-04-27 19:39:51', '2026-04-27 19:39:51'),
('019dd1f5-0e4a-7009-97e9-d3dcd5d046d0', 'dekan', '2026-04-27 19:39:51', '2026-04-27 19:39:51'),
('019dd1f5-0e4d-71f6-a1c4-8ce4252042de', 'kaprodi', '2026-04-27 19:39:51', '2026-04-27 19:39:51'),
('019dd1f5-0e4e-723d-84c5-35a02b9a505d', 'dosen', '2026-04-27 19:39:51', '2026-04-27 19:39:51'),
('019dd1f5-0e50-71ff-915a-4cb900ec153c', 'baak', '2026-04-27 19:39:51', '2026-04-27 19:39:51'),
('019dd1f5-0e52-71f1-b198-cd688ee73443', 'finance', '2026-04-27 19:39:51', '2026-04-27 19:39:51'),
('019dd1f5-0e54-7318-85ad-a9c3e1ae4881', 'kemahasiswaan', '2026-04-27 19:39:51', '2026-04-27 19:39:51');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` char(36) NOT NULL,
  `role_id` char(36) NOT NULL,
  `permission_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('VZ16BuHjvrdVAFeB09v8zjEtWQy9PBUd0whlDYv6', '019dd1f5-0f14-71f0-9f39-b1a5ba7b39ff', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQVZvcTRDWkJZZ1RZMzlUZzJHTTdlZmc5YmRNUTZsUm5COGFDMVVQcyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9ncC8wMTlkYjM3Yi1iNGZlLTczMGEtYTAyOS0xZjUyYzIzNmJmOWEvbWFuYWdlIjtzOjU6InJvdXRlIjtzOjk6ImdwLm1hbmFnZSI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtzOjM2OiIwMTlkZDFmNS0wZjE0LTcxZjAtOWYzOS1iMWE1YmE3YjM5ZmYiO30=', 1777365855);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` char(36) NOT NULL,
  `kode_subject` varchar(255) NOT NULL,
  `nama_subject` varchar(255) NOT NULL,
  `sks_t` int(11) NOT NULL,
  `sks_p` int(11) NOT NULL,
  `total_sks` int(11) NOT NULL,
  `prerequisite_id` char(36) DEFAULT NULL,
  `semester` int(11) NOT NULL,
  `assesment_type` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `kode_subject`, `nama_subject`, `sks_t`, `sks_p`, `total_sks`, `prerequisite_id`, `semester`, `assesment_type`, `created_at`, `updated_at`) VALUES
('019dbe46-cafc-736d-a255-df0124ca92bb', 'ICT060', 'Introduction to Computing', 2, 0, 2, NULL, 1, '[\"Quiz\"]', '2026-04-23 23:56:43', '2026-04-24 00:48:07'),
('019dbe47-ca6c-723e-bbc7-2b5f9632f32b', 'ICT061', 'Data Structures and Algorithms', 2, 1, 3, NULL, 1, '[\"Coding\",\"Problem Solving\"]', '2026-04-23 23:57:49', '2026-04-24 01:12:47'),
('019dbe48-76e3-715a-a9c5-3cc5b1ef10a4', 'ICT062', 'Platform Technologies', 2, 0, 2, NULL, 1, '[\"SQL Lab\"]', '2026-04-23 23:58:33', '2026-04-24 00:49:09'),
('019dbe49-1460-733c-9905-4e7f1b1cf125', 'GEN022', 'GWEP 1', 3, 0, 3, NULL, 1, '[\"Presentation\"]', '2026-04-23 23:59:13', '2026-04-24 00:49:26'),
('019dbe4a-15fe-73a1-aeb6-9a601e8fc920', 'GEN002', 'Indonesian Language', 2, 0, 2, NULL, 1, '[\"Writing\"]', '2026-04-24 00:00:19', '2026-04-24 00:49:41'),
('019dbe4a-7c5e-733d-8f3b-1b8cba284d0b', 'GEN003', 'Pancasila', 2, 0, 2, NULL, 1, '[\"Essay\"]', '2026-04-24 00:00:45', '2026-04-24 00:49:51'),
('019dbe4b-1bc0-701d-832e-449ae0868925', 'ICT003', 'Object-Oriented Programming (Java)', 2, 1, 3, NULL, 1, '[\"Project\",\"Coding\"]', '2026-04-24 00:01:26', '2026-04-24 00:50:11'),
('019dbe4d-6d53-7241-85f3-9b185261c92b', 'MAT002', 'Discrete Mathematics', 2, 0, 2, NULL, 1, '[\"Problem Solving\"]', '2026-04-24 00:03:58', '2026-04-24 00:50:29'),
('019dbe4f-1209-72a5-bc2d-475b51a73f12', 'ICT004', 'UI/UX Design 1', 2, 1, 3, NULL, 2, '[\"Project\",\"Prototype\"]', '2026-04-24 00:05:46', '2026-04-24 01:14:55'),
('019dbe50-8d2a-73a7-86cb-14e93152a199', 'ICT063', 'Computer Programming 1', 2, 1, 3, NULL, 2, '[\"Coding\"]', '2026-04-24 00:07:23', '2026-04-24 01:15:07'),
('019dbe51-04bd-73a0-ab3e-b8d72b9e2cd4', 'ICT007', 'Graphics Design and Multimedia', 2, 1, 3, NULL, 2, '[\"Design Project\"]', '2026-04-24 00:07:53', '2026-04-24 01:15:20'),
('019dbe51-6ace-73c3-bf94-47a134e12020', 'GEN004', 'Citizenship', 2, 0, 2, NULL, 2, '[\"Essay\"]', '2026-04-24 00:08:20', '2026-04-24 01:15:33'),
('019dbe51-bf4b-7056-a1ce-7f3b6d0215b3', 'GEN023', 'GWEP 2', 3, 0, 3, NULL, 2, '[\"Presentation\"]', '2026-04-24 00:08:41', '2026-04-24 01:15:46'),
('019dbe52-50e8-70e6-ad8a-26bcbaedf657', 'ICT006', 'Information Systems Analysis and Design 1', 2, 0, 2, NULL, 2, '[\"Case Study\"]', '2026-04-24 00:09:18', '2026-04-24 01:15:59'),
('019dbe52-dbba-710a-ba86-9442fb2df1e3', 'ICT009', 'Database Systems 1', 2, 1, 3, NULL, 2, '[\"SQL Lab\"]', '2026-04-24 00:09:54', '2026-04-24 01:16:08'),
('019dbe53-4313-7138-9292-3499dd3ecf9d', 'ICT022', 'Project Management', 3, 0, 3, NULL, 3, '[\"Project\"]', '2026-04-24 00:10:20', '2026-04-24 01:16:30'),
('019dbe53-a879-731d-9dc8-92564301f206', 'ICT064', 'Intelligent Systems', 2, 1, 3, NULL, 3, '[\"Project\"]', '2026-04-24 00:10:46', '2026-04-24 01:16:41'),
('019dbe54-3145-7312-b88b-44062d057c7d', 'ICT014', 'Computer Networking 1', 2, 1, 3, NULL, 3, '[\"SQL Lab\"]', '2026-04-24 00:11:21', '2026-04-24 01:16:57'),
('019dbe54-a643-730a-aae6-28b64c4fd280', 'ICT012', 'UI/UX Design 2', 2, 1, 3, NULL, 3, '[\"Prototype\"]', '2026-04-24 00:11:51', '2026-04-24 01:17:09'),
('019dbe55-0f6b-71b8-8472-a5f10aae0e62', 'ICT065', 'Computer Programming 2', 2, 1, 3, NULL, 3, '[\"Coding\"]', '2026-04-24 00:12:18', '2026-04-24 01:17:20'),
('019dbe55-69a6-7393-8916-d9998ede883c', 'GEN024', 'GWEP 3', 2, 0, 2, NULL, 3, '[\"Presentation\"]', '2026-04-24 00:12:41', '2026-04-24 01:17:33'),
('019dbe58-65f5-70c7-ac86-3e7d52c3a76a', 'GEN001', 'Religion', 2, 0, 2, NULL, 3, '[\"Reflection\"]', '2026-04-24 00:15:57', '2026-04-24 01:17:42'),
('019dbe58-c3c7-7225-8ec2-cce00fd687a6', 'ICT081', 'Technology Professional Ethics', 2, 0, 2, NULL, 3, '[\"Case Study\"]', '2026-04-24 00:16:21', '2026-04-24 01:17:55'),
('019dbe59-54cb-738e-868f-2bd9c4ac4803', 'ICT032', 'Computer Networking 2', 2, 1, 3, NULL, 4, '[\"SQL Lab\"]', '2026-04-24 00:16:58', '2026-04-24 01:18:23'),
('019dbe5d-c794-73a1-a1c7-025a74a67d2f', 'ICT066', 'Web Systems and Technologies', 2, 1, 3, NULL, 4, '[\"Project\"]', '2026-04-24 00:21:50', '2026-04-24 01:18:37'),
('019dbe5e-7b29-71ae-a2b3-69f3536f0f36', 'ICT023', 'Database Systems 2', 2, 1, 3, NULL, 4, '[\"Project\"]', '2026-04-24 00:22:36', '2026-04-24 01:18:48'),
('019dbe5e-dc14-736d-a372-8378331d88f2', 'GEN025', 'GWEP 4', 2, 0, 2, NULL, 4, '[\"Presentation\"]', '2026-04-24 00:23:01', '2026-04-24 01:19:00'),
('019dbe5f-4504-701a-87ec-96eef5168e60', 'ICT067', 'Information Assurance and Security 1', 2, 0, 2, NULL, 4, '[\"Quiz\"]', '2026-04-24 00:23:27', '2026-04-24 01:19:11'),
('019dbe5f-c055-72a0-bb94-a97eaeeb66a6', 'MAT003', 'Statistics and Probability', 3, 0, 3, NULL, 4, '[\"Analisys\"]', '2026-04-24 00:23:59', '2026-04-24 01:19:21'),
('019dbe60-2c7e-717c-9c90-70d93c5c87bc', 'ICT069', 'Cloud Programming 1', 2, 1, 3, NULL, 4, '[\"SQL Lab\"]', '2026-04-24 00:24:27', '2026-04-24 01:19:34'),
('019dbe61-46e7-715a-aa18-6717bdc19d3c', 'ICT082', 'Business Analysis for IT', 2, 0, 2, NULL, 4, '[\"Case Study\"]', '2026-04-24 00:25:39', '2026-04-24 01:19:44'),
('019dbe61-c7c4-7140-91c5-d986f2497336', 'ICT070', 'Systems Administration and Maintenance', 2, 1, 3, NULL, 5, '[\"Project\"]', '2026-04-24 00:26:12', '2026-04-24 01:14:26'),
('019dbe62-349c-71f2-a173-c81e7c0e2342', 'ICT071', 'Managing IT Resources', 3, 0, 3, NULL, 5, '[\"Case Study\"]', '2026-04-24 00:26:40', '2026-04-24 01:20:07'),
('019dbe63-6134-71a8-b76c-154f4929629b', 'ICT072', 'IT Business Solutions', 3, 0, 3, NULL, 5, '[\"Project\"]', '2026-04-24 00:27:57', '2026-04-24 01:20:16'),
('019dbe63-bc81-71d4-898d-5a5539b93f42', 'ICT 033', 'Computer Networking Project', 2, 1, 3, NULL, 5, '[\"Project\"]', '2026-04-24 00:28:20', '2026-04-24 01:20:26'),
('019dbe64-2873-726a-b705-ec5b214b3fd7', 'GEN026', 'GWEP 5', 3, 0, 3, NULL, 5, '[\"Presentation\"]', '2026-04-24 00:28:48', '2026-04-24 01:20:38'),
('019dbe6d-2a9b-7272-adcb-1452d344eaa8', 'ICT073', 'Information Assurance and Security 2', 2, 0, 2, NULL, 5, '[\"Case Study\",\"SQL Lab\"]', '2026-04-24 00:38:38', '2026-04-24 01:20:48'),
('019dbe74-3346-71df-889c-c62b17b663d6', 'ICT035', 'Technopreneur: Idea Generation', 2, 0, 2, NULL, 5, '[\"Business Pitch\"]', '2026-04-24 00:46:19', '2026-04-24 01:21:03'),
('019dbe75-4d9a-7018-89b6-ab57bd193c2c', 'ICT074', 'Ethical Hacking', 2, 1, 3, NULL, 6, '[\"SQL Lab\"]', '2026-04-24 00:47:31', '2026-04-24 01:21:24'),
('019dbe7f-db85-7354-9c01-3cabddd553d9', 'GEN027', 'GWEP 6', 3, 0, 3, NULL, 6, '[\"Presentation\"]', '2026-04-24 00:59:03', '2026-04-24 01:21:33'),
('019dbe80-7745-7335-b7e7-46eedb5cec1a', 'ICT075', 'Computer Forensics', 3, 0, 3, NULL, 6, '[\"Investigation Report\"]', '2026-04-24 00:59:43', '2026-04-24 01:21:42'),
('019dbe80-e53c-704d-acb6-a0a39b67d3a4', 'ICT030', 'Research Methodology', 2, 0, 2, NULL, 6, '[\"Proposal\"]', '2026-04-24 01:00:11', '2026-04-24 01:21:54'),
('019dbe81-4f8e-7045-a2b6-d990d89ee510', 'ICT076', 'Cloud Programming 2', 2, 1, 3, NULL, 6, '[\"Project\"]', '2026-04-24 01:00:38', '2026-04-24 01:22:08'),
('019dbe81-b9b7-7048-af26-a7482da47a33', 'ICT077', 'Application Development & Emerging Technologies', 2, 1, 3, NULL, 6, '[\"Project\"]', '2026-04-24 01:01:05', '2026-04-24 01:22:17'),
('019dbe82-1319-7053-87e1-728ab55ac0c3', 'ICT078', 'Advanced Programming', 2, 0, 2, NULL, 6, '[\"Coding\"]', '2026-04-24 01:01:28', '2026-04-24 01:22:27'),
('019dbe82-a994-710f-b534-2ca1546bebcc', 'ICT079', 'IT Internship', 0, 20, 20, NULL, 7, '[\"Performance\",\"Report\"]', '2026-04-24 01:02:07', '2026-04-24 01:22:37'),
('019dbe83-223f-70cf-8e92-736550a0cbf9', 'ICT080', 'Capstone Project', 0, 6, 6, NULL, 8, '[\"Project\",\"Presentation\"]', '2026-04-24 01:02:38', '2026-04-24 01:22:45');

-- --------------------------------------------------------

--
-- Table structure for table `univs`
--

CREATE TABLE `univs` (
  `id` char(36) NOT NULL,
  `kode_univ` varchar(255) NOT NULL,
  `nama_univ` varchar(255) NOT NULL,
  `nama_pimpinan` varchar(255) NOT NULL,
  `sign` varchar(255) DEFAULT NULL,
  `address` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `univs`
--

INSERT INTO `univs` (`id`, `kode_univ`, `nama_univ`, `nama_pimpinan`, `sign`, `address`, `email`, `website`, `created_at`, `updated_at`) VALUES
('019db379-079c-7017-9ddb-95ea4dc9cd85', '041127', 'Universitas Horizon Indonesia', 'Bpk Rektor', 'signatures/sig_1776832580.png', 'Jl. Pangkal Perjuangan By Pass No.KM.1, Tanjungpura, Kec. Karawang Bar., Karawang, Jawa Barat', 'admin@gmail.com', 'https://horizon.ac.id', '2026-04-21 21:35:46', '2026-04-21 21:36:44');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` char(36) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','Inactive') DEFAULT 'active',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
('019dd1f5-0f14-71f0-9f39-b1a5ba7b39ff', 'Admin System', 'admin@gmail.com', '$2y$12$QSNelRQZXpkG9Wcu1rBUsev4AQ8PMT1B.DpyXMTinn9HgSvGp6zPS', 'active', 'V5kJlsPThzqwvs8fJnkHOcgsSXdTJgltpNDHEVpFIzNTqNrp2L6k0IMh2Kot', '2026-04-27 19:39:51', '2026-04-27 19:49:12'),
('019dd1ff-50e0-735e-be2c-e8e9ff8d651b', 'Anwar', 'anwarhlmn27@gmail.com', '$2y$12$6bGbJnkWpIN72f/xlY32cuzI0KsmZtxQ/J0/8DhqDc6tjTOfm3kAC', 'active', 'eTNf76WfnUSzlpPpYPwAWKnWfzXFSivfW9XBjmrID4Wj1my6ZnFBBSRIhitG', '2026-04-27 19:51:03', '2026-04-27 20:37:09'),
('019dd237-bcd3-73b0-a5be-8cc90e5963d5', 'Deden', 'deden@example.com', '$2y$12$z/7mTM1Wv6YLk/WJT8IqhetPN5eRe3lkSH3ueZA0PYzcZbv7pl1xC', 'active', NULL, '2026-04-27 20:52:41', '2026-04-27 20:52:41'),
('019dd237-bfa7-7363-a875-a9416b56e408', 'Oman', 'oman@example.com', '$2y$12$NJ4y1jpQWOWbDVODjIxdoejieSusxlNHeK6J7z8KgqrUA72qPB4vW', 'active', 'j6I7SDPd4kNyDqMnhuhEdBtrybCHjxnJyHHDYWCetVfZJBlFYraRfwRsUmUH', '2026-04-27 20:52:42', '2026-04-27 20:52:42');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `role_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
('15427346-dfba-40bc-81a4-f9ee861f04d3', '019dd237-bfa7-7363-a875-a9416b56e408', '019dd1f5-0e4e-723d-84c5-35a02b9a505d', NULL, NULL),
('c9d1dd41-4661-4fbe-9bd5-71340b09ed6c', '019dd1ff-50e0-735e-be2c-e8e9ff8d651b', '019dd1f5-0e37-736e-bd93-f5a544f28e4d', NULL, NULL),
('d8c08e82-f97d-4806-8fe1-5e45b341f0cf', '019dd1f5-0f14-71f0-9f39-b1a5ba7b39ff', '019dd1f5-0e37-736e-bd93-f5a544f28e4d', NULL, NULL),
('e1d785c5-23ba-45e6-a466-25f53080b78e', '019dd237-bcd3-73b0-a5be-8cc90e5963d5', '019dd1f5-0e4e-723d-84c5-35a02b9a505d', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `visis`
--

CREATE TABLE `visis` (
  `id` char(36) NOT NULL,
  `visi` text NOT NULL,
  `misi` text NOT NULL,
  `tujuan1` text DEFAULT NULL,
  `tujuan2` text DEFAULT NULL,
  `tujuan3` text DEFAULT NULL,
  `tujuan4` text DEFAULT NULL,
  `tujuan5` text DEFAULT NULL,
  `strategi1` text DEFAULT NULL,
  `strategi2` text DEFAULT NULL,
  `strategi3` text DEFAULT NULL,
  `strategi4` text DEFAULT NULL,
  `strategi5` text DEFAULT NULL,
  `doc_penyusunan` varchar(255) DEFAULT NULL,
  `doc_pengesahan` varchar(255) DEFAULT NULL,
  `doc_sosialisasi` varchar(255) DEFAULT NULL,
  `doc_hasil_survey` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `visible_id` char(36) NOT NULL,
  `visible_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `visis`
--

INSERT INTO `visis` (`id`, `visi`, `misi`, `tujuan1`, `tujuan2`, `tujuan3`, `tujuan4`, `tujuan5`, `strategi1`, `strategi2`, `strategi3`, `strategi4`, `strategi5`, `doc_penyusunan`, `doc_pengesahan`, `doc_sosialisasi`, `doc_hasil_survey`, `created_at`, `updated_at`, `visible_id`, `visible_type`) VALUES
('019db37d-9352-7023-84ba-97ab62fc6495', 'Unggul', 'Unggul Pendidikan, Penelitian, PkM', 'tujuan 1', 'tujuan 2', 'tujuan 3', 'tujuan 4', NULL, 'strategi 1', 'strategi 2', 'strategi 3', 'strategi 4', NULL, 'docs/visi/zo12KFcHI6NWNJ9rb4bQgI1EUC8LetnprpMKl92I.pdf', 'docs/visi/2Ocsd6BA1oFtKBg6HEpzaK7HLoSXsEn109lTcvMD.pdf', 'docs/visi/0TlKpSGLMTdDKFcsLf0zhZaJ0DYoidZBZVLu8Ofu.pdf', 'docs/visi/SyoXmz1qQtfYiPkLBPYaPTKJmYa5YGjSYe5TpEBx.pdf', '2026-04-21 21:40:44', '2026-04-21 21:40:44', '019db37b-b4fe-730a-a029-1f52c236bf9a', 'App\\Models\\Prodi'),
('019dbe38-6e74-7151-bda3-b5b228ef6534', 'Menjadi Fakultas Teknologi Informasi dan Komputer yang unggul dalam mencetak lulusan berdaya saing global, berkarakter inovatif, dan kompeten sebagai Software Developer Profesional yang diakui secara Internasional.', '1. Menyelenggarakan pendidikan tinggi yang unggul dan adaptif di bidang teknologi informasi dan komputer untuk mencetak lulusan yang profesional, kompeten, dan siap bersaing secara global sebagai Software Developer; \r\n2. Mengembangkan penelitian inovatif dan aplikatif di bidang rekayasa perangkat lunak yang berkontribusi pada kemajuan ilmu pengetahuan serta pemecahan masalah di dunia nyata; \r\n3. Melaksanakan pengabdian kepada masyarakat melalui pemanfaatan teknologi informasi yang solutif, relevan, dan berkelanjutan untuk mendukung transformasi digital; \r\n4. Membangun kemitraan strategis dengan industri, pemerintah, dan lembaga internasional untuk memperluas jejaring akademik, meningkatkan mutu tridharma, dan memperkuat pengakuan global fakultas; \r\n5. Meningkatkan kualitas dosen, mahasiswa, dan tenaga kependidikan melalui sertifikasi, pelatihan, dan partisipasi aktif dalam kegiatan akademik serta profesional berskala nasional maupun internasional', 'Menghasilkan lulusan yang kompeten, berintegritas, serta memiliki jiwa kepemimpinan dan kewirausahaan yang mampu bersaing secara global  di era digital melalui pendidikan berbasis teknologi mutakhir.', 'Meningkatkan inovasi dan kreativitas mahasiswa dengan mendorong pembelajaran berbasis proyek, riset terapan, dan pengembangan solusi digital yang relevan dengan kebutuhan industri dan masyarakat', 'Mengembangkan penelitian unggulan di bidang teknologi informasi dan komputer untuk berkontribusi pada kemajuan ilmu pengetahuan, penguatan industri, dan peningkatan kesejahteraan masyarakat.', 'Memberikan kontribusi nyata kepada masyarakat melalui penerapan keilmuan di bidang teknologi informasi dan komputer dalam bentuk kegiatan pengabdian yang berdampak pada kualitas layanan publik dan kehidupan sosial.', '2. Memperluas jejaring kerjasama strategis dengan mitra nasional dan internasional guna membuka akses yang lebih luas bagi mahasiswa dalam pengembangan karir, riset kolaboratif, dan inovasi global.', 'Relevansi CPL dengan kebutuhan industri digital. ● Lulusan tersertifikasi & terserap kerja ≤ 6 bulan. ● Peningkatan kualitas pembelajaran berbasis teknologi.', 'Mahasiswa terlibat dalam proyek multidisiplin & MBKM. ● Kualitas TA meningkat (riset terapan, produk digital). ● Peningkatan partisipasi mahasiswa dalam publikasi dan kompetisi.', 'Dosen aktif riset & publikasi terindeks. ● HKI, paten, prototipe, dan teknologi tepat guna. ● Kolaborasi riset dengan industri & mitra internasional.', 'PkM berbasis riset dosen & mahasiswa. ● Luaran PkM berupa publikasi, produk digital, atau HKI sosial. ● Peningkatan kemitraan dengan UMKM, sekolah, komunitas.', 'Jumlah & kualitas MoU meningkat. ● Joint class, teaching by expert, dan program mobilitas mahasiswa. Hibah & pendanaan kolaboratif.', NULL, NULL, NULL, NULL, '2026-04-23 23:41:02', '2026-04-23 23:41:02', '019db37a-cca9-7252-a0b4-345fd2001ff5', 'App\\Models\\Fakultas');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `clos`
--
ALTER TABLE `clos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clos_id_plo_foreign` (`id_plo`),
  ADD KEY `clos_id_subject_foreign` (`id_subject`);

--
-- Indexes for table `course_mapings`
--
ALTER TABLE `course_mapings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_mapings_id_prodi_foreign` (`id_prodi`),
  ADD KEY `course_mapings_id_subject_foreign` (`id_subject`),
  ADD KEY `course_mapings_id_plo_foreign` (`id_plo`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fakultas`
--
ALTER TABLE `fakultas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fakultas_id_univs_foreign` (`id_univs`);

--
-- Indexes for table `gps`
--
ALTER TABLE `gps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gps_id_prodi_foreign` (`id_prodi`);

--
-- Indexes for table `gp_attachments`
--
ALTER TABLE `gp_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gp_attachments_id_prodi_foreign` (`id_prodi`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kurikulums`
--
ALTER TABLE `kurikulums`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kurikulums_id_prodi_foreign` (`id_prodi`);

--
-- Indexes for table `kurikulum_subjects`
--
ALTER TABLE `kurikulum_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kurikulum_subjects_id_kurikulum_foreign` (`id_kurikulum`),
  ADD KEY `kurikulum_subjects_id_subject_foreign` (`id_subject`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plos`
--
ALTER TABLE `plos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plos_id_prodi_foreign` (`id_prodi`),
  ADD KEY `plos_id_gp_foreign` (`id_gp`);

--
-- Indexes for table `prodis`
--
ALTER TABLE `prodis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prodis_id_fakultas_foreign` (`id_fakultas`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_permissions_role_id_foreign` (`role_id`),
  ADD KEY `role_permissions_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subjects_kode_subject_unique` (`kode_subject`),
  ADD KEY `subjects_prerequisite_id_foreign` (`prerequisite_id`);

--
-- Indexes for table `univs`
--
ALTER TABLE `univs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_email_unique` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_roles_user_id_foreign` (`user_id`),
  ADD KEY `user_roles_role_id_foreign` (`role_id`);

--
-- Indexes for table `visis`
--
ALTER TABLE `visis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visis_visible_id_visible_type_index` (`visible_id`,`visible_type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `clos`
--
ALTER TABLE `clos`
  ADD CONSTRAINT `clos_id_plo_foreign` FOREIGN KEY (`id_plo`) REFERENCES `plos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `clos_id_subject_foreign` FOREIGN KEY (`id_subject`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_mapings`
--
ALTER TABLE `course_mapings`
  ADD CONSTRAINT `course_mapings_id_plo_foreign` FOREIGN KEY (`id_plo`) REFERENCES `plos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_mapings_id_prodi_foreign` FOREIGN KEY (`id_prodi`) REFERENCES `prodis` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_mapings_id_subject_foreign` FOREIGN KEY (`id_subject`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fakultas`
--
ALTER TABLE `fakultas`
  ADD CONSTRAINT `fakultas_id_univs_foreign` FOREIGN KEY (`id_univs`) REFERENCES `univs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gps`
--
ALTER TABLE `gps`
  ADD CONSTRAINT `gps_id_prodi_foreign` FOREIGN KEY (`id_prodi`) REFERENCES `prodis` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gp_attachments`
--
ALTER TABLE `gp_attachments`
  ADD CONSTRAINT `gp_attachments_id_prodi_foreign` FOREIGN KEY (`id_prodi`) REFERENCES `prodis` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kurikulums`
--
ALTER TABLE `kurikulums`
  ADD CONSTRAINT `kurikulums_id_prodi_foreign` FOREIGN KEY (`id_prodi`) REFERENCES `prodis` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kurikulum_subjects`
--
ALTER TABLE `kurikulum_subjects`
  ADD CONSTRAINT `kurikulum_subjects_id_kurikulum_foreign` FOREIGN KEY (`id_kurikulum`) REFERENCES `kurikulums` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kurikulum_subjects_id_subject_foreign` FOREIGN KEY (`id_subject`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `plos`
--
ALTER TABLE `plos`
  ADD CONSTRAINT `plos_id_gp_foreign` FOREIGN KEY (`id_gp`) REFERENCES `gps` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `plos_id_prodi_foreign` FOREIGN KEY (`id_prodi`) REFERENCES `prodis` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `prodis`
--
ALTER TABLE `prodis`
  ADD CONSTRAINT `prodis_id_fakultas_foreign` FOREIGN KEY (`id_fakultas`) REFERENCES `fakultas` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_prerequisite_id_foreign` FOREIGN KEY (`prerequisite_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
