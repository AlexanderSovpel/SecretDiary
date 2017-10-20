/**
 * Created by Alexander on 17.04.15.
 */
var notes_list = document.querySelector('#notes_list');
var notes = notes_list.children;
var current_id = document.querySelector('#current_id');
var current_title = document.querySelector('#current_title');
var current_text = document.querySelector('#current_text');
var current = document.querySelector('#current');

function loadNotes() {
    var xmlhttp = getHttpRequest();

    var params = "action=load_notes";

    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            notes_list.innerHTML = this.responseText;
            addEditNoteHandlers();
        }
    }

    sendHttpRequest(xmlhttp, params);
}

function addEditNoteHandlers() {
    for (var i = 0; i < notes.length; ++i) {
        notes[i].addEventListener("click", function(event) {
            if (current.className == 'no_current_note')
                current.className = 'current_note';

            current_id.value = event.currentTarget.children[0].value;
            current_title.value = event.currentTarget.children[2].innerHTML;
            current_text.value = event.currentTarget.children[3].innerHTML;
        });
    }
}