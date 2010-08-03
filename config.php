<?php
$CI =& get_instance();
$plugin = OpenVBX::$currentPlugin;
$plugin_info = $plugin->getInfo();

$col_creds = PluginData::get('collab_user');
if($col_creds) {
	define('COL_DOMAIN', $col_creds->domain);
	define('COL_TOKEN', $col_creds->token);
	define('COL_TIMEZONE', $col_creds->timezone);
}

define('COL_PLUGIN_DIR', $plugin_info['dir_name']);
define('COL_PLUGIN_PATH', PLUGIN_PATH.'/'.COL_PLUGIN_DIR);
define('COL_PLUGIN_DIR', base_url().'plugins/'.COL_PLUGIN_DIR);

// Check for API
$uri = $CI->uri->segments;
if(@$uri[3] == 'api') {
	require_once(COL_PLUGIN_PATH.'/libraries/plugin_server.php');
	require_once(COL_PLUGIN_PATH.'/libraries/activecollab_client.php');

	$api_path = str_replace('/config/ActiveCollab-VBX/api', '', $CI->uri->uri_string);
	if(empty($api_path)) $api_path = '';
	$response = PluginServer::processRequest($api_path);
	$t = explode('/', $response->path);
	$api_path_segs = array();
	foreach($t as $seg) {
		if($seg != '') $api_path_segs[] = $seg;
	}

	/*
		Object: activecollab
			Authenticate account.
	*/	
	if($api_path_segs[0] == 'activecollab') {
		if(empty($api_path_segs[1])) {
			/*
				Method: Post
					Authenticates the user

				Parameter:
					domain
					token
					timezone
			*/
			if($response->method == 'post') 
			{ // {{{
				try {
					$params = $response->params;

					// Error checking
					$errors = array();
					if(empty($params['domain'])) $errors[] = array('name'=>'domain', 'msg'=>'activeCollab Domain is required.');
					if(empty($params['token'])) $errors[] = array('name'=>'token', 'msg'=>'Token is required.');
					if(!empty($errors)) throw new Exception('FORM_ERROR');

					// Sanatize
					foreach($params as &$v) $v = trim($v);

					define(COL_DOMAIN, $params['domain']);
					define(COL_TOKEN, $params['token']);

					$client = activecollab_client::request('/info');
					if(!$client) {
						throw new Exception('INVALID_USER');
					} else {
						PluginData::set('collab_user', $params);
						throw new Exception('SUCCESS');
					}

					throw new Exception('EXCEPTION');
				} catch(Exception $e) {
					switch($e->getMessage()) {
						case 'FORM_ERROR':
							PluginServer::sendResponse(400, 'FORM_ERROR', 'There are errors on your form.', array('errors' => $errors));
							break;

						case 'INVALID_USER':
							PluginServer::sendResponse(401, 'INVALID_USER', 'Invalid activeCollab user credentials.');
							break;

						case 'SUCCESS':
							PluginServer::sendResponse(200, 'SUCCESS', 'activeCollab credentials saved.', array('url' => $params['url']));
							break;

						default:
							PluginServer::sendResponse(500, $e->getMessage(), 'Exception server error.');
							break;
					}
				}
			} // }}}

			/*
				Method: Delete
					Removes the user credentials

				Parameters:
					none
			*/
			else if($response->method == 'delete') 
			{ // {{{
				PluginData::delete('collab_user');
				PluginServer::sendResponse(200, 'SUCCESS', 'activeCollab credentials deleted.');
			} // }}}
		}
	}

	else {
		PluginServer::sendResponse(404);
	}
	exit;

// Otherwise, load the config page
} else {
	require_once(COL_PLUGIN_PATH.'/pages/settings.php');
}
