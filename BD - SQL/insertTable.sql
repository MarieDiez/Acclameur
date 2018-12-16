insert into artiste (nom,prenom,nom_artiste,descriptionArtiste,img) values 
('Rodrigo - Gabriela','Sánchez - Quinte','Rodrigo y Gabriela','Rodrigo y Gabriela (souvent abrégé « Rod y Gab ») est un groupe de musique originaire de Mexico et composé de Rodrigo Sánchez (guitare solo) et de Gabriela Quintero (guitare rythmique).','images/RodGab.jpg'),
('Frager','Tom','Tom Frager','Tom Frager, né le 1er juillet 1977 à Dakar au Sénégal, est auteur-compositeur-interprète français faisant partie du groupe Gwayav	et surfeur dix fois champion de surf en Guadeloupe.Il est principalement connu pour avoir interprété la chanson Lady Melody. ','images/TomFrager.jpg'),
('Edward','Christopher Sheeran','Ed Sheeran','Edward Christopher Sheeran, dit Ed Sheeran, né le 17 février 1991 à Halifax (Yorkshire de l Ouest), est un auteur-compositeur-interprète et guitariste britannique. Sa carrière musicale commence en 2011 avec la maison de disques Atlantic Records, qui signe son premier album, +, écoulé à 4 millions de copies. Suivent les albums x (2014) et ÷ (2017), qui rencontrent également un grand succès international.
Il écrit par ailleurs des chansons pour plusieurs autres artistes, tels que Justin Bieber, Taylor Swift, Robbie Williams, James Blunt ou encore les One Direction. ','images/EdSheeran.jpg'),
(null,null,'Les violons sur le sable','Si vous n aimez pas la musique classique, la plage, les grandes voix d Opéra, les étoiles, les orchestres symphoniques, la danse, alors venez au Violon sur le Sable... vous y verrez tout ce que vous n aimiez pas avant ! Le Violon sur le Sable invite petits et grands, mélomanes avertis ou qui s’ignorent, à s’asseoir sur le sable, face à l’océan, le cœur dans les étoiles, pour savourer les plus belles pages classiques inattendues et jamais entendues en pareil endroit. La plage devient alors l’une de plus grandes et plus belles salles de concert capable d’accueillir plus de 50 000 personnes chaque soirée de spectacle, sous le ciel étoilé…,','images/violons.jpg'),
('Gergely','Peter','Peter Gergely','Peter Gergely est un guitariste hongrois de 24 ans. C est un youtubeur talentueu reprenant de célèbre chanson tout en en faisant des arrangements','images/PeterGergely.jpg'),
(null,null,'Eklipse','Eklipse est un groupe de musique compos de 4 jeune femmes violoniste. Le groupe produit des chansons sensuelle pleine de joie et de bonne humeur.','images/eklipse.jpg'),
(null,null,'Reggae SKA','Le Reggae Sun Ska Festival est un festival qui se déroule chaque année depuis 1998 en Gironde. Il ne s agit plus d un simple festival musical. En effet, depuis l édition de 2005, l association organisatrice Music Action s inscrit totalement dans une démarche éco-citoyenne et, aujourd hui, s affiche comme un festival durable','images/ska.jpg'),
('Diallo','Alpha','Black M','Black M ou Black Mesrimes, de son vrai nom Alpha Diallo, né le 27 décembre 1984 à Paris, est un rappeur et chanteur français d ascendance guinéenne. Il est membre du groupe Sexion d Assaut. En 2014, il sort son premier album solo intitulé Les Yeux plus gros que le monde. ','images/blackM.jpg');

insert into concertGenre (genreConcert,description,img) values
('Rock','Le rock est un genre musical apparu dans les années 1950 aux États-Unis et qui s est développé en différents sous-genres à partir des années 1960, notamment aux États-Unis et au Royaume-Uni1. Il prend ses racines dans le rock n roll des années 1940 et 1950, lui-même grandement influencé par le rhythm and blues et la country','images/rock.jpg'),
('Pop','La musique pop est un genre musical apparu dans les années 1960 au Royaume-Uni et aux États-Unis. Ces chansons parlent en général de l amour ou des relations entre les femmes et les hommes. Elle met l accent sur la chanson individuelle plutôt que sur l album, et utilise essentiellement des chansons courtes avec des rythmes associés à la danse','images/pop.jpg'),
('Jazz','Le jazz est un genre musical originaire du Sud des États-Unis, créé à la fin du XIXe siècle et au début du XXe siècle au sein des communautés afro-américaines. Avec plus de cent ans d existence, du ragtime au jazz actuel, il recouvre de nombreux sous-genres marqués par un héritage de la musique euro-américaine et afro-américaine, et conçus pour être joués en public','images/jazz.jpg'),
('Blues','Le blues (/bluz/) est un genre musical, vocal et instrumental dérivé des chants de travail des populations afro-américaines. Le blues est apparu dans le sud des États-Unis au cours du XIXe siècle. C est un style où le chanteur exprime sa tristesse et ses déboires. ','images/blues.jpg'),
('Flamenco','Le flamenco est un genre musical et une danse datant du XVIIIe siècle qui se danse seul, créé par le peuple andalou, sur la base d un folklore populaire issu des diverses cultures qui s épanouirent au long des siècles en Andalousie. ','images/flamenco.jpg'),
('Classique','La période classique en musique recouvre par convention la musique écrite entre la mort de Johann Sebastian Bach soit 1750 et le début de la période romantique, soit les années 1820. Par extension, on appelle « musique classique » toute la musique savante européenne, de la musique du Moyen Âge à la musique contemporaine.','images/classique.jpg'),
('Rap','Le rap est une forme d expression vocale, un des cinq piliers du mouvement culturel et musical hip-hop, ayant émergé au début des années 1970 dans les ghettos aux États-Unis. Le rap se caractérise par sa diction très rythmée','images/rap.jpg'),
('Reggae','Le reggae est un genre musical ayant émergé à la fin des années 1960, il est la plus populaire des expressions musicales jamaïcaines. Il devient, à la faveur de son succès international, un style musical internationalement apprécié, porteur d une culture qui lui est propre.','images/reggae.jpg'),
('comédie musicale','La comédie musicale est un genre théâtral, mêlant comédie, chant et danse. Apparue au tout début du XXe siècle, elle se situe dans la lignée du mariage du théâtre et de la musique classique qui avait donné naissance aux siècles précédents au ballet, à l opéra, à l opéra-bouffe et à l opérette.','images/comedie.jpg'),
('festivale','Un festival est une manifestation à caractère festif, organisée à époque fixe et récurrente annuellement, autour d une activité liée au spectacle, aux arts, aux loisirs, etc., d une durée de un ou plusieurs jours. ','images/festival.jpg');



insert into concertIndex (importance,genreConcert,lieu,dateConcert,description,prix,nbPlaces,nbPlacesLibres,lat,long,lienIframe)values (1,'Flamenco' ,'Zénith de Paris', '19-11-2018','Danse de flamenco à la fin du concert',40,600,600,48,2,'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2623.111713721843!2d2.391039815050612!3d48.89420810613909!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66dca02eb4cd3%3A0xf0d7efda07904839!2sLe+Z%C3%A9nith+Paris+-+La+Villette!5e0!3m2!1sfr!2sfr!4v1544299501736
');
insert into concertIndex (genreConcert,lieu,dateConcert,description,prix,nbPlaces,nbPlacesLibres,lat,long,lienIframe) values 
('Pop','Francofolie-La Rochelle','24-12-2018','Pas ce détails suplementaire',15,200,200,46,-1,'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2623.1117137218494!2d2.3910398150506236!3d48.89420810613909!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x480153af35323eab%3A0x86e53a10ffd0f677!2sFrancofolies!5e0!3m2!1sfr!2sfr!4v1544299739859'),
('Pop','Toulouse','4-02-2019','Pas ce détails suplementaire',35,500,500,43,1,'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d92456.81930837205!2d1.3628004951663903!3d43.60080288591057!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12aebb6fec7552ff%3A0x406f69c2f411030!2sToulouse!5e0!3m2!1sfr!2sfr!4v1544299836311'),
('Classique','Royan','30-06-2019','Feu d artifice à la fin du concert',0,1000,1000,46,-1,'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d44637.10130564729!2d-1.047665566920914!3d45.63437566715471!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x48017642c60779df%3A0xfc699a2a58ce73e1!2s17200+Royan!5e0!3m2!1sfr!2sfr!4v1544299886028'),
('Pop','Toulon','28-10-2018','Pas ce détails suplementaire',20,100,100,43,6,'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d46583.70322694915!2d5.898411457839598!3d43.13641834450567!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12c91b027b35b2fd%3A0x40819a5fd8fc830!2sToulon!5e0!3m2!1sfr!2sfr!4v1544299927464'),
('Pop','Nîme','3/03/2019','Pas ce détails suplementaire',18,350,350,44,4,'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d184200.57063075458!2d4.202767281463312!3d43.83232117593911!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12b42d0bd6e85339%3A0xde88134f9f200c03!2zTsOubWVz!5e0!3m2!1sfr!2sfr!4v1544299970614'),
('Reggae','Gironde','28/03/2019','concert en pleine air',0,1500,1500,45,0,'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d90493.58959686977!2d-0.6561814533590556!3d44.863828220851474!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd5527e8f751ca81%3A0x796386037b397a89!2sBordeaux!5e0!3m2!1sfr!2sfr!4v1544300007057'),
('Flamenco','La Rochelle','15/03/2019','Pas ce détails suplementaire',24,1000,1000,46,-1,'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2623.1117137218494!2d2.3910398150506236!3d48.89420810613909!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x480153af35323eab%3A0x86e53a10ffd0f677!2sFrancofolies!5e0!3m2!1sfr!2sfr!4v1544299739859'),
('Rap','La Rochelle','17/04/2019','Pas ce détails suplementaire',28,1000,1000,46,-1,'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2623.1117137218494!2d2.3910398150506236!3d48.89420810613909!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x480153af35323eab%3A0x86e53a10ffd0f677!2sFrancofolies!5e0!3m2!1sfr!2sfr!4v1544299739859');
						  
insert into groupe_artiste (id_artiste,id_concert) values 
(1,1),
(1,8),
(3,2),
(3,6),
(2,2),
(4,4),
(3,3),
(5,6),
(5,2),
(6,2),
(7,7),
(8,9);
			
insert into personne (nom,prenom,ville,img) values 
('Principale','Admin','ville','images/profileAdminP.png'),
('Inscrite','Personne','ville1','images/profilePersonne.png'),
('Secondaire','Admin','ville1','images/profileAdminSec.png');



insert into personneInscrite(id_personne,mail,mdp) values 
(2,'personne.mail@gmail.com','940c0f26fd5a30775bb1cbd1f6840398d39bb813');

insert into adminInscrit (id_personne,adminPrincipal,mail,mdp) values 
(1,TRUE,'admin.mail@gmail.com','940c0f26fd5a30775bb1cbd1f6840398d39bb813'),
(3,FALSE,'admin1.mail@gmail.com','940c0f26fd5a30775bb1cbd1f6840398d39bb813');


