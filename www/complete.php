<?
	function createLogin($user_id){

		define ('HOST', "localhost");
		define ('USER', "root");
		define ('PASSW', "kissmyass");
		define ('DB', "gdepara");

	  	$trans = array("а"=>"a","б"=>"b","в"=>"v","г"=>"g",
				"д"=>"d","е"=>"e","ё"=>"yo","ж"=>"j",
				"з"=>"z","и"=>"i","й"=>"i","к"=>"k",
				"л"=>"l","м"=>"m","н"=>"n","о"=>"o",
				"п"=>"p","р"=>"r","с"=>"s","т"=>"t",
				"у"=>"u","ф"=>"f","х"=>"h","ц"=>"c",
				"ч"=>"ch", "ш"=>"sh","щ"=>"sh",
				"ы"=>"y","э"=>"e","ю"=>"yu","я"=>"ya",
				"А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
				"Д"=>"D","Е"=>"E", "Ё"=>"Yo","Ж"=>"J",
				"З"=>"Z","И"=>"I","Й"=>"I","К"=>"K",
				"Л"=>"L","М"=>"M","Н"=>"N","О"=>"O",
				"П"=>"P", "Р"=>"R","С"=>"S","Т"=>"T",
				"У"=>"U","Ф"=>"F", "Х"=>"H","Ц"=>"C",
				"Ч"=>"Ch","Ш"=>"Sh","Щ"=>"Sh", 
				"Ы"=>"I","Э"=>"E","Ю"=>"Yu","Я"=>"Ya",
				"ь"=>"","Ь"=>"","ъ"=>"","Ъ"=>"");
	  	
		mysql_connect(HOST, USER, PASSW) or die(mysql_error());
		mysql_select_db(DB) or die(mysql_error());
		mysql_query('SET NAMES utf8'); 
		
		$res=mysql_query("SELECT res1.surname, res1.name, res1.patronymic, gr.name AS group_name 
				FROM (SELECT us.surname, us.name, us.patronymic, st.group 
					FROM students AS st, users AS us 
					WHERE st.user_id=us.id AND us.id='".mysql_escape_string($user_id)."') 
				AS res1, groups AS gr WHERE res1.group=gr.id") or die(mysql_error());

		$res=mysql_fetch_assoc($res);

		//TODO: check for unique login

		$login=substr(strtr($res['name'],$trans),0,1)
			.substr(strtr($res['patronymic'],$trans),0,1)
			.strtr($res['surname'],$trans)
			.strtr($res['group_name'],$trans);
		
		return strtolower($login);
	
		
	}

session_start();

if (isset($_SESSION['register'])) {?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="styles/styles.css" />
<script src="scripts/js/jquery-1.5.js"></script> 
<script>
$(document).ready(function(){
	
	$('#login').addClass('gray').attr('readonly', true);

	$('#regForm').submit(function(e) {
		e.preventDefault();
	
		$('input').removeClass('pink');
		$('#error').css('visibility', 'hidden');
		
		$.getJSON("scripts/php/finish_registration.php", $("#regForm").serialize(),function(message){
			$('#loading').hide();	
			if (parseInt(message.status)==1){
				alert("Successful registration!");
				$('input').addClass('gray').attr('readonly', true);			
			} else
			{
				$('#error').css('visibility', 'visible').html(message.text);
				$.each(message.fields, function(){
					$('#'+this).addClass('pink');
				});
			}
		});
	});

});
</script>

</head>

<body>
<div id="div-regForm">
<div class="form-title">Где пара?</div>

      <div class="form-sub-title">Введите ваш пароль</div>
		<form id="regForm" method="post">
		<table>
			<tbody>
				<tr>
					<label for="login">Ваш логин:</label>
				</tr>
				<tr>
					<div class="input-container">
						<input name="login" id="login" class="first" type="text" value="<?echo createLogin($_SESSION['user_id']);?>"/>
					</div>
				</tr>
				
				<tr>
					<label for="passwd1">Введите пароль:</label>
				</tr>
				<tr>
					<div class="input-container">
						<input name="passwd1" id="passwd1" class="passwd" type="text" />
					</div>
				</tr>				
				
				<tr>
					<label for="passwd2">Повторите пароль:</label>
				</tr>
				<tr>
					<div class="input-container">
						<input name="passwd2" id="passwd2" class="passwd"  type="text" />
					</div>
				</tr>
						
					
				<tr>
					<input type="submit" class="greenButton" value="Завершить регистрацию" />
					<img id="loading" src="img/ajax-loader.gif" alt="working.." width="20" height="20"/>
				</tr>
				
			</tbody>
		</table>
	</form>
	<div id="error">&nbsp;</div>
	
</div>
</body>

<?
}
?>
