<?php
session_start();

/* PAGE DE MODIFICATIONS DE CONCERTS ( et artiste) */

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

// selectionne tous les genres de concerts par ordre alphabetique 
$reqConcertGenre="
select distinct genreconcert
from concertGenre 
ORDER BY concertGenre.genreconcert";
$infoConcertGenre=$connexion->query($reqConcertGenre);
$resConcertGenre=$infoConcertGenre->fetchAll(PDO::FETCH_OBJ);


// on verifie qu on a bien recuperer l id du concert en parametre
if (isset($_GET['idconcert']) && !empty($_GET['idconcert'])){
	// et on recupere sa valeur
	$idconcert=$_GET['idconcert'];
	
	// on recupere tous les artistes du concert
	$sql="select * from artiste ar
		  join groupe_artiste ga on ar.id_artiste=ga.id_artiste
		  join concertIndex ci on ci.id_concertIndex=ga.id_concert
		  where ci.id_concertIndex=:idconcert";
	$info=$connexion->prepare($sql);
	$info->bindParam(':idconcert',$idconcert);
	$info->execute();
	$res0=$info->fetchAll(PDO::FETCH_OBJ);
	
	// on recupere toutes les données du concert pour afficher dans le formulaire
	$sql="select * from concertIndex ci
		  where ci.id_concertIndex=:idconcert";
	$info=$connexion->prepare($sql);
	$info->bindParam(':idconcert',$idconcert);
	$info->execute();
	$res=$info->fetch(PDO::FETCH_OBJ);
	
	
	// POST 
	// si on modifie le formulaire on met a jour la base de données : elle sera mise a jour meme sans modification
	if (isset($_POST["send"])){
		if (!empty($_POST["lieu"]) && (!empty($_POST["prix"]) || $_POST["prix"]==0)  && !empty($_POST["description"]) && !empty($_POST["nbPlaces"]) && !empty($_POST["nbplaceslibres"]) && (!empty($_POST['importance']) || $_POST['importance']==0)){

			// on recupere les informations du formulaire
			$dateconcert=$_POST["dateconcert"];
			$lieu=$_POST["lieu"];
			$prix=$_POST["prix"];
			$genreconcert=$_POST["genreconcert"];
			$description=$_POST["description"];
			$nbplaces=$_POST["nbPlaces"];
			$nbplaceslibres=$_POST["nbplaceslibres"];
			$importance=$_POST["importance"];
			
			// on verifie que les modifications n'entraine pas de double concert
			$sqlverif="select * from concertIndex where lieu=:lieu and dateconcert=:date";
			$infoverif=$connexion->prepare($sqlverif);
			$infoverif->execute(array('lieu'=>$lieu,'date'=>$dateconcert));
			$resverif=$infoverif->fetchAll(PDO::FETCH_OBJ);
			
			if(count($resverif)>0 && ($res->lieu != $lieu) && ($res->dateconcert != $dateconcert)){
				echo("<script>alert(\"Concert déjà existant, revoyer vos date et lieu\")</script>");
			}
			
			else {
				// si tout est bon on met a jour la base de données
				$sql3="update concertIndex set importance=:importance, genreconcert=:genreconcert, lieu=:lieu, dateconcert=:dateconcert, description=:description, prix=:prix, nbplaces=:nbplaces, nbplaceslibres=:nbplaceslibres where id_concertIndex=:idconcert;";
				$info3=$connexion->prepare($sql3);
				$info3->execute(array('importance'=>$importance,'genreconcert'=>$genreconcert,'lieu'=>$lieu,'dateconcert'=>$dateconcert,
				'description'=>$description,'prix'=>$prix,'nbplaces'=>$nbplaces,'nbplaceslibres'=>$nbplaceslibres,'idconcert'=>$idconcert));
			
				// on se redirige vers la page de gestion du concert
				echo("<script>alert(\"Modification prise en compte !\")</script>");
				redirect("gestion.php",$time=0);
				
			}
		}
		// si il y a des champs vide qui ne devrait pas l etre : alert
		else {
			echo("<script>alert(\"Remplir tous les champs\")</script>");
		}
		
	}
	
	


	
}

// si on veut supprimer un artiste d un concert : on se redirige ici avec un parametre supprimer et l id de l artiste et du concert		
if (isset($_GET['supprimer']) && isset($_GET['idartiste'])  && isset($_GET['idconcert']) && !empty($_GET['supprimer'])  && !empty($_GET['idconcert'])&& !empty($_GET['idartiste'])){

		// on recupere l id de l artiste
		$idartiste=$_GET['idartiste'];
		
		// on supprime le lien entre le concert et l artiste
		$requete5="delete from groupe_artiste where  id_artiste=:idartiste and id_concert=:idconcert;";
		$info5=$connexion->prepare($requete5);
		$info5->bindParam(":idartiste",$idartiste);
		$info5->bindParam(":idconcert",$idconcert);
		$info5->execute();

		// on se redirige vers la meme page
		header("Location:modif.php?idconcert=$idconcert");
		
}

// val=1 -> si on veut afficher les champs et 0 si on veut la valeur
$val=1;
if(!isset($_GET["champs"]) || ($_GET["champs"]==="valeur")){
	$val=0;
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

			<!-- Main -->
			<article id="main">

					<!-- ICONE Modif -->
					<header class="special container">
						<span class="icon fa-edit"></span>
						<h2>modifier</h2>
						<h2>Un concerts</h2>
					</header>
					
					<!-- section -->
					<section class="wrapper style3 container special">
					
						<!-- ICONE USERS -->
		  				<header class="special container">
							<span class="icon fa-group"></span>
							<h2>Les artistes présents</h2>
						</header>
						
						<!-- si il n y a pas d artiste au concert -->
		  				<?php if (count($res0)==0):?>
		  					<p>Pas d'artiste présent</p>
		  					
		  				<!-- sinon -->
		  				<?php else :?>
		  					<!-- on affiche tous les artistes presents avec possibilité de modification et de suppression -->
		  					<?php for ($i=0;$i<count($res0);$i++):?>
								<p class="cadre"><?=$res0[$i]->nom_artiste?></p> 
								<a class="button" href="modifArtiste.php?idartiste=<?=$res0[$i]->id_artiste?>&idconcert=<?=$idconcert?>">Modifier</a>
								<a class="button" href="modif.php?supprimer=1&idartiste=<?=$res0[$i]->id_artiste?>&idconcert=<?=$idconcert?>">Supprimer</a>
								<hr>
							<?php endfor;?>
						<?php endif;?>
						
						<!-- ICONE Concert -->
						<header class="special container"  style="margin-top:100px;">
							<span class="icon fa-volume-up"></span>
							<h2>Le concert</h2>
						</header>

		  				<a href="modif.php?champs=placeholder& idconcert=<?=$idconcert?>" class="button">Champs</a>
		  				<a href="modif.php?champs=valeur& idconcert=<?=$idconcert?>" class="button">Valeur</a>
		  					
		  				<!-- si on veut afficher la valeur -->
		  				<?php if($val===0) :?>
		  					<!-- redirige vers cette pas avec l id du concert -->
							<form method="post" action="modif.php?idconcert=<?=$idconcert?>" style="margin-top:20px;" enctype="multipart/form-data">
								<div class="row gtr-50">
									<select name="importance" class="col-6">
										<option value="<?=$res->importance?>"><?=$res->importance?></option>
										<option value="0">0</option>
										<option value="1">1</option>
									</select>
									<div class="col-6 col-12-mobile">				
										<input type="date" name="dateconcert" value="<?=$res->dateconcert?>" placeholder="Date concert">
									</div>									<div class="col-6 col-12-mobile">				
										<input type="text" name="lieu" value="<?=$res->lieu?>" placeholder="Lieu">
									</div>
									<div class="col-6 col-12-mobile">				
										<input type="text" name="prix" value="<?=$res->prix?>" placeholder="Prix">
									</div>
									<select name="genreconcert">
										<!-- affiche le genre actuel -->
										<option value="<?=$res->genreconcert?>"><?=$res->genreconcert?></option>
										<!-- propose tous les genres -->
										<?php foreach ($resConcertGenre as $k=> $v):?>	
										<option value="<?=$v->genreconcert?>"><?=$v->genreconcert?></option>									<?php endforeach;?>
									</select>
									<div class="col-6 col-12-mobile">				
										<input type="text" name="nbplaceslibres" value="<?=$res->nbplaceslibres?>" placeholder="Nombres de places libres">
									</div>
									<div class="col-6 col-12-mobile">				
										<input type="text" name="nbPlaces" value="<?=$res->nbplaces?>" placeholder="Nombre de places totales">
									</div>	
									<div class="col-12">				
										<input type="text" name="description" value="<?=$res->description?>" placeholder="Description du concert">
									</div>
																	
									<div class="col-12">				
									<input type="submit" name="send" value="Modifier">
									</div>
										
								</div>
							</form>
							
						<!-- sinon -->
						<?php else :?>
							<form method="post" action="#" style="margin-top:20px;" enctype="multipart/form-data">
								<div class="row gtr-50">

									<div class="col-6 col-12-mobile">				
										<input type="text" name="importance" placeholder="importance">
									</div>
									<div class="col-6 col-12-mobile">				
										<input type="text" name="dateconcert" placeholder="Date concert">
									</div>
									<div class="col-6 col-12-mobile">				
										<input type="text" name="lieu"  placeholder="Lieu">
									</div>
									<div class="col-12">				
										<input type="text" name="prix" placeholder="Prix">
									</div>
									<div class="col-6 col-12-mobile">				
										<input type="text" name="genreconcert" placeholder="Genre">
									</div>
									<div class="col-12">				
										<input type="text" name="description"  placeholder="Description du concert">
									</div>
									<div class="col-6 col-12-mobile">				
										<input type="text" name="nbplaceslibres" placeholder="Nombres de places libres">
									</div>
									<div class="col-6 col-12-mobile">				
										<input type="text" name="nbPlaces" placeholder="Nombre de places totales">
									</div>								
									<div class="col-12">				
									<input type="submit" name="send" value="Modifier">
									</div>
									
								</div>
							</form>
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
