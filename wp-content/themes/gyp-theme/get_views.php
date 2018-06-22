<?php
require_once( explode('wp-content' , __FILE__ )[0] . 'wp-load.php');
	$start_time = microtime(true);
        function post_links() {
                $args = array(
                        'numberposts' => -1,
                        'orderby' => 'DESC',
                        'post_type'   => 'arenda',
                        'suppress_filters' => true
                );
                $get_all_links = get_posts($args);
                foreach ($get_all_links as $get_link){
			$key_field = 'post_views_count';
			$postID = $get_link->ID;
			$count = get_post_meta($postID, $key_field , true);
			$count = (int)($count + 1);
			update_post_meta($postID, $key_field , $count);
                }
        }
	function echo_time ($start_time) {
                $time = microtime(true) - $start_time;
                $time = mb_substr($time , 0, 5);
                return $time;
        }
	echo post_links();
	echo 'Час виповнення скрипту '. echo_time($start_time) . ' секунд';
?>
