<?
session_start();
   $path = $_SERVER['DOCUMENT_ROOT'];
   $path .= "/login.php";
   include_once($path);

$pointapp="Calendar"; 
$pagetitle ="Calendar"; 
$AppName = "Calendar"; 
$pagetitlename ="Calendar";


$today = date("Y-m-d");

$sql = "SELECT * FROM apps WHERE name = '$AppName'"; 
$result = mysqli_query($con,$sql);
$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
$CalendarAppIcon = date("d");
$AppLevel = $row['level'];


$cookieremember=$_COOKIE['RememberUser'];
$cookiesessionid=$_COOKIE['user_id'];
if(empty($_SESSION["user_id"])AND($cookieremember!="Yes")) {
   $path = $_SERVER['DOCUMENT_ROOT'];
   $path .= "/loginform.php";
   include_once($path);
} else { 
if(!empty($_SESSION["user_id"])){
$result = mysqli_query($con,"SELECT * FROM users WHERE Username='" . $_SESSION["user_id"] . "'");
$row  = mysqli_fetch_array($result,MYSQLI_ASSOC);
}

$ParentPage = "/index.php";

   $path = $_SERVER['DOCUMENT_ROOT'];
   $path .= "/header.php";
   include_once($path);
 
?>

<style>

/*  CALENDAR CSS  */

#Calendar{
  padding:0 0 10px 0;
  margin:0;
  font-family:"Century Ghotic", Times, serif;
  border:1px gray solid; 
  
}

#Calendar_Labels{
    display: grid;  
    grid-template-columns: auto auto auto auto auto auto auto;  
    list-style: none;
    text-align:center;
    
}

#Calendar_Days {  
    display: grid;  
    grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr;  
    grid-gap: 10px;
    list-style: none;
    vertical-align:top;
    margin:5px; 
    padding:0;
}

.CellCase {  
    border:none; 
    text-align:left; 
    padding:5px; 
    overflow:scroll;
    min-height:100px;
    background-color:#DDD;
}

.CellCase > span{
  font-size:2em;
}

.event:hover > div{ 
box-shadow: 2px 2px 5px #888888;
filter: brightness(95%);

}

.event{
padding: 3px;
text-align:left; 
margin-bottom:2px;
}

.event > div{
  padding:5px;
  font-size:1em;
  border-radius:5px; 
  color:black;


  line-height:1em; 
}


.prev{
  position:absolute; 
  left:50px;
  padding:5px;
  font-size:1.2em;
  color:#FFF;
  background-color:dodgerblue;
  border-radius:4px; 
}

.next{
  position:absolute; 
  right:50px;
  padding:5px;
  font-size:1.2em;
  color:#FFF;
  background-color:dodgerblue;
  border-radius:4px; 
}

.next:hover, .prev:hover{
  filter: brightness(80%);
}

.title{
  font-size:2em;
}

/*  Right click button custom menu  */
.show {
  z-index: 1000;
  position: absolute;
  background-color: #FFFFFF;
  border: 1px solid darkgray;
  border-radius: 10px;
  display: block;
  list-style-type: none;
  list-style: none;
  text-align:left;
}

.hide {
  display: none;
}

.show ul{
    margin: 10px 0 10px 0;

}

.show li {
  list-style: none;
  line-height:1em;
  font-size:0.9em;
}

.show li:hover {
    background-color: lightgray;
}

.show li a {
  border: 0 !important;
  text-decoration: none;
  width:100%;
  margin: 0px; 
  display: block; 
  height: 100%; 
  padding: 3px 15px 3px 15px ;


}


:focus{
    outline:none;
}
 
div.clear{
    clear:both;
} 



::-webkit-scrollbar
{
	width: 5px;
  height:0;
	background-color: #DDD;
}

::-webkit-scrollbar-thumb
{
	background-color: #787878;
	border: 0;
}


.CloseButton{
cursor: pointer;
font-size:1.4em;
}
.CloseButton:hover{
background-color:#CD2602 !important;
color:black;
padding: 0 7px 0 7px !important;
}


/*   PAGE SECTIONS CONTROL  */
#DashboardContainer {
    background-color: #FFF;
    position:relative;
}
#colLeft {
    display: inline-block;
    vertical-align:top;
}

#colRight {
    display: inline-block;
    position:relative;

}

</style>

<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">


 <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>

 <!---These 2 following scripts are for the draggable, droppable & resizable functions-->  
  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>


    
     <script>                                     
$(document).ready(function(){

$(function() {
    var result = {};
    $(".draggable").draggable({
    	start:function(e){
            result.drag = e.target.id.split("_")[1];
    EventID = $(this).attr('EventID');
    EventType = $(this).attr('EventType');
    ParentABlock = $(this).siblings().attr('CurrentDateBlock');
    },
        
    appendTo: 'body',
    containment: "window",
    scroll: false,
    helper: 'clone'
    });
    $(".droppable").droppable({

        drop: function(event, ui) {
            
            var $this = $(this);
            result.drop = event.target.id.split("_")[1];
            if(result.drag == result.drop){
var NewParentABlock = event.target.id;
NewParentABlock = NewParentABlock.substring(3);

var r = confirm(" Are you sure you want to move " + EventType + " ID# " + EventID + " from " + ParentABlock  +  " to " + NewParentABlock + "?");
if (r == true) {
                $.get("ajax.php", {NewDate:NewParentABlock,EventType:EventType,EventID:EventID}).done(function(data){                
                    // Display the returned data in browser                                     
                }); 
}
else{
 ui.helper.hide();

$( ".droppable" ).draggable( "option", "cancel", ".title" );



}           
            }

            $this.append(ui.draggable);    
            
            var width = $this.width();
            var height = $this.height();
            var cntrLeft = (width / 2) - (ui.draggable.width() / 2);
            var cntrTop = (height / 2) - (ui.draggable.height() / 2);
            
            ui.draggable.css({
                left: cntrLeft + "px",
                top: cntrTop + "px"  
            });
        }
    });
});







var resposition = '';
var dragposition = '';

$( function() {
    $( "#ExternalDataPopup" ).resizable({resize: function(event,ui){resposition = ui.position; }});
} );


$( function() {
    $( "#ExternalDataPopup" ).draggable({ cancel: '.SelectableText', drag: function(event,ui){ dragposition = ui.position;  }});
} );



/*
$( function() {
    $( "#colLeft" ).resizable({handles: 'e, w'});
} );


$( function() {
    $( "#colRight" ).resizable({});
} );

$( function() {
    $( "#colRight" ).draggable({});
} );

*/

});  



$(document).keyup(function(e) {
     if (e.key === "Escape") { // escape key maps to keycode `27`
        // <DO YOUR WORK HERE>
        CloseExternalDataPopup();

    }
});


/// Right click javascript
$(document).ready(function() {


///  RIGHT CLICK CALENDAR EVENT
if ($("#CustomRightClick").addEventListener) {
  $("#CustomRightClick").addEventListener('contextmenu', function(e) {
    alert("You've tried to open context menu"); //here you draw your own menu
    e.preventDefault();
  }, false);
} else {

  //document.getElementById("CustomRightClick").attachEvent('oncontextmenu', function() {
  //$(".CustomRightClick").bind('contextmenu', function() {
  $('body').on('contextmenu', 'a.CustomRightClick', function() {
    window.event.returnValue = false;
   
    var EventID = $(this).data('id');


    //alert("contextmenu"+event);
    document.getElementById("rmenu").className = "show";
    document.getElementById("rmenu").style.top = mouseY(event) + 'px';
    document.getElementById("rmenu").style.left = mouseX(event) + 'px';



    //Get selected Calendar event id

    //Open Command
    document.getElementById("ButtonOpenEvent").href = "https://vgrcanada.org/apps/Calendar/calendarevent.php?calendareventid="+EventID;
    

    //Edit Command
    document.getElementById("ButtonEditEvent").href = "https://vgrcanada.org/apps/Calendar/calendareventedit.php?calendareventid="+EventID+"&action=edit";

    //Delete Command
    document.getElementById("ButtonDeleteEvent").href = "https://vgrcanada.org/apps/Calendar/calendarevent.php?actionform=delete&calendareventid="+EventID;

    //Duplicate Command
    document.getElementById("ButtonDuplicateEvent").href = "https://vgrcanada.org/apps/Calendar/calendareventedit.php?actionform=duplicate&calendareventid="+EventID;

    //Copy Command
    document.getElementById("ButtonCopyEvent").onclick = function ()
    {
    var ID_to_copy = EventID;
        $(function()
        {
            $.get("ajax.php", {EventIDtoCopy:ID_to_copy}).done(function(data){                
                    // Display the returned data in browser      
                }); 
        });
    }
    document.getElementById("rmenuPhonenote").className = "hide";
    document.getElementById("rmenuCase").className = "hide";

  });
}


///  RIGHT CLICK CALENDAR DAY
if ($("#CustomRightClickCase").addEventListener) {
  $("#CustomRightClickCase").addEventListener('contextmenu', function(e) {
    alert("You've tried to open context menu"); //here you draw your own menu
    e.preventDefault();
  }, false);
} else {

  //document.getElementById("CustomRightClickCase").attachEvent('oncontextmenu', function() {
  //$(".CustomRightClickCase").bind('contextmenu', function() {
  $('body').on('contextmenu', 'li.CustomRightClickCase', function() {

    window.event.returnValue = false;

    //if((document.getElementById("rmenu").classList.contains("hide")) && (document.getElementById("rmenuPhonenote").classList.contains("hide"))){
    document.getElementById("rmenuCase").className = "show";
    document.getElementById("rmenuCase").style.top = mouseY(event) + 'px';
    document.getElementById("rmenuCase").style.left = mouseX(event) + 'px';

    //}

    var DateToPaste = $(this).data('id');

    //Get selected Calendar event id
    //var url_string = this.href; 
    //var url = new URL(url_string);
    //var DateToPaste = url.searchParams.get("evdate");  




    // New Event on selected day
    document.getElementById("ButtonNewEvent").href = "https://vgrcanada.org/apps/Calendar/calendareventedit.php?evdate="+DateToPaste;

    // New PhoneNote on selected day
    document.getElementById("ButtonNewPhoneNote").href = "https://vgrcanada.org/apps/PhoneNotes/phonenoteedit.php?CalendarDate="+DateToPaste;
    

    //Paste Command
    document.getElementById("ButtonPasteEvent").onclick = function ()
    {
    var NewParentABlock = DateToPaste;

        $(function()
        {
            $.get("ajax.php", {PasteLocation:NewParentABlock}).done(function(data){                
                    // Display the returned data in browser   
                    location.reload();
                }); 
        });

    }


  });
}


///  RIGHT CLICK PHONE NOTE EVENT
if ($("#CustomRightClickPhoneNote").addEventListener) {
  $("#CustomRightClickPhoneNote").addEventListener('contextmenu', function(e) {
    alert("You've tried to open context menu"); //here you draw your own menu

    e.preventDefault();

  }, false);
} else {

  //document.getElementById("CustomRightClickCase").attachEvent('oncontextmenu', function() {
  //$(".CustomRightClickCase").bind('contextmenu', function() {
  $('body').on('contextmenu', 'a.CustomRightClickPhoneNote', function() {

    window.event.returnValue = false;

    var PhoneNoteID = $(this).data('id');

    document.getElementById("rmenuPhoneNote").className = "show";
    document.getElementById("rmenuPhoneNote").style.top = mouseY(event) + 'px';
    document.getElementById("rmenuPhoneNote").style.left = mouseX(event) + 'px';
    

    //Open Command
    document.getElementById("ButtonOpenPhoneNote").href = "https://vgrcanada.org/apps/PhoneNotes/phonenotes.php?phonenoteid="+PhoneNoteID;


    //Edit Command
    document.getElementById("ButtonEditPhoneNote").href = "https://vgrcanada.org/apps/PhoneNotes/phonenoteedit.php?action=edit&phonenoteid="+PhoneNoteID;
  
// Make sure the other right click menu is well hidden...
setTimeout(function (){
document.getElementById("rmenuCase").className = "hide";
}, 1);   
  
  });

  

     



}

});



function ShowExternalDataPopup(EventID, AppToDisplay) {
   
    $.ajax({
          type: "POST",
          url: "ajax.php",
          data: AppToDisplay+"="+EventID,
          dataType : "html",
          //affichage de l'erreur en cas de problème
          error: function(XMLHttpRequest, textStatus, errorThrown) {
                  alert(XMLHttpRequest + '--' + textStatus + '--' + errorThrown);
              },
              //function s'il n'y a pas de probleme
          success:function(data){   
        
            if(AppToDisplay=="ShowPhoneNote"){TextAppToDisplay="Phone Note";}
            if(AppToDisplay=="CalendarEvent"){TextAppToDisplay="Event";} 


            document.getElementById("ExternalDataPopup").innerHTML = "<div id='ExternalDataPopup' class='ui-widget-content'>";         
     
            document.getElementById("ExternalDataPopup").innerHTML += "<div style='text-align:center; margin-top:-30px; height:30px; width: 350px; background-color:lightgray; position:fixed; z-index:99999; padding:5px; border-bottom:1px solid gray; border-top-right-radius: 7px; border-top-left-radius: 7px;'>"+TextAppToDisplay+"<a class='CloseButton' style='padding: 0 7px 0 7px; float:right;' onclick='CloseExternalDataPopup()'>✖</a></div>";
            document.getElementById("ExternalDataPopup").innerHTML += data;
            document.getElementById("ExternalDataPopup").innerHTML += "</div>";         
            document.getElementById("ExternalDataPopup").innerHTML += "</div>";         
          }

          
        });
      


            document.getElementById("ExternalDataPopup").style.position =  'fixed';
            document.getElementById("ExternalDataPopup").style.top =  '10%';
            document.getElementById("ExternalDataPopup").style.left =  '10%';


            document.getElementById("ExternalDataPopup").className = "show";
           
  };


// Make menu right clicks menus disappear 
$(document).bind("click", function(event) {
document.getElementById("rmenu").className = "hide";
document.getElementById("rmenuCase").className = "hide";
document.getElementById("rmenuPhoneNote").className = "hide";


});


function mouseX(evt) {
if (evt.pageX) {
  return evt.pageX;
} else if (evt.clientX) {
  return evt.clientX + (document.documentElement.scrollLeft ?
    document.documentElement.scrollLeft :
    document.body.scrollLeft);
} else {
  return null;
}
}

function mouseY(evt) {
if (evt.pageY) {
  return evt.pageY;
} else if (evt.clientY) {
  return evt.clientY + (document.documentElement.scrollTop ?
    document.documentElement.scrollTop :
    document.body.scrollTop);
} else {
  return null;
}
}


function CloseExternalDataPopup()
{
    document.getElementById("ExternalDataPopup").innerHTML = "";

    document.getElementById("ExternalDataPopup").className = "hide";
}
  
  </script>

<?
$CurrentPageNow = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>  


<script> 
$(document).ready(function(){
  
setInterval(function(){
$("#CalendarsToRefresh").load('<? echo $CurrentPageNow; ?> #CalendarsToRefresh', function(resp, status, xhr) {
$(function() {
    var result = {};
    $(".draggable").draggable({
    	start:function(e){
            result.drag = e.target.id.split("_")[1];
    EventID = $(this).attr('EventID');
    EventType = $(this).attr('EventType');
    ParentABlock = $(this).siblings().attr('CurrentDateBlock');
    },
        
    appendTo: 'body',
    containment: "window",
    scroll: false,
    helper: 'clone'
    });
    $(".droppable").droppable({

        drop: function(event, ui) {
            
            var $this = $(this);
            result.drop = event.target.id.split("_")[1];
            if(result.drag == result.drop){
var NewParentABlock = event.target.id;
NewParentABlock = NewParentABlock.substring(3);

var r = confirm(" Are you sure you want to move " + EventType + " ID# " + EventID + " from " + ParentABlock  +  " to " + NewParentABlock + "?");
if (r == true) {
                $.get("ajax.php", {NewDate:NewParentABlock,EventType:EventType,EventID:EventID}).done(function(data){                
                    // Display the returned data in browser                                     
                }); 
}
else{
 ui.helper.hide();

$( ".droppable" ).draggable( "option", "cancel", ".title" );



}           
            }

            $this.append(ui.draggable);    
            
            var width = $this.width();
            var height = $this.height();
            var cntrLeft = (width / 2) - (ui.draggable.width() / 2);
            var cntrTop = (height / 2) - (ui.draggable.height() / 2);
            
            ui.draggable.css({
                left: cntrLeft + "px",
                top: cntrTop + "px"  
            });
        }
    });
});
});


}, 15000);
});
</script>  
  

<?
   $currentpath = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";;
?> 


<div id="DashboardContainer" style="">

<!---
<div id="colLeft" style="min-height:600px; min-width:150px; margin:5px 10px 10px 10px; border: 1px solid gray; ">

</div>
---->

<div id="colRight" style="margin: 1px solid gray; ">


 


<?php
/**
*@author  Xu Ding
*@email   thedilab@gmail.com
*@website http://www.StarTutorial.com
**/ 

$AlleventsIndividual = "";   
 
            $sql = "SELECT * FROM calendarevents";  
            $result = mysqli_query($con,$sql);                  
            $AlleventsIndividual = mysqli_fetch_array($result,MYSQLI_ASSOC);

  $Allevents = array($AlleventsIndividual);    

class Calendar {  
     
    /**
     * Constructor
     */
    public function __construct(){     
        $this->naviHref = htmlentities($_SERVER['PHP_SELF']);
    }
     
    /********************* PROPERTY ********************/  
    private $dayLabels = array("Sun","Mon","Tue","Wed","Thu","Fri","Sat");     
    private $currentYear=0;     
    private $currentMonth=0;     
    private $currentDay=0;     
    private $currentDate=null;    
    private $daysInMonth=0;     
    private $naviHref= null;
     
    /********************* PUBLIC **********************/  
        
    /**
    * print out the calendar
    */
    public function show($ThisMonth) {
        $year  == null;         
        $month == null;         
        if(null==$year&&isset($_GET['year'])){
            $year = $_GET['year'];         
        }
        else if(null==$year){ 
            $year = date("Y",time());           
        }                   
        if(null==$month&&isset($_GET['month'])){ 
            $month = $_GET['month'];         
        }else if(null==$month){ 
            $month = date("m",time());         
        } 
        
        if($ThisMonth == "next"){
        if($month==12){$month=1; $year = $year+1;}
        else{$month = $month+1;}
        }                         
        $this->currentYear=$year;         
        $this->currentMonth=$month;         
        $this->daysInMonth=$this->_daysInMonth($month,$year);            
        $content='<div id="Calendar" class="">'.
                        '<div id="Calendar_Navigation" class="">'.
                        $this->_createNavi().
                        '</div>'.
                        '<div class="">'.
                                '<ul id="Calendar_Labels" class="">'.$this->_createLabels().'</ul>';   
                                //$content.='<div class="clear"></div>';     
                                $content.='<ul id="Calendar_Days" style="">';    
                                 
                                $weeksInMonth = $this->_weeksInMonth($month,$year);
                                // Create weeks in a month
                                for( $i=0; $i<$weeksInMonth; $i++ ){
                                     
                                    //Create days in a week
                                    for($j=1;$j<=7;$j++){
                                        $content.=$this->_showDay($i*7+$j);
                                    }
                                }                                 
                                $content.='</ul>';                                 
                                //$content.='<div class="clear"></div>';                  
                        $content.='</div>';                 
        $content.='</div>';
        return $content; 
    }


public function getCalendarEvents($CellDate) { 
   $path = $_SERVER['DOCUMENT_ROOT'];
   $path .= "/config.php";
   include($path);
            $eventyear = $this->currentYear; 
            $eventmonth = $this->currentMonth; 
            $eventday = $this->currentDay -1;  
            
            if($eventday<10){$eventday="0$eventday";}           
           
           
            $sql = "SELECT * FROM calendarevents";      
            $results = mysqli_query($con,$sql);              
            $resultset = array();
            $AllResultsEventsCalendarToday = "";
            while ($row = mysqli_fetch_array($results,MYSQLI_ASSOC)) {
              if(empty($row["endday"])){
              if(($eventyear==$row["year"])AND($eventmonth==$row["month"])AND($eventday==$row["day"])){
              $resultset[] = $row;
              }
              }    
              else{
              $eventcurrentdate = $CellDate;
              $eventstartdate = $row["year"]."-".$row["month"]."-".$row["day"];
              $eventenddate = $row["endyear"]."-".$row["endmonth"]."-".$row["endday"];
              if(($eventcurrentdate >= $eventstartdate) && ($eventcurrentdate <= $eventenddate)){
              $resultset[] = $row;
              }
              }
            }

           

            // $resultset now holds all rows from the first query.
            foreach ($resultset as $result){
            if(!empty($result["tagcolor"])){$tagcolor=$result["tagcolor"];}else{$tagcolor="orange";}
            $AppToDisplay = "CalendarEvent";
         $AllResultsEventsCalendarToday .='<a onclick="ShowExternalDataPopup('.$result["id"].',\''.$AppToDisplay.'\')" ng-click="openFolder(detail.id, detail.name)" data-id="'.$result["id"].'"  style="display:block; cursor:pointer;" class="draggable event CustomRightClick" EventType="CalendarEvent" EventID="'.$result["id"].'">
            <div id="DateEvents" style="background-color:'.$tagcolor.';">'; 
            if(
              (strpos($result["title"],'Call')!== false)OR
              (strpos($result["title"],'call')!== false)
            ){$titleToShow='&#9742; '.$result["title"].' ';}else{$titleToShow=''.$result["title"].'';}   
              
            $AllResultsEventsCalendarToday .= ' '.$titleToShow.'
            </div>            
        </a>';}  

/* SHIPMENTS  */

            $sql = "SELECT id,datereceived,title,city,eta FROM shipments";      
            $results = mysqli_query($con,$sql);               
            while ($row = mysqli_fetch_array($results,MYSQLI_ASSOC)) {
              if($eventcurrentdate == $row["datereceived"]){
              $resultsetship[] = $row;
              }  
              elseif($eventcurrentdate == $row["eta"]){
              $resultsetas[] = $row;
              }           
            }           
 
            // $resultset now holds all rows from the first query.
            foreach ($resultsetship as $resultship){
            $tagcolor="#FFFFFF";
         $AllResultsEventsCalendarToday .='<a href="/apps/Shipments/viewshipment.php?id='.$resultship["id"].'" style="display:block;" class="event" EventType="ShipmentArrived" EventID="'.$resultship["id"].'">
            <div id="DateEvents" style="background-color:'.$tagcolor.'; ">
            &#9972; '.$resultship["title"].' arrived in '.$resultship["city"].'
            </div>            
        </a>';}  
            // $resultsetas  ETA of SHIPMENTS now holds all rows from the first query.
            foreach ($resultsetas as $resultseta){
            $tagcolor="#FF5E5E";
         $AllResultsEventsCalendarToday .='<a href="/apps/Shipments/viewshipment.php?id='.$resultseta["id"].'" style="display:block;" class="event"  EventType="ShipmentETA" EventID="'.$resultseta["id"].'">
            <div id="DateEvents" style="background-color:'.$tagcolor.';">
            &#9972; ETA: '.$resultseta["title"].' in '.$resultseta["city"].'
            </div>            
        </a>';}     
        
      
        
                          
/* End of SHIPMENTS  */ 
/* PHONE NOTES  */

            $sql = "SELECT id,date,firstname,lastname FROM phonenotes";      
            $results = mysqli_query($con,$sql);               
            while ($row = mysqli_fetch_array($results,MYSQLI_ASSOC)) {
              if($eventcurrentdate == $row["date"]){
              $resultsetphone[] = $row;
              }            
            }           
 
            // $resultset now holds all rows from the first query.
            foreach ($resultsetphone as $resultphone){
            $tagcolor="#ccffcc";
         //$AllResultsEventsCalendarToday .='<a href="/apps/PhoneNotes/phonenotes.php?phonenoteid='.$resultphone["id"].'" class="event PhoneNoteEvent" style="display:block;" EventType="PhoneNote" EventID="'.$resultphone["id"].'">
         $AppToDisplay = 'ShowPhoneNote';
         $AllResultsEventsCalendarToday .='<a onclick="ShowExternalDataPopup('.$resultphone["id"].',\''.$AppToDisplay.'\')" ng-click="openFolder(detail.id, detail.name)" data-id="'.$resultphone["id"].'" class="CustomRightClickPhoneNote event PhoneNoteEvent" style="cursor:pointer; display:block;" EventType="PhoneNote" EventID="'.$resultphone["id"].'">

            <div id="DateEvents" style="background-color:'.$tagcolor.'; ">
            &#9742; '.$resultphone["firstname"].' '.$resultphone["lastname"].'. 
            </div>            
        </a>';}                    
/* End of PHONE NOTES  */ 
        
        return $AllResultsEventsCalendarToday;   
                              

}





    /********************* PRIVATE **********************/ 
    /**                                                                                     
    * create the li element for ul
    */    
     public function _showDay($cellNumber){         
        if($this->currentDay==0){             
            $firstDayOfTheWeek = date('N',strtotime($this->currentYear.'-'.$this->currentMonth.'-02'));                    
            if(intval($cellNumber) == intval($firstDayOfTheWeek)){                 
                $this->currentDay=1;                 
            }
        }
         
        if( ($this->currentDay!=0)&&($this->currentDay<=$this->daysInMonth)){             
            $this->currentDate = date('Y-m-d',strtotime($this->currentYear.'-'.$this->currentMonth.'-'.($this->currentDay)));             
            $cellContent = $this->currentDay;             
            $this->currentDay++;  
            $eventyear = $this->currentYear; 
            $eventmonth = $this->currentMonth; 
            $eventday = $this->currentDay -1;  
            
            $today = date("Y-m-d");
            if($eventday<10){$eventday="0$eventday";}
            if(strlen($eventmonth)==1){$eventmonth="0$eventmonth";}
             
            if($today=="$eventyear-$eventmonth-$eventday"){$TodayBackColor=" style=\"text-align:left; padding:5px;  background-color:dodgerblue; overflow:scroll;\"";} 
            
            global $Allevents;
            foreach($Allevents as $event){
            $events = $event;
            }
                 
        }else{             
            $this->currentDate =null; 
            $cellContent=null;            
        }  

        if(!empty($eventday)){
        return '
        <li '.$TodayBackColor.' ng-click="openFolder(detail.id, detail.name)" data-id="'.$this->currentDate.'" id="li-'.$this->currentDate.'" class="CellCase CustomRightClickCase '.($cellNumber%7==1?' start ':($cellNumber%7==0?' end ':' ')).($cellContent==null?'mask':'').' droppable">
            <span>'.$cellContent.'</span>
            '.$this->getCalendarEvents($eventyear.'-'.$eventmonth.'-'.$eventday).'
        </li>';   }
        else{
        return '<li '.$TodayBackColor.' id="li-'.$this->currentDate.'" class="CellCase '.($cellNumber%7==1?' start ':($cellNumber%7==0?' end ':' ')).($cellContent==null?'mask':'').'">

        </li>';   }        
    }
     
    /**
    * create navigation
    */
    public function _createNavi(){         
        $nextMonth = $this->currentMonth==12?1:intval($this->currentMonth)+1;         
        $nextYear = $this->currentMonth==12?intval($this->currentYear)+1:$this->currentYear;         
        $preMonth = $this->currentMonth==1?12:intval($this->currentMonth)-1;        
        $preYear = $this->currentMonth==1?intval($this->currentYear)-1:$this->currentYear;
         
        return
            '<div class="header">'.
                '<span><a class="prev" href="'.$this->naviHref.'?month='.sprintf('%02d',$preMonth).'&year='.$preYear.'">Prev</a></span>'.
                    '<span class="title">'.date('Y M',strtotime($this->currentYear.'-'.$this->currentMonth.'-1')).'</span>'.
                '<span><a class="next" href="'.$this->naviHref.'?month='.sprintf("%02d", $nextMonth).'&year='.$nextYear.'">Next</a></span>'.
            '</div>';
    }
         

    /**
    * create calendar week labels
    */
    private function _createLabels(){  
                 
        $content='';         
        foreach($this->dayLabels as $index=>$label){             
            $content.='<li class="'.($label==6?'end title':'start title').' title">'.$label.'</li>'; 
        }         
        return $content;
    }               
    /**
    * calculate number of weeks in a particular month
    */
    private function _weeksInMonth($month=null,$year=null){
         
        if( null==($year) ) {
            $year =  date("Y",time()); 
        }
         
        if(null==($month)) {
            $month = date("m",time());
        }
         
        // find number of days in this month
        $daysInMonths = $this->_daysInMonth($month,$year);         
        $numOfweeks = ($daysInMonths%7==0?0:1) + intval($daysInMonths/7);        
        $monthEndingDay= date('N',strtotime($year.'-'.$month.'-'.$daysInMonths));         
        $monthStartDay = date('N',strtotime($year.'-'.$month.'-01'));
        if($monthEndingDay<$monthStartDay){             
            $numOfweeks++;         
        }         
        return $numOfweeks;
    }
 
    /**
    * calculate number of days in a particular month
    */
    private function _daysInMonth($month=null,$year=null){         
        if(null==($year))
            $year =  date("Y",time());  
        if(null==($month))
            $month = date("m",time());             
        return date('t',strtotime($year.'-'.$month.'-01'));
    }     
}


$calendar = new Calendar();

echo"<div id=\"CalendarsToRefresh\">"; 
echo $calendar->show("");  

echo"<br><br><br><br><br><br>";

$calendarNextMonth = new Calendar("");
echo $calendarNextMonth->show("next");


echo"</div>";



?>


<!--- SHOW PHONE NOTE--->
<div id="ExternalDataPopup" class="hide ui-widget-content" style="position:relative; width:350px !important; height:450px !important; overflow:scroll; ">   </div>


<div class="hide" id="rmenu" style="box-shadow: 2px 2px 5px #888888;">
  <ul style=" margin: 10px 0 10px 0;  padding:0;">
    <li>
      <a id="ButtonOpenEvent" href="">☛ Open</a>
    </li>
    <li>
      <a id="ButtonEditEvent" href="">✐ Edit</a>
    </li>

    <li id="ButtonCopyEvent" style="padding:5px 15px 5px 15px; cursor:pointer;">
     ❏ Copy
    </li>

    <li>
      <a id="ButtonDuplicateEvent" href="">⚌ Duplicate</a>
    </li>

    <div style="with:100%; height:1px; background-color:lightgray; margin-top:7px; margin-bottom:7px;"></div>
    <li style="">
      <a id="ButtonDeleteEvent" href="" onclick="return confirm('Are you sure you want to delete this entry?')">✖ Delete</a>
    </li>
  </ul>
</div>



<div class="hide" id="rmenuPhoneNote" style="box-shadow: 2px 2px 5px #888888;">
  <ul style=" margin: 10px 0 10px 0;  padding:0;">
    <li>
      <a id="ButtonOpenPhoneNote" href="">☛ Open</a>
    </li>
    <li>
      <a id="ButtonEditPhoneNote" href="">✐ Edit</a>
    </li>
  </ul>
</div>


<div class="hide" id="rmenuCase" style="box-shadow: 2px 2px 5px #888888;">
  <ul style=" margin: 10px 0 10px 0;  padding:0;">
  <li>
    <a id="ButtonNewEvent" href="">✎ New Event</a>

    </li>
    <li>
    <a id="ButtonNewPhoneNote" href="">☎ New Phone Note</a>

    </li>
    
    <li id="ButtonPasteEvent" style="padding:5px 15px 5px 15px; ";
    <?    if (isset($_SESSION["Calendar_ID_Event_Copied"]))
    {echo"color:black;";} 
    else{echo" color:gray";}?>
        cursor:pointer;\">
    ◰ Paste
    </li>
    
  </ul>
</div>
</div>



<?
}

?>



</div>
</html>