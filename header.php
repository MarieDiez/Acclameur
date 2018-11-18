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
