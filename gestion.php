<?php
session_start();


/* PAGE D AFFICHAGE DES CONCERTS + SUPPRESSION */


// Date francaise 
setlocale (LC_TIME, 'fr_FR.utf8','fra');

// fichier de connexion mysql
//include 'connexion.php';

// fichier de connexion postgre 
include 'connexionPostgres.php';

$connexion=connexion();



// REQUETE SQL

// selectionne tous les concerts par ordre alphabetique de lieu
$sql="select  *
from ( select distinct on (ci.id_concertIndex) *
       from concertIndex ci
	) as ssReq order by ssReq.lieu  ";
$info=$connexion->query($sql);
$res=$info->fetchAll(PDO::FETCH_OBJ);


// si on supprime un concert : on se redirige ici avec un passage par url pour indiquer qu il y a une suppression	
// on envoie l id du concert a supprimer	
if (isset($_GET['supprimer']) && isset($_GET['idconcert']) && !empty($_GET['supprimer']) && !empty($_GET['idconcert'])){
		
	// on recupere l id du concert
	$idconcert=$_GET['idconcert'];
	// on supprime le lien du concert avec le ou les artistes associés
	$requete5="delete from groupe_artiste where  id_concert=:idconcert;";
	// on supprime le concert de la base de donnée
	$requete6="delete from concertIndex where  id_concertIndex=:idconcert";
	$info5=$connexion->prepare($requete5);
	$info5->bindParam(":idconcert",$idconcert);
	$info5->execute();
	$info6=$connexion->prepare($requete6);
	$info6->bindParam(":idconcert",$idconcert);
	$info6->execute();
		
	// on se redirige vers la page de gestion
	header("Location:gestion.php");
		
}

// Admin -> commenté en index
if (isset($_SESSION["admin"]) && !empty($_SESSION["admin"])){
	$admin=$_SESSION["admin"];
	if (isset($_SESSION["mail"]) && !empty($_SESSION["mail"])){
		$mail=$_SESSION["mail"];	   
		if ($admin==1 || $admin==2){
			$requete4="select img,nom,prenom,adminPrincipal from personne 
				   join adminInscrit on personne.id_personne = adminInscrit.id_personne
				   where adminInscrit.mail=:mail";
		}
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

if (isset ($_GET['deco']) && !empty($_GET['deco'])){
	session_destroy();
	header("Location:index.php");
}				

?>




<!DOCTYPE HTML>

<html>
	<head>
		<title><?=$res->genreconcert?></title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
	</head>
	<body class="no-sidebar is-preload">
		<div id="page-wrapper">

			<!-- Header -->
			<?php require("header.php");?>


			<!-- Main -->
			<article id="main">
					
					<!-- ICONE Modif -->
					<header class="special container">
						<span class="icon fa-edit"></span>
						<h2>Modifier / Supprimer</h2>
						<h2>Des concerts</h2>
					</header>
					
					
					<!-- section -->
					<section class="wrapper style3 container special">

						<!-- ICONE Concert -->
						<header class="special container">
							<span class="icon fa-volume-up"></span>
							<h2>Tous les concerts</h2>
						</header>

						<!-- Boucle d affichage des concert -->
						<?php for ($i=0;$i<count($res);$i++):?>
							<!-- on affiche le lieu et la date du concert -->
							<p class="cadre"> <?=$res[$i]->lieu?> le <?=strftime('%d %B %Y',strtotime($res[$i]->dateconcert))?></p> 
							<!-- si on veut modifier un concert on se redirige vers la page de modification avec l id du concert -->
							<a class="button" href="modif.php?idconcert=<?=$res[$i]->id_concertindex?>">Modifier</a>
							<!-- si on veut supprimer on se redirige vers cette page avec un parametre -->
							<a class="button" href="gestion.php?supprimer=1&idconcert=<?=$res[$i]->id_concertindex?>">Supprimer</a>
							<hr>
						<?php endfor;?>	
	
					</section>
			</article>
			
			
			<!-- Footer -->
			<?php require("footer.php");?>

		</div>

		<!-- Scripts -->
		<?php require("scripts.php");?>

	</body>
</html>
