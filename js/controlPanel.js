/**
 * Created by Alexander on 16.04.15.
 */
var xmlhttp = getHttpRequest();
var params = "action=get_name";

xmlhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
        document.title = this.responseText + "'s Diary";
    }
}

sendHttpRequest(xmlhttp, params);

function addNewNote () {
    var current = document.querySelector('#current');
    current.className = 'current_note';

    current_id.value = '';
    current_title.value = "";
    current_text.value = "";
}

var search_button = document.querySelector('#search');
search_button.addEventListener('click', function () {
    var search_field = document.querySelector('#search_field');
    if (search_field.className == "hidden")
        search_field.className = "shown";
    else {
        search_field.className = "hidden";
    }
});

function changeSettings() {
    location.href = "../settings.html";
}

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

function toNotes() {
    location.href = "../diary.html";
}
