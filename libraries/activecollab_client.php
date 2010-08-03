<?php
class activecollab_client {
	/*
		Function: request

		Parameters:
			path
			method
			params

		Globals:
			COL_DOMAIN
			COL_TOKEN
	*/
	function request($path, $method='GET', $params='') {
		if(!defined('COL_DOMAIN') || !defined('COL_TOKEN')) {
			error_log('activeCollab credentials missing.');
			return FALSE;
		}

		$url = 'http://'.COL_DOMAIN.'.activecollab.net/api.php';
		$querystr = array(
			'path_info' => $path,
			'token' => COL_TOKEN,
			'format' => 'json'
		);
		if(is_array($params) && !empty($params)) {
			$querystr = array_merge($query_str, $params);
		}
		$url .= '?'.http_build_query($querystr);
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => FALSE,
			CURLOPT_FOLLOWLOCATION => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE
		));

		switch($method) {
			case 'GET':
            	curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
				break;

			case 'POST':
				curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/xml"));
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
				break;

			default:
				return FALSE;
		}

		$results = curl_exec($ch);
		$ch_info = curl_getinfo($ch);
		$ch_error = curl_error($ch);

		error_log('curl to '.$url.': '.json_encode($ch_info));
	
		if($ch_error) {
			error_log('curl error to activecollab api: '.$ch_error);
			return FALSE;
		} else if($ch_info['http_code'] == 200) {
			return json_decode($results);
		}

		return FALSE;
	}
}
