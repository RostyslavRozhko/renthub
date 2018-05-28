DELETE FROM `%prefix%project_networks` WHERE network_id IN (SELECT id FROM `wp_supsystic_ss_networks` WHERE name = 'Twitter Follow');
DELETE FROM `%prefix%networks` WHERE name = 'Twitter Follow'