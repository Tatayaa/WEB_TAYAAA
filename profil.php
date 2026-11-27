<?php require 'config.php';$u=me();check();$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){$a=$_POST['a']??'';
 if($a==='nama'){$n=trim($_POST['name']??'');if($n!==''){$db->prepare('UPDATE users SET name=? WHERE id=?')->execute([mb_substr($n,0,20),$u['id']]);$msg='Nama diganti.';}}
 elseif($a==='foto'&&isset($_FILES['p'])&&$_FILES['p']['error']===UPLOAD_ERR_OK){$f=$_FILES['p'];$ok=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];$mt=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
  if($f['size']>2*1024*1024||!isset($ok[$mt])||!@getimagesize($f['tmp_name']))$msg='Foto harus JPG/PNG/WEBP, maksimal 2 MB.';
  else{$fn=bin2hex(random_bytes(8)).'.'.$ok[$mt];move_uploaded_file($f['tmp_name'],__DIR__.'/uploads/'.$fn);if($u['photo'])@unlink(__DIR__.'/uploads/'.basename($u['photo']));$db->prepare('UPDATE users SET photo=? WHERE id=?')->execute([$fn,$u['id']]);$msg='Foto profil diganti.';}}
 elseif($a==='hapusfoto'){if($u['photo'])@unlink(__DIR__.'/uploads/'.basename($u['photo']));$db->prepare('UPDATE users SET photo=NULL WHERE id=?')->execute([$u['id']]);$msg='Foto dihapus.';}
 elseif($a==='sandi'){$n=$_POST['new']??'';if(!password_verify($_POST['old']??'',$u['pass']))$msg='Kata sandi lama salah.';
  elseif(strlen($n)<8||!preg_match('/[A-Za-z]/',$n)||!preg_match('/\d/',$n))$msg='Kata sandi baru minimal 8 karakter, ada huruf dan angka.';
  else{$db->prepare('UPDATE users SET pass=? WHERE id=?')->execute([password_hash($n,PASSWORD_DEFAULT),$u['id']]);$msg='Kata sandi diganti.';}}
 $u=me();}
top($u,'Profil');?>
<section class="card center"><?=avatar($u,'big')?><h2><?=e($u['name'])?></h2><div class="lab">@<?=e($u['username'])?></div><?php if($msg):?><div class="tip"><?=e($msg)?></div><?php endif;?></section>
<section class="card"><h2>Foto profil</h2><form method="post" enctype="multipart/form-data" class="f"><?=csrf()?><input type="hidden" name="a" value="foto"><input class="fld" type="file" name="p" accept="image/jpeg,image/png,image/webp" required><button class="btn">Unggah foto</button></form>
<?php if($u['photo']):?><form method="post"><?=csrf()?><input type="hidden" name="a" value="hapusfoto"><button class="btn ghost">Hapus foto</button></form><?php endif;?></section>
<section class="card"><h2>Ganti nama</h2><form method="post" class="f"><?=csrf()?><input type="hidden" name="a" value="nama"><input class="fld" name="name" maxlength="20" value="<?=e($u['name'])?>" required><button class="btn">Simpan</button></form></section>
<section class="card"><h2>Ganti kata sandi</h2><form method="post" class="f"><?=csrf()?><input type="hidden" name="a" value="sandi"><input class="fld" type="password" name="old" placeholder="Kata sandi lama" autocomplete="current-password" required><input class="fld" type="password" name="new" placeholder="Baru (min. 8, huruf + angka)" autocomplete="new-password" required><button class="btn">Ganti</button></form></section>
<a class="btn ghost" href="logout.php">Keluar</a>
<?php bottom('profil');
