<?
define ('HOST', "localhost");
define ('USER', "root");
define ('PASSW', "root");
define ('DB', "gdepara");

function generatePassword ($length = 8)
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

//just a stub, write generated code to file. TODO
function sendSms($mobile, $code){
	$f=fopen("sms.txt", "w");
	fwrite($f, $code);
	fclose($f);
}


function buildAnswer($status, $text=null, $fields=null){
	echo json_encode(array("status"=>$status, "text"=>$text, "fields"=>$fields));
}
function fetchUser($phone){	
	mysql_connect(HOST, USER, PASSW) or die(mysql_error());
	mysql_select_db(DB) or die(mysql_error());
	$res=mysql_query("select id, name, surname, patronymic, mobile, registered from users where mobile='".$phone."'") or die(mysql_error());
	return mysql_fetch_assoc($res);
}

session_start();
if (isset($_SESSION['code'])){
	if (empty($_REQUEST['code'])){
		buildAnswer(2, "Code field is required!", array("code"));
	} else
	if ($_REQUEST['code']==$_SESSION['code']){
		buildAnswer(1);
	} else {
		buildAnswer(2, "Invalid confirming code!", array("code"));
	}
} else
{
	$fields=array('name', 'surname', 'patronymic', 'mobile');
	foreach ($fields as $field)
	{
		if (empty($_REQUEST[$field])) $empty[]=$field;
	}
	if (isset($empty)){
		buildAnswer(2, "All fields are required!", $empty);
		return;	
	} 
	$user=fetchUser($_REQUEST['mobile']);
	if (empty($user)){
		buildAnswer(2, "No such phone in database!", array('mobile'));
	} else
	if ($user['registered']){
		buildAnswer(2, "User already registered!", array('mobile'));	
	} else
	if ($_REQUEST['name']!=$user['name'] || $_REQUEST['surname']!=$user['surname'] || $_REQUEST['patronymic']!=$user['patronymic']){
		buildAnswer(2, "Phone exists, but name is invalid!", array("name", "surname", "patronymic"));
	} else {
		$_SESSION['code']=generatePassword();
		sendSms($_REQUEST['mobile'], $_SESSION['code']);
		buildAnswer(0);
	}
}
?>
