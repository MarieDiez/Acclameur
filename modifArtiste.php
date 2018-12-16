<?php
session_start();


/* PAGE DE MODIFICATION DES ARTISTES */


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

// on peut acceder a cette page par 2 moyens 
// 1 -> par la page de gestion des artistes 
// 2 -> par la page de modification des concerts : si c'est le cas lors de la modification de l'artiste on aura besoin de 
// revenir sur la page du concert : on envoie l id du concert en parametre pour la modification de l'artistes

// si on viens d une page de concert
if (isset($_GET['idconcert']) && !empty($_GET['idconcert'])){
	// on recupere l'id
	$idconcert=$_GET['idconcert'];
}

// si on a bien passer par url l'id de l'artiste qu'on veut modifier : on recupere l'id
if (isset($_GET['idartiste']) && !empty($_GET['idartiste'])){
	$idartiste=$_GET['idartiste'];
	
	// on recupere toutes les informations de l'artiste pour l'affichage du formulaire
	$sql="select * from artiste ar
		  where id_artiste=:idartiste";
	$info=$connexion->prepare($sql);
	$info->bindParam(':idartiste',$idartiste);
	$info->execute();
	$res0=$info->fetch(PDO::FETCH_OBJ);
	

	
	// POST 
	// si on a modifier le formulaire il faut mettre a jour la base de donnée : elle sera mise a jour meme sans modification
	if (isset($_POST["send"]) && isset($_POST["nom_artiste"])  && isset($_POST["descriptionartiste"])){
		if(!empty($_POST["nom_artiste"]) && !empty($_POST["descriptionartiste"])){
	

			// si le nom et le prenom ne sont pas vide : on recupere les valeurs POST
			if (!empty($_POST['nom']) && !empty($_POST['prenom'])){
				$nom=htmlspecialchars($_POST["nom"]);
				$prenom=htmlspecialchars($_POST["prenom"]);				
			}
			
			// si l un des deux est vide : on demande de remplir les 2
			else if ((!empty($_POST['nom']) && empty($_POST['prenom'])) || (empty($_POST['nom']) && !empty($_POST['prenom']))){
				echo("<script>alert(\"Si vous rempissez le nom veuillez remplir le prenom et inversement\")</script>");		
				$nom=$res0->nom;
				$prenom=$res0->prenom;	
			}
			// si les 2 sont vide : on pose les variables nom et prenom a vide
			else if (empty($_POST['nom']) && empty($_POST['prenom'])){
				$nom="";
				$prenom="";			
			}
				
			// on recupere le nom d'artiste
			$nom_artiste=$_POST["nom_artiste"];
			// on recupere la description de l'artiste
			$descriptionartiste=$_POST["descriptionartiste"];
			// on recupere l'id de l'artiste
			$idartiste=$res0->id_artiste;

			// si l'image n'est pas modifier : on garde l'ancienne sinon on prend la nouvelle
			$nomimg=str_replace(" ","",$nom_artiste);
			if (!empty($_FILES['fichier']) && $_FILES['fichier']['error']==0){
				$img='images/'.$nomimg.'.jpg';
				move_uploaded_file($_FILES["fichier"]["tmp_name"], 'images/'.$nomimg.'.jpg');
				copy('images/'.$nomimg.'.jpg', 'images/banner'.$nomimg.'.jpg');
			}
			else {
				$img=$res0->img;
			}
		
			
			// on met a jour la base de donnée avec les informations recuperées
			$sql4="update artiste set nom=:nom, prenom=:prenom, nom_artiste=:nom_artiste, descriptionartiste=:descriptionartiste, img=:img where id_artiste=:idartiste;";
			$info4=$connexion->prepare($sql4);
			$info4->execute(array('nom'=>$nom,'prenom'=>$prenom,'nom_artiste'=>$nom_artiste,
			'descriptionartiste'=>$descriptionartiste,'img'=>$img,'idartiste'=>$idartiste));			
			
			
			// si on vient d une page avec un concert : on s'y redirige 
			if (isset($idconcert)){
				header("Location:modif.php?idconcert=$idconcert");
			}
			// sinon on redirige vers la page de gestion des artistes
			else {
			
				echo("<script>alert(\"Modification prise en compte !\")</script>");
				redirect("gestionArtiste.php",$time=0);
			
			}		
		}
		else {
			echo("<script>alert(\"Remplir tous les champs\")</script>");		
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

// affichage du formulaire sous forme de valeurs ou de champs vide : 
// si on clique sur champs alors $val = 1 sinon $val = 0 -> on affiche les valeurs
$val=1;
if(!isset($_GET["champs"]) || ($_GET["champs"]==="valeur")){
	$val=0;
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

					<!-- ICONE MODIF -->
					<header class="special container">
						<span class="icon fa-edit"></span>
						<h2>modifier</h2>
						<h2>Un artiste</h2>
					</header>
					
					<!-- section -->
					<section class="wrapper style3 container special">
      				
      					<!-- ICONE : USER -->
						<header class="special container">
							<span class="icon fa-user"></span>
							<h2><?=$res0->nom_artiste?></h2> 
							<img style="margin-top:20px;" src="<?=$res0->img?>" alt="image artiste" width="475px">
						</header>
		  				   
      					<!-- BOUTTON champs et valeurs -->				
      					<a href="modifArtiste.php?champs=placeholder& idartiste=<?=$idartiste?>" class="button">Champs</a>
      					<a href="modifArtiste.php?champs=valeur& idartiste=<?=$idartiste?>" class="button">Valeur</a>
      					
      					<!-- Si on veut la valeur : -->
      					<?php if($val===0) :?>
      						
      						<!-- si on viens d une page concert on se redirige avec l id -->
		  					<?php if (isset($idconcert)):?>
								<form method="post" action="modifArtiste.php?idartiste=<?=$idartiste?>&idconcert=<?=$idconcert?>" style="margin-top:20px;" enctype="multipart/form-data">
								
							<!-- on se redirige sans l id du concert -->
							<?php else :?>
								<form method="post" action="modifArtiste.php?idartiste=<?=$idartiste?>" style="margin-top:20px;"   enctype="multipart/form-data">
							<?php endif;?>
								
								<div class="row gtr-50">
									<div class="col-6 col-12-mobile">				
										<input type="text" name="prenom" value="<?=$res0->prenom?>" placeholder="Prénom">
									</div>
									<div class="col-6 col-12-mobile">				
										<input type="text" name="nom" value="<?=$res0->nom?>" placeholder="Nom">
									</div>
									<div class="col-6 col-12-mobile">				
										<input type="text" name="nom_artiste" value="<?=$res0->nom_artiste?>" placeholder="Nom d'artiste">
									</div>
									<div class="col-6 col-12-mobile">				
										<input type="file" name="fichier" >
									</div>
									<div class="col-12">				
										<textarea name="descriptionartiste"  placeholder="Description de l'artiste" rows="5"><?=$res0->descriptionartiste?> </textarea>
									</div>
									<div class="col-12">				
									<input type="submit" name="send" value="Modifier">
									</div>
									
								</div>
							</form>
						<!-- si on veut les champs -->
						<?php else :?>
						
							<form method="post" action="#" enctype="multipart/form-data">
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
										<input type="text" name="img" placeholder="image">

									</div>
									<div class="col-12">				
										<textarea placeholder="Description de l'artiste" rows="5"></textarea>
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
