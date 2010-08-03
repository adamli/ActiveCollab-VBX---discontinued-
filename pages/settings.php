<style>
label span.caption { color:gray; font-style:italic; }

a.ajax_loader { background:url(<?php echo base_url() ?>assets/i/ajax-loader.gif); display:inline-block; width:16px; height:11px; vertical-align:middle; }
div.form_bottom { line-height:30px; margin-top:20px; }
div.header { margin:10px; padding:10px; }
div.section { background-color:white; margin:20px; margin-top:0px; padding:20px; }
div.section > h3:first-child { text-transform:uppercase; margin-top:0px; margin-bottom:20px; font-size:18px; } 
div.section > * { vertical-align:middle; }
span[class$="_err"] { color:red; }

#collab_logo { background:url(http://www.activecollab.com/public/assets/images/design/homepage/site_logo.gif); font-size:20px; width:169px; height:24px; color:transparent; }
#logo_tag { font-size:10px; }
</style>

<div class="vbx-content-main">
	<div class="vbx-table-section" style="background-color:#e8e5c2;">
		<div class="header">
			<h2 id="collab_logo">activeCollab Settings</h2>
			<span id="logo_tag">Settings</span>
		</div>

		<div id="activecollab_creds" class="section">
			<h3>Api Access Credentials</h3>

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
				<button id="save_creds_btn" class="inline-button submit-button">
					<span>Save</span>
				</button>

				<?php if($col_creds): ?>
				<a id="del_creds_btn" href="#delete_creds" style="margin-right:10px;">Delete</a>
				<?php endif; ?>

				<div class="system_msg" style="display:inline-block;">
				</div>
			</div>
		</div>
	</div>
</div>

<script>
var base_url = '<?php echo base_url(); ?>';
var plugin_url = '<?php echo COL_PLUGIN_URL ?>';
var plugin_dir = '<?php echo COL_PLUGIN_DIR ?>';
var col_creds = <?php echo $col_creds ? 'true' : 'false' ?>
</script>
<?php OpenVBX::addJS('config.js'); ?>
