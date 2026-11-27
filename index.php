<?php require 'config.php';$u=me();$m=date('Y-m');
$q=$db->prepare("SELECT type,COALESCE(SUM(amt),0) s FROM tx WHERE uid=? AND substr(d,1,7)=? AND cat<>'Tabungan' GROUP BY type");$q->execute([$u['id'],$m]);$mm=['in'=>0,'out'=>0];foreach($q as $r)$mm[$r['type']]=$r['s'];
$sv=$db->prepare("SELECT COALESCE(SUM(amt),0) FROM tx WHERE uid=? AND goal_id IS NOT NULL");$sv->execute([$u['id']]);$tab=$sv->fetchColumn();
$l=$db->prepare('SELECT * FROM tx WHERE uid=? ORDER BY d DESC,id DESC LIMIT 3');$l->execute([$u['id']]);$last=$l->fetchAll(PDO::FETCH_ASSOC);
top($u,'Beranda');?>
<section class="hero"><small>Halo, <?=e($u['name'])?>! Saldo kamu</small><div class="bal"><?=rp(saldo($u['id']))?></div>
<div class="io"><div><small>Masuk bulan ini</small><b><?=rp($mm['in'])?></b></div><div><small>Keluar bulan ini</small><b><?=rp($mm['out'])?></b></div></div></section>
<div class="two"><a class="btn out" href="tambah.php?t=out">− Pengeluaran</a><a class="btn in" href="tambah.php?t=in">+ Pemasukan</a></div>
<a class="card link" href="tabungan.php"><?=deco('piggy')?><div><b>Tabunganmu</b><div class="lab"><?=rp($tab)?> terkumpul · mau menabung? Buka halaman tabungan</div></div><span>›</span></a>
<section class="card"><div class="hd"><h2><?=deco('coin')?> 3 catatan terbaru</h2><a href="riwayat.php">Lihat semua</a></div>
<?php if(!$last):?><p class="empty">Belum ada catatan.</p><?php endif;foreach($last as $t)txrow($t,'index.php');?></section>
<?php bottom('index');
