<?php 
session_start (); 
include_once('./include/fonctions.php');

require_once('./xajax_core/xajax.inc.php');
$xajax = new xajax(); //On initialise l'objet xajax.

if (isset($_REQUEST['page']))	//si la page souhaitée est spécifiée 
{
	importe_ajax_function($_REQUEST['page'],$xajax);
}
else
{	//si la page n'est pas spécifiée c'est que l'utilisateur se trouve sur la page d'accueil.
	//on va donc importer les fonction ajax pour la page d'acceuil
	importe_ajax_function(1,$xajax);
}

$xajax->processRequest();// Fonction qui va se charger de générer le Javascript, à partir des données que l'on a fournies à xAjax APRÈS AVOIR DÉCLARÉ NOS FONCTIONS.

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	
	<title></title>
	
	<link rel="stylesheet" media="screen" href="./styles/calendrier.css" />
	
	<?php 
		$ua = $_SERVER['HTTP_USER_AGENT'];
		if (stristr($_SERVER['HTTP_USER_AGENT'], "Mobile")) 
		{
			echo "<link rel='stylesheet' media='screen' href='./styles/styles-mobile.css' />";
		}
		else
		{
			echo "<link rel='stylesheet' media='screen' href='./styles/styles.css' />" ;
		}
	?>
	
	
	<script src="./scripts/jquery.js"></script>
	<?php $xajax->printJavascript(); /* Affiche le Javascript */?>
	
	<script>
		function choix_personne(choixP){
			$('#frm1').hide();
			$('#frm2').hide();
			switch (choixP){
				case 1:	
					if($('#ajout_admin').attr('checked'))
					{
						$('#frm1').show();
					}
					else
					{
						$('#frm1').hide();
					}
					break;
				case 2: 
					if($('#ajout_employe').attr('checked'))
					{
						$('#frm2').show();
					}
					else
					{
						$('#frm2').hide();
					}
					break;
				default:
					break;
			}
		}
	
		function selection(id)
		{
			$("tr").css("background-color", "");
			$(id).css("background-color", "red");
		}
	
		function choix_type (choix)
		{
			switch (choix)
			{
				case 1:	
					if($('#1').attr('checked'))
					{
						$('#type_materiel').show();
					}
					else
					{
						$('#type_materiel').hide();
					}
					break;
				case 2: 
					if($('#2').attr('checked'))
					{
						$('#type_lic_duree').show();
					}
					else
					{
						$('#type_lic_duree').hide();
					}
					break;
				case 3: 
					if($('#3').attr('checked'))
					{
						$('#type_lic_version').show();
					}
					else
					{
						$('#type_lic_version').hide();
					}
					break;
				case 4: 
					if($('#4').attr('checked'))
					{
						$('#type_lic_duree_associe').show();
					}
					else
					{
						$('#type_lic_duree_associe').hide();
					}
					break;
				case 5: 
					if($('#5').attr('checked'))
					{
						$('#type_lic_version_associe').show();
					}
					else
					{
						$('#type_lic_version_associe').hide();
					}
					break;
				default:
					break;
			}
		}
	</script>
	
</head>
<body>
	<div id="header">
		<?php include('./vue/entete.php'); ?>
	</div>

	
	<!-- Le contenu de la page, chargé en fonction du choix de l'utilisateur. -->
	<!-- si la personne n'est pas connectée on affiche le formulaire de connection à la placec  -->
	<div id="contenu">
		<?php
			//si la personne est connectée
			if(isset($_SESSION['id']) && isset($_SESSION['nom']) && isset($_SESSION['prenom']))
			{	//ici on va démarer la fonction avec en paramètre l'identifiant de la page souhaitée et elle s'occupera de nous renvoyer son contenu
				//include('/include/fonctions.php');
				if (isset($_REQUEST['page']))	//si la page souhaitée est spécifiée 
				{
					afficher($_REQUEST['page']);
				}
				else
				{	//si la page n'est pas spécifiée on affiche la page d'acceuil
					afficher(1);
				}
			}
			else if(!isset($_SESSION['id']) && isset($_REQUEST['page']) && $_REQUEST['page']==0)
			{
				afficher($_REQUEST['page']);
			}
			//si la personne n'est pas connectée
			else
			{
				//afficher_form_conn();
				include('./vue/form_connexion.php');
			}
		?>
	</div>

	
	<!-- Bas-de-page. Créateurs du site + la date actuelle. -->
	<div id="footer">
		<?php include('./vue/pied.php'); ?>
	</div>

</body>


<script src="./scripts/script_calendrier.js"></script>

</html>