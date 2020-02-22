function checkImg(file){
	var reader = new FileReader();
	var valid = true;
	reader.onload = function(e) {
		var img = document.createElement('img');
	  
		img.onload = function() {
			if(!checkFileSize(file, $('#media-modal'))){
				valid = false;
				return;
				
			}
			//$('#file-response').append('<img src="'+this.src+'" alt="">');
		};
		img.src = e.target.result;
	}
	reader.readAsDataURL(file);
	return valid;
}


function checkFileSize(file, $el){
	var kb = (file.size / 1024).toFixed(3);
	if(kb >= 2048){
		$($el).prepend('<div class="error">Размер файла "'+file.name+'" превышает 2MB</div>');
		goToError();
		return false;
	}
	return true;
}

function goToError(){
	$('body, html').scrollTop($('.error').offset().top);
}


var imgIndex = 0;
var toolType = false;
var filesUpload = [];
var path;
$(function(){
	try{
		path = root + 'admin/media/';
	}catch(e){}
	$('#post-images .cancel').click(function(){
		$('#post-img-container').addClass('none').children('img').attr('src', '');
		$('[name="_jmp_post_img"]').val('');
		$(this).addClass('none');
	});
	
	$('#filemanager-imgs').on('click', function(event){
		var $target = $(event.target);
		
		if($target.hasClass('getLink'))
			prompt('Ссылка', $($target).attr('src'));
		else if($target.hasClass('del'))
			if(confirm('Подтвердите удаление!'))
				imgdel($target, $($target).next().attr('src'));
			
	});

	$('body').on('change', '#upload-img', function(e){
		if(filesUpload.length >= 20){
			alert('Больше 20 изображений загружать нельзя');
			return false;
		}
		
		var files = $('#upload-img')[0].files;
		for(var i=0;i<files.length;i++){
			//if(checkImg(files[i]))
				filesUpload.push(files[i]);
		}
		send1();
	});
	
	$('body').on('click', '.media-thumbs', function(e){
		if(e.target.className == 'media-thumb'){
			chooseMediaThumb(e.target);
		}else if(e.target.parentNode.className == 'media-thumb'){
			chooseMediaThumb($(e.target).closest('.media-thumb'));
		}
	});
	
	
	$('body').on('click', '#media-delete', function(e){
		mediaDelete();
	});
	
	$('#add-post-img').click(function(){
		if(mediaLoaded){
			$('#alpha-back').removeClass('none');
		}else{
			$.get(root + 'admin/media/async/', function(data){
				$('#media-modal').html(data);
				$('#alpha-back, #select-for-post').removeClass('none');
				mediaLoaded = true;
			});	
		}
	});
	
	$('body').on('click', '#select-for-post', function(){
		var $activeImg = $('.media-thumb.chosen > img');
		var originalSrc = $activeImg.data('original');
		var id = $activeImg.data('id');
		$('#post-img-container > img').attr('src', originalSrc).parent().removeClass('none');
		$('[name="_jmp_post_img"]').val(id);
		$('#alpha-back').addClass('none');
		$('#post-images .cancel').removeClass('none');
	});
	
	$('#alpha-back').click(function(e){
		if(e.target.id == 'alpha-back')
			$(this).addClass('none');
	});
});
var mediaLoaded = false;

function chooseMediaThumb(el){
	if($(el).hasClass('chosen')) return;
	var showBlock = '#media-original-show';
	var wrap = '#wrap-media';
	$('.media-thumbs').addClass('col-md-8');
	$('.media-thumb').removeClass('chosen');
	$(el).addClass('chosen');
	$(showBlock).removeClass('none');
	$(wrap).addClass('col-md-4');
	$img = $(el).children('img');
	var originalImgSrc = $img.data('original');
	var meta = $img.data('meta');
	var showImg = new Image();
	showImg.onload = function(){
		$(showBlock + ' > img').attr('src', this.src);
		$(showBlock + ' > .size').html('Original: ' + $(showBlock + ' > img')[0].naturalWidth + 'x' + $(showBlock + ' > img')[0].naturalHeight);
		$(showBlock + ' input').val(originalImgSrc);
		if(typeof meta.sizes){
			$(showBlock + ' .thumbnails').html('');
			for(i in meta.sizes){
				$(showBlock + ' .thumbnails').append('<hr><b>' + i + ': ' + meta.sizes[i].width + 'x' + meta.sizes[i].height + '<br>Путь:<br><input class="w100 copy" value="'+$img.data('dir') + meta.sizes[i].file+'">');
				$(showBlock + ' .thumbnails').append('<hr><a class="w100 copy" href="'+$img.data('dir') + meta.sizes[i].file+'">'+$img.data('dir') + meta.sizes[i].file+'</a>');
			}
		}
	};
	showImg.src = originalImgSrc;
}

function mediaDelete(){
	var id = $('.media-thumb.chosen > img').data('id');
	$.post(root +'admin/media/del/' + id + '/');
	$('.media-thumb.chosen').remove();
	$('#media-original-show').addClass('none').children('img').attr('src', '');
	$('#wrap-media').removeClass('col-md-4');
	$('.media-thumbs').removeClass('col-md-8');
}


function imgdel(item, src){
	$.post(path + 'del/',{src:src},function(msg){
		if(msg == 'OK'){
			$(item).parent().remove();
		}else{
			alert('Ошибка!');
		}
	})
}


function send1(){
	var fd = new FormData($('form')[0]);
	try{
		fd.delete('_jmp_post_img');
	}catch(e){}
	if(filesUpload.length)
		filesUpload.forEach(function(i){
			fd.append('files[]', i);
		});
	$.ajax({
		url : path + 'add/',
		type: "POST",
		processData: false,
		contentType: false,
		data: fd, 
		dataType : 'json',
		success: function(data){
			if(typeof(data.thumbSrcList) != "undefined")
				data.thumbSrcList.forEach(function(item, i){
					//$('<div/>').prependTo('.thumbs').html('<img src="'+item+'">').addClass('media_thumb');
					$('.media-thumbs').prepend('<div class="media-thumb"><img src="'+item.thumb+'" data-original="'+item.orig+'" data-id="'+item.id+'" data-meta=\''+item.meta+'\' data-dir="'+item.dir+'"></div>');
				});
			else
				alert(data.error);
		}
	});
	filesUpload = [];
}

