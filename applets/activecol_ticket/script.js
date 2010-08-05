var col_creds_form = {
    populate_projects: function(app_el) 
    { // {{{
        var that = col_creds_form;

        $.ajax({
            url: base_url+'config/ActiveCollab-VBX/api/projects',
            cache: false,
            type: 'get',
            data: {
                token: col_token,
                domain: col_domain
            },
            dataType: 'text',
            success: function(r) {
                try {
                    r = r.match(/\{.*\}/)[0];
                    r = JSON.parse(r);

                    if(r.key == 'SUCCESS') {
                        $.each(r.data.projects, function(k, project) {
                            var sel = app_el.find('select[name="proj_id"]');
                            var new_opt = $('<option></option>')
                                .attr('value', project.id)
                                .text(project.name);
                            new_opt.appendTo(sel);
                        });
                    }
                } catch(e) { }
            }
        });
    }, // }}}

	submit_creds: function(app_el) 
    { // {{{
        var that = col_creds_form;
        var domain_el = $('input[name="domain"]', app_el);
        var token_el = $('input[name="token"]', app_el);
        var timezone = -((new Date()).getTimezoneOffset()/60);

        $('span[class$="_err"]', app_el).empty();
        $('div.system_msg', app_el).empty().css('color', 'inherit');

        var errors = [];
        if(domain_el.val().trim() == '') errors.push({ name:'domain', msg:'activeCollab Domain is required.' });
        if(token_el.val().trim() == '') errors.push({ name:'token', msg:'API Token is required.' });

        if(errors.length == 0) {
            $('div.system_msg', app_el).html('<a class="ajax_loader"></a> Testing your credentials.');

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
            				$('div.system_msg', app_el).html('Success! Credentials saved.').css('color', 'green');
                            var app = app_el.closest('div.vbx-applet');
                            col_domain = domain_el.val();
                            col_token = token_el.val();
                            $.notify('activeCollab credentials saved.');
                            app.find('div.credentials').css('display', 'none');
                            app.find('div.ticket_settings').css('display', 'block');
                            that.populate_projects(app_el);
						} else if(r.key == 'INVALID_USER') {
            				$('div.system_msg', app_el).html('Invalid activeCollab credentials saved.').css('color', 'red');
						} else if(r.key == 'FORM_ERROR') {
            				$('div.system_msg', app_el).html('Cannot save credentials due to errors on the form.').css('color', 'red');
						}
					} catch(e) {
            			$('div.system_msg', app_el).html('Cannot save credentials due to exception ' + e).css('color', 'red');
					}
				}
			});
        } else {
            $('div.system_msg', app_el).html('Invalid credentials.').css('color', 'red');
            $.each(errors, function(k, v) {
                if(v.name == 'domain') $('span.domain_err', app_el).text(v.msg);
                else if(v.name == 'token') $('span.token_err',app_el).text(v.msg);
            });
        }
	} // }}}
}

$(document).ready(function() {
    $('div.activecol_ticket_applet button.save_creds_btn').live('click', function() {
        col_creds_form.submit_creds($(this).closest('div.vbx-applet'));
    });
});
