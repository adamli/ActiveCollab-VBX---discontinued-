<?php
$CI =& get_instance();
$plugin_info = $plugin->getInfo();

$proj_id = AppletInstance::getValue('proj_id', '');

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
?>

<style>
div.activecol_ticket_applet div.section { margin-bottom:20px; }
div.activecol_ticket_applet div.section h2 { margin:0px; }
div.activecol_ticket_applet div.section p { margin:0px; margin-bottom:5px; }
</style>

<div class="vbx-applet activecol_ticket_applet">
	<p>This applet will take a voicemail of incoming calls and create a ticket in activeCollab projects.</p>

	<div class="section">
		<div class="prompt">
			<h2>Prompt</h2>
			<p>What will the caller hear before leaving their message?</p>
			<?php echo AppletUI::AudioSpeechPicker('prompt') ?>
		</div>
	</div>

	<div class="section">
		<h2>Project</h2>
		<p>Tickets will drop into this project.</p>
		<select name="proj_id">
			<option value=""></option>
			<?php foreach($projects as $project): ?>
			<option value="<?php echo $project->id ?>" <?php echo $proj_id == $project->id ? 'selected="selected"' : '' ?>><?php echo $project->name ?></option>
			<?php endforeach; ?>
		</select>
	</div>

	<div class="section">
		<h2>Take voicemail</h2>
		<p>Voicemail will automatically go into incoming caller's inbox.</p>
	</div>

	<div class="section">
		<h2>Default voicemail user</h2>
		<p>If incoming call doesn't match any users, voicemail goes into this inbox.</p>
		<?php echo AppletUI::UserGroupPicker('default_user'); ?>
	</div>
</div>
