<?php
session_start();

// Date francaise 
setlocale (LC_TIME, 'fr_FR.utf8','fra');

// fichier de connexion mysql
//include 'connexion.php';

// fichier de connexion postgre 
include 'connexionPostgres.php';

$connexion=connexion();

	


if (isset($_GET["panier"]) && !empty($_GET["panier"]) && !isset($_SESSION["panier"]) ){
	$idconcert=$_GET["panier"];
	$_SESSION["panier"][$idconcert]=1;	
	header("Location:panier.php");
}
else if (isset($_GET["panier"]) && !empty($_GET["panier"]) && isset($_SESSION["panier"]) ){
	$idconcert=$_GET["panier"];
	foreach($_SESSION["panier"] as $k=>$v){
		if($k==$idconcert){
			$_SESSION["panier"][$idconcert]=$v+1;
			$trouverIndex=true;
		}
		else {
			if(!$trouverIndex){
				$_SESSION["panier"][$idconcert]=1;
			}
			
		}
	}
	
	header("Location:panier.php");
}
	
// si il y a eu un passage de parametre par url et que les variables donnees ne sont pas vide on recupere le contenu
else if (isset ($_GET['id_artiste'])&& isset ($_GET['id_concert'])){
	if(!empty($_GET['id_artiste']) && !empty($_GET['id_concert'])){
		$idArtisteParam=$_GET['id_artiste'];
		$idConcert=$_GET['id_concert'];
	}
}
// concert de l artiste
else if (isset ($_GET['id_artiste'])&& isset ($_GET['lieu']) && isset ($_GET['date']) &&
	!empty($_GET['date']) && !empty($_GET['id_artiste']) && !empty($_GET['lieu'])){
		$idArtisteParam=$_GET['id_artiste'];
		$lieupara=$_GET['lieu'];
		$datepara=$_GET['date'];
		
		$sql="select * from concertIndex ci 
		join groupe_artiste ga on ga.id_concert=ci.id_concertindex
		join artiste ar on ar.id_artiste=ga.id_artiste
		where ar.id_artiste=:idAr and ci.lieu=:lieu and ci.dateconcert=:date";
		$info=$connexion->prepare($sql);
		$info->execute(array("idAr"=>$idArtisteParam,"lieu"=>$lieupara,"date"=>$datepara));
		$res=$info->fetch(PDO::FETCH_OBJ);
		
		
		header("Location:infoConcert.php?id_artiste=$idArtisteParam&id_concert=$res->id_concertindex");
	
}
// si il n y a pas eu de passage par url on redirige vers la page index
else {
	header("Location:index.php");
}

// REQUETE 

// Toute info sur l artiste +  concert
$sql="
select *
from artiste ar
join groupe_artiste ga on ga.id_artiste=ar.id_artiste
join concertIndex ci on ci.id_concertIndex = ga.id_concert
where ar.id_artiste=:idArtisteParam and ci.id_concertindex=:idConcert;
";
// traitement de la requete 
$info=$connexion->prepare($sql);
$info->bindParam(":idArtisteParam",$idArtisteParam);
$info->bindParam(":idConcert",$idConcert);
$info->execute();
$res=$info->fetch(PDO::FETCH_OBJ);




// selectionne tous les nom d artiste dans ce concert
$sql2="
select ar.nom_artiste, ar.id_artiste
from groupe_artiste ga
join concertIndex ci on ci.id_concertIndex=ga.id_concert
join artiste ar on ar.id_artiste=ga.id_artiste
where ci.id_concertindex=:id;
";
// traitement de la requete
$info2=$connexion->prepare($sql2);
$info2->bindParam(":id",$idConcert);
$info2->execute();
$res2=$info2->fetchAll(PDO::FETCH_OBJ);



// on ajoute a une variable chaque nom
$artistePre=array();


for ($i=0;$i<count($res2);$i++){
	if ($res2[$i]->nom_artiste != $res->nom_artiste){
		$artistePre[$i]=array($res2[$i]->id_artiste=>$res2[$i]->nom_artiste);
	}
}

// si il n y a pas d autre artiste on le precise
if (count($artistePre)==0){
	$artistePre="Pas d'autres artistes présents pour ce concert";
	
}

// tous les concerts de l artiste 

// selectionne le bon concert en fonction de l id de l artiste et du lieu 
$sql3="
select ci.lieu, ci.dateconcert,ci.id_concertIndex
from concertIndex ci
join groupe_artiste ga on ga.id_concert=ci.id_concertIndex
join artiste ar on ar.id_artiste = ga.id_artiste
where ar.id_artiste=:idArtisteParam;
";
// traitement de la requete 
$info3=$connexion->prepare($sql3);
$info3->bindParam(":idArtisteParam",$idArtisteParam);
$info3->execute();
$res3=$info3->fetchAll(PDO::FETCH_OBJ);




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
		<title><?=$res->nom_artiste?></title>
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
			<img src="../images/banner<?=str_replace(' ','',$res->nom_artiste)?>.jpg" alt="" height="500px" width="100%">
					
				<article id="main">	


					<header class="special container">
						<span class="icon fa-tag"></span>
						<h2><?=$res->nom_artiste?>  </h2>
					</header>

				
						<!-- info principale -->
						<section class="wrapper style1 container special">
							<div class="row">
								<div class="col-4 col-12-narrower">

									<section>
										<header>
											<h3>Date</h3>
										</header>
										<p class="cadre"><?=strftime('%d %B %Y',strtotime($res->dateconcert))?></p>
									</section>

								</div>
								<div class="col-4 col-12-narrower">

									<section>
										<header>
											<h3>Lieu</h3>
										</header>
										<p class="cadre"><?=$res->lieu?></p>	
									</section>

								</div>
								<div class="col-4 col-12-narrower">

									<section>
										<header>
											<h3>Prix</h3>
										</header>
										<p class="cadre"><?=$res->prix?>€</p>
									</section>

								</div>
							</div>
						</section>
						
						
						
						<!-- Info suplementaire -->
						<section class="wrapper style4 container">

								<div class="content">
									<section>
										<a href="#" class="image featured"><img src="images/pic04.jpg" alt="" /></a>
										<header>
											<h2>Informations complémentaire :</h2>
										</header>
										<ul>
											<?php if ($artistePre=="Pas d'autres artistes présents pour ce concert"):?>
											<li><p>Les autres artiste présents : <?=$artistePre?> </p></li>
											<?php else :?>
											<p>Les autres artiste présents :</p>
											<?php foreach($artistePre as $k=>$v):?>
												<?php foreach($v as $k1=>$v1):?>
													<li><a href="infoConcert.php?id_artiste=<?=$k1?>&id_concert=<?=$idConcert?>" class="button primary"><?=$v1?></a></li>
												<?php endforeach;?>
											<?php endforeach;?>
											<?php endif;?>
											<li><p>Biographie : </p>
											<p><?=$res->descriptionartiste?></p></li>
											<li><p>Genre : <?=$res->genreconcert?></p></li>
											<li><p>Détails : <?=$res->description?></p></li>
											<li><p>Tous les concerts de <?=$res->nom_artiste?> :</p>
												<table border="1">
													<tr>
														<td class="pp">Lieu</td>
														<td class="pp">Date</td>
													</tr>
													<?php foreach($res3 as $k=>$v):?>
													<tr>
														<td><a href="infoConcert.php?id_artiste=<?=$idArtisteParam?>&lieu=<?=$v->lieu?>&date=<?=$v->dateconcert?>"><?=$v->lieu?></a></td>
														<td><?=strftime('%d %B %Y',strtotime($v->dateconcert))?></td>
													</tr>
													<?php endforeach;?>
												</table>
											</li>
										</ul>
										
									</section>
								</div>

						</section>

						
					<section class="wrapper style container special">
						<?php if (isset($_SESSION["admin"])):?>
							<a href="infoConcert.php?panier=<?=$idConcert?>" class="button primary ">Réserver ce concert</a>
						<?php else :?>
							<a href="connexionSignIn.php" class="button primary ">Réserver ce concert</a>
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
