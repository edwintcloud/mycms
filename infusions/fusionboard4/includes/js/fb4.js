/*
	fusionBoard 4.0
	php-Invent Team
	http://www.php-invent.com
	
	Developer: Ian Unruh (SoBeNoFear)
	ianunruh@gmail.com
*/ 

function checkForm() {
   sc_spellCheckers.resumeAll();
   return true;
}
		   
function sw(pre, el, other) {
	var i = document.getElementById(pre + '-' + el);
	var o = document.getElementById(pre + '-' + other);
	
	var switchBold = document.getElementById(other + '-toggle');
	var boldOff = document.getElementById(el + '-toggle');
	
	boldOff.innerHTML = el;
	switchBold.innerHTML = "<b>" + other + "</b>";
	
	o.style.display = 'block';
	i.style.display = 'none';	
}
	
/**
 *   Class by Stickman -- http://www.the-stickman.com
 *      with thanks to:
 *      [for Safari fixes]
 *         Luis Torrefranca -- http://www.law.pitt.edu
 *         and
 *         Shawn Parker & John Pennypacker -- http://www.fuzzycoconut.com
 *      [for duplicate name bug]
 *         'neal'
 */
function MultiSelector( list_target, max ){
	this.list_target = list_target;
	this.count = 0;
	this.id = 0;
	if( max ){
		this.max = max;
	} else {
		this.max = -1;
	};
	this.addElement = function( element ){
		if( element.tagName == 'INPUT' && element.type == 'file' ){
			element.name = 'file_' + this.id++;
			element.multi_selector = this;
			element.setAttribute("class", "textbox");
			element.onchange = function(){
				var new_element = document.createElement( 'input' );
				new_element.type = 'file';
				this.parentNode.insertBefore( new_element, this );
				this.multi_selector.addElement( new_element );
				this.multi_selector.addListRow( this );
				this.style.position = 'absolute';
				this.style.left = '-1000px';
			};
			if( this.max != -1 && this.count >= this.max ){
				element.disabled = true;
			};
			this.count++;
			this.current_element = element;
		} else {
			alert( 'Error: not a file input element' );
		};
	};
	this.addListRow = function( element ){
		var new_row = document.createElement( 'div' );
		var new_row_button = document.createElement( 'input' );
		new_row_button.type = 'button';
		new_row_button.value = 'Delete';
		new_row_button.setAttribute("class", "button");
		new_row.element = element;
		new_row_button.onclick= function(){
			this.parentNode.element.parentNode.removeChild( this.parentNode.element );
			this.parentNode.parentNode.removeChild( this.parentNode );
			this.parentNode.element.multi_selector.count--;
			this.parentNode.element.multi_selector.current_element.disabled = false;
			return false;
		};
		new_row.innerHTML = element.value;
		new_row.appendChild( new_row_button );
		this.list_target.appendChild( new_row );
	};
};

/* award management ajax */

var xmlHttp

function GetXmlHttpObject()
{
var xmlHttp=null;
try
  {
  // Firefox, Opera 8.0+, Safari
  xmlHttp=new XMLHttpRequest();
  }
catch (e)
  {
  // Internet Explorer
  try
    {
    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
return xmlHttp;
}

function awardAdd(image,user,desc)
{
xmlHttp=GetXmlHttpObject();
if (xmlHttp==null)
  {
  alert ("Your browser does not support AJAX!");
  return;
  } 
var url="includes/ajax/awardForm.php";
url=url+"?image="+image;
url=url+"&user="+user;
url=url+"&desc="+desc;
url=url+"&sid="+Math.random();
xmlHttp.onreadystatechange=stateChangedB();
xmlHttp.open("GET",url,true);
xmlHttp.send(null);
pausecomp(250);
awardRender(user);
} 

function awardRender(str)
{
if (str.length==0)
  { 
  document.getElementById("awardContent").innerHTML="";
  return;
  }
xmlHttp=GetXmlHttpObject();
if (xmlHttp==null)
  {
  alert ("Your browser does not support AJAX!");
  return;
  } 
var url="includes/ajax/awardRender.php";
url=url+"?q="+str;
url=url+"&sid="+Math.random();
xmlHttp.onreadystatechange=stateChangedA;
xmlHttp.open("GET",url,true);
xmlHttp.send(null);
} 

function pausecomp(millis)
{
var date = new Date();
var curDate = null;

do { curDate = new Date(); }
while(curDate-date < millis);
} 

function stateChangedA() 
{ 
if (xmlHttp.readyState==4)
{ 
document.getElementById("awardContent").innerHTML=xmlHttp.responseText;
}
}

function stateChangedB() 
{ 
if (xmlHttp.readyState==4)
{ 
document.getElementById("awardForm").innerHTML=xmlHttp.responseText;
}
}

function deleteAward(del, user)
{
xmlHttp=GetXmlHttpObject();
if (xmlHttp==null)
  {
  alert ("Your browser does not support AJAX!");
  return;
  } 
var url="includes/ajax/awardForm.php";
url=url+"?del="+del;
url=url+"&sid="+Math.random();
xmlHttp.onreadystatechange=stateChangedC();
xmlHttp.open("GET",url,true);
xmlHttp.send(null);
pausecomp(250);
awardRender(user);
} 

function stateChangedC() 
{ 
if (xmlHttp.readyState==4)
{ 
document.getElementById("deleted").innerHTML=xmlHttp.responseText;
}
}

