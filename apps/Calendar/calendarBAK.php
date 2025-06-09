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
.header {
  background-color: #4a89dc !important;
  color: white;
  padding: 15px;
  text-align: center;
  position: relative;
}/* CALENDAR CSS */

#Calendar {
  padding: 0 0 10px 0;
  margin: 0 0 30px 0;
  font-family: "Century Gothic", Arial, sans-serif;
  border: 1px solid #bbb; /* Darker border */
  border-radius: 5px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

#Calendar_Navigation {
  background-color: #4a89dc;
  color: white;
  padding: 15px;
  text-align: center;
  position: relative;
}

#Calendar_Labels {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  list-style: none;
  text-align: center;
  background-color: #f5f5f5;
  border-bottom: 1px solid #bbb; /* Darker border */
  padding: 10px 0;
  margin: 0;
}

#Calendar_Days {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  grid-gap: 0;
  list-style: none;
  vertical-align: top;
  margin: 0;
  padding: 0;
  background-color: white;
}

.CellCase {
  border-right: 1px solid #ccc; /* Darker border */
  border-bottom: 1px solid #ccc; /* Darker border */
  text-align: left;
  padding: 5px;
  overflow: auto;
  min-height: 100px;
  max-height: 200px;
  background-color: white;
  transition: background-color 0.2s;
}

.CellCase:nth-child(7n) {
  border-right: none;
}

.CellCase > span {
  font-weight: bold;
  margin-bottom: 5px;
  color: #444;
  font-size: 1.2em;
  display: block;
}

.CellCase:hover {
  background-color: #f9f9f9;
  max-height: 800px;
}

.CellCase.mask {
  background-color: #f9f9f9;
}

.event:hover > div {
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
  filter: brightness(95%);
}

.event {
  padding: 3px;
  text-align: left;
  margin-bottom: 4px;
  cursor: pointer;
}

.event > div {
  padding: 5px;
  font-size: 0.85em;
  border-radius: 3px;
  color: black;
  line-height: 1.2em;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.2s;
  word-break: break-word;
}

.prev {
  position: absolute;
  left: 15px;
  padding: 5px 10px;
  font-size: 1em;
  color: white;
  background-color: transparent;
  border-radius: 3px;
  transition: background-color 0.3s;
}

.next {
  position: absolute;
  right: 15px;
  padding: 5px 10px;
  font-size: 1em;
  color: white;
  background-color: transparent;
  border-radius: 3px;
  transition: background-color 0.3s;
}

.next:hover, .prev:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

.title {
  font-size: 1.5em;
  font-weight: bold;
}

/* Right click button custom menu */
.show {
  z-index: 1000;
  position: absolute;
  background-color: white;
  border: 1px solid #aaa; /* Darker border */
  border-radius: 5px;
  display: block;
  list-style-type: none;
  list-style: none;
  text-align: left;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.hide {
  display: none;
}

.show ul {
  margin: 0;
  padding: 10px 0;
}

.show li {
  list-style: none;
  line-height: 1.4em;
  font-size: 0.9em;
}

.show li:hover {
  background-color: #f5f5f5;
}

.show li a, .show li #ButtonCopyEvent, .show li #ButtonPasteEvent {
  border: 0 !important;
  text-decoration: none;
  width: 100%;
  margin: 0;
  display: block;
  height: 100%;
  padding: 8px 15px;
  color: #333;
  cursor: pointer;
}

.show div[style="with:100%; height:1px; background-color:lightgray; margin-top:7px; margin-bottom:7px;"] {
  height: 1px;
  background-color: #aaa; /* Darker divider */
  margin: 5px 0;
}

:focus {
  outline: none;
}

div.clear {
  clear: both;
}

::-webkit-scrollbar {
  width: 5px;
  height: 0;
  background-color: #f5f5f5;
}

::-webkit-scrollbar-thumb {
  background-color: #bbb;
  border: 0;
  border-radius: 10px;
}

.CloseButton {
  cursor: pointer;
  font-size: 1.2em;
  float: right;
  margin: 0 !important;
  padding: 6px 15px 4px 15px !important;
  border-top-right-radius: 7px;
  color: #ccc;
}

.CloseButton:hover {
  background-color: #ff3b4b !important;
  color: white;
}

/* PAGE SECTIONS CONTROL */
#DashboardContainer {
  background-color: white;
  position: relative;
  padding: 15px;
}

#colLeft {
  display: inline-block;
  vertical-align: top;
}

#colRight {
  display: inline-block;
  position: relative;
  width: 100%;
}

/* Popup styles */
.ExternalDataPopup {
  border: 1px solid #aaa; /* Darker border */
  border-radius: 8px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.popup-header {
  background-color: #4a89dc;
  color: white;
  padding: 10px 15px;
  cursor: move;
  user-select: none;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top-left-radius: 7px;
  border-top-right-radius: 7px;
  min-height: 40px;
  position: sticky;
  top: 0;
  z-index: 2;
}

.popup-title {
  flex-grow: 1;
  text-align: center;
  font-weight: bold;
}

.popup-close {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  padding: 0 15px;
  font-size: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  transition: background-color 0.2s;
}

.popup-close:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

.popup-content {
  overflow: auto;
  max-height: calc(100% - 40px);
  padding: 15px;
}

/* Improve resizable handles */
.ui-resizable-handle {
  background: #f0f0f0;
  border: 1px solid #ddd;
  width: 8px;
  height: 8px;
}

#ExternalDataPopup, .ExternalDataPopup {
  position: fixed;
  left: 50%;
  top: 50%;
  max-width: 600px;
  width: 90%;
  height: 90vh;
  transform: translate(-50%, -50%);
  z-index: 1000;
  background: white;
  display: none;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
  border: 1px solid #aaa; /* Darker border */
  border-radius: 8px;
}

/* Off-canvas menu styles */
#off-canvas-menu {
  position: fixed;
  top: 0;
  left: 0;
  width: 250px;
  height: 100%;
  background-color: white;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
  z-index: 1500;
  transition: transform 0.3s ease;
  padding: 20px;
  transform: translateX(-100%);
}

/* Keep this class name as -translate-x-full for Tailwind compatibility */
#off-canvas-menu.-translate-x-full {
  transform: translateX(-100%);
}

#off-canvas-menu:not(.-translate-x-full) {
  transform: translateX(0);
}

#open-menu {
  position: fixed;
  top: 15px;
  left: 15px;
  background-color: #4a89dc;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 5px 10px;
  font-size: 1.2em;
  cursor: pointer;
  z-index: 1400;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

#close-menu {
  width: 100%;
  text-align: right;
  background: none;
  border: none;
  color: #ff3b4b;
  font-size: 1.2em;
  cursor: pointer;
  margin-bottom: 15px;
}

/* Form inputs in popups */
.ExternalDataPopup .p_title {
  margin-bottom: 20px;
  font-weight: bold;
}

.ExternalDataPopup .p_input {
  margin-bottom: 25px;
}

.ExternalDataPopup .p_title + .p_input {
  margin-top: 15px;
}

.ExternalDataPopup input,
.ExternalDataPopup textarea,
.ExternalDataPopup select {
  margin-top: 8px;
  margin-bottom: 15px;
  width: 100%;
}

/* Add responsive design */
@media screen and (max-width: 768px) {
  #ExternalDataPopup, .ExternalDataPopup {
    width: 95%;
    height: 95vh;
  }

  .CellCase {
    min-height: 80px;
  }

  .event div {
    font-size: 0.75em;
    padding: 3px;
  }

  .title {
    font-size: 1.2em;
  }

  .prev, .next {
    padding: 3px 6px;
  }
}

@media screen and (max-width: 480px) {
  .CellCase {
    min-height: 60px;
    padding: 3px;
  }

  .CellCase > span {
    font-size: 0.9em;
    margin-bottom: 3px;
  }

  .event div {
    font-size: 0.7em;
    padding: 2px;
  }
}

/* Today's date highlight */
.CellCase[style*="background-color:dodgerblue"] {
  background-color: #e6f7ff !important;
}

.CellCase[style*="background-color:dodgerblue"] > span {
  color: #4a89dc;
  font-weight: bold;
}

</style>

 <!---Off Canvas-->  
<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">



 <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>

 <!---These 2 following scripts are for the draggable, droppable & resizable functions-->  
  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
  
  <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css"/>
  <script>
        document.addEventListener("DOMContentLoaded", function () {
            const offCanvasMenu = document.getElementById("off-canvas-menu");
            const openMenuButton = document.getElementById("open-menu");
            const closeMenuButton = document.getElementById("close-menu");
            const phoneNoteCheckbox = document.getElementById("filter-phone-note");
            const calendarEventCheckbox = document.getElementById("filter-calendar-event");
            const shipmentArrivedCheckbox = document.getElementById("filter-shipment-arrived");
            const shipmentETACheckbox = document.getElementById("filter-shipment-eta");

            openMenuButton.addEventListener("click", function () {
                offCanvasMenu.classList.remove("-translate-x-full");
            });

            closeMenuButton.addEventListener("click", function () {
                offCanvasMenu.classList.add("-translate-x-full");
            });

            function updateVisibility() {
                document.querySelectorAll(".event").forEach(event => {
                    const eventType = event.getAttribute("EventType");
                    if (eventType === "PhoneNote") {
                        event.style.display = phoneNoteCheckbox.checked ? "block" : "none";
                    } else if (eventType === "CalendarEvent") {
                        event.style.display = calendarEventCheckbox.checked ? "block" : "none";
                    } else if (eventType === "ShipmentArrived") {
                        event.style.display = shipmentArrivedCheckbox.checked ? "block" : "none";
                    } else if (eventType === "ShipmentETA") {
                        event.style.display = shipmentETACheckbox.checked ? "block" : "none";
                    }
                });
            }

            phoneNoteCheckbox.addEventListener("change", updateVisibility);
            calendarEventCheckbox.addEventListener("change", updateVisibility);
            shipmentArrivedCheckbox.addEventListener("change", updateVisibility);
            shipmentETACheckbox.addEventListener("change", updateVisibility);
        });
    </script>
    
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
    $( ".ExternalDataPopup" ).resizable({resize: function(event,ui){resposition = ui.position; }});
} );


$( function() {
    $( ".ExternalDataPopup" ).draggable({ cancel: '.SelectableText', drag: function(event,ui){ dragposition = ui.position;  }});
} );




});  



$(document).keyup(function(e) {
     if (e.key === "Escape") { // escape key maps to keycode `27`
        // <DO YOUR WORK HERE>
        $('#ExternalDataPopup').addClass('hide').hide();
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
    document.getElementById("rmenu").style.top = mouseY(event)-50 + 'px';
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
    document.getElementById("rmenuCase").style.top = mouseY(event)-50 + 'px';
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
    document.getElementById("rmenuPhoneNote").style.top = mouseY(event)-50 + 'px';
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

let currentPopup = null;

function ShowExternalDataPopup(EventID, AppToDisplay) {
    var $popup = $('#ExternalDataPopup');

    // Show loading state immediately
    $popup.html(`
        <div class="popup-header">
            <span class="popup-title">Loading...</span>
            <button type="button" class="popup-close">✕</button>
        </div>
        <div class="popup-content" style="display:flex; justify-content:center; align-items:center; height:calc(100% - 40px);">
            <div style="text-align:center;">
                <div class="loading-spinner" style="margin:0 auto 1rem; width:40px; height:40px;">
                    <svg viewBox="0 0 50 50" style="animation: rotate 2s linear infinite;">
                        <circle cx="25" cy="25" r="20" fill="none" stroke="#888" stroke-width="4" 
                                style="stroke-dasharray: 100; stroke-dashoffset: 60;">
                        </circle>
                    </svg>
                </div>
                <div>Loading content...</div>
            </div>
        </div>
    `);

    // Initialize draggable/resizable
    try { $popup.draggable('destroy'); } catch(e){}
    try { $popup.resizable('destroy'); } catch(e){}

    $popup.draggable({
        handle: '.popup-header',
        start: function() {
            $(this).css({ transform: 'none' });
        },
        containment: 'window'
    }).resizable({
        handles: 'all',
        minWidth: 300,
        minHeight: 200
    });

    // Show popup
    $popup.removeClass('hide').show();

    // Load content
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: AppToDisplay + "=" + EventID,
        dataType: "html",
        success: function(data) {
            const TextAppToDisplay = {
                'ShowPhoneNote': 'Phone Note',
                'CalendarEvent': 'Event',
                'Shipment': 'Shipment'
            }[AppToDisplay] || 'Details';

            // Update content
            $popup.html(`
                <div class="popup-header">
                    <span class="popup-title">${TextAppToDisplay}</span>
                    <button type="button" class="popup-close">✕</button>
                </div>
                <div class="popup-content">${data}</div>
            `);

            // Reapply draggable and resizable after content update
            try { $popup.draggable('destroy'); } catch(e){}
            try { $popup.resizable('destroy'); } catch(e){}
            
            $popup.draggable({
                handle: '.popup-header',
                start: function() {
                    $(this).css({ transform: 'none' });
                },
                containment: 'window'
            }).resizable({
                handles: 'all',
                minWidth: 300,
                minHeight: 200
            });

            // Reattach close handler
            $popup.find('.popup-close').on('click', function() {
                $popup.addClass('hide').hide();
            });
        }
    });

    // Initial close handler
    $popup.find('.popup-close').on('click', function() {
        $popup.addClass('hide').hide();
    });
}

// Simplified close function
function CloseExternalDataPopup() {
    if (currentPopup) {
        currentPopup.hide();
    }
}

// Add escape key handler
$(document).on('keyup', function(e) {
    if (e.key === "Escape") {
        CloseExternalDataPopup();
    }
});




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

/// Attempt scrolling to the current date:
let currentDate = new Date();
CurrentCaseDate = document.getElementById("li-"+currentDate.toISOString().split('T')[0]);
CurrentCaseDate.scrollIntoView({behavior: 'smooth', block: 'center'});


/// Refresh calendars  
setInterval(function(){
      // Store checkbox states before refresh
      const filterStates = {
        PhoneNote: document.getElementById("filter-phone-note").checked,
        CalendarEvent: document.getElementById("filter-calendar-event").checked,
        ShipmentArrived: document.getElementById("filter-shipment-arrived").checked,
        ShipmentETA: document.getElementById("filter-shipment-eta").checked
    };
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

                // Reapply filter states after refresh
                document.querySelectorAll(".event").forEach(event => {
                const eventType = event.getAttribute("EventType");
                if (filterStates[eventType] === false) {
                    event.style.display = "none";
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

            $sql = "SELECT id,datesent,datereceived,title,city,eta FROM shipments";      
            $results = mysqli_query($con,$sql);               
            while ($row = mysqli_fetch_array($results,MYSQLI_ASSOC)) {
              if($eventcurrentdate == $row["datereceived"]){
              $resultsetship[] = $row;
              }  
              elseif($eventcurrentdate == $row["eta"]){
              $resultsetas[] = $row;
              }   
              elseif($eventcurrentdate == $row["datesent"]){
              $resultsshipped[] = $row;
              }           
            }           
 
            // $resultset now holds all rows from the first query.
            foreach ($resultsetship as $resultship){
            $tagcolor="#FFFFFF";
            $AppToDisplay = 'Shipment';

         $AllResultsEventsCalendarToday .='<a onclick="ShowExternalDataPopup('.$resultship["id"].',\'Shipment\')" style="display:block; cursor:pointer;" class="event" EventType="ShipmentArrived" EventID="'.$resultship["id"].'" ng-click="openFolder(detail.id, detail.name)" data-id="'.$resultship["id"].'">
            <div id="DateEvents" style="background-color:'.$tagcolor.'; ">
            &#9972; '.$resultship["title"].' arrived in '.$resultship["city"].'
            </div>            
        </a>';}  
        foreach ($resultsshipped as $resultshipped){
          $tagcolor="#FFFFFF";
       $AllResultsEventsCalendarToday .='<a onclick="ShowExternalDataPopup('.$resultshipped["id"].',\'Shipment\')" style="display:block; cursor:pointer;" class="event" EventType="ShipmentArrived" EventID="'.$resultshipped["id"].'" ng-click="openFolder(detail.id, detail.name)" data-id="'.$resultshipped["id"].'">
          <div id="DateEvents" style="background-color:'.$tagcolor.'; ">
          &#9972; '.$resultshipped["title"].' shipped to '.$resultshipped["city"].'
          </div>            
      </a>';}  
            // $resultsetas  ETA of SHIPMENTS now holds all rows from the first query.
            foreach ($resultsetas as $resultseta){
            $tagcolor="#FF5E5E";
         $AllResultsEventsCalendarToday .='<a onclick="ShowExternalDataPopup('.$resultseta["id"].',\'Shipment\')" style="display:block; cursor:pointer;" class="event"  EventType="ShipmentETA" EventID="'.$resultseta["id"].'" ng-click="openFolder(detail.id, detail.name)" data-id="'.$resultseta["id"].'">
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
            '<div class="header" id="'.date('Y F',strtotime($this->currentYear.'-'.$this->currentMonth.'-1')).'">'.
                '<span><a class="prev" href="'.$this->naviHref.'?month='.sprintf('%02d',$preMonth).'&year='.$preYear.'">Prev</a></span>'.
                    '<span class="title" style="font-weight:bold; font-size:2em;">'.date('Y F',strtotime($this->currentYear.'-'.$this->currentMonth.'-1')).'</span>'.
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

  <!-- Open Menu Button -->
  <button id="open-menu" class="fixed top-2 left-2 bg-blue-500 text-white px-3 py-1 rounded">≡</button>

    <!-- Off Canvas Menu -->
    <div id="off-canvas-menu" class="fixed top-0 left-0 w-64 h-full bg-gray-100 p-4 shadow-lg transform -translate-x-full transition-transform">
        <button id="close-menu" class="text-right w-full text-red-500">Close ✖</button>
        <h2 class="text-lg font-bold">Calendar filters</h2>
        <label class="block mt-2">
            <input type="checkbox" id="filter-phone-note" checked class="mr-2"> Phone Notes
        </label>
        <label class="block mt-2">
            <input type="checkbox" id="filter-calendar-event" checked class="mr-2"> Calendar Events
        </label>
        <label class="block mt-2">
            <input type="checkbox" id="filter-shipment-arrived" checked class="mr-2"> Shipments
        </label>
        <label class="block mt-2">
            <input type="checkbox" id="filter-shipment-eta" checked class="mr-2"> Shipment ETA
        </label>
    </div>



<!--- SHOW PHONE NOTE--->
<div id="ExternalDataPopup" class="hide ui-widget-content ExternalDataPopup"></div>

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