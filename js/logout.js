/**
 * Created by Alexander on 24.04.15.
 */
var logout_button = document.querySelector('#logout');
logout_button.addEventListener('click', logout());

function logout() {
    var xmlhttp = getHttpRequest();

    var params = "action=logout";

    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            location.href = "../index.html";
        }
    }

    sendHttpRequest(xmlhttp, params);
}