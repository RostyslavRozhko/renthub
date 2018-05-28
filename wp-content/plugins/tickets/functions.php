<?php

/* Функція для обробки даних вибраних з бази даних */
function getTicketsData() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'tickets';

	$ticket_rows = $wpdb->get_results( "SELECT * FROM $table_name" );

	/* Формуємо масив для вибірки варіантів статусу */
	$ticket_status = array(
		0 => array(
			"label" => "Не опрацьовано",
			"color" => "warning"
		),
		1 => array(
			"label" => "Опрацьовано",
			"color" => "success"
		),
	);

	/* Присвоюємо нашій табличці значення */
	foreach ( $ticket_rows as $ticket_row ) {
		$current_ticket_status           = $ticket_status[ $ticket_row->ticket_status ];
		$color                           = $current_ticket_status['color'];
		$label                           = $current_ticket_status['label'];
		$ticket_row->post_url            = "<a href=" . get_permalink( $ticket_row->post_id ) . " target=\"_blank\" >" . get_the_title( $ticket_row->post_id ) . "</a>";
		$ticket_row->author_url          = "<a href=" . get_author_posts_url( $ticket_row->author_id ) . " target=\"_blank\" >" . get_the_author_meta( "login", $ticket_row->author_id ) . "</a>";
		$ticket_row->ticket_status_badge = "<span class=\"badge badge-" . $color . "\">" . $label . "</span>";;
	}


	wp_send_json( $ticket_rows );
}

add_action( 'wp_ajax_get_tickets_data', "getTicketsData" );


/* Функція для видалення рядка з таблички */
function deleteTicket() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'tickets';
	$ticket_id  = $_POST['ticket_id'];
	$wpdb->delete( $table_name, array( 'ticket_id' => $ticket_id ) );
}

/*  */
add_action( 'wp_ajax_delete_ticket', "deleteTicket" );


/* Функція для оновлення статусу у табличці */
function updateTicketStatusCode() {
	global $wpdb;
	$table_name             = $wpdb->prefix . 'tickets';
	$ticket_id              = $_POST['ticket_id'];
	$new_ticket_status_code = $_POST['new_ticket_status_code'];

	$wpdb->update( $table_name, array( 'ticket_status' => $new_ticket_status_code ), array( 'ticket_id' => $ticket_id ) );
}


/*  */
add_action( 'wp_ajax_update_ticket_status_code', "updateTicketStatusCode" );


/* Функція для вставки надійденого запиту у базу даних та надсилання повідомлення на пошту */
function sendFeedback() {
	global $wpdb;
	global $user_ID;

	$table_name = $wpdb->prefix . 'tickets';

	$post_id        = $_POST['post_id'];
	$author_id      = get_post_field( 'post_author', $post_id );
	$ticket_message = $_POST['text'];

	$wpdb->insert(
		$table_name,
		array(
			'author_id'      => $author_id,
			'post_id'        => $post_id,
			'ticket_message' => $ticket_message,
		)
	);
    
    $post_id = $_POST['post_id'];
    $author_id = get_post_field ('post_author', $post_id);
    $text = $_POST['text'];

    $admin_email = get_option('admin_email');

    $subject = 'User feedback';
    $headers = array(
           'Content-Type: text/html; charset=UTF-8',
           'From: ' . $admin_email . "\r\n",
        );

    $message  = 'New user user feedback on your site' . "\r\n\r\n";
    $message .= sprintf(__('Post ID: %s'), $post_id) . "\r\n\r\n";
    $message .= sprintf(__('Used ID: %s'), $author_id) . "\r\n\r\n";
    $message .= sprintf(__('Reason: %s'), $text) . "\r\n";

    wp_mail($admin_email, $subject, $message, $headers);

}


/* Для зареєстрованих і незареєстрованих користувачів при виконанні скрипта send_feedback виконуємо функцію sendFeedback*/
add_action( 'wp_ajax_send_feedback', "sendFeedback" );
add_action( 'wp_ajax_nopriv_send_feedback', "sendFeedback" );
