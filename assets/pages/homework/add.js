ClassicEditor
	.create(document.querySelector("#CK"), {
		ckfinder: {
            uploadUrl: base_url+"homework/json_upload_image"
        }
	})
	.then(editor => {
		if( access_denied != undefined ) {
			editor.isReadOnly = true;
		}
	})
	.catch(error => {
		console.error( error );
	});