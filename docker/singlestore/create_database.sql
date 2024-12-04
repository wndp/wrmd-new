CREATE DATABASE `wrmd` DEFAULT CHARACTER SET = `utf8mb4` DEFAULT COLLATE = `utf8mb4_unicode_ci`;
CREATE DATABASE `testing` DEFAULT CHARACTER SET = `utf8mb4` DEFAULT COLLATE = `utf8mb4_unicode_ci`;
CREATE DATABASE `wildalert` DEFAULT CHARACTER SET = `utf8mb4` DEFAULT COLLATE = `utf8mb4_unicode_ci`;

CREATE TABLE IF NOT EXISTS `wildalert`.`taxa` (
    `id` int AUTO_INCREMENT,
    `species` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `genus` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `family` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `order` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `class` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `alpha_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `is_mbta` tinyint(1) DEFAULT '0',
    `iucn_id` bigint(20) DEFAULT NULL,
    `inaturalist_taxon_id` bigint(20) DEFAULT NULL,
    `bow_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    SHARD KEY `__SHARDKEY` (`id`),
    SORT KEY `__UNORDERED` ()
);

CREATE TABLE IF NOT EXISTS `wildalert`.`common_names` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `taxon_id` bigint(20) unsigned NOT NULL,
  `language` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `common_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subspecies` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alpha_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_names_taxon_id_index` (`taxon_id`) USING HASH,
  SHARD KEY `__SHARDKEY` (`id`),
  SORT KEY `__UNORDERED` ()
);

CREATE TABLE IF NOT EXISTS `wildalert`.`taxon_metas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `taxon_id` bigint(20) unsigned NOT NULL,
  `key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `taxon_metas_taxon_id_index` (`taxon_id`) USING HASH,
  SHARD KEY `__SHARDKEY` (`id`),
  SORT KEY `__UNORDERED` ()
);

CREATE TABLE IF NOT EXISTS `wildalert`.`classifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) unsigned NOT NULL,
  `category` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prediction` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `accuracy` decimal(10,9) NOT NULL,
  `is_suspected` tinyint(1) NOT NULL DEFAULT '0',
  `is_validated` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `PRIMARY` (`id`) USING HASH,
  KEY `classifications_case_id_index` (`case_id`) USING HASH,
  SHARD KEY `__SHARDKEY` (`id`),
  SORT KEY `__UNORDERED` ()
);

CREATE TABLE `conservation_statuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `taxon_id` bigint(20) unsigned NOT NULL,
  `authority` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `territory` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conservation_statuses_taxon_id_index` (`taxon_id`) USING HASH,
  SHARD KEY `__SHARDKEY` (`id`),
  SORT KEY `__UNORDERED` ()
);

INSERT INTO `taxa` (`id`, `species`, `genus`, `family`, `order`, `class`, `alpha_code`, `is_mbta`, `iucn_id`, `inaturalist_taxon_id`, `bow_code`, `created_at`, `updated_at`) VALUES
(1150, 'homochroa', 'Oceanodroma', 'Hydrobatidae', 'Procellariiformes', 'Aves', 'ASSP', 1, 22698562, 1289618, NULL, '2024-05-04 14:12:39', '2024-05-04 14:12:39'),
(1271, 'jamaicensis', 'Buteo', 'Accipitridae', 'Accipitriformes', 'Aves', 'RTHA', 1, 22695933, 5212, NULL, '2024-05-04 14:12:39', '2024-05-04 14:12:39'),
(2946, 'mexicanus', 'Haemorhous', 'Fringillidae', 'Passeriformes', 'Aves', 'HOFI', 1, 22720563, 199840, NULL, '2024-05-04 14:12:39', '2024-05-04 14:12:39'),
(1043745, NULL, 'Parulidae', 'Passeri', 'Passeriformes', 'Aves', NULL, 0, NULL, NULL, NULL, '2024-05-04 14:12:39', '2024-05-04 14:12:39'),
(1033364, 'Boylii', 'Rana', 'Ranidae', 'Anura', 'Amphibia', NULL, 0, 19175, 25646, NULL, '2024-05-04 14:12:39', '2024-05-04 14:12:39');

INSERT INTO `common_names` (`id`, `taxon_id`, `language`, `common_name`, `subspecies`, `alpha_code`, `created_at`, `updated_at`) VALUES
(2251799813712004, 1271, 'en', 'Red-tailed Hawk', '', '', '2024-07-19 23:09:50', '2024-07-19 23:09:50'),
(2251799813712652, 1150, 'en', 'Ashy Storm-petrel', '', '', '2024-07-19 23:09:50', '2024-07-19 23:09:50'),
(2251799813712653, 1150, 'en', 'Ashy Storm Petrel', '', '', '2024-07-19 23:09:50', '2024-07-19 23:09:50'),
(2251799813718990, 2946, 'en', 'House Finch', '', '', '2024-07-19 23:09:50', '2024-07-19 23:09:50'),
(2251799813722063, 1271, 'en', 'Harlan\'s Hawk', '', '', '2024-07-19 23:09:50', '2024-07-19 23:09:50'),
(2251799813722155, 1043745, 'en', 'Warbler sp', '', '', '2024-07-19 23:09:50', '2024-07-19 23:09:50'),
(2251799813722157, 1043745, 'en', 'Unidentified Warbler', '', '', '2024-07-19 23:09:50', '2024-07-19 23:09:50'),
(2251799813689847, 1033364, 'es', 'Rana Pata Amarilla', '', '', '2024-07-19 23:09:50', '2024-07-19 23:09:50'),
(2251799813697279, 1033364, 'en', 'Yellow-legged Frog', '', '', '2024-07-19 23:09:50', '2024-07-19 23:09:50'),
(2251799813697280, 1033364, 'en', 'Foothill Yellow-legged Frog', '', '', '2024-07-19 23:09:50', '2024-07-19 23:09:50');

INSERT INTO `taxon_metas` (`id`, `taxon_id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1125899906843687, 2946, 'biological_group', 'Songbird', '2024-07-19 23:49:41', '2024-07-19 23:49:41'),
(1125899906843688, 2946, 'diet', 'Granivore', '2024-07-19 23:49:41', '2024-07-19 23:49:41'),
(1125899906843689, 2946, 'diet', 'Herbivore', '2024-07-19 23:49:41', '2024-07-19 23:49:41'),
(1125899906843690, 2946, 'phenology', 'Diurnal', '2024-07-19 23:49:41', '2024-07-19 23:49:41'),
(1125899906846337, 1043745, 'biological_group', 'Songbird', '2024-07-19 23:49:41', '2024-07-19 23:49:41'),
(1125899906846338, 1043745, 'phenology', 'Diurnal', '2024-07-19 23:49:41', '2024-07-19 23:49:41'),
(1125899906847995, 1150, 'biological_group', 'Seabird', '2024-07-19 23:49:41', '2024-07-19 23:49:41'),
(1125899906847996, 1150, 'diet', 'Insectivore', '2024-07-19 23:49:41', '2024-07-19 23:49:41'),
(1125899906847997, 1150, 'diet', 'Molluscivore', '2024-07-19 23:49:41', '2024-07-19 23:49:41'),
(1125899906847998, 1150, 'diet', 'Piscivore', '2024-07-19 23:49:41', '2024-07-19 23:49:41'),
(1125899906847999, 1150, 'phenology', 'Diurnal', '2024-07-19 23:49:41', '2024-07-19 23:49:41'),
(1125899906848000, 1150, 'phenology', 'Nocturnal', '2024-07-19 23:49:41', '2024-07-19 23:49:41'),
(1125899906850888, 1271, 'biological_group', 'Raptor', '2024-07-19 23:49:41', '2024-07-19 23:49:41'),
(1125899906850889, 1271, 'diet', 'Carnivore', '2024-07-19 23:49:41', '2024-07-19 23:49:41'),
(1125899906850890, 1271, 'phenology', 'Diurnal', '2024-07-19 23:49:41', '2024-07-19 23:49:41');

INSERT INTO `conservation_statuses` (`id`, `taxon_id`, `authority`, `territory`, `status`, `created_at`, `updated_at`) VALUES
(1125899906842625, 1271, 'IUCN', '*', 'LC', '2024-10-23 17:28:20', '2024-10-23 17:28:20'),
(1125899906842626, 1271, 'USFWS', 'US', 'LC', '2024-10-23 17:28:31', '2024-10-23 17:28:31'),
(1125899906842627, 1150, 'IUCN', '*', 'EN', '2024-10-23 17:28:31', '2024-10-23 17:28:31'),
(1125899906842628, 2946, 'IUCN', '*', 'LC', '2024-10-23 17:28:20', '2024-10-23 17:28:20'),
(1125899906842629, 1033364, 'IUCN', '*', 'NE', '2024-10-23 17:28:20', '2024-10-23 17:28:20'),
(1125899906842630, 1033364, 'IUCN', '*', 'NT', '2024-10-23 17:28:20', '2024-10-23 17:28:20'),
(1125899906842631, 1033364, 'USFWS', 'US-8', 'T', '2024-10-23 17:28:20', '2024-10-23 17:28:20'),
