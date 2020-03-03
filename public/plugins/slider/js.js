;(function(){
	
let 
	url,
	filesUpload = [];
$(function(){
	url  = root + 'admin/plugins/settings/';
	$('#sliderphotos').change(function(e){
		if(filesUpload.length >= 20){
			alert('Больше 20 изображений загружать нельзя');
			return false;
		}
		
		var files = $('#sliderphotos')[0].files;
		for(var i=0;i<files.length;i++){
			filesUpload.push(files[i]);
			//checkImg(200, 300, files[i], false, imgIndex++);
		}
		send3();
		filesUpload = [];
	});
	
	$('body').on('click', '.del', function(){
		$.post(url, {del:$(this).parent().attr('id').split('sort-')[1]}, (responce) => {
			if (responce == 'OK') {
				$(this).parent().remove();
			}
		});
	});
	
	$('body').on('click', '.text', function(){
		let 
			item = $(this).parent(),
			title = $(item).data('title'),
			text = $(item).data('text');
			//console.log($(item).data('title'));
		$('#edit-text > .slide-title').val(title);
		$('#edit-text > .slide-text').val(text);
		$('#edit-text').data('slide', $(item).attr('id').split('sort-')[1]);
		$('#edit-text').removeClass('none');
		
	});
	
	$('#edit-text > .edit-slide-ok').click(function(){
		let 
			id = $('#edit-text').data('slide'),
			title = $('#edit-text > .slide-title').val(),
			text = $('#edit-text > .slide-text').val();
		
		$.post(url, {'edit-slide':[id, title, text]}, function(){
			$('#sort-' + $('#edit-text').data('slide')).data({title:title, text:text})
		});
	});
	
	$("#sliderphotosContainer").sortable(
	{
		cursor: 'move',
		update: function() {
			let data1;
			$.post(url, {sort: $('#sliderphotosContainer').sortable("toArray")}, function (data){
				let i = 0;
				for (key in data) {
					$('.slider-img')[i++].id = 'sort-' + key;
				}
			}, 'json');
			
		}
	});
	
	$('.sorted-save').click(function(){
		$(this).addClass('active');
	});
	
	$('#edit-text > .edit-slide-ok, #edit-text > .edit-slide-cancel').click(function(){$(this).parent().addClass('none')})
});

function send3(){
	var fd = new FormData();
	if(filesUpload.length)
		filesUpload.forEach(function(i){
			fd.append('files[]', i);
		});
	$.ajax({
		url : url,
		type: "POST",
		processData: false,
		contentType: false,
		data: fd, 
		dataType : 'json',
		success: function(data){
			try{
				for(key in data) {
					console.log(data[key]);
					$('#sliderphotosContainer').append(`<div class="col-md-4 ui-sortable-handle slider-img" id="sort-${key}"><img class="shower" src="${root}content/plugins/slider/images/glavnii/${data[key][0]}.${data[key][1]}"><div class="text">&#9998;</div><div class="del">x</div></div>`);
				}
			}catch(e){}
		}
	});
	filesUpload = [];
}


})();


