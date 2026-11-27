<?php require 'config.php';$u=me();check();
if($_SERVER['REQUEST_METHOD']==='POST'){$a=$_POST['a']??'';$gid=(int)($_POST['g']??0);
 $g=$db->prepare('SELECT * FROM goals WHERE id=? AND uid=?');$g->execute([$gid,$u['id']]);$g=$g->fetch(PDO::FETCH_ASSOC);
 if($a==='goal'){$n=trim($_POST['name']??'');$t=(int)($_POST['target']??0);if($n!==''&&$t>0)$db->prepare('INSERT INTO goals(uid,name,target) VALUES(?,?,?)')->execute([$u['id'],mb_substr($n,0,40),$t]);}
 elseif($a==='save'&&$g&&($v=(int)$_POST['amt'])>0)$db->prepare("INSERT INTO tx(uid,type,amt,cat,note,d,goal_id) VALUES(?,'out',?,'Tabungan',?,?,?)")->execute([$u['id'],$v,'Tabung: '.$g['name'],date('Y-m-d'),$gid]);
 elseif($a==='del'&&$g){$db->prepare('DELETE FROM tx WHERE goal_id=? AND uid=?')->execute([$gid,$u['id']]);$db->prepare('DELETE FROM goals WHERE id=?')->execute([$gid]);}
 header('Location: tabungan.php');exit;}
$gs=$db->prepare('SELECT g.*,COALESCE((SELECT SUM(amt) FROM tx WHERE goal_id=g.id),0) saved FROM goals g WHERE uid=? ORDER BY id DESC');$gs->execute([$u['id']]);$gs=$gs->fetchAll(PDO::FETCH_ASSOC);
top($u,'Tabungan');?>
<section class="card"><h2><?=deco('piggy')?> Tabunganku</h2><?php if(!$gs):?><p class="empty">Belum ada target. Buat satu di bawah ya.</p><?php endif;
foreach($gs as $g):$pc=min(100,$g['saved']/$g['target']*100);?>
<div class="goal"><div class="hd"><b><?=e($g['name'])?></b><form method="post" onsubmit="return confirm('Hapus target ini? Uang yang ditabung kembali ke saldo.')"><?=csrf()?><input type="hidden" name="a" value="del"><input type="hidden" name="g" value="<?=$g['id']?>"><button class="x" aria-label="Hapus target">×</button></form></div>
<div class="prog"><b style="width:<?=$pc?>%"></b></div><div class="lab"><?=rp($g['saved'])?> / <?=rp($g['target'])?> (<?=floor($pc)?>%)<?=$pc>=100?' 🎉':''?></div>
<form method="post" class="row"><?=csrf()?><input type="hidden" name="a" value="save"><input type="hidden" name="g" value="<?=$g['id']?>"><input class="fld" type="number" name="amt" min="1" placeholder="Jumlah (Rp)" required><button class="btn">Tabung</button></form></div><?php endforeach;?></section>
<section class="card"><h2>Target baru</h2><form method="post" class="f"><?=csrf()?><input type="hidden" name="a" value="goal">
<input class="fld" name="name" maxlength="40" placeholder="Nama target (misal: sepatu baru)" required><input class="fld" type="number" name="target" min="1" placeholder="Target (Rp)" required><button class="btn">Buat target</button></form></section>
<?php bottom('tabungan');
