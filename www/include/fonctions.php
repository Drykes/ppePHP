<?php
/**
Fonction qui reçoit en paramètre les données de connexion, entrées par l'utilisateur puis vérifie si elles correspondennt 
à une personne dans la base. Si oui l'utilisateur sera connecté et les variables de session seront affectées.
*/
	function connexion($a, $b, $location)
	{
		$reponse = new xajaxResponse();
		include('conn_bdd.php');
		
		$req="SELECT * FROM PERSONNE WHERE login_personne=\"".$a."\" AND mdp_personne=\"".md5($b)."\" AND administrateur=1";

		$resultat=$bdd->query($req);
		$resultat->setFetchMode(PDO::FETCH_OBJ);
		$ligne=$resultat->fetch();
		
		if($ligne)
		{			
			$_SESSION['id']=$ligne->id_personne;
			$_SESSION['nom']=$ligne->nom_personne;
			$_SESSION['prenom']=$ligne->prenom_personne;
		
			$resultat->closeCursor();
			
			//on vérifie sur quel page rediriger la personne après la connection
			if(!empty($location))
			{
				$location="./?page=".$location;
			}
			else
			{
				$location="./";
			}
			$reponse->clear('frm_conn', 'innerHTML');
			$reponse->assign("msg","innerHTML","<div class='success'>Vous êtes maintenant connectés<br/>Vous allez être redirigés dans un instant.</div>");
			$reponse->script("setTimeout('window.location = \'$location\'',1500);");
			return $reponse; //la personne est connectée
		}
		else 
		{
			$reponse->assign("msg","innerHTML","<div class='error'>Identifiants incorrectes. Réessayez.</div>");
			return $reponse; //la personne n'est pas connectée
		}
	}
	
	function importe_ajax_function($id_page,$xajax)
	{
		if(isset($_SESSION['id']))
		{
			$xajax->register(XAJAX_FUNCTION, 'deconnexion');
		}
		else
		{
			$xajax->register(XAJAX_FUNCTION, 'connexion');
		}
		switch($id_page)
		{
			case 0 : $xajax->register(XAJAX_FUNCTION, 'recuperation');
				break;
			case 1:
				break;
			case 2:
				$xajax->register(XAJAX_FUNCTION, 'charger');
				$xajax->register(XAJAX_FUNCTION, 'saisiePret');
				$xajax->register(XAJAX_FUNCTION, 'resumer');
				break;
			case 3:
				$xajax->register(XAJAX_FUNCTION, 'charger');
				$xajax->register(XAJAX_FUNCTION, 'resumer');
				$xajax->register(XAJAX_FUNCTION, 'supp_materiel');
				$xajax->register(XAJAX_FUNCTION, 'supp_lic_duree');
				$xajax->register(XAJAX_FUNCTION, 'supp_lic_version');
				$xajax->register(XAJAX_FUNCTION, 'frm_modif_licence');
				$xajax->register(XAJAX_FUNCTION, 'modifier_licence');
				break;
			case 4:
				$xajax->register(XAJAX_FUNCTION, 'nvo_utilisateur');
				break;
			case 5:
				$xajax->register(XAJAX_FUNCTION, 'changer_mdp');
				break;
			case 6:
				break;
			default:
				break;
		}
	}

/**
Cete fonction affiche le contenu de la page en fonction de la valeur passée en paramètre
*/	
	function afficher($id_page) 
	{
		switch ($id_page)
		{
			case 0: if(!isset($_SESSION['id']))
				{
					include ("./vue/form_recuperation.php");
				}
				else
				{
					echo "Vous êtes déjà connectés.<br/> Pour changer votre mot de passe rendez-vous sur votre page <a href='./profil.php'>profil</a>.";
				}
				break;
			case 1 : include ("./vue/accueil.php");
				break;
			case 2 :include ("./vue/saisie_prets.php");
				break;
			case 3 : include ("./vue/resume_prets.php");
				break;
			case 4 : include ("./vue/administration.php");
				break;
			case 5 : include ("./vue/profil.php");
				break;
			default : include ("./vue/404.php");
				break;
		}
	}

/**
Cette fonction va chercher et renvoyer une liste de suggestions des personnes
*/
	function charger($a)
	{	
		$reponse = new xajaxResponse();
		$liste="";
		if (!isset($bdd))
		{
			require_once('conn_bdd.php');
		}
		
		if(!empty($a))
		{
			$req="SELECT id_personne, CONCAT( nom_personne, ' ', prenom_personne ) AS nomprenom FROM PERSONNE WHERE CONCAT( nom_personne,  ' ', prenom_personne ) LIKE  \"%$a%\" LIMIT 0 , 5";
			$resultat=$bdd->query($req);
			
			if($resultat->rowCount()>0)
			{
				while($lignes=$resultat->fetch(PDO::FETCH_OBJ))
				{
					$liste .= "<span onMouseDown=\"$('#msg').append('<div style=\'display:block;z-index:1005;outline:0px;position:fixed;left:50%;right:50%;margin-top:-150px;margin-left:-150px;top:50%;width:300px;height:300px;\'><img src=\'images/chargement.gif\'/></div>');$('#nomRecherche').val('$lignes->nomprenom'); $('#id').val('$lignes->id_personne');xajax_resumer('$lignes->id_personne');\" >
									<input type='hidden' id='id_personne' name='id_personne' value='". $lignes->id_personne ."' />". $lignes->nomprenom."
								</span><br/>";
				}
			}
		}
		
		$reponse->assign('noms', 'innerHTML', $liste);
		$reponse->assign('id', 'innerHTML', '');
		
		
		return $reponse;
	}

	function resumer($id)
	{
		$reponse = new xajaxResponse();
		$a="";
		require_once('conn_bdd.php');
		
/*----------------TABLEAU MATERIELS---------------------*/
		
		$req_materiel="SELECT id_materiel, date_circulation, garantie, nom_marque, type_materiel FROM MATERIEL NATURAL JOIN MARQUE NATURAL JOIN TYPE_MATERIEL WHERE id_personne = $id";
		$resultat=$bdd->query($req_materiel);
		$i=0;
		
		//les 3 variables qui permettent de savoir après si la personne a rien emprunté
		$mat_present=false;
		$lic_dur_present=false;
		$lic_vers_present=false;
		
		while($lignes=$resultat->fetch(PDO::FETCH_OBJ))
		{
			if($i==0)
			{
				$a.="<table class='tab_recap'>";
				$a.="<CAPTION><h3>Les Materiels : </h3></CAPTION>";
				$a.="<tr>";
					$a.="<th>N° Materiel</th>";
					$a.="<th>Date de mise en circulation</th>";
					$a.="<th>Garantie</th>";
					$a.="<th>Marque</th>";
					$a.="<th>Type</th>";
					$a.="<th>Actions</th>";
				$a.="</tr>";
			}
			$a.="<tr id='materiel_$lignes->id_materiel'>";
				$a.="<td>$lignes->id_materiel</td>";
				$date_circulation = implode('/', array_reverse(explode('-', $lignes->date_circulation)));
				$a.="<td>$date_circulation</td>";
				$a.="<td>$lignes->garantie an(s)</td>";
				$a.="<td>$lignes->nom_marque</td>";
				$a.="<td>$lignes->type_materiel</td>";
				$a.="<td id='action_$lignes->id_materiel'><a onclick='if(confirm(\"Etes-vous sur de supprimer ce materiel ? Des licences associées vont également être supprimées.\")){xajax_supp_materiel($id,$lignes->id_materiel);}'>Supprimer</a></td>";
			$a.="</tr>";
			$i++;
		}
		if($i>0)
		{
			$a.="</table><br/>";
			$mat_present=true;
		}
		
/*---------------TABLEAU LICENCES DUREE--------------------------*/
		
		$req_lic_dur="SELECT id_licence_duree, nom_version, debut_licence, duree_licence, id_materiel FROM LICENCE_DUREE WHERE id_personne=$id";
		$resultat=$bdd->query($req_lic_dur);
		$i=0;
		
		while($lignes=$resultat->fetch(PDO::FETCH_OBJ))
		{
			if($i==0)
			{
				$a.="<table class='tab_recap'>";
				$a.="<CAPTION><h3>Les Licences de durée : </h3></CAPTION>";
				$a.="<tr>";
					$a.="<th>N° Licence</th>";
					$a.="<th>Nom de la licence</th>";
					$a.="<th>Date de début</th>";
					$a.="<th>La durée</th>";
					$a.="<th>N° materiel associé</th>";
					$a.="<th>Actions</th>";
				$a.="</tr>";
			}
			$a.="<tr>";
				$a.="<td>$lignes->id_licence_duree</td>";
				$a.="<td>$lignes->nom_version</td>";
				$date_debut = implode('/', array_reverse(explode('-', $lignes->debut_licence)));
				$a.="<td>$date_debut</td>";
				$a.="<td>$lignes->duree_licence an(s)</td>";
				$a.="<td><a href='#materiel_$lignes->id_materiel' class='lien_lic-materiel' onClick='selection(\"#materiel_$lignes->id_materiel\");' >$lignes->id_materiel</a></td>";
				$a.="<td><a onclick=\"$('#bloc_frm_modif').append('<div style=\'display:block;z-index:1005;outline:0px;position:fixed;left:50%;right:50%;margin-top:-150px;margin-left:-150px;top:50%;width:300px;height:300px;\'><img src=\'images/chargement.gif\'/></div>');xajax_frm_modif_licence(1,$lignes->id_licence_duree);document.location.href='#popup';\">Modifier</a> | <a onclick=\"if(confirm('Etes-vous sur de supprimer cette licence ?')){xajax_supp_lic_duree($id,$lignes->id_licence_duree,'',true);}\">Supprimer</a></td>";
			$a.="</tr>";
			$i++;
		}
		if($i>0)
		{
			$a.="</table><br/>";
			$lic_dur_present=true;
		}
		
/*---------------TABLEAU LICENCES VERSION--------------------------*/
		
		$req_lic_dur="SELECT id_licence_version, nom_version, id_materiel FROM LICENCE_VERSION WHERE id_personne=$id";
		$resultat=$bdd->query($req_lic_dur);
		$i=0;
		
		while($lignes=$resultat->fetch(PDO::FETCH_OBJ))
		{
			if($i==0)
			{
				$a.="<table class='tab_recap'>";
				$a.="<CAPTION><h3>Les Licences de version : </h3></CAPTION>";
				$a.="<tr>";
					$a.="<th>N° Licence</th>";
					$a.="<th>Nom de la version</th>";
					$a.="<th>N° materiel associé</th>";
					$a.="<th>Actions</th>";
				$a.="</tr>";
			}
			$a.="<tr>";
				$a.="<td>$lignes->id_licence_version</td>";
				$a.="<td>$lignes->nom_version</td>";
				$a.="<td><a href='#materiel_$lignes->id_materiel' class='lien_lic-materiel' onClick='selection(\"#materiel_$lignes->id_materiel\");' >$lignes->id_materiel</a></td>";
				$a.="<td><a onclick=\"$('#bloc_frm_modif').append('<div style=\'display:block;z-index:1005;outline:0px;position:fixed;left:50%;right:50%;margin-top:-150px;margin-left:-150px;top:50%;width:300px;height:300px;\'><img src=\'images/chargement.gif\'/></div>');xajax_frm_modif_licence(2,$lignes->id_licence_version);document.location.href='#popup';\">Modifier</a> | <a onClick=\"if(confirm('Etes-vous sur de supprimer cette licence ?')){xajax_supp_lic_version($id,$lignes->id_licence_version,'',true);}\">Supprimer</a></td>";
			$a.="</tr>";
			$i++;
		}
		if($i>0)
		{
			$a.="</table><br/>";
			$lic_vers_present=true;
		}
		
		$reponse->assign('resume', 'innerHTML', $a);
		
		if(!$mat_present && !$lic_dur_present && !$lic_vers_present)
		{
			$reponse->assign('msg', 'innerHTML', '<div class="warning">Il n\'y a aucun emprunt pour cette personne.</div>');
		}
		else
		{
			$reponse->assign('msg', 'innerHTML', '');
		}
		
		return $reponse;
	}
	
	

	function saisiePret($id_personne="", $materiel, $lic_duree, $lic_version, $marque, $type, $date_circulation, $garantie, $nom_licence_duree, $debut_licence, $duree_licence, $nom_licence_version, $lic_duree_associe, $lic_version_associe, $nom_licence_duree_associe, $debut_licence_associe, $duree_licence_associe, $nom_licence_version_associe, $nvl_marque)
	{
		$reponse = new xajaxResponse();
		$elements_incomplets="";
		$msg_reponse = "";
		require('conn_bdd.php');
		if(empty($id_personne))
		{
			$elements_incomplets.="<li>Choisissez une personne</li>";
		}
		if($materiel)
		{	//si c'est un prêt de materiel
			if($marque==0)
			{
				$elements_incomplets.="<li>Choisissez la marque</li>";
			}
			if($type==0)
			{
				$elements_incomplets.="<li>Choisissez le Type</li>";
			}
			if(empty($date_circulation))
			{
				$elements_incomplets.="<li>Entrez la date de mise en circulation</li>";
			}
			else
			{
				$date_circulation = implode('/', array_reverse(explode('/', $date_circulation)));
			}
			if(empty($garantie) && $garantie==NULL)
			{
				$elements_incomplets.="<li>Indiquez le nombre d'années de garantie</li>";
			}
			if($marque!=0 && $type!=0 && !empty($date_circulation) && $garantie>=0)
			{
			
				if($marque==-1){
					if(empty($nvl_marque)){
						$elements_incomplets.="<li>Indiquez le nom de la nouvelle marque</li>";
					}
					else {
						$req_marque="INSERT INTO MARQUE VALUES(default, \"$nvl_marque\");";
						$count=$bdd->exec($req_marque);
						
						if($count==1)
						{
							$marque=$bdd->lastInsertId();
						}
						else
						{
							$elements_incomplets.="<li>la nouvelle marque n'a pas pu être enregistrée</li>";
						}
					}
				}
				$req_materiel="INSERT INTO MATERIEL(id_materiel, date_circulation, garantie, id_marque, id_type_materiel, id_personne) VALUES (default, '$date_circulation',$garantie,$marque,$type,$id_personne); ";
				
				if($lic_duree_associe || $lic_version_associe)//si la personne a choisi d'associer également une licence 
				{					
					$count=$bdd->exec($req_materiel);
					$id_materiel=$bdd->lastInsertId();
					if($count==1)
					{
						$msg_reponse .= "<div class='success'>Le materiel a bien été enregistré.</div><br/>";
						//le materiel a bien été créé donc on crée les licences en les associant
						
						if($lic_duree_associe)
						{
							if(empty($nom_licence_duree_associe))
							{
								$elements_incomplets.="<li>Entrez le nom de la licence de durée associée</li>";
							}
							if(empty($debut_licence_associe))
							{
								$elements_incomplets.="<li>Entrez la date du début de la licence de durée associée</li>";
							}
							else
							{
								$debut_licence_associe = implode('/', array_reverse(explode('/', $debut_licence_associe)));
							}
							if(empty($duree_licence_associe))
							{
								$elements_incomplets.="<li>Entrez la durée de la licence associée</li>";
							}
							if(!empty($nom_licence_duree_associe) && !empty($debut_licence_associe) && !empty($duree_licence_associe))
							{
								$req="INSERT INTO LICENCE_DUREE(id_licence_duree, nom_version, debut_licence, duree_licence, id_personne, id_materiel) VALUES (default, '$nom_licence_duree_associe', '$debut_licence_associe', $duree_licence_associe, $id_personne, $id_materiel);";
				
								$count=$bdd->exec($req);
				
								if($count==1)
								{
									$msg_reponse .= "<div class='success'>La licence de durée a bien été enregistrée et associée.</div><br/>";
								}
								else 
								{
									$msg_reponse .= "<div class='error'>La licence de durée n'a pas pu être associée ni enregistrée.</div><br/>";
								}
							}
						}
						if($lic_version_associe)
						{
							if(!empty($nom_licence_version_associe))
							{
								$req="INSERT INTO LICENCE_VERSION(id_licence_version, nom_version, id_personne, id_materiel) VALUES (default, '$nom_licence_version_associe', $id_personne, $id_materiel);";
				
								$count=$bdd->exec($req);

								if($count==1)
								{
									$msg_reponse .= "<div class='success'>La licence de version a bien été enregistrée et associée.</div><br/>";
								}
								else 
								{
									$msg_reponse .= "<div class='error'>La licence de version n'a pas pu être associée ni enregistrée.</div><br/>";
								}
							}
							else
							{
								$elements_incomplets.="<li>Entrez le nom de la licence associée</li>";
							}
						}
					}
					else 
					{
						$msg_reponse .= "<div class='error'>Le materiel n'a pas pu être enregistré.</div><br/>";
					}
				}
				else
				{
					$count=$bdd->exec($req_materiel);
				
					if($count==1)
					{
						$msg_reponse .= "<div class='success'>Le materiel a bien été enregistré.</div><br/>";
					}
					else 
					{
						$msg_reponse .= "<div class='error'>Le materiel n'a pas pu être enregistré.</div><br/>";
					}
				}
			}
		}
		if($lic_duree)
		{	//si c'est un prêt pour une licence de durée
			if(empty($nom_licence_duree))
			{
				$elements_incomplets.="<li>Entrez le nom de la licence de durée</li>";
			}
			if(empty($debut_licence))
			{
				$elements_incomplets.="<li>Entrez la date du début de la licence</li>";
			}
			else
			{
				$reponse->assign('errDebut_licence', 'innerHTML', "");
				$debut_licence = implode('/', array_reverse(explode('/', $debut_licence)));
			}
			if(empty($duree_licence))
			{
				$elements_incomplets.="<li>Entrez la durée de la licence</li>";
			}
			if(!empty($nom_licence_duree) && !empty($debut_licence) && !empty($duree_licence))
			{	//le form pour la licence duree a été correctement rempli
				
				$req="INSERT INTO LICENCE_DUREE(id_licence_duree, nom_version, debut_licence, duree_licence, id_personne) VALUES (default, '$nom_licence_duree', '$debut_licence', $duree_licence, $id_personne);";
				
				$count=$bdd->exec($req);
				
				if($count==1)
				{
					$msg_reponse .= "<div class='success'>La licence de durée a bien été enregistrée.</div><br/>";
				}
				else 
				{
					$msg_reponse .= "<div class='error'>La licence de durée n'a pas pu être enregistrée.</div><br/>";
				}
			}
		}
		if($lic_version)
		{	//si c'est un prêt pour une licence de version
			if(!empty($nom_licence_version))
			{
				$req="INSERT INTO LICENCE_VERSION(id_licence_version, nom_version, id_personne) VALUES (default, '$nom_licence_version', $id_personne);";
					
				$count=$bdd->exec($req);

				if($count==1)
				{
					$msg_reponse .= "<div class='success'>La licence de version a bien été enregistrée.</div><br/>";
				}
				else 
				{
					$msg_reponse .= "<div class='error'>La licence de version n'a pas pu être enregistrée.</div><br/>";
				}
			}
			else
			{
				$elements_incomplets.="<li>Entrez le nom de la licence</li>";
			}
		}
		//Si le choix du type de prêt n'a pas été fait on affiche un message d'erreur
		if(!$materiel && !$lic_duree && !$lic_version)
		{
			$elements_incomplets.="<li>Choisissez le type de prêt</li>";
		}
				
		if(!empty($elements_incomplets))
		{
			$elements_incomplets="<div class='warning'><ul>".$elements_incomplets."</ul></div>";
		}
		$reponse->assign('msg', 'innerHTML', $elements_incomplets.$msg_reponse);
		return $reponse;
	}
	
	function supp_materiel($id_personne,$id_materiel)
	{
		$reponse = new xajaxResponse();
		
		$msg="";
		
		/*Avant d'effacer le materiel il faut vérifier s'il n'est pass en relation avec une licence de durée ou de version*/
		require('conn_bdd.php');
		
		$req="SELECT * FROM LICENCE_DUREE WHERE id_materiel=$id_materiel AND id_personne=$id_personne";
		$resultat=$bdd->query($req);
		
		if($resultat->rowCount()>0)
		{
			while($lignes=$resultat->fetch(PDO::FETCH_OBJ))
			{
				$msg.=supp_lic_duree($id_personne,$lignes->id_licence_duree,$msg,false);
			}
		}
		$resultat->closeCursor();
		
		$req="SELECT * FROM LICENCE_VERSION WHERE id_materiel=$id_materiel AND id_personne=$id_personne";
		$resultat=$bdd->query($req);
		
		if($resultat->rowCount()>0)
		{
			while($lignes=$resultat->fetch(PDO::FETCH_OBJ))
			{
				$msg.=supp_lic_version($id_personne,$lignes->id_licence_version,$msg,false);
			}
		}
		$resultat->closeCursor();
		
		//on supprime maintenant le materiel
		$req="DELETE FROM MATERIEL WHERE id_materiel=$id_materiel";
		$count=$bdd->exec($req);

		if($count>=1)
		{
			$msg.= "<div class='success'>Le materiel a été supprimé.</div><br/>";
		}
		else 
		{
			$msg.= "<div class='error'>Le materiel n'a pas pu être supprimé.</div><br/>";
		}
		
		$reponse->assign("msg", 'innerHTML', $msg);
		$reponse->script("xajax_resumer($id_personne);");
		$reponse->script("setTimeout('$(\'#msg\').empty()',3000);");
		return $reponse;
		
	}
	
	function supp_lic_duree($id_personne,$id_lic_duree,$msg,$appel_depuis_ajax)
	{
		if($appel_depuis_ajax)
		{
			$reponse = new xajaxResponse();
		}
		
		require('conn_bdd.php');
		$req="DELETE FROM LICENCE_DUREE WHERE id_licence_duree=$id_lic_duree";
		$count=$bdd->exec($req);

		if($count>=1)
		{
			$msg.= "<div class='success'>Une licence de durée a été supprimée.</div><br/>";
		}
		else 
		{
			$msg.= "<div class='error'>Une licence de durée n'a pas pu être supprimée.</div><br/>";
		}
		
		if($appel_depuis_ajax)
		{
			$reponse->assign("msg", 'innerHTML', $msg);
			$reponse->script("xajax_resumer($id_personne);");
			$reponse->script("setTimeout('$(\'#msg\').empty()',3000);");
			return $reponse;
		}
		else
		{
			return $msg;
		}
	}
	
	function supp_lic_version($id_personne,$id_lic_version,$msg,$appel_depuis_ajax)
	{
		if($appel_depuis_ajax)
		{
			$reponse = new xajaxResponse();
		}
		
		require('conn_bdd.php');
		$req="DELETE FROM LICENCE_VERSION WHERE id_licence_version=$id_lic_version";
		$count=$bdd->exec($req);

		if($count>=1)
		{
			$msg.= "<div class='success'>Une licence de version a été supprimée.</div><br/>";
		}
		else 
		{
			$msg.= "<div class='error'>Une licence de version n'a pas pu être supprimée.</div><br/>";
		}
		
		if($appel_depuis_ajax)
		{
			$reponse->assign("msg", 'innerHTML', $msg);
			$reponse->script("xajax_resumer($id_personne);");
			$reponse->script("setTimeout('$(\'#msg\').empty()',3000);");
			return $reponse;
		}
		else
		{
			return $msg;
		}
	}	
	
	function nvo_utilisateur($type_utilisateur,$nom,$prenom,$mail,$login_personne="",$motdepasse="",$mdp_confirm="")
	{
		$reponse = new xajaxResponse();
		$elements_incomplets = "";
		$msg_reponse = "";
		$mail_ok=true;		

		if(isset($type_utilisateur))
		{
			require('conn_bdd.php');
			if($type_utilisateur==0) // si le type concerne un admin
			{
				if(empty($nom))
				{
					$elements_incomplets.="<li>Entrez le nom de la personne</li>";
				}
				if(empty($prenom))
				{
					$elements_incomplets.="<li>Entrez le prenom de la personne</li>";
				}
				if(empty($mail))
				{
					$elements_incomplets.="<li>Entrez le mail de la personne</li>";
					$mail_ok=false;
				}
				else
				{	//verifier si ça correspond à une adresse mail
					if(filter_var($mail, FILTER_VALIDATE_EMAIL))
					{
						//on vérifie si ce mail n'existe pas déjà dans la base
						$req='SELECT * FROM PERSONNE WHERE mail_personne="'.$mail.'";';
						$resultat=$bdd->query($req);
				
						if($resultat->rowCount()>0)
						{
							$elements_incomplets.="<li>Le mail que vous avez entré existe déjà</li>";
							$mail_ok=false;
						}
					}
					else
					{
						$elements_incomplets.="<li>L'adresse mail est incorrecte</li>";
						$mail_ok=false;
					}
				}
				if(empty($login_personne))
				{
					$elements_incomplets.="<li>Entrez le login de la personne</li>";
					$login_ok=false;
				}
				else
				{
					//on vérifie si ce login n'existe pas déjà dans la base
					$req='SELECT * FROM PERSONNE WHERE login_personne="'.$login_personne.'";';
					$resultat=$bdd->query($req);
			
					if($resultat->rowCount()>0)
					{
						$elements_incomplets.="<li>Le login que vous avez entré existe déjà</li>";
						$login_ok=false;
					}
					else
					{
						$login_ok=true;
					}	
				}
				if(empty($motdepasse))
				{
					$elements_incomplets.="<li>Entrez le mot de passe de la personne</li>";
				}
				if(empty($mdp_confirm))
				{
					$elements_incomplets.="<li>Confirmez le mot de passe de la personne</li>";
				}
				if($motdepasse !== $mdp_confirm)
				{
					$elements_incomplets.="<li>La confirmation du mot de passe est incorrecte</li>";
				}
				
				//si toutes les conditions sont réunies alors on peut créer l'utilisateur 
				if(!empty($nom) && !empty($prenom) && $mail_ok==true && $login_ok==true && !empty($motdepasse) && !empty($mdp_confirm) && $motdepasse === $mdp_confirm)
				{
										
					$req="INSERT INTO PERSONNE(id_personne,nom_personne,prenom_personne,mail_personne,login_personne,mdp_personne,administrateur,id_type_personne) 
						VALUES (default, \"".$nom."\", \"".$prenom."\", \"".$mail."\", \"".$login_personne."\", \"".md5($motdepasse)."\", true, 1);";
					
					$count=$bdd->exec($req);
						
					if($count==1)
					{
						$msg_reponse .= "<div class='success'>L'utilisateur a bien été créé.</div><br/>";
						$reponse->assign('frm_nvo_utilisateur', 'innerHTML', "");
					}
					else 
					{
						$msg_reponse .= "<div class='error'>L'utilisateur n'a pas pu être créé suite à une erreur.</div><br/>";
					}
				}
			}
			else if ($type_utilisateur==1) //si le type concerne un employé
			{
				if(empty($nom))
				{
					$elements_incomplets.="<li>Entrez le nom de la personne</li>";
				}
				if(empty($prenom))
				{
					$elements_incomplets.="<li>Entrez le prenom de la personne</li>";
				}
				if(empty($mail))
				{
					$elements_incomplets.="<li>Entrez le mail de la personne</li>";
					$mail_ok=false;
				}
				else
				{	//verifier si ça correspond à une adresse mail
					if(filter_var($mail, FILTER_VALIDATE_EMAIL))
					{
						//on vérifie si ce mail n'existe pas déjà dans la base
						$req='SELECT * FROM PERSONNE WHERE mail_personne="'.$mail.'";';
						$resultat=$bdd->query($req);
				
						if($resultat->rowCount()>0)
						{
							$elements_incomplets.="<li>Le mail que vous avez entré existe déjà</li>";
							$mail_ok=false;
						}
					}
					else
					{
						$elements_incomplets.="<li>L'adresse mail est incorrecte</li>";
						$mail_ok=false;
					}
				}
				
				//si toutes les conditions sont réunies alors on peut créer l'utilisateur 
				if(!empty($nom) && !empty($prenom) && $mail_ok==true)
				{
										
					$req="INSERT INTO PERSONNE(id_personne,nom_personne,prenom_personne,mail_personne,login_personne,mdp_personne,administrateur,id_type_personne) 
						VALUES (default, \"".$nom."\", \"".$prenom."\", \"".$mail."\", \"vide\", \"vide\", false, 1);";
					
					$count=$bdd->exec($req);
						
					if($count==1)
					{
						$msg_reponse .= "<div class='success'>L'utilisateur a bien été créé.</div><br/>";
						$reponse->assign('frm_nvo_utilisateur', 'innerHTML', "");
					}
					else 
					{
						$msg_reponse .= "<div class='error'>L'utilisateur n'a pas pu être créé suite à une erreur.</div><br/>";
					}
				}
			}
			else
			{
				$elements_incomplets.="<li>Le choix du type d'utilisateur est incorrecte</li>";
			}
		}
		else
		{
			$elements_incomplets.="<li>Choisissez le type de compte à créer</li>";
		}
		if(!empty($elements_incomplets))
		{
			$elements_incomplets="<div class='warning'><ul>".$elements_incomplets."</ul></div>";
		}
		$reponse->assign('msg', 'innerHTML', $elements_incomplets.$msg_reponse);
		return $reponse;
	}
	
	
	function changer_mdp($ancien_mdp,$nvo_mdp,$confirm_nvo_mdp)
	{
		require('conn_bdd.php');
		$reponse = new xajaxResponse();
		$elements_incomplets = "";
		$msg_reponse = "";
		$ancien_mdp_ok=true;
	
		if(empty($ancien_mdp))
		{
			$elements_incomplets.="<li>Entrez votre mot de passe actuel</li>";
			$ancien_mdp_ok=false;
		}
		else
		{	//nous allons vérifier ici que l'ancien mot de passe correspond 
			$req="SELECT * FROM PERSONNE WHERE id_personne=".$_SESSION['id']." AND mdp_personne=\"".md5($ancien_mdp)."\";";
			$resultat=$bdd->query($req);
			if($resultat->rowCount()!=1)
			{
				$elements_incomplets.="<li>Votre ancien mot de passe est incorrecte</li>";
				$ancien_mdp_ok=false;
			}
			
		}
		if(empty($nvo_mdp))
		{
			$elements_incomplets.="<li>Entrez votre nouveau mot de passe</li>";
		}
		if(empty($confirm_nvo_mdp))
		{
			$elements_incomplets.="<li>Confirmez votre nouveau mot de passe</li>";
		}
		if($nvo_mdp !== $confirm_nvo_mdp)
		{
			$elements_incomplets.="<li>La confirmation du nouveau mot de passe est incorrecte</li>";
		}
		
		if ($ancien_mdp_ok && !empty($nvo_mdp) && !empty($confirm_nvo_mdp) && $nvo_mdp === $confirm_nvo_mdp)
		{	//toutes les conditions sont remplies pour valider le changement de mot de passe 
			
			$req="UPDATE PERSONNE SET mdp_personne=\"".md5($nvo_mdp)."\" WHERE id_personne=".$_SESSION['id'].";";

			$count=$bdd->exec($req);

			if($count==1)
			{
				$msg_reponse .= "<div class='success'>Votre mot de passe a été changé avec succes.</div><br/>";
				$reponse->assign('frm_mdp', 'innerHTML', "");
			}
			else 
			{
				$msg_reponse .= "<div class='error'>Le mot de passe n'a pas pu être changé.</div><br/>";
			}
			
		}
		
		if(!empty($elements_incomplets))
		{
			$elements_incomplets="<div class='warning'><ul>".$elements_incomplets."</ul></div>";
		}
	
		$reponse->assign('msg', 'innerHTML', $elements_incomplets.$msg_reponse);
		return $reponse;
	}
	
	
	function deconnexion() 
	{
		$reponse = new xajaxResponse();
		session_destroy();
		

		$reponse->script("document.location.href='/'");
		return $reponse;
	}

	function modifier_licence($pers_concerne, $type_licence, $id_licence, $nom_licence, $date_debut)
	{
		$reponse = new xajaxResponse();
		$msg_rep="<ul>";
		if($type_licence==1)
		{//licence de duree
			if(empty($nom_licence))
			{
				$msg_rep.="<li>Saisissez le nom.</li>";
			}
			if(empty($date_debut))
			{
				$msg_rep.="<li>Saisissez la date de début de la licence.</li>";
			}
			if(!empty($nom_licence) && !empty($date_debut))
			{
				require('conn_bdd.php');

				$date_debut = implode('/', array_reverse(explode('/', $date_debut)));	

				$req="UPDATE LICENCE_DUREE SET nom_version=\"".$nom_licence."\", debut_licence=\"".$date_debut."\" WHERE id_licence_duree=".$id_licence.";";
				$resultat=$bdd->exec($req);
	
				if($resultat>0)
				{
					$reponse->assign('msg', 'innerHTML', "<div class='success'>La licence de durée n°$id_licence a bien été mise à jour</div>");
				}
				else
				{
					$reponse->assign('msg', 'innerHTML', "<div class='error'>La licence de durée n°$id_licence n'a pas pu être mise à jour</div>");
				}
				$reponse->script("$('#bloc_frm_modif').empty();setTimeout('xajax_resumer($pers_concerne);',1500);");
			}
		}
		else if($type_licence==2)
		{//licence de version
			if(empty($nom_licence))
			{
				$msg_rep.="<li>Saisissez le nom.</li>";
			}
			else
			{
				require('conn_bdd.php');
	
				$req="UPDATE LICENCE_VERSION SET nom_version=\"".$nom_licence."\" WHERE id_licence_version=".$id_licence.";";
				$resultat=$bdd->exec($req);
	
				if($resultat>0)
				{
					$reponse->assign('msg', 'innerHTML', "La licence de version n°$id_licence a bien été mise à jour");
				}
				else
				{
					$reponse->assign('msg', 'innerHTML', "La licence de version n°$id_licence n'a pas pu être mise à jour");
				}
				$reponse->script("$('#bloc_frm_modif').empty();setTimeout('xajax_resumer($pers_concerne);',1500);");
			}
		}
		else
		{
			$msg_rep.="Erreur";
		}
		
		$reponse->assign('info', 'innerHTML', $msg_rep.'</ul>');
		return $reponse;
	}

	function frm_modif_licence($type_licence, $id_licence) 
	{
		$reponse = new xajaxResponse();
		$msg_rep = "";
		$leBloc="<div id='popup'>";
		
		if($type_licence==1)
		{//licence de duree
			require('conn_bdd.php');

			$req="SELECT * FROM LICENCE_DUREE WHERE id_licence_duree=$id_licence";
			$resultat=$bdd->query($req);
			$ligne=$resultat->fetch(PDO::FETCH_OBJ);
			$pers_concerne=$ligne->id_personne;
			$date_debut = implode('/', array_reverse(explode('-', $ligne->debut_licence)));
			$leBloc.='LICENCE DE DUREE <br/> <div id="info"></div>
				<form id="frm_modif_licence" onSubmit="xajax_modifier_licence('.$pers_concerne.',1,'.$id_licence.',$(\'#nom_licence_duree\').val(),$(\'#date_debut_licence\').val()); return false;">
					<table>
						<tr>
							<td>N° de licence : </td>
							<td>'.$id_licence.'</td>
						</tr>
						<tr>
							<td>Nom : </td>
							<td><input type="text" id="nom_licence_duree" name="nom_licence_duree" value="'.$ligne->nom_version.'" /></td>
						</tr>
						<tr>
							<td>Date de début : </td>
							<td><input class="mCalendarFR" type="text" id="date_debut_licence" name="date_debut_licence" value="'.$date_debut.'" /></td>
						</tr>
						<tr>
							<td>Durée : </td>
							<td>'.$ligne->duree_licence.' an(s)</td>
						</tr>
						<tr>
							<td>N° matériel associé : </td>
							<td>'.$ligne->id_materiel.'</td>
						</tr>
						<tr>
							<td colspan="2"><input id="valider" name="valider" type="submit" value="Valider"></td>
						</tr>
					</table>
				</form>';
		}
		else if($type_licence==2)
		{//licence de version
			require('conn_bdd.php');

			$req="SELECT * FROM LICENCE_VERSION WHERE id_licence_version=$id_licence";
			$resultat=$bdd->query($req);
			$ligne=$resultat->fetch(PDO::FETCH_OBJ);

			$leBloc.='LICENCE DE VERSION <br/> <div id="info"></div>
				<form id="frm_modif_licence" onSubmit="xajax_modifier_licence('.$pers_concerne.',2,'.$id_licence.',$(\'#nom_licence_version\').val(),\'\'); return false;">
					<table>
						<tr>
							<td>N° de licence : </td>
							<td>'.$id_licence.'</td>
						</tr>
						<tr>
							<td>Nom : </td>
							<td><input type="text" id="nom_licence_version" name="nom_licence_version" value="'.$ligne->nom_version.'" /></td>
						</tr>
						<tr>
							<td>N° matériel associé : </td>
							<td>'.$ligne->id_materiel.'</td>
						</tr>
						<tr>
							<td colspan="2"><input id="valider" name="valider" type="submit" value="Valider"></td>
						</tr>
					</table>
				</form>';
		}
		else
		{
			$leBloc.="Impossible d'afficher le formulaire suite à une erreur inattendue.";
		}
		
		$leBloc.='<a onclick="$(\'#bloc_frm_modif\').empty();">Fermer</a></div>';
		$reponse->assign('bloc_frm_modif', 'innerHTML', $leBloc);
		$reponse->script("document.styleSheets[1].insertRule('.mCalandarMain { position:fixed; }', 0);mCalendar.init();");
		return $reponse;
	}

	function recuperation($mail) 
	{
		$reponse = new xajaxResponse();
		//il faut tout d'abord vérifier que l'email entré est correcte et existe dans la base de données
		$mail_ok=false;
		$elements_incomplets="";
		$msg_reponse="";

		if(filter_var($mail, FILTER_VALIDATE_EMAIL))
		{	//adresse mail correcte
			$mail_ok=true;
		}
		else
		{
			$elements_incomplets.="<li>L'adresse mail $mail est incorrecte</li>";
			$mail_ok=false;
		}

		if($mail_ok)
		{
			require('conn_bdd.php');
			$req="SELECT * FROM PERSONNE WHERE mail_personne='$mail' AND administrateur=1;";
			$resultat=$bdd->query($req);
			
			if($resultat->rowCount()>0)
			{//la personne avec l'email a été trouvée dans la base
				$lignes=$resultat->fetch(PDO::FETCH_OBJ);
				//on va générer un mot de passe : 	

				$nvo_mdp=genere_mdp();	
				$req="UPDATE PERSONNE SET mdp_personne=\"".md5($nvo_mdp)."\" WHERE mail_personne=\"".$mail."\" AND administrateur=1;";

				$count=$bdd->exec($req);

				if($count==1)
				{
					if(envoyer_mail($mail, $lignes->login_personne, $nvo_mdp))
					{
						$msg_reponse .= "<div class='success'>Un mail vient de vous être envoyé avec les nouveaux identifiants de connexion.</div><br/>";
						$reponse->assign('frm_recuperation', 'innerHTML', '');
					}
					else
					{
						$msg_reponse .= "<div class='error'>Une erreur innattendue s'est produite. Veuillez recommencer svp.</div><br/>";
					}
				}
				else 
				{
					$msg_reponse .= "<div class='error'>Une erreur innattendue s'est produite. Veuillez recommencer svp.</div><br/>";
				}		
			}
			else
			{
				$msg_reponse .= "<div class='error'>Le mail n'a pas été trouvé.</div><br/>";
			}
		}
		if(!empty($elements_incomplets))
		{
			$elements_incomplets="<div class='warning'><ul>".$elements_incomplets."</ul></div><br/>";
		}
		$reponse->assign('msg', 'innerHTML', $elements_incomplets.$msg_reponse);
		return $reponse;
	}

	function genere_mdp()
	{
		$chaine="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$nbChar=8;
		$tailleChaine=strlen($chaine);
		$resultat="";
		
		for($i=0; $i<$nbChar; $i++)
		{
			$nb=mt_rand(0,($tailleChaine-1));
			$resultat.=$chaine[$nb];
		}
		
		return $resultat;
	}

	function envoyer_mail($destinataire, $login, $mdp)
	{
		$objet = "Demande de nouveau mot de passe" ;
		$headers = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=UTF8\n";
		$headers .= "From: GSB <administration@gsb-informatique.hostzi.com>\n";
		$headers .= "Reply-To: avakov@hotmail.fr\n";

		$message = '
		<html>
		<head>
		<title>Votre nouveau mot de passe</title>
		</head>
		<body>
		<img src="http://gsb-informatique.hostzi.com/images/logo.png" border="0">
		<p>Suite à votre demande d\'un nouveau mot de passe nous vous envoyons vos nouveaux identifiants : </p>
		<table>
		<tr> 
		<td>Login : </td>
		<td>'.$login.'</td>
		</tr>
		<tr> 
		<td>Nouveau Mot de passe : </td>
		<td><b>'.$mdp.'</b></td>
		</tr>
		</table>
		<p>N\'oubliez pas que lorsque vous serez connectés vous pourrez modifier votre mot de passe sur votre page profil.</p>
		</body> 
		</html>
		';

		// On envoi l’email
		if ( mail($destinataire, $objet, $message, $headers) ) 
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
?>