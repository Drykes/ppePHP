<table id='tableauheader'>
	<tr>
		<td id="logo">
			<a href="./"><img src='./images/logo.png' border='0' /> </a>
		</td>
		<td id="menu">
			<h2>Application de gestion des prêts</h2>
			<?php if(isset($_SESSION['id'])) { ?>
				<ol>
					<li <?php if(!isset($_REQUEST['page']) || (isset($_REQUEST['page']) && $_REQUEST['page']==1)){echo "id='encours'";}else{} ?>><a href="./accueil.php">Accueil</a></li>
					<li <?php if(isset($_REQUEST['page']) && $_REQUEST['page']==2){echo "id='encours'";} ?>><a href="./saisie.php">Page de saisie</a></li>
					<li <?php if(isset($_REQUEST['page']) && $_REQUEST['page']==3){echo "id='encours'";} ?>><a href="./recap.php">Page récapitulative</a></li>
					<li <?php if(isset($_REQUEST['page']) && $_REQUEST['page']==4){echo "id='encours'";} ?>><a href="./administration.php">Administration</a></li>
					<li <?php if(isset($_REQUEST['page']) && $_REQUEST['page']==5){echo "id='encours'";} ?>><a href="./profil.php">Profil</a></li>
				</ol>
			<?php } ?>
		</td>
		<td id="infos">
			<?php if(isset($_SESSION['id'])) { echo "<br/>".$_SESSION['nom']." ".$_SESSION['prenom']; ?>
				<div id='lienDeco'><a onclick="xajax_deconnexion();">Déconnection</a></div>
			<?php } ?>
		</td>
	</tr>
</table>
