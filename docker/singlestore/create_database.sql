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
