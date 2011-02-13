<?
define ('HOST', "localhost");
define ('USER', "root");
define ('PASSW', "root");
define ('DB', "gdepara");

function generateCode ($length = 8)
{
    $password = "";
    $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
    $maxlength = strlen($possible);
    if ($length > $maxlength) {
      $length = $maxlength;
    }
    $i = 0; 
    while ($i < $length) { 

      $char = substr($possible, mt_rand(0, $maxlength-1), 1);
        
      if (!strstr($password, $char)) { 
        $password .= $char;
        $i++;
      }

    }
    return $password;
}

function sendSmsMessage($in_phoneNumber, $in_msg)
{
	define('CONFIG_KANNEL_USER_NAME', 'kanneluser');
	define('CONFIG_KANNEL_PASSWORD', 'bar');
	define('CONFIG_KANNEL_HOST', 'localhost');
	define('CONFIG_KANNEL_PORT', '13013');
	define('FROM', 'gdepara');
	$url = '/cgi-bin/sendsms?username=' . CONFIG_KANNEL_USER_NAME
          . '&password=' . CONFIG_KANNEL_PASSWORD
          . '&charset=UTF-8'
          . "&to={$in_phoneNumber}"
          . '&text=' . urlencode($in_msg);


   $results = file('http://'
                   . CONFIG_KANNEL_HOST . ':'
                   . CONFIG_KANNEL_PORT . $url);
}

function buildAnswer($status, $text=null, $fields=null){
	echo json_encode(array("status"=>$status, "text"=>$text, "fields"=>$fields));
}
function fetchUser($phone){	
	mysql_connect(HOST, USER, PASSW) or die(mysql_error());
	mysql_select_db(DB) or die(mysql_error());
	mysql_query('SET NAMES utf8'); 
	$res=mysql_query("select id, name, surname, patronymic, mobile, registered from users where mobile='".mysql_escape_string($phone)."'") or die(mysql_error());
	return mysql_fetch_assoc($res);
}

session_start();

if (isset($_SESSION['code'])){
	$code=trim($_REQUEST['code']);
	if (empty($code)){
		buildAnswer(2, "Введите код подтверждения!", array("code"));
	} else
	if (md5($code)==$_SESSION['code']){
		buildAnswer(1);
	} else {
		buildAnswer(2, "Неверный код подтверждения!", array("code"));
	}
} else
{
	$name=trim($_REQUEST['name']);
	$surname=trim($_REQUEST['surname']);
	$patronymic=trim($_REQUEST['patronymic']);
	$mobile=trim($_REQUEST['mobile']);
	$fields=array('name', 'surname', 'patronymic', 'mobile');
	foreach ($fields as $field)
	{
		if (empty($_REQUEST[$field])) $empty[]=$field;
	}
	if (isset($empty)){
		buildAnswer(2, "Заполните все поля!", $empty);
		return;	
	} 
	$user=fetchUser($mobile);
	
	if (empty($user)){
		buildAnswer(2, "Такой телефон не зарегистрирован!", array('mobile'));
	} else
	if ($user['registered']){
		buildAnswer(2, "Такой пользователь уже зарегистрирован!", array('mobile'));	
	} else
	if ($name!=$user['name'] || $surname!=$user['surname'] || $patronymic!=$user['patronymic']){
		buildAnswer(2, "Номер зарегистрирован, но ФИО введено не верно!", array("name", "surname", "patronymic"));
	} else {
		$code=generateCode();
		sendSmsMessage($mobile, "Confirming code: $code");
		$_SESSION['code']=md5($code);
		buildAnswer(0);
	}
}
?>
