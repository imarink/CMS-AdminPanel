<?php
function loadText($page, $results){
  global $db, $p, $content, $domain, $cms, $user;
  
  if($user['is_admin']) $akcije = 1;
  else{
    $array=$db->fetch_all("SELECT ID_USER, time FROM text ORDER BY time DESC LIMIT $results[start], $results[limit]");
    foreach($array as $var)
      if($var["time"] > $cms['mod_time'] && $var['ID_USER'] == $user['id'] && !$akcije) $akcije = 1;
  }

  $content["title"]= "Tekstovi";
  
  if($akcije)
  $content["js"]="
<script type='text/javascript'>
  $().ready(function(){
    $('a.delete').click(function(){
      if(confirm('Jeste li sigurni da želite izbrisati tekst \"'+$('#naziv_'+$(this).attr('rel')).html()+'\"? Akcija je nepovratna!')){
        $.ajax({
          type: 'POST',
          url: 'ajax.php?action=textDelete',
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
      <p><small><a href=''>$domain[name] $cms[name]</a> &gt; Tekstovi</small></p>
      <h4>Tekstovi - Pregled svih</h4>
      <p class='float_r'><small>Stranica <b>$page[current]</b> od <b>$page[all]</b>. Rezultati <b>$results[start2]-$results[do]</b> od <b>$results[all]</b>.</small></p>
      <p><span><a href='$p[1]/add/' class='add'>Dodaj tekst</a></span></p>
      <table>
        <tr>
          <th class='align_c' style='width:25px'>ID</th><th>Naslov</th><th class='align_c' style='width:95px'>Kategorija</th><th style='width:110px'>Izvor</th><th class='align_c' style='width:65px'>Pregleda</th><th class='align_c' style='width:70px'>Datum</th>".($akcije ? "<th style='width:130px'>Akcije</th>" : "")."
        </tr>";
      $j = 0;
      $data=$db->fetch_all("SELECT * FROM text ORDER BY published ASC, time DESC LIMIT $results[start], $results[limit]");
      foreach($data as $row){
        if($row['source']) $row['source'] = "<span><a href='$row[source]' class='link' target='_blank'>".getHost($row['source'])."</a></span>";
        else $row['source'] = "-";
        
        $content["text"].="
        <tr".($j % 2 == 1 ? " class='dark'" : "").">
          <td class='align_c'>".$row['ID_TEXT']."</td>".($row['special'] ? "<td".(!$row['published'] ? " class='admin'" : "")."><b id='naziv_$row[ID_TEXT]'>$row[title]</b></td>" : "<td id='naziv_$row[ID_TEXT]'".(!$row['published'] ? " class='admin'" : "").">$row[title]</td>")."<td class='align_c'><small>[$row[ID_CAT]]</small> ".$content['categs'][$row['ID_CAT']]."</td><td>$row[source]</td><td class='align_c'>$row[views]</td><td class='align_c'>".date("d.m.Y.", $row['time'])."</td>".($akcije ? "<td>".($user['is_admin'] || ($row["time"] > $cms['mod_time'] && $row['ID_USER'] == $user['id']) ? "<span><a href='$p[1]/edit/$row[ID_TEXT]/' class='edit'>Izmjeni</a> <a href='#' class='delete' rel='$row[ID_TEXT]'>Obriši</a></span>" : "")."</td>" : "")."
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
      <p><span><a href='$p[1]/add/' class='add'>Dodaj tekst</a></span></p>
      <p class='help'><b>Pomoć:</b> Prilikom brisanja, biti će obrisani i svi komentari tog teksta.</p>";
}
?>
