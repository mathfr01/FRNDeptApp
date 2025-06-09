<?php

///////////// DELETE event
if($_GET['actionform']=="delete")
{
$calendareventid = $_GET['calendareventid'];
$calendareventcalendarevent = $_GET['calendareventtitle'];
$calendareventapp = $_GET['calendareventapp'];

	$sql = "DELETE FROM calendarevents WHERE id='$calendareventid'";
 if ($con->query($sql) === TRUE) {
$sentformmessage = "<div class=\"SentFormMessageYes\">Event deleted successfully.</div>";
$firstname = $_SESSION["FirstName"];
$lastname = $_SESSION["LastName"];
$fulldate = date('l F jS Y h:i:s A');	
$sql2 = "INSERT INTO log (id, name, date, event) VALUES('', '" . $firstname ."' '" . $lastname . "', '" . $fulldate . "','Deleted event $calendareventcalendarevent.');  ";	
if ($con->query($sql2) === TRUE)
{
if($FormParentPage=="viewcountry"){
header('location:reportsoverview.php?c='.$FormParentCountry.'');
}
else{
header('location:calendar.php');
}
}
} else {
    $sentformmessage = "<div class=\"SentFormMessageNo\">An error occured, the event was not deleted.</div>";
} 
  
}



?>
