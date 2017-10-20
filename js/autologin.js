/**
 * Created by Alexander on 18.04.15.
 */
function autologin() {
    var xmlhttp = getHttpRequest();

    var params = "action=auto_login";

    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseURL != "http://sdiary.pe.hu/php/Main.php") {
                location.href = this.responseURL;
            }
        }
    }

    sendHttpRequest(xmlhttp, params);
}