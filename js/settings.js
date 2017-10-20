/**
 * Created by Alexander on 18.04.15.
 */
var accountSettings = document.querySelector('#account_settings');
var notificationSettings = document.querySelector('#notification_settings');
var accountLi = document.querySelector('#account_li');
var notifLi = document.querySelector('#notif_li');

var email = document.querySelector('#email');
var username = document.getElementById('username');
var password = document.querySelector('#password');
var passwordRepeat = document.querySelector('#password_repeat');
var notificationTime = document.querySelector('#notification_time');

var error = document.querySelector('#error');

var xmlhttp = getHttpRequest();

var params = "action=user_info";

xmlhttp.onreadystatechange = function () {
    var notificationDays = document.getElementsByName('notification_day');

    if (this.readyState == 4 && this.status == 200) {
        var userInfo = this.responseText;
        var separatedInfo = userInfo.split('&');
        for (var i = 0; i < separatedInfo.length; ++i) {
            var userDetails = separatedInfo[i].split('=');

            switch(i) {
                case 0:
                    email.value = userDetails[1];
                    break;
                case 1:
                    username.value = userDetails[1];
                    document.title = userDetails[1] + "'s Diary";
                    break;
                case 2:
                    for (var j = 0; j < 24; ++j) {
                        var timeOption = document.createElement('option');
                        var hour = (j < 10) ? "0" + j : j;
                        timeOption.valueOf = j;
                        timeOption.innerHTML = "UTC " + hour + ":00";
                        notificationTime.appendChild(timeOption);

                        if (j == userDetails[1])
                            notificationTime.children[j].setAttribute('selected', 'selected');
                    }
                    break;
                case 3:
                    var selectedDays = userDetails[1].split(', ');
                    for (var k = 0; k < notificationDays.length; ++k) {
                        if (selectedDays.indexOf(notificationDays[k].value) >= 0) {
                            notificationDays[k].setAttribute('checked', 'checked');
                        }
                    }
                    break;
            }
        }
    }
}

sendHttpRequest(xmlhttp, params);


function saveSettingsChanges() {
    var notificationDays = document.getElementsByName('notification_day');

    params = "action=save_settings&" +
        "email=" + email.value + "&" +
        "username=" + username.value + "&" +
        "password=" + password.value + "&" +
        "password_repeat=" + passwordRepeat.value + "&" +
        "notification_time=";

    for (var i = 0; i < notificationTime.children.length; ++i) {
        if (notificationTime.children[i].getAttribute('selected'))
            params += notificationTime.children[i].valueOf + "&";
    }

    params += "notification_day=";
    var selectedDays = [];
    for (var i = 0; i < notificationDays.length; ++i) {
        if (notificationDays[i].checked) {
            selectedDays.push(notificationDays[i].value);
        }
    }
    params += selectedDays.join(', ').toString();

    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseText != "OK") {
                error.className = 'error';
                error.innerHTML = xmlhttp.responseText;
            }
            else {
                error.className = 'success';
                error.innerHTML = "Изменения успешно сохранены";
            }
        }
    }

    sendHttpRequest(xmlhttp, params);
}

function dismissSettingsChanges() {
    location.href = "../diary.html";
}

function showAccountSettings() {
    accountSettings.className = 'active';
    accountLi.className = 'active';

    notificationSettings.className = 'inactive';
    notifLi.className = 'inactive';
}

function showNotificationSettings() {
    accountSettings.className = 'inactive';
    accountLi.className = 'inactive';

    notificationSettings.className = 'active';
    notifLi.className = 'active';
}