let flashMessage = require('./flash-message.js');
let loader = require('./loader.js');
require('jquery.redirect');

$("[data-ajax-call]").click(function (e) {
    e.preventDefault();
    loader.showLoader();
    const ajaxUrl = $(this).data("ajax-call");
    let redirect = null;
    if ($(this).data("ajax-href") !== undefined && $(this).data("ajax-href") !== null && $(this).data("ajax-href") !== "") {
        redirect = $(this).data("ajax-href");
    } else if ($(this).attr("href") !== undefined && $(this).attr("href") !== null && $(this).attr("href") !== "") {
        redirect = $(this).attr("href");
    }
    $.post(ajaxUrl, {redirect: redirect})
        .done(function (data) {
            const showAlert = function () {
                if (data.messages.length > 0) {
                    const createAlert = function () {
                        data.messages.forEach(function (message) {
                            let displayMessage = '<div class="alert alert-' + message.class + '" role="alert">\n' +
                                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
                                '<span aria-hidden="true">&times;</span>\n' +
                                '</button>\n';
                            if (message.title !== undefined && message.title !== null && message.title !== "") {
                                displayMessage += '<h4 class="alert-heading">' + message.title + '</h4>\n';
                            }
                            displayMessage += '<p>' + message.message + '</p>\n' +
                                '</div>';
                            $("#flash-messages").append(displayMessage);
                        })
                    };
                    $.when(createAlert()).done(function () {
                        flashMessage.displayFlashMessage();
                    });
                }
            };
            $.when(showAlert()).done(function () {
                if (redirect !== null && data.stopRedirect === false) {
                    if (data.timeRedirection !== null) {
                        setTimeout(function () {
                            $.redirect(data.redirect, {'messagesAfterRedirect': data.messagesAfterRedirect});
                        }, data.timeRedirection);
                    } else {
                        $.redirect(data.redirect, {'messagesAfterRedirect': data.messagesAfterRedirect});
                    }
                } else {
                    loader.hideLoader();
                }
            });
        });
});