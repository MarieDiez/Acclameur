<?php
session_start();


/* PAGE D AJOUT DE CONCERT / D ARTISTE / DE LIEN CONCERT - ARTISTE */

// Date francaise 
setlocale (LC_TIME, 'fr_FR.utf8','fra');

// fichier de connexion mysql
//include 'connexion.php';

// fichier de connexion postgre 
include 'connexionPostgres.php';

$connexion=connexion();


// fonction de redirection
function redirect($url,$time=0) {
	echo "<meta http-equiv=\"refresh\" content=\"$time; URL=$url\" />";
	exit();
}


// REQUETES
 
// selectionne tous les concert par ordre alphabetique de lieu : sert pour liaison concert - artiste 
$reqConcert="
select lieu
from (
	select distinct on (concertIndex.lieu)
	*
	from concertIndex
) as concert 
ORDER BY concert.lieu";
$infoConcert=$connexion->query($reqConcert);
$resConcert=$infoConcert->fetchAll(PDO::FETCH_OBJ);


// selectionne toutes les dates de concerts disctinctement : sert pour liaison concert - artiste
$reqConcertDate="
select distinct dateconcert
from concertIndex 
ORDER BY concertIndex.dateconcert";
$infoConcertDate=$connexion->query($reqConcertDate);
$resConcertDate=$infoConcertDate->fetchAll(PDO::FETCH_OBJ);


// selectionne tout les genres de concert distinctement : sert pour la creation de concert
$reqConcertGenre="
select distinct genreconcert
from concertGenre 
ORDER BY concertGenre.genreconcert";
$infoConcertGenre=$connexion->query($reqConcertGenre);
$resConcertGenre=$infoConcertGenre->fetchAll(PDO::FETCH_OBJ);


// selectionne tous les artistes par ordre alphabetique de nom d artiste : sert pour la liaison concert-artiste
$reqArtiste="select * from artiste order by artiste.nom_artiste";
$infoArtiste=$connexion->query($reqArtiste);
$resArtiste=$infoArtiste->fetchAll(PDO::FETCH_OBJ);



// nombre d'artiste a lié avec le concert
if (isset($_GET['nblienconcert'])){
	$nblienconcert=$_GET['nblienconcert'];
	
}



// nombre de concert a lié avec l 'artiste
if (isset($_GET['nblienartiste'])){
	$nblienartiste=$_GET['nblienartiste'];

}

// Faire un lien concert / artiste
if (isset($_GET['ajouterConcertArtiste']) && !empty($_GET['ajouterConcertArtiste'])){
	
	// on recupere la valeur
	$ajouterConcertArtiste=$_GET['ajouterConcertArtiste'];
	
	// POST
	if ($_POST["send"]){
	
		// si on a le nombre de lien a faire avec un concert on travail dessus
		if (isset($_GET['nblienartiste'])){
			$nblienartiste=$_GET['nblienartiste'];
			$nb=$nblienartiste;
		}
		// sinon si on a le nombre de lien a faire avec un artiste on travail dessus
		else if (isset($_GET['nblienconcert'])){
			$nb=$nblienconcert;
		}
		// sinon on fait le lien de base : 1 concert avec un artiste
		else {
			$nb=1;
		}
		
		// pour chaque lien a faire
		for ($i=0;$i<$nb;$i++){
		
			// liens avec n concert
			if (isset($_GET['nblienartiste'])){
			
				// on recupere les valeurs du formulaire 
				// il peut y avoir plusieurs concert <-> lieu/date --> [$i]
				$dateconcert=$_POST["dateconcert"][$i];
				$lieuconcert=$_POST["lieuconcert"][$i];
				
				// il peut n y avoir qu un artiste
				$nom=$_POST["artiste"];
				
				// on selectionne l id de l artiste a partir de son nom
				$req="select id_artiste from artiste where nom_artiste=:nom_artiste";
				$inf=$connexion->prepare($req);
				$inf->bindParam(':nom_artiste',$nom);
				$inf->execute();
				$res=$inf->fetchAll(PDO::FETCH_OBJ);
				$artiste=$res[0]->id_artiste;
				
			}
			// lien avec n artiste
			else if (isset($_GET['nblienconcert'])){
			
				// on recupere les valeurs du formulaire
				// il peut n y avoir qu un concert
				$dateconcert=$_POST["dateconcert"];
				$lieuconcert=$_POST["lieuconcert"];
				
				// il peut y avoir plusieurs artiste -> [$i]
				$artiste=$_POST["artiste"][$i];

			}
			// lien 1 concert / 1 artiste
			else {
				$dateconcert=$_POST["dateconcert"];
				$lieuconcert=$_POST["lieuconcert"];
				$artiste=$_POST["artiste"];
			}
			
			// on selectionne l id du concert avec le lieu et la date
			$sql0="select id_concertindex from concertIndex where lieu=:lieu and dateconcert=:date";
			$info0=$connexion->prepare($sql0);
			$info0->bindParam(":lieu",$lieuconcert);
			$info0->bindParam(":date",$dateconcert);
			$info0->execute();
			$res0=$info0->fetchAll(PDO::FETCH_OBJ);
			
		
			// on verifie qu il existe
			if (count($res0)==0){
				echo("<script>alert(\"Le concert n'existe pas\")</script>");
			}
			// si il existe
			else {
				
				// on recupere l id
				$concert=$res0[0]->id_concertindex;
				
				// on fais le lien
				$sqlAjout="insert into groupe_artiste (id_artiste,id_concert) values (:artiste,:concert)";
				$infoAjout=$connexion->prepare($sqlAjout);
				$infoAjout->bindParam(':artiste',$artiste);
				$infoAjout->bindParam(':concert',$concert);
				$infoAjout->execute();
				
				echo("<script>alert(\"Lien ajouter !\")</script>");
				redirect("ajouter.php",$time=0);
				// on redirige a la fin
				//header("Location:ajouter.php");
			}
		}
		
			
	}
}




// CREER UN CONCERT OU UN ARTISTE 
// element indique si on veut creer un concert ou un artiste
if (isset($_GET['element']) && !empty($_GET['element'])){
	
	// on recupere la donnée dans la variable
	$element=$_GET['element'];
	
	
	// TRAITEMENT POUR LA CREATION DE CONCERT 
	
	// POST 
	if (isset($_POST["send"]) && $element=="concert"){
		
		
		if ((!empty($_POST["importance"]) || $_POST["importance"] ==0) && !empty($_POST["dateconcert"])&& !empty($_POST["lieu"]) && (!empty($_POST["prix"]) || $_POST["prix"] ==0) && !empty($_POST["genreconcert"]) && !empty($_POST["nbplaces"]) && !empty($_POST["nbplaceslibres"]) && !empty($_POST["description"])&& (!empty($_POST["nblienconcert"]) || $_POST["nblienconcert"]==0) && (!empty($_POST["lat"]) || $_POST["lat"] ==0 ) && (!empty($_POST["long"]) || $_POST["long"] ==0 ) && (!empty($_POST["iframe"]))){
		
			
			// lors de la creation d un concert on indique le nombre de lien que l on veut faire avec un artiste
			$nblienconcert=htmlspecialchars($_POST["nblienconcert"]);		
			
			// recuperation des elements 
			$importance=htmlspecialchars($_POST["importance"]);
			$dateconcert=htmlspecialchars($_POST["dateconcert"]);
			$lieu=htmlspecialchars($_POST["lieu"]);
			$genreconcert=$_POST["genreconcert"];
			$prix=htmlspecialchars($_POST["prix"]);
			$nbplaces=htmlspecialchars($_POST["nbplaces"]);
			$nbplaceslibres=htmlspecialchars($_POST["nbplaceslibres"]);
			$description=htmlspecialchars($_POST["description"]);
			$lat=$_POST["lat"];
			$long=$_POST["long"];	
			$iframe=htmlspecialchars($_POST["iframe"]);
			
			// condition de securite pour le nombre de place
			if ($nbplaceslibres>$nbplaces){
				echo("<script>alert(\"Le nombres de place disponible ne peut pas être > aux nombres de places totales\")</script>");		
			}
			else {
						
				// met dans une session les valeurs pour ne pas avoir a les réecrires
				$_SESSION["lieuconcert"]=$lieu;
				$_SESSION["dateconcert"]=$dateconcert;
				
				// Test pour savoir si il y a deja un concert a ce lieu et a cette date
				$sqlTest="select * from concertIndex ci where ci.lieu=:lieuconcert and ci.dateconcert=:dateconcert";
				$infoTest=$connexion->prepare($sqlTest);
				$infoTest->execute(array("lieuconcert"=>$lieu,"dateconcert"=>$dateconcert));
				$resTest=$infoTest->fetchAll(PDO::FETCH_OBJ);
						

				// si oui : on arrete + alert
				if (count($resTest)>0){
					echo("<script>alert(\"Le concert existe déjà\")</script>");
				}
				// sinon on continue	
				else {
				
					// insertion du concert dans la table concertIndex
					$sql0="insert into concertIndex(importance,genreconcert,lieu,dateconcert,description,prix,nbplaces,nbplaceslibres,lat,long,lieniframe) values (:importance,:genreconcert,:lieu,:dateconcert,:description,:prix,:nbplaces,:nbplaceslibres,:lat,:long,:iframe)";
					$info0=$connexion->prepare($sql0);
					$info0->execute(array("importance"=>$importance,
					"genreconcert"=>$genreconcert,"lieu"=>$lieu,"dateconcert"=>$dateconcert,"description"=>$description,
					"prix"=>$prix,"nbplaces"=>$nbplaces,"nbplaceslibres"=>$nbplaceslibres,"lat"=>$lat,"long"=>$long,"iframe"=>$iframe));
						
					
					// si il n y a pas de lien on se redirige vers la page d ajout
					if ($nblienconcert==0 && !isset($_GET['liaisonartiste'])){
					
						echo("<script>alert(\"Concert créé !\")</script>");
						redirect("ajouter.php",$time=0);
					//	header("Location:ajouter.php");
					}	
					// si il y a une liaison a faire apres la creation 
					else if (isset($_GET['liaisonartiste'])){
						
						// recupere valeurs
						$nom_artiste=$_SESSION['nom_artiste'];
						$lieu=$_SESSION["lieuconcert"];
						$dateconcert=$_SESSION["dateconcert"];
						
						// selectionne id di concert
						$sqlConcert="select id_concertindex from concertIndex ci where ci.lieu=:lieuconcert and ci.dateconcert=:dateconcert";
						$infoConcert=$connexion->prepare($sqlConcert);
						$infoConcert->execute(array("lieuconcert"=>$lieu,"dateconcert"=>$dateconcert));
						$resConcert=$infoConcert->fetchAll(PDO::FETCH_OBJ);
						$idConcert=$resConcert[0]->id_concertindex;
						
						// selectionne id artiste
						$sqlAr="select id_artiste from artiste where nom_artiste=:nom";
						$infoAr=$connexion->prepare($sqlAr);
						$infoAr->execute(array("nom"=>$nom_artiste));
						$resAr=$infoAr->fetchAll(PDO::FETCH_OBJ);
						$idAr=$resAr[0]->id_artiste;
				
						// on fais le lien
						$reqinsert="insert into groupe_artiste (id_artiste,id_concert) values (:idAr,:idconcert)";
						$infoinsert=$connexion->prepare($reqinsert);
						$infoinsert->execute(array("idAr"=>$idAr,"idconcert"=>$idConcert));
						
						
						echo("<script>alert(\"Ajout pris en compte !\")</script>");
						redirect("ajouter.php",$time=0);
						//header("Location:ajouter.php");
						
					}
					// sinon vers la page de creation d artiste
					else {
						header("Location:ajouter.php?nblienconcert=$nblienconcert");
					}					
				}						
			}
	
		}
		else {
			echo("<script>alert(\"remplir tous les champs\")</script>");
		}
			
	}
	
	
	// TRAITEMENT POUR LA CREATION D ARTISTE
	else if (isset($_POST["send"]) && $element=="artiste"){
		
		if (!empty($_POST["nom_artiste"]) && !empty($_POST["descriptionartiste"])&& (!empty($_POST["nblienartiste"]) || $_POST["nblienartiste"]==0) && $_FILES["fichier"]["error"]==0){
			
		
			// recupere les elements du formulaire
			$nblienartiste=htmlspecialchars($_POST["nblienartiste"]);
			$nom_artiste=htmlspecialchars($_POST["nom_artiste"]);
			$descriptionartiste=htmlspecialchars($_POST["descriptionartiste"]);
			$img='images/'.$_FILES['fichier']['name'];
			
			// Test pour savoir si l artiste existe deja  
			$sqlTest="select * from artiste ar where ar.nom_artiste=:nom_artiste";
			$infoTest=$connexion->prepare($sqlTest);
			$infoTest->execute(array("nom_artiste"=>$nom_artiste));
			$resTest=$infoTest->fetchAll(PDO::FETCH_OBJ);
			
			// si oui alert
			if (count($resTest)>0){
				echo("<script>alert(\"L'artiste existe déjà\")</script>");
			} 
			// sinon on continue
			else {
				// si le nom et le prenom ne sont pas vide : on recupere les valeurs 
				if (!empty($_POST['nom']) && !empty($_POST['prenom'])){
					$nom=htmlspecialchars($_POST["nom"]);
					$prenom=htmlspecialchars($_POST["prenom"]);
					
					// on insert l artiste dans la base
					$sql="insert into artiste (nom,prenom,nom_artiste,img,descriptionartiste) values (:nom,:prenom,:nom_artiste,:img,:descriptionartiste)";
					$info=$connexion->prepare($sql);
					$info->execute(array("nom"=>$nom,"prenom"=>$prenom,
					"nom_artiste"=>$nom_artiste,"img"=>$img,"descriptionartiste"=>$descriptionartiste));
					
					
				}
				// si un des 2 est vide : alert + on ne creer pas l artiste
				else if ((!empty($_POST['nom']) && empty($_POST['prenom'])) || (empty($_POST['nom']) && !empty($_POST['prenom']))){
					echo("<script>alert(\"Si vous rempissez le nom veuillez remplir le prenom et inversement\")</script>");
				}
				// si les 2 sont vide on ajoute artiste sans nom/prenom
				else {
					// on insert
					$sql="insert into artiste (nom_artiste,img,descriptionartiste) values (:nom_artiste,:img,:descriptionartiste)";
					$info=$connexion->prepare($sql);
					$info->execute(array("nom_artiste"=>$nom_artiste,"img"=>$img,"descriptionartiste"=>$descriptionartiste));
					
					
				}
				// on move upload l image a la fin de la creation
				move_uploaded_file($_FILES["fichier"]["tmp_name"], 'images/'.$_FILES["fichier"]["name"]);
				
				// on met dans une session le nom d artiste
				$_SESSION['nom_artiste']=$nom_artiste;
				
				// si il n y a pas de lien avec un concert on se redirige vers la page d ajout
				if ($nblienartiste==0 && !isset($_GET['liaisonconcert'])){
					echo("<script>alert(\"Artiste créé !\")</script>");
					redirect("ajouter.php",$time=0);
				//	header("Location:ajouter.php");
				}
				// si il y a des liens a faire apres creation d un artiste
				else if (isset($_GET['liaisonconcert'])){
						
						// on recupere valeurs
						$nom_artiste=$_SESSION['nom_artiste'];
						$lieu=$_SESSION["lieuconcert"];
						$dateconcert=$_SESSION["dateconcert"];
						
						// on selectionne l id du concert avec le lieu et la date
						$sqlConcert="select id_concertindex from concertIndex ci where ci.lieu=:lieuconcert and ci.dateconcert=:dateconcert";
						$infoConcert=$connexion->prepare($sqlConcert);
						$infoConcert->execute(array("lieuconcert"=>$lieu,"dateconcert"=>$dateconcert));
						$resConcert=$infoConcert->fetchAll(PDO::FETCH_OBJ);
						$idConcert=$resConcert[0]->id_concertindex;
						
						// on selectionne l id de l artiste
						$sqlAr="select id_artiste from artiste where nom_artiste=:nom";
						$infoAr=$connexion->prepare($sqlAr);
						$infoAr->execute(array("nom"=>$nom_artiste));
						$resAr=$infoAr->fetchAll(PDO::FETCH_OBJ);
						$idAr=$resAr[0]->id_artiste;
				
						// on fais le lien
						$reqinsert="insert into groupe_artiste (id_artiste,id_concert) values (:idAr,:idconcert)";
						$infoinsert=$connexion->prepare($reqinsert);
						$infoinsert->execute(array("idAr"=>$idAr,"idconcert"=>$idConcert));
						
						echo("<script>alert(\"Ajout pris en compte !\")</script>");
						redirect("ajouter.php",$time=0);
						// on redirige vers la page d ajout
						//header("Location:ajouter.php");
						
				}
				// sinon vers la page de liaison 
				else {
					header("Location:ajouter.php?nblienartiste=$nblienartiste");
				}		
			}
		}
		else {
			echo("<script>alert(\"remplir tous les champs\")</script>");
		}
	}
	
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
		<title>L'Acclameur</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
	</head>
	<body class="no-sidebar is-preload">
		<div id="page-wrapper">

			<!-- Header -->
			<?php require("header.php");?>


<!-- Banner -->
				<section id="banner3">
				
					<div class="inner">

						<!-- Affichage en foncion de l ajout a faire -->
						<header>
							<?php if($element=="concert"):?>
								<span class="icon fa-volume-up" style="font-size:4em;"></span>
								<?php else : ?>
								<?php if($element=="artiste"):?>
								<span class="icon fa-user" style="font-size:4em;"></span>
								<?php else : ?>
								<span class="icon fa-database" style="font-size:4em;"></span>
								<?php endif;?>
							<?php endif;?>
							<h2>Créer dans la base de donnée</h2>
						</header>
						

					</div>

				</section>



			<!-- Main -->
			
			<article id="main">
				<!-- proposition des ajouts possibles si rien n a ete choisit -->
				<?php if (!isset($_GET['element']) && !isset($_GET['nblienconcert']) && !isset($_GET['nblienartiste']) && !isset($_GET['ajouterConcertArtiste'])):?>
					<header class="special container">
						<span class="icon fa-pencil"></span>
						<h2>Concerts et Artistes</h2>
					</header>
	
				<section class="wrapper style3 container special">
				
						<ul class="buttons">
							<li><a href="ajouter.php?element=concert" class="button spatial">Créer concert</a></li>
							<li><a href="ajouter.php?element=artiste" class="button spatial">Créer artiste</a></li>
						</ul>
						<a href="ajouter.php?ajouterConcertArtiste=1" class="button spatial">Ajouter un artiste à un concert</a>
					

				</section>
				
				<!-- CREATION CONCERT -->
				<?php else : ?>
				<?php if ($element=='concert'):?>
					
					<header class="special container">
						<span class="icon fa-volume-up"></span>
						<h2>Créer un concert</h2>
						<p>Le concert doit être en lien à au moins un artiste</p>
					</header>
					<section class="wrapper style3 container special">
						<h2>Formulaire à remplir</h2>
						<!-- creation d un concert apres la creation d artiste -->
						<?php if (isset($_GET['liaisonartiste'])):?>
							<form method="post" action="ajouter.php?element=concert&liaisonartiste=0" style="margin-top:20px;" enctype="multipart/form-data">
						<!-- creation direct / sans etre passer par la creation d artiste -->
						<?php else :?>
							<form method="post" action="ajouter.php?element=concert" style="margin-top:20px;" enctype="multipart/form-data">
						<?php endif;?>
						
							<div class="row gtr-50">

								
								<select name="importance" class="col-6">
									<option value="0">0</option>
									<option value="1">1</option>
								</select>
								<div class="col-6 col-12-mobile">				
									<input type="date" name="dateconcert" placeholder="Date concert">
								</div>
								<div class="col-6 col-12-mobile">				
									<input type="text" name="lieu"  placeholder="Lieu">
								</div>
								<div class="col-6 col-12-mobile">				
									<input type="text" name="prix" placeholder="Prix">
								</div>
								<select name="genreconcert">
									<!-- affiche tous les genres de musique -->
									<?php foreach ($resConcertGenre as $k=> $v):?>
									<option value="<?=$v->genreconcert?>"><?=$v->genreconcert?></option>
									<?php endforeach;?>
								</select>
								<div class="col-6 col-12-mobile">				
									<input type="text" name="nbplaceslibres" placeholder="Nombres de places libres">
								</div>
								<div class="col-6 col-12-mobile">				
									<input type="text" name="nbplaces" placeholder="Nombre de places totales">
								</div>		
								<div class="col-12">				
									<textarea name="description" placeholder="Description du concert" rows="5"></textarea>
								</div>		
								<!-- si on est passer par la creation d artiste : on ne propose plus de lien avec un artiste -->
								<?php if(isset($_GET['liaisonartiste'])):?>
									<input type="hidden" name="nblienconcert" value="0">
								<?php else :?>		
								<div class="col-12">			
									<input type="number" name="nblienconcert"  placeholder="Nombre d'artiste à ajouter à ce concert">
								</div>			
								<?php endif;?>	
								
								<div class="col-12">				
									<input type="text" name="iframe" placeholder="Lien Iframe">
								</div>	
								<div class="col-6 col-12-mobile">				
									<input type="number" name="lat" placeholder="Latitude">
								</div>
								<div class="col-6 col-12-mobile">				
									<input type="number" name="long" placeholder="Longitude">
								</div>	
								<div class="col-12">				
								<input type="submit" name="send" value="Ajouter">
								</div>
				
						</form>
						<p>*Note : la création concert/artiste doit se faire indépendement</p>
					
					<?php else :?>
					
					<!-- CREATION ARTISTE -->
					<?php if ($element=='artiste'):?>
						<header class="special container">
							<span class="icon fa-user"></span>
							<h2>Créer un artiste</h2>
							<p>L'artiste doit être ajouté à au moins concert</p>
						</header>
						<section class="wrapper style3 container special">
						<h2>Formulaire à remplir</h2>
						<!-- si on est passer par la creation de concert avant -->
						<?php if (isset($_GET['liaisonconcert'])):?>
							<form method="post" action="ajouter.php?element=artiste&liaisonconcert=0" style="margin-top:20px;"enctype="multipart/form-data">
						
						<!-- si on est pas passer par la creation de concert avant -->
						<?php else :?>
							<form method="post" action="ajouter.php?element=artiste" style="margin-top:20px;"enctype="multipart/form-data">
						<?php endif;?>
						
						<div class="content">
							<div class="row gtr-50">
								<div class="col-6 col-12-mobile">				
									<input type="text" name="prenom"  placeholder="Prénom">
								</div>
								<div class="col-6 col-12-mobile">				
									<input type="text" name="nom"  placeholder="Nom">
								</div>
								<div class="col-6 col-12-mobile">				
									<input type="text" name="nom_artiste"  placeholder="Nom d'artiste">
								</div>
								<div class="col-6 col-12-mobile">				
									<input type="file" name="fichier">

								</div>
								<div class="col-12">				
									<textarea name="descriptionartiste" placeholder="Description" rows="5"></textarea>
								</div>
								<!-- si on est passer par la creation de concert avant : on affiche pas le nombre de lien possible a faire avec un concert -->
								<?php if(isset($_GET['liaisonconcert'])):?>
									<input type="hidden" name="nblienartiste" value="0">
								<?php else :?>		
								<div class="col-12">			
									<input type="number" name="nblienartiste"  placeholder="Nombre de concert à ajouter à ce concert">
								</div>			
								<?php endif;?>	
								<div class="col-12">			
								<input type="submit" name="send" value="Ajouter">
								</div>
								
							</div>
							</div>
						</form>
						<p>*Note : la création artiste/concert doit se faire indépendement</p>

					<!-- nombre de lien a faire avec un artiste -->
					<?php else :?>
					<?php if (isset($nblienconcert) && !isset($ajouterConcertArtiste)):?>
					<header class="special container">
							<span class="icon fa-database"></span>
							<h2>Un concert | <?=$nblienconcert?> artistes</h2>
						</header>
						<section class="wrapper style3 container special">
						<!-- on affiche le concert -->
						<h2>A <?=$_SESSION['lieuconcert']?> - Le <?=strftime('%d %B %Y',strtotime($_SESSION['dateconcert']))?></h2>
						<!-- liaison avec artiste existant -->
						<a href="ajouter.php?ajouterConcertArtiste=1&nblienconcert=<?=$nblienconcert?>" class="button">Lier à un artiste existant</a>
						<!-- si on veux lie a un artiste on peut le cree -->
						<?php if ($nblienconcert ==1):?>
						<section class="wrapper style4 special container medium">
						<p>OU</p>
						<a href="ajouter.php?element=artiste&liaisonconcert=0" class="button primary">Créer un artiste</a>
						</section>
						<?php endif;?>
						

					<!-- nombre de lien a faire avec un artiste -->
					<?php else :?>
					<?php if (isset($nblienartiste) && !isset($ajouterConcertArtiste)):?>
					<header class="special container">
							<span class="icon fa-database"></span>
							<h2>Un artiste | <?=$nblienartiste?> concerts</h2>
						</header>
						<section class="wrapper style3 container special">
						<!-- on affiche l artiste -->
						<h2><?=$nom_artiste?></h2>
						
						<!-- lien avec concert existant -->
						<a href="ajouter.php?ajouterConcertArtiste=1&nblienartiste=<?=$nblienartiste?>" class="button">Lier à un concert existant</a>
						<!-- si on lit a un concert on peut le creer -->
						<?php if ($nblienartiste ==1):?>
						<section class="wrapper style4 special container medium">
						<p>OU</p>
						<a href="ajouter.php?element=concert&liaisonartiste=0" class="button primary">Créer concert</a>
						</section>
						<?php endif;?>							
						
					<!-- lien concert / artiste -->
					<?php else :?>
					<?php if (isset($ajouterConcertArtiste)):?>
						<header class="special container">
							<span class="icon fa-database"></span>
							<h2>Ajouter un artiste à un concert</h2>
							
						</header>
						<section class="wrapper style3 container special">
						<!--<?php if (!isset($_GET['nblienartiste']) && !isset($_GET['nblienconcert'])):?>
						
						<form method="post" action="ajouter.php?ajouterConcertArtiste=1">
						<?php else :?>-->
						
						<!-- si on veux lie 1 artiste avec n concert -->
						<?php if (isset($_GET['nblienartiste'])):?>
						<form method="post" action="ajouter.php?ajouterConcertArtiste=1&nblienartiste=<?=$nblienartiste?>"enctype="multipart/form-data">
						<?php else :?>
						<!-- si on veux lie 1 concert avec n artiste -->
						<?php if (isset($_GET['nblienconcert'])):?>
						<form method="post" action="ajouter.php?ajouterConcertArtiste=1&nblienconcert=<?=$nblienconcert?>"enctype="multipart/form-data">
						<?php endif;?>
						<?php endif;?>
						<!-- si on veut lier 1 artiste avec n concert : -->
							<?php if(isset($nblienartiste)):?>
							<select name="artiste">
								<!-- on affiche l artiste -->
								<option value="<?=$_SESSION['nom_artiste']?>"><?=$_SESSION['nom_artiste']?></option>
								</select>
							<?php else :?>
								<!-- sinon si on veut lie un concert a n artiste : on affiche n artiste : -->
							<?php if(isset($nblienconcert)):?>
								<?php for ($i=0;$i<$nblienconcert;$i++):?>
								<select name="artiste[]">
								<?php foreach ($resArtiste as $k=>$v):?>
								<option value="<?=$v->id_artiste?>"><?=$v->nom_artiste?></option>
								<?php endforeach;?>
								</select>
								<?php endfor;?>
						<!--	<?php else :?>
							
							
							<select name="artiste">
								<?php foreach ($resArtiste as $k=>$v):?>
								<option value="<?=$v->id_artiste?>"><?=$v->nom_artiste?></option>
								<?php endforeach;?>
								</select>-->
							<?php endif;?>
							
							<?php endif;?>
							<!-- si on lie 1 artiste a n concert -->
						<?php if(isset($_GET['nblienartiste'])):?>
							<!-- on affiche n concert -->
							<?php for($i=0;$i<$nblienartiste;$i++):?>
								<p>Ajout <?=$i+1?> : </p>
								<select name="lieuconcert[]">
									<?php foreach ($resConcert as $k=>$v):?>
									<option value="<?=$v->lieu?>"><?=$v->lieu?></option>
									<?php endforeach;?>
								</select>
								<select name="dateconcert[]">
									<?php foreach ($resConcertDate as $k=>$v):?>
									<option value="<?=$v->dateconcert?>"><?=strftime('%d %B %Y',strtotime($v->dateconcert))?></option>
									<?php endforeach;?>
								</select>
							<?php endfor;?>
						<?php else :?>
						<!-- sinon si on lit 1 concert a n artiste -->
						<?php if(isset($_GET['nblienconcert'])):?>
							<!-- on affiche un concert -->
								<select name="lieuconcert">
									<option value="<?=$_SESSION['lieuconcert']?>"><?=$_SESSION['lieuconcert']?></option>
								</select>
								<select name="dateconcert">
									<option value="<?=$_SESSION['dateconcert']?>"><?=$_SESSION['dateconcert']?></option>
								</select>
				
						<?php endif;?>
						<?php endif;?>
						<div class="col-12">
							<input type="submit" name="send" value="Ajouter">
						</div>
					</form>
					<?php endif;?>
					<?php endif;?>
					<?php endif;?>
					<?php endif;?>
					<?php endif;?>
					<?php endif;?>
					
					<!-- liaison classique : 1 concert / 1 artiste -->
					<?php if (!isset($_GET['nblienartiste']) && !isset($_GET['nblienconcert']) && !isset($_GET['liaisonconcert']) && !isset($_GET['liaisonartiste'] )&& !isset($_GET['element'])):?>
							<form method="post" action="ajouter.php?ajouterConcertArtiste=1"enctype="multipart/form-data">
							
							<!-- on affiche 1 artiste -->
							<select name="artiste">
								<?php foreach ($resArtiste as $k=>$v):?>
								<option value="<?=$v->id_artiste?>"><?=$v->nom_artiste?></option>
								<?php endforeach;?>
								</select>
								
							<!-- on affiche 1 concert -->
							<select name="lieuconcert">
									<?php foreach ($resConcert as $k=>$v):?>
									<option value="<?=$v->lieu?>"><?=$v->lieu?></option>
									<?php endforeach;?>
								</select>
								<select name="dateconcert">
									<?php foreach ($resConcertDate as $k=>$v):?>
									<option value="<?=$v->dateconcert?>"><?=strftime('%d %B %Y',strtotime($v->dateconcert))?></option>

									<?php endforeach;?>
								</select>
								<div class="col-12">
							<input type="submit" name="send" value="Ajouter">
						</div>
						</form>
					<?php endif;?>
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
