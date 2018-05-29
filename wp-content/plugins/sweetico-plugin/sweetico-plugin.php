<?php
/*
Plugin Name: Sweetico Plugin
Description: Site specific code changes for sweetico.com
*/

// Disable notification for admin when users change passwords
if ( !function_exists( 'wp_password_change_notification' ) ) {
    function wp_password_change_notification() {}
}

// Disable some user notifications
add_filter( 'send_email_change_email', '__return_false' );
add_filter( 'send_password_change_email', '__return_false');


// Refuse plugins update - $DISABLE_UPDATE defined in wp-config.php
  function filter_plugin_updates( $update ) {    
    global $DISABLE_UPDATE;
    if( !is_array($DISABLE_UPDATE) || count($DISABLE_UPDATE) == 0 ){  return $update;  }
    foreach( $update->response as $name => $val ){
        foreach( $DISABLE_UPDATE as $plugin ){
            if( stripos($name,$plugin) !== false ){
                unset( $update->response[ $name ] );
            }
        }
    }
    return $update;
  }
  add_filter( 'site_transient_update_plugins', 'filter_plugin_updates' );

  
// Checks if user is logged in, if not redirect to home
  function auth_redirect_home() {
	$user = wp_get_current_user();
    if ($user->ID == 0) {
        nocache_headers();
        wp_redirect(home_url());
        exit();
    }
  }
  
  function file_url_exists( $file ) {
    $file_headers = @get_headers($file);
    if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
      return false;
    } else {
      return true;
    }
  }
  
/***
 * Generate a thumbnail on the fly
 *
 * @return thumbnail url
 ***/
  function get_thumb( $src_url='', $width=null, $height=null, $crop=true, $cached=true ) {

    if ( empty( $src_url ) ) throw new Exception('Invalid source URL');
    if ( empty( $width ) ) $width = get_option( 'thumbnail_size_w' );
    if ( empty( $height ) ) $height = get_option( 'thumbnail_size_h' );

    $src_info = pathinfo($src_url);

    $upload_info = wp_upload_dir();

    $upload_dir = $upload_info['basedir'];
    $upload_url = $upload_info['baseurl'];
    $thumb_name = $src_info['filename']."_".$width."X".$height.".".$src_info['extension'];

    if ( FALSE === strpos( $src_url, home_url() ) ){
      $source_path = $upload_info['path'].'/'.$src_info['basename'];
      $thumb_path = $upload_info['path'].'/'.$thumb_name;
      $thumb_url = $upload_info['url'].'/'.$thumb_name;
      if (!file_exists($source_path) && !copy($src_url, $source_path)) {
      throw new Exception('No permission on upload directory: '.$upload_info['path']);
    }

    }else{
    // define path of image
      $rel_path = str_replace( $upload_url, '', $src_url );
      $source_path = $upload_dir . $rel_path;
      $source_path_info = pathinfo($source_path);
      $thumb_path = $source_path_info['dirname'].'/'.$thumb_name;

      $thumb_rel_path = str_replace( $upload_dir, '', $thumb_path);
      $thumb_url = $upload_url . $thumb_rel_path;
    }

    if($cached && file_exists($thumb_path)) return $thumb_url;

    $editor = wp_get_image_editor( $source_path );
    $editor->resize( $width, $height, $crop );
    $new_image_info = $editor->save( $thumb_path );

    if(empty($new_image_info)) throw new Exception('Failed to create thumb: '.$thumb_path);

    return $thumb_url;
  }
  
/***
 * Delete images with all sizes
 *
 ***/
  function delete_images( $img ) {
	if( !$img || !file_url_exists( $img )) return;
	
	$url = parse_url($img);
    $site_url = parse_url(site_url());
    if( $url['host'] != $site_url['host'] )
		return;
	
	$img_path = preg_replace('#/+#','/', ABSPATH.$url['path']);
	$file = basename($img_path);
	$info = pathinfo($file);
	$file_name =  basename($file,'.'.$info['extension']);
	$glob = dirname($img_path)."/". $file_name ."_*.*";
	$files = glob($glob);
    foreach ($files as $file){
	  unlink( $file );
	}
	unlink( $img_path );
  }
  
?>