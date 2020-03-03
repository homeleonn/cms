<link rel="stylesheet" href="<?=PLUGINS?>seo/style.css">



<div id="post-seo" class="side-block">
	<div class="block-title">SEO</div>
	<div class="block-content">
		<b>SEO-заголовок(тег: title):</b><br>
		<input type="text" class="w100" name="_seo_title" value="<?=$title?>"><br>
		<b>Описание(Мета-тег: description):</b><br>
		<input type="text" class="w100" name="_seo_description" value="<?=$descr?>">
		<div id="seo-recommended"><small>* отклонение в несколько символов незначительно</small></div>
	</div>
</div>
<script>

if(!window.jQuery)
	document.write('<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></scr'+'ipt>');

(function(){
	var $title 		= $('[name="_seo_title"]');
	var $desc 		= $('[name="_seo_description"]');
	var $recBlock 	= $('#seo-recommended');
	
	var content;
	
	
	
	function a(item, type, text){
		if(!$recBlock.find('#'+item).length){
			$recBlock.append('<div id="'+item+'"></div>');
		}
		$('#'+item).attr('class', 'seo-'+type).html(text);
	}
	
	function b(){
		var length = $desc.val().length;
		var optimal = 'оптимальная длинна 170-290 символов!';
		if(length > 290)
			a('seo-desc', 'red', ' <b>Мета-описание</b> слишком длинное, '+length+' символов, ' + optimal);
		else if(length < 170)
			a('seo-desc', 'red', ' <b>Мета-описание</b> слишком короткое, '+length+' символов, ' + optimal);
		else
			a('seo-desc', 'green', ' <b>Мета-описание</b> '+length+' символов. Отлично, ' + optimal);
	}
	
	function d(){
		var length = $title.val().length;
		if(!length) length = $('#title').val().length;
		var optimal = 'оптимальная длинна 70-80 символов!';
		if(length > 80)
			a('seo-title', 'red', ' <b>СЕО-заголовк</b> слишком длинный, '+length+' символов, ' + optimal);
		else if(length < 70)
			a('seo-title', 'red', ' <b>СЕО-заголовк</b> слишком длинный, '+length+' символов, ' + optimal);
		else
			a('seo-title', 'green', ' <b>СЕО-заголовк</b> '+length+' символов. Отлично, ' + optimal);
	}
	
	function c(){
		// del tags
		content = content.replace(/<[^\s]*?>/g, '').trim();
		var spaceLength = content.match(/\s+/g);
		var length = spaceLength == null ? (content ? 1 : 0) : spaceLength.length + 1;
		
		if(length < 300)
			a('seo-content', 'red', ' Слишком мало текста, '+length+' слов, оптимальное количество слов 300!');
		else
			a('seo-content', 'green', ' С количеством слов в тексте все впорядке, '+length+' слов. Отлично!');
	}
	
	$(function(){
		setTimeout(function(){
			content = getContent();
			c();
			$('textarea').on('blur', function(){
				content = getContent();
				c();
			});
		}, 1);
		
		d();
		$('[name="_seo_title"], #title').on('blur', d);
		
		b();
		$desc.on('blur', b);
		
	});
	
	
}());
</script>