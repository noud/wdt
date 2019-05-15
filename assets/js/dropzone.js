import $ from 'jquery';

import 'dropzone/dist/basic.css';
import 'dropzone/dist/dropzone.css';
import Dropzone from 'dropzone';

const maxUploadFileSize = 1024 * 1024 * 20; // not more than 20mb

Dropzone.autoDiscover = false;

$(document).ready(function() {
    const urlPost = document.getElementById('urls').getAttribute('data-attachment-post');
    const urlRemove = document.getElementById('urls').getAttribute('data-attachment-remove');
    
	const frm = $('#quotation');
	
	frm.submit(function (e) {
	    e.preventDefault();
	
	    $.ajax({
	        type: frm.attr('method'),
	        url: frm.attr('action'),
	        data: frm.serialize(),
	    });
	});
	const myDropzone = new Dropzone(
		"div#ticket-dropzone",
		{
			url: urlPost,
			addRemoveLinks: true,
		    dictDefaultMessage: 'Sleep uw bijlage(n) om te uploaden.',
		    dictRemoveFile: 'Verwijder bijlage',
		    dictCancelUpload: 'Stop toevoeging',
		    dictCancelUploadConfirmation: 'Weet u zeker dat u deze bijlage wilt verwijderen?',
		}
	);
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
		if(file.size > maxUploadFileSize)
		{
			this.removeFile(file); // if you want to remove the file or you can add alert or presentation of a message
		}
	});
});