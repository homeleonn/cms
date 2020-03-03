
if(!window.jQuery)
	document.write('<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></scr'+'ipt>');

(function(){
	$$(() => {
		
		if(!$('.newslider').length) {
			return;
		}
		setTimeout(() => {
		
		var ww;
		var mobile = $(window).width() < 600;
		var wh;
		var wcenter;
		var t0;
		var move, index = mobile ? 0 : 1;
		var speed = 0.5;
		var stepTime = stepTimeFix = false;
		var left;
		var length = $('.slider-new .item').length;
		var touch;
		
		
			
		build();
		var slideNew = document.getElementById('s1');
		touch = ('ontouchstart' in slideNew);
		
		if (touch) {
			slideNew.addEventListener('touchstart', down, false);
		} else {
			slideNew.addEventListener('mousedown', down, false);
		}
		
		function down(e) {
			t0 = performance.now();
			if (stepTime)
				stepTimeFix = performance.now() - stepTime;
			$('.slider-new').addClass('mdown');
			$('.slider-new #s1').stop();

			var coords = getCoords(slideNew);
			var shiftX = (e.pageX ? e.pageX : e.touches[0].screenX) - coords.left;
			
			move = coords.left;
			//slideNew.style.zIndex = 1000;

			
			document.addEventListener('mousemove', moveAt, false);
			if (touch) 
				document.addEventListener('touchmove', moveAt, false);
			
			function moveAt(e) {//console.log(e);
				// $(slideNew).css({'transform': 'translateX('+((e.pageX ? e.pageX : e.touches[0].screenX) - shiftX) + 'px'+')'});
				$(slideNew).css('left', (e.pageX ? e.pageX : e.touches[0].screenX) - shiftX + 'px');
				
			}
			

			document.addEventListener('mouseup', up, false);
			if (touch) 
				document.addEventListener('touchend', up, false);
			
			function up() {
				document.removeEventListener('mousemove', moveAt);
				if (touch) 
					document.removeEventListener('touchmove', moveAt);
				document.removeEventListener('mouseup', up);
				if (touch) 
					document.removeEventListener('touchend', up);
				
				
				$('.slider-new').removeClass('mdown');
				
				move -= getCoords(slideNew).left;
				//console.log(stepTimeFix, performance.now() - stepTime);
				// if (!move && stepTimeFix > 1000 * speed && performance.now() - stepTime < 1000 * speed){
					// document.removeEventListener('mousemove', moveAt, false);
					// document.removeEventListener('touchmove', moveAt, false);
					// document.removeEventListener('mouseup', up, false);
					// document.removeEventListener('touchend', up, false);
					// return;
				// }
				
				if (!move) return;
				
				stepTime = performance.now();
				if (performance.now() - t0 < 300) {//console.log(stepTimeFix);
					// if (!move && stepTimeFix && stepTimeFix < 1000 * speed){
						// return;
					// }
					index = move > 1 ? index + 1 : index - 1;
				} else {
					var center = false;
					var coords;
					$('.slider-new .item').each(function(i){
						coords = $(this).offset().left + ($(this).width() / 2) - wcenter;
						
						
						if (center === false || Math.abs(coords) < Math.abs(center)) {
							center = coords;
							index = i;
						}console.log(i, index, length);
					});
					
				}
				
				makeActive(index);
			};
		}

		slideNew.ondragstart = function() {
			return false;
		};
		
		function checkLength(){
			if (index < 0) index = length - 1;
			else if (index + 1 > length) index = 0;
			
			return index;
		}
		

		function getCoords(elem) {	 // кроме IE8-
			var box = elem.getBoundingClientRect();
			return {
				left: box.left + pageXOffset
			};
		}
		
		function build(){
			createSizes();
			for(var i=0; i<length; i++){
				$('.slider-new .controls-indicators').append('<div class="citem">');
			}
			
			makeActive(index);
		}
		
		function createSizes(){
			ww = $(window).width();
			wh = $(window).height();
			wcenter = ww / 2;
			var mw, mh;
			if (ww < 600) {
				mw = mh = 1;
			} else {
				mw = 0.75;
				mh = 0.85;
			}
			
			$('.slider-new .item').css('max-width', ww * mw + 'px');
			$('.slider-new .item img').css('max-height', wh * mh + 'px');
		}
		
		function makeActive(index){
			index = checkLength();
			$('.slider-new .citem').removeClass('active');
			$('.slider-new .citem').eq(index).addClass('active');
			var parentPos = $('.slider-new .item').parent().offset();
			var $activeItem = $('.slider-new .item:nth-child('+(index + 1)+')');
			left = wcenter - $activeItem.width() / 2;
			left -= $activeItem.offset().left - parentPos.left;
			
			setTimeout(() => {$('.slider-new .item').removeClass('active');}, 0);
			setTimeout(() => {$activeItem.addClass('active');}, 0);
			$('#s1').stop().animate({'left': left}, 1000 * speed, function(){stepTime = false});
			// $('#s1').stop().animate({  left: '+=50' }, 
				// {
					// step: function () {
						// $(this).css({'transform': 'translateX('+left+'px)'});
						// console.log(1);
					// },
					// duration: 1000 * speed,
					// complete: function(){stepTime = false;}
				// }
			// );
			//$('#s1').stop().css({'transform': 'translateX('+left+'px)'});
			//setTimeout(() => {$('#s1')}, 1000);
			//go(left);
			
		}
		
		function go(left){
			var currentPos = +$('.slider-new #s1').css('transform').split(',')[4];
			console.log(currentPos, left);
			var stepCount = 20;
			var step = left / stepCount;
			var up = currentPos < left;
			
			function repeat(){
				if (--stepCount) {
					currentPos = (up ? currentPos + step : currentPos -step);
					//$('#s1').css({'left': currentPos});
					$('.slider-new #s1').css({'transform': 'translateX('+currentPos+'px)'});
					setTimeout(() => {repeat()}, 25);
				}
			}
			
			repeat();
		}
		
		$('.slider-new .controls > div').on('click', function(){
			console.log(Math.random());
			index = this.className == 'next' ? index + 1 : index - 1;
			makeActive(index);
		});	
		
		$('.slider-new').on('click', '.citem', function(){
			index = $(this).index();
			makeActive(index);
		});	
		
		var remake = fnOnTimeout(function(){createSizes();makeActive(++index);console.log(1);});
		
		$(window).resize(function(){
			remake();
		});
		
		
		}, 1);
		
		
	});
	function newSliderBuild(src){
			// <div class="item"><img src="http://localhost/funkids/content/uploads/2018/09/M28wjj62UMd.jpg"></div>
			// <div class="item"><img src="http://localhost/funkids/content/uploads/2018/09/wNNTI312Te6d.jpg"></div>
			// <div class="item"><img src="http://localhost/funkids/content/uploads/2018/09/1016aExaTNdTN.jpg"></div>
			// <div class="item"><img src="http://localhost/funkids/content/plugins/slider/images/glavnii/1.jpg"></div>
			// <div class="item"><img src="http://localhost/funkids/content/plugins/slider/images/glavnii/1.jpg"></div>
		document.getElementsByClassName('newslider')[0].innerHTML = `
		<div class="slider-new clearfix" id="slider-new">
			<div id="s">
				<div id="s1"></div>
			</div>
			<div class="controls">
				<div class="prev">&#9668;</div>
				<div class="next">&#9658;</div>
			</div>
			<div class="controls-indicators"></div>
		</div>
		`;
		
		src.forEach(function(item){
			//document.getElementById('s1').innerHTML += '<div class="item"><img src="'+theme+'img/1x1.gif" data-src="'+item+'" class="lazy"></div>';
			document.getElementById('s1').innerHTML += '<div class="item"><img src="'+item+'"></div>';
		});
	}
	
	if(document.getElementsByClassName('newslider').length) {
		newSliderBuild(document.getElementsByClassName('newslider')[0].dataset.src.split(','));
	}	
}());




