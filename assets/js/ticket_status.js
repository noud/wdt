import $ from 'jquery';

$(document).ready(function() {
    document.getElementById('ticket_status_status').addEventListener("change", function(evt){
    	document.getElementById('ticket_status').submit();
    }, true);
});