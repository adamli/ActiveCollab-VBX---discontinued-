<?php
$plugin_info = $plugin->getInfo();
$prompt = '';
$proj_id = AppletInstance::getValue('proj_id', '');
$default_user = '';

define('COL_PLUGIN_DIR', $plugin_info['dir_name']);
define('COL_PLUGIN_PATH', PLUGIN_PATH.'/'.COL_PLUGIN_DIR);
define('COL_PLUGIN_DIR', base_url().'plugins/'.COL_PLUGIN_DIR);

$col_creds = PluginData::get('collab_user');
if($col_creds) {
	define('COL_DOMAIN', $col_creds->domain);
	define('COL_TOKEN', $col_creds->token);
	define('COL_TIMEZONE', $col_creds->timezone);

	require_once(COL_PLUGIN_PATH.'/libraries/activecollab_client.php');

	$projects = activecollab_client::request('/projects');
}
