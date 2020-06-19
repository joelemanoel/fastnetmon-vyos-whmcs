<?php
require_once("init.php");

// IP do servidor que vai chamar o script por curl
$ipspermitido = ["192.168.0.1", "192.168.0.2"];

$ip = $_GET["ip"];

// Pega o IP de quem está fazendo a requisição. Funciona com ou sem cloudflare e proxy
if(!empty($_SERVER["HTTP_CLIENT_IP"])){
    $ipremoto = $_SERVER["HTTP_CLIENT_IP"];
}elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
	$ipremoto = $_SERVER["HTTP_X_FORWARDED_FOR"];
}else{
	$ipremoto = $_SERVER["REMOTE_ADDR"];
}

// Verifica se temos um IP em GET
if(!empty($ip)){
	
	// Segurança mínima: verificar se o IP chamando o script é autorizado
	if(in_array($ipremoto, $ipspermitido)){

		// Pega o IP e procura pelo ID do produto na database
		$id = mysql_fetch_array(mysql_query("SELECT * FROM tblhosting WHERE dedicatedip = '{$ip}'"));

		// Se o produto existir, envia o email
		if(!empty($id)){
			localAPI("SendEmail", array(
				"messagename" => "DDOs Guard",
				"id" => $id["id"]
			));
			echo "Alerta enviado para cliente com sucesso";
		}
	}else{
		echo "IP não permitido";
	}
}
?>
