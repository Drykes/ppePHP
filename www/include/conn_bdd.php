<?php	

/*
* Connexion à la base de données : 
*/

	$hote="mysql11.000webhost.com";
	$dbname="a5300961_gsb";
	$utilisateur="a5300961_david";
	$mdp="abc123";
	
	try 
	{
		$bdd=new PDO('mysql:host='.$hote.';dbname='.$dbname,$utilisateur,$mdp);
	}
	catch (Exception $e)
	{
		die ("Impossible de se connecter à la base de données.");
	}
	
?>