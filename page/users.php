<?php

// ISPIS SVIH KORISNIKA
function loadUsers($page, $results){
  global $db, $p, $content, $domain, $cms, $user;

  $array=$db->fetch_all("SELECT ID_USER FROM users ORDER BY ID_USER LIMIT $results[start], $results[limit]");
  foreach($array as $val){
    if($val["ID_USER"] == $user['id']){
      $akcije = 1;
    }
  }

  $content["title"]= "Korisnici";

  $content["text"]="
      <p><small><a href=''>$domain[name] $cms[name]</a> &gt; Korisnici</small></p>
      <h4>Korisnici - Pregled svih</h4>
      <p class='float_r'><small>Stranica <b>$page[current]</b> od <b>$page[all]</b>. Rezultati <b>$results[start2]-$results[do]</b> od <b>$results[all]</b>.</small></p>
      ".($user['is_admin'] ? "<p class='print_none'><span><a href='$p[1]/add/' class='add'>Dodaj korisnika</a></span></p>" : "")."
      <table>
        <tr>
          <th class='align_c' style='width:25px'>ID</th><th style='width:".($akcije ? "130" : "150")."px'>Korisničko ime</th><th style='width:".($akcije ? "220" : "240")."px'>Email</th><th style='width:".($akcije ? "160" : "180")."px'>Ime i prezime</th><th>Web stranica</th><th class='align_c' style='width:80px'>Privilegije</th>".($akcije ? "<th class='align_c print_none' style='width:70px'>Akcije</th>" : "")."
        </tr>";
      $j = 0;
      $data=$db->fetch_all("SELECT ID_USER, username, email, realname, website, is_admin, is_mod FROM users ORDER BY ID_USER LIMIT $results[start], $results[limit]");
      foreach($data as $row){
        if(!$row['realname']) $row['realname'] = "-";
        if($row['website']) $row['website'] = "<span><a href='$row[website]' class='link' target='_blank'>".getHost($row['website'])."</a></span>";
        else $row['website'] = "-";
        $content["text"].="
        <tr".($j % 2 == 1 ? " class='dark'" : "").">
          <td class='align_c'>$row[ID_USER]</td><td>$row[username]</td><td>$row[email]</td><td>$row[realname]</td><td>$row[website]</td><td class='align_c'>".($row['is_admin'] ? "<em class='admin'>administrator</em>" : ($row['is_mod'] ? "<em class='mod'>moderator</em>" : "korisnik"))."</td>".($akcije ? "<td class='print_none'>".($row['ID_USER']==$user['id'] ? "<span><a href='usercp/' class='usercp'>Izmjeni</a></span>" : "")."</td>" : "")."
        </tr>";
        $j++;
      }
  $content["text"].="
      </table>";
  if($page['all'] > 1){
    $content["text"].="
      <p class='float_r'><small>Stranica:";
    for($i = 1; $i <= $page['all']; $i++){
      if($page['current'] == $i) $content["text"].=" <em class='page_active'>$i</em>";
      else $content["text"].=" <a href='$p[1]/".($i > 1 ? "$i/" : "")."' class='page'>$i</a>";
    }
    $content["text"].="</small></p>";
  }
  if($user['is_admin']) $content["text"].="
      <p class='print_none'><span><a href='$p[1]/add/' class='add'>Dodaj korisnika</a></span></p>";
}
?>
