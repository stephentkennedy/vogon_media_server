function bindTrays(){
	//Bind tray expansion
	$('.controls .tray-expand').click(function(){
		var parent = $(this).parent().parent();
		if(parent.hasClass('open')){
			parent.removeClass('open');
		}else{
			if($(window).width() > 994){
				if(parent.hasClass('left')){
					$('.tray.left').removeClass('open');
				}else{
					$('.tray.right').removeClass('open');
				}
			}else{
				$('.tray').removeClass('open');
			}
			parent.addClass('open');
		}
	});
	$('.controls .tray-full').click(function(){
		var parent = $(this).parent().parent();
		if($(this).hasClass('fa-arrows-alt-h')){
			parent.removeClass('one-third').addClass('one');
		}else{
			parent.removeClass('one').addClass('one-third');
		}
	});
}
$(document).ready(function(){
	bindTrays();
});