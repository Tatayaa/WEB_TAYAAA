<?php require 'config.php';$u=me();check();
if($_SERVER['REQUEST_METHOD']==='POST')$db->prepare('DELETE FROM tx WHERE id=? AND uid=?')->execute([(int)$_POST['id'],$u['id']]);
$b=$_POST['back']??'index.php';header('Location: '.(in_array($b,['index.php','riwayat.php'])?$b:'index.php'));
