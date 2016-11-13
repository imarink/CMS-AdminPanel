<?php
require_once 'codes/class.database.php';
require_once 'codes/connection.php';
require_once 'codes/functions.php';
require_once 'settings.php';
require_once 'codes/user.php';

if(!$_GET["action"] || !$user['is_mod']) die(header("Location: $domain[url]"));

switch($_GET["action"]){

  // BRISANJE TEKSTA
  case "textDelete":
    $url = $domain['url']."text/";
    $id = $_POST['id'];
    if(!isset($id) || !is_numeric($id) || $id < 1) header("Location: $url");
    $row=$db->fetch_first("SELECT ID_TEXT, tags FROM text WHERE ID_TEXT='$id'".(!$user['is_admin'] ? " AND time>$cms[mod_time] AND ID_USER=$user[id]" : ""));
    if($db->num_rows!=1) header("Location: $url");
    if(is_file("../".$content['upload']['folder'][3].$id.$content['upload']['format'][1]))
      unlink("../".$content['upload']['folder'][3].$id.$content['upload']['format'][1]);
    if(is_file("../".$content['upload']['folder'][4].$id.$content['upload']['format'][1]))
      unlink("../".$content['upload']['folder'][4].$id.$content['upload']['format'][1]);
    if(is_file("../".$content['upload']['folder'][5].$id.$content['upload']['format'][1]))
      unlink("../".$content['upload']['folder'][5].$id.$content['upload']['format'][1]);
    $db->delete("text", "ID_TEXT='$id'".(!$user['is_admin'] ? " AND time>$cms[mod_time] AND ID_USER=$user[id]" : ""));
    $db->delete("comments", "ID_TEXT='$id'");
    $tags = explode(",", $row['tags']);
    $num = count($tags);
    for($i = 0; $i < $num; $i++){
      $tag = removeSpace($tags[$i]);
      if(strlen($tag) > 2) $db->update("text_tag", array("count"=>"count-1"), "tag_link='".removeSpecial($tag)."' AND count>0");
    }
    break;
  
  // BRISANJE KOMENTARA
  case "commentDelete":
    $url = $domain['url']."comments/";
    $id = $_POST['id'];
    if(!isset($id) || !is_numeric($id) || $id < 1) header("Location: $url");
    $row=$db->fetch_first("SELECT ID_TEXT FROM comments WHERE ID_COM='$id'".(!$user['is_admin'] ? " AND time>$cms[mod_time]" : ""));
    if($db->num_rows!=1) header("Location: $url");
    $db->delete("comments", "ID_COM='$id'".(!$user['is_admin'] ? " AND time>$cms[mod_time]" : ""));
    $db->update("text", array("comments"=>"comments-1"), "ID_TEXT='$row[ID_TEXT]' AND comments>0");
    break;
  
  // BRISANJE DATOTEKA
  case "uploadDelete":
    $url = $domain['url']."upload/";
    $id = $_POST['id'];
    if(!isset($id) || !is_numeric($id) || $id < 1) header("Location: $url");
    $row=$db->fetch_first("SELECT ID_UP FROM upload WHERE ID_UP='$id'".(!$user['is_admin'] ? " AND time>$cms[mod_time]" : ""));
    if($db->num_rows!=1) header("Location: $url");
    if(is_file("../".$content['upload']['folder'][1].$id.$content['upload']['format'][1]))
      unlink("../".$content['upload']['folder'][1].$id.$content['upload']['format'][1]);
    $db->delete("upload", "ID_UP='$id'".(!$user['is_admin'] ? " AND time>$cms[mod_time]" : ""));
    break;
  
  // BRISANJE ANKETE
  case "pollDelete":
    $url = $domain['url']."poll/";
    $id = $_POST['id'];
    if(!isset($id) || !is_numeric($id) || $id < 1) header("Location: $url");
    $row=$db->fetch_first("SELECT ID_POLL FROM poll WHERE ID_POLL='$id'");
    if($db->num_rows!=1) header("Location: $url");
    $db->delete("poll", "ID_POLL='$id'");
    $db->delete("poll_answers", "ID_POLL='$id'");
    break;
  
  // BRISANJE KOMENTARA
  case "linksDelete":
    $url = $domain['url']."links/";
    $id = $_POST['id'];
    if(!isset($id) || !is_numeric($id) || $id < 1) header("Location: $url");
    $row=$db->fetch_first("SELECT ID FROM links WHERE ID='$id'");
    if($db->num_rows!=1) header("Location: $url");
    $db->delete("links", "ID='$id'");
    break;
}

$db->close;
exit;
?>
