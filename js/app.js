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

app.enhance_decodeURI = function(string){
	var string = decodeURI(string);
	var slash_pattern = /\%2F/g;
	var comma_pattern = /\%2C/g;
	var space_pattern = /\+/g;
	var at_pattern = /\%40/g;
	var patterns = {
		slash: {
			pattern: slash_pattern,
			replace: '/'
		},
		comma: {
			pattern: comma_pattern,
			replace: ',',
		},
		space: {
			pattern: space_pattern,
			replace: ' '
		},
		at_pattern: {
			pattern: at_pattern,
			replace: '@'
		},
		amp: {
			pattern: /\%26/g,
			replace: '&'
		}
	};
	for(var i in patterns){
		var replace = patterns[i];
		string = string.replace(replace.pattern, replace.replace);
	}
	return string;
}

let getTimeout = (() => { // IIFE
    let _setTimeout = setTimeout, // Reference to the original setTimeout
        map = {}; // Map of all timeouts with their end times

    setTimeout = (callback, delay) => { // Modify setTimeout
        let id = _setTimeout(callback, delay); // Run the original, and store the id
        map[id] = Date.now() + delay; // Store the end time
        return id; // Return the id
    };

    return (id) => { // The actual getTimeout function
        // If there was no timeout with that id, return NaN, otherwise, return the time left clamped to 0
        return map[id] ? Math.max(map[id] - Date.now(), 0) : NaN;
    }
})();

window.app = app;
window.getTimeout = getTimeout;