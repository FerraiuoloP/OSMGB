<?php
/*
*** Input:
*** gest_persone.php
*** Output. excel.php
Questo file serve a scegliere la query da eseguire attraverso menù a tendina
Questo file serve  a Scaricare in locale con estensione.xls una tabella ricevuta dal db dopo opportuna query a scelta dell'utente
*** 03/04/2020 M.Scursatone : Creazione file e prima implementazione
*/
$config_path = __DIR__;
$util1 = $config_path .'/../util.php';
require_once $util1;
setup();
$lang=isset($_SESSION['lang'])?$_SESSION['lang']:"ITA";
$jsonFile=file_get_contents("../gestione_lingue/translations.json");//Converto il file json in una stringa
$jsonObj=json_decode($jsonFile);//effettuo il decode della stringa json e la salvo in un oggetto
?>
 <?php
 $util2 = $config_path .'/../db/db_conn.php';
 require_once $util2;
?>

<?php stampaIntestazione(); ?>
<body>
<?php stampaNavbar(); ?>
<form action="excel_moranca.php" method="post" >
SELEZIONARE LA ZONA:
<select name="zona">
<option value="%">tutte</option>   
<option value="N">nord</option>
<option value="O">ovest</option>
<option value="S">sud</option>
</select>
<br>
NOME DEL FILE DA SCARICARE:
<input type="text" name="file" placeholder="*Scrivi un nome*"  value="">
.xls
<br>
<input type='submit' name='invia'>
</form>  