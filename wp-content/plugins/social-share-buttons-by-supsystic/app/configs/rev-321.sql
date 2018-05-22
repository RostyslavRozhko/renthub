
INSERT INTO `%prefix%networks` (`id`, `name`, `url`, `class`, `brand_primary`, `brand_secondary`, `total_shares`) VALUES (18, 'WhatsApp', 'whatsapp://send?text={url}', 'whatsapp', '#43C353', '#ffffff', 0)
  ON DUPLICATE KEY UPDATE
    name = VALUES(name), url = VALUES(url), class = VALUES(class), brand_primary = VALUES(brand_primary), brand_secondary = VALUES(brand_secondary), total_shares = VALUES(total_shares);

INSERT INTO `%prefix%networks` (`id`, `name`, `url`, `class`, `brand_primary`, `brand_secondary`, `total_shares`) VALUES (19, 'Tumblr', 'http://www.tumblr.com/share/link?url={url}&name={title}&description={description}', 'tumblr', '#36465D', '#ffffff', 0)
  ON DUPLICATE KEY UPDATE
    name = VALUES(name), url = VALUES(url), class = VALUES(class), brand_primary = VALUES(brand_primary), brand_secondary = VALUES(brand_secondary), total_shares = VALUES(total_shares);

INSERT INTO `%prefix%networks` (`id`, `name`, `url`, `class`, `brand_primary`, `brand_secondary`, `total_shares`) VALUES (10, 'Delicious', 'https://del.icio.us/save?v=5&jump=close&url={url}&title={title}', 'delicious', '#3399ff', '#ffffff', 0)
ON DUPLICATE KEY UPDATE
    name = VALUES(name), url = VALUES(url), class = VALUES(class), brand_primary = VALUES(brand_primary), brand_secondary = VALUES(brand_secondary), total_shares = VALUES(total_shares);