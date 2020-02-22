$(function(){
	$('#menu > li.top').click(function(e){
		if($(e.target).data('menu')){
			var menu = $('#menu > li.top');
			var flag = $(this).hasClass('slowShow');
			
			menu.removeClass('slowShow');
			if(!flag) $(this).addClass('slowShow');
			$(this).children('[class ^= "submenu"]').toggle('fast');
			menu.not('.slowShow').children('[class ^= "submenu"]').hide('fast');
		}
		
	});
	
	
	
	// select active menu element
	var activeTemp = document.URL;
	var $active = activeTemp;
	if(!($active = menuSelector(activeTemp)).length){
		var i = 3;
		while(i--){
			if(($active = menuSelector(activeTemp = activeTemp.replace(/(.*)\/.*\/$/gi, '$1/'))).length)
				break;
		}
	}
	$active.closest('li.top').find('div > a').click();
	if(!$active.parent('li').length){
		$active.closest('li.top').addClass('active');
	}else{
		$active.parent().addClass('active').closest('li.top').addClass('active')
	}
	
	
	/*clear cache*/
	$('#clear-cache').click(function(){
		if(confirm('Подтвердите очистку кеша!')){
			$.post(root + 'admin/user/clearcache/', function(){alert('Ok!')});
		}
	});
	
	$('.thumbnails').on('click', '.copy', function(e){
		if (document.selection) {
			const range = document.body.createTextRange();
			range.moveToElementText(this);
			range.select();
		} else if (window.getSelection) {
			const range = document.createRange();
			range.selectNode(this);
			window.getSelection().addRange(range);
		}
		document.execCommand('copy');
		e.preventDefault();
	});
	
});

function menuSelector(href){
	return $('#menu a[href="'+href+'"]');
}

function getCookie(name) {
  var matches = document.cookie.match(new RegExp(
	"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]).replace(/\+/g, ' ') : undefined;
}

function setCookie(name, value, hours){
	var date = new Date( new Date().getTime() + 3600*hours*1000 );
	var exp = (!hours) ? '' : "expires="+date.toUTCString();
	document.cookie=name+'='+value+";"+exp;
}

function delCookie(name){
	var date = new Date(0);
	document.cookie= name+"=; expires="+date.toUTCString();
}

function gebi(id){
	return document.getElementById(id);
}


function drawLoadingWait($el, del){
	var del = del || false;
	var loadClass = 'loading-wait';
	
	$el.addClass('rel');
	if(del) $el.children('.'+loadClass).remove();
	else	$el.append('<div class="'+loadClass+'"></div>');
}


function ExtraFiled(){
	this.state = 0;
	this.counter = 0;
	var self = this;
	this.getSetNew = false;
	this.setNew = function(e){
		e.preventDefault();
		self.getSetNew = true;console.log(1);
		if(!this.state){
			$('#select_extra_name').addClass('none');
			$('#input_extra_name').removeClass('none');
			$('#init_new_extra').text('Отмена');
			this.state = !this.state;
		}else{
			extraFiled.cancel();
		}
	}
	
	this.set = function(){
		if(!self.getSetNew && $('#select_extra_name')[0].selectedIndex == 0){
			console.log(this.getSetNew, $('#select_extra_name'), $('#select_extra_name')[0].selectedIndex);
			alert('Выберите имя поля или введите новое');return;
		}else{
			var nameEl 	= $('#input_extra_name');
			var valueEl = $('#extra_value_editor');
			var name 	= nameEl.val();
			var value 	= valueEl.val();
			if($('#select_extra_name')[0].selectedIndex && name == ''){
				name = $('#select_extra_name option:selected').text();
			}
			if(name == ''){
				alert('Выберите имя поля или введите новое');return;
			}else if(value == ''){
				alert('Введите значение');return;
			}else if($('[name="'+name+'"]').length || $('[name="extra_fileds['+name+']"]').length){
				alert('Поле с данным именем уже существует');return;
			}else if(name[0] == '_'){
				alert('Недопустимое имя поля! Не может начинаться с "_"');return;
			}
		}
		
		if(!$('.new_extra_fields > .field').length){
			$('.fields_caption').removeClass('none');
		}
		
		
		var field = $('.field.prototype').clone();
		field.find('.extra_name').val(name);
		field.find('textarea').val(value).attr({'name' : 'extra_fileds['+name+']'});
		field.find('.extra_field_delete, .extra_field_update').attr({'data-extra_index' : extraFiled.counter});
		nameEl.val(''); valueEl.val('');
		$('.new_extra_fields').append(field.removeClass('prototype none'));
		extraFiled.counter++;
		extraFiled.cancel();
	}
	
	
	
	this.cancel = function(){
		$('#select_extra_name').removeClass('none');
		$('#input_extra_name').addClass('none');
		$('#init_new_extra').text('Введите новое');
		this.state = !this.state;
	}
	
	$(function(){
		$('#add_new_extra_name').click(extraFiled.set);
		$('#init_new_extra').click(function(e){
			extraFiled.setNew(e);
		});
		
		$('.new_extra_fields').click(function(e){
			var className = '.' + e.target.className;
			var field = $('[data-extra_index="'+$(e.target).data('extra_index')+'"]').closest('.field');
			switch(className){
				case '.extra_field_delete':{
					field.remove();
					if(!$('.new_extra_fields > .field').length){
						$('.fields_caption').addClass('none');
					}
				}break;
				case '.extra_field_update':{
					field.find('textarea').attr('name', 'extra_fileds['+field.find('.extra_name').val()+']');
				}break;
			}
		});
	}); 
}

var extraFiled = new ExtraFiled();
var shower = new Shower();

function Shower(cl)
{
	if(!$('#shower').length){
		$('body').after('<div id="shower"><span></span><div id="img"><img src="" alt="Просмотр изображения"><div  id="showerTools"> <div></div><div></div> </div> <div id="counter">0 / 0</div> <div id="close">x</div></div></div>');
	}
	var cl = cl || '.shower';
	var $imgs = $('img'+cl);
	var $wrapper = $('#shower');
	var $imgWrap = $('#shower > #img');
	var $img = $imgWrap.children('img');
	var self = this;
	
	this.index = 0;
	
	$imgWrap.children('#counter').html((this.index+1)+' / '+$imgs.length);
	
	this.get = function(img){
		var newSrc = $(img).attr('data-large-img') ? 'data-large-img' : 'src';
		$img.attr({'src':$(img).attr(newSrc)});
		
		if(!$wrapper.hasClass('block')){
			$wrapper.addClass('block');
			$wrapper.animate({'opacity': 1}, 500);
		}
		if($imgWrap.height() > $(window).height() - 30) {
			$img.css('max-height', $(window).height() - 150);
			$img.css('min-width', 'auto');
		}
		$imgWrap.css({
			'margin-left': ((-$imgWrap.width()/2-20)+'px'), 
			'margin-top': (($imgWrap.height() > $(window).height() ? 0 : 20)+'px'), 
			'visibility':'visible'
		});
		
		$img.animate({'opacity': 1}, 300);
	}
	
	this.hide = function(){
		self.index = 0;
		self.setCounter();
		$wrapper.animate({'opacity': 0}, 500, function(){
			$wrapper.removeClass('block');
			$imgWrap.css({'visibility':'hidden'});
		})
	}
	
	this.getIndex = function(src){
		var index = 0;
		$imgs.each(function(i, img){
			if($(img).attr('src') == src){
				index = i;
				return false;
			}
		});
		
		return index;
	}
	
	this.setCounter = function(){
		$imgWrap.children('#counter').html((self.index+1)+' / '+$imgs.length);
	}
	$(function(){
		$('body').on('click', 'img'+cl, function(){
			self.index = self.getIndex($(this).attr('src'));
			self.setCounter();
			self.get(this);
		});
		// $imgs.click(function(){
			// self.index = self.getIndex($(this).attr('src'));
			// self.setCounter();
			// self.get(this);
		// });
		
		$('#shower #close, #shower > span').click(function(){
			self.hide();
		});
		
		$('#showerTools div:first').click(function(){
			
			self.index--;
			if(self.index < 0){
				self.hide();
				return false;
			}else
				$img.animate({'opacity': 0}, 300, function(){self.get($imgs[self.index])});
				
			
			self.setCounter();
		});
		
		$('#showerTools div:last').click(function(){
			self.index++;
			if(self.index >= $imgs.length){
				self.index = 0;
				self.hide();
				return false;
			}else
				$img.animate({'opacity': 0}, 300, function(){self.get($imgs[self.index])});
			
			self.setCounter();
		});
	});
}

/*post capture options*/

$('#post-options-box > label > input').change(function(){
	var forBlock = $(this).data('for');
	if(!this.checked) 
		$(forBlock).addClass('none'); 
	else 
		$(forBlock).removeClass('none')
});


let draggable = false;
;(function($){
	let slctr = '.mytable.posts';
	let activated = false;
	
	draggable = function (activate = false) {console.log(activated);
		if (!activate) {
			if (!activated) return;
			activated = false;
			$(slctr).off('drop dragover').removeAttr('id');
			$(slctr + ' tr').off('dragstart').attr('draggable', false);
		} else if (!activated) {
			activated = true;
			$(slctr).attr('id', 'draggable');
			
			setTimeout(function(){
				$('#draggable' + slctr).on('drop', function (ev) {
					ev.originalEvent.preventDefault();
					var data = ev.originalEvent.dataTransfer.getData("text");

					thisdiv = ev.originalEvent.target;
					thisdiv = $(thisdiv).closest('tr');
					$(document.getElementById(data)).insertBefore(thisdiv);
					console.log(ev);
					let sorted = [];
					$('table.posts tr').each(function(){
						if ($(this).data('post_id')) {
							sorted.push($(this).data('post_id'));
						}
					});
					$.post(root + 'admin/'+ postType +'/changeOrderValue/', {sorted});
					console.log(sorted);
				}).on('dragover', function (ev) {
					ev.originalEvent.preventDefault();
				});
				 
				$('#draggable' + slctr + ' tr').on('dragstart', function (ev) {
					var e = ev.originalEvent;
					e.dataTransfer.setData("text", e.target.id);
				}).attr('draggable', true);
				
			}, 1);
		}
	}
	
	
	function sort(arr, index){
		var index = index || 'sort',
			min, k, buff, newArr = [];
		for(var i=1;i<arr.length;i++){
			if(typeof arr[i] == "undefined" || !$(arr[i]).data('post_id')) continue;
			min = parseInt($(arr[i]).data('post_id'));
			k = i;
			for(var j=i+1;j<arr.length;j++){
				if(typeof arr[j] == "undefined" || !$(arr[i]).data('post_id')) continue;
				if(parseInt($(arr[j]).data('post_id')) < min){
					min = parseInt($(arr[j]).data('post_id'));
					k = j;
				}
			}
			
			if(k != i){
				buff = arr[i];
				arr[i] = arr[k];
				arr[k] = buff;
			}
			newArr.push(arr[i]);
		}
		//newArr.push(arr[i]);
		//console.log(newArr);
		
		return newArr;
		
		// if (!Array.isArray(arr)) {
			// console.log(Object.values(arr));return;
			// let factArr = [];
			// for (var item in arr) {
				// factArr.push(arr[item]);
			// }
			// arr = factArr;
		// }
		
		// return arr;
	}
	
	$(function(){
		$(slctr + ' tr').each(function(i, item){
			if (!i) return;
			$(item).attr('id', 'postlist-' + i);
		});
			
		$('#order').change(function(){
			let tableCaption = $('table.posts tr:first-child');
			$.post(root + 'admin/'+ postType +'/changeOrder/' + $(this).val() + '/', function(data)
			{
				if ($('#order').val() == 'DISTINCT') {
					draggable(true);
					var sorted = [];
					JSON.parse(data)['ids'].forEach(function(item){
						sorted.push($('table.posts tr[data-post_id='+item+']'));
					});
				} else {
					draggable(false);
					var sorted = sort($('table.posts tr'));
					if ($('#order').val() == 'DESC') {
						sorted = sorted.reverse();
					}
				}
				
				$('table.posts').html('').append(tableCaption);
				sorted.forEach(function(item){
					$('table.posts').append(item);
				});
			});
		});
				
		if ($('#order') && $('#order').val() == 'DISTINCT') {
			draggable(true);
		}
		
	});
})(jQuery);



$(function(){
	
});
