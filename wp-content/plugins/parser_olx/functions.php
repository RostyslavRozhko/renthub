<?php
	function curl_content($url){
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36');
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER ,false);
	    $html = curl_exec($ch);
	    return $html;
	    unset($url);
	}
	function telephone ($link) {
	    $array = array("url" => $link);
	    $tmp = tempnam("/tmp", "PHANTOM_"); 
	    file_put_contents($tmp, "var params = ".json_encode($array)."; ".file_get_contents(plugins_url("parser_olx/js/click_phone.js"))); 
	    return exec("/usr/local/bin/phantomjs ".escapeshellarg($tmp));
	    unlink($tmp);
	}
	function get_img ($link) {
	    $array = array("url" => $link);
	    $tmp = tempnam("/tmp", "PHANTOM_"); 
	    file_put_contents($tmp, "var img = ".json_encode($array)."; ".file_get_contents(plugins_url("parser_olx/js/get_img.js"))); 
	    return exec("/usr/local/bin/phantomjs ".escapeshellarg($tmp));
	    unlink($tmp);
	}
	function RandomEmail($max = 6) { 
	    $i = 0; 
	    $possible_keys = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
	    $keys_length = strlen($possible_keys); 
	    $str = ""; 
	    while($i < $max) { 
	        $rand = mt_rand(1,$keys_length-1); 
	        $str.= $possible_keys[$rand]; 
	        $i++; 
	    } 
	  return $str; 
	}
	function get_one_post ($names) {
	    global $wpdb;
	    $get_title = $wpdb->get_row("SELECT ID , post_title  FROM $wpdb->posts WHERE post_type = 'arenda' AND post_title = '$names'");
	    return $get_title;
	}
	function get_one_post_author_exists ($names_ad , $post_author) {
	    global $wpdb;
	    $get_title_author_post = $wpdb->get_row("SELECT ID , post_title , post_author  FROM $wpdb->posts WHERE post_type = 'arenda' AND post_title = '$names_ad' AND post_author = '$post_author' ");
	    return $get_title_author_post;
	}
	function getGeoPosition($adress){
	    $url_maps = "https://maps.google.com/maps/api/geocode/json?key=AIzaSyBpRFvYomx8_jJ2e2R6sCsGEUVkrpfohLc&address=" . urlencode($adress). "&sensor=true";
	    $json = file_get_contents($url_maps);
	    $data = json_decode($json, TRUE);
	    if($data['status']=="OK"){
	      $address_code = '["' . $data['results'][0][place_id] . '"]';
	      $townstr = "[{" . '"lat"' . ':' . $data['results'][0][geometry][location][lat]. ',' . '"lng"' . ':' . $data['results'][0][geometry][location][lng]. "}]";
	      $get_geo_data = array(
	        'address' => $address_code, 
	        'town' => $townstr
	      );
	    return $get_geo_data;
	    }
	}
	function parser ($url, $start, $end , $category){
	  if($start < $end)  {

	      $get_content = curl_content($url);
	      $doc = phpQuery::newDocument($get_content);
	      $contents = $doc->find('#offers_table');
	      $contents = $contents->find('.breakword');
	      foreach ($contents as $content) {
	          $content = pq($content);
	          /*назва оголошення*/
	          $ad_name = trim($content->find('.lheight20')->text());
	          /*Ссилка на оголошення*/
	          $links = $content->find('a')->attr('href');

	          $new_tel = telephone($links);
	          /*Ціна , якщо є*/
	          $price = $content->find('p.price > strong')->text();
	          /*Місто*/
	          $town_time = $content->find('.space');
	          $town = trim($town_time->find('.breadcrumb > span')->text());
	          /*Отримання данних з карточки товару*/
	          $get_item_content = curl_content($links);
	          $doc_item = phpQuery::newDocument($get_item_content);
	          $content_item = $doc_item->find('.descriptioncontent');
	          /*Опис оголошення*/
	          $description = trim($content_item->find('#textContent')->html());
	          /*Картинка , ящко є*/

	          $img_array = explode(',' , get_img($links));

	          foreach ($img_array as $img) {
	              $new_img = str_replace('https://img01-olxua.akamaized.net/img-olxua/' , "" , $img);
	              $upload_dir = (object) wp_upload_dir($time);
	              $path = $upload_dir->path.'/' . $new_img;
	              if (!file_exists($path)) {
	                  copy($img, $path);
	              }
	          }
	          /*Ім'я власника оголошення*/
	          $name = trim($doc_item->find('.offer-user__details > h4')->text());

	          $town_new = getGeoPosition($town)['town'];
	          $address_new = getGeoPosition($town)['address'];

	          $user_exists = get_user_by('login', $new_tel);

	          if (!$user_exists->ID && !empty($new_tel)) {
	            $user_id = wp_create_user($new_tel , '7kgpzz1957' , RandomEmail(6).'@email.ua');
	            $user_id_role = new WP_User($user_id);
	            $user_id_role->set_role('editor');

	            $post_ads = array(
	              'post_author'   => $user_id,
	              'post_content'  => $description,
	              'post_status'   => 'publish',
	              'post_title'    => $ad_name,
	              'post_parent'   => '',
	              'post_type'     => 'arenda',
	            );
	            $get_post_parse = get_one_post($ad_name);
	            if ($get_post_parse->post_title !== $ad_name && !empty($new_tel)) {
	                    $post_id = wp_insert_post($post_ads, $wp_error);
	                    wp_set_post_terms($post_id, $category, 'cate' , false);

	                    foreach ($img_array as $img) {
	                        $attachment = array(
	                            'post_author' => $user_id,
	                            'post_mime_type' => 'image/jpeg',
	                            'post_title' => preg_replace( '/\.[^.]+$/', '', str_replace('https://img01-olxua.akamaized.net/img-olxua/' , "" , $img)),
	                            'post_content' => '',
	                            'post_status' => 'inherit',
	                            'guid' => $upload_dir->url.'/' . str_replace('https://img01-olxua.akamaized.net/img-olxua/' , "" , $img)
	                        );
	                        $attachment_id = wp_insert_attachment($attachment, $upload_dir->url.'/' .  str_replace('https://img01-olxua.akamaized.net/img-olxua/' , "" , $img));
	                        require_once(ABSPATH . 'wp-admin/includes/image.php');
	                        $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_dir->path.'/' .  str_replace('https://img01-olxua.akamaized.net/img-olxua/' , "" , $img));
	                        wp_update_attachment_metadata($attachment_id, $attachment_data);
	                    }
	                    if (count($img_array) == 1) {
	                      add_post_meta($post_id, 'img1', $upload_dir->url.'/' .str_replace('https://img01-olxua.akamaized.net/img-olxua/' , "" , $img_array[0]), true);
	                    }
	                    else if (count($img_array) == 2) {
	                        add_post_meta($post_id, 'img1', $upload_dir->url.'/' .str_replace('https://img01-olxua.akamaized.net/img-olxua/' , "" , $img_array[0]), true);
	                        add_post_meta($post_id, 'img2', $upload_dir->url.'/' .str_replace('https://img01-olxua.akamaized.net/img-olxua/' , "" , $img_array[1]), true);
	                    }
	                    else {
	                        add_post_meta($post_id, 'img1', $upload_dir->url.'/' .str_replace('https://img01-olxua.akamaized.net/img-olxua/' , "" , $img_array[0]), true);
	                        add_post_meta($post_id, 'img2', $upload_dir->url.'/' .str_replace('https://img01-olxua.akamaized.net/img-olxua/' , "" , $img_array[1]), true);
	                        add_post_meta($post_id, 'img3', $upload_dir->url.'/' .str_replace('https://img01-olxua.akamaized.net/img-olxua/' , "" , $img_array[2]), true);
	                    }

	                    add_post_meta($post_id, 'cc_price', $price, true);
	                    update_user_meta($user_id, 'nickname', $name);
	                    update_user_meta($user_id, 'phone', strip_tags($new_tel) , true);
	                    add_post_meta($post_id, 'cc_locations', $town_new);
	                    add_post_meta($post_id, 'cc_address_list', $address_new);
	              }
	              else {
	                  $post_id = get_post($get_post_parse->ID);
	                  wp_update_post($post_id);
	              }
	          }
	          else if($user_exists->ID && !empty($new_tel)){
	              $post_ads = array(
	                'post_author'   => $user_exists->ID,
	                'post_content'  => $description,
	                'post_status'   => 'publish',
	                'post_title'    => $ad_name,
	                'post_parent'   => '',
	                'post_type'     => 'arenda',
	              );
	              $get_post_parse = get_one_post($ad_name);
	              $get_post_parse_author = get_one_post_author_exists($ad_name , $user_exists->ID);
	              if (!empty($new_tel) && !$get_post_parse_author->post_author) {
	                    $post_id = wp_insert_post($post_ads, $wp_error);
	                    wp_set_post_terms($post_id, $category, 'cate' , false);

	                    foreach ($img_array as $img) {
	                         $attachment = array(
	                            'post_author' => $user_exists->ID,
	                            'post_mime_type' => 'image/jpeg',
	                            'post_title' => preg_replace( '/\.[^.]+$/', '', str_replace('https://img01-olxua.akamaized.net/img-olxua/' , "" , $img)),
	                            'post_content' => '',
	                            'post_status' => 'inherit',
	                            'guid' => $upload_dir->url.'/' . str_replace('https://img01-olxua.akamaized.net/img-olxua/' , "" , $img)
	                        );
	                        $attachment_id = wp_insert_attachment($attachment, $upload_dir->url.'/' . str_replace('https://img01-olxua.akamaized.net/img-olxua/' , "" , $img));
	                        require_once(ABSPATH . 'wp-admin/includes/image.php');
	                        $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_dir->path.'/' . str_replace('https://img01-olxua.akamaized.net/img-olxua/' , "" , $img));
	                        wp_update_attachment_metadata($attachment_id, $attachment_data);
	                    }
	                    if (count($img_array) == 1) {
	                      add_post_meta($post_id, 'img1', $upload_dir->url.'/' .str_replace('https://img01-olxua.akamaized.net/img-olxua/' , "" , $img_array[0]), true);
	                    }
	                    else if (count($img_array) == 2) {
	                        add_post_meta($post_id, 'img1', $upload_dir->url.'/' .str_replace('https://img01-olxua.akamaized.net/img-olxua/' , "" , $img_array[0]), true);
	                        add_post_meta($post_id, 'img2', $upload_dir->url.'/' .str_replace('https://img01-olxua.akamaized.net/img-olxua/' , "" , $img_array[1]), true);
	                    }
	                    else {
	                        add_post_meta($post_id, 'img1', $upload_dir->url.'/' .str_replace('https://img01-olxua.akamaized.net/img-olxua/' , "" , $img_array[0]), true);
	                        add_post_meta($post_id, 'img2', $upload_dir->url.'/' .str_replace('https://img01-olxua.akamaized.net/img-olxua/' , "" , $img_array[1]), true);
	                        add_post_meta($post_id, 'img3', $upload_dir->url.'/' .str_replace('https://img01-olxua.akamaized.net/img-olxua/' , "" , $img_array[2]), true);
	                    }

	                    add_post_meta($post_id, 'cc_price', $price, true);
	                    update_user_meta($user_exists->ID, 'nickname', $name);
	                    update_user_meta($user_exists->ID, 'phone', strip_tags($new_tel) , true);
	                    add_post_meta($post_id, 'cc_locations', $town_new);
	                    add_post_meta($post_id, 'cc_address_list', $address_new);
	              }
	              else {
	                  $post_id = get_post($get_post_parse_author->ID);
	                  wp_update_post($post_id);
	              }
	          }
	    }
	    $get_next_link = $doc->find('.item > .current')->parent()->next()->children()->attr('href');
	    if(!empty($get_next_link)){
	      $start++;
	      parser($get_next_link, $start, $end , $category);
	      }
	  }
	}
	function get_all_page ($url) {
	    $get_content = curl_content($url);
	    $doc = phpQuery::newDocument($get_content);
	    $get_pagination = $doc->find('.pager');
	    $get_last_page = trim($get_pagination->find('.next')->prev()->text());
	    return $get_last_page;
	}
	function parser_admin () {
	$categories = get_terms(array(
	  'taxonomy'      => 'cate',
	  'orderby'       => 'id', 
	  'order'         => 'ASC',
	  'hide_empty'    => false, 
	  'object_ids'    => null, 
	  'include'       => array(),
	  'exclude'       => array(), 
	  'exclude_tree'  => array(), 
	  'number'        => '', 
	  'fields'        => 'all', 
	  'count'         => false,
	  'slug'          => '', 
	  'parent'         => '',
	  'hierarchical'  => true, 
	  'child_of'      => 0, 
	  'get'           => '', 
	  'name__like'    => '',
	  'pad_counts'    => false, 
	  'offset'        => '', 
	  'search'        => '', 
	  'cache_domain'  => 'core',
	  'name'          => '', 
	  'childless'     => false,
	  'update_term_meta_cache' => true,
	  'meta_query'    => '',
	)); 
	?>
	  <h2><?php echo get_admin_page_title() ?></h2>
	  <div class="container-fluid">
	    <form action="" method="post">
	        <label>Категорія:</label><br>
	        <select type="text" id="cate" class="form-control col-md-6" name="category">
	          <?php foreach ($categories as $category) { ?>
	          <?php if ($category->parent){ ?>
	                <option value="<?=$category->term_id?>,<?=$category->parent;?>">&nbsp;&nbsp;&nbsp;<?=$category->name;?></option>
	          <?php } else {?>
	                <option value="<?=$category->term_id?>"><?=$category->name?></option>
	          <?php } ?>
	          <?php } ?>
	        </select>
	        <br>
	        <label for="url">URL адрес:</label><input type="text" value="<?=$_POST['url'];?>" id="url" class="form-control col-md-6" name="url"/><br>
	        <label for="current">Перша сторінка</label><input type="text" value="" id="current" class="form-control col-md-6" name="current" /><br>
	        <label for="next">По яку сторінку парсити</label><input type="text" value="" id="next" class="form-control col-md-6" name="next"/><br> 
	    <input type="submit" class="btn btn-success button action" value="Отримуємо контент"><img class="loading" src="<?php echo plugins_url('parser_olx/images/loader.gif');?>"><div class="content_count">Пройшло часу&nbsp;<div id="count">1</div>&nbsp;секунд</div>
	    </form>
	  </div>
	  <?php
	    if(isset($_POST['current']) && isset($_POST['next']) && !empty($_POST['current']) && !empty($_POST['next'])){
	      $start_time = microtime(true);
	      $url = $_POST['url'];
	      if (preg_match('/^(http|https):\\/\\/[a-z0-9]+([\\-\\.]{1}[a-z0-9]+)*\\.[a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i' , $url) !== 1){
	          echo '<span class="error_url">Введіть коректний урл!</span>';
	      }
	      $start = trim($_POST['current']);
	      $end = trim($_POST['next']);
	      $category = array_map('intval' , explode(',' , $_POST['category']));
	      parser($url, $start, $end , $category);
	      function echo_time ($start_time) {                                                      
	        $time = microtime(true) - $start_time;                                             
	        $time = mb_substr($time , 0, 5);
	        return $time;
	      }
	      ?>
	      <b class="time_script">Час виповнення скрипту: <?php echo echo_time($start_time); ?>&nbsp;секунд</b>
	<?php
	  }
	}
?>