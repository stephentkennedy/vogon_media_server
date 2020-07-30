var lazy = {
	ident: '',
	screen_b: 0,
	check: function(dom){
		var top = $(dom).offset().top;
		if(top < ($(window).scrollTop() + lazy.screen_b)){
			return true;
		}else{
			return false;
		}
	},
	load: function(dom){
		var src = $(dom).data('src');
		$(dom).attr('src', src).addClass('loaded');
	},
	loop: function(){
		var items = $(lazy.ident+':not(.loaded)');
		items.each(function(i){
			var dom = items[i];
			if(lazy.check(dom)){
				lazy.load(dom);
			}
		});
	},
	init: function(ident){
		lazy.ident = ident;
		lazy.screen_b = window.innerHeight;
		$(window).off('resize.lazy').on('resize.lazy', function(){
			lazy.init(ident);
		});
		$(window).off('scroll.lazy').on('scroll.lazy', lazy.loop);
		lazy.loop();
	},
};