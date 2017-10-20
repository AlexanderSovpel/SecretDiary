/**
 * Created by Alexander on 29.04.15.
 */
function showPasswordRestore() {
    var forgotBlock = document.querySelector('#password_restore');
    var loginBlock = document.querySelector('#container_login');
    if (forgotBlock.className == "hidden") {
        forgotBlock.className = "";
        loginBlock.className = "hidden";
    }
    else {
        forgotBlock.className = "hidden";
        loginBlock.className = "";
    }
}

var email = document.querySelector('#email_restore');
email.addEventListener('blur', function() {
    if (!validateEmail(email.value))
        error_p.innerHTML = "Введите корректный e-mail адрес";
    else error_p.innerHTML = "";
});

function restorePasswordStep1() {
    var restoreError = document.querySelector('#restore_error');

    var xmlhttp = getHttpRequest();

    var params = "action=restore1&" +
        "restore_email=" + email.value;

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            if (xmlhttp.responseText == "1") {
                location.href = "restore.html";
            }
            else {
                restoreError.innerHTML = "Данный пользователь не зарегистрирован в системе";
            }
        }
    }

    sendHttpRequest(xmlhttp, params);
}

function restorePasswordStep2() {
    var restoreCode = document.querySelector('#restore_code');
    var restoreError = document.querySelector('#restore_error');

    var xmlhttp = getHttpRequest();

    var params = "action=restore2&" +
        "restore_code=" + restoreCode.value;

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            if (xmlhttp.responseText == "1") {
                var step2 = document.querySelector('#step2');
                step2.className = "hidden";

                var step3 = document.querySelector('#step3');
                step3.className = "";
            }
            else {
                restoreError.innerHTML = "Неверный код подтверждения";
            }
        }
    }

    sendHttpRequest(xmlhttp, params);
}

function restorePasswordStep3() {
    var password = document.querySelector('#password');
    var passwordRepeat = document.querySelector('#password_repeat');
    var restoreError = document.querySelector('#restore_error');

    var xmlhttp = getHttpRequest();

    var params = "action=restore3&" +
        "password=" + password.value + "&" +
        "password_repeat=" + passwordRepeat.value;

    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseURL == "http://sdiary.pe.hu/php/Main.php") {
                restoreError.innerHTML = this.responseText;
            }
            else {
                location.href = this.responseURL;
            }
        }
    }

    sendHttpRequest(xmlhttp, params);
}
