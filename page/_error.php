<?php
function loadError(){
  global $content, $domain, $cms;
   
  $content["title"]= "Greška 404 - Stranica nije pronađena";

  $content["text"]="
  <p><small><a href=''>$domain[name] $cms[name]</a> &gt; Greška 404</small></p>
  <h4>Greška 404</h4>
    <h5>Stranica nije pronađena.</h5>
    <div class='justify'>
      <p>Stranica koju tražite možda je uklonjena, ima promjenjeno ime ili je privremeno nedostupna.</p>
      <p>Što možete učiniti?</p>
      <ul>
        <li>Pogledajte jeste li dobro upisali adresu u vašem browseru.</li>
        <li>Otvorite <a href=''>$domain[name] $cms[name]</a> naslovnu stranicu te pronađite link koji ste tražili.</li>
        <li>Kliknite na dugme za povratak u browseru i probajte neki drugi link.</li>
      </ul>       
      <p>Ukoliko ste sigurni da stranica koju ste pokušali otvoriti mora biti ovdje, molimo vas pošaljite e-mail autoru $cms[name]-a na <a href='mailto:ccrogames@gmail.com?subject=$cms[name] $cms[version] ($domain[name])'>ccrogames@gmail.com</a> i obavijestite ga o stranici kojoj ste pokušali pristupiti.</p>
    </div>";

}
?>
