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
		input, button, p, a {
		    margin-bottom: 15px;
		}
		h2 {
		    margin-bottom: 50px;
		}
	</style>
</head>
<body>
	<div class="container">
		<h2>Informe o seu certificado</h2>
			<form class="form-group" method="POST" enctype="multipart/form-data">
				<input class="form-control" type="file" name="certificado" placeholder="Informe seu certificado" required="required">
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

	if ($valid === true) {
		echo "<h3 class='container bg-success text-white'>Certificado válido</h3>";
	}
	else {
		echo "<h3 class='container bg-danger text-white'>Certificado inválido</h3>";
	}

	unlink($uploadfile);
}