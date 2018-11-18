<?php

define("SERVEUR","localhost");
define("USER","postgres");
define("MDP","");
define("BD","concert");


// fonction de connexion
function connexion($hote=SERVEUR,$username=USER,$mdp=MDP,$bd=BD){

try {
    $connex= new PDO('pgsql:host='.$hote.';dbname='.$bd, $username, $mdp);
    $connex->exec("SET CHARACTER SET utf8");
    return $connex;
}
catch(Exception $e) {
    echo 'Erreur : '.$e‐>getMessage().'<br />';
    echo 'N° : '.$e‐>getCode();
    return null;
    }
    
}

?>
