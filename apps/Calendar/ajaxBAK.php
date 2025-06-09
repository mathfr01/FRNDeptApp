<?php
/* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
   $path = $_SERVER['DOCUMENT_ROOT'];
   $path .= "/config.php";
   include_once($path);
 
// Check connection
if($con === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

 
if(isset($_REQUEST['NewDate'])){  
session_start();
$NewDate=$_REQUEST['NewDate'];
$EventID=$_REQUEST['EventID'];
$EventType=$_REQUEST['EventType'];

$newyear = date('Y', strtotime($NewDate));
$newmonth = date('m', strtotime($NewDate));
$newday = date('d', strtotime($NewDate));

if((!empty($newyear))AND(!empty($newmonth))AND(!empty($newday))){

$sql = "UPDATE calendarevents SET year='$newyear', month='$newmonth', day='$newday' WHERE id='$EventID'"; 
     
if ($con->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
die();
    echo "Error updating record: " . $con->error;
}


}
}


/// POPUP FORMS IN CALENDAR
if (isset($_GET['get_form'])) {
    $form_type = $_GET['get_form'];
    $form_id = isset($_GET['id']) ? $_GET['id'] : '';
    $date = isset($_GET['date']) ? $_GET['date'] : '';
    
    ob_start();
    if ($form_type === 'event') {
        if (!empty($form_id)) {
            include('calendareventedit.php?calendareventid=' . $form_id . '&action=edit&ajax=true');
        } else {
            include('calendareventedit.php?evdate=' . $date . '&ajax=true');
        }
    } else if ($form_type === 'phonenote') {
        if (!empty($form_id)) {
            include('phonenoteedit.php?phonenoteid=' . $form_id . '&action=edit&ajax=true');
        } else {
            include('phonenoteedit.php?CalendarDate=' . $date . '&ajax=true');
        }
    }
    echo ob_get_clean();
    exit;
}

/// COPY COMMAND
if(isset($_REQUEST['EventIDtoCopy'])){  
    session_start();
    
    $EventID=$_REQUEST['EventIDtoCopy'];

    $_SESSION["Calendar_ID_Event_Copied"] = $EventID;
}


/// PASTE COMMAND
if(isset($_REQUEST['PasteLocation'])){  
    session_start();
    
    $PasteLocation=$_REQUEST['PasteLocation'];
    $EventID = $_SESSION["Calendar_ID_Event_Copied"];

    $Pasteyear = date('Y', strtotime($PasteLocation));
    $Pastemonth = date('m', strtotime($PasteLocation));
    $Pasteday = date('d', strtotime($PasteLocation));

    $sql = "SELECT * FROM calendarevents WHERE id = '$EventID'"; 
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
    
    $calendareventtitle = $row['title'];
    $Newcalendareventtitle = "$calendareventtitle - copy";
    $calendareventdescription = $row['description'];
    $calendareventtagcolor = $row['tagcolor'];
    $calendareventassignment = $row['assignment'];
    
   

    $sql = "INSERT INTO calendarevents (id, title, description, year, month, day, endyear, endmonth, endday, tagcolor, Created_By, Date_Created, assignment)
    VALUES ('', '$Newcalendareventtitle', '$calendareventdescription', '$Pasteyear', '$Pastemonth', '$Pasteday', '', '', '', '$calendareventtagcolor', '$fullusername', '$today', '$calendareventassignment')";
    if ($con->query($sql) === TRUE)
    {
    $sentformmessage = "<div class=\"SentFormMessageYes\">The event was successfully imported.</div>";
    $firstname = $_SESSION["FirstName"];
    $lastname = $_SESSION["LastName"];
    $fulldate = date('l F jS Y h:i:s A');	
    $sql2 = "INSERT INTO log (id, name, date, event) VALUES('', '" . $firstname ."' '" . $lastname . "', '" . $fulldate . "','Duplicated event ".$calendareventtitle.".');  ";	
    if ($con->query($sql2) === TRUE)
    {}
    $calendareventapp = $_POST['app'];
    header('location:calendar.php');
    }
    else{
    $sentformmessage = "<div class=\"SentFormMessageNo\">An error occured, the eventwas not imported.</div>";
    $calendareventapp = $_POST['app'];
    }
}

    
/// SHOW PHONE NOTE COMMAND
if(isset($_REQUEST['ShowPhoneNote'])){  
    session_start();
    
    $PhoneNoteId=$_REQUEST['ShowPhoneNote'];


$sql = "SELECT * FROM phonenotes WHERE id = '$PhoneNoteId'"; 
$result = mysqli_query($con,$sql);

while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
    echo"<style>
    .p_title{  font-weight: bold; color:grey;}
    .p_input{font-size:0.8em; margin-top:-10px; margin-bottom:7px; color:grey;}
    </style>
    <div style='padding:5px !important;' class='SelectableText'>";
if(!empty($row['caller'])){echo"<div style=\"text-align:left;\"><p class=\"p_title\" style=\"\">Person who made the call:</p><p class=\"p_input\">". nl2br($row['caller'])."</p></div>"; }
if(!empty($row['peopleoncall'])){echo"<div style=\"text-align:left;\"><p class=\"p_title\" style=\"\">People on the phone call</p><p class=\"p_input\">". nl2br($row['peopleoncall'])."</p></div>"; }
if(!empty($row['phonenote'])){echo"<div style=\" text-align:left; \"><p class=\"p_title\" style=\"\">Phone Notes</p><p class=\"\">".html_entity_decode($row['phonenote'])."</p></div>
"; }
echo"</div>";
}
}




/// SHOW EVENT COMMAND
if(isset($_REQUEST['CalendarEvent'])){  
    session_start();
    
    $EventId=$_REQUEST['CalendarEvent'];


$sql = "SELECT * FROM calendarevents WHERE id = '$EventId'"; 
$result = mysqli_query($con,$sql);


while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){echo 
"<div style='padding:5px !important;' class='SelectableText'><b>".$row['title']."</b>
<br>
<span style=\"font-size:0.7em;\">
".$row['Date_Created']."</span>
".$row['description']."</div>";
}
}


/// SHOW EVENT COMMAND
if(isset($_REQUEST['Shipment'])){  
    session_start();
    
    $EventId=$_REQUEST['Shipment'];


$sql = "SELECT * FROM shipments WHERE id = '$EventId'"; 
$result = mysqli_query($con,$sql);

$row = mysqli_fetch_array($result,MYSQLI_ASSOC);

$country = $row['country'];
$city = $row['city'];
$status = $row['status'];
$datesent = $row['datesent'];
$datereceived = $row['datereceived'];
$title = $row['title'];
$description = $row['description'];
$shippingcost = $row['shippingcost'];
$orderid = $row['orderid'];
$carrier = $row['carrier'];
$clearingcost = $row['clearingcost'];
$origin = $row['origin'];
$trackingnumber = $row['tracking'];
$eta = $row['eta'];
$Created_By = $row['Created_By'];
$Date_Created = $row['Date_Created'];
$Last_Modified_By = $row['Last_Modified_By'];
$Date_Last_Modified = $row['Date_Last_Modified'];


echo"    <div class=\"centercontent\" style=\" \">";
 

if((!empty($Created_By))OR(!empty($Last_Modified_By))){
echo"<div style=\"float:left;\">";
if(!empty($Created_By)){echo"<div style=\"width:100%; text-align:left; line-height:0.7em; color:lightgray; font-style:italic; font-size:0.8em;\"><p class=\"\">Created by $Created_By on $Date_Created</p></div>";}
if(!empty($Last_Modified_By)){echo"<div style=\"width:100%; text-align:left; line-height:0.7em; color:lightgray; font-style:italic; font-size:0.8em;\"><p class=\"\">Last modified by $Last_Modified_By on $Date_Last_Modified</p></div>";}
echo"</div>";
}   

echo" 
<style>
.p_title{  font-weight: bold;}
</style>

<div onclick=\"location.href='https://www.vgrcanada.org/apps/Shipments/shipments.php?action=edit&id=$EventId';\" class=\"divbluebutton\" style=\"float:right; display:inline-block;\">Edit</div>
                         
<div style=\"width:100%; text-align:center;\">
<div style=\"color:black; text-align:center; position:relative; left:0; right:0; margin:auto; font-size:2em; padding-top:0.5em; display:inline-block;\">$title for $country</div>
</div>




<div id=\"separator\" style=\"width:100%; height:0.5em; background-color:#FAFAFA;\"></div>";

if((!empty($country))OR(!empty($city))){
if(!empty($country)){echo"<div style=\"width:100%; text-align:left; line-height:0.4em;\"><p class=\"p_title\">Country</p><p class=\"p_input\">$country</p></div>";}
if(!empty($city)){echo"<div style=\"width:100%; text-align:left; line-height:0.4em;\"><p class=\"p_title\">City</p><p class=\"p_input\">$city</p></div>";}
echo"<div id=\"separator\" style=\"width:100%; height:0.2em; background-color:#FAFAFA;\"></div>";
}

if((!empty($status))OR(!empty($datesent))OR(!empty($datereceived))OR(!empty($orderid))OR(!empty($carrier))){
if(!empty($origin)){echo"<div style=\"width:100%; text-align:left; line-height:0.4em;\"><p class=\"p_title\">Shipped from</p><p class=\"p_input\">$origin</p></div>";}
if(!empty($status)){echo"<div style=\"width:100%; text-align:left; line-height:0.4em;\"><p class=\"p_title\">Status</p><p class=\"p_input\">$status</p></div>";}
if(!empty($datesent)){echo"<div style=\"width:100%; text-align:left; line-height:0.4em;\"><p class=\"p_title\">Date sent</p><p class=\"p_input\">$datesent</p></div>";}
if(!empty($eta)){echo"<div style=\"width:100%; text-align:left; line-height:0.4em;\"><p class=\"p_title\">ETA</p><p class=\"p_input\">$eta</p></div>";}
if(!empty($datereceived)){echo"<div style=\"width:100%; text-align:left; line-height:0.4em;\"><p class=\"p_title\">Date received</p><p class=\"p_input\">$datereceived</p></div>";}
if(!empty($orderid)){echo"<div style=\"width:100%; text-align:left; line-height:0.4em;\"><p class=\"p_title\">Order ID</p><p class=\"p_input\">$orderid</p></div>";}
if(!empty($carrier)){echo"<div style=\"width:100%; text-align:left; line-height:0.4em;\"><p class=\"p_title\">Carrier</p><p class=\"p_input\">$carrier</p></div>";}
if(!empty($trackingnumber)){echo"<div style=\"width:100%; text-align:left; line-height:0.4em;\"><p class=\"p_title\">Tracking Number</p><p class=\"p_input\">$trackingnumber</p></div>";}
if((!empty($trackingnumber))AND($carrier=="FedEx")){echo"<div style=\"display:inline-block;\"><a href=\"https://www.fedex.com/apps/fedextrack/?action=track&tracknumbers=$trackingnumber\" target=\"_blank\" class=\"ContactSubMenuLinks\">Click here to track shipment</a></div>";}
if((!empty($trackingnumber))AND($carrier=="Flat Rate")){echo"<div style=\"display:inline-block;\"><a href=\"https://tools.usps.com/go/TrackConfirmAction?tRef=fullpage&tLc=2&text28777=&tLabels=$trackingnumber\" target=\"_blank\" class=\"ContactSubMenuLinks\">Click here to track shipment</a></div>";}   
if((!empty($trackingnumber))AND($carrier=="DHL")){echo"<div style=\"display:inline-block;\"><a href=\"http://international.dhl.ca/en/express/tracking.html?brand=DHL&AWB=$trackingnumber\" target=\"_blank\" class=\"ContactSubMenuLinks\">Click here to track shipment</a></div>";}   
if((!empty($trackingnumber))AND($carrier=="UPS")){echo"<div style=\"display:inline-block;\"><a href=\"http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=$trackingnumber\" target=\"_blank\" class=\"ContactSubMenuLinks\">Click here to track shipment</a></div>";}   


echo"<div id=\"separator\" style=\"width:100%; height:0.2em; background-color:#FAFAFA;\"></div>";
}


if((!empty($shippingcost))OR(!empty($clearingcost))){
if(!empty($shippingcost)){echo"<div style=\"width:100%; text-align:left;\"><p class=\"p_title\">Shipping cost</p><p class=\"p_input\" style=\"margin-top:-0.5em;\">". nl2br($shippingcost)."</p></div>"; }
if(!empty($clearingcost)){echo"<div style=\"width:100%; text-align:left;\"><p class=\"p_title\">Clearing cost</p><p class=\"p_input\" style=\"margin-top:-0.5em;\">". nl2br($clearingcost)."</p></div>";}


echo"<div id=\"separator\" style=\"width:100%; height:0.2em; background-color:#FAFAFA;\"></div>";
}

if(!empty($description)){echo"<div style=\"width:100%; text-align:left; line-height:1.1em;\"><p class=\"p_title\">Shipping Description</p><p class=\"p_input\" style=\"margin-top:-10px;\">". nl2br($description)."</p></div>";}

}



/// NEW PHONE FROM CALENDAR  FORM
if(isset($_REQUEST['NewPhoneNoteForm'])){  
    session_start();
    
$PhoneNoteDate=$_REQUEST['NewPhoneNoteForm'];

echo"
<form id=\"NewPhoneNoteForm\" action=\"#\" method=\"post\" autocomplete=\"off\" style=\"text-align:left; padding:5px;\">";



echo"
<div class=\"search-box\" style=\"text-align:left; padding:10px; width:300px;\">
<input type=\"text\" class=\"search-field\" placeholder=\"Name of correspondant\" name=\"FullNameCorrespondant\" autofocus style=\"background-color:transparent; border:0px; font-size:1em; color:gray; width:90%; outline:none;\">
<div class=\"result\" style=\"background-color:white;\"></div>
</div> 
";


echo"
<input type=\"hidden\" id=\"ContactID\" name=\"ContactID\">

<div class=\"fieldsinline\">
<label class=\"input\">
  Date
  <input type=\"date\" name=\"phonenotedate\" style=\"width:200px;\" value=\"$PhoneNoteDate\" required/>
</label>
</div>
<div class=\"contactseparator\"></div>

<div class=\"fieldsinline\">
<label class=\"input\" style=\"text-align:left;\">
  <span>Person who made the call</span>
  <input type=\"text\" name=\"caller\" required/>
</label>
</div>

<div class=\"fieldsinline\">
<label class=\"input\" style=\"text-align:left;\">
  <span>People on the phone call</span>
  <input type=\"text\" name=\"peopleoncall\" required/>
</label>
</div>   

<!-- include libraries(jQuery, bootstrap) -->
<link href=\"https://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css\" rel=\"stylesheet\">
<script src=\"https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js\"></script> 
<script src=\"https://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js\"></script> 

<!-- include summernote css/js -->
<link href=\"https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote.css\" rel=\"stylesheet\">
<script src=\"https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote.js\"></script>
<script type=\"text/javascript\">
$(document).ready(function() {
  $('#PhoneNotes').summernote({
  defaultFontName: 'Arial',
  height:200
  });
});
</script>

  <textarea id=\"PhoneNotes\" name=\"phonenotephonenote\" style=\"font-family:Arial; cursor:text;\"/>
  </textarea>

<input type=\"hidden\" name=\"actionform\" value=\"newPhoneNote\">

<hr>

<center>
<button type=\"submit\" class=\"formbutton\" >Import Phone Note</button>
     </center>
</form>

";
}

/////////ACTION TO ADD NEW phonenote

if($_POST['actionform']=="newPhoneNote")
{

$phonenotecontactid = $_POST['ContactID'];

$phonenotedate = $_POST['phonenotedate'];
$phonenotephonenote=mysqli_real_escape_string($con, $_POST['phonenotephonenote']);
$caller = mysqli_real_escape_string($con, $_POST['caller']);
$peopleoncall = mysqli_real_escape_string($con, $_POST['peopleoncall']);

if($phonenotecontactid=="")
{
$phonenotecountry = "";
$phonenotecity = "";
$phonenotefirstname = mysqli_real_escape_string($con, $_POST['FullNameCorrespondant']);
$phonenotelastname = "";
}
else
{
$sql = "SELECT * FROM contacts WHERE id = '$phonenotecontactid'"; 
$result = mysqli_query($con,$sql);
$row = mysqli_fetch_array($result,MYSQLI_ASSOC);

$phonenotecountry = $row['BusinessCountryRegion'];
$phonenotecity=mysqli_real_escape_string($con, $row['BusinessCity']);
$phonenotefirstname = $row['FirstName'];
$phonenotelastname = $row['LastName'];
}


$sql = "INSERT INTO phonenotes (id, country, city, firstname, lastname, date, contactid, phonenote, caller, peopleoncall, Created_By, Date_Created)
VALUES ('', '$phonenotecountry', '$phonenotecity', '$phonenotefirstname', '$phonenotelastname', '$phonenotedate', '$phonenotecontactid', '$phonenotephonenote', '$caller', '$peopleoncall', '$fullusername', '$today')";
if ($con->query($sql) === TRUE)
{
$sentformmessage = "<div class=\"SentFormMessageYes\">The Phone Note with $phonenotefirstname $phonenotelastname was successfully imported.</div>";
$firstname = $_SESSION["FirstName"];
$lastname = $_SESSION["LastName"];
$fulldate = date('l F jS Y h:i:s A');	
$sql2 = "INSERT INTO log (id, name, date, event) VALUES('', '" . $firstname ."' '" . $lastname . "', '" . $fulldate . "','Imported phonenote with ".$phonenotefirstname." ".$phonenotelastname.".');  ";	
if ($con->query($sql2) === TRUE)
{}
}
else{
$sentformmessage = "<div class=\"SentFormMessageNo\">An error occured, the Phone Note with $phonenotefirstname $phonenotelastname was not imported.</div>";
}
//header('location:calendar.php');

	



}
