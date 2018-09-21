const apiUrl = 'http://chatsymfony/api';
const socketServer = 'http://localhost:3000/';
const socketScript = socketServer + 'socket.io/socket.io.js';
let thisScriptSrc = '';
let urlParams = {};
let user_id = null;
let temp_user_id = null;
const animationTime = 200;
let baseChat = "";
const marginWindow = 15;
let site = "";
const $ = require('jquery');
let allChatRooms = [];

$(document).on("click", "#id-web-site span.header, #id-web-site span.closeWindow", function () {
    let windowChat = $(this).parent().parent();
    $(windowChat).toggleClass("active");
    if ($(windowChat).hasClass("active")) {
        if ($(this).hasClass("closeWindow")) {
            $(windowChat).fadeOut(animationTime, function () {
                // Animation complete.
                $(windowChat).remove();
                changePositionWindowChat();
            });
        } else {
            $(windowChat).animate({
                bottom: "0px"
            }, animationTime, function () {
                // Animation complete.
            });
        }
    } else {
        $(windowChat).animate({
            bottom: "-302px"
        }, animationTime, function () {
            // Animation complete.
        });
        if ($(this).hasClass("closeWindow")) {
            $(windowChat).fadeOut(animationTime, function () {
                // Animation complete.
                $(windowChat).remove();
                changePositionWindowChat();
            });
        }
    }
});

$(document).on("submit", "#id-web-site div.chatWindow form", function (e) {
    e.preventDefault();
    const data = $(e.target).serializeArray();
    //quand c'est admin chat on envoie l'event avec le chat room id
    //quand c'est private chat on envoie au user
    //quand c'est private group on envoie au user
    //quand c'est public group on en au chat room id
    console.log(data);
    console.log(allChatRooms);
});

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
        const source_code = String(document.documentElement.outerHTML);
        const params = 'site_id=' + urlParams["id"] + '&source_code=' + source_code + '&url=' + window.location.href + '&thisScriptSrc=' + thisScriptSrc;
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

function getSocketScript(responseData) {
    loadScript(socketScript, function () {
        loadSocket(responseData);
    });
}

function errorHandler(statusCode) {
    console.log("failed with status", statusCode);
}

function getAdminChatRoom(user_id, temp_user_id, site, callback) {
    const xhr = new XMLHttpRequest();
    const params = 'site_id=' + site.id + '&temp_user_id=' + temp_user_id + '&user_id=' + user_id + '&chat_type=admin';
    xhr.open('POST', apiUrl + '/get-chat-room', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.send(params);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                const resp = xhr.responseText;
                const respJson = JSON.parse(resp);
                const adminChat = respJson.chatRoom;
                allChatRooms.push(adminChat);
                callback(adminChat);
            }
        }
    };
}

function changePositionWindowChat() {
    let newPositionRight = marginWindow;
    $(baseChat).children('div').each(function () {
        if ($(this).is(":visible")) {
            $(this).css("right", newPositionRight + "px");
            newPositionRight += $(this).width() + marginWindow;
        }
    });
}

function displayChatRoom(chatRoom) {
    let display = site.templateAdminChat.replace(new RegExp('%chat-room-id%', 'g'), chatRoom.id);
    $(baseChat).append(display);
}

function loadSocket(responseData) {
    site = responseData.site;
    let inAdminPanel = false;
    if (urlParams.adminpanel !== undefined && urlParams.adminpanel === "1") {
        inAdminPanel = true;
    }
    if ((urlParams.user_id === undefined || urlParams.user_id === "") && (urlParams.temp_user_id === undefined || urlParams.temp_user_id === "")) {
        if (inAdminPanel) {
            alert("user_id && temps_user_id undefined !");
        }
    } else {
        if (site.hasAdminChat === true || (site.hasAdminChat === false && inAdminPanel === true)) {
            //display admin chat
            if (urlParams.user_id === undefined || urlParams.user_id === "") {
                user_id = null;
            } else {
                user_id = urlParams.user_id;
            }
            if (urlParams.temp_user_id === undefined || urlParams.temp_user_id === "") {
                temp_user_id = null;
            } else {
                temp_user_id = urlParams.temp_user_id;
            }
            //toujours appeler le display admin chat room juste pour mettre à jour les users
            getAdminChatRoom(user_id, temp_user_id, site, function (adminChat) {
                manageChatWindow(site, adminChat, function () {
                    changePositionWindowChat();
                });
                $(function () {
                    // const socket = io(socketServer);
                });
            });

            function manageChatWindow(site, adminChat, callback) {
                $("body").prepend("<div id='id-web-site'></div>");
                baseChat = $("body").find("#id-web-site");
                $(baseChat).append(site.cssChat);
                if (urlParams.adminpanel === "1") {
                    if (urlParams.dashboardwindow === "admin-chat") {
                        if ((site.hasAdminChat && !site.hasPrivateChat) || (!site.hasAdminChat && !site.hasPrivateChat)) {
                            //on affiche que le admin chat
                            displayChatRoom(adminChat);
                            callback();
                        } else {
                            //on affiche les 2
                        }
                    } else if (urlParams.dashboardwindow === "private-chat") {
                        if (!site.hasAdminChat) {
                            //on affiche que le private chat
                        } else {
                            //on affiche les 2
                        }
                    } else {
                        alert("error! dashboardwindow is not defined");
                    }
                } else {
                    if (site.hasAdminChat && !site.hasPrivateChat) {
                        //on affiche que le admin chat
                        displayChatRoom(adminChat);
                        callback();
                    } else if (!site.hasAdminChat && site.hasPrivateChat) {
                        //on affiche que le private chat
                    } else if (site.hasAdminChat && site.hasPrivateChat) {
                        //on affiche les 2
                    } else {
                        //on fait rien
                    }
                }
            }
        }
    }
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