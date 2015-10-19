
<?php

Login();

function Login()
{
	if(empty($_POST['username']))
	{
		$this->HandleError("UserName is empty!");
		return false;
	}

	$username = trim($_POST['username']);

	//Create a UDP socket
	if(!($sock = socket_create(AF_INET, SOCK_DGRAM, 0)))
	{   
		$errorcode = socket_last_error();
		$errormsg = socket_strerror($errorcode);

		die("Couldn't create socket: [$errorcode] $errormsg \n");
	}   

	echo "Socket created \n";

	// Bind the source address
	if( !socket_bind($sock, '127.0.0.1' , 9999) )
	{   
		$errorcode = socket_last_error();
		$errormsg = socket_strerror($errorcode);
		die("Could not bind socket : [$errorcode] $errormsg \n");
	}   

	echo "Socket bind OK \n";


	if(CheckLoginInDB($username))
	{
		$rstr = generateRandomString(64);
		$publicKey = getPubKey("public.pem");
		$encrytoStr = encrypto($rstr, $publicKey);
		echo($encrytoStr);
		echo '\n';
		$remote_ip = $_SERVER['REMOTE_ADDR'];
		$remote_port = $_SERVER['REMOTE_PORT'];
		echo $remote_ip;
		echo	'\n';	
		echo $remote_port;
		echo	'\n';	
		$len = strlen($encrytoStr);
		//socket_sendto($sock, $encrytoStr, $len , 0 , $remote_ip , $remote_port);

	}

	echo($username); 

	//$_SESSION[GetLoginSessionVar()] = $username;

	return true;
}

function getPubKey($path)
{
	$pKey = file_get_contents($path);
	$publicKey="";
	$publicKey= openssl_get_publickey($pKey);
	if(!$publicKey) {
		echo "Cannot get public key";
	}
	return $publicKey;
}


function CheckLoginInDB($username)
{
	$list = array("gtisc@gatech.edu");
	if(in_array($username, $list)){
		return true;
	}
	return false;
}

function encrypto($plaintext, $publicKey)
{
	$encrypted = " ";
	if (!openssl_public_encrypt($plaintext, $encrypted, $publicKey)){
		die('Failed to encrypt data');
	}
	return $encrypted;
}


function generateRandomString($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

?>
