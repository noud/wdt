import $ from 'jquery';
import Dropzone from 'dropzone';

$(document).ready(function() {
    let urlPost = document.getElementById('urls').getAttribute('data-attachment-post');
    let urlRemove = document.getElementById('urls').getAttribute('data-attachment-remove');
    
	var frm = $('#quotation');
	
	frm.submit(function (e) {
	    e.preventDefault();
	
	    $.ajax({
	        type: frm.attr('method'),
	        url: frm.attr('action'),
	        data: frm.serialize(),
	    });
	});
	var myDropzone = new Dropzone("div#ticket-dropzone", { url: urlPost, addRemoveLinks: true});
	myDropzone.on("sending", function(file, xhr, formData) {
		  formData.append("filename", file.name);
		  formData.append("filesize", file.size);
		});
	myDropzone.on("removedfile", function(file, xhr, formData) {
	    $.ajax({
	        type: 'POST',
	        url: urlRemove,
	        data: {'filename': file.name},
	    });
	});
	myDropzone.on("addedfile", function(file) {
		if(file.size > (1024 * 1024 * 20)) // not more than 20mb
		{
			this.removeFile(file); // if you want to remove the file or you can add alert or presentation of a message
		}
	});
});
