<?
session_start();
   $path = $_SERVER['DOCUMENT_ROOT'];
   $path .= "/login.php";
   include_once($path);

$calendareventid = $_GET['calendareventid'];

 
$pagetitle ="Event"; 
$AppName = "calendar"; 
$pagetitlename ="Events";

if(!empty($_POST['parentpage'])){$FormParentPage=$_POST['parentpage']; $FormParentCountry=$_POST['parentcountry'];}
elseif(!empty($_GET['parentpage'])){$FormParentPage=$_GET['parentpage']; $FormParentCountry=$_GET['parentcountry'];}


$today = date("Y-m-d");

$sql = "SELECT * FROM apps WHERE name = '$AppName'"; 
$result = mysqli_query($con,$sql);
$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
/// $AppIcon = "https://d29fhpw069ctt2.cloudfront.net/icon/image/84451/preview.svg";
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


/* MARK TASK AS COMPLETE */
if($_POST['actionform']=="MarkComplete")
{
$taskid = $_POST['taskid'];
$tasktitle = $_POST['tasktitle'];
$useremail = $_POST['useremail'];
$FormParentPage = $_POST['FormParentPage'];


$sql = "UPDATE calendarevents SET status='Complete', tagcolor='#DDD' WHERE id='$taskid'";
if ($con->query($sql) === TRUE)
{
   //// Add event for this task's Followers
$sql = "SELECT * FROM calendarevents WHERE id = '$taskid'"; 
$result = mysqli_query($con,$sql);
while($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
{
$followers = explode(',',$row["Following"]);
foreach ($followers as $FollowerEmailAddress) {
$headers = "From: VGR French Dept.<noreply@vgrcanada.org>" . "\r\n";$headers .= "Reply-To: noreply@vgrcanada.org \r\n";$headers .= "MIME-Version: 1.0\r\n";$headers .= "Content-Type: text/html; charset=UTF-8\r\n";$message = '<html><body>';$message .= $Emailcontent;$message .= '</body></html>';
$subject = "Event ".$row["title"]." completed";
$to = $FollowerEmailAddress;
$emailmessage="
<style>
div, p, h1, h2, h3, h4, h5, h6, span{font-family:'Century Gothic';}
a{text-decoration:none; color:gray;}
.canvas{border:1px solid gray; padding:10px; margin:5px; text-align:left;}
.UserPic{
 border-radius:50%;
 width:2em;
 height:2em;
 margin:5px;
 border:1px solid gray;
 display:inline-block;
}
</style>
   <center>
   <h1>French Department<br>Notification</h1>";
$emailmessage.="
<br><br>
The following event has been completed:<br>
<i><b><a href=\"https://vgrcanada.org/apps/Calendar/calendarevent.php?calendareventid=$taskid\">".$row["title"]."</b></i></a>
<div class=\"canvas\" align=\"left\">
Event assigned to: ".$row["assignment"]."<br>
Description: ".$row["description"]."<br>Followers: ";

$Currentfollowers = explode(',',$row["Following"]);
foreach ($Currentfollowers as $FollowerEmailAddress) {
if (strpos($FollowerEmailAddress, '@') !== false) {
 $FollowerEmailAddress = str_replace(' ','',$FollowerEmailAddress) ;

$sql2 = "SELECT * FROM users WHERE Username = '$FollowerEmailAddress'"; 
$result2 = mysqli_query($con,$sql2);
$row2 = mysqli_fetch_array($result2,MYSQLI_ASSOC);
$taskassignmentfullname = $row2['FirstName'];
$taskassignmentfullname .= " "; 
$taskassignmentfullname .= $row2['LastName'];
$taskassignmentpicture = $row2['ProfilePicture'];
$emailmessage.="<img class=\"UserPic\" width=\"50\" src=\"https://www.vgrcanada.org/images/ProfilePicture/$taskassignmentpicture\" title=\"$taskassignmentfullname\">";
} }  

$emailmessage.="</div>
</center>
";
    $mail = mail ($to, $subject, $emailmessage, $headers);
     if ($mail) {
    } else {
    }  
}    
}

if($FormParentPage=="viewcountry"){
header('location:/apps/CountriesOverview/reportsoverview.php?c='.$FormParentCountry.'');
die();
}
elseif($FormParentPage=="calendar"){
header('location:/apps/Calendar/calendar.php');
die();
}
else{
header('location:/apps/Tasks/tasksoverview.php');
die();
}
}
}

/* MARK TASK AS INCOMPLETE */
if($_POST['actionform']=="MarkInComplete")
{
$taskid = $_POST['taskid'];
$tasktitle = $_POST['tasktitle'];
$FormParentPage = $_POST['FormParentPage'];

$sql = "UPDATE calendarevents SET status='In process' WHERE id='$taskid'";
if ($con->query($sql) === TRUE)
{   
if($FormParentPage=="viewcountry"){
header('location:/apps/CountriesOverview/reportsoverview.php?c='.$FormParentCountry.'');
die();
}
elseif($FormParentPage=="calendar"){
header('location:/apps/Calendar/calendar.php');
die();
}
else{
header('location:tasksoverview.php');
die();
}
}
}

///////////// ARCHIVE task
if($_POST['actionform']=="Archive")
{
$taskid = $_POST['taskid'];
$tasktitle = $_POST['tasktitle'];

$sql = "UPDATE calendarevents SET archive='1' WHERE id='$taskid'";
if ($con->query($sql) === TRUE)
{
  //// Add event for this task's Followers
$sql = "SELECT * FROM calendarevents WHERE id = '$taskid'"; 
$result = mysqli_query($con,$sql);
while($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
{
$followers = explode(',',$row["Following"]);
foreach ($followers as $FollowerEmailAddress) {
$headers = "From: VGR French Dept.<noreply@vgrcanada.org>" . "\r\n";$headers .= "Reply-To: noreply@vgrcanada.org \r\n";$headers .= "MIME-Version: 1.0\r\n";$headers .= "Content-Type: text/html; charset=UTF-8\r\n";$message = '<html><body>';$message .= $Emailcontent;$message .= '</body></html>';
$subject = "Event $tasktitle was archived";
$to = $FollowerEmailAddress;
$emailmessage="
<style>
div, p, h1, h2, h3, h4, h5, h6, span{font-family:'Century Gothic';}
a{text-decoration:none; color:gray;}
.canvas{border:1px solid gray; padding:10px; margin:5px; text-align:left;}
.UserPic{
 border-radius:50%;
 width:2em;
 height:2em;
 margin:5px;
 border:1px solid gray;
 display:inline-block;
}
</style>
   <center>
   <h1>French Department<br>Notification</h1>";
$emailmessage.="
<br><br>
The following event has been archived:<br><i><b>
<a href=\"https://vgrcanada.org/apps/Tasks/viewtask.php?taskid=$taskid\">$tasktitle</b></i></a>
<div class=\"canvas\" align=\"left\">
Task assigned to: ".$row["assignment"]."<br>
Description: ".$row["description"]."<br>Followers: ";

$Currentfollowers = explode(',',$row["Following"]);
foreach ($Currentfollowers as $FollowerEmailAddress) {
if (strpos($FollowerEmailAddress, '@') !== false) {
 $FollowerEmailAddress = str_replace(' ','',$FollowerEmailAddress) ;

$sql2 = "SELECT * FROM users WHERE Username = '$FollowerEmailAddress'"; 
$result2 = mysqli_query($con,$sql2);
$row2 = mysqli_fetch_array($result2,MYSQLI_ASSOC);
$taskassignmentfullname = $row2['FirstName'];
$taskassignmentfullname .= " "; 
$taskassignmentfullname .= $row2['LastName'];
$taskassignmentpicture = $row2['ProfilePicture'];
$emailmessage.="<img class=\"UserPic\" width=\"50\" src=\"https://www.vgrcanada.org/images/ProfilePicture/$taskassignmentpicture\" title=\"$taskassignmentfullname\">";
} }  

$emailmessage.="</div>
</center>
";
    $mail = mail ($to, $subject, $emailmessage, $headers);
     if ($mail) {
    } else {
    }  
}    
}

$sentformmessage = "<div class=\"SentFormMessageYes\">The event was successfully Archived.</div>";
$firstname = $_SESSION["FirstName"];
$lastname = $_SESSION["LastName"];
$fulldate = date('l F jS Y h:i:s A');	
$sql2 = "INSERT INTO log (id, name, date, event) VALUES('', '" . $firstname ."' '" . $lastname . "', '" . $fulldate . "',' archived event ".$tasktitle.".');  ";	
if ($con->query($sql2) === TRUE){
if($FormParentPage=="viewcountry"){
header('location:/apps/CountriesOverview/reportsoverview.php?c='.$FormParentCountry.'');
}
else{
header('location:tasksoverview.php');}}
}
else{$sentformmessage = "<div class=\"SentFormMessageNo\">An error occured, the event $tasktitle was not archived.</div>";}
}


///// FOLLOW TASK 
if($_POST['actionform']=="FollowTask")
{
$taskid = $_POST['taskid'];
$tasktitle = $_POST['tasktitle'];
$useremail = $_POST['useremail'];


$sql3 = "SELECT * FROM calendarevents WHERE id = '$taskid' AND Following NOT Like '%$useremail%'";  
$result3 = mysqli_query($con,$sql3); 
$row3 = mysqli_fetch_array($result3,MYSQLI_ASSOC);
$currentviewers = $row3['Following'];
$newviewers = "$useremail, $currentviewers";

$sql = "UPDATE calendarevents SET Following='$newviewers' WHERE id='$taskid'";
if ($con->query($sql) === TRUE)
{
header('location:/apps/Calendar/calendarevent.php?calendareventid='.$taskid.'&sent=follow');
die();	
   
} 
}

///// STOP FOLLOW TASK 
if($_POST['actionform']=="StopFollowTask")
{
$taskid = $_POST['taskid'];
$tasktitle = $_POST['tasktitle'];
$useremail = $_POST['useremail'];


$sql3 = "SELECT * FROM calendarevents WHERE id = '$taskid'";  
$result3 = mysqli_query($con,$sql3); 
$rowcount=mysqli_num_rows($result3); 
while($row3 = mysqli_fetch_array($result3,MYSQLI_ASSOC)){
$currentviewers = $row3['Following'];
}
$useremailWithComa = "$useremail,"; 
$newviewers = str_replace($useremailWithComa,"",$currentviewers);

$sql = "UPDATE calendarevents SET Following='$newviewers' WHERE id='$taskid'";
if ($con->query($sql) === TRUE)
{
header('location:/apps/Calendar/calendarevent.php?calendareventid='.$taskid.'&sent=unfollow');
 die();
}
}



///// ADD A NEW TASK FOLLOWER 
if($_GET['actionform']=="NewTaskFollower")
{
$taskid = $_GET['calendareventid'];
$NewFollower = $_GET['newfollower'];

$sql3 = "SELECT * FROM calendarevents WHERE id = '$taskid' AND Following NOT Like '%$NewFollower%'";  
$result3 = mysqli_query($con,$sql3); 
$row3 = mysqli_fetch_array($result3,MYSQLI_ASSOC);
$currentviewers = $row3['Following'];
$newviewers = "$NewFollower, $currentviewers";

$sql = "UPDATE calendarevents SET Following='$newviewers' WHERE id='$taskid'";
if ($con->query($sql) === TRUE)
{
header('location:/apps/Calendar/calendarevent.php?calendareventid='.$taskid.'&sent=follow');
die();	
   
} 
}


///////////// UNARCHIVE task
if($_POST['actionform']=="Unarchive")
{
$taskid = $_POST['taskid'];
$tasktitle = $_POST['tasktitle'];

$sql = "UPDATE calendarevents SET archive='0' WHERE id='$taskid'";
if ($con->query($sql) === TRUE)
{
   //// Add event for this task's Followers
$sql = "SELECT * FROM calendarevents WHERE id = '$taskid'"; 
$result = mysqli_query($con,$sql);
while($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
{
$followers = explode(',',$row["Following"]);
foreach ($followers as $FollowerEmailAddress) {
$headers = "From: VGR French Dept.<noreply@vgrcanada.org>" . "\r\n";$headers .= "Reply-To: noreply@vgrcanada.org \r\n";$headers .= "MIME-Version: 1.0\r\n";$headers .= "Content-Type: text/html; charset=UTF-8\r\n";$message = '<html><body>';$message .= $Emailcontent;$message .= '</body></html>';
$subject = "Event $tasktitle was unarchived";
$to = $FollowerEmailAddress;
$emailmessage="
<style>
div, p, h1, h2, h3, h4, h5, h6, span{font-family:'Century Gothic';}
a{text-decoration:none; color:gray;}
.canvas{border:1px solid gray; padding:10px; margin:5px; text-align:left;}
.UserPic{
 border-radius:50%;
 width:2em;
 height:2em;
 margin:5px;
 border:1px solid gray;
 display:inline-block;
}
</style>
   <center>
   <h1>French Department<br>Notification</h1>";
$emailmessage.="
<br><br>
The following event has been unarchived:<br><i><b>
<a href=\"https://vgrcanada.org/apps/Calendar/calendarevent.php?calendareventid=$taskid\">$tasktitle</b></i></a>
<div class=\"canvas\" align=\"left\">
Task assigned to: ".$row["assignment"]."<br>
Description: ".$row["description"]."<br>Followers: ";

$Currentfollowers = explode(',',$row["Following"]);
foreach ($Currentfollowers as $FollowerEmailAddress) {
if (strpos($FollowerEmailAddress, '@') !== false) {
 $FollowerEmailAddress = str_replace(' ','',$FollowerEmailAddress) ;

$sql2 = "SELECT * FROM users WHERE Username = '$FollowerEmailAddress'"; 
$result2 = mysqli_query($con,$sql2);
$row2 = mysqli_fetch_array($result2,MYSQLI_ASSOC);
$taskassignmentfullname = $row2['FirstName'];
$taskassignmentfullname .= " "; 
$taskassignmentfullname .= $row2['LastName'];
$taskassignmentpicture = $row2['ProfilePicture'];
$emailmessage.="<img class=\"UserPic\" width=\"50\" src=\"https://www.vgrcanada.org/images/ProfilePicture/$taskassignmentpicture\" title=\"$taskassignmentfullname\">";
} }   

$emailmessage.="</div>
</center>
";
    $mail = mail ($to, $subject, $emailmessage, $headers);
     if ($mail) {
    } else {
    }  
}    
}

$sentformmessage = "<div class=\"SentFormMessageYes\">The event $tasktitle was successfully Unarchived.</div>";
$firstname = $_SESSION["FirstName"];
$lastname = $_SESSION["LastName"];
$fulldate = date('l F jS Y h:i:s A');	
$sql2 = "INSERT INTO log (id, name, date, event) VALUES('', '" . $firstname ."' '" . $lastname . "', '" . $fulldate . "',' Unarchived event ".$tasktitle.".');  ";	
if ($con->query($sql2) === TRUE){}}
else{$sentformmessage = "<div class=\"SentFormMessageNo\">An error occured, the event $tasktitle was not Unarchived.</div>";}
}

///////////// DELETE event
if($_POST['actionform']=="delete")
{
$calendareventid = $_POST['calendareventid'];
$calendareventcalendarevent = $_POST['calendareventtitle'];
$calendareventapp = $_POST['calendareventapp'];

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
///////////// DELETE event  WITH  GET METHOD
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


/* GET VARIABLES FROM calendarevents */
$sql = "SELECT * FROM calendarevents WHERE id = '$calendareventid' order by id DESC"; 
$result = mysqli_query($con,$sql);
$count=mysqli_num_rows($result);

if($count==0){header('location:calendar.php');}

$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
$calendareventid = $row['id'];

$calendareventtitle = $row['title'];
$calendareventdescription = $row['description'];
$calendareventtagcolor = $row['tagcolor'];
$calendareventassignment = $row['assignment'];
    $taskassignment = $calendareventassignment;
$followers = $row['Following'];
$taskstatus = $row['status'];

$Created_By = $row['Created_By'];
$Date_Created = $row['Date_Created'];
$Last_Modified_By = $row['Last_Modified_By'];
$Date_Last_Modified = $row['Date_Last_Modified'];

$calendareventyear = $row['year'];
$calendareventmonth = $row['month'];
$calendareventday = $row['day'];
if(strlen($calendareventday)>1){$calendareventdayFormat=$calendareventday;}else{$calendareventdayFormat="0$calendareventday";}

$calendareventdate = "$calendareventyear-$calendareventmonth-$calendareventdayFormat";

if(!empty($row['endyear'])){
$calendareventendyear = $row['endyear'];
$calendareventendmonth = $row['endmonth'];
$calendareventendday = $row['endday'];
if(strlen($calendareventendday)>1){$calendareventenddayFormat=$calendareventendday;}else{$calendareventenddayFormat="0$calendareventendday";}

$calendareventenddate = "$calendareventendyear-$calendareventendmonth-$calendareventenddayFormat";
}



if($FormParentPage=="viewcountry"){
$ParentPage = "reportsoverview.php?c=$FormParentCountry";
}
else{
$ParentPage = "calendar.php";
}
   $path = $_SERVER['DOCUMENT_ROOT'];
   $path .= "/header.php";
   include_once($path);


   if (strpos($followers, $useremail) !== false) 
{
 $userfollowing = 1;
}
else{
 $userfollowing = 0;
}


?>
<style>
.p_title{
color:black;
padding-top:10px;  
font-weight:bold;  
}
.p_input a{
color:#007AFF;
}
</style>

    <div class="centercontent">
<?
echo $sentformmessage;       

if((!empty($Created_By))OR(!empty($Last_Modified_By))){
echo"<div style=\"float:left;\">";
if(!empty($Created_By)){echo"<div style=\"width:100%; text-align:left; line-height:0.7em; color:lightgray; font-style:italic; font-size:0.8em;\"><p class=\"\">Created by $Created_By on $Date_Created</p></div>";}
if(!empty($Last_Modified_By)){echo"<div style=\"width:100%; text-align:left; line-height:0.7em; color:lightgray; font-style:italic; font-size:0.8em;\"><p class=\"\">Last modified by $Last_Modified_By on $Date_Last_Modified</p></div>";}
echo"</div><br>";
} 

echo" 
<div style=\"position:relative; float:right; width:70px;\">
<div onclick=\"location.href='calendareventedit.php?calendareventid=$calendareventid&action=edit';\" class=\"divbluebutton\" style=\"float:right; display:inline-block;\">Edit</div>";

echo"
<form name=\"GoMakeComplete$calendareventid\" action=\"calendarevent.php\" method=\"post\">
<input type=\"hidden\" name=\"taskid\" value=\"$calendareventid\">
<input type=\"hidden\" name=\"tasktitle\" value=\"$calendareventtitle\">
<input type=\"hidden\" name=\"useremail\" value=\"$useremail\">
<input type=\"hidden\" name=\"FormParentPage\" value=\"calendar\">
<input type=\"hidden\" name=\"actionform\" value=\"MarkComplete\">
</form>

<form name=\"MarkInComplete$calendareventid\" action=\"calendarevent.php\" method=\"post\">
<input type=\"hidden\" name=\"taskid\" value=\"$calendareventid\">
<input type=\"hidden\" name=\"tasktitle\" value=\"$calendareventtitle\">
<input type=\"hidden\" name=\"useremail\" value=\"$useremail\">
<input type=\"hidden\" name=\"FormParentPage\" value=\"calendar\">
<input type=\"hidden\" name=\"actionform\" value=\"MarkInComplete\">
</form>
"; 

echo"<br><br><div onclick=\"document.forms['";if($taskstatus=="Complete"){echo"MarkInComplete$calendareventid";}else{echo"GoMakeComplete$calendareventid";} echo"'].submit();\" class=\"";if($taskstatus=="Complete"){echo"CompletedTaskButton";}else{echo"CompleteTaskButton";} echo"\" style=\"width:50px; height:50px; font-size:2.5em; \" title=\"Make Comlete\"><span class=\"glyphicon glyphicon-ok\"></span></div><br><br>";


echo"</div>
                         
<div style=\"width:100%; text-align:center;\">
<div style=\"color:black; text-align:center; position:relative; left:0; right:0; margin:auto; font-size:2em; padding-top:0.5em; display:inline-block;\">$calendareventcalendarevent</div>
</div>

<div id=\"separator\" style=\"width:100%; height:0.5em; background-color:#FAFAFA;\"></div>";



if(!empty($calendareventtitle)){echo"<div style=\"width:100%; text-align:left; line-height:0.4em;\"><p class=\"p_title\">Title</p><p class=\"p_input\">$calendareventtitle</p></div>";}
if(!empty($calendareventdate)){echo"<div style=\"width:100%; text-align:left; line-height:0.4em;\"><p class=\"p_title\">Date</p><p class=\"p_input\">$calendareventdate</p></div>";}
if(!empty($calendareventenddate)){echo"<div style=\"width:100%; text-align:left; line-height:0.4em;\"><p class=\"p_title\">Finish Date</p><p class=\"p_input\">$calendareventenddate</p></div>";}

if(!empty($taskassignment)){
if(!empty($taskassignment)){
$sql0 = "SELECT * FROM users WHERE Username = '$taskassignment'"; 
$result0 = mysqli_query($con,$sql0);
$row0 = mysqli_fetch_array($result0,MYSQLI_ASSOC);
$taskassignmentfullname = $row0['FirstName'];
$taskassignmentfullname .= " "; 
$taskassignmentfullname .= $row0['LastName'];
$taskassignmentpicture = $row0['ProfilePicture'];
}
echo"<div style=\"width:100%; text-align:left;\"><p class=\"p_title\">Assignment</p>
<div style=\"min-width:300px;\">
<div class=\"Assigned ContactSubMenu\" style=\"position:relative; display:block; background-image: url('/images/ProfilePicture/$taskassignmentpicture'); background-position:top center; background-size:cover; background-repeat:no-repeat; margin:0 0 0 20px; text-align:center;\">";
echo"
  <div class=\"ContactSubMenuContent\">";  
  echo"<div class=\"ContactSubMenuLinks\""; if($taskassignment==""){echo"style=\"color:black; font-weight:bold; text-decoration:underline;\"";} echo"><a href=\"calendareventedit.php?taskid=$calendareventid&tasktitle=$calendareventtitle&assign=y&user=\">No assignment</a></div>";  
  echo"<div class=\"ContactSubMenuLinks\""; if($taskassignment=="stevek@branham.org"){echo"style=\"color:black; font-weight:bold; text-decoration:underline;\"";} echo"><a href=\"calendareventedit.php?taskid=$calendareventid&tasktitle=$calendareventtitle&assign=y&user=stevek@branham.org\">Steve</a></div>";
  echo"<div class=\"ContactSubMenuLinks\""; if($taskassignment=="mathieu@vgroffice.org"){echo"style=\"color:black; font-weight:bold; text-decoration:underline;\"";} echo"><a href=\"calendareventedit.php?taskid=$calendareventid&tasktitle=$calendareventtitle&assign=y&user=mathieu@vgroffice.org\">Mathieu</a></div>";
  echo"<div class=\"ContactSubMenuLinks\""; if($taskassignment=="guy@vgroffice.org"){echo"style=\"color:black; font-weight:bold; text-decoration:underline;\"";} echo"><a href=\"calendareventedit.php?taskid=$calendareventid&tasktitle=$calendareventtitle&assign=y&user=guy@vgroffice.org\">Guy</a></div>";
  echo"<div class=\"ContactSubMenuLinks\""; if($taskassignment=="ruth@branham.org"){echo"style=\"color:black; font-weight:bold; text-decoration:underline;\"";} echo"><a href=\"calendareventedit.php?taskid=$calendareventid&tasktitle=$calendareventtitle&assign=y&user=ruth@branham.org\">Ruth</a></div>"; 
  echo"<div class=\"ContactSubMenuLinks\""; if($taskassignment=="claudine@vgroffice.org"){echo"style=\"color:black; font-weight:bold; text-decoration:underline;\"";} echo"><a href=\"calendareventedit.php?taskid=$calendareventid&tasktitle=$calendareventtitle&assign=y&user=claudine@vgroffice.org\">Claudine</a></div>";
  echo"<div class=\"ContactSubMenuLinks\""; if($taskassignment=="alix@vgroffice.org"){echo"style=\"color:black; font-weight:bold; text-decoration:underline;\"";} echo"><a href=\"calendareventedit.php?taskid=$calendareventid&tasktitle=$calendareventtitle&assign=y&user=alix@vgroffice.org\">Alix</a></div>";     
  echo"<div class=\"ContactSubMenuLinks\""; if($taskassignment=="denis@vgroffice.org"){echo"style=\"color:black; font-weight:bold; text-decoration:underline;\"";} echo"><a href=\"calendareventedit.php?taskid=$calendareventid&tasktitle=$calendareventtitle&assign=y&user=denis@vgroffice.org\">Denis</a></div>";
  echo"<div class=\"ContactSubMenuLinks\""; if($taskassignment=="1937@vgroffice.org"){echo"style=\"color:black; font-weight:bold; text-decoration:underline;\"";} echo"><a href=\"calendareventedit.php?taskid=$calendareventid&tasktitle=$calendareventtitle&assign=y&user=1937@vgroffice.org\">George</a></div>";
  echo"</div>
</div>
<p class=\"p_input\" style=\"display:block; font-weight:bold; font-size:1.2em;\">$taskassignmentfullname</p></div>
</div>       
";}



echo"<div style=\"width:100%; text-align:left; line-height:0.4em;\"><p class=\"p_title\">Color</p><p class=\"p_input\" style=\"background-color:$calendareventtagcolor; width:30px; height:25px;\"></p></div>";

if(!empty($calendareventdescription)){echo"<div style=\"width:100%; text-align:left; line-height:1.1em;\"><p class=\"p_title\">Description</p><p class=\"p_input\" style=\"margin-top:-10px;\">$calendareventdescription</p></div>";}

echo"<div id=\"separator\" style=\"width:100%; height:0.2em; background-color:#FAFAFA;\"></div>";




echo"
<hr style=\"margin-top:10px;\">
<form action=\"\" onsubmit=\"return confirm('Permanently delete this event?');\" method=\"post\" style=\"text-align:left;\">
<center>
<button type=\"submit\" class=\"formbuttondelete\">Delete</button>
</center> 
<input type=\"hidden\" name=\"calendareventid\" value=\"$calendareventid\">
<input type=\"hidden\" name=\"calendareventtitle\" value=\"$calendareventtitle\">
<input type=\"hidden\" name=\"actionform\" value=\"delete\">
</form> ";

////////  FOLLOWING BUTTON
echo"
<div style=\"float:right;\">
<form action=\"\");\" method=\"post\" style=\"text-align:left;\">
<button type=\"submit\" class=\"formbutton\" style=\"font-size:1em;\">"; if($userfollowing=='0'){echo"<i class=\"far fa-bell\"></i> Follow task";}else{echo"<span style=\"color:gray;\"><i class=\"fas fa-bell\"></i> Following</span>";} echo"</button>
<input type=\"hidden\" name=\"taskid\" value=\"$calendareventid\">
<input type=\"hidden\" name=\"tasktitle\" value=\"$calendareventtitle\">
<input type=\"hidden\" name=\"useremail\" value=\"$useremail\">
<input type=\"hidden\" name=\"actionform\" value=\""; if($userfollowing=='0'){echo"FollowTask";}else{echo"StopFollowTask";} echo"\">
</form>
</div>
";


/////////  FOLLOWERS SECTION
echo"
<style>
.UserPic{
 border-radius:50%;
 width:2em;
 height:2em;
 margin:5px;
 border:1px solid gray;
 display:inline-block;
}

.NewFollowersButton:hover>.NewFollowersMenu{
 display:inline-block;
 cursor:pointer;
}

.NewFollowersButton:hover{
 cursor:pointer;
}
 
.NewFollowersMenu{
display:none;
border:1px solid lightgray;
border-radius:5px;
padding:5px;
position:absolute;
font-size:0.5em;
background-color:white;
z-index:1000;
}
</style>

<div class=\"fas fa-plus-circle NewFollowersButton\" style=\"margin:0 10px 0 0; padding:0; float:left; font-size:2em; color:gray;\">
<div class=\"NewFollowersMenu\">";
$sql = "SELECT * FROM users WHERE ProfilePicture != ''"; 
$result = mysqli_query($con,$sql);
while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
   //Remove Jonathan Phillips from Results
   if($row['FirstName']=="Jonathan"){continue;}
 $taskassignmentfullname = $row['FirstName'];
$taskassignmentfullname .= " "; 
$taskassignmentfullname .= $row['LastName'];
echo "<a href=\"?calendareventid=$calendareventid&actionform=NewTaskFollower&newfollower=".$row["Username"]."\"><img class=\"UserPic\" src=\"/images/ProfilePicture/".$row["ProfilePicture"]."\" title=\"$taskassignmentfullname\"></a>"; 
}
echo"</div>
</div>";

if(!empty($followers)){echo"
<div style=\"float:left; color:gray; display:inline-block; padding:10px 10px 0 0;\">
Followers:
</div>
"; }

$Currentfollowers = explode(',',$followers);
foreach ($Currentfollowers as $FollowerEmailAddress) {
if (strpos($FollowerEmailAddress, '@') !== false) {
 $FollowerEmailAddress = str_replace(' ','',$FollowerEmailAddress) ;

$sql = "SELECT * FROM users WHERE Username = '$FollowerEmailAddress'"; 
$result = mysqli_query($con,$sql);
$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
$taskassignmentfullname = $row['FirstName'];
$taskassignmentfullname .= " "; 
$taskassignmentfullname .= $row['LastName'];
$taskassignmentpicture = $row['ProfilePicture'];
echo"<img class=\"UserPic\" src=\"/images/ProfilePicture/$taskassignmentpicture\" title=\"$taskassignmentfullname\">";
} }   


?>

</div>

<?
}
?>

</html>