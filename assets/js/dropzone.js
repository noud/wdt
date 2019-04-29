import 'dropzone/dist/basic.css';
import 'dropzone/dist/dropzone.css';
import Dropzone from 'dropzone';


let method = $("#method").data('method');
let endpoint = $("#uploader").data('endpoint');
let uploadFormId = $("#uploader").data('upload-form-id');
let attachmentRemove =  $("#attachment_remove").data('attachment_remove');

        Dropzone.autoDiscover = false;

        var myDropzone = new Dropzone("#my-dropzone", {
            url: endpoint + "?uploadFormId=" + uploadFormId,
            dictDefaultMessage: 'Sleep uw bijlage(n) om te uploaden.',
            dictRemoveFile: 'Verwijder bijlage.',
            dictCancelUpload: 'Stop toevoeging',
            dictCancelUploadConfirmation: 'Weet u zeker dat u deze bijlage wilt verwijderen?',
        });
    	myDropzone.on("addedfile", function(file) {
    		if(file.size > (1024 * 1024 * 20)) // not more than 20mb
    		{
    			this.removeFile(file); // if you want to remove the file or you can add alert or presentation of a message
    		}
    	});

        myDropzone.on("success", function (file, response) {
            if (response['target_file'] != '') {
                var currentValue = jQuery('#article_' + method + '_form_attachments').val();
                if (currentValue == '') {
                    jQuery('#article_' + method + '_form_attachments').val(response['target_file'] + '|' + response['target_size'] + '|' + response['target_url']);
                } else {
                    jQuery('#article_' + method + '_form_attachments').val(currentValue + ", " + response['target_file'] + '|' + response['target_size'] + '|' + response['target_url']);
                }
            }
        });

        myDropzone.options.addRemoveLinks=true;
        myDropzone.on("removedfile", function (file) {
            // if not yet saved file.xhr.response contains a json with the unique_upload_id
            if (typeof file.xhr != 'undefined') {
                var obj = JSON.parse(file.xhr.response);
                var id = null;
                var uniqueUploadId = obj.unique_upload_id;
            } else {
                var id = file.id;
                var uniqueUploadId = null;
            }

            var name = file.name;
            $.ajax({
                type: 'POST',
                url: attachmentRemove,
                data: {'removeAttachment[name]': name, 'removeAttachment[id]': id, 'removeAttachment[uploadFormId]': uploadFormId, 'removeAttachment[uniqueUploadId]': uniqueUploadId},
                success: function (data) {
                    var currentValue = jQuery('#article_' + method + '_form_attachments').val();
                    var myarr = currentValue.split(", ");
                    for (var i in myarr) {
                        if (myarr[i].indexOf(data.filename) > -1) {
                            myarr.splice(i, 1);
                        }
                    }
                    var newValue = myarr.join(", ");
                    jQuery('#article_' + method + '_form_attachments').val(newValue);
                }
            });
        });
        myDropzone.options.maxFiles = 30;

        var currentValue = jQuery('#article_' + method + '_form_attachments').val();
        if (currentValue.length > 0) {

            var currentArray = currentValue.split(", ");
            currentArray.forEach(function (element) {
                var fileArray = element.split("|");

                var fileName = fileArray[0];
                fileName = fileName.split("/");
                fileName = fileName[fileName.length - 1];

                // Create the mock file:
                var mockFile = {name: fileName, size: fileArray[1], id: fileArray[2].split('/').slice(-1)[0]};

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
                var existingFileCount = 1; // The number of files already uploaded
                myDropzone.options.maxFiles = myDropzone.options.maxFiles - existingFileCount;
            });
        };

        function ContainsAny(str, items) {
            for (var i in items) {
                var item = items[i];
                if (str.indexOf(item) > -1) {
                    return true;
                }

            }
            return false;
        }