<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<title><?php echo "$content[title] | $domain[name] $cms[name]";?></title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<meta http-equiv='content-language' content='hr' />
<base href='<?php echo "$domain[url]";?>' />
<link rel='stylesheet' href='css/style.css' media='screen' type='text/css' />
<!--[if IE 6]><link rel='stylesheet' href='css/style-ie.css' media='screen' type='text/css' /><![endif]-->
</head>
<body id='login'>
<div id='container_login'>
  <p class='opis'>Dobrodošli u <strong><?php echo "$domain[name]";?></strong> administraciju!</p>
  <p class='opis'>Unesite točno korisničko ime i lozinku za ulazak u administraciju.</p>
  <div id='box_login'><?php echo "$content[text]";?>
  </div>
</div>
<div id='footer_login'>
  <p>&copy;<?php echo "$cms[time]";?> <b><?php echo "$cms[name]";?> (<?php echo "$cms[version]";?>)</b> za <b><?php echo "$domain[name]";?></b> web stranicu. Dizajn i k&#244;d by: <a href='<?php echo "$author[link]";?>' target='_blank'><?php echo "$author[website]";?></a>. Sva prava pridržana.</p>
</div>
</body>
</html>
