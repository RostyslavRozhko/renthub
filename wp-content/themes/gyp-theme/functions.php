<?php
  require __DIR__.'/S3.php';  
  load_theme_textdomain('prokkat', get_stylesheet_directory() . '/lang');
  
  //require_once( get_template_directory() . '/library/control/theme_functions.php' );
  //require_once( get_template_directory() . '/admin/dynamic-image.php' );
  require_once( ABSPATH . '/wp-admin/includes/template.php' );
  require_once( get_stylesheet_directory() . '/map_functions.php' );
  require_once( get_stylesheet_directory() . '/search-section.php' );
  require_once( get_stylesheet_directory() . '/messages_functions.php' );
  require_once( get_stylesheet_directory() . '/libs/custom-ajax-auth.php' );
  require_once( get_stylesheet_directory() . '/gateways/liqpay/liqpay-ipn.php' );
  

  add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
  add_action( 'wp_enqueue_scripts', 'theme_dequeue_scripts', 100 );
  add_action( 'wp_enqueue_scripts', 'plupload_scripts' );
  add_action( 'admin_enqueue_scripts', 'plupload_scripts' );
  add_action( 'admin_enqueue_scripts', 'my_admin_enqueue_styles' );
  remove_action('wp_enqueue_scripts', 'fep_enqueue_scripts');

  function add_cors_http_header(){
    header("Access-Control-Allow-Origin: *");
}
add_action('init','add_cors_http_header');

  function theme_dequeue_scripts() {
	
  }

  function theme_enqueue_styles()
  {
    show_admin_bar(false);
	$scriptsrc = get_stylesheet_directory_uri() . '/js/';
    $libsrc = get_stylesheet_directory_uri() . '/libs/';
	
		wp_register_script( 'close_message_delete', $scriptsrc . 'close_message_delete.js', 'jquery', '1.1', true );
		wp_enqueue_script( 'close_message_delete' );
	//wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_uri(), array(), '1.0' );
	wp_enqueue_style( 'main', get_stylesheet_directory_uri() . '/css/main.css', array(), '1.1' );
	
	wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', $libsrc . '/jquery/jquery-1.11.3.min.js', '1.0', true );
    wp_enqueue_script('jquery');

    wp_register_script( 'slick-js', $libsrc . 'slick/slick.min.js', 'jquery', '1.1', true );
	wp_register_script( 'fancybox-js', $libsrc . 'fancybox/jquery.fancybox.pack.js', 'jquery', '1.0', true );
  wp_register_script( 'common-js', $scriptsrc . 'common.js', 'jquery', '1.1', true );
  wp_register_script( 'cookies-js', $scriptsrc . 'cookies.js', 'jquery', '1.1', true );
	
	wp_enqueue_script( 'slick-js' );
	wp_enqueue_script( 'fancybox-js' );
    wp_enqueue_script( 'common-js' );
    wp_enqueue_script( 'cookies-js' );
	
	wp_localize_script( 'common-js', 'commonjs_object', array( 
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'show_text' => __('Show', 'prokkat'),
        'hide_text' => __('Hide', 'prokkat'),
    ));
	
	wp_register_script( 'leads-script', get_stylesheet_directory_uri() . '/js/leads-script.js', array('jquery'), '1.5', true );
    wp_enqueue_script( 'leads-script' );

    wp_localize_script( 'leads-script', 'leads_object', array( 
      'ajaxurl' => admin_url( 'admin-ajax.php?lang='.pll_current_language() ),
      'loadingmessage' => __('Sending message...', 'prokkat'),
      'successmessage' => __('Сообщение успешно отправлено!', 'prokkat'),
	  'nonce' => wp_create_nonce('fep-message'),
	  'equalTo' => __('Wrong answer.', 'prokkat'),
    ));
	
	wp_register_script('validate-script', get_stylesheet_directory_uri() . '/js/jquery.validate.min.js', array('jquery'), '1.2', true );
  wp_enqueue_script('validate-script');
  
  wp_localize_script( 'validate-script', 'validate_object', array(
      'required' => __('This field is required.', 'prokkat'),
	    'email' => __('Please enter a valid email address.', 'prokkat')
    ));
  }
  
  function my_admin_enqueue_styles()
  {
    wp_enqueue_style( 'fb', site_url() . '/wp-content/plugins/nextend-facebook-connect/buttons/facebook-btn.css' );
  }
  
  function plupload_scripts() {
    if( !is_user_logged_in() )
      return;
    wp_enqueue_script('plupload-all');

    wp_register_script('myplupload', get_stylesheet_directory_uri() .'/js/myplupload.js', array('jquery'), '1.4', true );
    wp_enqueue_script('myplupload');

	if( is_admin() ) {
      wp_register_style('myplupload', get_stylesheet_directory_uri() .'/css/myplupload.css');
      wp_enqueue_style('myplupload');
    }
  }
/*
  if( current_user_can('contributor') ) add_action('admin_init', 'allow_contributor_uploads');
  function allow_contributor_uploads() {
	$contributor = get_role('contributor');
	$contributor->add_cap('upload_files');
	$contributor->add_cap('edit_published_posts');
  }
*/
  // Show expired ad single page (draft) for everyone
  add_filter('pre_get_posts', 'preview_draft_posts');
  function preview_draft_posts($query) {
    if( is_single() )
      $query->set('post_status', array('publish', 'pending', 'draft'));
    return $query;
  }
  
  function after_setup_theme_actions() {
	// Disable Admin Bar for All Users Except for Administrators
    if (!current_user_can('administrator') && !is_admin()) {
      show_admin_bar(false);
    }
	// Add location for footer menu to admin panel
  register_nav_menu('footer_buyers', 'Buyers Menu');
  register_nav_menu('footer_sellers', 'Sellers Menu');
  register_nav_menu('footer_about', 'About Menu');
  register_nav_menu('popular-cats', 'Popular Cats Menu');
  register_nav_menu('cities_menu', 'Cities Menu');
	// Disable front-end-pm plugin default mailing
	if( !is_admin() )
	  remove_action('wp_loaded', array(fep_email_class::init(), 'actions_filters'));
  }
  add_action('after_setup_theme', 'after_setup_theme_actions');
  
  
  function my_action_url( $action = '' ) {
	  return add_query_arg( array( 'action'=>$action ), site_url( 'dashboard/' ));
  }
  
  
/***
 * Translation
 *
 */
  add_filter( 'wp_get_nav_menu_items', 'translate_nav_menu_items', 10, 2 ); // nav menu items
  function translate_nav_menu_items( $items ) {
    foreach ( $items as $key => $item ) {
        $items[$key]->title = __($items[$key]->title, 'cc');
    }
    return $items;
  }
  
  add_filter('the_title', 'translate_string');
  add_filter('single_post_title', 'translate_string'); // page title of ad
  add_filter('single_term_title', 'translate_string'); // page title of category
  add_filter('widget_title', 'translate_string'); // widget title
  add_filter('the_category', 'translate_string', 10, 2 );
  add_filter('list_cats', 'translate_string');
  function translate_string( $str ) {
	  return __( $str, 'cc' );
  }

  add_filter( 'get_the_terms', 'translate_get_terms', 10, 2 ); // Categories
  if( !is_admin() ) add_filter( 'get_terms', 'translate_get_terms', 10, 2 ); // Categories
  function translate_get_terms( $items ) {
    foreach ( $items as $key => $item ) {
	  if (isset($items[$key]->name)){
        $items[$key]->name = __($items[$key]->name, 'cc');
	  }
    }
	usort($items, array("TestObj", "cmp_obj"));
    return $items;
  }
  
  class TestObj{
    var $name;
    function TestObj($name) {
        $this->name = $name;
    }
    static function cmp_obj($a, $b) {
        $al = mb_strtolower( $a->name,'UTF-8' );
        $bl = mb_strtolower( $b->name,'UTF-8' );
		if( mb_substr( $al,0,1,'UTF-8' ) == 'і' ) $al = substr_replace( $al,'и',0,1 );
        return strcmp( $al, $bl );
    }
  }

  
  // add_filter( 'site_url', 'add_lang_site_url' );
  // function add_lang_site_url( $url ) {
	// if( !is_admin() )
	//   if( pll_current_language() != 'uk' ) $url = add_query_arg( 'lang', pll_current_language(), $url );
	// return $url;
  // }

/******************************************************/
  
/*
 * Add class to submenu link
 */
  add_filter( 'nav_menu_link_attributes', 'filter_nav_menu_link_attributes', 10, 4 ); 
  function filter_nav_menu_link_attributes( $atts, $item, $args, $depth ) { 
    $atts['class'] = 'submenu__link';
    return $atts; 
  }
  
  class Popular_Walker_Nav_Menu extends Walker_Nav_Menu {
    private $city_name;
    private $city_id;

    public function __construct($city_name, $city_id) {
        $this->city_name = $city_name;
        $this->city_id = $city_id;
    }

    function start_lvl( &$output, $depth = 0, $args = array() ) {
      $indent = str_repeat("\t", $depth);
      $output .= "\n$indent\n";
    }

    function end_lvl( &$output, $depth = 0, $args = array() ) {
      $indent = str_repeat("\t", $depth);
      $output .= "$indent\n";
    }

    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
      $city_title = $item->title;
      $city_title .=  ' ' .$this->city_name;
      $item_output = "";

      $url = add_query_arg(array('search_loc' => $this->city_id, 'address' => $this->city_name), esc_attr($item->url));

      $item_output .= '<li>';
      $item_output .= '<a href="'.$item->url.'">';
      $item_output .= '<img src="'. get_wp_term_image($item->object_id) .'" >';
      $item_output .= '<span>' . $city_title . '</span>';
      $item_output .= '</a>';
      $item_output .= '</li>';
      $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    function end_el( &$output, $item, $depth = 0, $args = array() ) {
      $output .= "\n";
    }
  }

/*
 * Change Main Menu class
 */
  class Main_Walker_Nav_Menu extends Walker_Nav_Menu
  {
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
 
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
		
		if ( $depth == 0 ) {
		  $classes[] = 'menu__item';
          $args->link_after = "<i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i>";
		}
	    else $args->link_after = '';

        $args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
 
        $output .= $indent . '<li' . $id . $class_names .'>';
 
        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
        $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
        $atts['href']   = ! empty( $item->url )        ? $item->url        : '';

        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
 
        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title = apply_filters( 'the_title', $item->title, $item->ID );

        $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );
 
        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= $args->link_before . $title . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;
 
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
 
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		if($depth==0) { 
			$output .= "\n$indent
                                <div class=\"submenu \" >
                                    <div >
                                        <div >
                                            <ul class=\"submenu__item\">\n";
		} else {
			$output .= "\n$indent<div class=\"submenu \" >
                                    <div >
                                        <div>
                                            <ul class=\"submenu__item\">\n";
		}
	}
	
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>
                                        </div>
                                    </div>
                                </div>\n";
	}
  } //Main_Walker_Nav_Menu

  
/*
 * Change Category List class
 */
  class Custom_Walker_Category extends Walker_Category
  {
    public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 )
	{
        //$cat_link = add_query_arg( $_SERVER['QUERY_STRING'], '', esc_url( get_term_link( $category ) ) );
		
		parse_str( $_SERVER['QUERY_STRING'], $array);
		
		$search_loc = isset( $array['search_loc'] ) ? $array['search_loc'] : '';
		$city = isset( $array['address'] ) ? $array['address'] : '';
		$cat_link = add_query_arg( array( 'search_loc' => $search_loc, 'address' => $city, 'lang' => pll_current_language() ), esc_url( get_term_link( $category )));
    
    $selected = $args['selected'];
    
		$cat_name = apply_filters(
            'list_cats',
            esc_attr( $category->name ),
            $category
        );
 
        if ( ! $cat_name ) {
            return;
        }
 
   	if ( $depth == 0 ) {
      $class = "menu-cat__item_title";
      if($category->term_id == $selected) {
        $class .= ' selected-cat';
      }
      $link = '<li><a class="'. $class .'" href="' . $cat_link . '" ';
		}
		else {
		  $link = '<a class="menu-cat__link" href="' . $cat_link . '" ';
		}
 
        $link .= '>';
        $link .= $cat_name . '</a>';
      
        if ( 'list' == $args['style'] ) {
            $output .= "\t<li";
            $css_classes = array(
                'menu-cat__item',
                'cat-item-' . $category->term_id,
            );
            $css_classes = implode( ' ', apply_filters( 'category_css_class', $css_classes, $category, $depth, $args ) );
            
            $output .=  ' class="' . $css_classes . '"';
            $output .= ">$link\n";
        } elseif ( isset( $args['separator'] ) ) {
            $output .= "\t$link" . $args['separator'] . "\n";
        } else {
            $output .= "\t$link\n";
        }
    }
  } // Custom_Walker_Category

/******************************************************************/

  function is_premium() {
    global $wpdb;
    $cc_post_status = "publish";
    $cc_post_type = POST_TYPE;
    $cc_meta_add_type = "cc_add_type";
    
    $query = "SELECT $wpdb->posts.*
    FROM
    $wpdb->posts
    INNER JOIN $wpdb->postmeta
    ON $wpdb->posts.ID = $wpdb->postmeta.post_id
    INNER JOIN $wpdb->term_relationships
    ON $wpdb->posts.ID = $wpdb->term_relationships.object_id
    WHERE
    $wpdb->posts.post_type = '$cc_post_type'
    AND $wpdb->posts.post_status = '$cc_post_status'
    AND ($wpdb->postmeta.meta_key ='$cc_meta_add_type' AND $wpdb->postmeta.meta_value = 'pro')";

    $classifields = array();
    $classifields['query'] = $wpdb->query($query);
    $classifields['result'] = $wpdb->get_results($query);
    return sizeOf($classifields['result']) > 0;
  }

	function debug_to_console( $data ) {
    	$output = $data;
    	if ( is_array( $output ) )
        	$output = implode( ',', $output);

    	echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
  }	
  
  function autocomplete() {
    wp_enqueue_script('autocomplete', get_stylesheet_directory_uri().'/js/autocomplete.js', array('jquery'));

    wp_enqueue_style('autocomplete.css', get_stylesheet_directory_uri().'/css/jquery.auto-complete.css');

    wp_enqueue_script('jq_autocomplete', get_stylesheet_directory_uri().'/js/jquery.auto-complete.min.js', array('jquery'));
  }
  add_action('wp_enqueue_scripts', 'autocomplete');

  add_action('wp_ajax_nopriv_get_listing_names', 'ajax_listings');
add_action('wp_ajax_get_listing_names', 'ajax_listings');
 
function ajax_listings() {
  global $wpdb;
  $cc_post_status = "publish";
  $cc_post_type = POST_TYPE;
 
	$name = $wpdb->esc_like(stripslashes($_POST['name'])).'%';
	$sql = "SELECT post_title 
		FROM $wpdb->posts 
		WHERE post_title LIKE '%$name'
    AND $wpdb->posts.post_type = '$cc_post_type'
    AND $wpdb->posts.post_status = '$cc_post_status'";
 
	$results = $wpdb->get_results($sql);
 
	$titles = array();
	foreach( $results as $r )
		$titles[] = addslashes($r->post_title);
		
	echo json_encode($titles);
 
	die();
}

add_filter( 'posts_where', 'wpse18703_posts_where', 10, 2 );
function wpse18703_posts_where( $where, &$wp_query )
{
    global $wpdb;
    if ( $wpse18703_title = $wp_query->get( 'title_like' ) ) {
        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'' . esc_sql( $wpdb->esc_like( $wpse18703_title ) ) . '%\'';
    }
    return $where;
}

if ( ! function_exists( 'ipt_kb_total_cat_post_count' ) ) :

  function ipt_kb_total_cat_post_count( $cat_id ) {
      $q = new WP_Query( array(
          'nopaging' => true,
          'tax_query' => array(
              array(
                  'taxonomy' => 'category',
                  'field' => 'id',
                  'terms' => $cat_id,
                  'include_children' => true,
              ),
          ),
          'fields' => 'ids',
      ) );
      return $q->post_count;
  }
  
  endif;

  function get_cate_count($term_id, $parent = false) {
    global $wpdb;
	  $query = '';
    $cc_post_status = "publish";
    $cc_post_type = POST_TYPE;
    $cats = array();

    if($parent) {
      $children = get_term_children($term_id, CUSTOM_CAT_TYPE);
      foreach($children as $child) {
        $id = pll_get_term($child, 'ru');
        if($id)
          $cats[] = $id;        
        $id = pll_get_term($child, 'uk');
        if($id)
          $cats[] = $id; 
      }
    } else {
      $id = pll_get_term($term_id, 'ru');
      if($id)
        $cats[] = $id;        
      $id = pll_get_term($term_id, 'uk');
      if($id)
        $cats[] = $id; 
    }

    $arr_str = implode(",", $cats);

    $query = "SELECT count($wpdb->posts.ID) AS count
      FROM $wpdb->posts
      INNER JOIN $wpdb->term_relationships
      ON $wpdb->posts.ID = $wpdb->term_relationships.object_id
      INNER JOIN $wpdb->terms
      ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->terms.term_id
      WHERE $wpdb->posts.post_status = '$cc_post_status'
      AND $wpdb->posts.post_type = '$cc_post_type'
      AND ($wpdb->term_relationships.term_taxonomy_id IN ($arr_str))";

    $classifields = array();
    $classifields['query'] = $wpdb->query($query);
    $classifields['result'] = $wpdb->get_results($query);

    $result = '';
    foreach($classifields['result'] as $res) {
      $res = json_decode(json_encode($res), true);
      $result = $res['count'];
    }
    return $result;
  }

  function max_price($category) {
    global $wpdb;
	  $query = '';
    $cc_post_status = "publish";
    $cc_post_type = POST_TYPE;
    $cc_meta_city_id = "cc_city_id";   

    $cats[] = pll_get_term($category, 'uk');
    $cats[] = pll_get_term($category, 'ru');  
    
    $arr_str = implode(",", $cats);

    $query = "SELECT min($wpdb->postmeta.meta_value) AS min,  max($wpdb->postmeta.meta_value) AS max
      FROM $wpdb->posts
      INNER JOIN $wpdb->term_relationships
      ON $wpdb->posts.ID = $wpdb->term_relationships.object_id
      INNER JOIN $wpdb->terms
      ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->terms.term_id
      LEFT JOIN $wpdb->postmeta 
      ON $wpdb->posts.ID = $wpdb->postmeta.post_id
      WHERE $wpdb->posts.post_status = '$cc_post_status'
      AND $wpdb->posts.post_type = '$cc_post_type'
      AND $wpdb->postmeta.meta_key = 'cc_price'
      AND ($wpdb->term_relationships.term_taxonomy_id IN ($arr_str))";

    // $query .= " ORDER BY $wpdb->postmeta.meta_value";


    $classifields = array();
    $classifields['query'] = $wpdb->query($query);
    $classifields['result'] = $wpdb->get_results($query);

    $result = '';
    foreach($classifields['result'] as $res) {
      $res = json_decode(json_encode($res), true);
      if($res['min'] > $res['max']) {
        $result .= $res['max'] . ' - ' . $res['min'];
      } else if($res['min'] == '' || $res['max'] == ''){
        $result .= '0 - 0';
      } else {
        $result .= $res['min'] . ' - ' . $res['max'];
      }
    }
    return $result;
  }

  function get_price_placeholders($arr) {
    $min = 9999999;
    $max = 0;
    foreach ($arr as $item) {
      $price = get_post_meta( $item->id, 'cc_price', true );
      if ($min > $price) {
        $min = $price;
      }
      if ($max < $price) {
        $max = $price;
      }
    }
    $result = [$min, $max];
    return $result;
  }

  
  add_shortcode('custom-pay-status', 'custom_payment_status');
  function custom_payment_status() {
	  
    $payment_method = $_REQUEST['pay_method'];
	$payment_status = __( 'Completed', 'cc' );

	$str ='
	  <div class="fanks fanks_notfound">
        <div class="fanks__title fanks_status">
          <span>'. __( 'Thank you for your payment', 'cc' ) .'</span>
          <p>'. __( 'Payment Status:', 'cc' ) .'  '. $payment_status .'</p>
          <div><i class="fa fa-birthday-cake" aria-hidden="true"></i></div>
          <div class="line"></div>
        </div>
        <div class="text-center">
          <a class="btn btn_lblue" href="'. site_url() .'">'. __( 'Home', 'cc' ) .'</a>
        </div>
      </div>';
	  
    return $str;
  }
  
  
  // Change avatar image source
  function custom_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
    $user = false;
    if ( is_numeric( $id_or_email ) ) {
      $id = (int) $id_or_email;
      $user = get_user_by( 'id' , $id );
    } elseif ( is_object( $id_or_email ) ) {
      if ( ! empty( $id_or_email->user_id ) ) {
        $id = (int) $id_or_email->user_id;
        $user = get_user_by( 'id' , $id );
      }
    } else {
      $user = get_user_by( 'email', $id_or_email );	
    }
    if ( $user && is_object( $user ) ) {
      $ava = get_user_meta($user->data->ID, 'user_avatar', true);
	  if( $ava ) {
		$url = parse_url($ava);
        $site_url = parse_url(site_url());
        if( $url['host'] == $site_url['host'] ) {
		  $src_info = pathinfo( $ava );
          $ava_resized = $src_info['dirname'].'/'.$src_info['filename']."_152X152.".$src_info['extension'];
		  $ava = file_url_exists( $ava_resized ) ? $ava_resized : $ava;
	    }
		$avatar = "<img alt='{$alt}' src='{$ava}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
	  }
    }
    return $avatar;
  }
  add_filter('get_avatar', 'custom_avatar', 1, 5);
  
  
/***
 * Functions for plupload photo uploading
 *
 ***/
  function plupload_admin_head() {
	global $current_user;
  // place js config array for plupload
    $plupload_init = array(
        'runtimes' => 'html5,silverlight,flash,html4',
        'browse_button' => 'plupload-browse-button', // will be adjusted per uploader
        'container' => 'plupload-upload-ui', // will be adjusted per uploader
        'file_data_name' => 'async-upload', // will be adjusted per uploader
        'multiple_queues' => true,
        'max_file_size' => wp_max_upload_size() . 'b',
        'url' => admin_url('admin-ajax.php'),
        'flash_swf_url' => includes_url('js/plupload/plupload.flash.swf'),
        'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
        'filters' => array(array('title' => __('Allowed Files'), 'extensions' => '*')),
        'multipart' => true,
        'urlstream_upload' => true,
        'multi_selection' => false, // will be added per uploader
         // additional post data to send to our ajax hook
        'multipart_params' => array(
            '_ajax_nonce' => "", // will be added per uploader
            'action' => 'plupload_action', // the ajax action name
            'imgid' => 0, // will be added per uploader
            'user_id' => $current_user->ID,
        ),
        'main_img_caption' => __('Main image', 'prokkat'),
        'make_main_text' => __( 'Make the main', 'prokkat' ),
		'no-avatar' => get_stylesheet_directory_uri() .'/img/no-avatar.png',
    );
    ?>
    <script type="text/javascript">
      var base_plupload_config=<?php echo json_encode($plupload_init); ?>;
    </script>
    <?php
  }
  add_action("wp_head", "plupload_admin_head");
  add_action("admin_head", "plupload_admin_head");
  
/*Upload img to amazon*/
	function upload_amazon($file_path,$file_name) {
	      $accessKey = 'AKIAJRPSL3LGIFILFIYA';
        $secretKey = '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';
        $s3 = new S3($accessKey, $secretKey);
        $bucket = S3::listBuckets()[0];
        return S3::putObject(S3::inputFile($file_path, false), $bucket, $file_name , S3::ACL_PUBLIC_READ);
	}

  function translit($s) {
    $s = (string) $s;
    $s = strip_tags($s);
    $s = str_replace(array("\n", "\r"), " ", $s);
    $s = preg_replace("/\s+/", ' ', $s);
    $s = trim($s);
    $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s);
    $s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
    $s = str_replace(" ", "-", $s);
    return ucfirst($s);
}
  function g_plupload_action() {

    // check ajax noonce
    $imgid = $_POST["imgid"];
    check_ajax_referer($imgid . 'pluploadan');

    // handle file upload
    $status = wp_handle_upload($_FILES[$imgid . 'async-upload'], array('test_form' => true, 'action' => 'plupload_action'));

	/*For upload img to amazon*/
	$filename = $status['file'];

	$file_path = $status['file'];
	$file_name_img = $_FILES[$imgid . 'async-upload']['name'];

	upload_amazon ($file_path , translit($file_name_img));

    $attachment = array(
        'post_mime_type' => $status['type'],
        'post_title' => preg_replace( '/\.[^.]+$/', '', basename($filename)),
        'post_content' => '',
        'post_status' => 'inherit',
        'guid' => $status['url']
    );
    $attachment_id = wp_insert_attachment( $attachment, $status['url'] );
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
    wp_update_attachment_metadata( $attachment_id, $attachment_data );

	//  Resize imagewal
	if( $imgid == "ava" ) {
	      $img_url = get_user_meta($_POST["user_id"], 'user_avatar', true);
		  if( $img_url ) 
        delete_images( $img_url );
		  $img_ava_url = get_thumb($status['url'], 152, 152);
      $img_ava_name = basename($img_ava_url);
      $url_amazon = 'https://s3-us-west-1.amazonaws.com/storage-renthub/';
      update_user_meta( $_POST["user_id"], 'user_avatar', $url_amazon.$img_ava_name);

      $upload_dir = (object) wp_upload_dir($time);
	  $path_ava = $upload_dir->path.'/' . $img_ava_name;
	  upload_amazon ($path_ava , $img_ava_name);

	}
    else {
  	  $img_url_515 = get_thumb($status['url'], 515, 515);
  	  $img_url_145 = get_thumb($status['url'], 145, 86);
      $img_img_515 = basename($img_url_515);
      $img_img_145 = basename($img_url_145);
      $upload_dir = (object) wp_upload_dir($time);
      $path_img_145 = $upload_dir->path.'/' . $img_img_145;
  	  $path_img_515 = $upload_dir->path.'/' . $img_img_515;
      upload_amazon ($path_img_145 , $img_img_145);
      upload_amazon ($path_img_515 , $img_img_515);
	}
    // send the uploaded file url in response
    echo $status['url'];
    exit;
  }
  add_action('wp_ajax_plupload_action', "g_plupload_action");
 
  
  function delete_image_ajax()
  {
	if( $_POST["type"]=='ava' ) {
	  global $current_user;
	  $img_url = get_user_meta($current_user->ID, 'user_avatar', true);
	  delete_images( $img_url );
      delete_user_meta( $current_user->ID, 'user_avatar' );
    }
	else {
    $id = get_attachment_url($_POST["img_url"]);
    wp_delete_attachment($id);
	  delete_images( $_POST["img_url"] );
	}
  }
  add_action('wp_ajax_delete_image', "delete_image_ajax");
  

  function get_attachment_url( $attachment_url = '' ) {
 
    global $wpdb;
    $attachment_id = false;
   
    // If there is no url, return.
    if ( '' == $attachment_url )
      return;
   
    // Get the upload directory paths
    $upload_dir_paths = wp_upload_dir();
   
    // Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
    if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {
   
      // If this is the URL of an auto-generated thumbnail, get the URL of the original image
      $attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );
   
      // Remove the upload path base directory from the attachment URL
      $attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );
   
      // Finally, run a custom database query to get the attachment ID from the modified attachment URL
      $attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );
   
    }
   
    return $attachment_id;
  }

  
  function my_photo_markup( $id, $svalue='', $width=800, $height=800 )
  { 
  	?>
      <input type="hidden" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo $svalue; ?>" />
      <div class="plupload-upload-uic hide-if-no-js" id="<?php echo $id; ?>plupload-upload-ui">
	  <?php if( $id == "img1" && !is_admin() ) : ?>
        <input id="<?php echo $id; ?>plupload-browse-button" type="button" value="<?php _e('Select photo', 'prokkat') ?>" class="add__photo-btn" />
	  <?php else : ?>
        <a id="<?php echo $id; ?>plupload-browse-button" href="#0" class="btn_add-img "><img src="<?php echo get_stylesheet_directory_uri();?>/img/add-circular-button.svg" alt="add-image" class="img-responsive"></a>
	  <?php endif; ?>
		<span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce($id . 'pluploadan'); ?>"></span>
        <?php if ($width && $height): ?>
          <span class="plupload-resize"></span><span class="plupload-width" id="plupload-width<?php echo $width; ?>"></span>
          <span class="plupload-height" id="plupload-height<?php echo $height; ?>"></span>
        <?php endif; ?>
        <div class="filelist"></div>
      </div> <?php
  }  
  
/***
 * Generate dropdown categories
 *
 ***/
  function my_dropdown_categories($id, $option_none, $child_of=0, $selected=0 )
  {
	$li_id = '';
	if( $id == 'maincat' || $id == 'subcat' ) $li_id = ' id="'.$id.'-li"';
	
    $args = array(
      'id' => $id,
      'taxonomy' => CUSTOM_CAT_TYPE,
      'show_option_none' => __( $option_none, 'prokkat' ),
      'orderby' => 'name',
      'order' => 'ASC',
      'hide_empty' => 0,
      'hierarchical' => 1,
      'depth' => 1,
	  'child_of' => $child_of,
    'selected' => $selected,
    'class' => 'input_add'
    );
	?>    
      <div class="input-wrp input-wrp_block"  <?php echo $li_id; ?>>
              <?php wp_dropdown_categories( $args ); ?>   
        </div>     
    <?php
  }

  function update_filter_values($filter_id, $value) {
    $tag_id = 'tags_' . $filter_id;
    $old_values = get_field('choices', $tag_id);
    $pos = strpos($old_values, $value);
    if($pos === false) {
      update_field('choices', $old_values . $value . ';', $tag_id);
    }
  }

  function create_title($cate_id, $brand, $model) {
    $term = get_term($cate_id, CUSTOM_CAT_TYPE);
    $term_name = $term->name;
    return 'Аренда ' . mb_strtolower($term_name) . ' ' . $brand . ' ' . $model;
  }

  function update_man_list($cate_id, $value, $field_name) {
    $cate_fields_id = CUSTOM_CAT_TYPE . '_' . $cate_id;
    $old_values = get_field($field_name, $cate_fields_id);
    $pos = strpos($old_values, $value);
    if($pos === false) {
      update_field($field_name, $old_values . $value . ';', $cate_fields_id);
    }
  }

  function get_additional_post_fields() {
    return array(
      array(
        'name' => 'Производитель',
        'slug' => 'manufacturer',
        'check' => 'is_manufacturer'
      ),
      array(
        'name' => 'Модель',
        'slug' => 'model',
        'check' => 'is_model'
      )
    );
  }

  function my_filters_edit_list($cat_id, $post_id) {
    $result = '';
    $cate_fields_id = CUSTOM_CAT_TYPE . '_' . $cat_id;
    $filters = get_field('filters', $cate_fields_id);

    $additional_fields = get_additional_post_fields();
    foreach($additional_fields as $field) {
      $name = $field['name'];
      $slug = $field['slug'];
      $check = $field['check'];
      $is_set = get_field($check , $cate_fields_id);
      if($is_set) {
        $man_array = array_filter(explode(';', str_replace(array("\n","\r"), '', get_field($slug, $cate_fields_id))));
        $meta = get_post_meta( $post_id, $slug, true);  
  
        $result .= '<div class="input-wrp input-wrp_block add__block">';
        
        $result .= '<div class="req form__title">'. $name .'</div>
        <div class="input-wrp input-wrp_block">
        <input type="hidden" name="'. $slug .'_id" value="'. $cat_id .'">
        <select class="input_add edit-select" style="background-color: #f4f4f4;" name="'. $slug .'" placeholder="'. __('Select one', 'prokkat') .'">';
        
        foreach($man_array as $man) {
          $man = trim($man);
          $selected = $meta && $meta == $man ? 'selected' : '';  
          $result .= '<option value="'. $man .'" '. $selected .'>'. $man .'</option>';
        }
  
        $result .= '</select></div></div>';
        $result .= '<script>
          $(".edit-select").editableSelect({ filter: false });
        </script>';
        }
    }

    if($filters) {
      foreach ($filters as $filter) {
        $filter_id = $filter->term_id;
        $tag_id = 'tags_' . $filter_id;
        $required = get_field('required', $tag_id) ? 'req' : '';
        $filter_type = get_field('filter_type', $tag_id);

        $result .= '<div class="input-wrp input-wrp_block add__block">';

        if($filter_type == 'select'){
          $editable = get_field('editable', $tag_id);
          $choices = array_filter(explode(';', str_replace(array("\n","\r"), '', get_field('choices', $tag_id))));
          if($editable) {
            $result .= '<div class="'. $required .' form__title">'. $filter->name .'</div>
            <div class="input-wrp input-wrp_block">
            <span class="field_error"></span>
            <input type="hidden" name="filter_'.$filter_id.'" value="'. $filter_id .'">
            <select class="input_add edit-select" style="background-color: #f4f4f4;" name="'. $filter->slug .'" placeholder="'. __('Select one', 'prokkat') .'">';
            foreach($choices as $choice) {
              $choice = trim($choice);
              $meta = get_post_meta( $post_id, $filter->slug, true);  
              $selected = $meta && $meta == $choice ? 'selected' : ''; 
              $result .= '<option value="'. $choice .'" '. $selected .'>'. $choice .'</option>';
            }

            $result .= '</select></div>';
            $result .= '<script>
              $(".edit-select").editableSelect({ filter: false });
            </script>';
          } else {
            $result .= '<div class="'. $required .' form__title">'. $filter->name .'</div>
            <div class="input-wrp input-wrp_block">
            <span class="field_error"></span>
            <select name="'. $filter->slug .'" class="input_add">';
            
            foreach($choices as $choice) {
              $choice = trim($choice);
              $meta = get_post_meta( $post_id, $filter->slug, true);  
              $selected = $meta && $meta == $choice ? 'selected' : '';  
              $result .= '<option value="'. $choice .'" '. $selected .'>'. $choice .'</option>';
            }
            $result .= '</select></div>';
          }
        }

        if($filter_type == 'checkbox') {
          $choices = array_filter(explode(';', str_replace(array("\n","\r"), '', get_field('choices', $tag_id))));
          $result .= '<div class="'. $required .' form__title">'. $filter->name .'</div>
            <div class="input-wrp input-wrp_block filters filter-edit">';
            foreach($choices as $choice) {
              $choice = trim($choice);
              $meta = get_post_meta( $post_id, $filter->slug, true);
              $value_arr = explode(',', $meta);
              $checked = in_array($choice, $value_arr) ? 'checked' : '';
              $result .= '<label class="checkbox-container">
                <span class="field_error"></span>
                <input '.$checked.' type="checkbox" data-filter="'. $filter->slug .'" value="'. $choice .'">
                <span class="checkmark"></span>
                <div>'. $choice .'</div>
                </label>';
            }
            $result .= '</div>';
            $result .= '<input type="hidden" id="'. $filter->slug .'" class="'. $filter->slug .' filter_value_field" name="'. $filter->slug .'" value="">';
        }

        if($filter_type == 'input') {
          $meta = get_post_meta( $post_id, $filter->slug, true);
          $result .= '<div class="'. $required .' form__title">'. $filter->name .'</div>
                      <div class="input-wrp input-wrp_block ">
                      <span class="field_error"></span>
                      <input type="text" id="title" class="input_add"  value="'. $meta .'" name="'. $filter->slug .'" placeholder="'.__('Please enter title', 'prokkat').'">
                      </div>';
        }
        $result .= '</div>';
      }
    }
    return $result;
  }

  function getCatFilters()
  {
    $current_id = $_POST['catID'];
    $result = '';
    $cate_fields_id = CUSTOM_CAT_TYPE . '_' . $current_id;
    $filters = get_field('filters', $cate_fields_id);
    
    $additional_fields = get_additional_post_fields();
    foreach($additional_fields as $field) {
      $name = $field['name'];
      $slug = $field['slug'];
      $check = $field['check'];
      $is_set = get_field($check , $cate_fields_id);
      if($is_set) {
        $man_array = array_filter(explode(';', str_replace(array("\n","\r"), '', get_field($slug, $cate_fields_id))));
        $result .= '<div class="input-wrp input-wrp_block add__block">';
        
        $result .= '<div class="req form__title">'. $name .'</div>
        <div class="input-wrp input-wrp_block">
        <input type="hidden" name="'. $slug .'_id" value="'. $current_id .'">
        <span class="field_error"></span>
        <select class="input_add edit-select" style="background-color: #f4f4f4;" name="'. $slug .'" placeholder="'. __('Select one', 'prokkat') .'">';
        
        foreach($man_array as $man) {
          $man = trim($man);
          $result .= '<option value="'. $man .'">'. $man .'</option>';
        }
  
        $result .= '</select></div></div>';
        $result .= '<script>
          $(".edit-select").editableSelect({ filter: false });
        </script>';
      }
    }

    if($filters) {
      foreach ($filters as $filter) {
        $filter_id = $filter->term_id;
        $tag_id = 'tags_' . $filter_id;
        $required = get_field('required', $tag_id) ? 'req' : '';
        $filter_type = get_field('filter_type', $tag_id);

        $result .= '<div class="input-wrp input-wrp_block add__block">';

        if($filter_type == 'select'){
          $editable = get_field('editable', $tag_id);
          $choices = array_filter(explode(';', str_replace(array("\n","\r"), '', get_field('choices', $tag_id))));

          if($editable) {
          $result .= '<div class="'. $required .' form__title">'. $filter->name .'</div>
            <div class="input-wrp input-wrp_block">
            <input type="hidden" name="filter_'.$filter_id.'" value="'. $filter_id .'">
            <span class="field_error"></span>
            <select class="input_add edit-select" style="background-color: #f4f4f4;" name="'. $filter->slug .'" placeholder="'. __('Select one', 'prokkat') .'">';
            
            foreach($choices as $choice) {
              $choice = trim($choice);
              $result .= '<option value="'. $choice .'">'. $choice .'</option>';
            }

            $result .= '</select></div>';
            $result .= '<script>
              $(".edit-select").editableSelect({ filter: false });
            </script>';
          } else {
            $result .= '<div class="'. $required .' form__title">'. $filter->name .'</div>
            <div class="input-wrp input-wrp_block">
            <span class="field_error"></span>
            <select name="'. $filter->slug .'" class="input_add">';
            
            foreach($choices as $choice) {
              $choice = trim($choice);
              $result .= '<option value="'. $choice .'">'. $choice .'</option>';
            }
            $result .= '</select></div>';
          }
        }

        if($filter_type == 'checkbox') {
          $choices = array_filter(explode(';', str_replace(array("\n","\r"), '', get_field('choices', $tag_id))));
          $result .= '<div class="'. $required .' form__title">'. $filter->name .'</div>
            <div class="input-wrp input-wrp_block filters filter-edit">';
            foreach($choices as $choice) {
              $choice = trim($choice);
              $result .= '<label class="checkbox-container">
                <span class="field_error"></span>
                <input type="checkbox" data-filter="'. $filter->slug .'" value="'. $choice .'">
                <span class="checkmark"></span>
                <div>'. $choice .'</div>
                </label>';
            }
            $result .= '</div>';
            $result .= '<input type="hidden" class="'. $filter->slug .' filter_value_field" name="'. $filter->slug .'" value="">';
            $result .= '<script>
                  function updateCats() {
                    const values = $(".filters").find("input[type=\'checkbox\']").map(function() {
                        if($(this).prop("checked")) {
                            return this.value
                        }
                      }).get()
                    $(\'.'. $filter->slug .'\').val(values)
                }
                $(".filters").find("input[type=\'checkbox\']").click(function() { updateCats() })
              </script>';
        }

        if($filter_type == 'input') {
          $result .= '<div class="'. $required .' form__title">'. $filter->name .'</div>
                      <div class="input-wrp input-wrp_block ">
                      <span class="field_error"></span>
                      <input type="text" id="title" class="input_add" name="'. $filter->slug .'" placeholder="'.__('Please enter title', 'prokkat').'">
                      </div>';
        }
        $result .= '</div>';
      }
      echo json_encode( $result );
      die();
    } else {
      die('empty');
    }
  }
  add_action( 'wp_ajax_getCatFilters', 'getCatFilters' );


function sendSMS(){
  $code = $_POST['code'];
  $number = $_POST['number'];

  $number = preg_replace('/[() -]/', '', $number);

  $client = new SoapClient('http://turbosms.in.ua/api/wsdl.html');
  
  $auth = [   
    'login' => 'renthub345467',   
    'password' => 'FsGfg3456@'   
  ];   
  
  $client->Auth($auth);

  $message = [
    'sender' => 'Renthub',   
    'destination' => $number,   
    'text' => $code  . ' - Ваш код для авторизации на RentHUB' 
  ];

  $result = $client->SendSMS($message); 

  echo json_encode($result->SendSMSResult->ResultArray[0]);
  die();
}
  
add_action('wp_ajax_sendSMS', 'sendSMS');
add_action('wp_ajax_nopriv_sendSMS', 'sendSMS' );

update_user_meta( 160, 'user_activated', 1 );

function activate_account(){
  $user_id = $_POST['user_id'];
  $res = update_user_meta( $user_id, 'user_activated', 1 );
  if($res) {
    echo json_encode(array('success' => true));
    die();
  }
  echo json_encode(array('success' => false));
  die();
}

add_action('wp_ajax_activate_account', 'activate_account');
add_action('wp_ajax_nopriv_activate_account', 'activate_account' );

  // Generate subcategory dropdown
  function getSubCategories()
  {
    $parentCat = $_POST['catID'];
    $result = '';
    if ($parentCat < 1) die('');
    if (get_categories('taxonomy=cate&child_of=' . $parentCat . '&hide_empty=0')) {
	  $result = wp_dropdown_categories('id=subcat&show_option_none=' . __('Subcategory', 'prokkat') . '&orderby=name&order=ASC&hide_empty=0&hierarchical=1&taxonomy=' . CUSTOM_CAT_TYPE . '&depth=1&echo=0&child_of='.$parentCat);
	  echo json_encode( $result );
	  die();
    }
    else die('');
  }
  add_action( 'wp_ajax_getSubCategories', 'getSubCategories' );

  function getSearchResults($list) {
    $result = [];
    foreach ($list as $ad) {
      $id = $ad->ID;
      $get_post_author = get_post($id);
      $name = get_user_meta($get_post_author->post_author ,'nickname' , true);
      $location = get_post_meta($id, 'cc_locations', true);
	    $address = explode("},{" , $location)[0];
      $address = preg_replace ("/[^0-9\s\,\.]/","", $address);
      $address = strip_tags(trim(get_address($address)));
      $state = get_post_meta($id, 'cc_state', true);

      $result[] = array(
        'title' => get_the_title($id),
        'img' => ad_thumbnail_url($id),
        'link' => get_permalink($id),
        'state' => $state,
        'address' => $address,
        'name' => $name,
        'price' => price_output($id),
        'location' => $location,
        'categories' => get_the_term_list($id, CUSTOM_CAT_TYPE, '', ' ', '')
      );
    }
    return $result;
  }
  // add_action( 'wp_ajax_getSearchResults', 'getSearchResults' );

  
  // Benefits info
  function benefits_info()
  {
    $benefits = array();
    $lang = ( pll_current_language() != 'uk' ) ? '-'.pll_current_language() : '';
    $benefits['slug'] = array('ecco','quality-control','free-delivery');
    $benefits['name'] = array('Ecco','Quality Control','Free Delivery');
	$benefits['num'] = count( $benefits['slug'] );
	for( $i=0; $i<$benefits['num']; $i++ )
      $benefits['url'][] = add_query_arg( 'lang', false, site_url( $benefits['slug'][$i].$lang ));
	
	return $benefits;
  }
  

/***
 * Add custom fields to profile page in admin
 *
 ***/
  function modify_contact_methods($profile_fields) {

	// Add new fields
	$profile_fields['phone'] = __( 'Phone', 'cc' );
	$profile_fields['skype'] = __( 'Skype', 'cc' );

	return $profile_fields;
  }
  add_filter('user_contactmethods', 'modify_contact_methods');

function pll_title($post_id=false) {
  if ( !$post_id ) {
    global $post;
    $post_id = $post->ID;
  }
  return get_the_title( $post_id );
}
  
/**
 * Cut ad title in grid view
 *
 */
  function title_excerpt ( $post_id=false ) {
    if ( !$post_id ) {
      global $post;
      $post_id = $post->ID;
    }
    mb_internal_encoding("UTF-8");
    $max_length = 20;
    $title = get_the_title( $post_id );
    $str = mb_substr($title, 0, $max_length);
    if (strlen($title) > $max_length) $str .= "...";
    return $str;
  }

  function content_excerpt ( $post_id=false) {
    if ( !$post_id ) {
      global $post;
      $post_id = $post->ID;
    }
    mb_internal_encoding("UTF-8");
    $max_length = 120;
    $content = strip_tags(get_the_content($post_id));
    $str = mb_substr($content, 0, $max_length);
    if (strlen($content) > $max_length) $str .= "...";
    return $str;
  }

  function price_output_clean( $post_id=false ) {
      if ( !$post_id ) {
      global $post;
      $post_id = $post->ID;
    }
    if( get_post_meta( $post_id, 'cc_price', true )) {
      $output = get_post_meta( $post_id, 'cc_price', true );
    }
    return $output;
  }

  function price_output( $post_id=false )
  {
    if ( !$post_id ) {
	  global $post;
	  $post_id = $post->ID;
  }
  
    if( get_post_meta( $post_id, 'cc_price', true )) {
      $output = get_post_meta( $post_id, 'cc_price', true ) . '&nbsp;<span class="hrn">грн</span><span class="day">день</span>';
    }
    else {
      $output = __('Price by agreement', 'prokkat');
    }	
    
	  return $output;
  }
  
   
/***
 * Number of ad phone views
 *
 */
  function getPhoneViews( $post_id=false ) {
    
	if ( !$post_id ) {
	  global $post;
	  $post_id = $post->ID;
	}
    $count_key = 'phone_views_count';
    $count = get_post_meta($post_id, $count_key, true);
    if ($count == '') {
        delete_post_meta($post_id, $count_key);
        add_post_meta($post_id, $count_key, '0');
        return "0";
    }
    return $count;
  }

  function setPhoneViews() {
	global $user_ID;
	
	$post_id = $_POST['post_id'];
	$author_id = get_post_field ('post_author', $post_id);
	if( $user_ID && $user_ID == $author_id ) return;

    $count_key = 'phone_views_count';
    $count = get_post_meta($post_id, $count_key, true);
    if ($count == '') {
        delete_post_meta($post_id, $count_key);
        add_post_meta($post_id, $count_key, '0');
    } else {
        $count++;
        update_post_meta($post_id, $count_key, $count);
    }
  }
  add_action('wp_ajax_phone_views', "setPhoneViews");
  add_action('wp_ajax_nopriv_phone_views', "setPhoneViews");

  // function sendFeedback() {
  //   global $user_ID;
    
  //   $post_id = $_POST['post_id'];
  //   $author_id = get_post_field ('post_author', $post_id);
  //   $text = $_POST['text'];

  //   $admin_email = get_option('admin_email');
  //   $subject = 'User feedback';
  //   $headers = array(
  //          'Content-Type: text/html; charset=UTF-8',
  //          'From: ' . $admin_email . "\r\n",
  //       );

  //   $message  = 'New user user feedback on your site' . "\r\n\r\n";
  //   $message .= sprintf(__('Post ID: %s'), $post_id) . "\r\n\r\n";
  //   $message .= sprintf(__('Used ID: %s'), $author_id) . "\r\n\r\n";
  //   $message .= sprintf(__('Reason: %s'), $text) . "\r\n";

  //   wp_mail($admin_email, $subject, $message, $headers);
  // }

  // add_action('wp_ajax_send_feedback', "sendFeedback");
  // add_action('wp_ajax_nopriv_send_feedback', "sendFeedback");
  

/**
 * Get url of main ad photo
 */
  function ad_thumbnail ( $post_id=0 ) {
	  if ( !$post_id ) {
		  global $post;
		  $post_id = $post->ID;
	  }
	  $img_url = ad_thumbnail_url( $post_id );
	  return '
      <div></div>
        <a href="'. get_post_permalink( $post_id, false, true ) .'" class="link product-item__title">      
	      <img src="'. $img_url .'" class="img-responsive" alt="product" />
        </a>';
  }
  
  function ad_thumbnail_url ( $post_id=0 ) {
	  if ( !$post_id ) {
		  global $post;
		  $post_id = $post->ID;
	  }
	  $img_url = get_post_meta( $post_id, 'img1', true );
	  if( $img_url && file_url_exists( $img_url )) {
		$src_info = pathinfo( $img_url );
        $img_resized = $src_info['dirname'].'/'.$src_info['filename']."_515X515.".$src_info['extension'];
	    $img_url = file_url_exists( $img_resized ) ? $img_resized : $img_url;
	  }
      else $img_url = get_stylesheet_directory_uri() . '/img/placeholder.jpg';
	  return $img_url;
  }
  
  function notification_markup( $text ) {
	  return '
	    <div class="notification mess-info mess-info_center">
            <div class="upload__control-img close-mess">
                <div class="upload__control-del ">
                    <a href="#0">
                            <svg fill="#FFFFFF" height="30" viewBox="0 0 24 24" width="30" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                <path d="M0 0h24v24H0z" fill="none"/>
                            </svg>
                    </a>
                </div>
            </div>
            '.$text.'
        </div>';
  }

/**
 * Updating listing type
 */
  function custom_upgrade_listing($post_id, $featured_home, $featured_cate ){
    update_post_meta($post_id, 'cc_add_type', 'pro');
	if( $featured_home ) update_post_meta( $post_id, 'cc_f_checkbox1', esc_attr( $featured_home ));
	if( $featured_cate ) update_post_meta( $post_id, 'cc_f_checkbox2', esc_attr( $featured_cate ));
  }
  
  
/***
 * Emails
 *
 */
  function custom_new_user_notification( $user_id, $random_password='' ) {
 
    global $wpdb, $wp_hasher;
    $user = get_userdata( $user_id );
 
    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
 
    $message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
    $message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
    $message .= sprintf(__('Email: %s'), $user->user_email) . "\r\n";
 
    //@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

    // Generate something random for a password reset key.
	if( !$random_password ) {
      $key = wp_generate_password( 20, false );
 
      /** This action is documented in wp-login.php */
      do_action( 'retrieve_password_key', $user->user_login, $key );
 
      // Now insert the key, hashed, into the DB.
      if ( empty( $wp_hasher ) ) {
        require_once ABSPATH . WPINC . '/class-phpass.php';
        $wp_hasher = new PasswordHash( 8, true );
      }
      $hashed = time() . ':' . $wp_hasher->HashPassword( $key );
      $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

	  $activation_url = network_site_url("activation/?key=$key&login=" . rawurlencode($user->user_login), 'login');
	}
	
	ob_start();
	include( get_stylesheet_directory() . '/email/index.php' );
	$message = ob_get_clean();
	
  $admin_email = get_option('admin_email');
  
	$headers = array(
	  'Content-Type: text/html; charset=UTF-8',
	  'From: ' . $blogname . ' <' . $admin_email . '>' . "\r\n",
  );
  wp_mail($user->user_email, __('Welcome to RentHUB!', 'prokkat'), $message, $headers);
  }

  
  add_action('init', 'expiry_check');
  function expiry_check()
  {
    global $wpdb;
    $post_type = POST_TYPE;
  
    //Expiry term for older and admin's listings.
  
    $listing_query = 'SELECT * FROM '.$wpdb->posts.' WHERE post_type = "'.$post_type.'" AND post_status = "publish"';
    $listing_result = $wpdb->get_results($listing_query);
    if (!empty($listing_result)) {
      foreach ($listing_result as $listing) {
		$post_id = $listing->ID;
        $is_expiry_set = get_post_meta($post_id, 'cc_listing_duration', true);
        /**
         * Check if ad expiry date is already set. 
         * If not set, it will set the expiry date based on current free package active period.
         */
        if (empty($is_expiry_set)) {
            cc_set_default_expiry($post_id);
        }
        //getting listing status
        $expire = custom_has_ad_expired($post_id);
        //if listing expired
        if ($expire == true) {
			$post_author = $listing->post_author;
			$to = get_the_author_meta('user_email', $post_author);
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$admin_email = get_option('admin_email');
			$subject = 'Your ad reactivation notice';
			$headers = array(
	           'Content-Type: text/html; charset=UTF-8',
	           'From: ' . $blogname . ' <' . $admin_email . '>' . "\r\n",
	        );
			
			$permalink = get_permalink( $post_id );
			$img_url = ad_thumbnail_url ( $post_id );
			$edit_link = add_query_arg( array( 'action'=>'edit', 'pid'=>$post_id ), site_url( 'dashboard/' ));
			$message =  'text';
			ob_start();
	        include( get_stylesheet_directory() . '/email/termin.php');
	        $message = ob_get_clean();

            wp_mail($to, $subject, $message, $headers);
        }
      }
    }
  }
  
  
  // change ad to draft if it's expired
  function custom_has_ad_expired($post_id) {
	  
    global $wpdb;
    $expire_date = get_post_meta($post_id, 'cc_listing_duration', true);

    // if current date is past the expires date, change post status to draft
    if ($expire_date) {
        if (strtotime(date('Y-m-d H:i:s')) > (strtotime($expire_date))) {
            wp_update_post( array('ID' => $post_id, 'post_status' => 'draft') );
            // After expired, listing will be set premium to free listing
            update_post_meta( $post_id, 'cc_add_type', 'free' );
			update_post_meta( $post_id, 'cc_f_checkbox1', '' );
			update_post_meta( $post_id, 'cc_f_checkbox2', '' );
            return true;
        }
    }
  }

  function custom_renew_listing($listing_id) {
    $renewal_period = gc_renewal_periond();
    if ($renewal_period > 0) {
        // set the ad ad expiration date
        $ad_expire_date = date_i18n('m/d/Y H:i:s', strtotime('+' . $renewal_period . ' days'));
        //now update the expiration date on the ad
        update_post_meta($listing_id, 'cc_listing_duration', $ad_expire_date);
        wp_update_post(array('ID' => $listing_id, 'post_date' => date('Y-m-d H:i:s'), 'edit_date' => true, 'post_status' => 'publish'));
		update_post_meta($listing_id, 'cc_add_type', 'free');
        return true;
    }
  }

  function rd_duplicate_post_as_draft(){
    global $wpdb;
    if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'rd_duplicate_post_as_draft' == $_REQUEST['action'] ) ) ) {
      wp_die('No post to duplicate has been supplied!');
    }
   
    /*
     * Nonce verification
     */
    if ( !isset( $_GET['duplicate_nonce'] ) || !wp_verify_nonce( $_GET['duplicate_nonce'], basename( __FILE__ ) ) )
      return;
   
    /*
     * get the original post id
     */
    $post_id = (isset($_GET['post']) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );
    /*
     * and all the original post data then
     */
    $post = get_post( $post_id );
   
    /*
     * if you don't want current user to be the new post author,
     * then change next couple of lines to this: $new_post_author = $post->post_author;
     */
    $current_user = wp_get_current_user();
    $new_post_author = $current_user->ID;
   
    /*
     * if post data exists, create the post duplicate
     */
    if (isset( $post ) && $post != null) {
   
      /*
       * new post data array
       */
      $args = array(
        'comment_status' => $post->comment_status,
        'ping_status'    => $post->ping_status,
        'post_author'    => $new_post_author,
        'post_content'   => $post->post_content,
        'post_excerpt'   => $post->post_excerpt,
        'post_name'      => $post->post_name,
        'post_parent'    => $post->post_parent,
        'post_password'  => $post->post_password,
        'post_status'    => 'draft',
        'post_title'     => $post->post_title,
        'post_type'      => $post->post_type,
        'to_ping'        => $post->to_ping,
        'menu_order'     => $post->menu_order
      );
   
      /*
       * insert the post by wp_insert_post() function
       */
      $new_post_id = wp_insert_post( $args );
   
      /*
       * get all current post terms ad set them to the new post draft
       */
      $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
      foreach ($taxonomies as $taxonomy) {
        $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
        wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
      }
   
      /*
       * duplicate all post meta just in two SQL queries
       */
      $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
      if (count($post_meta_infos)!=0) {
        $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
        foreach ($post_meta_infos as $meta_info) {
          $meta_key = $meta_info->meta_key;
          if( $meta_key == '_wp_old_slug' ) continue;
          $meta_value = addslashes($meta_info->meta_value);
          $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
        }
        $sql_query.= implode(" UNION ALL ", $sql_query_sel);
        $wpdb->query($sql_query);
      }
   
   
      /*
       * finally, redirect to the edit post screen for the new draft
       */
      wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
      exit;
    } else {
      wp_die('Post creation failed, could not find original post: ' . $post_id);
    }
    }
    add_action( 'admin_action_rd_duplicate_post_as_draft', 'rd_duplicate_post_as_draft' );
    
    /*
    * Add the duplicate link to action list for post_row_actions
    */
    function rd_duplicate_post_link( $postId ) {
      $url = '<a href="' . wp_nonce_url('admin.php?action=rd_duplicate_post_as_draft&post=' . $postId, basename(__FILE__), 'duplicate_nonce' ) . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
      return $url;
    }
    /*Функція виводить Назву колонки в табличці списку користувачів*/
    function column_name_admin_users( $column ) {
        $column['date'] = 'Дата регистрации';
        return $column;
    }
    
    add_filter( 'manage_users_columns', 'column_name_admin_users' );

    /*Функція виводить дату реестрації*/

    function column_date_registered( $val, $column_name, $user_id ) {
        switch ($column_name) {
            case 'date' :
                return get_the_author_meta( 'user_registered', $user_id);
              break;
            default:
        }
      return $val;
    }
    add_filter( 'manage_users_custom_column', 'column_date_registered', 10, 3 );

    /*Сортування по даті реєстраціїї*/

    function sortable_by_registered($sortable_columns){
        $sortable_columns['date'] = 'user_registered';

      return $sortable_columns;
    }
    add_filter('manage_users_sortable_columns', 'sortable_by_registered');
    /*Сортування по колонці дата для постів аренда*/

    add_filter( 'manage_edit-arenda_sortable_columns', 'login_sortable_column' );

    function login_sortable_column( $column ) {

      $column['author'] = 'login';

      return $column;
    }

    /*get town in category list*/
    function get_address($coordinates) {
        $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.$coordinates.'&sensor=false&language=ru';
        $json = file_get_contents($url);
        $data = json_decode($json, TRUE);
        if($data['status']=="OK"){
          $town = explode("," , $data['results'][0]['formatted_address']);
          $type = implode(' ' , $data['results'][0]['address_components'][0]['types']);
          if ($data['results'][0]['address_components'][2]['long_name'] == 'Київ' || $data['results'][0]['address_component'][2] == 'Киев'){
            return '<span class="town_ad">'.$town[1].',</span>'. $town[0].'</span>';
          }
          if (($data['results'][0]['address_components'][2]['long_name'] != 'Київ' || $data['results'][0]['address_component'][2] != 'Киев') &&  $type != 'route' && $type != 'premise') {
            return '<span class="town_ad">'.$town[2].',</span><span class="street">'. $town[0] . ' ' . $town[1].'</span>';
          }
          if($type == 'route'){
            return '<span class="town_ad">'.$town[1].',</span><span class="street">'. $town[0].'</span>';
          }
          if($type == 'premise'){
            return '<span class="town_ad">'.$town[3] . ',</span><span class="street">' . $town[2] . '  ' . $town[3].'</span>';
          }
        }
    }

    add_filter( 'slack_get_events', function( $events ) {
    $events['user_login'] = array(
        'action'      => 'wp_login',
        'description' => __( 'When user logged in', 'slack' ),
        'message'     => function( $user_login ) {
            return sprintf( '%s is logged in', $user_login );
        }
    );
    return $events;
} );

    remove_action( 'add_option_new_admin_email', 'update_option_new_admin_email' );
remove_action( 'update_option_new_admin_email', 'update_option_new_admin_email' );
 
function wpdocs_update_option_new_admin_email( $old_value, $value ) {
update_option( 'admin_email', $value );
}
add_action( 'add_option_new_admin_email', 'wpdocs_update_option_new_admin_email', 10, 2 );
add_action( 'update_option_new_admin_email', 'wpdocs_update_option_new_admin_email', 10, 2 );

function debug ($parameters){
    return '<pre>' . print_r($parameters , true) . '</pre>';
}

function get_tel($author_id){
    return get_the_author_meta('phone' , $author_id);
}
?>
