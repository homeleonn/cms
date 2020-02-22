
function isVisible(el){
	if(typeof $(el).offset() == "undefined") return 0;
	return $('html').scrollTop()+$(window).height()-$(el).offset().top;
}

function getMsg(data){
	data.eType = data.eType || 'err';
	var type = data.eType;
	var showNewMessageDelay = 500;
	if(!$('.events').length){
		$(data.el).find('#item-factory').after('<div class="events">');
	}
	
	$('.events').css('opacity', 0.5).html('<img src="'+root+'admin/view/img/preloader.gif">');
	if(isVisible($('.events')) < 30)
		$('html, body').animate({scrollTop: $(document).scrollTop() + 60}, 0);
	data.eType = 'events ' + data.eType;
	setTimeout(function(){
		$('.events')
			.eq(0)
			.stop()
			.css('opacity', 1)
			.removeClass()
			.addClass(data.eType)
			.html(data.text + (data.path ? ' | <a href="'+data.path+'">Перейти</a>' : ''));
		if(data.delay){
			$('.events').eq(0).stop().css('opacity', 1);
		}
	}, showNewMessageDelay);
}



function checkData(act, data){
	if(act != 'del' && !validData){
		return 'Данные введены некорректно';
	}
	
	if(checkEmpty(data)){
		return 'Не все поля заполнены';
	}
	
	return true;
}


function checkEmpty(data){
	var elInvalid = false;
	data.check.forEach(function(i){//console.log(data.elements, data.elements[i]);
	console.log(i, data.elements[i].value);
		if(data.elements[i].value == '') {
			elInvalid = true;
			return false;
		}
	});
	return elInvalid;
}

function checkUrl(url){
	return !(url && url.value.match('[A-Za-zа-яА-ЯЁё0-9-]{1,50}')) ? false : true;
}


function send(act, data, self){
	//console.log(self[0]);return;
	var fd = new FormData(self[0]);
	if(filesUpload.length)
		filesUpload.forEach(function(i){
			fd.append('files[]', i);
		});
	//fd.delete('morephotos');
	//console.log(fd);
	
	var type = root + 'admin/'+data.type+'/';
	
	$.ajax({
		url : type+act+'/',
		type: "POST",
		processData: false,
		contentType: false,
		data: fd, 
		dataType : 'json',
		success: function(data){//$('body').prepend(data);
			if(data.code == 10){
				document.location.href = type + 'edit/' + data.id + '/';
				return;
			}
			if(data.code){
				getMsg({text:'Действие выполнено успешно', eType:'success', el:self, delay: 15, path:data.link});
				if(act != 'edit'){
					$('#morephotosContainer').html('');
				}
				else if(act == 'del'){
					$(self).find(" :selected").remove();
				}
				filesUpload = [];
			}else{
				console.log(data);
				getMsg({text: (typeof data.msg != "undefined" ? data.msg : 'Ошибка'), eType:'err', el:self, delay: 10});
			}
		}
	});
}



//data: {type, elements, check}
function item(act, data){
	var valid;
	var self = $('#'+act+'-'+data.type);
	
	if(typeof(tinymce) != "undefined" && data.elements['content']/*data.type != 'page' && data.type != 'news'*/&& $('textarea#simple-editor')) 
		data.elements['content'].value = $('#editors span.active').attr('id') == 'visual' ? tinymce.get('content').getContent() : $('textarea#simple-editor').val();
	if((valid = checkData(act, data)) !== true){
		getMsg({text:valid, eType:'err', el:self, delay: 5});
		return false;
	}
	
	send(act, data, self);
}




/*удалить*/
function delItem(el, slug, id, type){
	var type = type || 'post';
	$.post(root + 'admin/'+slug+'/del/'+type+'/'+id+'/', function(data){console.log(data);
		if(data.substr(0, 2) == 'OK'){
			alert('Действие выполнено успешно');
			$(el).closest('tr').remove();
		}else{
			alert('Ошибка: '+data);
		}
	});
}

function checkElemets(type, act){
	var checkElemets = {
		categories:{
			add:['name','url', 'uploadimgname', 'parent'],
			edit:['name','url', 'uploadimgname'],
		},
		products:{
			add:['name','url', 'cat_id', 'uploadimgname'],
			edit:['name','url', 'uploadimgname','price','descr'],
		},
		pages:{
			add:['content','title'],
			edit:['content','title'],
		},
		educators:{
			add:[],
			edit:[],
		},
		news:{
			add:[],
			edit:[],
		},
	}
	//console.log(type, act);
	return [];
	//return checkElemets[type][act];
}

//----------------------------
function addTerm(term){
	var termNameInput = $('#post-' + term).find('input#new-' + term);
	var newTerm = termNameInput.val();
	if(!newTerm) return;
	var existingTerms = $('#term-' + term + ' > input[type="checkbox"]');
	
	if(issetTerm(existingTerms, newTerm)){
		termNameInput.val('');
		return;
	}
	
	$.post(root + 'admin/' + postType + '/add-term/' ,{term: term, name: newTerm, async: 1}, function(data){
		if(data)
			$('#post-' + term).find('#term-' + term).append('<input type="checkbox" value="'+newTerm+'" /> '+newTerm + '<br>');
		else
			termNameInput.val('');
	});
}

function issetTerm(existingTerms, newTerm){
	var isset = false;
	
	$(existingTerms).each(function(i, item){
		if($(item).val() == newTerm){
			isset = true;
			return;
		}
	});
	
	return isset;
}
//----------------------------	
	
function initTinymce(){
	tinymceInit = true;
	tinyMCE.init({ 
		selector:'textarea.visual',
		plugins : "image imagetools fullscreen hr anchor autoresize autolink autosave link lists table",
		relative_urls: false,
		remove_script_host: false,
		height : "600px"
	});
}

var validData = true;
var filesUpload = [];
var content = '';
$(function(){
	
	$('form input#item-factory').click(function(){
		var form = $(this).closest('form')[0];
		var [act, type] = $(form).attr('id').split('-');
		
		item(act, {
			type:type, 
			elements:form.elements,
			check:checkElemets(type, act)
		})
	});
	
	
	/*common checking inputs*/
	$('#price').on('blur', function(){
		validData = $.isNumeric($(this).val());
		$(this).css('background',validData ? 'lightgreen' : 'red');
	});
	
	
	
	
	if(!localStorage.getItem("visual-editor"))
		localStorage.setItem("visual-editor", "2");
	
	$('#editors').prepend('<div class="right choose-editor"><span id="visual">Визуальный</span> | <span id="simple">Текстовый</span></div>');
	$('#editors > textarea').after('<textarea id="simple-editor" style="width:100%;height: 600px;display: none;"></textarea>');
	
	try{
		if(localStorage.getItem("visual-editor") == "2"){
			$('#editors > textarea#simple-editor').text(text).css('display', 'block');
			$('#editors > .choose-editor > #simple').addClass('active');
		}else{
			initTinymce();
			setTimeout(function(){
				tinyMCE.get('content').setContent(text);
				$('.mce-tinymce').css({'visibility': 'visible', 'display': 'block'});
				$('#editors > .choose-editor > #visual').addClass('active');
			}, 1000);
			
		}	
	}catch(e){/* console.log(e); */}
	
	
	$('.choose-editor').click(function(e){
		if($(e.target).hasClass('active')) return;
		
		$(this).children('span').removeClass('active');
		$(e.target).addClass('active');
		
		if(e.target.id == 'visual'){
			if(!tinymceInit){
				initTinymce();
				setTimeout(function(){getVisual()}, 1000);
			}else{
				getVisual();
			}
			
		}else{
			localStorage.setItem("visual-editor", "2");
			$('textarea.visual, .mce-tinymce').css('display', 'none');
			$('textarea#simple-editor').css('display', 'block').val(content = tinyMCE.get('content').getContent());
		}
	});
	
	
	// Edit url
	
	
	
	
});

function getContent(){
	var editor = localStorage.getItem('visual-editor');
	if(!editor) return false;
	return (editor == 2 ? $('textarea#simple-editor').val() : tinyMCE.get('content').getContent()).trim();
}

if($('#edit-url-init').length)
	var editUrl = new EditUrl();


function EditUrl(){
	this.urlEl = $('a#url');
	this.achorEl = $('a#url > span.editing-part');
	this.fullUrl = this.anchorUrl = '';
	
	this.editUrlInit = function(){
		this.setUrl();
		this.drawInput();
	}
	
	this.setUrl = function(){
		this.fullUrl = this.urlEl.text();
		this.anchorUrl = this.achorEl.text();
	}
	
	this.drawInput = function(){
		$(this.urlEl).addClass('none');
		$('#edit-url-ok, #edit-url-cancel').addClass('inline');
		$('<span id="edit-url-draft">'+this.urlEl.html()+'</span>').insertAfter(this.urlEl);
		$('#edit-url-draft > span.editing-part').html('<input type="text" value="'+this.anchorUrl+'" id="new-editing-part-url">');
		this.draftUrlEl = $('#edit-url-draft');
		this.draftAchorEl = $('#edit-url-draft > span.editing-part > input');
	}
	
	this.setNewUrl = function(cancel){
		var newUrlValue = this.draftAchorEl.val();
		var newUrl = !cancel ? (newUrlValue && urlPattern.test(newUrlValue) ? newUrlValue : this.anchorUrl) : this.anchorUrl;
		this.achorEl.text(newUrl);
		$('input[name="url"]').val(newUrl);
		this.urlEl.attr('href', this.urlEl.text());
		$(this.urlEl).toggleClass('none');
		$('#edit-url-init').toggleClass('none');
		this.draftUrlEl.addClass('none');
		$('#edit-url-ok, #edit-url-cancel').toggleClass('inline');
	}
	
	
	
	$(function(){
		$('#edit-url-init').click(function(){
			editUrl.editUrlInit(this);
			$(this).addClass('none');
		});
		
		$('#edit-url-ok').click(function(){
			editUrl.setNewUrl();
		});
		
		$('#edit-url-cancel').click(function(){
			editUrl.setNewUrl(true);
		});
	});
}

function getShortMsg(text, afterEl){
	$(' <span class="short-msg"> ' + text + '</span>').insertAfter(afterEl);
	setTimeout(function(){
		$('.short-msg').animate({'opacity':0}, 3000, function(){$('.short-msg').remove()});
	}, 2000);
}

function getVisual(){
	localStorage.setItem("visual-editor", "1");
	$('.mce-tinymce').css('display', 'block');
	tinymce.get('content').setContent(content = $('textarea#simple-editor').css('display', 'none').val());
}