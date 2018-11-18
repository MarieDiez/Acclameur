<?php
session_start();


include 'connexionPostgres.php';
$connexion=connexion();


// si session
if(isset($_SESSION)){
	

	// je recupere mon panier
	$panier=$_SESSION["panier"];
	
	// si je suis connecter
	if (isset($_SESSION['id'])&& isset($_SESSION['prixttl']) && isset($_SESSION['nbplaceslibres']) && !empty($_SESSION['nbplaceslibres'])  && !empty($_SESSION['prixttl'] || $_SESSION['prixttl'] ==0) && 
	!empty($_SESSION['id'])){
			
			// je recupere info de la session
			$id=$_SESSION["id"];
			$prix=$_SESSION["prixttl"];
			$date=date('Y-m-d');
			$date="$date";
			$nbplaceslibres=$_SESSION['nbplaceslibres'];
			
			
		
		
			// je creer un commande
			$sql="insert into commande (date,id_client,prix_total) values(:date,:id_client,:prix_total)"; // id/ref?
			$info=$connexion->prepare($sql);
			$info->bindParam(":date",$date);
			$info->bindParam(":id_client",$id);
			$info->bindParam(":prix_total",$prix);
			$info->execute();
			
			// je selectionne l id de la derniere commande
			$sqlidcmd="select id_commande 
			from commande
			where id_commande=(select max(id_commande)
			from commande where date=:date and prix_total=:prix and id_client=:id)";
			$infoidcmd=$connexion->prepare($sqlidcmd);
			$infoidcmd->bindParam(":id",$id);
			$infoidcmd->bindParam(":date",$date);
			$infoidcmd->bindParam(":prix",$prix);
			$infoidcmd->execute();
			$residcmd=$infoidcmd->fetch(PDO::FETCH_OBJ);
	
			// et pour chaque element du panier
			foreach($panier as $k=>$v){
				
				// si la qte != 0 
				if ($v !=0){
					// je recupere id + qte
					$id=$k;
					$qte=$v;
			
					// j insert dans la table ligne commade un article
					$sql="insert into ligne_commande (id_commande,id_concert,qte) values(:id,:idconcert,:qte)";
					$info=$connexion->prepare($sql);
					$info->bindParam(":id",$residcmd->id_commande);
					$info->bindParam(":idconcert",$id);
					$info->bindParam(":qte",$qte);
					$info->execute();
				
					// je decremente le nombre de place restantes de la quantite commandées
					$nb=$nbplaceslibres[$k]-$qte;
				
					// mise a jour du nombre de place libres
					$sqlModifPlaceLibre="update concertIndex set nbplaceslibres=:nbplace where id_concertindex=:idConcert";
					$infoModifPlaceLibre=$connexion->prepare($sqlModifPlaceLibre);
					$infoModifPlaceLibre->bindParam(":idConcert",$k);
					$infoModifPlaceLibre->bindParam(":nbplace",$nb);
					$infoModifPlaceLibre->execute();
			
				}
			}
		
			// je "detruit" mon panier
			unset($panier);
			$_SESSION["panier"]=$panier;
			
			// mail de confirmation
			$message = "Commande confirmé";
			$message = wordwrap($message, 70);
			mail($_SESSION["mail"], 'Inscription', $message);
	
			// redirection
			header("Location:paiment.php");
	}
	else{
		header("Location:connexionSignIn.php");
	}	
}


?>

