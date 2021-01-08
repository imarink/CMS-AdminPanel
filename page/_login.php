<?php
function loadLogin(){
  global $db, $content, $domain, $cms;
  
  $content["title"]= "Prijava korisnika";

  $greska = 1;
  $slanje = "";

  if($_POST['submit_ok']){
    $form['username'] = cleanup($_POST['username']);
    $form['password'] = cleanup($_POST['password']);
    
    $form['protect'] = cleanup($_POST['protect']);
    
    $greska = 0;
  
    if(strlen($form['username']) < 3 || strlen($form['username']) > 20){
      $slanje = "<p class='error'>Korisničko ime <b>$form[username]</b> ne postoji u bazi!</p>";
      $greska = 1;
    }
    if($greska == 0){
      $db->fetch_first("SELECT ID_USER FROM users WHERE username='$form[username]' AND is_mod='1'");
      if($db->num_rows!=1){
        $slanje = "<p class='error'>Korisničko ime <b>$form[username]</b> ne postoji u bazi!</p>";
        $greska = 1;
      }
    }
    if((strlen($form['password']) < 6 || strlen($form['password']) > 15) && $greska == 0){
      $slanje = "<p class='error'>Unijeli ste pogrešnu lozinku!</p>";
      $greska = 1;
    }
    if($greska == 0){
      $form['password'] = sha1(md5($form['password']));
      $db->fetch_first("SELECT ID_USER FROM users WHERE username='$form[username]' AND password='$form[password]'");
      if($db->num_rows!=1){
        $slanje = "<p class='error'>Unijeli ste pogrešnu lozinku!</p>";
        $greska = 1;
      }
    }
    if($greska == 0){
      $referrer = parse_url(strtolower($_SERVER['HTTP_REFERER']));
      $host = "$referrer[host]";
      
      if($form['protect'] != $domain['protect'] || $host != $domain['root']){
        $slanje = "<p class='error'>Pokušaj hackiranja!</p>";
        $greska = 1;
      }
    }
    if($greska == 0){
      $row=$db->fetch_first("SELECT ID_USER, username, password, email, website FROM users WHERE username='$form[username]' AND password='$form[password]' AND is_mod='1'");
            
      $user['id'] = $row['ID_USER'];
      $user['username'] = $row['username'];
      $user['password'] = $row['password'];
      $user['email'] = $row['email'];
      $user['website'] = $row['website'];
      $user['is_admin'] = $row['is_admin'];
      $user['is_mod'] = 1;
      
      $cookie = implode(",", array($user['id'], $user['username'], $user['password'], $user['email']));
      setcookie("admin", $cookie, time()+60*60*24*30, "/");
  	  
  	  $slanje = "<p class='ok'>Prijava je uspješna!</p>";
  
      header("Location: $domain[url]");
    }
  }
  
  $content["text"]="
    <form action='login/' method='post' name='loginForm'>
      $slanje
      <p><label for='username'>Korisničko ime:</label><input type='text' name='username' id='username' value='$form[username]' size='20' maxlength='20' class='text' /></p>
      <p><label for='password'>Lozinka:</label><input type='password' name='password' id='password' value='' size='20' maxlength='15' class='text' /></p>
      <p class='button_align'><input name='protect' type='hidden' value='$domain[protect]' /><input type='submit' name='submit_ok' id='submit_ok' value='Prijava' class='button' /></p>
    </form>";
}

?>
