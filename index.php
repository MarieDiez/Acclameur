<?php 
session_start();

// CONNEXION ADMIN PRINCIPALE : mail : admin.mail@gmail.com mdp:motdepasse
// CONNEXION ADMIN SECONDAIRE : mail : admin1.mail@gmail.com mdp:motdepasse
// CONNEXION PERSONNE INSCRITE : mail : personne.mail@gmail.com mdp:motdepasse

/* INDEX */


// Date francaise 
setlocale (LC_TIME, 'fr_FR.utf8','fra');

// fichier de connexion mysql
//include 'connexion.php';

// fichier de connexion postgre 
include 'connexionPostgres.php';

$connexion=connexion();



// REQUETE SQL 

// selectionne le concert principale --> importance = 1
$requete = "
select * 
from concertIndex 
join groupe_artiste ga on ga.id_concert=concertIndex.id_concertIndex
join artiste ar on ar.id_artiste = ga.id_artiste
where concertIndex.importance=1;
";
// traitement de la requete 
$info=$connexion->query($requete);
$res=$info->fetchAll(PDO::FETCH_OBJ);



// selection 4 concerts aleatoire (d'importance :0 --> pas le concert principale) pour la page index
$requete2="
select  *
from ( select distinct on (ci.id_concertIndex) 
	   ci.id_concertIndex,
	   ar.id_artiste,
	   ar.nom_artiste,
	   ci.genreConcert,
	   ci.lieu,
	   ci.dateConcert,
	   ar.descriptionArtiste,
	   ar.img,
	   ci.prix,
	   ci.nbPlaces,
	   ci.nbPlacesLibres

       from concertIndex ci
	   join groupe_artiste ga on ga.id_concert=ci.id_concertIndex
	   join artiste ar on ar.id_artiste=ga.id_artiste
	   where ci.importance =0
	) as ssReq 
ORDER BY RANDOM() limit 4
";
// traitement de la requete
$info2=$connexion->query($requete2);
$res2=$info2->fetchAll(PDO::FETCH_ASSOC);


// selection tous les genres de musique
$requete3="select * from concertGenre";
$info3=$connexion->query($requete3);
$res3=$info3->fetchAll(PDO::FETCH_ASSOC);



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
		<title>L'Acclameur</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
	</head>
	<body class="index is-preload">
		<div id="page-wrapper">

			<!-- Header -->
			<header id="header" class="alt">
				<h1 id="logo"><a href="index.php">Acclameur<span>| Concerts - Informations - Réservations</span></a></h1>
				<nav id="nav">
					<ul>
						<li class="current"><a href="index.php">Home</a></li>
						<li class="submenu">
							<a href="#">Liens</a>
							<ul>
							<li><a href="concertGenre.php?genreconcert=tous">Concerts <span class="icon fa-music"></span></a></li>
									<?php if (isset($_SESSION["admin"])):?>
										<li><a href="panier.php">Mon panier <span class="icon fa-shopping-cart"></span></a></li>
									<?php endif;?>
							</ul>
						</li>
						<?php if ($admin==NULL):?>
						<li><a href="connexionSignIn.php" class="button primary">Connexion</a></li>
						<?php else :?>
						<li><img src=<?=$res4->img?> alt="image connexion" width="35px">
							<ul>
								<li><?=$res4->nom?></li>
								<li><p><?=$res4->prenom?></p></li>
								<?php if ($admin==2):?>
									<a href="infoCommandeClient.php" class="button">Info Commande - Client</a>
									<a href="gestionAdmin.php" class="button">Gérer les admins</a>
									<a href="pregestion.php" class="button">Gérer mon site</a>
									<li><a href="index.php?deco=1" class="button primary">Déconnexion</a><li>
								<?php else :?>
									<?php if ($admin==1):?>
										<a href="infoCommandeClient.php" class="button">Info Commande - Client</a>
										<a href="pregestion.php" class="button">Gérer mon site</a>
									<?php endif;?>
								<li><a href="index.php?deco=1" class="button primary">Déconnexion</a><li>
							<?php endif;?>
						</ul>
					</li>
					<?php endif;?>
						</ul>
					</nav>
					</header>

			<!-- Banner -->
				<section id="banner">

					<div class="inner">

						<header>
							<h2>L'Acclameur</h2>
						</header>
						<p>Bienvenu sur l'<strong>Acclameur</strong>,
						<br />
						<p>Retrouver tout vos concerts ici ! </p>

						<footer>
							<ul class="buttons stacked">
								<li><a href="#main" class="button fit scrolly">En voir plus</a></li>
							</ul>
						</footer>

					</div>

				</section>

			<!-- Main -->
				<article id="main">

					<header class="special container">

						<span class="icon fa-music"></span>
						<h2>chercher et réserver tous vos concerts en ligne</h2>
						<p>Vous trouverez sur ce site toutes les informations nécessaire sur vos concerts préferé</p>
					</header>


					<!-- concert principale -->
					<section id="concertPrincipale">

						<div class="inner">
						<?php if (count($res)!=0):?>
							<header>
								<!-- Nom ( valeur objet) -->
								<h2><?=$res[0]->nom_artiste?></h2>
							</header>
							<br />

							<!-- lieu -->
							<p>Retrouver les au <strong><?=$res[0]->lieu?></strong>,</p>
							<br/>
							<!-- date -->
							<p><?=strftime('%d %B %Y',strtotime($res[0]->dateconcert));?></p>


							<!-- image -->
							<img src=<?=$res[0]->img?> alt="concert du moment" height="320"/>

							<!-- prix -->
							<p class="prixP"><?=$res[0]->prix?>€</p>


							<footer>
								<ul class="buttons stacked">
									<li><a href="infoConcert.php?id_artiste=<?=$res[0]->id_artiste?>& id_concert=<?=$res[0]->id_concert?>" class="button fit scrolly">En savoir plus</a></li>
								</ul>
							</footer>
							<?php else :?>
								<p>Pas de concert à mettre en avant</p>
							
							<?php endif;?>
						</div>
					</section>

					<!-- ConcertIndex -->
						<section class="wrapper style3 container special">

							<header class="major">
								<h2>Regarder aussi</h2>
							</header>

								<!-- compteur d'élement du tableau : cmt -->
								<?php $cmp=0?>

								<!-- Boucle de parcours de la demi des elements du tableau : nombre de ligne -->
								<?php for ($j=0;$j<count($res2)/2;$j++): ?>

									<!-- ligne -->
									<div class="row">

									<!-- Boucle de parcours de 2 elements du tableau selon le compteur : nombre de colonne -->
									<?php for ($i=0;$i<2;$i++):?>

										<!-- colonne -->
										<div class="col-6 col-12-narrower">

											<!-- Mon affichage : image + nom + description-->
											<section>
												<a href="infoConcert.php?id_artiste=<?=$res2[$cmp]['id_artiste']?>& id_concert=<?=$res2[$cmp]['id_concertindex']?>" class="image featured"><img src=<?=$res2[$cmp]['img']?> alt="" height="250" /></a>
												<header>		
													<h3><?=$res2[$cmp]['nom_artiste']?></h3>
												</header>
												<p>À <strong><?=$res2[$cmp]['lieu']?></strong>
												- Le <?=strftime('%d %B %Y',strtotime($res2[$cmp]['dateconcert']))?></br> 												(<?=$res2[$cmp]['nbplaceslibres']?> places restantes)</p>
												<?php if ($res2[$cmp]['prix'] != 0 ):?>
												<p class="prix"><?=$res2[$cmp]['prix']?> €</p>
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



				<!-- Genre de musique -->

				<section class="wrapper style3 container special">

							<header class="major">
								<h2>Séléctionner vos concert selon votre genre préferé</h2>
							</header>



							<!-- compteur d'élement du tableau : cmtp -->			
							<?php $cmtp=0 ;?>

							<!-- Boucle de parcours de la demi des elements du tableau : nombre de ligne -->
							<?php for ($j=0;$j<count($res3)/2;$j++) :?>

							<!-- ligne -->
							<div class="row">

							<!-- Boucle de parcours de 2 elements du tableau selon le compteur : nombre de colonne -->
							<?php for ($i=0;$i<2;$i++):?>

								<!-- colonne -->
								<div class="col-6 col-12-narrower">

									<!-- Mon affichage : image + nom + description-->
									<section>
										<a href="concertGenre.php?genreconcert=<?=$res3[$cmtp]['genreconcert']?>" class="image featured"><img src=<?=$res3[$cmtp]['img']?> alt="" height="250"/></a>
										<header>
											<h3><?=$res3[$cmtp]['genreconcert']?></h3>
										</header>
									</section>

								</div>

							<!-- Incrémentation du compteur d elements : cmtp -->
							<?php $cmtp++ ;?>
							<?php endfor ;?>
							</div>
							<?php endfor ;?>







							<footer class="major">
								<ul class="buttons">
									<li><a href="concertGenre.php?genreconcert=tous" class="button">Voir tous les concerts</a></li> 
								</ul>
							</footer>

						</section>

				</article>

			<!-- CTA -->
				<section id="cta2">
					<header>
						<h2>Inscrivez-vous !</h2>
					</header>
					<footer>
						<ul class="buttons">
							<li><a href="inscription.php" class="button primary">Inscription</a></li>
						</ul>
					</footer>
				</section>

			<!-- Footer -->
			<?php require("footer.php");?>	

		</div>

		<!-- Scripts -->
		<?php require("scripts.php");?>		

	</body>
</html>

