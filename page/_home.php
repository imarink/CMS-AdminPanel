<?php
function loadHome(){
  global $content, $domain, $cms, $author, $user;
  
  $content["title"]= "Početna";

  $content["text"]="
      <p><small><a href=''>$domain[name] $cms[name]</a> &gt; Početna</small></p>
      <h4>Dobrodošli!</h4>
      <div class='justify'>
        <p>Dobrodošli <b>$user[username]</b> u $domain[name] administraciju!</p>
        <p>Ovaj <b>$cms[name] ($cms[version])</b> je napravio $author[name], za <b>$domain[name]</b> korištenje. Bilo kakvo kopiranje k&#244;da je zabranjeno osim ako vam autor to nije pismeno dopustio.</p>
        <p><b>Napomena:</b> Administracija radi na Firefoxu, Operi te Google Chrome-u. Internet Explorer nije podržan.</p>
        <p>Ukoliko trebate pomoć oko korištenja ovog $cms[name]-a obratite se autoru preko slijedeće e-mail adrese: <a href='mailto:$author[email]?subject=$cms[name] $cms[version] ($domain[name])'>$author[email]</a>.</p>
        <p>Autor: $author[name], <a href='$author[link]' target='_blank'>$author[website]</a>.</p>
        <p class='help'><b>Pomoć:</b> CMS je sustav za upravljanje web sadržajem (engl. Content Management System - CMS).</p>
      </div>";
}

?>
