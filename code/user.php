<?php

$cookie = explode(",", cleanup($_COOKIE['admin']));

$user['id'] = $cookie[0];
$user['username'] = $cookie[1];
$user['password'] = $cookie[2];
$user['email'] = $cookie[3];

$row=$db->fetch_first("SELECT realname, website, is_admin FROM users WHERE ID_USER='$user[id]' AND username='$user[username]' AND password='$user[password]' AND email='$user[email]' AND is_mod='1'");

if($db->num_rows==1){
  $user['realname'] = $row['realname'];
  $user['website'] = $row['website'];
  $user['is_admin'] = $row['is_admin'];
  $user['is_mod'] = 1;
}
else{
  unset($user);
}

?>
