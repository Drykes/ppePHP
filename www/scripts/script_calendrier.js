/**
 * @author le_chomeur
 */

 
 /**
  * ##########################################
  * #	Script de calendrier non intrusif    #
  * ##########################################
  * 
  * - ajouter Ã  un Ã©lÃ©ment input de type text : class="mCalendar" ou si une classe est dÃ©ja prÃ©sente : 
  *   class="classeExistante mCalendar" 
  * - pour gÃ©rer les formats de date : FR/EN ,
  *   class="mCalendarFR" ou class="mCalendarEN"
  *   la date en franÃ§ais Ã©tant celle par dÃ©faut
  */
 
 var mCalendar = (function() {
 	
	/**
	 * Variables et mÃ©thodes privÃ©es
	 */
	
	/* Auteur de cette mÃ©thode : SpaceFrog */
	var getElementsByReg=function(tag,attr,reg,mod,val){
			var tabReg=new Array();
			var tabElts=document.body.getElementsByTagName(tag);
			var TEL=tabElts.length;
			if(!(reg instanceof RegExp)){
			   if(reg.indexOf("*")>-1){
				  		reg=reg.replace(/\*/g,'.+');
					  		reg=new RegExp(reg);
					  		}
					else {return	tabReg;
						   }	
					}
			i=0;
			while(tabElts[i]){
				if(tabElts[i][attr]){
			         if(reg.test(tabElts[i][attr])){
			         tabReg.push(tabElts[i]);}
			         }
			         reg.test("");
			i++;         
			}
			return tabReg;
		};
	var w = new Array();
	var anneeListe = null;
	var calculPasEffect = function(position,x){
		c = (100 / 50);
		d = Math.round(Math.abs(position) / c);
		w = new Array();
		for (var i = 1; i <= d; i++) {
			w.push(easeInOutSine(i * c, x.startX, - x.startX, 100))
		}
	}
	var addEvent = function(func,onEvent,elementDom) { 
		    if (window.addEventListener) { 
				elementDom.addEventListener(onEvent, func, false); 
			} else if (document.addEventListener) { 
				elementDom.addEventListener(onEvent, func, false); 
			} else if (window.attachEvent) { 
				elementDom.attachEvent("on"+onEvent, func); 
		    } 
	};
	var destructCalendrier = function(e){
		var source=window.event?window.event.srcElement:e.target;
		if(source != mCalendar.inputCurrent){
			var CurrentNode = source ;
			var detruit = true;
			while(CurrentNode.parentNode){
				if(CurrentNode.className == "mCalandarMain"){
					detruit = false;
					break;
				}
				CurrentNode = CurrentNode.parentNode;
			}
			if(detruit && mCalendar.Calendar != null){
				cleanNode(mCalendar.Calendar);
				document.body.removeChild(mCalendar.CalendarDiv);
				mCalendar.Calendar = null;
			}
		}
	};
	var cleanNode = function (CurrentNode){
		
		//RÃ©cupÃ©ration de tous les noeuds enfants 
		while (CurrentNode.childNodes.length>0) {
			//Si le premier enfant a des enfants appel rÃ©cursif de la mÃ©thode
			if(CurrentNode.firstChild.childNodes.length>0){
				cleanNode(CurrentNode.firstChild);
			}
			//Sinon on parcours ses propriÃ©tÃ©s pour supprimer les Ã©vÃ¨nements liÃ© aux objet, puis destruction de l'objet
			else{
				
				var tempo = CurrentNode.firstChild ;
				var a = tempo.attributes, i, l, n;
			    if (a) {
			        l = a.length;
			        for (i = 0; i < l; i += 1) {
			            n = a[i].name;
			            if (typeof tempo[n] === 'function') {
			                tempo[n] = null;
			            }
			        }
			    }
				tempo = null;
				CurrentNode.removeChild(CurrentNode.firstChild);
			}
		}
	};
	
	
	//Fonction permettant de trouver la position de l'Ã©lÃ©ment ( input ) pour pouvoir positioner le calendrier
	var getPosition = function() {
		var tmpLeft = mCalendar.inputCurrent.offsetLeft;
		var tmpTop = mCalendar.inputCurrent.offsetTop;
		var MyParent = mCalendar.inputCurrent.offsetParent;
		while(MyParent) {
			tmpLeft += MyParent.offsetLeft;
			tmpTop += MyParent.offsetTop;
			MyParent = MyParent.offsetParent;
		}
			mCalendar.Calendar.style.left = tmpLeft + "px";
			mCalendar.Calendar.style.top = tmpTop +  mCalendar.inputCurrent.offsetHeight + 2 +"px";
	};
	
	
	/**
	 * MÃ©thode permetant de tester la date :
	 * - Format FR/EN
	 * - ValiditÃ©
	 * - AnnÃ©e bisextile
	 * Si tout est ok affectation des valeurs du champs sinon affectation de la date courante
	 */
	
	var updateDate = function(){
			var finalDate = new Date();
			finalDate.setDate(mCalendar.jour);
			finalDate.setMonth(mCalendar.mois-1);
			finalDate.setFullYear(mCalendar.annee);
			
			mCalendar.currentDate = finalDate;
	}
	
	var getCurrentDate = function(){
			var reg=new RegExp(mCalendar.separateur, "g");
			var dateOfField = mCalendar.inputCurrent.value;
			var dateExplode = dateOfField.split(reg);
			//RÃ©cupÃ©ration du mois / jour annÃ©e
			mCalendar.annee = parseInt(dateExplode[2]);
			mCalendar.jour = (mCalendar.formatDate == "FR") ? parseInt(dateExplode[0]):parseInt(dateExplode[1]);
			mCalendar.mois =  (mCalendar.formatDate == "FR") ? parseInt(dateExplode[1]):parseInt(dateExplode[0]);
			
			var pattern = (mCalendar.separateur == "/") ? /^(\d{1,2}\/){2}\d{4}$/:/^(\d{1,2}\-){2}\d{4}$/ ;
			//Si le format de la date n'est pas bonne on rÃ©cupÃ¨re la date courante
			if (!dateOfField.match(pattern) && dateOfField != ''){
				alert("Format de date incorrect");
			}
			
			//On crÃ©er une nouvelle date avec les paramÃ¨tres d'entrÃ©e pour tester sa validitÃ©
			testDate = new Date();
			testDate.setDate(mCalendar.jour);
			testDate.setMonth(mCalendar.mois-1);
			testDate.setFullYear(mCalendar.annee);
			
			//Et on les test, javascript corigeant automatiquement les erreurs de date (cf la doc ;-) )
			if ((testDate.getFullYear()!= mCalendar.annee ) || (testDate.getMonth()!=(mCalendar.mois-1)) || (testDate.getDate()!=mCalendar.jour)) {
				var dateCourante = new Date();
				mCalendar.annee = dateCourante.getFullYear();
				mCalendar.jour = dateCourante.getDate();
				mCalendar.mois =  dateCourante.getMonth()+1;
			}
			
			updateDate();
	};
	
	var miseAjourHeader = function(){
		
		cleanNode(mCalendar.headerUl);
		
		var nomDuJour =  mCalendar.joursNom[((mCalendar.currentDate.getDay()-1) == -1) ? 6 :(mCalendar.currentDate.getDay()-1)];
		var newtext = document.createTextNode(nomDuJour);
		mCalendar.jourNameLi.appendChild(newtext);
		newtext = document.createTextNode(mCalendar.jour);
		mCalendar.jourLi.appendChild(newtext);
		
		newtext = document.createTextNode(mCalendar.moisNom[(mCalendar.mois == 13) ? 0:mCalendar.mois-1]);
		mCalendar.moisLi.appendChild(newtext);
		
		newtext = document.createTextNode(mCalendar.annee);
		mCalendar.anneeLi.appendChild(newtext);
		
		mCalendar.headerUl.appendChild(mCalendar.jourNameLi);
		mCalendar.headerUl.appendChild(mCalendar.jourLi);

		mCalendar.headerUl.appendChild(mCalendar.moisLi);
		mCalendar.headerUl.appendChild(mCalendar.anneeLi);
		
		//Remplissage de la liste des mois
		for(var i = 0 , l = mCalendar.moisNom.length ; i <l ; i++){
			var liTemp = document.createElement('li');
			newtext = document.createTextNode(mCalendar.moisNom[i]);
			liTemp.appendChild(newtext);
			mCalendar.moisUl.appendChild(liTemp);
			//Pour chaque objet on ajoute le click
			addEvent(function(liTemp){return function(e){
						if (!e) var e = window.event;
						pickDate(liTemp);
						e.cancelBubble = true;
					}}(liTemp)
			,"click",liTemp);

		}
		
		//CrÃ©ation du bouton up annÃ©e
		liTemp = document.createElement('li');
		var up = document.createElement('a');
		up.className = "upAnnee";
		up.style.cursor = "pointer";
		addEvent(function(){
					refreshYear(-1);
				}
				,"click",up);
		liTemp.appendChild(up);
		mCalendar.anneeUl.appendChild(liTemp);
		for(var i = 0 ; i < 10 ; i++){
			liTemp = document.createElement('li');
			newtext = document.createTextNode(anneeListe+i);
			liTemp.appendChild(newtext);
			mCalendar.anneeUl.appendChild(liTemp);
			//Pour chaque objet on ajoute le le click
			addEvent(function(liTemp){return function(e){
						if (!e) var e = window.event;
						pickDate(liTemp);
						e.cancelBubble = true;
					}}(liTemp)
					,"click",liTemp);
		}
		anneeListe = parseInt(liTemp.innerHTML)+1;
		liTemp = document.createElement('li');
		var down = document.createElement('a');
		down.className = "downAnnee";
		down.style.cursor = "pointer";
		addEvent(function(){
			refreshYear(1);
		}
		,"click",down);

		liTemp.appendChild(down);
		mCalendar.anneeUl.appendChild(liTemp);
		mCalendar.anneeDiv.appendChild(mCalendar.anneeUl);
		//ajout de la liste de mois et d'annÃ©e dans leur conteneur
		mCalendar.moisLi.appendChild(mCalendar.moisUl);
		mCalendar.anneeLi.appendChild(mCalendar.anneeDiv);
		
	};
	
	var assemblyHeader = function(){
		var nomDuJour =  mCalendar.joursNom[((mCalendar.currentDate.getDay()-1) == -1) ? 6 :(mCalendar.currentDate.getDay()-1)];
		var newtext = document.createTextNode(nomDuJour);
		mCalendar.jourNameLi.appendChild(newtext);
		
		newtext = document.createTextNode(mCalendar.jour);
		mCalendar.jourLi.appendChild(newtext);
		
		newtext = document.createTextNode(mCalendar.moisNom[(mCalendar.mois == 12) ? 0:mCalendar.mois-1]);
		mCalendar.moisLi.appendChild(newtext);
		newtext = document.createTextNode(mCalendar.annee);
		mCalendar.anneeLi.appendChild(newtext);
		
		mCalendar.headerUl.appendChild(mCalendar.jourNameLi);
		mCalendar.headerUl.appendChild(mCalendar.jourLi);
		mCalendar.headerUl.appendChild(mCalendar.moisLi);
		mCalendar.headerUl.appendChild(mCalendar.anneeLi);
		
		//Remplissage de la liste des mois
		for(var i = 0 , l = mCalendar.moisNom.length ; i <l ; i++){
			var liTemp = document.createElement('li');
			newtext = document.createTextNode(mCalendar.moisNom[i]);
			liTemp.appendChild(newtext);
			mCalendar.moisUl.appendChild(liTemp);
			addEvent(function(liTemp){return function(e){
						if (!e) var e = window.event;
						pickDate(liTemp);
						e.cancelBubble = true;
					}}(liTemp)
			,"click",liTemp);
		}
		
		
		//Remplissage de la liste des annÃ©e ( 11 annÃ©e de plus par dÃ©faut
		//CrÃ©ation du bouton up annÃ©e
		liTemp = document.createElement('li');
		var up = document.createElement('a');
		up.className = "upAnnee";
		up.style.cursor = "pointer";
		addEvent(function(){
					refreshYear(-1);
				}
				,"click",up);
		liTemp.appendChild(up);
		mCalendar.anneeUl.appendChild(liTemp);
		for(var i = 0 ; i < 10 ; i++){
			liTemp = document.createElement('li');
			newtext = document.createTextNode(mCalendar.annee+i);
			liTemp.appendChild(newtext);
			mCalendar.anneeUl.appendChild(liTemp);
			addEvent(function(liTemp){return function(e){
						if (!e) var e = window.event;
						pickDate(liTemp);
						e.cancelBubble = true;
					}}(liTemp)
			,"click",liTemp);
		}
		anneeListe = parseInt(liTemp.innerHTML)+1;
		liTemp = document.createElement('li');
		var down = document.createElement('a');
		down.className = "downAnnee";
		down.style.cursor = "pointer";
		addEvent(function(){
			refreshYear(1);
		}
		,"click",down);

		liTemp.appendChild(down);
		mCalendar.anneeUl.appendChild(liTemp);
		
		//ajout de la liste de mois et d'annÃ©e dans leur conteneur
		mCalendar.anneeDiv.appendChild(mCalendar.anneeUl)
		mCalendar.moisLi.appendChild(mCalendar.moisUl);
		mCalendar.anneeLi.appendChild(mCalendar.anneeDiv);
		
		//CrÃ©ation des deux boutons mois suivant/prÃ©cÃ©dent
		newtext = document.createTextNode("bef");
		/*** mCalendar.moisBefA.appendChild(newtext); ***/
		mCalendar.moisBefA.style.cursor = "pointer";
		//Ajout de l'Ã©vÃ¨nement Click
		addEvent(function(){
					mCalendar.changeMois(-1);
				}
				,"click",mCalendar.moisBefA);
		
		newtext = document.createTextNode("next");
		/*** mCalendar.moisNextA.appendChild(newtext); ***/
		mCalendar.moisNextA.style.cursor = "pointer";
		//Ajout de l'Ã©vÃ¨nement Click
		addEvent(function(){
					mCalendar.changeMois(1);
				}
				,"click",mCalendar.moisNextA);
		
		
		//On ajoute le tout dans la div header
		mCalendar.headerDiv.appendChild(mCalendar.moisBefA);
		mCalendar.headerDiv.appendChild(mCalendar.headerUl);
		mCalendar.headerDiv.appendChild(mCalendar.moisNextA);
		
	};
	
	var buildCorp = function(){
		//Calcul du nombre de jours pour le mois de fÃ©vrier 
		var nbJoursfevrier = anneeBisextile(mCalendar.annee) ? 29 : 28;
		//Initialisation du tableau indiquant le nombre de jours par mois
		var nombreDeJours = new Array(31,nbJoursfevrier,31,30,31,30,31,31,30,31,30,31);
			
		
		//jour de la semaine pour remplir les jours du mois prÃ©cÃ©dent
		var premierDuMois = new Date(mCalendar.annee, mCalendar.mois-1, 1);
		var dernierDuMois = new Date(mCalendar.annee, (mCalendar.mois-1), nombreDeJours[mCalendar.mois-1]);
		var nbjourmoisprecedent = (( premierDuMois.getDay()== 0 ) ? 6 : premierDuMois.getDay() - 1);
		var nbjourmoissuivant = (( dernierDuMois.getDay()== 0 ) ? 7 : dernierDuMois.getDay());
		
		
		
		var nombreJoursMoisCourant = (nombreDeJours[(((mCalendar.mois-1) == 0) ? 11:mCalendar.mois-1)]);
		var nombreJoursMoisPrecedent = (nombreDeJours[(((mCalendar.mois-2) < 0) ? (11-2): ((mCalendar.mois-2) == 0) ? 11 : mCalendar.mois-2)]);
		var nombreJoursMoisSuivant = 7 - nbjourmoissuivant;
		
		var nombreJourAddMini = (nombreJoursMoisCourant - nbjourmoisprecedent);
		
		//On boucle pour ajouter les jours prÃ©cÃ©dent
		for(var i = nombreJourAddMini , l =  nombreJourAddMini + nbjourmoisprecedent ; i <l ; i++ ){
			var liTemp = document.createElement('li');
			newtext = document.createTextNode(i);
			liTemp.appendChild(newtext);
			mCalendar.corpUl.appendChild(liTemp);
			liTemp.className ="dateI";
			
		}
		//On boucle pour ajouter les jours en cours
		for(var i = 1 ; i <nombreJoursMoisCourant+1 ; i++ ){
			var liTemp = document.createElement('li');
			newtext = document.createTextNode(i);
			liTemp.appendChild(newtext);
			mCalendar.corpUl.appendChild(liTemp);
			if(i == mCalendar.jour){
				liTemp.className ="dateC";
			}
			
			//Pour chaque objet on ajoute le rollOver + le click
			addEvent(function(liTemp){return function(e){
						liTemp.oldClassName = liTemp.className; 
						liTemp.className = "mOver";
					}}(liTemp)
					,"mouseover",liTemp);
			addEvent(function(liTemp){return function(e){
						liTemp.className = liTemp.oldClassName;
					}}(liTemp)
					,"mouseout",liTemp);
			addEvent(function(liTemp){return function(e){
						if (!e) var e = window.event;
						pickDate(liTemp);
						e.cancelBubble = true;
					}}(liTemp)
			,"click",liTemp);					
					
			
		}
		
		//On boucle pour ajouter les jours pour le mois suivant
		for(var i = 1 ; i <nombreJoursMoisSuivant+1 ; i++ ){
			var liTemp = document.createElement('li');
			newtext = document.createTextNode(i);
			liTemp.appendChild(newtext);
			mCalendar.corpUl.appendChild(liTemp);
			liTemp.className ="dateI";
			
		}
		
		
		for(var i = 0, l = mCalendar.joursNomCourt.length; i < l ; i++ ){
			var liTemp = document.createElement('li');
			newtext = document.createTextNode(mCalendar.joursNomCourt[i]);
			liTemp.appendChild(newtext);
			mCalendar.listeJoursUl.appendChild(liTemp);
		}
		
		mCalendar.corpDiv.appendChild(mCalendar.listeJoursUl);
		mCalendar.corpDiv.appendChild(mCalendar.corpUl);
	};
	
	var assemblyCorpHead = function(){
		//CrÃ©ation d'une iframe pour passer au dessus des Ã©lÃ©ments de type select sous ie
		var frameBackGround = document.createElement('iframe');
		mCalendar.CalendarDiv = (mCalendar.CalendarDiv == null) ? document.createElement('div'):mCalendar.CalendarDiv;
		mCalendar.CalendarDiv.appendChild(frameBackGround);
		mCalendar.CalendarDiv.appendChild(mCalendar.headerDiv);
		mCalendar.CalendarDiv.appendChild(mCalendar.corpDiv);
		
		//Affectation des styles
		mCalendar.CalendarDiv.className = "mCalandarMain";
		mCalendar.headerDiv.className = "mCalandarheader";
		mCalendar.headerUl.className ="mInfo";
		mCalendar.moisBefA.className = "bef";
		mCalendar.moisNextA.className = "next";
		
		mCalendar.moisUl.className = "mois";
		mCalendar.anneeDiv.className = "annee";
		mCalendar.corpDiv.className = "corpDiv";
		mCalendar.corpUl.className = "corpUl";
		mCalendar.listeJoursUl.className = "listeJoursUl";
		
		
		document.body.appendChild(mCalendar.CalendarDiv);
		mCalendar.Calendar = mCalendar.CalendarDiv;
		
		
	};
	
	var buildMois = function(valeur){
		//CrÃ©ation d'un nouveau mois
		var newUlMois = document.createElement('ul');
		
		//Calcul du nombre de jours pour le mois de fÃ©vrier 
		var nbJoursfevrier = anneeBisextile(mCalendar.annee) ? 29 : 28;
		//Initialisation du tableau indiquant le nombre de jours par mois
		var nombreDeJours = new Array(31,nbJoursfevrier,31,30,31,30,31,31,30,31,30,31);
			
		
		//jour de la semaine pour remplir les jours du mois prÃ©cÃ©dent
		var premierDuMois = new Date(mCalendar.annee, mCalendar.mois-1, 1);
		var dernierDuMois = new Date(mCalendar.annee, (mCalendar.mois-1), nombreDeJours[mCalendar.mois-1]);
		var nbjourmoisprecedent = (( premierDuMois.getDay()== 0 ) ? 6 : premierDuMois.getDay() - 1);
		var nbjourmoissuivant = (( dernierDuMois.getDay()== 0 ) ? 7 : dernierDuMois.getDay());
		
		
		
		var nombreJoursMoisCourant = (nombreDeJours[(((mCalendar.mois-1) == 0) ? 11:mCalendar.mois-1)]);
		var nombreJoursMoisPrecedent = (nombreDeJours[(((mCalendar.mois-2) < 0) ? (11-2): ((mCalendar.mois-2) == 0) ? 11 : mCalendar.mois-2)]);
		var nombreJoursMoisSuivant = 7 - nbjourmoissuivant;
		
		var nombreJourAddMini = (nombreJoursMoisCourant - nbjourmoisprecedent);
		
		//On boucle pour ajouter les jours prÃ©cÃ©dent
		for(var i = nombreJourAddMini , l =  nombreJourAddMini + nbjourmoisprecedent ; i <l ; i++ ){
			var liTemp = document.createElement('li');
			newtext = document.createTextNode(i);
			liTemp.appendChild(newtext);
			newUlMois.appendChild(liTemp);
			liTemp.className ="dateI";
			
		}
		//On boucle pour ajouter les jours en cours
		for(var i = 1 ; i <nombreJoursMoisCourant+1 ; i++ ){
			liTemp = document.createElement('li');
			newtext = document.createTextNode(i);
			liTemp.appendChild(newtext);
			newUlMois.appendChild(liTemp);
			if(i == mCalendar.jour){
				liTemp.className ="dateC";
			}
			
			//Pour chaque objet on ajoute le rollOver + le click
			addEvent(function(liTemp){return function(e){
						liTemp.oldClassName = liTemp.className; 
						liTemp.className = "mOver";
					}}(liTemp)
					,"mouseover",liTemp);
			addEvent(function(liTemp){return function(e){
						liTemp.className = liTemp.oldClassName;
					}}(liTemp)
					,"mouseout",liTemp);
			addEvent(function(liTemp){return function(e){
						if (!e) var e = window.event;
						pickDate(liTemp);
						e.cancelBubble = true;
					}}(liTemp)
			,"click",liTemp);						
		}
		
		//On boucle pour ajouter les jours pour le mois suivant
		for(var i = 1 ; i <nombreJoursMoisSuivant+1 ; i++ ){
			liTemp = document.createElement('li');
			newtext = document.createTextNode(i);
			liTemp.appendChild(newtext);
			newUlMois.appendChild(liTemp);
			liTemp.className ="dateI";
			
		}
		//Une fois le nouveau mois construit on le place avant ou aprÃ¨s pour gÃ©rer l'effet d'apparition
		
		newUlMois.className = "corpUl";
		newUlMois.style.position = "absolute";
		newUlMois.style.left = (-valeur * parseInt(mCalendar.corpDiv.offsetWidth))+"px";
		
		mCalendar.corpDiv.appendChild(newUlMois);
		
		var position = parseInt(mCalendar.corpDiv.offsetWidth) * valeur;
		
		//On calcul l'animation 
		var x = newUlMois;
		x.timer = null;
		x.startX = parseInt(position);
		calculPasEffect(position,x);
		x.w = w ;
		x.currentStepX = 0 ;
		x.currentStepY = 0 ; 
		//exÃ©cute ^^
		go(x,c,false);
		//Mise a jour de la date
		anneeListe =  anneeListe - 10 ; // pour combler un bug de mise a jour
		miseAjourHeader();
		
	};
	
	var refreshYear = function(valeur){
		if (!mCalendar.timerActif) {
			mCalendar.timerActif = true;
			anneeListe = (valeur < 0) ? anneeListe - 20 : anneeListe;
			//Remplissage de la liste des annÃ©e ( 11 annÃ©e de plus par dÃ©faut
			var ulYearTemp = document.createElement('ul');
			//CrÃ©ation du bouton up annÃ©e
			liTemp = document.createElement('li');
			var up = document.createElement('a');
			up.className = "upAnnee";
			up.style.cursor = "pointer";
			addEvent(function(){
				refreshYear(-1);
			}, "click", up);
			liTemp.appendChild(up);
			ulYearTemp.appendChild(liTemp);
			for (var i = 0; i < 10; i++) {
				liTemp = document.createElement('li');
				newtext = document.createTextNode(anneeListe + i);
				liTemp.appendChild(newtext);
				ulYearTemp.appendChild(liTemp);
				addEvent(function(liTemp){return function(e){
						if (!e) var e = window.event;
						pickDate(liTemp);
						e.cancelBubble = true;
					}}(liTemp)
				,"click",liTemp);	
				
			}
			anneeListe = parseInt(liTemp.innerHTML) + 1;
			liTemp = document.createElement('li');
			var down = document.createElement('a');
			down.className = "downAnnee";
			down.style.cursor = "pointer";
			addEvent(function(){
				refreshYear(1);
			}, "click", down);
			liTemp.appendChild(down);
			ulYearTemp.appendChild(liTemp);
			
			//ajout de la liste de mois et d'annÃ©e dans leur conteneur
			mCalendar.anneeDiv.appendChild(ulYearTemp);
			
			//ulYearTemp.className = "corpUl";
			ulYearTemp.style.position = "absolute";
			ulYearTemp.style.top = (valeur * parseInt(mCalendar.anneeDiv.offsetHeight)) + "px";
			
			var position = parseInt(mCalendar.anneeDiv.offsetHeight) * valeur;
			
			
			//On calcul l'animation 
			var x = ulYearTemp;
			x.timer = null;
			x.startX = parseInt(position);
			w = new Array();
			calculPasEffect(position, x);
			x.w = w;
			x.currentStepX = 0;
			x.currentStepY = 0;
			//exÃ©cute ^^
			go2(x, c, false);
		}
	};
	var pickDate = function(elementClick){
		//rÃ©cupÃ©ration du format ( FR/EN ) du sÃ©parateur et des informations mois / annÃ©e
		//On commence par vÃ©rifier de quel Ã©lÃ©ment il s'agit
		
		//S'il s'agit d'un jour
		if(elementClick.className == "mOver"){
			mCalendar.jour = parseInt(elementClick.innerHTML); 	
			//On insÃ¨re la date selon le format et le sÃ©parateur et on dÃ©truit le calendrier
			mCalendar.inputCurrent.value = (mCalendar.formatDate == "FR") ? mCalendar.jour+mCalendar.separateur+mCalendar.mois+mCalendar.separateur+mCalendar.annee : mCalendar.mois+mCalendar.separateur+mCalendar.jour+mCalendar.separateur+mCalendar.annee;
			cleanNode(mCalendar.Calendar);
			document.body.removeChild(mCalendar.Calendar);
			mCalendar.Calendar = null;
		}
		else if(!isNaN(parseInt(elementClick.innerHTML))){
			mCalendar.annee = parseInt(elementClick.innerHTML);
			//Mise a jour du header+corp
			buildMois(+1);
		}
		else{
			var i=0;
			while(mCalendar.moisNom[i]){
				if(elementClick.innerHTML == mCalendar.moisNom[i]){
					break;
				}
				i++;
			}
			mCalendar.mois = i+1;
			//Mise a jour du header+corp
			buildMois(+1);
		}
	};

	var anneeBisextile = function(annee) {
 			return (((annee & 3) == 0) && ((annee % 100 != 0) || (annee % 400 == 0)));
	};
	
	var easeInOutSine = function (t, b, c, d) {
				return - c / 2 * (Math.cos(Math.PI * t / d) - 1) + b
	};
	
	var easeOutCubic = function (t, b, c, d) {
			 				return c*((t=t/d-1)*t*t + 1) + b;
	};

	var go = function(b, c, out) {
		if(b.timer != null){
			window.clearInterval(b.timer);
			b.timer = null;
		}
		b.timer = window.setInterval(function() {
		var a = true;
		if (b.w[b.currentStepX]) {
			var avance = (b.w[b.currentStepX] > 0) ? -1:1; 
			b.style['left'] = b.w[b.currentStepX] + 'px';
			mCalendar.corpUl.style['left'] = b.w[b.currentStepX] + (avance * parseInt(mCalendar.corpUl.offsetWidth)) + 'px';
		if(!out){
			b.currentStepX++;
		}
		else{
			b.currentStepX--;
		}
		a = false
		}
		if (a) {
			window.clearInterval(b.timer);
			b.timer = null;
			mCalendar.corpUl = b;
			mCalendar.timerActif = false;
			return
		}
		}, c);
	};

	var go2 = function(b, c, out) {
		if(b.timer != null){
			window.clearInterval(b.timer);
			b.timer = null;
		}
		b.timer = window.setInterval(function() {
		var a = true;
		if (b.w[b.currentStepY]) {
			var avance = (b.w[b.currentStepY] > 0) ? -1:1; 
			b.style['top'] = b.w[b.currentStepY] + 'px';
			mCalendar.anneeUl.style['top'] = b.w[b.currentStepY] + (avance * parseInt(mCalendar.anneeUl.offsetHeight)) + 'px';
		if(!out){
			b.currentStepY++;
		}
		else{
			b.currentStepY--;
		}
		a = false
		}
		if (a) {
			window.clearInterval(b.timer);
			b.timer = null;
			mCalendar.anneeUl = b;
			mCalendar.timerActif = false;
			return
		}
		}, c);
	};
	var go3 = function(b, c, out) {
		if(b.timer != null){
			window.clearInterval(b.timer);
			b.timer = null;
		}
		b.timer = window.setInterval(function() {
		var a = true;
		if (b.y[b.currentStepY] || b.w[b.currentStepX]) {
			b.style['height'] = b.y[b.currentStepY] + 'px';
			b.style['width'] = b.w[b.currentStepX] + 'px';
		if(!out){
			b.currentStepY++;
			b.currentStepX++;
		}
		a = false
		}
		if (a) {
			window.clearInterval(b.timer);
			b.timer = null;
			mCalendar.anneeUl = b;
			mCalendar.timerActif = false;
			return
		}
		}, c);
	};
				
	/**
	 * Variables et mÃ©thodes publiques
	 */
	return {
		"Calendar" : null,		//L'objet calendrier
		"currentDate" : null,	//Date courante complÃ¨te
		"jour" : null,			
		"mois" : null,
		"annee" : null,
		"CalendarDiv" : document.createElement('div'),	//Le calendrier HTML global
		"jourNameLi" : document.createElement('li'),	//Les objets HTML contenant les informations du header
		"jourLi" : document.createElement('li'),
		"moisLi" : document.createElement('li'),
		"anneeLi" : document.createElement('li'),
		"anneeDiv" : document.createElement('div'),
		"moisUl" : document.createElement('ul'),
		"anneeUl": document.createElement('ul'),
		"moisBefA": document.createElement('a'),
		"moisNextA": document.createElement('a'),
		"headerDiv" : document.createElement('div'),
		"headerUl" : document.createElement('ul'),
		"corpUl" : document.createElement('ul'),		//Les objets HTML contenant les jours
		"corpDiv" : document.createElement('div'),
		"listeJoursUl" : document.createElement('ul'),
		
		"joursNom" : new Array('Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'),
		"joursNomCourt" : new Array('Lun','Mar','Mer','Jeu','Ven','Sam','Dim'),
		"moisNom" : new Array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'),
		
		"formatDate" : "FR",	//Format de la date, anglais ou franÃ§ais
		"inputCurrent" : null,	//Champs sÃ©lectionnÃ©
		"separateur" : "/",		//sÃ©parateur par dÃ©faut
		"timerActif" : false,	//Time permettant de bloquer l'animation en cours
		"newC" : false ,		//Permet de savoir s'il s'agit d'un nouveau calendrier
		
		"init" : function() { 
			//RÃ©cupÃ©ration de tous les Ã©lÃ©ments aillant dans leur className "mCalendar"
			var listeChamps = getElementsByReg('input','className',/mCalendar/);
			//Affectation de l'Ã©vÃ¨nenement onclick sur le champs pour afficher la mCalendar
			for(var i = 0 , l = listeChamps.length; i <l ; i++ ){
				addEvent(function(elemDom){return function(e){
						var source=window.event?window.event.srcElement:e.target;
						if (mCalendar.Calendar == null || source != mCalendar.inputCurrent) {
							//destruction de l'ancien
							destructCalendrier(e)
							mCalendar.buildCalendar(elemDom);
							mCalendar.newC = true ;
						}
				}}(listeChamps[i])
				,"click",listeChamps[i]);
			}
			//On lance le scan sur le body pour voir si le calendrier existe
			addEvent(function(e){
					if(mCalendar.Calendar != null){
						if(mCalendar.newC){
							destructCalendrier(e);
						}
					}
				}
				,"click",document);
			
	  	},
		"addLoadListener" : function(func) { 
		    if (window.addEventListener) { 
				window.addEventListener("load", func, false); 
			} else if (document.addEventListener) { 
				document.addEventListener("load", func, false); 
			} else if (window.attachEvent) { 
				window.attachEvent("onload", func); 
		    } 
	  	},
		
		"buildCalendar" : function(inputCurrent){
			if(mCalendar.Calendar == null){

				mCalendar.inputCurrent = inputCurrent;
				//RÃ©cupÃ©ration du format de la date en fonction de la classe
				mCalendar.formatDate = (inputCurrent.className == "mCalendarEN") ? "EN":"FR";
				//RÃ©cupÃ©ration du sÃ©parateur
				var testSeparateur = new RegExp("\-$","gi");
				mCalendar.separateur = (testSeparateur.test(inputCurrent.value)) ? "-":"/";
	
				//RÃ©cupÃ©ration soit de la date du champs, soit de la date du jour si la date n'est pas valide ou vide
				//ET affectation des jours annÃ©es mois
				getCurrentDate();
				
				/*** alert(mCalendar.jour + " "+ mCalendar.mois+ " "+ mCalendar.annee) ***/;
				
				//Construction de l'entÃ¨te contenant les Ã©lÃ©ments et les listes
				assemblyHeader();
				
				//Construction du corp avec les jours + sÃ©lection du jour
				buildCorp();
				
				//Construction du header + corp dans une div+iframe
				assemblyCorpHead();
				
				//On positionne le calendrier
				getPosition();
			}
			
			//Apparition du calendrier
		},
		
		"changeMois" : function(valeur){
			if(!mCalendar.timerActif){
				mCalendar.timerActif = true;
				//Gestion des annÃ©es en fonction du mois
				if(mCalendar.mois == 12 && valeur == 1){
						mCalendar.annee +=1;
						mCalendar.mois = 1;
						
				}
				else if(mCalendar.mois == 1 && valeur == -1){
					mCalendar.annee -=1;
					mCalendar.mois = 12;
				}
				else{
					mCalendar.mois += valeur;
				}
				//Affectation de la nouvelle date
				updateDate();
				//On prÃ©pare le mois Ã  afficher en fonction des informations
				buildMois(valeur);
			}
		}
	}
	
 	})();

	mCalendar.init();