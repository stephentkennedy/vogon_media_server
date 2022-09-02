var app = {};

app.ajax_form = function (form_contents, callback) {
	var w = aPopup.newWindow(form_contents.content, form_contents.options);
	console.log(callback);
	w.find('.submit').click(function(){
		$(this).attr('disabled', 'true').html('Submitting ... <i class="fa fa-fw fa-cog fa-spin"></i>')
		var form = w.find('form');
		var proc = form.attr('action');
		var form_data = form.serialize();
		$.post(proc, form_data, function(returned){
			w.remove();
			if(callback != undefined){
				callback();
			}
		});
	});
	w.keypress(function(e){
		if(e.which == 13 && $(this).find('.submit').attr('disabled') != true){
			$(this).find('.submit').attr('disabled', 'true').html('Submitting ... <i class="fa fa-fw fa-cog fa-spin"></i>')
			var form = w.find('form');
			var proc = form.attr('action');
			var form_data = form.serialize();
			$.post(proc, form_data, function(returned){
				w.remove();
				if(callback != undefined){
					callback();
				}
			});
		}
	});
}