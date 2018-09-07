function getAllUrlParams(url) {
    let queryString = url ? url.split('?')[1] : window.location.search.slice(1);
    const obj = {};
    if (queryString) {
        queryString = queryString.split('#')[0];
        const arr = queryString.split('&');
        for (let i = 0; i < arr.length; i++) {
            const a = arr[i].split('=');
            let paramNum = undefined;
            let paramName = a[0].replace(/\[\d*\]/, function (v) {
                paramNum = v.slice(1, -1);
                return '';
            });
            let paramValue = typeof(a[1]) === 'undefined' ? true : a[1];
            paramName = paramName.toLowerCase();
            paramValue = paramValue.toLowerCase();
            if (obj[paramName]) {
                if (typeof obj[paramName] === 'string') {
                    obj[paramName] = [obj[paramName]];
                }
                if (typeof paramNum === 'undefined') {
                    obj[paramName].push(paramValue);
                }
                else {
                    obj[paramName][paramNum] = paramValue;
                }
            }
            else {
                obj[paramName] = paramValue;
            }
        }
    }
    return obj;
}

function invalidNumberOfScripts() {
    throw new Error("Invalid number of script");
}

document.addEventListener("DOMContentLoaded", function (event) {
    const scripts = document.querySelectorAll('[data-socket-chat]');
    let urlParams = {};
    if (scripts.length !== 1) {
        invalidNumberOfScripts();
    } else {
        scripts.forEach(function (element) {
            const url = element.getAttribute("src");
            urlParams = getAllUrlParams(url);
        });
        // Faire requete http pour voir si c'est installé

        console.log(urlParams);
    }
});