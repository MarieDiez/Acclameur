<?php
include '../connexion.php';

$connexion=connexion();


$sql="select distinct * from categorie";
$info=$connexion->query($sql); 
$res=$info->fetchAll(PDO::FETCH_OBJ);


// selctionne toutes les cmd
$sqlcmd="select * from commande";
$infocmd=$connexion->query($sqlcmd);
$rescmd=$infocmd->fetchAll(PDO::FETCH_OBJ);


// info du client
for ($i=0;$i<count($rescmd);$i++){
	$id=$rescmd[$i]->id_client;

	$sqlnom="select * from client where id_client=:id";
	$infonom=$connexion->prepare($sqlnom);
	$infonom->bindParam(":id",$id);
	$infonom->execute();
	$resnom[]=$infonom->fetch(PDO::FETCH_OBJ);
}


if (isset($_POST["send"]) && !empty($_POST["designation"]) && !empty($_POST["description"]) && !empty($_POST["categorie"]) && !empty($_POST["prix"]) && !empty($_POST["tva"]) && $_FILES['fichier']['error']==0){
	$des=htmlspecialchars($_POST["designation"]);
	$descr=htmlspecialchars($_POST["description"]);
	$categorie=htmlspecialchars($_POST["categorie"]);
	$prix=htmlspecialchars($_POST["prix"]);
	$tva=htmlspecialchars($_POST['tva']);
	$img='images/magasin/'.$_FILES["fichier"]["name"];  
	
	move_uploaded_file($_FILES["fichier"]["tmp_name"], '../images/magasin/'.$_FILES["fichier"]["name"]);
	
	// une image par article
	$sql3="select * from article where img_article=:img";
	$info3=$connexion->prepare($sql3);
	$info3->bindParam(':img',$img);
	$info3->execute();
	$res3=$info3->fetchAll(PDO::FETCH_OBJ);
	
	if (count($res3)==0){ // article pas dans la base 
		$sql2="insert into article (id_categorie,designation, prix, tva, description, img_article) values 				(:categorie,:designation,:prix,:tva,:description,:img)";
		$info2=$connexion->prepare($sql2);
		$info2->execute(array("categorie"=>$categorie,
		"designation"=>$des,"prix"=>$prix,"tva"=>$tva,"description"=>$descr,"img"=>$img));
		echo("<script>alert(\"Article ajouté !\")</script>");
	}
	else {
		echo("<script>alert(\"Article déjà dans la base !\")</script>");
	}
	
}

if (isset($_POST["supdesignation"]) && !empty($_POST["supdesignation"]) ){
	$des=$_POST["supdesignation"];
	
	$sqlverif="select * from article where designation=:des";
	$infoverif=$connexion->prepare($sqlverif);
	$infoverif->bindParam(":des",$des);
	$infoverif->execute();
	$resverif=$infoverif->fetchAll(PDO::FETCH_OBJ);
	
	if (count($resverif)!=0){
		$sql= "delete from article where designation=:des";
		$info=$connexion->prepare($sql);
		$info->bindParam(":des",$des);
		$info->execute();
		
		echo("<script>alert(\"Article supprimé !\")</script>");
	}
	else {
		echo("<script>alert(\"L'article n'existe pas !\")</script>");
	}
	
}

else {
	$des="";
	$descr="";
	$categorie="";
	$prix="";
	$img="";
}

?>



<!DOCTYPE html>
<html>
 <head>
	<meta charset="utf-8" />
  	<link href="../css/styleadmin.css" rel="stylesheet" type="text/css" />
	<title>SiteWebShop</title>

</head>
<body>



	<div id="container">
		<h1> Administration du site OpenShop</h1>
		
			<form method="post" action="index.php" enctype="multipart/form-data">
			<fieldset>
				<label>Désignation</label>
				<input type="text" name="designation"></br></br>
				<label>Description</label>
				<input type="text" name="description"></br></br>
				<label>Catégorie</label>
				<SELECT name="categorie">
					<?php for ($i=0;$i<count($res);$i++):?>
					<OPTION value="<?=$res[$i]->id_categorie?>"><?=$res[$i]->nom?></OPTION>
					<?php endfor;?>
				</SELECT></br></br>
				<label>Prix</label>
				<input type="text" name="prix"></br></br>
				<label>TVA</label>
				<input type="text" name="tva"></br></br>
				<label>Image : </label>
				<input type='file' name='fichier'></br></br>
				</fieldset>
				<input type="submit" name="send" value="envoyer">
			</form><br>
			
			<h1>Supprimer un article </h1>
			<form method="post" action="index.php" enctype="multipart/form-data">
			<fieldset>
				<label>Désignation</label>
				<input type="text" name="supdesignation"></br></br>
				</fieldset>
				<input type="submit" name="send" value="envoyer">
			</form>
		
			<h1>Toutes les commandes</h1>
			<?php foreach($rescmd as $k=>$v):?>
				<ul>
					<li>Commande du client <a href="infoClient.php?id_client=<?=$resnom[$k]->id_client?>"> <?=$resnom[$k]->nom?> <?=$resnom[$k]->prenom?></a> le <?=strftime('%d %B %Y',strtotime($v->date))?> d'une valeur de <?=$v->prix_total?>€</li>
				</ul>
			<?php endforeach;?>
			
			
	</div>


</body>
</html>
