<?php 
session_start();

/* PAGE DES COMMANDES */

include 'connexionPostgres.php';
$connexion=connexion();

// Date francaise 
setlocale (LC_TIME, 'fr_FR.utf8','fra');

// selectionne toutes les commandes 
$sql = "select * from commande";
$info=$connexion->query($sql);
$res=$info->fetchAll(PDO::FETCH_OBJ);

// pour chaque commande selectionne les ligne de commande -> tous les concerts d une commande  
for ($i=0;$i<count($res);$i++){
	$idCmd=$res[$i]->id_commande;
	$sql1 = "select * from ligne_commande join concertIndex ci on ci.id_concertindex=ligne_commande.id_concert
	where id_commande=:id";
	$info1=$connexion->prepare($sql1);
	$info1->bindParam(":id",$idCmd);
	$info1->execute();
	$res1[]=$info1->fetchAll(PDO::FETCH_OBJ);
}


// info personne
for ($i=0;$i<count($res);$i++){
	$idClient=$res[$i]->id_client;
	$sqlClient="select * from personne where id_personne=:id";
	$infoClient=$connexion->prepare($sqlClient);
	$infoClient->bindParam(":id",$idClient);
	$infoClient->execute();
	$resClient[]=$infoClient->fetch(PDO::FETCH_OBJ);
}


if (count($res)==0){
	$vide=true;
}

// Admin
// si la session est démarrer -> il y a eu une commexion : on regarde la valeur de l'admin
if (isset($_SESSION["admin"]) && !empty($_SESSION["admin"])){
// valeur possible : 0/1/2 
// 0 -> pas adminInscri
// 1 -> admin secondaire
// 2 -> admin principale
$admin=$_SESSION["admin"];
// on recupere les données de la personne avec son mail 
if (isset($_SESSION["mail"]) && !empty($_SESSION["mail"])){
	$mail=$_SESSION["mail"];
	// si admin : on cherche les informations dans la table admin
	if ($admin==1 || $admin==2){
		$requete4="select img,nom,prenom,adminPrincipal from personne 
			join adminInscrit on personne.id_personne = adminInscrit.id_personne
			where adminInscrit.mail=:mail";
		}
		// sinon dans la table personnne
		else{
			$requete4="select img,nom,prenom from personne 
				join personneInscrite on personne.id_personne = personneInscrite.id_personne
				where personneInscrite.mail=:mail";
			}
			$info4=$connexion->prepare($requete4);
			$info4->bindParam(":mail",$mail);
			$info4->execute();
			$res4=$info4->fetch(PDO::FETCH_OBJ);
		}
	}	
// si il y a une deconnexion --> avec passage par url : on detruit la session et on redirige vers la page d'acceuil
if (isset ($_GET['deco']) && !empty($_GET['deco'])){
	session_destroy();
	header("Location:index.php");
}	


?>
			
<!DOCTYPE HTML>
<html>
	<head>
		<title>Informations | Commandes - Clients</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
	</head>
	<body class="index is-preload">
		<div id="page-wrapper">
			
			<!-- Header -->
			<?php require("header.php");?>
			
			<article id="main">
			
			
			<header class="special container">
				<span class="icon  fa-cog"></span>
				<h2>Informations | Commandes - Clients</h2>
			</header>
			
			
			<section class="wrapper style3 container">
			<?php if ($vide):?>
				<p>Pas de commande</p>
			<?php else:?>
			<ul>
				<!-- pour chaque commande -->
				<?php for($i=0;$i<count($res);$i++):?>
					<h3>Commande #<?=$i+1?> : <?=strftime('%d/%m/%y',strtotime($res[$i]->date))?> ➭ <?=$res[$i]->prix_total?>€</h3>
					<li><p>Client : <a href="infoClient.php?id_client=<?=$res[$i]->id_client?>" ><?=$resClient[$i]->nom?> <?=$resClient[$i]->prenom?></a> </li>
				
					<!-- toutes les lignes de commandes -> tous les billets d une commande -->
					<?php foreach($res1 as $k=>$v):?>
					
						<!-- si l id du concert est le meme -->
						<?php if ($k==$i):?>
						
							<!-- on affiche tous les billets -->
							<?php foreach($v as $k1=>$v1):?>
									<li><?=$v1->qte?> Billet : <?=$v1->lieu?> le <?=strftime('%d %B %Y',strtotime($v1->dateconcert))?> pour <?=$v1->prix?>€</li>
							
							<?php endforeach;?>
						<?php endif;?>
					<?php endforeach;?>
					
				<p style="margin-bottom:50px"></p>
				<?php endfor;?>
				</ul>
			<?php endif;?>
			</section>
			</article>
			
			<!-- Footer -->
			<?php require("footer.php");?>
		</div>
		<!-- Scripts -->
		<?php require("scripts.php");?>
	</body>
</html>


