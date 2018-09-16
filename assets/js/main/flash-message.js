function displayFlashMessage() {
    $('#flash-messages').children('div.alert').each(function () {
        $(this).slideDown(200, function () {
            // Animation complete.
        });
        $(this).css('display', 'inline-block')
    });
}

$(document).ready(function () {
    displayFlashMessage();
});

$('div.alert').on('close.bs.alert', function (e) {
    e.preventDefault();
    $(this).slideUp(200, function () {
        // Animation complete.
    });
});

module.exports = {
    displayFlashMessage
};