
transliterate = (
	function() {
		var
			rus = "щ   ш  ч  ц  ю  я  ё  ж  ъ  ы  э  а б в г д е з и й к л м н о п р с т у ф х ь".split(/ +/g),
			eng = "shh sh ch cz yu ya yo zh yi ui e a b v g d e z i j k l m n o p r s t u f x i".split(/ +/g)
		;
		return function(from, to, engToRus) {
			var x;
			var text = gebi(from).value;
			for(x = 0; x < rus.length; x++) {
				text = text.split(engToRus ? eng[x] : rus[x]).join(engToRus ? rus[x] : eng[x]);
				text = text.split(engToRus ? eng[x].toUpperCase() : rus[x].toUpperCase()).join(engToRus ? rus[x].toUpperCase() : eng[x].toUpperCase());	
			}
			gebi(to).value = text.toLowerCase();
			return text;
		}
	}
)();

function repl(str, p1, p2, offset, s){
	return p1 == ')' || p1 == '(' ? '' : '-';
}

function gebi(id){
	return document.getElementById(id);
}

function translate1(from, to){
	/*var val = $('#name').val();
	if(val.match(/^[a-zа-яё0-9-\s\(\),]+$/i)){
		// if(val.match(/[а-яё]/i) && val.match(/[a-z]/i)){
			// $('#name').css('background','red');
			// return false;
		// }else{
			transliterate('name', 'url');
			$('#name').css('background','lightgreen');
			return true;
		//}
	}else{
		$('#name').css('background','red');
		return false;
	}*/
	transliterate(from, to);
	$('#name').css('background','lightgreen');
	return true;
}

//gebi('to').value = transliterate(gebi('from').value).toLowerCase()
