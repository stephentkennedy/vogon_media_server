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

app.simplify_array = function(array, max_members){
	if(typeof array == 'undefined'){
		return;
	}
	if(typeof max_members == 'undefined'){
		max_members = 10;
	}
	if(array.length > max_members){
		var increment = array.length / max_members;
		increment = Math.ceil(increment);
		var to_save = [];

		for(var i = 0; i < array.length; i += increment){
			var item = array[i];
			to_save.push(item);
		}
		array = to_save;
	}
	return array;
}

app.simplify_num_array = function(array, max_members){
	if(typeof array == 'undefined'){
		return;
	}
	if(typeof max_members == 'undefined'){
		max_members = 10;
	}
	if(array.length > max_members){
		var increment = array.length / max_members;
		increment = Math.ceil(increment);
		var to_save = [];
		var current = 0;
		for(var i = 0; i < array.length; i++){
			var item = array[i];
			current += item;
			if(i % increment == 0){
				current = Math.floor(current / increment);
				to_save.push(current);
				current = 0;
			}
		}
		array = to_save;
	}
	return array;
}
