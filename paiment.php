<?php
session_start();

/* PAGE DE PAIEMENT */

include 'connexionPostgres.php';
$connexion=connexion();

// Date francaise 
setlocale (LC_TIME, 'fr_FR.utf8','fra');


function redirect($url,$time=0) {

	//permet l'affichage de message avant une redirection
	echo "<p>Transaction en cours</p>";
	echo "<img src=\"images/ajax-loader.gif\" alt=\"loader\">";

	// message de confirmation
	echo("<script>alert(\"Commande effectuée !\")</script>");
	echo "<meta http-equiv=\"refresh\" content=\"$time; URL=$url\" />";

	exit();
}


// si send + non vide 
if (isset ($_POST["send"]) && isset ($_POST["paiment"]) && !empty($_POST["paiment"])){

	// remise a zero apres paiment
	$_SESSION["lieu"]=NULL;
	$_SESSION["date"]=NULL;
	$_SESSION["qte"]=NULL;
	$_SESSION["prix"]=NULL;

	// redirection
	redirect("index.php",$time=0);
}

// je recupere info session de la commande : pour verification par l utilisateur
if (isset ($_SESSION["qte"]) && isset($_SESSION["date"])  && isset ($_SESSION["lieu"])  && isset ($_SESSION["prix"]) && !empty ($_SESSION["qte"]) && !empty ($_SESSION["prix"]) && !empty($_SESSION["date"])  && !empty ($_SESSION["lieu"])){
	$lieu=$_SESSION["lieu"];
	$date=$_SESSION["date"];
	$qte=$_SESSION["qte"];
	$prix=$_SESSION["prix"];

}

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

// initialisation	
$somme=0;	
$cmtp=0;
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>Paiement</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
	</head>

	<body>
	<!-- DEBUT de la page -->
	<!-- HEADER -->
	<?php require "header.php";  ?>
		<article id="main">
			<header class="special container">
			<span class="icon fa-credit-card"></span>
			<h2>Paiment de la commande</h2>
		</header>
			<section class="wrapper style3 container special">
			<header class="major">
			<span class="icon fa-cc-visa fa-2x"></span>
			<span class="icon fa-cc-amex fa-2x"></span>
			<span class="icon fa-cc-mastercard fa-2x"></span>
			<span class="icon fa-cc-paypal fa-2x"></span>
			<span class="icon fa-cc-stripe fa-2x"></span>
			<span class="icon fa-cc-card fa-2x"></span>
			<span class="icon fa-cc-wallet fa-2x"></span>
		</header>
		<div style="text-align:left">
			<p>Récapitulatif de la commande :<p>
			<ul>
			<!-- une date par concert commander -> nombre de concert -->
			<?php for($i=0;$i<count($date);$i++):?>
				<li><p><?=$lieu[$i]?> le <?=strftime('%d %B %Y',strtotime($date[$i]))?> : <?=$qte[$i]?>*<?=$prix[$i]?>= <?=$prix[$i]*$qte[$i]?>€</p></li>
			<?php endfor;?>
			</ul>
		</div>
		<form method="post" action="paiment.php">
			<select name="paiment">
				<option>Visa</option>
				<option>American Express</option>
				<option>Master Card</option>
				<option>Paypal</option>
				<option>Strip</option>
				<option>Bon de réduction</option>
			</select><br>
			<p>Prix de la commande : <span class="prix"><?=$_SESSION["prixttl"]?> €</span></p>
			<input type="submit" name="send" value="Valider"><br>
		</form><br>

		</section>	
	</article>


		<!-- Footer -->
		<?php require("footer.php");?>
		</div>

		<!-- Scripts -->
		<?php require("scripts.php");?>
	</body>
</html>



