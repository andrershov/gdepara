<?
session_start();
if (isset($_SESSION['code'])){
	if (empty($_REQUEST['code'])){
		echo json_encode(array("status"=>2, "text"=>"Code field is required!", "fields"=>array("code")));
	} else
	if ($_REQUEST['code']==$_SESSION['code']){
		echo json_encode(array("status"=>1));
	} else {
		echo json_encode(array("status"=>2, "text"=>"Invalid confirming code", "fields"=>array("code")));
	}
} else
{
	$fields=array('name', 'surname', 'otchestvo', 'mobile');
	foreach ($fields as $field)
	{
		if (empty($_REQUEST[$field])) $empty[]=$field;
	}
	if (isset($empty)){
		echo json_encode(array("status"=>2, "text"=>"All fields are required!", "fields"=>$empty));
	} else
	if ($_REQUEST['mobile']!="111"){
		echo json_encode(array('status'=>2, 'text'=>"No such phone in database", "fields"=>array("mobile")));
	} else
	if ($_REQUEST['name']!='A' || $_REQUEST['surname']!='B' || $_REQUEST['otchestvo']!='C'){
		echo json_encode(array('status'=>2, 'text'=>"Phone exists, but name is invalid!", "fields"=>array("name", "surname", "otchestvo")));
	} else {
		$_SESSION['code']='123';
		echo json_encode(array('status'=>0));
	}
}
?>