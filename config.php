<?php
session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax']);
session_start();
$db=new PDO('sqlite:'.__DIR__.'/data/kantong.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
$db->exec("CREATE TABLE IF NOT EXISTS users(id INTEGER PRIMARY KEY,username TEXT UNIQUE,name TEXT,pass TEXT,photo TEXT);
CREATE TABLE IF NOT EXISTS goals(id INTEGER PRIMARY KEY,uid INT,name TEXT,target INT);
CREATE TABLE IF NOT EXISTS tx(id INTEGER PRIMARY KEY,uid INT,type TEXT,amt INT,cat TEXT,note TEXT,d TEXT,goal_id INT);");

// Foto hiasan kecil: ganti path ini dengan fotomu sendiri (jpg/png, kotak, ±100px sudah cukup)
$DECO=['coin'=>'assets/deco/coin.svg','jamo'=>'assets/deco/jamo.svg','wallet'=>'assets/deco/wallet.svg'];
$OUT=['Makan','Transport','Jajan','Belajar','Hiburan','Lainnya'];
$INC=['Uang saku','Hadiah','Kerja','Lainnya'];

function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function rp($n){return 'Rp'.number_format((int)$n,0,',','.');}
function deco($k){global $DECO;return '<img class="deco" src="'.e($DECO[$k]).'" alt="" width="22" height="22">';}
function csrf(){if(empty($_SESSION['t']))$_SESSION['t']=bin2hex(random_bytes(16));return '<input type="hidden" name="t" value="'.$_SESSION['t'].'">';}
function check(){if($_SERVER['REQUEST_METHOD']==='POST'&&!hash_equals($_SESSION['t']??'',$_POST['t']??'')){http_response_code(400);exit('Sesi tidak valid, muat ulang halaman.');}}
function me(){global $db;if(empty($_SESSION['uid'])){header('Location: login.php');exit;}
 $s=$db->prepare('SELECT * FROM users WHERE id=?');$s->execute([$_SESSION['uid']]);$u=$s->fetch(PDO::FETCH_ASSOC);
 if(!$u){session_destroy();header('Location: login.php');exit;}return $u;}
function avatar($u,$c=''){return $u['photo']?'<img class="av '.$c.'" src="uploads/'.e($u['photo']).'" alt="Foto profil">':'<span class="av '.$c.'">'.e(mb_strtoupper(mb_substr($u['name'],0,1))).'</span>';}
function top($u,$title){?><!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($title)?> – Kantong Biru</title><link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600&family=Plus+Jakarta+Sans:wght@500;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="style.css"></head><body>
<main><div class="top"><h1><?=deco('wallet')?> Kantong Biru</h1><a href="profil.php" aria-label="Profil"><?=avatar($u)?></a></div>
<?php }
function bottom($a){$n=['index'=>'Beranda','riwayat'=>'Rincian','tabungan'=>'Tabungan','profil'=>'Profil'];?>
</main><nav class="nav"><?php foreach($n as $k=>$v):?><a href="<?=$k?>.php"<?=$k===$a?' class="on"':''?>><?=$v?></a><?php endforeach;?></nav></body></html><?php }
function saldo($id){global $db;$s=$db->prepare("SELECT COALESCE(SUM(CASE WHEN type='in' THEN amt ELSE -amt END),0) FROM tx WHERE uid=?");$s->execute([$id]);return (int)$s->fetchColumn();}
function txrow($t,$back){?><div class="tx"><span class="em"><?=deco($t['type']==='in'?'coin':($t['cat']==='Tabungan'?'jamo':'wallet'))?></span>
<div class="t"><b><?=e($t['note']?:$t['cat'])?></b><span><?=e($t['cat'])?> · <?=e(date('d/m/Y',strtotime($t['d'])))?></span></div>
<b class="amt <?=$t['type']?>"><?=$t['type']==='in'?'+':'−'?><?=rp($t['amt'])?></b>
<form method="post" action="hapus.php" onsubmit="return confirm('Hapus catatan ini?')"><?=csrf()?><input type="hidden" name="id" value="<?=$t['id']?>"><input type="hidden" name="back" value="<?=e($back)?>"><button class="x" aria-label="Hapus">×</button></form></div><?php }
