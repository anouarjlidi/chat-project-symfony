$(document).ready(function () {
    $("#selectLocale .dropdown-item").click(function () {
        const $selectedLocale = $(this).data('value');
        const $currentLocale = $('html')[0].lang;
        if ($currentLocale !== $selectedLocale) {
            const $url = $(this).data('href');
            window.location.replace($url);
        }
    });
});
