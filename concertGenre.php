<?php
session_start();

/* PAGE D AFFICHAGE DES CONCERT SELON LE GENRE*/


// Date francaise 
setlocale (LC_TIME, 'fr_FR.utf8','fra');

// fichier de connexion mysql
//include 'connexion.php';

// fichier de connexion postgre 
include 'connexionPostgres.php';

$connexion=connexion();

// si il y a bien eu un passage par url de genreconcert et que la variable n est pas vide on recupere le contenu
if (isset ($_GET['genreconcert'])){
	if(!empty($_GET['genreconcert'])){
		$genre=$_GET['genreconcert'];
	}
}
// si il n y a pas eu de passage par url on renvoie vers la page index
else {
	header("Location:index.php");
}

// si le contenu est tous --> on affiche touts les genres
if ($genre == "tous"){
	$sql="select * from concertIndex ci
	join groupe_artiste ga on ga.id_concert=ci.id_concertIndex
	join artiste ar on ar.id_artiste=ga.id_artiste";
	$info=$connexion->query($sql);
}
// sinon on selectionne tous les concerts du genre preciser
else {
	$sql="select * from concertIndex ci
	join groupe_artiste ga on ga.id_concert=ci.id_concertIndex
	join artiste ar on ar.id_artiste=ga.id_artiste
	where ci.genreconcert=:genreconcert;";
	$info=$connexion->prepare($sql);
	$info->bindParam(':genreconcert',$genre);
	$info->execute();
	
}
$res=$info->fetchAll(PDO::FETCH_ASSOC);


$sql1="select description from concertGenre 
	where concertGenre.genreconcert=:genreconcert;";
$info1=$connexion->prepare($sql1);
$info1->bindParam(':genreconcert',$genre);
$info1->execute();
$res1=$info1->fetch(PDO::FETCH_ASSOC);
	
	
	
// Admin

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
			<img src="../images/genre<?=$genre?>.jpg" alt="" height="500px" width="100%">
					
				<article id="main">	

				<!-- Three -->
						<section class="wrapper style3 container special">

							<header class="major">
								<?php if($genre != "tous"):?>
									<h2>Tous les concerts de <?=$genre?></h2>
								<?php else :?>
									<h2>Tous les concerts</h2>
								<?php endif;?>
							</header>

								<!-- compteur d'élement du tableau : cmt -->
								<?php $cmp=0?>
								
								<!-- Boucle de parcours de la demi des elements du tableau : nombre de ligne -->
								<?php for ($j=0;$j<count($res)/2;$j++): ?>
									
									<!-- ligne -->
									<div class="row">
									
									<!-- Boucle de parcours de 2 elements du tableau selon le compteur : nombre de colonne -->
									<?php for ($i=$count;$i<2;$i++):?>
										
										<!-- colonne -->
										<div class="col-6 col-12-narrower">
										
											<!-- Mon affichage : image + nom + description-->
											<section>
		<a href="infoConcert.php?id_artiste=<?=$res[$cmp]['id_artiste']?>& id_concert=<?=$res[$cmp]['id_concertindex']?>" class="image featured">
												<img src=<?=$res[$cmp]['img']?> alt="img artiste" height="250" /></a>
												<header>		
													<h3><?=$res[$cmp]['nom_artiste']?></h3>
												</header>
												<p>À <strong><?=$res[$cmp]['lieu']?></strong>
												- Le <?=strftime('%d %B %Y',strtotime($res[$cmp]['dateconcert']))?></br> 												(<?=$res[$cmp]['nbplaceslibres']?> places restantes)</p>
												<?php if ($res[$cmp]['prix'] != 0 ):?>
												<p class="prix"><?=$res[$cmp]['prix']?> €</p>
												<?php else :?>
												<p class="prix">Gratuit</p>
												<?php endif;?>
											</section>	
										</div>	
								
									<!-- Incrémentation du compteur d elements-->
									<?php $cmp++ ;?>
									<?php endfor;?>
									</div>
									
								<?php endfor;?>
					
					<?php if($genre != "tous"):?>
					<section class="wrapper style3 container special">
						<p>Description : </p>
						<p><?=$res1[description]?></p>
					</section>
					<?php endif;?>
				</article>

			<!-- Footer -->
			<?php require("footer.php");?>

		</div>

		<!-- Scripts -->
		<?php require("scripts.php");?>

	</body>
</html>
