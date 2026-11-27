<?php require 'config.php'; check(); $err='';$mode=($_GET['m']??'masuk')==='daftar'?'daftar':'masuk';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $mode=$_POST['m']==='daftar'?'daftar':'masuk';$un=strtolower(trim($_POST['u']??''));$pw=$_POST['p']??'';
 if(time()<($_SESSION['wait']??0))$err='Terlalu banyak percobaan, tunggu 30 detik.';
 elseif(!preg_match('/^[a-z0-9_]{3,20}$/',$un))$err='Nama pengguna 3–20 karakter: huruf, angka, atau _.';
 elseif($mode==='daftar'){
  if(strlen($pw)<8||!preg_match('/[A-Za-z]/',$pw)||!preg_match('/\d/',$pw))$err='Kata sandi minimal 8 karakter, ada huruf dan angka.';
  else{$c=$db->prepare('SELECT 1 FROM users WHERE username=?');$c->execute([$un]);
   if($c->fetch())$err='Nama pengguna sudah dipakai.';
   else{$db->prepare('INSERT INTO users(username,name,pass) VALUES(?,?,?)')->execute([$un,$un,password_hash($pw,PASSWORD_DEFAULT)]);
    session_regenerate_id(true);$_SESSION['uid']=(int)$db->lastInsertId();header('Location: index.php');exit;}}
 }else{$s=$db->prepare('SELECT * FROM users WHERE username=?');$s->execute([$un]);$r=$s->fetch(PDO::FETCH_ASSOC);
  if($r&&password_verify($pw,$r['pass'])){session_regenerate_id(true);$_SESSION['uid']=(int)$r['id'];$_SESSION['fails']=0;header('Location: index.php');exit;}
  $_SESSION['fails']=($_SESSION['fails']??0)+1;if($_SESSION['fails']>=5){$_SESSION['wait']=time()+30;$_SESSION['fails']=0;}
  $err='Nama pengguna atau kata sandi salah.';}
}?><!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Masuk – Kantong Biru</title><link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600&family=Plus+Jakarta+Sans:wght@500;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="style.css"></head>
<body><main class="gate"><div class="card big"><div class="ic"><?=deco('wallet')?></div><h1 class="wel"><?=$mode==='daftar'?'Buat akun baru':'Selamat datang!'?></h1><p class="sub"><?=$mode==='daftar'?'Mulai catat uangmu dari sini.':'Masuk untuk membuka catatan uangmu.'?></p>
<div class="seg"><a href="?m=masuk"<?=$mode==='masuk'?' class="on"':''?>>Masuk</a><a href="?m=daftar"<?=$mode==='daftar'?' class="on"':''?>>Daftar</a></div>
<div class="note"><div><?=deco('coin')?>Gratis, tanpa biaya apa pun</div><div><?=deco('jamo')?>Nabung untuk targetmu</div><div><?=deco('wallet')?>Pantau saldo kapan saja</div></div>
<form method="post" class="f"><?=csrf()?><input type="hidden" name="m" value="<?=$mode?>">
<input class="fld" name="u" placeholder="Nama pengguna" maxlength="20" autocomplete="username" required>
<input class="fld" type="password" name="p" placeholder="Kata sandi<?=$mode==='daftar'?' (min. 8, huruf + angka)':''?>" autocomplete="<?=$mode==='daftar'?'new-password':'current-password'?>" required>
<div class="err" role="alert"><?=e($err)?></div><button class="btn"><?=$mode==='daftar'?'Daftar sekarang':'Masuk'?></button></form></div></main></body></html>
