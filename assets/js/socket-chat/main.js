const apiUrl = 'http://chatsymfony/api';
const socketScript = 'http://localhost:3000/socket.io/socket.io.js';
const socketServer = 'http://localhost:3000/';
let thisScriptSrc = '';
let urlParams = {};
let user_id = null;

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

function sendWebsiteData(urlParams) {
    return new Promise(function (resolve, reject) {
        const xhr = new XMLHttpRequest();
        const params = 'site_id=' + urlParams["id"];
        xhr.open('POST', apiUrl + '/request', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.send(params);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    const resp = xhr.responseText;
                    const respJson = JSON.parse(resp);
                    resolve(respJson);
                } else {
                    reject(xhr.status);
                }
            }
        }
    });
}

function loadScript(url, callback) {
    const script = document.createElement("script");
    script.type = "text/javascript";
    if (script.readyState) {  //IE
        script.onreadystatechange = function () {
            if (script.readyState === "loaded" ||
                script.readyState === "complete") {
                script.onreadystatechange = null;
                callback();
            }
        };
    } else {  //Others
        script.onload = function () {
            callback();
        };
    }
    script.src = url;
    document.getElementsByTagName("head")[0].appendChild(script);
}

function installWebSite(site) {
    const xhr = new XMLHttpRequest();
    const source_code = String(document.documentElement.outerHTML);
    const params = 'site_id=' + site.id + '&source_code=' + source_code + '&url=' + window.location.href + '&thisScriptSrc=' + thisScriptSrc;
    xhr.open('POST', apiUrl + '/install', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.send(params);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                const resp = xhr.responseText;
            } else {
                const status = xhr.status;
            }
        }
    }
}

function getSocketScript(responseData) {
    loadScript(socketScript, function () {
        loadSocket(responseData);
    });
    const site = responseData.site;
    if (site.isOnline !== true || site.installed === false) {
        installWebSite(site);
    }
}

function errorHandler(statusCode) {
    console.log("failed with status", statusCode);
}

function loadSocket(responseData) {
    const site = responseData.site;
    let inAdminPanel = false;
    console.log(site);
    console.log(urlParams);
    if (urlParams.adminpanel !== undefined && urlParams.adminpanel === "1") {
        inAdminPanel = true;
    }
    $(function () {
        //commencer par afficher les public chat rooms
        if (site.hasAdminChat === true || (site.hasAdminChat === false && inAdminPanel === true)) {
            //display admin chat
            if (urlParams.user_id === undefined || urlParams.user_id === "") {
                user_id = null;
            } else {
                user_id = urlParams.user_id;
            }
            displayAdminChatRoom(user_id, site, function () {
                // const socket = io(socketServer);
            });
        }

        function displayAdminChatRoom(user_id, site, callback) {
            const xhr = new XMLHttpRequest();
            const params = 'site_id=' + site.id + '&user_id=' + user_id + '&chat_type=admin';
            xhr.open('POST', apiUrl + '/get-chat-room', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.send(params);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        const resp = xhr.responseText;
                        const respJson = JSON.parse(resp);
                        const adminChat = respJson.adminChatRoom;
                        console.log(adminChat);
                        // document.body.insertAdjacentHTML('beforeend', site.templateAdminChat);
                        // document.body.insertAdjacentHTML('beforeend', site.cssAdminChat);
                        callback();
                    }
                }
            };
        }
    });
}

document.addEventListener("DOMContentLoaded", function (event) {
    const scripts = document.querySelectorAll('[data-socket-chat]');
    if (scripts.length !== 1) {
        invalidNumberOfScripts();
    } else {
        scripts.forEach(function (element) {
            const url = element.getAttribute("src");
            thisScriptSrc = url;
            urlParams = getAllUrlParams(url);
        });
        sendWebsiteData(urlParams).then(getSocketScript, errorHandler);
    }
});