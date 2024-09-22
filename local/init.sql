CREATE DATABASE `wrmd` DEFAULT CHARACTER SET = `utf8mb4` DEFAULT COLLATE = `utf8mb4_unicode_ci`;
CREATE DATABASE `wildalert` DEFAULT CHARACTER SET = `utf8mb4` DEFAULT COLLATE = `utf8mb4_unicode_ci`;

CREATE TABLE `wildalert`.`taxa` (
    `id` int AUTO_INCREMENT,
    `species` varchar(50),
    `genus` varchar(50),
    `family` varchar(50),
    `order` varchar(50),
    `class` varchar(50),
    `alpha_code` varchar(10),
    `is_mbta` tinyint(1) DEFAULT '0',
    `iucn_id` bigint(20),
    `inaturalist_taxon_id` bigint(20),
    `bow_code` varchar(10),
    `created_at` timestamp,
    `updated_at` timestamp,
    PRIMARY KEY (id)
);
