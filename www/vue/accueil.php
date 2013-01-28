<?php
require('./include/conn_bdd.php');

/*PERSONNES*/
$req="SELECT COUNT(NULLIF(administrateur,0)) AS 'sont_admin', COUNT(*) AS 'total' FROM PERSONNE";
$resultat=$bdd->query($req);
while($lignes=$resultat->fetch(PDO::FETCH_OBJ))
{
	$nb_membres[0] = $lignes->sont_admin;	//ce tableau contiendra seulement 2 valeurs : nombre de personnes administrateurs et nombre total de personnes
	$nb_membres[1] = $lignes->total;
}

//MATERIELS
$req="SELECT COUNT(DISTINCT id_personne) as nb_pers, count(*) as nb_materiel FROM MATERIEL";
$resultat=$bdd->query($req);
while($lignes=$resultat->fetch(PDO::FETCH_OBJ))
{
	//ce tableau contiendra 2 valeurs : nombre de personnes ayant emprunté un ou plusieurs materiels et nombre de materiels empruntés
	$moyenne_prets[0] = $lignes->nb_pers;
	$moyenne_prets[1] = $lignes->nb_materiel;
}

//LICENCES DE DUREE
$req="SELECT COUNT(DISTINCT id_personne) as nb_pers, count(*) as nb_lic_duree FROM LICENCE_DUREE";
$resultat=$bdd->query($req);
while($lignes=$resultat->fetch(PDO::FETCH_OBJ))
{
	$moyenne_lic_duree[0] = $lignes->nb_pers;
	$moyenne_lic_duree[1] = $lignes->nb_lic_duree;
}



//LICENCES DE VESION
$req="SELECT COUNT(DISTINCT id_personne) as nb_pers, count(*) as nb_lic_duree FROM LICENCE_VERSION";
$resultat=$bdd->query($req);
while($lignes=$resultat->fetch(PDO::FETCH_OBJ))
{
	$moyenne_lic_version[0] = $lignes->nb_pers;
	$moyenne_lic_version[1] = $lignes->nb_lic_duree;
}
?>
<h2>Accueil</h2>

<p style="padding:5px;border:solid 2px blue;display:inline-block;text-align:left;">
	Actuellement il y a <b><?php echo $nb_membres[1]; ?></b> utilisateurs enregistrés dont <b><?php echo $nb_membres[0]; ?></b> sont administrateurs.<br/><br/>
	
<b>Les matériels : </b><br/>
	En moyenne il y a <b><?php if($moyenne_prets[0]!=0){ echo ($moyenne_prets[1]/$moyenne_prets[0]);} else { echo "0"; }; ?></b> matériel(s) prêté(s) par personne. Au total <b><?php echo $moyenne_prets[1]; ?></b> matériel(s) pour <b><?php echo $moyenne_prets[0]; ?></b> personne(s).<br/><br/>
	
<b>Les licences de durée : </b><br/>
	En moyenne il y a <b><?php if($moyenne_lic_duree[0]!=0){ echo ($moyenne_lic_duree[1]/$moyenne_lic_duree[0]);} else { echo "0"; }; ?></b> licence(s) de durée prêtée(s) par personne. Au total <b><?php echo $moyenne_lic_duree[1]; ?></b> licence(s) de durée pour <b><?php echo $moyenne_lic_duree[0]; ?></b> personne(s).<br/><br/>
	
<b>Les licences de version : </b><br/>
	En moyenne il y a <b><?php if($moyenne_lic_version[0]!=0){ echo ($moyenne_lic_version[1]/$moyenne_lic_version[0]); } else { echo "0"; }; ?></b> licence(s) de version prêtée(s) par personne. Au total <b><?php echo $moyenne_lic_version[1]; ?></b> licence(s) de version pour <b><?php echo $moyenne_lic_version[0]; ?></b> personne(s).

</p>


<?php //afficher le tableau des matériel qui ont plus de 5 ans d'ancienneté 
$req="SELECT *, nom_marque FROM MATERIEL, MARQUE, TYPE_MATERIEL WHERE DATEDIFF( NOW(),date_circulation)  >= 1825 GROUP BY id_materiel";
$resultat=$bdd->query($req);
$i=0;
while($lignes=$resultat->fetch(PDO::FETCH_OBJ))
{
if($i==0)
			{
				echo "<table class='tab_recap'>";
				echo "<CAPTION><h4>Les Materiels qui ont plus de 5 ans d'ancienneté : </h4></CAPTION>";
				echo "<tr>";
					echo "<th>N° Materiel</th>";
					echo "<th>Date de mise en circulation</th>";
					echo "<th>Garantie</th>";
					echo "<th>Marque</th>";
					echo "<th>Type</th>";
				echo "</tr>";
			}
			echo "<tr id='materiel_$lignes->id_materiel'>";
				echo "<td>$lignes->id_materiel</td>";
				$date_circulation = implode('/', array_reverse(explode('-', $lignes->date_circulation)));
				echo "<td>$date_circulation</td>";
				echo "<td>$lignes->garantie an(s)</td>";
				echo "<td>$lignes->nom_marque</td>";
				echo "<td>$lignes->type_materiel</td>";
			echo "</tr>";
			$i++;
		}
		if($i>0)
		{
			echo "</table><br/>";
		}




//afficher le tableau des licences qui expirent dans moins de 15 jours 
$req="SELECT * FROM LICENCE_DUREE WHERE DATEDIFF(NOW() , debut_licence)  >= (duree_licence*365)-15";
$resultat=$bdd->query($req);
$i=0;
$a="";
while($lignes=$resultat->fetch(PDO::FETCH_OBJ))
{
if($i==0)
			{
				$a.="<table class='tab_recap'>";
				$a.="<CAPTION><h4>Les licences qui expirent dans moins de 15 jours : </h4></CAPTION>";
				$a.="<tr>";
					$a.="<th>N° Licence</th>";
					$a.="<th>Nom de la licence</th>";
					$a.="<th>Date de début</th>";
					$a.="<th>La durée</th>";
				$a.="</tr>";
			}
			$a.="<tr>";
				$a.="<td>$lignes->id_licence_duree</td>";
				$a.="<td>$lignes->nom_version</td>";
				$debut_licence = implode('/', array_reverse(explode('-', $lignes->debut_licence)));
				$a.="<td>$debut_licence</td>";
				$a.="<td>$lignes->duree_licence an(s)</td>";
			$a.="</tr>";
			$i++;
		}
		if($i>0)
		{
			$a.="</table><br/>";
			$lic_dur_present=true;
		}
echo $a;

?>