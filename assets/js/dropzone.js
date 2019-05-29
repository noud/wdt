import $ from 'jquery';

import 'dropzone/dist/basic.css';
import 'dropzone/dist/dropzone.css';
import Dropzone from 'dropzone';

const maxUploadFileSize = 1024 * 1024 * 20; // not more than 20mb

Dropzone.autoDiscover = false;

$(document).ready(function() {
    const urlPost = $('#urls').data('attachment-post');
    const csrfPost = $('#urls').data('attachment-post-csrf');
    const urlRemove = $('#urls').data('attachment-remove');
    const csrfRemove = $('#urls').data('attachment-remove-csrf');

	const frm = $('.attachment-remove');
	
	frm.submit(function (e) {
		let message = frm.attr('data-message');
		var status = confirm(message);
		if(status == false){
			e.preventDefault();
		}
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
		  formData.append("ticket_attachment_edit[filename]", file.name);
		  formData.append("ticket_attachment_edit[filesize]", file.size);
		  formData.append("ticket_attachment_edit[_token]", csrfPost);
		  formData.append("ticket_attachment_edit[file]", file);
		  formData.delete("file");
		});
	myDropzone.on("removedfile", function(file, xhr, formData) {
	    $.ajax({
	        type: 'POST',
	        url: urlRemove,
	        data: {
	        	'attachment_remove_edit[filename]': file.name,
	        	'attachment_remove_edit[_token]': csrfRemove
	        },
	    });
	});
	myDropzone.on("addedfile", function(file) {
		if(file.size > maxUploadFileSize)
		{
			this.removeFile(file); // if you want to remove the file or you can add alert or presentation of a message
		}
	});
});