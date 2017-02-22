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
		<h2>Preencha os campos abaixo para gerar o certificado</h2>
			<form class="form-group" method="POST">
					<input class="form-control" type="text" name="au" placeholder="Country Name (2 letter code) [AU]" size="2" maxlength="2" required="required">
					<input class="form-control" type="text" name="state" placeholder="State or Province Name (full name) [Some-State]" required="required">
					<input class="form-control" type="text" name="city" placeholder="Locality Name (eg, city) []" required="required">
					<input class="form-control" type="text" name="company" placeholder="Organization Name (eg, company) [Internet Widgits Pty Ltd]" required="required">
					<input class="form-control" type="text" name="section" placeholder="Organizational Unit Name (eg, section) []" required="required">
					<input class="form-control" type="text" name="server" placeholder="Common Name (e.g. server FQDN or YOUR name) []" required="required">
					<input class="form-control" type="email" name="email" placeholder="Email" required="required">
					<button class="btn btn-primary" type="submit">Gerar Certificado</button>
					<button class="btn" type="reset" value="Reset">Limpar</button>
			</form>
			<p><a href="/">Voltar para a página inicial</a></p>
	</div>
</body>
</html>
<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	//variables
	$attributes = array(
		"countryName" => $_POST['au'],
		"stateOrProvinceName" => $_POST['state'],
		"localityName" => $_POST['city'],
		"organizationName" => $_POST['company'],
		"organizationalUnitName" => $_POST['section'],
		"commonName" => $_POST['server'],
		"emailAddress" => $_POST['email']
	);
		
	$key_pub_files = glob('./keys/*.pub.key');
	foreach($key_pub_files as $file){
	  if(is_file($file)) {
	   if(basename($file, ".pub.key") == $attributes['emailAddress']) {
	   	die("<p>Certificado já existente!</p>");
	   	//header("Location: /");
	   }
	  }
	}

	// Create the keypair
	$res = openssl_pkey_new();

	// Get private key
	openssl_pkey_export($res, $privkey);

	// Get public key
	$pubkey=openssl_pkey_get_details($res)["key"];

	// Generate a certificate signing request
	$csr = openssl_csr_new($attributes, $privkey);

	// You will usually want to create a self-signed certificate at this
	// point until your CA fulfills your request.
	// This creates a self-signed cert that is valid for 3 days
	$sscert = openssl_csr_sign($csr, null, $privkey, 3);


//	openssl_csr_export($csr, $csrout);// and var_dump($csrout);
	openssl_x509_export($sscert, $csrout);

	$file_csr = fopen("keys/".$attributes['emailAddress'].".pem", "w");
	fwrite($file_csr, $csrout);
	fclose($file_csr);
		
	$file_privkey = fopen("keys/".$attributes['emailAddress'].".priv.key", "w");
	fwrite($file_privkey, $privkey);
	fclose($file_privkey);
	
	$file_pubkey = fopen("keys/".$attributes['emailAddress'].".pub.key", "w");
	fwrite($file_pubkey, $pubkey);
	fclose($file_pubkey);

	// Create files
	//file_put_contents("./".$email.".key");
	echo "<p>Esses arquivos ficará disponível durante três minutos</p>";

	echo "<p><a href='./keys/".$attributes['emailAddress'].".priv.key' target='_blank'>Chave Privada</a></p>";
	echo "<p><a href='./keys/".$attributes['emailAddress'].".pub.key' target='_blank'>Chave Pública</a></p>";
	
	echo "<p><a href='./keys/".$attributes['emailAddress'].".pem' target='_blank'>Certificado</a></p>";
	
	/*
	$Filename = "keys/".$email.".priv.key";
	$expire_stamp = date('Y-m-d H:i:s', strtotime("+1 min"));
	while ($expire_stamp > date("Y-m-d H:i:s"));
	unlink($file_privkey);
	*/
}