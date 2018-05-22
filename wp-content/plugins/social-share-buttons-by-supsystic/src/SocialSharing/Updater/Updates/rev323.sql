CREATE TABLE `%prefix%shares_object` (
  `share_id` INT NOT NULL,
  `code` VARCHAR(10) NOT NULL,
  `item_id` INT NOT NULL,
  PRIMARY KEY (`share_id`),
  KEY `idx_search1` (`code`,`item_id`)
);
