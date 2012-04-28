
var serviceUrl = "http://www.shoutcast.com/embed_module";
var embedCSSUrl = "http://www.shoutcast.com/css/embed_stylesheet.css";
var shoutcast_player = "http://www.shoutcast.com/shoutcast_player";
var domain = "http://www.shoutcast.com"
var bObj;
var resultsPerPage;
var stationNameLimit = 30;
var stationNPLimit = 25;
var cssDownloadStatus = 0;
var domainChk = 0;
function JSONscriptRequest(fullUrl) {
    // REST request path
    this.fullUrl = fullUrl; 
    // Keep IE from caching requests
    this.noCacheIE = '&noCacheIE=' + (new Date()).getTime();
    // Get the DOM location to put the script tag
    this.headLoc = document.getElementsByTagName("head").item(0);
    // Generate a unique script tag id
    this.scriptId = 'JscriptId' + JSONscriptRequest.scriptCounter++;
}

// Static script ID counter
JSONscriptRequest.scriptCounter = 1;

// buildScriptTag method
//
JSONscriptRequest.prototype.buildScriptTag = function () {

    // Create the script tag
    this.scriptObj = document.createElement("script");
    
    // Add script object attributes
    this.scriptObj.setAttribute("type", "text/javascript");
    this.scriptObj.setAttribute("charset", "utf-8");
    this.scriptObj.setAttribute("src", this.fullUrl + this.noCacheIE);
    this.scriptObj.setAttribute("id", this.scriptId);
}
 
// removeScriptTag method
// 
JSONscriptRequest.prototype.removeScriptTag = function () {
    // Destroy the script tag
    this.headLoc.removeChild(this.scriptObj);  
}

// addScriptTag method
//
JSONscriptRequest.prototype.addScriptTag = function () {
    // Create the script tag
    this.headLoc.appendChild(this.scriptObj);
}
function scEU3EmbedModule(pageType, searchString,domainCheck){
	if(domainCheck == "1"){
		domain = "http://de.shoutcast.com";
		serviceUrl = "http://de.shoutcast.com/embed_module";
		embedCSSUrl = "http://de.shoutcast.com/css/embed_stylesheet.css";
		shoutcast_player = "http://de.shoutcast.com/shoutcast_player";
	}else if(domainCheck == "2"){
		domain = "http://uk.shoutcast.com";
		serviceUrl = "http://uk.shoutcast.com/embed_module";
		embedCSSUrl = "http://uk.shoutcast.com/css/embed_stylesheet.css";
		shoutcast_player = "http://uk.shoutcast.com/shoutcast_player";
	}
		domainChk = domainCheck;
		scEmbedModule(pageType, searchString);
}
function scEmbedModule(pageType, searchString){
	resultsPerPage = 5;
	var urlString = serviceUrl+"?reqType="+pageType+"&callback=scEmbedCallback&cnt=5&s="+escape(searchString);
	if(cssDownloadStatus == 0) {
		getCSS();
	}
	// Create a new request object
	bObj = new JSONscriptRequest(urlString); 
	// Build the dynamic script tag
	bObj.buildScriptTag(); 
	// Add the script tag to the page
	bObj.addScriptTag();
}

//callback function

function scEmbedCallback(jsonData){
	
	var embedHtmlCode = "";
	
	embedHtmlCode += "<div class='embedModuleOuter fontstyle'>"+
					"<div class='embedModuleHdr'>"+
						"<a class='embedModuleLft' href='"+domain+"' target='_blank'></a>"+
						"<div class='embedModuleCenter'>"+
						"<a class='embedmoduleShare' href='"+domain+"/shoutcast-radio-widget' target='_blank'></a>"+
						"<div class='embedModuleRgt'></div></div>"+
					"</div>";
	
	var strKeyword = "Search Stations, Genre";
	if(jsonData.ResultSet.QueryType == "KEYWORD_SEARCH"){
		strKeyword = jsonData.ResultSet.SearchKeyword;
	} 
	
	embedHtmlCode += "<div class='embedModuleSearch'><div class='embedModuleSearchTxt'>Explore thousands of radio stations from around the world</div>"+
						"<div class='embedModuleSearchTxtbxOutr'>"+
							"<div class='embedSearchOutr'>"+
								"<form action='javascript:submitEmbedSearch()' name='EmbedSearch'>"+
									"<input id='embedSearchTxt' type='text' class='embedModuleSearchTxtbx brfontstyle12' value='"+strKeyword+"'"+ 
										" onFocus='retainFieldStatusEmbed(\"Search Stations, Genre\",\"embedSearchTxt\")' onblur='searchBox4()'>"+
									"<input type=submit class='embedSearch' value=''/>"+
								"</form>"+
							"</div>"+
							"<div class='headerSimpleSearchEmbed1'>"+
								"<select class='embedDropdown' id='embedDropdown' onchange='javascript:submitEmbedGenreSearch()'>"+
									"<option value=''>--Choose a genre--</option>";
	var strGenre = "";
	if(jsonData.ResultSet.QueryType == "GENRE_SEARCH"){
		strGenre = jsonData.ResultSet.SearchKeyword;
	}
	if(jsonData.ResultSet.Genres != null){
		for(var i=0; i<jsonData.ResultSet.Genres.length; i++){
			embedHtmlCode += "<option value='"+jsonData.ResultSet.Genres[i]+"' ";
			if(strGenre == jsonData.ResultSet.Genres[i]){
				embedHtmlCode += " selected ";
			}
			embedHtmlCode += ">"+jsonData.ResultSet.Genres[i]+"</option>";
		}
	}
	embedHtmlCode +=			"</select>"+
							"</div>"+
						"</div>"+
					"</div>";
	
	var pageHeader = "";
	if(jsonData.ResultSet.QueryType == "KEYWORD_SEARCH"){
		var strKeywordH = jsonData.ResultSet.SearchKeyword;
		if(strKeywordH.length > 20){
			strKeywordH = strKeywordH.substring(0,20)+"...";
		}
		pageHeader = strKeywordH + " Radio Stations";
	} else if(jsonData.ResultSet.QueryType == "GENRE_SEARCH"){
		var strGenreH = jsonData.ResultSet.SearchKeyword;
		if(strGenreH.length > 20){
			strGenreH = strGenreH.substring(0,20)+"...";
		}
		pageHeader = strGenreH + " Radio Stations";
	} else if(jsonData.ResultSet.QueryType == "MOST_POPULAR"){
		pageHeader = "Top SHOUTcast Radio Stations Today";
	} else {
		pageHeader = "Top SHOUTcast Radio Stations Today";
	}
	embedHtmlCode += "<div class='embedzModuleTp fontstyle'>"+pageHeader+"</div><div class='embedFixedHeight'>";

	var classname = '';
	if(jsonData.ResultSet.stations != null && jsonData.ResultSet.stations.length != 0){
		for(var j=0; j<jsonData.ResultSet.stations.length;j++){
			if((j%2)==0){
				classname="embedDiroutrDark";
			} else {
				classname="embedDiroutrLight";
			}
			var stationName = jsonData.ResultSet.stations[j].name;
			if(stationName.length > stationNameLimit){
				stationName = stationName.substring(0,stationNameLimit)+"...";
			}
			var nowPlaying = jsonData.ResultSet.stations[j].ct;
			if(nowPlaying ==null || nowPlaying == ""){
				nowPlaying = "-";
			}
			if(nowPlaying.length > stationNPLimit){
				nowPlaying = nowPlaying.substring(0,stationNPLimit)+"...";
			}
			var genreString = jsonData.ResultSet.stations[j].genre;
			var genreArray = genreString.split(" ");
		embedHtmlCode +="<div class='"+classname+" clearFix'>"+
							"<div class='dirTuneMoreDiv clearFix'>";
		if(jsonData.ResultSet.UsrAgntAction == "1"){
			embedHtmlCode += "<a href='#' onclick='javascript:embedPopup(this,\"mywindow\", \""+jsonData.ResultSet.stations[j].id+"\",\""+genreArray[0]+"\",1);'>";
		} else if(jsonData.ResultSet.UsrAgntAction == "2"){
			embedHtmlCode += "<a href='javascript:playInWinamp("+jsonData.ResultSet.stations[j].id+")'>";
		} 
			embedHtmlCode +=	"<div class='tuneIn'></div>"+
								"</a>"+
							"</div>";

		embedHtmlCode +=	"<div class='embedDirSta'>"+
								"<div class='embedDirSta1'>"+
									"<div class='dirStationTxt'><span class='dirFontStyle fontstyle'>Station:</span></div>"+
										"<div class='embedDirSta2 fontstyle' title='"+jsonData.ResultSet.stations[j].name+"'>"+
										 stationName+ 
										"</div>"+
									"</div>"+
									"<div class='embedDirNp'>"+
										"<div class='embedDirNpTxt'><span class='dirFontStyle fontstyle'>Now Playing:</span></div>"+
										"<div title='"+jsonData.ResultSet.stations[j].ct+"' class='embedDirNpTxt1'>"+
											"<span class='fontstyle'>"+
												nowPlaying+ 
										"</span>"+
										"</div>"+
									"</div>"+
								"</div>"+
							"</div>"+
							"<div class='embeddirseperator'></div>";

		}embedHtmlCode +="</div>";
	} else {
		var strKeywordF = jsonData.ResultSet.SearchKeyword;
		if(strKeywordF.length > 20){
			strKeywordF = strKeywordF.substring(0,20)+"...";
		}
		embedHtmlCode += "<div class='embedNoSta'><div class='embedNoStaTxt'>"+
						"<b>Unfortunately, there weren't any SHOUTcast radio streams found containing the term <span class='dirYellowHover'>"+
						strKeywordF+"</span>. Please search  again.</b></div></div>";
	}
	//var linkToSc = "http://www.shoutcast.com/";domain
	var linkToSc = domain;
	if(jsonData.ResultSet.QueryType == "GENRE_SEARCH"){
		linkToSc += "/radio/"+jsonData.ResultSet.SearchKeyword;
	} else if(jsonData.ResultSet.QueryType == "KEYWORD_SEARCH"){
		linkToSc += "/directory/search_results.jsp?searchCrit=simple&s="+jsonData.ResultSet.SearchKeyword;
	}

	embedHtmlCode +=		"<div class='embedAllSta'>"+
								"<div class='embedStations1 fontstyle'>"+jsonData.ResultSet.stations.length+" of "+jsonData.ResultSet.TotalResults+" stations</div>"+
								"<div class='embedStations2'><a class='brfontstyle12' href='"+linkToSc+"' target='_blank'>See All Stations</a></div>"+
							"</div>"+
							"<div class='embedModuleFooter'>"+
								"<div class='embedModuleFooterCntr'><div class='embedfooterht fontstyle'>&copy; 2009 Nullsoft, Inc. All Rights Reserved.</div><div class='embedfooterht fonstyle'><a href='"+domain+"/privacy' target='_blank'>Privacy Policy</a><span class='embedFooterSep'>|</span><a href='"+domain+"/disclaimer' target='_blank'>Terms</a></div></div>"+
							"</div>"+
						"</div>";

	document.getElementById("sc_embed_module").innerHTML = embedHtmlCode;
	bObj.removeScriptTag();
}


function retainFieldStatusEmbed(status, id){
	if(document.getElementById(id).value == status){
		document.getElementById(id).value = "";
	}
}

function searchBox4(){
	if(document.getElementById("embedSearchTxt").value == ""){
		document.getElementById("embedSearchTxt").value ="Search Stations, Genre";
	}
	
}

function embedPopup(mylink, mywin, staId,genrename,contentFlag)
{
	//holdStationID(staId);
	//winRef = window.open("http://www.shoutcast.com/shoutcast_player"+'?'+'stationid='+staId+'&'+'Genre='+genrename+'&'+'ContentFlag='+contentFlag, mywin, 'width=860,height=658,scrollbars=yes,resizable=no,status=0,left=150,top=20,location=yes');
	winRef = window.open(shoutcast_player+'?'+'stationid='+staId+'&'+'Genre='+genrename+'&'+'ContentFlag='+contentFlag+"&uc=U&isCallInternal=NO&related=yes", mywin, 'width=860,height=658,scrollbars=yes,resizable=no,status=0,left=150,top=20,location=yes');
	winRef.focus();
}

function holdStationID(stationID)
{
	var reply="NO";
	var date = new Date();
	date.setTime(date.getTime()+(365*24*60*60*1000));
	var expires = "; expires="+date.toGMTString();
	var counterID = 0;
	var getcookies = document.cookie;
	var tempArray = new Array();
	tempArray = getcookies.split(';');

	for(ctrr=0;ctrr<tempArray.length;ctrr++)
	{
		if(tempArray[ctrr].indexOf(stationID) != -1)
	    {
		   retValue = forExistingStationIDs(stationID);  
		   if (retValue == false)
		   {
			   return false;
		   }
		   getcookies = document.cookie;
		   break;
		}
    }

	if (getcookies.indexOf("StCounterID") != -1)
	{
		pos1 = getcookies.indexOf("StCounterID")+"StCounterID".length+1;
        pos2 = getcookies.indexOf(";",pos1);
		if (pos2 == -1)
		{
			pos2 = document.cookie.length;
		}

		//if(document.cookie.substring(pos1,pos2) != ""){
			counterID= document.cookie.substring(pos1,pos2);
		//}
		++counterID;
		if (counterID > 9)
		{
			counterID = 0;
			reply="YES";
            document.cookie = "ArrayShift"+"="+reply+"; path=/"+expires;
		}
		document.cookie = "StCounterID"+"="+counterID+"; path=/"+expires;
	}
	else
	{
		  var first=0;
		  document.cookie = "StCounterID"+"="+first+"; path=/"+expires;
	}

	var stCookieID = "StationID"+counterID;
	document.cookie = stCookieID+"="+stationID+"; path=/"+expires;
}

function forExistingStationIDs(stationID)
{
	var date = new Date();
	date.setTime(date.getTime()+(365*24*60*60*1000));
	var expires = "; expires="+date.toGMTString();
	var cks = document.cookie;
	var tArray = new Array();
	var tctr = -1;
	tArray  = cks.split(';');
	var found = 0;
	for(ctrr=0;ctrr<tArray.length;ctrr++)
	{
	   if (tArray[ctrr].indexOf("StationID") != -1)
	   { 
		   ++found;
		   pos   = tArray[ctrr].indexOf("StationID");
		   posx  = tArray[ctrr].indexOf("=");
		   cntID = tArray[ctrr].substring(pos+"StationID".length,posx);
	   }
	}

	if (found==1 && cntID==0)
		 return false; 

	for(ctrr=0;ctrr<tArray.length;ctrr++)
	{
	   if (tArray[ctrr].indexOf("StationID") != -1)
	   {

		   // Will give the Station counter
		   pos   = tArray[ctrr].indexOf("StationID");
		   posx  = tArray[ctrr].indexOf("=");
		   cntID = tArray[ctrr].substring(pos+"StationID".length,posx);

		   // Will give the Station ID
		   pos  = tArray[ctrr].indexOf("StationID");
		   posx = tArray[ctrr].substring(pos+"StationID".length,pos+"StationID".length+1);
		   stID = tArray[ctrr].substring(pos+"StationID".length+2,tArray[ctrr].length);

		   if (tArray[ctrr].indexOf(stationID) == -1)
		   {
			   ++tctr;			
			   document.cookie = "StationID"+tctr+"="+stID+"; path=/"+expires;
			   document.cookie = "StCounterID"+"="+tctr+"; path=/"+expires;
		   }

	   }
	}
	return true;
}

function submitEmbedSearch(){
	var keyword = document.getElementById("embedSearchTxt").value;
	var reqType = "KEYWORD_SEARCH";
	scEU3EmbedModule(reqType, keyword,domainChk);
}

function submitEmbedGenreSearch(){
	var genre = document.getElementById("embedDropdown").value;
	var reqType = "GENRE_SEARCH";
	if(genre != ""){
		scEU3EmbedModule(reqType, genre, domainChk);
	}
}


function getCSS() {
	
	headTag = document.getElementsByTagName("head")[0];
	var ExCss = document.createElement('link');
	ExCss.type = 'text/css';
	ExCss.rel = 'stylesheet';
	ExCss.href = embedCSSUrl;
	ExCss.media = 'screen';
	headTag.appendChild(ExCss);
	cssDownloadStatus++;
}

function playInWinamp(staId)
{
	holdStationID(staId);
	this.location="http://yp.shoutcast.com/sbin/tunein-station.pls?id="+staId;
}