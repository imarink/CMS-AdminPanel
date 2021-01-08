<?php

// FORMA KORISNIKA - DODAVANJE I USER CP
function loadUsersForm($edit = 0){
  global $db, $p, $content, $domain, $cms, $user;
  
  if($edit){
    $var['title2'] = $var['title'] = "User CP";
    $var['button'] = "Izmjeni";
  }
  else{
    $var['title'] = "Dodaj - Korisnici";
    $var['title2'] = "Korisnici - Dodaj";
    $var['button'] = "Dodaj";
  }
  
  $content["title"]= $var['title'];
  
  $slanje = "";
  $greska = 1;
  
  if($_POST['submit_ok']){
    $form['username'] = cleanup(removeSpace($_POST['username']));
    $form['email'] = cleanup($_POST['email']);
    $form['realname'] = cleanup($_POST['realname']);
    $form['website'] = cleanup($_POST['website']);
    $form['password'] = cleanup($_POST['password']);
    
    if(!$edit){
      $form['is_admin'] = cleanup($_POST['is_admin']);
      if($form['is_admin'] == 1) $form['is_mod'] = 1;
      else $form['is_mod'] = cleanup($_POST['is_mod']);
    }
    else $form['source'] = cleanup($_POST['source']);

    $form['protect'] = cleanup($_POST['protect']);

    $greska = 0;
    
    if($greska == 0 && (strlen($form['username']) < 3 || strlen($form['username']) > 20)){
      $slanje = "<p class='error'>Korisničko ime nije ispravno napisano! Mora biti između 3 i 20 znakova.</p>";
      $greska = 1;
    }
    if($greska == 0){
      $db->fetch_first("SELECT ID_USER FROM users WHERE username='$form[username]'".($edit ? " AND ID_USER!='$user[id]'" : ""));
      if($db->num_rows==1){
        $slanje = "<p class='error'>Korisničko ime <b>$form[username]</b> je zauzeto!</p>";
        $greska = 1;
      }
    }
    if($greska == 0 && !is_mail($form['email'])){
      $slanje = "<p class='error'>Niste unijeli ispravnu e-mail adresu!</p>";
      $greska = 1;
    }
    if($greska == 0){
      $db->fetch_first("SELECT ID_USER FROM users WHERE email='$form[email]'".($edit ? " AND ID_USER!='$user[id]'" : ""));
      if($db->num_rows==1){
        $slanje = "<p class='error'>E-mail adresa <b>$form[email]</b> je zauzeta!</p>";
        $greska = 1;
      }
    }
    if($greska == 0 && $form['realname']){
      if(strlen($form['realname']) < 10 || strlen($form['realname']) > 40){
        $slanje = "<p class='error'>Ime i prezime nije ispravno napisano! Mora biti između 10 i 40 znakova.</p>";
        $greska = 1;
      }
    }
    if($greska == 0 && $form['website'] && $edit){
      if(!is_url($form['website'])){
        $slanje = "<p class='error'>Web stranica nije ispravna! Mora sadržavati <b>http://</b> i ostale potrebne znakove.</p>";
        $greska = 1;
      }
    }
    if(($greska == 0 && !$edit) || ($greska == 0 && $form['password_new'] && $edit)){
      if(strlen($form['password']) < 6 || strlen($form['password']) > 15){
        $slanje = "<p class='error'>Lozinka nije ispravno napisan! Mora biti između 6 i 15 znakova.</p>";
        $greska = 1;
      }
      if($greska == 0 && $form['password'] == $form['username']){
        $slanje = "<p class='error'>Korisničko ime i lozinka moraju biti različiti.</p>";
        $greska = 1;
      }
    }
    
    if($greska == 0){
      $referrer = parse_url(strtolower($_SERVER['HTTP_REFERER']));
      $host = "$referrer[host]";
        
      if($form['protect'] != $domain['protect'] || $host != $domain['root']){
        $slanje = "<p class='error'>Pokušaj hakiranja ili spama!</p>";
        $greska = 1;
      }
    }
    if($greska == 0){
      // USER CP
      if($edit){
        if($form['password']){
          $form['password'] = sha1(md5($form['password']));
          $row=$db->update("users", array("username"=>$form['username'], "password"=>$form['password'], "email"=>$form['email'], "realname"=>$form['realname'], "website"=>$form['website']), "ID_USER='$user[id]'");
        }
        else{
          $row=$db->update("users", array("username"=>$form['username'], "email"=>$form['email'], "realname"=>$form['realname'], "website"=>$form['website']), "ID_USER='$user[id]'");
        }
        if($row){
          $slanje = "<p class='ok'>Izmjena je uspješno izvršena! <span><a href='' class='home'>Početna &raquo;</a></span></p>";
          
          $user['username'] = $form['username'];
          if($form['password']) $user['password'] = $form['password'];
          $user['email'] = $form['email'];
          
          $cookie = implode(",", array($user['id'], $user['username'], $user['password'], $user['email']));
    
          setcookie("admin", $cookie, time()+60*60*24*30, "/");        	  
        }
        else{
          $slanje = "<p class='error'>Došlo je do greške prilikom obrade podataka. Pokušajte ponovno!</p>";
        }
        $greska = 1;
      }
      // NOVI KORISNIK
      else{
        $row=$db->insert("users", array("username"=>$form['username'], "password"=>sha1(md5($form['password'])), "email"=>$form['email'], "is_admin"=>$form['is_admin'], "is_mod"=>$form['is_mod'], "timeRegistered"=>time()));
        if($row){
        $slanje = "<p class='ok'>Korisnik <b>$form[username]</b> je uspješno dodan u bazu! <span><a href='$p[1]/' class='views'>Lista korisnika &raquo;</a></span></p>
        <p>Korisnički podaci su slijedeći:</p>
        <p>Korisničko ime: <b>$form[username]</b><br />E-mail adresa: <b>$form[email]</b><br />Lozinka: <b>$form[password]</b><br />Privilegije: ".($form['is_admin'] ? "<b class='admin'>administrator</b>" : ($form['is_mod'] ? "<b class='mod'>moderator</b>" : "korisnik"))."<br />Vrijeme dodavanja: ".date("d.m.Y. H:i")."</p>";
        
        $mail['title'] = "$domain[name] - Registracija korisnika";
        $mail['text'] = "<p>Uspješno ste registrirani na web stranici $domain[name]. Korisnički podaci su slijedeći:</p>
        <p>Korisničko ime: <b>$form[username]</b><br />E-mail adresa: <b>$form[email]</b><br />Lozinka: <b>$form[password]</b><br />Privilegije: ".($form['is_admin'] ? "<b>administrator</b>" : ($form['is_mod'] ? "<b >moderator</b>" : "korisnik"))."<br />Vrijeme registracije: ".date("d.m.Y. H:i")."</p>
        ".($form['is_mod'] ? "<p>Link za prijavu u Administraciju: <a href='$domain[url]'>$domain[url]</a></p>" : "");
        $success = sendMail($form['email'], $mail['title'], $mail['text']);
        if($success) $slanje .= "<p class='help'><b>Pomoć:</b> Korisnički podaci su uspješno poslani na $form[email].</p>";
        }
        else{
          $slanje = "<p class='error'>Došlo je do greške prilikom obrade podataka. Pokušajte ponovno!</p>";
        }
      }
    }
  }
  elseif($edit){
    $form['username'] = $user['username'];
    $form['email'] = $user['email'];
    $form['realname'] = $user['realname'];
    $form['website'] = $user['website'];
  }
  
  $content["text"]="
      <p><small><a href=''>$domain[name] $cms[name]</a> &gt; ".($edit ? "$var[title]" : "<a href='$p[1]/'>Korisnici</a> &gt; $var[button]")."</small></p>
      <h4>$var[title2]</h4>
      ".($greska ? (!$edit ? "<p><span><a href='$p[1]/' class='views'>Lista korisnika &raquo;</a></span></p>" : "")."
      <form method='post' action='$p[1]/".($edit ? "" : "$p[2]/")."'>" :"" )."
    $slanje
        ".($greska ? ($edit ? "<p><label for='id'>* ID:</label><input name='id' id='id' type='text' value='$user[id]' maxlength='2' class='text' style='width:50px;color:#999;background-color:#f5f5f5' disabled='disabled' /></p>" : "")."
        <p><label for='username'>* Korisničko ime:</label><input name='username' id='username' type='text' value='$form[username]' maxlength='20' class='text' style='width:200px' /></p>
        <p><label for='email'>* E-mail adresa:</label><input name='email' id='email' type='text' value='$form[email]' maxlength='60' class='text' style='width:300px' /></p>
        ".($edit ? "<p><label for='realname'>Ime i prezime:</label><input name='realname' id='realname' type='text' value='$form[realname]' maxlength='60' class='text' style='width:300px' /></p>" : "")."
        ".($edit ? "<p><label for='website'>Web stranica:</label><input name='website' id='website' type='text' value='$form[website]' maxlength='60' class='text' style='width:300px' /></p>" : "")."
        <p><label for='password'>".($edit ? "Nova " : "* ")."Lozinka:</label><input name='password' id='password' type='password' value='' maxlength='15' class='text' style='width:150px' /></p>
        ".($edit ? "" : "<p><label for='is_admin'>Administrator:</label><input name='is_admin' id='is_admin' type='checkbox' value='1'  ".($form['is_admin'] ? "checked='checked' ": "")."class='checkbox' /></p>
        <p><label for='is_mod'>Moderator:</label><input name='is_mod' id='is_mod' type='checkbox' value='1' ".($form['is_mod'] ? "checked='checked' ": "")."class='checkbox' /></p>")."
        <p class='button_align'><input name='protect' id='protect' type='hidden' value='$domain[protect]' />
        <input type='submit' name='submit_ok' id='submit_ok' value='$var[button]' class='button' /></p>
        <p class='button_align'><b>polja označena zvjezdicom (*) su obavezna</b></p>
      </form>
      ".(!$edit ? "<p><span><a href='$p[1]/' class='views'>Lista korisnika &raquo;</a></span></p>
      <p class='help'><b>Pomoć:</b> Pripazite kome dajete ovlasti administratora.</p>" : "") : "")."
      ".($edit ? "<p class='help'><b>Pomoć:</b> Polje 'Nova lozinka' služi za promjenu lozinke. Izmjena nije obavezna.</p>" : "");

}
?>
