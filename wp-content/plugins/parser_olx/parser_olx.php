<?php
/**
 * Plugin Name: Парсер OLX
 * Description: Парсер данних с категорій OLX
 * Plugin URI:  https://ipnethost.kiev.ua/
 * Author URI:  https://ipnethost.kiev.ua/
 * Author:      Саша Александров
 * Version:     Версія плагіну, 1.0
 */
require __DIR__.'/functions.php';
require __DIR__.'/phpQuery.php';

if (!defined( 'ABSPATH' )){
	header('HTTP/1.0 403 Forbidden');
	exit('Запускати файли плагіну напряму  - не можна!.');
}

add_action('admin_menu', 'check_parser_admin');

function check_parser_admin(){
 	$page_olx = add_menu_page('Парсинг контенту з сайту OLX', 'Парсер OLX', 'manage_options', 'parser-page', 'parser_admin', plugins_url( 'parser_olx/images/parse.png'),4);
 	add_action( 'admin_print_scripts-' . $page_olx, 'parser_olx_script' );
}

function parser_olx_script () {
	wp_enqueue_script('jquery');
	wp_enqueue_script('parser_olx' , plugins_url('/js/parser_olx.js' , __FILE__));
	wp_enqueue_style('parser_olx' , plugins_url('/css/parser_olx.css' , __FILE__));
	wp_enqueue_style('bootstrap-min' , plugins_url('/css/bootstrap.min.css' , __FILE__));
}
