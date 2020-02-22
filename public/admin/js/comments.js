$(function(){
	;(function($){
		var $show 	= $('#admin-comment-block-show');
		var $cancel = $('#admin-cancel-comment');
		var $add 	= $('#admin-add-comment');
		var $commentText = $('#comment-text');
		
		function close(){
			$show.removeClass('none');
			$("#admin-comments-block").addClass('none');
			$commentText.val('');
		}
		
		$show.click(function(){
			if($(this).hasClass('none')) return;
			$(this).addClass('none');
			$("#admin-comments-block").removeClass('none');
		});
		
		$cancel.click(close);
		
		$add.click(function(){
			$.post(root + 'user/comments/add/' + $('#post_id').val() + '/', {comment: $commentText.val()}, function(){
				close();
			});
		});
		
		
		$('body').on('click', '#comment-delete', function(){
			var self = this;
			var item = $(this).closest('table');
			$.post(root + 'admin/delComment/' + $(item).data('id') + '/', function(data){
				if(data.response == 1) item.remove();
			}, 'json');
		});
		
		$('.comment-content-cell .remove').click(function(){
			var item = $(this).closest('tr');
			var data = commentDel(item.data('id'), item, 'callbackTest');
			console.log(data);
		});
		
		$('.comment-content-cell .edit').click(function(){
			var cell = $(this).closest('.comment-content-cell');
			var content = cell.children('.content');
			content.addClass('none');
			$('#comments-block-form-edit').prependTo($(this).closest('.comment-content-cell')).removeClass('none').children('textarea').val(content.text());
		});
		
		$('#comments-block-form-edit').submit(function(e){
			e.preventDefault();
			var item = $(this).closest('tr');
			console.log($(this).serializeArray(), [{'name':'2'}, {'content':'3'}]);
			$.post(root + 'admin/comment-edit/' + item.data('id') + '/', $(this).serializeArray(), function(data){
				if(data.response){
					$('#comments-block-form-edit').addClass('none');
					item.find('.comment-content-cell > .content').removeClass('none').text(data.content);
				}
				console.log(data, item.find('.comment-content-cell > .content'));
			}, 'json');
			
		});
		
		
	})(window.jQuery);
});

function commentDel(commentId, item, callback){
	$.post(root + 'admin/delComment/' + commentId + '/', function(data){
		window[callback](data);
		item.remove();
	}, 'json');
}

function callbackTest(data){
	try{
		data.children.forEach(function(comment){
			$('.comment-cell[data-id='+comment.comment_id+']').remove();
		});
	}catch(e){}
}




