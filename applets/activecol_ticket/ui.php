<?php
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
div.activecol_ticket_applet a.ajax_loader { background:url(<?php echo base_url() ?>assets/i/ajax-loader.gif); display:inline-block; width:16px; height:11px; vertical-align:middle; }
div.activecol_ticket_applet div.form_bottom { line-height:30px; margin-top:20px; }
div.activecol_ticket_applet label span.caption { color:gray; font-style:italic; }
</style>

<div class="vbx-applet activecol_ticket_applet">
	<p>This applet will take a voicemail of incoming calls and create a ticket in activeCollab projects.</p>

    <div class="credentials" <?php echo $col_creds ? 'style="display:none;"' : '' ?>>
        <!-- {{{ -->
		<div class="activecollab_creds section">
			<h2 style="margin-bottom:10px;">Api Access Credentials</h2>

			<div class="vbx-input-container input">
				<label>
					activeCollab URL
					<span class="caption">activeCollab Domain</span>
					<div style="font-size:16px;">http:// <input name="domain" class="small" type="text" value="<?php echo defined('COL_DOMAIN') ? COL_DOMAIN : '' ?>" style="display:inline-block;" /> .activecollab.net/api.php</div>
				</label>
				<span class="domain_err"></span>
			</div>

			<div class="vbx-input-container input" style="margin-top:20px;">
				<label>
					API Key
					<span class="caption">Key that can befound in your activeCollab account, which will allow this plugin access</span>
					<input name="token" class="medium" type="text" value="<?php echo defined('COL_TOKEN') ? COL_TOKEN : '' ?>" />
				</label>
				<span class="token_err"></span>
			</div>

			<div class="form_bottom">
				<button class="save_creds_btn inline-button submit-button">
					<span>Save</span>
				</button>

				<div class="system_msg" style="display:inline-block;">
				</div>
			</div>
		</div>
    </div><!-- }}} -->

    <div class="ticket_settings" <?php echo $col_creds ? '' : 'style="display:none;"' ?>>
        <!-- {{{ -->
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
                <?php if($projects): foreach($projects as $project): ?>
                <option value="<?php echo $project->id ?>" <?php echo $proj_id == $project->id ? 'selected="selected"' : '' ?>><?php echo $project->name ?></option>
                <?php endforeach; endif; ?>
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
    </div><!-- }}} -->
</div>

<script>
var col_domain = '<?php echo defined('COL_DOMAIN') ? COL_DOMAIN : '' ?>';
var col_token = '<?php echo defined('COL_TOKEN') ? COL_TOKEN : '' ?>';
var col_creds = <?php echo $col_creds ? '1' : '0' ?>;
</script>
