<?php
function loadTags($page, $results){
  global $db, $p, $content, $domain, $cms, $user;
  
  if($user['is_admin']) $akcije = 1;
  else $akcije = 0;

  $content["title"]= "Tagovi";

  $content["text"]="
      <p><small><a href=''>$domain[name] $cms[name]</a> &gt; Tagovi</small></p>
      <h4>Tagovi - Pregled svih</h4>
      <p class='float_r'><small>Stranica <b>$page[current]</b> od <b>$page[all]</b>. Rezultati <b>$results[start2]-$results[do]</b> od <b>$results[all]</b>.</small></p>
      <table>
        <tr>
          <th style='width:180px'>Tag</th><th class='align_c' style='width:180px'>Link</th><th>Opis</th><th class='align_c' style='width:40px'>Broj</th>".($akcije ? "<th class='align_c' style='width:70px'>Akcije</th>" : "")."
        </tr>";
      $j = 0;
      $data=$db->fetch_all("SELECT * FROM text_tag ORDER BY title DESC, count DESC LIMIT $results[start], $results[limit]");
      foreach($data as $row){     
        $content["text"].="
        <tr".($j % 2 == 1 ? " class='dark'" : "").">
         <td>".($row['title'] ? "<b>$row[title]</b>" : "$row[tag]")."</td><td>$row[tag_link]</td><td>".cutText($row['text'], 160)."</td><td class='align_c'>$row[count]</td>".($akcije ? "<td>".($user['is_admin'] || ($row["time"] > $cms['mod_time'] && $row['ID_USER'] == $user['id']) ? "<span><a href='$p[1]/edit/$row[tag_link]/' class='edit'>Izmjeni</a></span>" : "")."</td>" : "")."
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
  $content["text"].="
      <div class='clear'></div>
      <p class='help'><b>Pomoć:</b> Tagove nije moguće brisati niti dodavati.</p>";
}
?>
