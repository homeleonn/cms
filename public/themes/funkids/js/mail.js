;(function($){
	$(function(){
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
	
	