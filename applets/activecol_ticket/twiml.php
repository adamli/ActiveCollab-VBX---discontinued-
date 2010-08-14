<?php
session_start();
$plugin_info = $plugin->getInfo();
$prompt = AppletInstance::getAudioSpeechPickerValue('prompt');
$proj_id = AppletInstance::getValue('proj_id', '0');
$next = AppletInstance::getDropZoneUrl('next');
$default_user = AppletInstance::getUserGroupPickerValue('default_user');
?>

<?php // Time to transcribe - create ticket in activeCollab 
if(!empty($_REQUEST['TranscriptionText'])): ?>
    <?php // {{{
    define('COL_PLUGIN_DIR', $plugin_info['dir_name']);
    define('COL_PLUGIN_PATH', PLUGIN_PATH.'/'.COL_PLUGIN_DIR);
    define('COL_PLUGIN_DIR', base_url().'plugins/'.COL_PLUGIN_DIR);
        
    require_once(COL_PLUGIN_PATH.'/libraries/activecollab_client.php');

    $col_creds = PluginData::get('collab_user');
    if(empty($col_creds)) {
        error_log('ACTIVECOLLAB-VBX: Invalid activeCollab credentials. Call ended.');
        die;
    }
    define('COL_DOMAIN', $col_creds->domain);
    define('COL_TOKEN', $col_creds->token);
    define('COL_TIMEZONE', $col_creds->timezone);

	$body =
		'"'.$_REQUEST['TranscriptionText'].'"'."<br />".
		"<a href=\"{$_REQUEST['RecordingUrl']}\" target=\"_blank\">{$_REQUEST['RecordingUrl']}</a>";

    $new_ticket = activecollab_client::request(
		'/projects/'.$proj_id.'/tickets/add', 
		'POST', 
		array(
			'submitted' => 'submitted', 
			'ticket[name]' => 'Voicemail Ticket from '.format_phone($_REQUEST['Caller']), 
			'ticket[body]' => $body
		)
	);
    error_log('ACTIVECOLLAB-VBX: Creating new ticket - '.json_encode($new_ticket));

    $params = http_build_query($_REQUEST);
    $redirect_url = site_url('twiml/transcribe').'?'.$params;
    header("Location: $redirect_url");
    // }}} ?>

<?php // Create the voicemail placer 
elseif(!empty($_REQUEST['RecordingUrl'])): ?>
    <?php // {{{ 
    // Match phone numbers
    $users = OpenVBX::getUsers();
    $found_user = FALSE;
    foreach($users as $user) {
        foreach($user->devices as $device) {
            if(strpos($device->value, $_SESSION['Caller']) !== FALSE) {
                $found_user = $user;
                break 2;
            }
        }
    }

    OpenVBX::addVoiceMessage(
        $found_user ? $found_user : $default_user,
        $_REQUEST['CallSid'],
        $_REQUEST['Caller'],
        $_REQUEST['Called'],
        $_REQUEST['RecordingUrl'],
        $_REQUEST['Duration']
    );
    // }}} ?>

<?php // Initial response 
else: ?>
    <?php // {{{ ?>
    <Response>
        <?php if(strpos($prompt, '.mp3') !== FALSE): ?>
        <Play><?php echo $prompt ?></Play>
        <?php else: ?>
        <Say><?php echo $prompt ?></Say>
        <?php endif; ?>

        <Record transcribe="true" transcribeCallback="" />

        <?php if(!empty($next)): ?>
        <Redirect><?php echo $next ?></Redirect>
        <?php else: ?>
        <Hangup />
        <?php endif; ?>
    </Response>
<?php endif; // }}} ?>
