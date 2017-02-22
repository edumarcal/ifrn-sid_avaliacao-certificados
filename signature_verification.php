<!-- Agradeço a DEUS pelo dom do conhecimento -->
<!DOCTYPE html>
<html>
<head>
	<title>Geração de Certificados</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style type="text/css">
		textarea, input, button, p, a {
		    margin-bottom: 15px;
		}
		h2 {
		    margin-bottom: 50px;
		}
	</style>
</head>
<body>
	<div class="container">
		<h2>Verificação da assinatura</h2>
			<form class="form-group" method="POST" enctype="multipart/form-data">
				<!--
				<label for="texto">Informe o texto manual ou selecione o arquivo</label>
				<textarea class="form-control" style="min-width: 100%; min-height: 30%" rows="10" placeholder="Informe o texto" name="texto" id="texto"></textarea>
				-->
				<input class="form-control" type="file" name="arquivo" placeholder="Informe o documento assinado">
				<label for="Certificado">Informe o Certificado Digital</label>
				<input class="form-control" type="file" name="certificado" placeholder="Informe seu certificado" required="required" id="certificado">
				<button class="btn btn-primary" type="submit">Verificar o Certificado</button>
				<button class="btn" type="reset" value="Reset">Limpar</button>
			</form>
			<p><a href="/">Voltar para a página inicial</a></p>
	</div>
</body>
</html>
<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$dirTemp = 'temp/';
	$file = $_FILES['certificado']['name'];
	$uploadfile = $dirTemp . basename($file);

	move_uploaded_file($_FILES['certificado']['tmp_name'], $uploadfile);
	
	$certInfo = @openssl_x509_read(file_get_contents($uploadfile)) or die("<h3 class='container bg-danger text-white'>Certificado inválido</h3>");
	
	$valid = openssl_x509_checkpurpose($certInfo,X509_PURPOSE_SSL_SERVER, array($uploadfile));

	if ($valid === false) {
		unlink($uploadfile);
		die("<h3 class='container bg-danger text-white'>Certificado inválido</h3>");
	}
	
	// Captura o texto manual
	if (!empty($_POST['texto'])) {
		$texto = $_POST['texto'];

		$tempContexto = explode("Código de verificação: ", $texto);
		$hashTextoInformado = hash("md5", $tempContexto[0]);
		$hashCertificadoInformado = openssl_x509_fingerprint($certInfo, "md5");

		$hashGeral = @$tempContexto[1];
		$hashInformado = $hashCertificadoInformado . $hashTextoInformado;
		
		if ($hashInformado == $hashGeral) {
			die("<h3 class='container bg-success text-white'>Assinatura válida</h3>");
		} else {
			die("<h3 class='container bg-danger text-white'>Assinatura inválido</h3>");
		}	
	}

	// Captura o documento via file
	$f = $_FILES['arquivo']['name'];
	$uploadfileArquivo = $dirTemp . basename($f);
	move_uploaded_file($_FILES['arquivo']['tmp_name'], $uploadfileArquivo);

	$myfile = fopen($uploadfileArquivo, "r") or die("Não foi posivel abrir o arquivo!");
	$tempTexto =  fread($myfile,filesize($uploadfileArquivo));
	fclose($myfile);
	$tempContexto = explode("\n\nCódigo de verificação: ", $tempTexto);

	$texto = @$tempContexto[0];
	$hashGeral = @$tempContexto[1];

	$hashTextoInformado = hash("md5", $texto);
	$hashCertificadoInformado = openssl_x509_fingerprint($certInfo, "md5");
	$hashInformado = $hashCertificadoInformado . $hashTextoInformado;

	if ($hashInformado == $hashGeral) {
		die("<h3 class='container bg-success text-white'>Assinatura válida</h3>");
	} else {
		die("<h3 class='container bg-danger text-white'>Assinatura inválido</h3>");
	}
}