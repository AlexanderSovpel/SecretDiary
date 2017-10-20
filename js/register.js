/**
 * Created by Alexander on 17.04.15.
 */
/**
 * Created by Alexander on 17.04.15.
 */
var error_p = document.querySelector('#error');
var name = document.querySelector('#username');

var email = document.querySelector('#email');
email.addEventListener('blur', function() {
    if (!validateEmail(email.value))
        error_p.innerHTML = "Введите корректный e-mail адрес";
});

var password = document.querySelector('#password');
password.addEventListener('blur', function() {
    if (!validatePassword(password.value))
        error_p.innerHTML = "Пароль должен содержать от 6 до 14 символов";
});

var passwordRepeat = document.querySelector('#password_repeat');
passwordRepeat.addEventListener('blur', function() {
    if (passwordRepeat.value != password.value)
        error_p.innerHTML = "Введённые пароли не совпадают";
});


function register() {
    var xmlhttp = getHttpRequest();

    var params = "action=register&" +
        "name=" + name.value + "&" +
        "register_email=" + email.value + "&" +
        "password=" + password.value + "&" +
        "password_repeat=" + passwordRepeat.value;

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