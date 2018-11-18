<?php 
session_start();

/* PAGE DE CONNEXION */

// fichier de connexion mysql
//include 'connexion.php';

// fichier de connexion postgre 
include 'connexionPostgres.php';

$connexion=connexion();


function redirect($url,$time=0) {
	echo "<meta http-equiv=\"refresh\" content=\"$time; URL=$url\" />";
	exit();
}


// formulaire
if (isset ($_POST["send"])){
	if (!empty($_POST["mail"]) && !empty($_POST["mdp"])){
		$mail=htmlspecialchars($_POST["mail"]);
		$mdp=sha1(htmlspecialchars($_POST["mdp"]));
		$rep=TRUE;

		// REQUETE SQL 
		
		// selectionne la personne avec ce mail et ce mdp
		$sql="select * from personneInscrite where mail=:mail  and mdp=:mdp";
		$info=$connexion->prepare($sql);
		$info->bindParam(":mail",$mail);
		$info->bindParam(":mdp",$mdp);
		$info->execute();
		$res=$info->fetchAll(PDO::FETCH_OBJ);

		// selectionne la personne avec ce mail et ce mdp
		$sql2="select * from adminInscrit where mail=:mail  and mdp=:mdp";
		$info2=$connexion->prepare($sql2);
		$info2->bindParam(":mail",$mail);
		$info2->bindParam(":mdp",$mdp);
		$info2->execute();
		$res2=$info2->fetchAll(PDO::FETCH_OBJ);

		// selectionne la personne avec ce mail et ce mdp
		$sql3="select * from adminInscrit where mail=:mail  and mdp=:mdp and adminprincipal=:rep";
		$info3=$connexion->prepare($sql3);
		$info3->bindParam(":mail",$mail);
		$info3->bindParam(":mdp",$mdp);
		$info3->bindParam(":rep",$rep);
		$info3->execute();
		$res3=$info3->fetchAll(PDO::FETCH_OBJ);


		// dans quelle table et quel statu a la personne
		if (count($res3)>0){

			$_SESSION["admin"]=2;
			$_SESSION["mail"]=$mail;
			$_SESSION["id"]=$res3[0]->id_personne;
			header("Location:index.php");
		}
		else if (count($res)>0){
			$_SESSION["admin"]=-1;
			$_SESSION["mail"]=$mail;
			$_SESSION["id"]=$res[0]->id_personne;
			// id de la personne $_SESSION['id']=$res3[0]->id_personne;
			header("Location:index.php");
		}
		else if (count($res2) >0){
			$_SESSION["admin"]=1;
			$_SESSION["mail"]=$mail;
			$_SESSION["id"]=$res2[0]->id_personne;
			header("Location:index.php");
		}


	}
	else {
		echo("<script>alert(\"Remplir tout les champs\")</script>");
	}

}

if (isset($_GET["oublie"])){

	// mail de confirmation
	$message = "Mail de Récupération";
	$message = wordwrap($message, 70);
	mail($mail, 'Mail de récupération', $message);

	echo("<script>alert(\"Un mail de recuperation vous à été envoyé\")</script>");
	redirect("connexionSignIn.php",$time=0);
}


?>


<!DOCTYPE HTML>
<html>
	<head>
		<title>Connexion</title>
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
											<?php if (isset($_SESSION["admin"])):?>
												<li><a href="panier.php">Mon panier <span class="icon fa-shopping-cart"></span></a></li>
											<?php endif;?>
								</ul>
							</li>
						</ul>
					</nav>
				</header>

			<!-- Main -->
				<article id="main">

					<header class="special container">
						<span class="icon fa-file-text-o"></span>
						<h2>connectez-vous</h2>
					</header>

						<section class="wrapper style4 special container medium">
								<div class="content">
									<form method="post" action="connexionSignIn.php">
										<div class="row gtr-50">
											<div class="col-12">
												<input type="text" name="mail" placeholder="E-mail" />
											</div>
											<div class="col-12">
												<input type="password" name="mdp" placeholder="Mot de passe" />
											</div>
											<div class="col-12">
												<ul class="buttons">
													<li><a href="inscription.php" class="button"value="Inscription">Inscription</a></li>
													<li><input type="submit" name="send" value="Connexion" /></li>
												</ul>
											</div>
										</div>
									</form>

									<?php if (count($res)==0 && count($res2)==0 && isset($_POST["send"])):?>
										<p>E-mail ou mot de passe incorrete, merci de rééssayer</p>
									<?php endif;?>
								</div>
						</section>
						<a style="margin-left:45%;"href="connexionSignIn.php?oublie=1">Mot de passe oublié ? </a>		
				</article>

			<!-- Footer -->
			<?php require("footer.php");?>

		</div>

		<!-- Scripts -->
		<?php require("scripts.php");?>

	</body>
</html>

