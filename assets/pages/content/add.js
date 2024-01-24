ClassicEditor
	.create(document.querySelector("#CK"), {
		ckfinder: {
            uploadUrl: base_url+"content/json_upload_image"
        }
	})
	.catch(error => {
		console.error( error );
	});