$(document).ready(function () {
    /* Підключаємо стилі для вспливаючих вікон */
    PNotify.prototype.options.styling = "bootstrap3";
    PNotify.prototype.options.styling = "fontawesome";

    /* Створюємо кнопки для зміни статусу і видалення запиту */
    var action_buttons = '<button class="btn btn-info btn-sm dropdown-toggle" type="button" id="ticketStatusSelector" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
        '    Статус' +
        '  </button>' +
        '  <div class="dropdown-menu" aria-labelledby="ticketStatusSelector" id="ticketStatusSelectorOptions">' +
        '    <a class="dropdown-item" id="solvedButton" value="1">Опрацьовано</a>' +
        '    <a class="dropdown-item" id="notsolvedButton" value="0">Не опрацьовано</a>' +
        '  </div>' +
        '<button type="button" class="btn btn-sm btn-danger" id="deleteTicketButton">Видалити</button>';


    /* Викорисовуючи datatables створюємо табличку і присвоюємо індекси полям*/
    var table = $('#tickets-table').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Ukrainian.json"
        },
        ajax: {
            method: "POST",
            url: ajaxurl,
            data: {action: 'get_tickets_data'},
            dataSrc: ""
        },
        columns: [
            {title: 'ID', data: 'ticket_id'},
            {title: 'Статус', data: 'ticket_status_badge'},
            {title: 'Автор', data: 'author_url'},
            {title: 'Назва товару', data: 'post_url'},
            {title: 'Скарга', data: 'ticket_message'},
            {title: 'Дата', data: 'ticket_registered'},
            { title: 'Операції', defaultContent: action_buttons, orderable: false, width: "200px" }
        ]
    });


    /* Обробляємо скрипт натискання на кнопку видалення запиту */
    /* Виводимо вспливаючі вікна для підтвердження виконання дії */
    $('#tickets-table tbody').on('click', '#deleteTicketButton',  function () {
        var data = table.row($(this).parents('tr')).data();
         $.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: "delete_ticket",
                ticket_id: data.ticket_id
            }
        })
            .done(function () {
                   console.log("delete done");
                   table.ajax.reload();

                new PNotify({
                    title: 'Виконано!',
                    text: 'Запис успішно видалено',
                    type: 'success'
                });
            })
            .fail(function () {
                new PNotify({
                    title: 'Помилка!',
                    text: 'Виникла помилка в процесі обробки запиту',
                    type: 'error'
                });
            })
    });


    /* Обробляємо скрипт натискання на кнопку зміни статусу запиту */
    /* Виводимо вспливаючі вікна для підтвердження виконання дії */
    $('#tickets-table tbody').on('click', '#ticketStatusSelectorOptions a', function () {
        console.log(this);
        var new_ticket_status_code = $(this).attr("value");
        var data = table.row($(this).parents('tr')).data();

       $.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: "update_ticket_status_code",
                ticket_id: data.ticket_id,
                new_ticket_status_code: new_ticket_status_code
            }
        })
            .done(function () {
                console.log("update done");
                table.ajax.reload();
                new PNotify({
                    title: 'Виконано!',
                    text: 'Статус успішно оновлено',
                    type: 'success'
                });
            })
            .fail(function () {
                new PNotify({
                    title: 'Помилка!',
                    text: 'Виникла помилка в процесі обробки запиту',
                    type: 'error'
                });
            })
    });
});