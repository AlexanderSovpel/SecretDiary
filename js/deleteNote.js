/** * Created by Alexander on 17.04.15. */var note_id = document.querySelector('#current_id');var note_title = document.querySelector('#current_title');var note_text = document.querySelector('#current_text');function deleteNote() {    var xmlhttp = getHttpRequest();    var params = "action=delete_note&" +        "current_id=" + note_id.value;    xmlhttp.onreadystatechange = function () {        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {            loadNotes();            note_id.value = "";            note_title.value = "";            note_text.value = "";        }    }    sendHttpRequest(xmlhttp, params);}