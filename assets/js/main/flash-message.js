function displayFlashMessage() {
    $('#flash-messages').children('div.alert').each(function () {
        $(this).slideDown(200, function () {
            // Animation complete.
        });
    });
}

$(document).ready(function () {
    displayFlashMessage();
});

module.exports = {
    displayFlashMessage
};