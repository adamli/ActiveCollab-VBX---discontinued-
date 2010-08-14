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
	function request($path, $method='GET', $params=array()) {
		if(!defined('COL_DOMAIN') || !defined('COL_TOKEN')) {
			error_log('ACTIVECOLLAB-VBX: activeCollab credentials missing.');
			return FALSE;
		}

		$url = COL_DOMAIN.'?path_info='.$path.'&token='.COL_TOKEN;
		$ch = curl_init();

		switch($method) {
			case 'GET':
				$url .= '&format=json&'.http_build_query($params);
            	curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
				break;

			case 'POST':
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
				break;

			default:
				return FALSE;
		}

		curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => FALSE,
			CURLOPT_FOLLOWLOCATION => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE
		));


		$results = curl_exec($ch);
		$ch_info = curl_getinfo($ch);
		$ch_error = curl_error($ch);

		error_log('ACTIVECOLLAB-VBX: Pinging '.$url.' - '.json_encode($ch_info));
		error_log('ACTIVECOLLAB-VBX: Results - '.$results);
	
		if($ch_error) {
			error_log('ACTIVECOLLAB-VBX: Curl error to activeCollab API: '.$ch_error);
			return FALSE;
		} else if($ch_info['http_code'] == 200) {
			return json_decode($results);
		}

		return FALSE;
	}
}
