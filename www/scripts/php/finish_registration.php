<?
function buildAnswer($status, $text=null, $fields=null) {
	echo json_encode(array("status"=>$status, "text"=>$text, "fields"=>$fields));
}

function updateUser($id, $login, $passwd) {
	define ('HOST', "localhost");
	define ('USER', "root");
	define ('PASSW', "kissmyass");
	define ('DB', "gdepara");	
	
	mysql_connect(HOST, USER, PASSW) or die(mysql_error());
	mysql_select_db(DB) or die(mysql_error());
	mysql_query('SET NAMES utf8'); 
	$res=mysql_query("update users set registered=1, login='".mysql_escape_string($login)
			."', passwd='".mysql_escape_string(md5($passwd))
			."' where id='".mysql_escape_string($id)."'") 
			or die(mysql_error());
	return $res;
}

function checkPasswd($passwd1,$passwd2) {
	if ($passwd1!=$passwd2) {
		return false;
	} else 
	if (empty($passwd1)) {
		return false;
	} 
	
	return true;
}

session_start();

if (checkPasswd($_REQUEST['passwd1'],$_REQUEST['passwd2'])) {
	$login=trim($_REQUEST['login']);	
	$passwd=trim($_REQUEST['passwd1']);
	$id=trim($_SESSION['user_id']);	
	
	if (!updateUser($id,$login,$passwd)) {
		buildAnswer(0, "SQL Error!");
	} else {
		session_unset();		
		//SESSION['Profile']=true;	
		buildAnswer(1);
	}
} else {
	buildAnswer(0, "Пароли не совпадают или пустые!", array("passwd1","passwd2"));
}

?>
