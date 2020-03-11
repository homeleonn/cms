function Slider(element){
	var list   = [];
	[].forEach.call($('.'+element+' .item'), function(l1){
		list.push(l1);
	});
	
	this.element = $('.'+element+' .ss');
	this.count = list.length;
	this.list = list;
	this.op = true;
	this.sliderTimer;
	this.sliderProgressBarTimer;
	var self = slider = this;
	
	delete(list);

	this.run = function(timeout){
		var timeout = timeout || 5000;
		this.sliderTimer = setTimeout(function(){
			self.element.parent().find('.arr-right').click();
		}, timeout);
	}
	
	this.stop = function(){
		clearTimeout(this.sliderTimer);
		clearTimeout(this.sliderProgressBarTimer);
	}

	this.go = function(active, next, mode, type){
		$(next).addClass(mode);
		$(next)[0].offsetWidth;
		$(active).addClass(type);
		$(next).addClass(type);
		setTimeout(function(){
			$(active)
				.removeClass('active')
				.removeClass(type);
			$(next)
				.removeClass(type)
				.removeClass(mode)
				.addClass('active');
				
			slider.opTrue();
		}, 500);
	}
	
	
	this.next = function(type, self){
		//clearTimeout(frontSliderTimer);	
		this.parent(self);
		var active;
		if(!(active = this.oport())) return false;
		var type = type || 'left';
		var mode = type == 'left' ? 'next' : 'prev';
		var next = type == 'left' ? $(active).next() : $(active).prev();
		if(!next.length){
			next = $(this.element).find(' .item:' + (type == 'left' ? 'first' : 'last'));
		}
		var nextImg = next.children('img');
		var src = nextImg.attr('data-src');
		if(src){ 
			nextImg.removeAttr('data-src');
			nextImg.attr('src', src);
		}
		$(this.element).next().find('img').removeClass().eq(next.index()).addClass('active');
		this.go(active, next, mode, type);
	}
	
	this.identNext = function(elem){
		this.element = elem.closest('.thumbs').prev().children('.ss');
		if(elem.hasClass('active')) return;
		var active;
		if(!(active = this.oport())) return false;
		$(this.element).parent().next().find('img').removeClass();
		elem.addClass('active');
		var 
			mode = 'next',
			type = 'left';
			
		
		this.go(active, $(this.element).children('div').eq(elem.index()), mode, type);
	}
	
	this.parent = function(self){
		this.element = $(self).closest('.slider').eq(0);
	}
	
	this.active = function(){
		return $(this.element).find('.active');
	}
	
	this.opTrue = function(){
		this.op = true;
	}
	
	this.oport = function(){
		if(!this.op) return false;
		this.op = false;
		return this.active();
	}
		
	this.nextItem = function(){
		var current = this.count + 1;
		return this.list[current < this.count ? current : 1];
	}
	
	this.previous = function(){
		var current = this.count - 1;
		return this.list[current ? current : this.count];
	}
	
	this.getList = function(){
		return this.list;
	}
	
	this.getActive = function(){
		return $('.slider .ss').find('.item.active').next().css('display','block');
	}
	
	this.goProgress = function(width, $progressbar){
		//return false;
		if(yout) return;
		var width = width || 0;
		if(!width) clearTimeout(sliderProgressBarTimer);
		sliderProgressBarTimer = setInterval(function(){
			if((width += 0.85) >= 100) {
				clearTimeout(sliderProgressBarTimer);
			}
			$progressbar.css('width', width + '%');
		}, 40);
	}
	
	$(function(){
		$('.thumbs img').click(function(){
			slider.identNext($(this));
		});
		
		$('.controls > .arr-left').click(function(){
			slider.next('right', $(this));
		});
		
		$('.controls > .arr-right').click(function(){
			slider.next('left', $(this));
		});
		
		$('.controls > .arr-right, .controls > .arr-left').click(function(){
			slider.stop();
			if($(window).width() < 480) return;
			var self = this;
			var width = 0;
			var $progressbar = $(self).closest('.slider-wrapper').find('.progressbar');
			$progressbar.css('width', width + '%');
			yout = false;
			slider.run();
			//self.goProgress(width, $progressbar);
		});
		//slider.goProgress(0, $('.main .slider-wrapper .progressbar'));
		
		
		$('.main .slider-wrapper')
		.on('mouseover', '.item',
			function() {
				slider.stop();
			}.bind(this)
		).on('mouseout', '.item',
			function() {
				slider.stop();
				if(yout) return;
				var currentWidth = $('.main .slider-wrapper').find('.progressbar').width() / $('.main .slider-wrapper').find('.ss').width() * 100;
				slider.run();
				//runFrontSlider(5000 - 5000 * (currentWidth / 100));
				//slider.goProgress(currentWidth, $('.main .slider-wrapper').find('.progressbar'));
			}
		);
	});
}

// $$(() => {
	// var ww = $(window).width();
	// var wcenter = ww / 2;
	// var t0;
	// var move, index = 0;
	
	// build();
	// var slideNew = document.getElementById('s1');

	// slideNew.onmousedown = function(e) {
		// t0 = performance.now();
		// $('#s1').stop();

	  // var coords = getCoords(slideNew);
	  // var shiftX = e.pageX - coords.left;
	  
	  // move = coords.left;

	  // slideNew.style.zIndex = 1000;

	  // function moveAt(e) {
		// slideNew.style.left = e.pageX - shiftX + 'px';
	  // }

	  // document.onmousemove = function(e) {
		// moveAt(e);
	  // };

	  // document.onmouseup = function() {
		// document.onmousemove = null;
		// slideNew.onmouseup = null;
		
		// move -= getCoords(slideNew).left;
		
		// if (performance.now() - t0 < 200) {
			// index = move > 0 ? index + 1 : index - 1;
			
			// if (index < 0) index = 0;
			// else if (index + 1 > $('.item').length) index = $('.item').length - 1;
		// } else {
			// var center = false;
			// var coords;
			// $('.item').each(function(i){
				// coords = $(this).offset().left + ($(this).width() / 2) - wcenter;
				
				
				// if (center === false || Math.abs(coords) < Math.abs(center)) {
					// center = coords;
					// index = i;
				// }
			// });
		// }
		
		// makeActive(index);
	  // };
	// }

	// slideNew.ondragstart = function() {
	  // return false;
	// };

	// function getCoords(elem) {   // кроме IE8-
	  // var box = elem.getBoundingClientRect();
	  // return {
		// left: box.left + pageXOffset
	  // };
	// }
	
	// function build(){
		// $('.slider-new .item').css('width', ww * 0.4 + 'px');
		// makeActive(0);
	// }
	
	// function makeActive(index){
		// var parentPos = $('.slider-new .item').parent().offset();
		// var $activeItem = $('.slider-new .item:nth-child('+(index + 1)+')');
		// var left = wcenter - $activeItem.width() / 2;
		// left -= $activeItem.offset().left - parentPos.left;
		
		// setTimeout(() => {$('.slider-new .item').removeClass('active');}, 0);
		// setTimeout(() => {$activeItem.addClass('active');}, 0);
		// $('#s1').stop().animate({'left': left}, 1000);
		// setTimeout(() => {$('#s1')}, 1000);
		// //go(left)();
		
	// }
	
	// function go(left){
		// var currentPos = +$('#s1').css('left').split('px')[0];
		// var stepCount = 40;
		// var step = left / stepCount;
		// var up = currentPos < left;
		
		
		// return function(){
			// if (--stepCount) {
				// currentPos = up ? currentPos + step : currentPos - step;
				// setTimeout(() => {$('#s1').css({'left': currentPos});}, 25);
				// console.log(1);
			// }
		// }
	// }
	
	// function fastScroll(){
		
	// }
// });

// Carousel widget
(function($){
	var widgetSlide = false,
		resizeCheckDelay = 1000,
		widgetLevel = 0, 
		widgetOffset = false,
		goZeroMargin = false;
		
	var fnForResize = fnOnTimeout(carouselWidgetResize);
	
	$(function(){
		if(!$('.carousel-widget').length) return;
		$('.carousel-widget .controls').click(function(e){
			if(widgetSlide) return;
			var $widget 		= $(this).closest('.carousel-widget');
			var $content 		= $widget.find('.widget-content');
			var $insideContent 	= $widget.find('.inside-content');
			var columns 		= $widget.data('carousel-widget-column-mobile') ?   $widget.data('carousel-widget-column-mobile'):
																					$widget.data('carousel-widget-column');
			var items 			= $insideContent.find('.item').length;
			if(items < columns) columns = items;
			var currentMargin 	= parseFloat($insideContent.css('margin-left'), 10);
			var right 			= e.target.className == 'rightside';
			var offset 			= ($content.width() + 30) / columns;
			var marginLimit 	= items * offset - (offset * columns);
			
			if(!right && !parseInt(currentMargin, 10))
				var margin = -marginLimit;
			else if(right && parseInt(currentMargin, 10) == -parseInt(marginLimit, 10)) 
				var margin = 0;
			else
				var margin = currentMargin + (right ? -offset : offset)
			
			widgetSlide = true;
			$insideContent.animate({'margin-left': margin + 'px'}, 200, function(){
				widgetSlide = false;
			});
		});
		
		
		carouselWidgetResize();
		$(window).resize(function(){
			fnForResize();
		});
	});
	
	function carouselWidgetResize(){
		if($(window).width() > 620 && $(window).width() < 990 && widgetLevel != 1){
			widgetOffset = true;
		}else if($(window).width() > 996 && $(window).width() < 1050 && widgetLevel != 2){
			widgetOffset = true;
			widgetLevel = 2;
		}else if($(window).width() > 1060 && $(window).width() < 1200 && widgetLevel != 3){
			widgetOffset = true;
			widgetLevel = 3;
		}else if($(window).width() > 1202 && $(window).width() < 2000 && widgetLevel != 4){
			widgetOffset = true;
			widgetLevel = 4;
		}else if($(window).width() < 600){
			widgetOffset = true;
			widgetLevel = 6;
		}
		
		if(widgetOffset){
			var columns;
			if($(window).width() < 600){
				columns = 1;
				widgetLevel = 0;
			}else{
				columns = false;
			}
			
			$('.carousel-widget').data('carousel-widget-column-mobile', columns);
			$('.carousel-widget').each(function(i){
				carouselWidgetColumns(this, columns);
			});
			widgetOffset = false;
		}
	}
	
	
	function carouselWidgetColumns(elem, columns){
		var columns = columns || $(elem).data('carousel-widget-column');
		var items 	= $(elem).find('.item').length;
		if(items < columns) {
			columns = items;
		}
		$(elem).find('.item').css('width', ($(elem).find('.widget-content').width()) / columns - (columns > 1 ? 5 + columns * 5 : 0));
		var $inside = $(elem).find('.inside-content');
		if(parseFloat($inside.css('margin-left'), 10) && !goZeroMargin){
			goZeroMargin = true;
			$inside.animate({'margin-left': 0 + 'px'}, function(){
				goZeroMargin = false;
			});
		}
	}

})(jQuery);



function isFrontPage(){
	return document.location.pathname == root;
}

// toggle animation
function toggleForScroll(to, className, scrollPx, callbackOff, callbackOn, delay){
	var toggle = false, timeout = false, delay = delay || 1000;
	
	var fn = fnOnTimeout(function(){
		if((!toggle && $(window).scrollTop() >= scrollPx) || (toggle && $(window).scrollTop() < scrollPx)){
			$(to).toggleClass(className);
			var callback = (toggle = !toggle) ? callbackOff : callbackOn;
			if(callable(callback)) callback();
		}
	}, 2000);
	
	$(window).scroll(function(e){
		fn();
	});
}

function fnOnTimeout(callback, delay){
	var timeout = false,
		delay = delay || 1000;
	
	return function(){
		if(!timeout){
			timeout = true;
			setTimeout(function(){
				callback();
				timeout = false;
			}, delay);
		}
	}
}

function callable(callback){
	return typeof callback == 'function';
}

function animate(){
	$('.wrapper.main').toggleClass('animate');
	return setInterval(function(){
		if(!$('body').hasClass('offanimate'))
			$('.wrapper.main').toggleClass('animate');
	}, 20 * 1000);
}




var yout = false, slider, animateId;
$(function(){
	Shower('.shower');
	//setTimeout(function(){$('body').addClass('loaded');}, 2000);
	$('body').addClass('loaded');
	
	$('.get-review-form').click(function(e){
		e.preventDefault();
		note.get('Оставить отзыв', 4);
		$('#captcha').prop('src', root + 'get-captcha-for-comment/');
	});
	
	if(isFrontPage()){
		slider = new Slider('slider');
		slider.run();
		animateId = animate();
		toggleForScroll('body', 'offanimate', 900, 
			function(){
				clearTimeout(animateId);
				$('.wrapper.main').removeClass('animate');
			},
			function(){
				animateId = animate();
			}
		);
		
		$(".front-menu").on("click","a", function (e) {
			e.preventDefault();
			var id  = $(this).attr('href'),
				top = $(id).offset().top;
			$('body,html').animate({scrollTop: top - 50}, 500);
		});
		
		
	}else{
		
		(function(){
			if($('.heroes-catalog-wrapper').length){
				$('.heroes-catalog .list').one('mouseover', function(){
					$('.heroes-catalog img').each(function(){
						var data = $(this).data('src');
						if(data){
							this.src = data;
						}
					});
				});
				
				// var fn = function(){
					// var block = $('.heroes-catalog-wrapper');
					// var blockHeight = block.height();
					// var parentBlockHeight = $('.heroes-catalog-wrapper').parent().height();
					// var parentTop = $('.heroes-catalog-wrapper').parent().offset().top;
					// var limit = parentBlockHeight;
					
					// return function(){
						// if($(window).scrollTop() > parentTop){
							// var margin = $(window).scrollTop() - parentTop;
							// if(margin > limit) margin = limit;
							// block.css('margin-top', margin);
						// }else{
							// block.css('margin-top', 0);
						// }
						// console.log($(window).scrollTop(), blockHeight, parentBlockHeight, parentTop, limit);
					// }
					
				// }
				// var fn = fnOnTimeout(fn(), 500);
				
				// $(window).scroll(function(e){
					// fn();
				// });
			}
		})();
	}
	
	
	
	
	
	setInterval(function(){
		$('.phone').toggleClass('red');
	}, 5000);
	
	if(document.location.host.indexOf('webhostapp.com') + 1){
		$('img[alt="www.000webhost.com"]').remove();
	}
	
	
	$('.slider-wrapper .yout').click(function(){
		yout = true;
		var img = $(this).find('img');
		if(!img.length) return;
		$(this).closest('.item').html('<iframe width="1120" height="385" src="https://www.youtube.com/embed/'+img.data('youtube')+'/?autoplay=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>');
		if($(this).closest('.wrapper.main').length){
			slider.stop();
		}
	});
	
	$('.yout1').click(function(){
		var img = $(this).find('img');
		if(!img.length) return;
		$(this).html('<iframe width="1120" height="385" src="https://www.youtube.com/embed/'+img.data('youtube')+'/?autoplay=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>');
	}); 
	
	
	
	
	$('.up').click(function(){
		$('html, body').animate({
			scrollTop: 0
		}, 'fast');
	});
	
	// fix menu
	toggleForScroll('nav.menu', 'fixed', 100);
	
	$('nav .top-menu').click(function(){
		$(this).toggleClass('visible');
	});
	
	
	
	$('.phone, .phone-top, .bottom-phone .icon-phone').click(function(e){
		e.preventDefault();
		note.get('Заказать обратный звонок', 3);
	});
	
	
	$('body').on('keyup', 'textarea.limit-symbols', function(){
		var limit = $(this).data('limit');
		if(!limit) return;
		if($(this).next()[0].className != 'limit'){
			$(this).after('<p class="limit"></p>');
		}
		$(this).next().text('Осталось: '+ (parseInt($(this).data('limit'), 10) - $(this).val().length) + ' символов');
	});
	
});

var menuFixed = false;

function requestSet(type, el){
	var type = type || 2;
	var data = {};
	var title = 'Сообщение';
	var $wrapper = $(el).closest('.request-wrapper');
	
	var $captcha = $wrapper.find(".captcha-wrapper");
	
	if(!$captcha.hasClass('none')){
		var captcha = $captcha.find('.captcha-code').val();
		if(!captcha.length){
			fillIErrorInput($captcha.find('.captcha-code'));
			$captcha.find('.captcha-reload').click();
			return;
		}
	}
	switch(type){
		case 3:{
			var $tel = $wrapper.find('.tel');
			if(!$tel.val()) {fillIErrorInput($tel);return;}
			if(checkTel($tel.val())){
				data.type 	= type;
				data.tel 	= $tel.val();
				data.captcha= captcha;
				title = 'Обратный звонок';
			}else{
				fillIErrorInput($tel);
				return;
			}
		}break;
		case 4:{
			var $name = $wrapper.find('.name');
			var $text = $wrapper.find('textarea.text');
			if(!$name.val()) {fillIErrorInput($name);return;}
			if(!$text.val()) {fillIErrorInput($text);return;}
			data.type 	= type;
			data.name 	= $name.val();
			data.text 	= $text.val();
			data.captcha= captcha;
			title = 'Отзыв';
		}break;
	}
	
	$.post(root + 'reviews/mail/', data, function(msg){
		if(msg == 999){
			note.get('Ошибка', 'Неопределенная ошибка');
		}else if(msg == 0){
			fillIErrorInput($captcha.find('.captcha-code'));
			$captcha.find('.captcha-reload').click();
		}else if(msg == 1){
			$captcha.removeClass('none');
			$captcha.find('img.captcha').prop('src', root + 'get-captcha-for-comment/');
			return;
		}else{
			note.get(title, msg);
		}
	});
}

function fillIErrorInput($input){
	$input.css('border', '2px red solid');
}

var note = new Note();
function Note(){
	this.vis = true;
	var forms = {
		'callme' : 
			'<div id="callme" class="request-wrapper center">\
				<input type="text" class="tel" name="tel" value="" placeholder="Ваш телефон">\
				<input type="button" class="send" onclick="requestSet(3, this)" value="Отправить">\
			</div>',
		
		'reviewForm' : 
			'<div id="review-form" class="request-wrapper">\
				<input type="text" class="name" placeholder="Ваше имя">\
				<textarea placeholder="Введите Ваш отзыв" class="text limit-symbols" data-limit="500" rows="5"></textarea>\
				<input type="button" class="send" onclick="requestSet(4, this)" value="Отправить">\
			</div>'
	},
	frame = '<div id="note-wrap"><span onclick="note.hide()"></span><div id="note"><div id="note-title"></div><div id="note-content"></div><div class="center"><button id="note-submit" class="button7">Отправить</button>	<button id="note-close" onclick="note.hide()">X</button></div></div></div>';
	
	function loadFrame(){
		if(frame){
			$('body').append(frame);
			frame = false;
		}
	}
	
	this.get = function(title, content, submit){
		loadFrame();
		var title 		= title   || 'Уведомление';
		var content 	= content || 'Текст';
		var submit 	= submit  || false;
		
		
		$('#note-title').html(title);
		if(content == 2){
			this.clone('#order-question');
		}else if(content == 3){
			this.setForm(forms.callme);
		}else if(content == 4){
			this.setForm(forms.reviewForm);
		}else{
			$('#note-content').html(content);
		}
		
		if(content == 3 || content == 4){
			$('#captcha-wrapper').clone().addClass('none').insertBefore($('#note-content .send'));
		}
		
		if(submit) $('#note-submit').addClass('block');
		
		$('#note-wrap').addClass('block');
		$('#note')[0].offsetWidth;
		$('#note').css({marginTop: ($(window).height() < 470 ? '0' : '10%')});
		$('body').css('overflow', 'hidden');
		this.vis = false;
	}
	
	this.hide = function(){
		if(this.vis) return;
		$('#note').css({marginTop: '-400px'});
		setTimeout(function(){
			$('#note-wrap, #note-submit').removeClass('block');
			$('body').css('overflow', 'auto');
		}, 500);
	}
	
	this.clone = function(from){
		$('#note-content').html('');
		$(from).clone(true).addClass('block').appendTo('#note-content');
	}
	
	this.setForm = function(form){
		$('#note-content').html(form);
	}
}


// lazyloading
;(function($){
	var lazyImgs = [];
	var prevScrollTop = 0;
	var step = 200;
	var winHeight = $(window).height()
	var beforeImgStep = winHeight + 700;
	var find = false;
	var fn = fnOnTimeout(() => {
		var scroll = $(window).scrollTop()
		if(scroll > prevScrollTop + step)
		{
			prevScrollTop = scroll;
			
			lazyImgs.forEach(function(item, i){
				if(item[2] - (beforeImgStep) < prevScrollTop){
					find = true;
					delete(lazyImgs[i]);
					
					var newImg = new Image();
					newImg.onload = function(){
						item[0].src = item[1];
					}
					newImg.src = item[1];
				}
			});
			
			if(find) {
				find = false;
				recounting();
			}
		}
	}, 500);
	
	function recounting(){
		lazyImgs.forEach(function(item, i){
			lazyImgs[i][2] = $(item[0]).offset().top;
		});
	}
	
	
	
	$(function(){
		$('img.lazy').each(function(){
			if(this.dataset['src'])
				lazyImgs.push([this, this.dataset['src'], $(this).offset().top]);
		});
		
		$(window).scroll(function(){
			fn();
		});
	});
})(jQuery);



function Shower(cl)
{
	if(typeof window.showerPluginLoad != "undefined") return false;
	window.showerPluginLoad = true;
	
	$('body').one('click', 'a' + cl, function(e){
		e.preventDefault();
		new Shower1(cl);
		$(this).click();
	});
	
	function Shower1(cl){
		$('body').append('<div id="shower"><div id="shower-tools"><div id="shower-prev"></div><div id="shower-next"></div></div><span></span><div id="img"><img src="" alt="Просмотр изображения"><div id="counter">0 / 0</div><div id="shower-title"></div><div id="close">x</div></div></div>');
		var cl = cl || '.shower';
		
		var $imgs = $('a' + cl);
		var $wrapper = $('#shower');
		var $imgWrap = $('#shower > #img');
		var $img = $imgWrap.children('img');
		var $title = $imgWrap.children('#shower-title');
		var self = this;
		var isChrome = !!window.chrome && (!!window.chrome.webstore || !!window.chrome.runtime);
		
		this.index = 0;
		
		$imgWrap.children('#counter').html((this.index+1)+' / '+$imgs.length);
		
		this.get = function(src){
			//var newSrc = $(img).attr('data-large-img') ? 'data-large-img' : 'src';
			//$img.attr({'src':$(img).attr(src)});
			var ww = $(window).width();
			var wh = $(window).height();
			
			var showImg = new Image();
			showImg.onload = function(){
				$img.attr({'src':src.href});
				console.log($img[0].naturalWidth, $img[0].naturalHeight);
				if(!$wrapper.hasClass('block')){
					$wrapper.addClass('block');
					$wrapper.animate({'opacity': 1}, 500);
				}
				
				var nw = showImg.naturalWidth;
				var nh = showImg.naturalHeight;
				
				$img.animate({'width': 'auto', 'height': 'auto','max-height': wh - 100}, 50, function(){
					//$img.css({'max-height': wh - 100});
					// console.log($img.width(), $img.height());
					// var h = $img.height();
					// if(h != nh){
						// var newWidth = nw * (h / nh);
						// if(newWidth > ww){
							// $img.css('height', nw * (newWidth / nw));
						// }
						// console.log(newWidth, h, nh);
						// $img.css('width', newWidth);
					// }
					
					$imgWrap.css('visibility','visible');
					$imgWrap.animate({
						'margin-left': ((-$imgWrap.width()/2-20)+'px'), 
						'margin-top': (($imgWrap.height() > wh ? 0 : 20)+'px')
					}, 50);
					
					$img.animate({'opacity': 1}, 300);
				});
			}
			showImg.src = src.href;
			$title.text(src.title);
			self.setCounter();
		}
		
		this.hide = function(){
			self.index = 0;
			$wrapper.animate({'opacity': 0}, 500, function(){
				$wrapper.removeClass('block');
				$imgWrap.css({'visibility':'hidden'});
				self.setCounter();
			})
		}
		
		this.getIndex = function(img1){
			var index = 0;
			$imgs.each(function(i, img){
				if($(img)[0] == $(img1)[0]){
					index = i;
					return false;
				}
			});
			
			return index;
		}
		
		this.setCounter = function(){
			$imgWrap.children('#counter').html((self.index+1)+' / '+$imgs.length);
		}
		
		$('#shower #close, #shower > span').click(function(){
			self.hide();
		});
		
		$('#shower-prev').click(function(){
			self.index--;
			if(self.index < 0){
				self.hide();
				return false;
			}else
				$img.animate({'opacity': 0}, 300, function(){self.get($imgs[self.index])});
				
			
			self.setCounter();
		});
		
		$('#shower-next, #shower img').click(function(){
			self.index++;
			if(self.index >= $imgs.length){
				self.index = 0;
				self.hide();
				return false;
			}else
				$img.animate({'opacity': 0}, 300, function(){self.get($imgs[self.index])});
		});
		
		$('body').on('click', 'a' + cl, function(e){
			e.preventDefault();
			self.index = self.getIndex(this);
			self.get(this);
		});
	}
}

function findActiveLink(parent, href = document.location.href){
	return $(parent + ' a[href="'+href+'"]');
}
	
$(function(){
	$('.nav a[href="'+document.location.pathname+'"]').addClass('active');
	if(document.location.pathname.toString() == root){
		$('.menu a[href="'+root+'"]').addClass('active');
	}else{
		var parts = document.location.pathname.split(root)[1].split('/');
		$('.menu a[href="'+root+parts[0]+'/"]').addClass('active');
	}
	var $active = $('.menu a[href="'+document.location.pathname+'"]');
	if(!$active.parent('li').length){
		$active.closest('li.top-menu').addClass('active');
	}else{
		$active.parent().addClass('active').closest('li.top-menu').addClass('active')
	}
	
	if ($('.filters').length) {
		let $activeFilter = findActiveLink('.filters');
		if ($activeFilter.length) {
			$activeFilter.addClass('active');
		} else {
			findActiveLink('.filters', document.location.href.split(/page\/\d+\//)[0]).addClass('active');
		}
	}
	
	$('.heroes-catalog a[href="'+document.URL+'"]').addClass('active');
	
	$('#comments-block-form').submit(function(e){
		e.preventDefault();
		var self = this;
		var comment = $(this.elements.content).val().replace(/\s+/g, ' ');
		var login = $(this.elements.login).val().replace(/\s+/g, ' ');
		if(comment == '' || comment == ' ')	{alert('Введите сообщение!');return;}
		if(login == '' || login == ' ')		{alert('Введите ваше имя!');return;}

		var comment_count = $('#comment-count').text();
		$.post(root + 'user/comments/add/' + $(this.elements.post_id).val() + '/', {
			login: login,
			comment: comment,
			comment_parent: $(this.elements.parent).val(),
			comment_count: comment_count,
			captcha_code: $(this.elements.captcha_code).val(),
		}, function(data){
			if (typeof data.error !== 'undefined') {
				alert(data.error);
				return;
			}
			if(data.comment_parent){
				var $parent = $('[data-id="'+data.comment_parent+'"]');
				var $sub = $parent.find('td.sub-comments');
				
				if($sub.length){
					//$sub.append(data.comment);
					$(data.comment).insertBefore($('#comments-block-form'))
				}else{
					$parent.append('<tr><td colspan="3" class="sub-comments"><div>Ответы (1)</div>' + data.comment + '</td></tr>');
				}
				
			}else{
				$('#post-comments .block-content').prepend(data.comment);
			}
			$('#comment-count').text((+comment_count+1));
			$('#comment-text, #captcha-code').val('');
			$('#comment-parent').val('0');
			captchaReload();
		}, 'json');
		
	});
	
	$('table .icon-comment, table .comment-author').click(function(){
		var item = $(this).closest('table');
		//console.log($(item));
		$(item).after($('#comments-block-form'));
		$('#comment-text').val((!item.hasClass('general') ? '+' : '')+item.data('author') + ', ' + $('#comment-text').val()).focus();
		$('#comment-parent').val(item.hasClass('general') ? item.data('id') : item.closest('table.general').data('id'));
		window.scrollTo(0, $('#comment-text').offset().top - 300);
	});
	
	$('body').on('click', '.captcha-reload', function(){
		captchaReload(this);
	});
	
	
	
	// (function(){
		// if ($('.header').css('background-image').substr(0, 3) == 'url') {
			// let $header = $('.header'),
			// $headerHeight = $header.height();
			// $('<div/>').css({'height':$header.height(), 'background': 'blue', 'position':'absolute', 'top':0, 'width':'100%'}).prependTo($header);
			// // var headerImgSlide = fnOnTimeout(function(){
				
			// // }, );
			
			// $header.css({'z-index': '-1', 'transition': '.05s'});
			// $(window).scroll(function(e){
				// let wscrll = $(window).scrollTop();
				// if (wscrll < 500) {
					// $header.css('transform', 'translate3d(0,'+(wscrll/2)+'px,0)');
				// }
			// });
		// }
	// })();
	
	
});

function captchaReload(el){
	if(typeof captcha1 != 'undefined' && captcha1) return false;
	captcha1 = true; 
	console.log($(el), el);
	var $captcha = $(el).closest('.captcha-wrapper').find('.captcha');
	$captcha.addClass('rotate1').attr('src', root+'get-captcha-for-comment/?async&'+Math.random())
	setTimeout(function(){ 
		$captcha.removeClass('rotate1');
		captcha1 = false; 
	}, 1000);
}

function drawCaptchaFrame(){
	$('body').append('<div class="captcha-wrapper none" id="captcha-wrapper"><img alt="captcha" class="captcha pointer captcha-reload" src=""><span class="icon-arrows-cw captcha-reload" title="Обновить капчу"></span><br>Введите символы с картинки<input type="text" class="captcha-code"></div>');
}

;(function($){
	$(function(){
		drawCaptchaFrame();
		$('#order-question #q-set').click(function(e){
			e.preventDefault();
			var $parent = $(this).closest('#order-question');
			var name = $parent.find("#qname").val();
			var tel  = $parent.find("#qtel").val();
			var mail = $parent.find("#qmail").val();
			var message = $parent.find("#qq").val();
			var captcha = '';
			
			if(!message) message = '';
			if(!name.length){
				note.get('Ошибка', 'Введите Ваше имя');
				return;
			}
			
			
			if(!checkMail(mail)){
				note.get('Ошибка', 'Почта введена некорректно. Проверьте данные');
				return;
			}
			
			if(!checkTel(tel)){
				return;
			}
			
			
			var $captcha = $parent.find(".captcha-wrapper");
			
			if(!$captcha.hasClass('none')){
				var captcha = $captcha.find('.captcha-code').val();
				if(!captcha.length){
					note.get('Ошибка', 'Введите защитный код');
					return;
				}
			}
			
			$.post(root + 'reviews/mail/', {type:2,name:name,tel:tel,mail:mail,text:message,captcha:captcha}, function(msg){
				if(msg == 0){
					note.get('Сообщение', 'Неверно введен защитный код');
					$captcha.find('.captcha-reload').click();
				}else if(msg == 1){
					$captcha.removeClass('none');
					$captcha.find('img.captcha').prop('src', root + 'get-captcha-for-comment/');
					return;
				}else{
					$parent.find("#qname, #qtel, #qmail, #qq, #captcha-code").val('');
					if(captcha.length)
						$captcha.find('.captcha-reload').click();
					note.get('Сообщение', msg);
				}
			});
		});
	});
})(jQuery);






function checkTel(tel){
	var telPattern = /^(\+\d+(-|\s)?)?\(?\d{3}\)?(-|\s)?\d{3}(-|\s)?\d{2}(-|\s)?\d{2}$/;
	if(!telPattern.test(tel)){
		note.get('Ошибка', 'Неверный формат номера телефона. Необходимый вид формата (код оператора) 000 00 00');
		return false;
	}
	
	return true;
}

function checkBlurMail(self){
	var mail = $(self).val();
	var color = 'red';
	
	if(checkMail(mail)){
		validmail 	= true;
		color 		= 'green';
	}
	
	$(self).css('border', '2px '+ color +' solid');
}


function checkMail(mail){
	var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,10}\.)?[a-z]{2,10}$/i;
	if(!pattern.test(mail)){
		return false;
	}
	
	return true;
}
	
	
