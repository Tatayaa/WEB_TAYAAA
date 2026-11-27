<?php require 'config.php';$u=me();check();$t=($_GET['t']??$_POST['t2']??'out')==='in'?'in':'out';$cats=$t==='in'?$INC:$OUT;$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){$a=(int)$_POST['amt'];$c=in_array($_POST['cat']??'',$cats)?$_POST['cat']:$cats[0];$d=$_POST['d']??'';
 if($a<=0)$err='Isi jumlah dulu.';elseif(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$d)||$d>date('Y-m-d'))$err='Tanggal tidak valid.';
 else{$db->prepare('INSERT INTO tx(uid,type,amt,cat,note,d) VALUES(?,?,?,?,?,?)')->execute([$u['id'],$t,$a,$c,mb_substr(trim($_POST['note']??''),0,40),$d]);header('Location: index.php');exit;}}
top($u,'Catat');?>
<section class="card"><h2><?=$t==='in'?'Catat pemasukan':'Catat pengeluaran'?></h2>
<form method="post" class="f"><?=csrf()?><input type="hidden" name="t2" value="<?=$t?>">
<input class="fld" type="number" name="amt" min="1" placeholder="Jumlah (Rp)" required autofocus>
<select class="fld" name="cat"><?php foreach($cats as $c):?><option><?=e($c)?></option><?php endforeach;?></select>
<input class="fld" name="note" maxlength="40" placeholder="Catatan (opsional)">
<input class="fld" type="date" name="d" value="<?=date('Y-m-d')?>" max="<?=date('Y-m-d')?>">
<div class="err"><?=e($err)?></div><button class="btn">Simpan</button><a class="btn ghost" href="index.php">Batal</a></form></section>
<?php bottom('index');
