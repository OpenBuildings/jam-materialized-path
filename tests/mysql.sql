DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NULL,
  `parent_id` int(11) UNSIGNED NULL DEFAULT 0,
  `path` varchar(255) NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `categories` (`id`, `name`, `parent_id`, `path`)
VALUES
  (1, 'Root', 0, null),
  (2, 'Category 1', 1, '1'),
  (3, 'Category 2', 1, '1'),
  (4, 'Sub Category 1', 2, '1/2'),
  (5, 'Sub Category 2', 2, '1/2'),
  (6, 'Sub Category 3', 3, '1/3'),
  (7, 'Leaf 1', 6, '1/3/6'),
  (8, 'Leaf 2', 6, '1/3/6');
