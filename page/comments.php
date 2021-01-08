<?php

// ISPIS SVIH KOMENTARA
function loadComments($page, $results){
  global $db, $p, $content, $domain, $cms, $user;
  
  if($user['is_admin']) $akcije = 1;
  else{
    $array=$db->fetch_all("SELECT time FROM comments ORDER BY time DESC LIMIT $results[start], $results[limit]");
    foreach($array as $var)
      if($var["time"] > $cms['mod_time'] && !$akcije) $akcije = 1;
  }
  
  $title = "Tekst komentari";
  
  $content["title"]= "$title";
  
  if($akcije)
  $content["js"]="
<script type='text/javascript'>
  $().ready(function(){
    $('a.delete').click(function(){
      if(confirm('Jeste li sigurni da želite izbrisati komentar \"'+$('#naziv_'+$(this).attr('rel')).html()+'\"? Akcija je nepovratna!')){
        $.ajax({
          type: 'POST',
          url: 'ajax.php?action=commentDelete',
          data: 'id='+$(this).attr('rel'),
          success:function(r){
            window.location = '$domain[url]$p[1]/';
          }
        })
      }
      return false;
    });
  });
</script>";

  $content["text"]="
      <p><small><a href=''>$domain[name] $cms[name]</a> &gt; $title</small></p>
      <h4>$title - Pregled svih</h4>
      <p class='align_r'><small>Stranica <b>$page[current]</b> od <b>$page[all]</b>. Rezultati <b>$results[start2]-$results[do]</b> od <b>$results[all]</b>.</small></p>
      <table>
        <tr>
          <th class='align_c' style='width:25px'>ID</th><th style='width:110px'>Korisničko ime</th><th style='width:160px'>E-mail adresa</th><th style='width:170px'>Web stranica</th><th>Komentar</th>".($akcije ? "<th class='align_c' style='width:55px'>Akcije</th>" : "")."
        </tr>";
      $j = 0;
      $data=$db->fetch_all("SELECT comments.ID_COM, comments.ID_USER, comments.name, comments.email AS mail, comments.text, comments.website AS web, comments.time, users.username, users.email, users.website, users.is_admin, users.is_mod, text.title, text.ID_TEXT FROM comments LEFT JOIN users ON comments.ID_USER=users.ID_USER LEFT JOIN text ON comments.ID_TEXT=text.ID_TEXT ORDER BY comments.time DESC LIMIT $results[start], $results[limit]");
      foreach($data as $row){
      $web = ($row['ID_USER'] ? ($row['website'] ? "<span><a href='$row[website]' class='link' target='_blank'>".getHost($row['website'])."</a></span>" : "-") : ($row['web'] ? "<span><a href='$row[web]' class='link' target='_blank'>".getHost($row['web'])."</a></span>" : "-"));
      $content["text"].="
        <tr".($j % 2 == 1 ? " class='dark'" : "").">
          <td class='align_c'>$row[ID_COM]</td><td>".($row['ID_USER'] ? ($row['is_admin'] ? "<b class='admin'>$row[username]</b>" : ($row['is_mod'] ? "<b class='mod'>$row[username]</b>" : "<b>$row[username]</b>")) : "<i>Gost $row[name]</i>")."</td><td>".($row['ID_USER'] ? "$row[email]" : "$row[mail]")."<td>$web</td><td><b><small>[$row[ID_TEXT]]</small> ".cutText($row['title'], ($akcije ? "50" : "58"))."</b><a href='../facebook-novosti/".removeSpecial($row['title'])."-$row[ID_TEXT]/#comments' class='link' target='_blank'></a><br /><em id='naziv_$row[ID_COM]'>".cutText($row['text'], ($akcije ? "65" : "85"))."</em></td>".($akcije ? "<td>".($user['is_admin'] || $row['time'] > $cms['mod_time'] ? "<span><a href='#' class='delete' rel='$row[ID_COM]'>Obriši</a></span>" : "")."</td>" : "")."
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
    $content["text"].=(!$user['is_admin'] ? "
      <p class='help'><b>Pomoć:</b> Moderatori mogu brisati samo komentare starosti do $cms[mod_days] dana.</p>" : "");
}

?>
