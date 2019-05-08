import 'dropzone/dist/basic.css';
import 'dropzone/dist/dropzone.css';
import Dropzone from 'dropzone';

let endpoint = $("#uploader").data('endpoint');
let uploadFormId = $("#uploader").data('upload-form-id');
let attachmentRemove =  $("#attachment_remove").data('attachment_remove');

Dropzone.autoDiscover = false;

const maxUploadFiles = 30;
const maxFileSize = 1024 * 1024 * 20; // not more than 20mb

const myDropzone = new Dropzone("#my-dropzone", {
    url: endpoint + "?uploadFormId=" + uploadFormId,
    dictDefaultMessage: 'Sleep uw bijlage(n) om te uploaden.',
    dictRemoveFile: 'Verwijder bijlage',
    dictCancelUpload: 'Stop toevoeging',
    dictCancelUploadConfirmation: 'Weet u zeker dat u deze bijlage wilt verwijderen?',
});
myDropzone.on("addedfile", function(file) {
    if(file.size > maxFileSize)
    {
        this.removeFile(file); // if you want to remove the file or you can add alert or presentation of a message
    }
});
myDropzone.on("sending", function(file, xhr, formData) {
  formData.append("filename", file.name);
});

myDropzone.on("success", function (file, response) {
    if (response['target_file'] != '') {
        const currentValue = jQuery('#ticket_add_attachments').val();
        if (currentValue == '') {
            jQuery('#ticket_add_attachments').val(response['file_name'] + '|' + response['target_file'] + '|' + response['target_size'] + '|' + response['target_url']);
        } else {
            jQuery('#ticket_add_attachments').val(currentValue + ", " + response['file_name'] + '|' + response['target_file'] + '|' + response['target_size'] + '|' + response['target_url']);
        }
    }
});

myDropzone.options.addRemoveLinks=true;
myDropzone.on("removedfile", function (file) {
    // if not yet saved file.xhr.response contains a json with the unique_upload_id
    if (typeof file.xhr != 'undefined') {
        const obj = JSON.parse(file.xhr.response);
        const id = null;
        const uniqueUploadId = obj.unique_upload_id;
    } else {
        const id = file.id;
        const uniqueUploadId = null;
    }

    const name = file.name;
    $.ajax({
        type: 'POST',
        url: attachmentRemove,
        data: {'removeAttachment[name]': name, 'removeAttachment[id]': id, 'removeAttachment[uploadFormId]': uploadFormId, 'removeAttachment[uniqueUploadId]': uniqueUploadId},
        success: function (data) {
        	const currentValue = jQuery('#ticket_add_attachments').val();
            let myarr = currentValue.split(", ");
            for (let i in myarr) {
                if (myarr[i].indexOf(data.filename) > -1) {
                    myarr.splice(i, 1);
                }
            }
            let newValue = myarr.join(", ");
            jQuery('#ticket_add_attachments').val(newValue);
        }
    });
});
myDropzone.options.maxFiles = maxUploadFiles;

const currentValue = jQuery('#ticket_add_attachments').val();
if (currentValue.length > 0) {

    let currentArray = currentValue.split(", ");
    currentArray.forEach(function (element) {
        const fileArray = element.split("|");

        const fileName = fileArray[0];
        fileName = fileName.split("/");
        fileName = fileName[fileName.length - 1];

        // Create the mock file:
        const mockFile = {name: fileName, size: fileArray[1], id: fileArray[2].split('/').slice(-1)[0]};

        // Call the default addedfile event handler
        myDropzone.emit("addedfile", mockFile);

        // And optionally show the thumbnail of the file:
        if (ContainsAny(fileName.toLowerCase(), ["jpg", "jpeg", "png", "gif"])) {
            myDropzone.emit("thumbnail", mockFile, fileArray[2]);
        }

        // Make sure that there is no progress bar, etc...
        myDropzone.emit("complete", mockFile);

        // If you use the maxFiles option, make sure you adjust it to the
        // correct amount:
        const existingFileCount = 1; // The number of files already uploaded
        myDropzone.options.maxFiles = myDropzone.options.maxFiles - existingFileCount;
    });
};

function ContainsAny(str, items) {
    for (let i in items) {
        let item = items[i];
        if (str.indexOf(item) > -1) {
            return true;
        }

    }
    return false;
}