;(function($){
	var error, success, currentItem, addExpand,
	menuHandlerUrl = root + 'admin/menu/';
	
	$(function(){
		
		// Show menu items from active menu
		if(typeof(menuItems) !== "undefined" && menuItems)
			selectMenu(menuItems);
		
		
		if(error = getCookie('error')){
			$('#content').prepend('<div class="err">'+ error +'</div>');
			delCookie('error');
		}else if(success = getCookie('menu')){
			$('#content').prepend('<div class="succ">'+ success +'</div>');
			delCookie('menu');
		}
		
		$('#menu-select').change(function(){
			$.ajax({
				url : menuHandlerUrl + 'select/',
				type: "POST",
				data: {id:$(this).val()},
				dataType: 'json',
				success: function(data){
					$('#nestable3 > .dd-list').html('');
					$('#del_menu_id').val($('#menu-select').val());
					if(data){
						addExpand = true;
						selectMenu(data);
					}
					else{
						$('#save-menu').addClass('none');
					}
				}
			});
		});
		
		// Activate selected menu
		$('.menu-select > button').click(function(){
			$.post(menuHandlerUrl + 'activate/', {id:$('#menu-select').val()}, function(data){
				//alert('Удачно!');
				console.log(data);
			});
		});
		
		// Add some link
		$('#some-link > button').click(function(){
			var name = $('#some-link > input.name').val();
			var url  = $('#some-link > input.url').val();
			if(!name) return;
			
			currentItem = countItems();
			menuFill({id:currentItem++, name: name, url: url, origname: name, type:'somelink'});
			$('#nestable3').nestable().change();
			$('#save-menu').removeClass('none');
		});
		
		$('.inset div').click(function(){
			if($(this).hasClass('active')) return;
			
			$('#menu-create .item-lists div, .inset div').removeClass('active');
			$(this).addClass('active');
			$('#menu-create .item-lists div').eq($(this).data('id')).addClass('active');
			if($(this).data('id') == 2) 
				$('.tools').addClass('none');
			else 
				$('.tools').removeClass('none');
		});
		
		$('#nestable3').nestable().on('change', updateOutput);
		updateOutput($('#nestable3').data('output', $('#nestable-output')));
		
		$('#nestable3').on('click', '.trigger', function(){
			$(this).toggleClass('on').parent().css('height', $(this).hasClass('on') ? 'auto':'30px');
		});
		
		$('#nestable3').on('keyup', '.sub input', function(e){
			var val = $(this).val();
			$(this).closest('.dd-item').data('name', val).find('span.new').text(val);
		});
		
		$('#nestable3').on('click', '.sub .del', function(e){
			$(this).closest('.dd-item').remove();
		});
		
		$('div.tools span').click(chooseAll);
		$('div.tools button').click(addToMenu);
		$('#save-menu').click(function(){
			$('#nestable3').nestable().change();
			saveMenu($('#nestable-output').val());
		});
	});
	
	function saveMenu(data){
		$.ajax({
			url : menuHandlerUrl + 'edit/',
			type: "POST",
			data: {menu:data, menu_id:$('#menu-select').val()},
			success: function(data){
				alert('Удачно!');
			}
		});
	}

	var updateOutput = function(e)
	{
		var list   = e.length ? e : $(e.target),
			output = list.data('output');
		if (window.JSON) {
			output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
		} else {
			output.val('JSON browser support required for this demo.');
		}
	};

	function addToMenu(){
		var items = $('.item-lists > div.active > input:checked');
		if(!items.length) return;
		
		currentItem = countItems();
		items.each(function(i, item){
			// if($(item).data('sub')){
				// $(item).data('id', -1);
				// menuFill($(item).data(), $('.dd-item[data-id='+(currentItem - 1)+']'));
			// }else{
				// $(item).data('id', currentItem++);
				// menuFill($(item).data());
			// }
			
			$(item).data('id', currentItem++);
				menuFill($(item).data());
		});
		$('#nestable3').nestable().change();
		$('#save-menu').removeClass('none');
	}

	function chooseAll(){
		$('.item-lists div.active input').attr('checked', true);
	}
	
	
	function selectMenu(menu){
		var menuSorted = [];
		
		menu.forEach(function(item){
			if(item.parent == "-1")
				menuSorted[item.object_id] = !menuSorted[item.object_id] ? 
					item : 
					$.extend(menuSorted[item.object_id], item);
			else{
				if(!menuSorted[item.parent]) 
					menuSorted[item.parent] = {children:[]};
				else{
					if(!menuSorted[item.parent]['children'])
						menuSorted[item.parent]['children'] = [];
				}
					
				menuSorted[item.parent]['children'].push(item);
			}
		});
		
		menuSorted = sort(menuSorted);
		
		currentItem = countItems();
		menuSorted.forEach(function(item){
			item.id = currentItem++;
			var clone = menuFill(item);
			
			if(item.children){
				item.children = sort(item.children);
				if(addExpand){
					clone.prepend('<button data-action="collapse" type="button">');
					clone.prepend('<button data-action="expand" type="button" style="display: none;">');
				}
				clone.append('<ol class="dd-list">');
				item.children.forEach(function(subItem){
					subItem.id = currentItem++;
					menuFill(subItem, clone.children('.dd-list'));
				});
			}
		});	
		$('#nestable3').nestable().change();
		$('#nestable3 .dd-item').each(function(){$(this).children('button').eq(0).click()});
		$('#save-menu').removeClass('none');
		$('#del_menu_id').val($('#menu-select').val());
	}
	
	function sort(arr, index){
		var index = index || 'sort',
			min, k, buff;
		for(var i=0;i<arr.length;i++){
			if(typeof arr[i] == "undefined") continue;
			min = parseInt(arr[i][index]);
			k = i;
			for(var j=i+1;j<arr.length;j++){
				if(typeof arr[j] == "undefined") continue;
				if(parseInt(arr[j][index]) < min){
					min = parseInt(arr[j][index]);
					k = j;
				}
			}
			
			if(k != i){
				buff = arr[i];
				arr[i] = arr[k];
				arr[k] = buff;
			}
		}
		
		return arr;
	}
	
	function menuFill(item, to){
		//console.log(to);
		//var aa = to ? 1 : 0;
		var to = to || $('.dd > .dd-list');
		var clone = $('#item-prototype li').clone();
		/*if(aa){
			console.log(to.find('.dd-list'));
			if(!to.find('.dd-list').length)
				$(to).append('<ol class="dd-list"></ol>');
			
			//to.find('.dd-list').append(clone);
			item.id = -1;
		} */
		
		clone.data(item);
		$(clone).data('id', item.id).attr('data-id', item.id);
		clone.find('.item-title').children('.new, .old').text(item.name);
		clone.find('.sub').find('.linkname').val(item.name);
		var fullUrl = !item.url.toLowerCase() ? 'javascript:void(0);' : root+item.url+'/';
		clone.find('.sub').find('.original-link').html('<a href="'+fullUrl+'">'+item.origname+'</a>');
		to.append(clone);
		// if(!aa)
			// to.append(clone);
		// else
			// to.find('.dd-list').append(clone);
		
		return clone;
	}
	
	function countItems(){
		return $('.dd-item').length - 1;
	}
}(jQuery));