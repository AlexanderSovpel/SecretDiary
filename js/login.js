/**
 * Created by Alexander on 17.04.15.
 */
var error_p = document.querySelector('#error');
var remember = document.querySelector('#remember');

var email = document.getElementById('email');

var password = document.querySelector('#password');
password.addEventListener('blur', function() {
   if (!validatePassword(password.value))
       error_p.innerHTML = "Пароль должен содержать от 6 до 14 символов";
   else error_p.innerHTML = "";
});

function login() {
    var email = document.getElementById('email');

    var xmlhttp = getHttpRequest();

    var params = "action=login&" +
        "login_email=" + email.value + "&" +
        "password=" + password.value + "&" +
        "remember=";
    if (remember.getAttribute('checked'))
        params += 'checked';

    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseURL == "http://sdiary.pe.hu/php/Main.php") {
                error_p.innerHTML = this.responseText;
            }
            else {
                location.href = this.responseURL;
            }
        }
    }

    sendHttpRequest(xmlhttp, params);
}