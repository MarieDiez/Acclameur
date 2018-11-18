<?php 
session_start();

/* PAGE D INSCRIPTION */

// fichier de connexion mysql
//include 'connexion.php';

// fichier de connexion postgre 
include 'connexionPostgres.php';

$connexion=connexion();

function redirect($url,$time=0) {
	//permet l'affichage de message avant une redirection
	echo "<meta http-equiv=\"refresh\" content=\"$time; URL=$url\" />";
	exit();
}

// verifie si client inscrit
function MailDansBase($mail,$connexion){
	$sql="select * from personneinscrite where mail=:mail";
	$info=$connexion->prepare($sql);
	$info->bindParam(':mail',$mail);
	$info->execute();
	$res=$info->fetchAll(PDO::FETCH_OBJ);


	if (count($res)==0){
		return false;
	}
	else {
		return true;	
	}

}

// si send 
if (isset($_POST["send"])){

	// recupere info
	$mail=htmlspecialchars($_POST["mail"]);
	$mail=addSlashes($mail);
	$mdp=htmlspecialchars($_POST["mdp"]);
	$mdpVerif=htmlspecialchars($_POST["mdpVerif"]);
	$nom=htmlspecialchars($_POST["nom"]);
	$nom=addSlashes($nom);
	$prenom=htmlspecialchars($_POST["prenom"]);
	$prenom=addSlashes($prenom);
	$img='images/'.$_FILES["fichier"]["name"];
	$ville=htmlspecialchars($_POST["ville"]);

	// non vide : on traite les infos
	if( !empty($_POST["mail"]) && isset($_POST["mdpVerif"]) && !empty($_POST["mdpVerif"]) && isset($_POST["ville"]) && isset($_POST["mdp"]) && isset($_POST["nom"]) && isset($_POST["prenom"])&& !empty($_POST["ville"]) && isset($_FILES["fichier"]) && ($_FILES["fichier"]["error"]==0) && !empty($_POST["mdp"]) && !empty($_POST["nom"])&& !empty($_POST["prenom"])){



		// code mdp
		$mdp=sha1($mdp);
		$mdpVerif=sha1($mdpVerif);

		if ($mdp==$mdpVerif){


			// verifie si inscrit
			if (MailDansBase($mail,$connexion)==false){


				// insertion de la personne
				$sql="insert into personne (nom,prenom,ville,img) values (:nom,:prenom,:ville,:img)";
				$info=$connexion->prepare($sql);
				$info->execute(array("nom"=>$nom,"prenom"=>$prenom,"ville"=>$ville,"img"=>$img));

				// recupere id de la personne 
				$sqlId="select id_personne from personne where nom=:nom and prenom=:prenom and ville=:ville and img=:img";
				$infoId=$connexion->prepare($sqlId);
				$infoId->execute(array("nom"=>$nom,"prenom"=>$prenom,"ville"=>$ville,"img"=>$img));
				$resId=$infoId->fetch(PDO::FETCH_OBJ);
				$id=$resId->id_personne;


				move_uploaded_file($_FILES["fichier"]["tmp_name"], 'images/'.$_FILES["fichier"]["name"]);

				// insertion de la personne dans personneinscrite
				$sqlInsert="insert into personneInscrite (id_personne,mail,mdp) values (:id,:mail,:mdp)";
				$infoInsert=$connexion->prepare($sqlInsert);
				$infoInsert->execute(array("id"=>$id,"mail"=>$mail,"mdp"=>$mdp));


				// mail de confirmation
				$message = "Inscription confirmé";
				$message = wordwrap($message, 70);
				mail($mail, 'Inscription', $message);

				//redirection
				echo("<script>alert(\"Inscription réussite !\")</script>");
				redirect("connexionSignIn.php",$time=0);


			}
			else {
				echo("<script>alert(\"Personne déjà inscrite\")</script>");

			}
		}
		else {

			echo("<script>alert(\"Mot de passe différents ! \")</script>");
		}
	}
	else {
		echo("<script>alert(\"Merci de remplir tous les champs\")</script>");

	}	
}
else {
	$mail="";
	$nom="";
	$prenom="";
	$ville="";
	$img="";
}
?>


<!DOCTYPE HTML>
<html>
	<head>
		<title>Inscription</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
	</head>
	<body class="contact is-preload">
		<div id="page-wrapper">

			<!-- Header -->
				<header id="header">
					<h1 id="logo"><a href="index.php">Acclameur<span>| Concerts - Informations - Réservations</span></a></h1>
					<nav id="nav">
						<ul>
							<li class="current"><a href="index.php">Home</a></li>
							<li class="submenu">
								<a href="#">Liens</a>
								<ul>
									<li><a href="concertGenre.php?genreconcert=tous">Concerts <span class="icon fa-music"></span></a></li>
								</ul>
							</li>
						</ul>
					</nav>
				</header>

			<!-- Main -->
				<article id="main">

					<header class="special container">
						<span class="icon fa-file-text-o"></span>
						<h2>Inscrivez-vous</h2>
					</header>


						<section class="wrapper style4 special container medium">

								<div class="content">
									<form method="post" action="inscription.php" enctype="multipart/form-data" >
										<div class="row gtr-50">
											<div class="col-6 col-12-mobile">
												<input type="text" name="nom" placeholder="Nom" Value="<?=$nom?>" />
											</div>
											<div class="col-6 col-12-mobile">
												<input type="text" name="prenom" Value="<?=$prenom?>" placeholder="Prénom" />
											</div>
											<div class="col-12">
												<input type="text" name="mail" Value="<?=$mail?>" placeholder="E-mail" />
											</div>
											<div class="col-6 col-12-mobile">
												<input type="password" name="mdp" placeholder="Mot de passe" />
											</div>
											<div class="col-6 col-12-mobile">
												<input type="password" name="mdpVerif" placeholder="Réécrivez votre mot de passe" />
											</div>
											<div class="col-12">
												<input type="text" name="ville" Value="<?=$ville?>" placeholder="Ville" />
											</div>
											<div class="col-12">
												<input type="file" name="fichier" Value="<?=$img?>" />
											</div>
											<div class="col-12">
												<ul class="buttons">
													<li><input type="submit" name="send" value="Inscription" /></li>
												</ul>
											</div>
										</div>
									</form>
								</div>

						</section>

				</article>

			<!-- Footer -->
			<?php require("footer.php");?>

		</div>

		<!-- Scripts -->
		<?php require("scripts.php");?>

	</body>
</html>

