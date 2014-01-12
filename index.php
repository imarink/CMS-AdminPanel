<?php
error_reporting(E_WARNING);
ini_set('display_errors', '1');

header('Content-Type: text/html; charset=utf-8');
header('Content-Language: hr');
date_default_timezone_set("Europe/Zagreb");

require_once 'codes/class.database.php';
require_once 'codes/connection.php';
require_once 'codes/functions.php';
require_once 'settings.php';
require_once 'codes/user.php';
require_once 'codes/upload.php';

// ako je korisnik prijavljen
if($user['is_mod']){
  switch($p[1]){
    case '':
      /*require_once('page/_home.php');
      loadHome();*/
      header("Location:$domain[url]text/");
      break;
      
    case 'login':
      header("Location: $domain[url]");
      break;
    case 'usercp':
      require_once('page/usersForm.php');
      loadUsersForm($edit = 1);
      break;
    case 'logout':
      require_once('page/_logout.php');
      break;
    
    // TEKSTOVI
    case 'text':
      // dodavanje novog posta
      if($p[2] == "add"){
        require_once('page/textForm.php');
        loadTextForm();
      }
      // izmjena posta
      elseif($p[2] == "edit" && is_numeric($p[3]) && $p[3] > 0){
        $db->fetch_first("SELECT ID_TEXT FROM text WHERE ID_TEXT='$p[3]'".(!$user['is_admin'] ? " AND time>$cms[mod_time] AND ID_USER=$user[id]" : ""));
        if($db->num_rows==1){
          require_once('page/textForm.php');
          loadTextForm($edit = 1);
        }
        else header("Location: $domain[url]$p[1]/");
      }
      // prikaz svih postova
      else{
        require_once('page/text.php');
        getPages("ID_TEXT", "text");
        loadText($page, $results);
      }
      break;
    
    // TAGOVI
    case 'tags':
      // izmjena posta
      if($p[2] == "edit" && strlen($p[3]) > 2 && $user['is_admin']){
        $db->fetch_first("SELECT tag_link FROM text_tag WHERE tag_link='$p[3]'");
        if($db->num_rows==1){
          require_once('page/tagsForm.php');
          loadTagsForm($edit = 1);
        }
        else header("Location: $domain[url]$p[1]/");
      }
      // prikaz svih postova
      else{
        require_once('page/tags.php');
        getPages("tag_link", "text_tag");
        loadTags($page, $results);
      }
      break;
    
    // KOMENTARI
    case 'comments':
      require_once('page/comments.php');
      // prikaz svih komentara
      getPages("ID_COM", "comments");
      loadComments($page, $results);
      break;
    
    // UPLOAD DATOTEKA
    case 'upload':
      // dodavanje nove datoteke
      if($p[2] == "add"){
        require_once('page/uploadForm.php');
        loadUploadForm();
      }
      // izmjena datoteke
      elseif($p[2] == "edit" && is_numeric($p[3]) && $p[3] > 0){
        $db->fetch_first("SELECT ID_UP FROM upload WHERE ID_UP='$p[3]'".(!$user['is_admin'] ? " AND time>$cms[mod_time] AND ID_USER=$user[id]" : ""));
        if($db->num_rows==1){
          require_once('page/uploadForm.php');
          loadUploadForm($edit = 1);
        }
        else header("Location: $domain[url]$p[1]/");
      }
      // prikaz svih datoteka
      else{
        require_once('page/upload.php');
        getPages("ID_UP", "upload");
        loadUpload($page, $results);
      }
      break;
      
    // ANKETE
    case 'poll':
      // dodavanje nove ankete
      if($p[2] == "add" && $user['is_admin']){
        require_once('page/pollForm.php');
        loadPollForm();
      }
      // prikaz svih anketa
      else{
        require_once('page/poll.php');
        getPages("ID_POLL", "poll");
        loadPoll($page, $results);
      }
      break;
    
    // LINKOVI
    case 'links':
      // dodavanje novog linka
      if($p[2] == "add" && $user['is_admin']){
        require_once('page/linksForm.php');
        loadLinksForm();
      }
      // izmjena linka
      elseif($p[2] == "edit" && is_numeric($p[3]) && $p[3] > 0 && $user['is_admin']){
        $db->fetch_first("SELECT ID FROM links WHERE ID='$p[3]'");
        if($db->num_rows==1){
          require_once('page/linksForm.php');
          loadLinksForm($edit = 1);
        }
        else header("Location: $domain[url]$p[1]/");
      }
      // prikaz svih datoteka
      else{
        require_once('page/links.php');
        getPages("ID", "links");
        loadLinks($page, $results);
      }
      break;
    
    // KORISNICI
    case 'users':
      // dodavanje novog korisnika
      if($p[2] == "add" && $user['is_admin']){
        require_once('page/usersForm.php');
        loadUsersForm();
      }
      // prikaz svih korisnika
      else{
        require_once('page/users.php');
        getPages("ID_USER", "users");
        loadUsers($page, $results);
      }
      break;
      
    default:
      notFoundAdmin();
      break;
  }

  // ispis stranice
  require_once 'codes/template.php';
}

// ako korisnik nije prijavljen
else{
  require_once('page/_login.php');
  loadLogin();
  
  // ispis stranice
  require_once('codes/templateLogin.php');
}

$db->close;
exit;

?>
