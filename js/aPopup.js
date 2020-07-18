/*
Date: 11/22/2019
Name: Stephen Kennedy
Comment: This document powers the Almost Simple Popup system.
*/
var aPopup = {};
aPopup.newWindow = function(content, options){
		if(options == undefined){
			options = false;
		}
		var style = ' style="';
		if(options != false && options.width != undefined){
			style += 'min-width:'+options.width+';';
		}
		if(options != false && options.height != undefined){
			style += 'min-height:'+options.height+';';
		}
		if(options != false && options.title != undefined){
			var title = options.title;
		}else{
			var title = '';
		}
		if(options != false && options.position != undefined){
			var cls = options.position;
		}else{
			var cls = '';
		}
		style += '"';
		var object = $('<div class="popup '+cls+'"'+style+'><div class="handle"><div><a class="drag"><i class="fa fa-ellipsis-v"></i></a> '+title+'</div><div><a class="closePop"><i class="fa fa-times"></i></a></div></div><div class="content">'+content+'</div></div>').appendTo('#popup');
		aPopup.clickBind();
		return object;
	}

	aPopup.loadingIcon = "<div class='spinner'><i class='fa fa-cog'></i></div>";

	aPopup.ajaxWindow = function(action, data){
		if(data == undefined){
			data = false;
		}
		var style = ' style="';
		if(data != false && data.width != undefined){
			style += 'width:'+data.width+';';
		}
		if(data != false && data.height != undefined){
			style += 'height:'+data.height+';';
		}
		style += '"';
		if(data != false && data.title != undefined){
			var title = data.title;
		}else{
			var title = '';
		}
		if(data != false && data.position != undefined){
			var cls = data.position;
		}else{
			var cls = '';
		}
		var active = $('<div class="popup '+cls+'"'+style+'><div class="handle"><div><a class="drag"><i class="fa fa-ellipsis-v"></i></a> '+title+'</div><div><a class="closePop"><i class="fa fa-times"></i></a></div></div><div class="content">'+aPopup.loadingIcon+'</div><div class="shadow"></div></div>').appendTo('#popup');
		aPopup.clickBind();
		if(data != false){
			data.action = action;
		}else{
			data = {action: action}
		}
		$.post("/main/ajax.php?session=" + globalSession, data).fail(function(data){
			var data = "Request timed out.";																	
			$(active[0] + ' .content').html(data);
		}).success(function(data){
			$(active[0]).find('.content').html(data);
		});
	}

	aPopup.clickBind = function(){
		//Rebind the close button
		$('.closePop').off().click(function(){
			$(this).parent().parent().parent().remove();
		});
		//Register our click event, remove any existing events to keep from binding the same event over and over.
		$('.control').off().click(function(){
			var data = this.dataset;
			aPopup.ajaxWindow($(this).attr('data-action'), data) ;
		});
		//Initialize our draggable.
		$( ".popup" ).draggable({
			addClasses: false,
			containment: '.page-content',
			handle: ".handle"
		}).resizable({
			minHeight: 100,
			minWidth: 100,
			autoHide: true
		});
		//This allows us to have the grab/grabbing cursor change when clicking on the handle.
		$('.popup .handle').mousedown(function(){
			$(this).addClass('dragging');
		}).mouseup(function(){
			$(this).removeClass('dragging');
		});
	}
$(document).ready(function(){
	aPopup.clickBind();
	var labels = $('.form-label');
	for(i in labels){
		if(isInt(i)){
			var label = $(labels[i]);
			$('#' + label.attr('data-for')).click(function(){
				if(!label.hasClass('active')){
					label.addClass('active');
				}
			});
			$('#' + label.attr('data-for')).focusout(function(){
				if(this.value == ''){
					label.removeClass('active');
				}
			});
		}
	}
});
function isInt(value) {return !isNaN(value) && (function(x) { return (x | 0) === x; })(parseFloat(value))}