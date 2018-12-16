
DROP TABLE if exists ligne_commande;
DROP TABLE if exists commande;
DROP TABLE if exists groupe_artiste;
DROP TABLE if exists concertIndex;
DROP TABLE if exists concertGenre;
DROP TABLE if exists artiste;
DROP TABLE if exists personneInscrite;
DROP TABLE if exists adminInscrit;
DROP TABLE if exists personne;

CREATE TABLE artiste (
	id_artiste serial PRIMARY KEY,
	nom varchar(30),
	prenom varchar(30),
	nom_artiste varchar(30),
	descriptionArtiste text,
	img varchar(100)
);

CREATE TABLE concertGenre (
	genreConcert varchar(20) PRIMARY KEY,
	description text,
	img varchar(50)
);

CREATE TABLE concertIndex (
	id_concertIndex serial PRIMARY KEY,
	importance integer DEFAULT 0,
	genreConcert varchar(20),
	lieu varchar(30),
	dateConcert date,
	description text,
	prix integer,
	nbPlaces integer,
	nbPlacesLibres integer  CHECK ( nbPlacesLibres <= nbPlaces),
	lat integer,
	long integer,
	lienIframe text,
	foreign key (genreConcert) REFERENCES concertGenre(genreConcert)
);

CREATE TABLE groupe_artiste(
	id_artiste integer,
	id_concert integer,
	PRIMARY KEY(id_artiste,id_concert),
	foreign key(id_concert) REFERENCES concertIndex(id_concertIndex),
	foreign key(id_artiste) REFERENCES artiste(id_artiste)
);

CREATE TABLE personne(
	id_personne serial PRIMARY KEY,
	nom varchar(30),
	prenom varchar(30),
	ville varchar(30),
	img varchar(300)
);

CREATE TABLE personneInscrite(
	id_personneInscrite serial PRIMARY KEY,
	id_personne integer,
	mail varchar(50),
	mdp text,
	FOREIGN KEY (id_personne) REFERENCES personne(id_personne)
);


CREATE TABLE adminInscrit(
	id_adminInscrit serial PRIMARY KEY,
	id_personne integer,
	adminPrincipal boolean,
	mail varchar(50),
	mdp text,
	FOREIGN KEY (id_personne) REFERENCES personne(id_personne)
);

CREATE TABLE commande (
	id_commande serial PRIMARY KEY,
	date date,
	id_client integer,
	prix_total decimal
);

CREATE TABLE ligne_commande(
	id_commande integer,
	id_concert integer,
	qte integer,
	FOREIGN KEY (id_commande) REFERENCES  commande(id_commande),
	FOREIGN KEY (id_concert) REFERENCES  concertIndex(id_concertindex)
);