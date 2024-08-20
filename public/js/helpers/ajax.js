$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'accept': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

const httpColors = new Map([
    [200, '#67B98D'],
    [201, '#67B98D'],
    [401, '#dc3545'],
    [403, '#dc3545'],
    [404, '#6c757d'],
    [422, '#343a40'],
    [500, '#F8D74B']
]);

function api(url) {
    return '/api/' + url;
}

function view(url) {
    return '/view/' + url;
}

function url(uri) {
    return '' + uri;
}

function showResponseModal(response, message, timeout) {

    const status = response.status;
    message ||= response.responseJSON.message;
    if (timeout === 'undefined') {
        timeout = -1;
    }

    if (message === null || message === undefined || message === "") {
        message = response.statusText;
    }

    const statusModal = new bootstrap.Modal('#statusModal', {});
    const modalDocument = $('#statusModal');
    modalDocument.find('.response-message').text(message);
    modalDocument.find('.modal-content').css('background-color', httpColors.get(status));
    statusModal.show();

    if (typeof timeout == 'number' && timeout >= 0) {
        setTimeout(() => (statusModal.hide(), timeout));
    }
}

function hideResponseModal() {
    // const statusModal = bootstrap.Modal('#statusModal');
    $('#statusModal').modal('hide');
}

function dataURItoBlob(dataURI) {
    // convert base64/URLEncoded data component to raw binary data held in a string
    var byteString;
    if (dataURI.split(',')[0].indexOf('base64') >= 0)
        byteString = atob(dataURI.split(',')[1]);
    else
        byteString = unescape(dataURI.split(',')[1]);

    // separate out the mime component
    var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

    // write the bytes of the string to a typed array
    var ia = new Uint8Array(byteString.length);
    for (var i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
    }

    return new Blob([ia], { type: mimeString });
}

// function appendToFormData(formData, data, key)
// {
// 	for (var key in data) {
// 	    if(data[key] != null || data[key] != undefined || (Array.isArray(data[key]) && data[key].length > 0)){
//             if (Array.isArray(data[key]))
//             {
//                 data.append(key, JSON.stringify(data[key]));
//                 appendToFormData(formData)
//             }
//             else
// 		        data.append(key, data[key]);
// 	    }
// 	}
//     return formData;
// }

function appendToFormData(formData, data, previousKey) {
    if (data instanceof Object) {
        Object.keys(data).forEach(key => {
            const value = data[key];
            if (value instanceof Object && !Array.isArray(value)) {
                return appendToFormData(formData, value, key);
            }
            if (previousKey) {
                key = `${previousKey}[${key}]`;
            }
            if (value != null || value != undefined) {
                if (Array.isArray(value)) {
                    value.forEach(val => {
                        formData.append(`${key}[]`, val);
                    });
                } else {
                    formData.append(key, value);
                }
            }
        });
    }
}

var BASE64_MARKER = ';base64,';

function convertDataURIToBinary(dataURI) {
    var base64Index = dataURI.indexOf(BASE64_MARKER) + BASE64_MARKER.length;
    var base64 = dataURI.substring(base64Index);
    var raw = window.atob(base64);
    var rawLength = raw.length;
    var array = new Uint8Array(new ArrayBuffer(rawLength));

    for (var i = 0; i < rawLength; i++) {
        array[i] = raw.charCodeAt(i);
    }
    return array;
}

//**dataURL to blob**
function dataURLtoBlob(dataurl) {
    var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new Blob([u8arr], { type: mime });
}

//**blob to dataURL**
function blobToDataURL(blob, callback) {
    var a = new FileReader();
    a.onload = function (e) { callback(e.target.result); }
    a.readAsDataURL(blob);
}

function getUploadCard(url, container, callback)
{
    container ||= '#uploadModal .modal-body';
    callback ||= () => {};
    url ||= '';

    // const url = id == '' ? view(`project/portfolio/upload`) : view(`project/portfolio/upload/${id}`);

    const cardContainer = $(container);
    // Retrieve view list
    $.ajax({
        type: "GET",
        data: {
        },
        url: url,
        error: function (err) {
            showResponseModal(err);
        },
        success: function (data, statusText, response) {
            if($("#uploadModalHeading").length)
            {
                $("#uploadModalHeading").empty();
            }
            cardContainer.empty();
            cardContainer.append(data.view);
            callback();
        }
    });
}

function filterReqParam(object, useFormData)
{
    useFormData ||= false;
    const reqBody = useFormData ? new FormData() : {};
    for (var key in object) {
        if(object[key] !== null && object[key] !== undefined && object[key] !== ''){
            useFormData ? reqBody.append(key, object[key]) : reqBody[key] = object[key];
        }
    }

    return reqBody;
}