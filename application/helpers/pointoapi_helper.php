<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function pointoapi_Request($library, $data = []) {

	global $app;
	
	// config
	if( !isset($data['api_key']) ) $data['api_key'] = branch_now('pointoapi_key');
	
	// library host
	$library_host = [
		'SynorexAPI/Storage/Upload' => [
			'endpoint' => 'https://api.synorexcloud.com/pointoapi/storage/upload',
		],
		'SynorexAPI/Email/Send' => [
			'endpoint' => 'https://api.synorexcloud.com/pointoapi/email/send',
		],
		'SynorexAPI/SMS/Send' => [
			'endpoint' => 'https://api.synorexcloud.com/pointoapi/sms/send',
		],
		'PointoAPI/Cert/Verify' => [
			'endpoint' => 'https://pointoapi.synorexcloud.com/api/cert/verify',
		],
		'SynorexAPI/Payment/New' => [
			'endpoint' => 'https://api.synorexcloud.com/pointoapi/payment/new',
		],
		'SynorexAPI/Payment/Check' => [
			'endpoint' => 'https://api.synorexcloud.com/pointoapi/payment/check',
		],
	];

	if(!isset($library_host[ $library ])) return 'endpoint_not_found';

	// request
	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => $library_host[ $library ]['endpoint'],
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => $data,
		CURLOPT_HTTPHEADER => array(
			'Cookie: PHPSESSID=1de95961097e908d9764a6460f47e8e1'
		),
	));

	// return curl_exec($curl);

	$response = json_decode(curl_exec($curl), true);
	if(!isset($response)) $response = [ 'status' => 'sdk_error' ];

	curl_close($curl);

	// return
	return $response;

}

function pointoapi_Upload($file, $data = []) {
	
	global $Db;

	if(!isset($data['default'])) $data['default'] = null;

	if($file['error'] == 4) {

		return empty($data['default']) ? null : $data['default'] ;

	} else {

		$type = explode('.', $file['name']);
		
		if(isset($data['api_key'])) {
			$api_key = $data['api_key'];
		} else {
			$api_key = null;
		}
		
		$pointoapi_data = pointoapi_Request('SynorexAPI/Storage/Upload', [
			'file' => new CURLFILE($file['tmp_name']),
			'file_name' => $file['name'],
			'api_key' => $api_key,
		]);
		
		// return $pointoapi_data;

		if(!is_array($pointoapi_data)) $pointoapi_data = [];
		
		if(isset($pointoapi_data['result']['file_source'])) {

			$NewId = get_new('id');
			
			$this_ci =& get_instance();
			
			$this_ci->db->insert('tbl_uploads', [
				'pid' => $NewId,
				'type' => isset($data['type']) ? $data['type'] : null ,
				'branch' => isset($data['branch']) ? $data['branch'] : null ,
				'file_name' => $file['name'],
				'file_type' => end($type),
				'file_size' => $file['size'],
				'file_source' => $pointoapi_data['result']['file_source'],
				'create_by' => null,
				'update_by' => null,
			]);

			return $NewId;

		} else {

			return null;

		}

	}

}

function pointoapi_UploadSource($upload_id = '') {

	$upload_source = datalist_Table('tbl_uploads', 'file_source', $upload_id);

	return empty($upload_source) ? 'https://cdn.synorexcloud.com/assets/images/blank/4x3.jpg' : $upload_source ;

}