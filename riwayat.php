<?php require 'config.php';$u=me();$f=in_array($_GET['f']??'',['in','out'])?$_GET['f']:'';
$w=$f?' AND type=?':'';$p=$f?[$u['id'],$f]:[$u['id']];
$s=$db->prepare("SELECT * FROM tx WHERE uid=?$w ORDER BY d DESC,id DESC LIMIT 200");$s->execute($p);$rows=$s->fetchAll(PDO::FETCH_ASSOC);
top($u,'Rincian');?>
<section class="card"><h2><?=deco('coin')?> Rincian pemasukan &amp; pengeluaran</h2>
<div class="seg three"><a href="?"<?=!$f?' class="on"':''?>>Semua</a><a href="?f=in"<?=$f==='in'?' class="on"':''?>>Pemasukan</a><a href="?f=out"<?=$f==='out'?' class="on"':''?>>Pengeluaran</a></div>
<?php if(!$rows):?><p class="empty">Belum ada catatan.</p><?php endif;foreach($rows as $t)txrow($t,'riwayat.php');?></section>
<?php bottom('riwayat');
