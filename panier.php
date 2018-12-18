<?php
session_start();

// Date francaise 
setlocale (LC_TIME, 'fr_FR.utf8','fra');

include 'connexionPostgres.php';
$connexion=connexion();


// augmenter le nombre de billets 
if (isset($_POST["send"]) && !empty($_POST["number"]) && isset($_GET["id_concert"]) && !empty($_GET["id_concert"])){
	$_SESSION["panier"][$_GET["id_concert"]]=$_POST["number"];
	header("Location:panier.php");
}

// si il y a une session
if(isset($_SESSION["panier"])){

	// je recupere la session panier et je recupere tous les concerts avec nombre de ticket >0
	foreach($_SESSION["panier"] as $k=>$v){
		if ($v>0){
			$panier[$k]=$v;
		}
	}



	// selection tous les concerts du panier
	foreach($panier as $k=>$v){
		$concert= "
			select * 
			from concertIndex ci
			where id_concertindex=:id
			";
		$info=$connexion->prepare($concert);
		$info->bindParam(':id',$k);
		$info->execute();	
		$res[]=$info->fetch(PDO::FETCH_OBJ);
	}

}

// panier vide 
if (!isset($_SESSION["panier"]) || count($panier)==0){
	$vide=true;
}


// tous les artistes des concerts
foreach($res as $k=>$v){
	$idConcert=$v->id_concertindex;
	$sqlArtisteConc="
	select * 
	from artiste ar 
	join groupe_artiste ga on ga.id_artiste=ar.id_artiste
	join concertIndex ci on ci.id_concertindex=ga.id_concert
	where id_concertindex=:id;
	";
	$infoArtisteConc=$connexion->prepare($sqlArtisteConc);
	$infoArtisteConc->bindParam(":id",$idConcert);
	$infoArtisteConc->execute();
	$resArtisteConc[$idConcert]=$infoArtisteConc->fetchAll(PDO::FETCH_OBJ);
}

// supprimer concert
if (isset($_GET['supprimer']) && isset($_GET["id_concert"]) && !empty($_GET['supprimer']) && !empty($_GET["id_concert"])){
	// recupere position de l article
	$k=$_GET["id_concert"];
	// je supprime dans le panier l id de l article que je trouve avec la position 

	$panier[$k]=0;

	// j actualise le panier
	$_SESSION["panier"]=$panier;
	// je redirige vers le panier
	header("Location:panier.php");
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
$libre=true;
?>



<!DOCTYPE HTML>

<html>
	<head>
		<title>Panier</title>
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
			<!-- L'image de banniere porte un nom specifique -->

				<article id="main">	


					<header class="special container">
						<span class="icon fa-shopping-cart"></span>
						<h2>Mon Panier</h2>
					</header>



					<section class="wrapper style4 container">
					<?php if (isset($vide)):?>
						<h2 style="text-align:center;">Panier Vide</h2>
						<img style="margin-left:40%" src="images/corbeille.png" alt="corbeille" width="20%">
					<?php else :?>
						<?php foreach($res as $k=>$v):?>
							<div class="row gtr-150">
								<div class="col-8 col-12-narrower">
														<!-- Content -->
									<div class="content">
										<section>
											<h2>À <strong><?=$v->lieu?></strong></h2>
												<p>Le <?=strftime('%d %B %Y',strtotime($v->dateconcert))?></br> 										(<?=$v->nbplaceslibres?> places restantes)</p>
												<?php if ($v->prix != 0 ):?>

													<p class="prix"><?=$v->prix?> € x <?=$panier[$v->id_concertindex]?> = <?=$panier[$v->id_concertindex]* $v->prix ?>€ </p>
												<?php $somme+=$panier[$v->id_concertindex]* $v->prix?>
												<?php else :?>
													<p class="prix">Gratuit</p>
												<?php endif;?>
												<form style="margin-top:-40px;" method="post" action="panier.php?id_concert=<?=$v->id_concertindex?>">
													<label>Quantité : </label>
													<input style="width:100px; margin-bottom:10px;" type="number" value="<?=$panier[$v->id_concertindex]?>" min="1" name="number">	
													<a href="panier.php?supprimer=1&id_concert=<?=$v->id_concertindex?>" style="border: solid 1px #777; color:black; padding:0.4em " type="submit">X</a><br>
													<input class="button small" type="submit" value="Envoyer" min="1" name="send">

												</form>

										</section>
									</div>
									</div>
									<div class="col-4 col-12-narrower">
													<!-- Sidebar -->

										<div class="sidebar">

											<section>
												<header>
													<h3>Aristes Présent</h3>
												</header>
												<?php foreach ($resArtisteConc as $key=>$val):?>
													<?php if($v->id_concertindex == $key):?>
														<?php foreach ($val as $key1=>$val1):?>	
																														<a style="width:25%"  href="infoConcert.php?id_artiste=<?=$val1->id_artiste?>& id_concert=<?=$v->id_concertindex?>" class="image featured">
																	<div style='position:relative'>
																		<img style="" src="<?=$val1->img?>" alt="image artiste" /></a>
																		<p style="position:absolute; left:120px; top:24px"><?=$val1->nom_artiste?></p>
																	</div>
														<?php endforeach;?>
													<?php endif;?>
												<?php endforeach;?>

												<footer>
													<ul class="buttons">
														<li><a href="infoConcert.php?id_artiste=<?=$val[0]->id_artiste?>&id_concert=<?=$v->id_concertindex?>" class="button small">Voir le concert</a></li>
													</ul>
												</footer>
											</section>
											
										</div>
										</div>
									</div>
									<!-- dans session les info necessaires a la commande -->
									<?php $_SESSION["qte"][$cmtp]=$panier[$v->id_concertindex];?>
									<?php $_SESSION["date"][$cmtp]=$v->dateconcert;?>
									<?php $_SESSION["lieu"][$cmtp]=$v->lieu;?>
									<?php $_SESSION["prix"][$cmtp]=$v->prix;?>
									
									<!-- dans session on place le nombre de place libre de chaque concert du panier -->
									<?php $_SESSION["nbplaceslibres"][$v->id_concertindex]=$v->nbplaceslibres;?>
								
									<?php $cmtp++;?>
							
							<!-- si un des concerts n as plus de place suffisante -->
							<?php if (($v->nbplaceslibres)<$panier[$v->id_concertindex] && $libre!=false):?>
								<?php $libre=false;?>
							<?php endif;?>
						<?php endforeach;?>
						
						<p style="text-align:center;" class="prix">Total des tickets :<?=$somme?> €</p>
						<?php $_SESSION["prixttl"]=$somme;?>
	
						<?php if (isset($libre) && $libre!=false):?>
							<a style="margin-left:36%;" href="commande.php" class="button primary">Commander</a>
						<?php else:?>
							<p class="cadre">Plus de place libre pour un des concerts</p>
						<?php endif;?>
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



