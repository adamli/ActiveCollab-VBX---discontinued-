var col_creds_form = {
	initialize: function() {
		var that = col_creds_form;
		that.render();
	},

	submit_creds: function() {
        var domain_el = $('input[name="domain"]');
        var token_el = $('input[name="token"]');
        var timezone = -((new Date()).getTimezoneOffset()/60);

        $('span[class$="_err"]').empty();
        $('div.system_msg').empty().css('color', 'inherit');

        var errors = [];
        if(domain_el.val().trim() == '') errors.push({ name:'domain', msg:'activeCollab Domain is required.' });
        if(token_el.val().trim() == '') errors.push({ name:'token', msg:'API Token is required.' });

        if(errors.length == 0) {
            $('div.system_msg').html('<a class="ajax_loader"></a> Testing your credentials.');

			$.ajax({
				url: base_url+'config/ActiveCollab-VBX/api/activecollab',
				cache: false,
				type: 'post',
				data: {
					domain: domain_el.val(),
					token: token_el.val(),
					timezone: timezone
				},
				dataType: 'text',
				success: function(r) {
					try {
						r = r.match(/\{.*\}/)[0];
						r = JSON.parse(r);

						if(r.key == 'SUCCESS') {
            				$('div.system_msg').html('Success! Credentials saved.').css('color', 'green');
							window.setTimeout(3000, function() { location.href = base_url + 'config/ActiveCollab-VBX'; });
						} else if(r.key == 'INVALID_USER') {
            				$('div.system_msg').html('Invalid activeCollab credentials.').css('color', 'red');
						} else if(r.key == 'FORM_ERROR') {
            				$('div.system_msg').html('Cannot save credentials due to errors on the form.').css('color', 'red');
						}
					} catch(e) {
            			$('div.system_msg').html('Cannot save credentials due to exception ' + e).css('color', 'red');
					}
				}
			});
        } else {
            $('div.system_msg').html('Invalid credentials.').css('color', 'red');
            $.each(errors, function(k, v) {
                if(v.name == 'domain') $('span.domain_err').text(v.msg);
                else if(v.name == 'token') $('span.token_err').text(v.msg);
            });
        }
	},

	submit_del_creds: function() {
		if(confirm('Are you sure you want to delete your activeCollab settings?')) {
            $('div.system_msg').html('<a class="ajax_loader"></a> Removing your credentials.');

			$.ajax({
				url: base_url+'config/ActiveCollab-VBX/api/activecollab',
				cache: false,
				type: 'delete',
				dataType: 'text',
				success: function(r) {
					location.href = base_url + 'config/ActiveCollab-VBX';
				}
			});
		}
	},

	render: function(name, options) {
		var that = col_creds_form;

		switch(name) {
			case undefined:
				$('#del_creds_btn').click(function() {
					that.submit_del_creds();
				});

				$('#save_creds_btn').click(function() {
					that.submit_creds();
				});
				break;
		}
	}
}

col_creds_form.initialize();
