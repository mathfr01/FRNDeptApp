<?
//calendareventedit.php
session_start();
   $path = $_SERVER['DOCUMENT_ROOT'];
   $path .= "/login.php";
   include_once($path);

$imagepath = "images/";

$pagetitle ="Event"; 
$AppName = "calendar"; 
$pagetitlename ="Event";


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

$firstname = $_SESSION["FirstName"];
$lastname = $_SESSION["LastName"];
$fullusername = "$firstname $lastname";

///////////// ASSIGN TASK 
if($_GET['assign']=="y")
{
$taskid = $_GET['taskid'];
$tasktitle = $_GET['tasktitle'];
$taskassignment = $_GET['user'];

$sql = "UPDATE calendarevents SET assignment='$taskassignment' WHERE id='$taskid'";
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
$subject = "Task $tasktitle was re-assigned";
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
The following task has been re-assigned to $taskassignment:<br><i><b>
<a href=\"https://vgrcanada.org/apps/Calendar/calendarevent.php?calendareventid=$taskid\">$tasktitle</b></i></a>
<div class=\"canvas\"  align=\"left\">";

if(!empty($row["description"])){
$emailmessage .=" Description: ".$row["description"]."<br>Followers: ";
}
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

////// SENT FORM MESSAGE
$sentformmessage = "<div class=\"SentFormMessageYes\">The task was successfully assigned.</div>";
$firstname = $_SESSION["FirstName"];
$lastname = $_SESSION["LastName"];
$fulldate = date('l F jS Y h:i:s A');	
$sql2 = "INSERT INTO log (id, name, date, event) VALUES('', '" . $firstname ."' '" . $lastname . "', '" . $fulldate . "',' assigned task ".$tasktitle." to ".$taskassignment.".');  ";	
if ($con->query($sql2) === TRUE){
header('location:/apps/Calendar/calendarevent.php?calendareventid=$taskid');
}
else{$sentformmessage = "<div class=\"SentFormMessageNo\">An error occured, the task $tasktitle was not assigned.</div>";}
}}

/////////NEW calendarevent
if($_POST['actionform']=="new")
{

$calendareventtitle=mysqli_real_escape_string($con, $_POST['title']);

$calendareventdate = $_POST['date'];
$tempdate = DateTime::createFromFormat("Y-m-d", $calendareventdate);
$calendareventyear= $tempdate->format("Y");
$calendareventmonth= $tempdate->format("m");
$calendareventday= $tempdate->format("d"); 


$result = mysqli_query($con, "SELECT * FROM calendarevents WHERE title = '$calendareventtitle' AND year = '$calendareventyear' AND month = '$calendareventmonth' AND day = '$calendareventday'");
$rowcount=mysqli_num_rows($result);
if($rowcount==0) {                                                                                                     

$calendareventdescription=mysqli_real_escape_string($con, $_POST['description']);
$calendareventtagcolor = $_POST['tagcolor'];
$calendareventassignment=mysqli_real_escape_string($con, $_POST['assignment']);

/*
$calendareventdate = $_POST['date'];
$tempdate = DateTime::createFromFormat("Y-m-d", $calendareventdate);
$calendareventyear= $tempdate->format("Y");
$calendareventmonth= $tempdate->format("m");
$calendareventday= $tempdate->format("d"); 
*/

if(!empty($_POST['enddate'])){
$calendareventenddate = $_POST['enddate'];
$tempenddate = DateTime::createFromFormat("Y-m-d", $calendareventenddate);
$calendareventendyear= $tempenddate->format("Y");
$calendareventendmonth= $tempenddate->format("m");
$calendareventendday= $tempenddate->format("d"); 
}

$sql = "INSERT INTO calendarevents (id, title, description, year, month, day, endyear, endmonth, endday, tagcolor, Created_By, Date_Created, assignment)
VALUES ('', '$calendareventtitle', '$calendareventdescription', '$calendareventyear', '$calendareventmonth', '$calendareventday', '$calendareventendyear', '$calendareventendmonth', '$calendareventendday', '$calendareventtagcolor', '$fullusername', '$today', '$calendareventassignment')";
if ($con->query($sql) === TRUE)
{
$sentformmessage = "<div class=\"SentFormMessageYes\">The event was successfully imported.</div>";
$firstname = $_SESSION["FirstName"];
$lastname = $_SESSION["LastName"];
$fulldate = date('l F jS Y h:i:s A');	
$sql2 = "INSERT INTO log (id, name, date, event) VALUES('', '" . $firstname ."' '" . $lastname . "', '" . $fulldate . "','Imported event ".$calendareventtitle.".');  ";	
if ($con->query($sql2) === TRUE)
{}
$calendareventapp = $_POST['app'];
}
else{
$sentformmessage = "<div class=\"SentFormMessageNo\">An error occured, the eventwas not imported.</div>";
$calendareventapp = $_POST['app'];
}

} else {
$sentformmessage = "<div class=\"SentFormMessageNo\">This event already existed. It was not imported.</div>";
$calendareventapp = $_POST['app'];
}
	

}
/////////DUPLICATE calendarevent
if($_GET['actionform']=="duplicate")
{
$calendareventid = $_GET['calendareventid'];

$sql = "SELECT * FROM calendarevents WHERE id = '$calendareventid'"; 
$result = mysqli_query($con,$sql);
$row = mysqli_fetch_array($result,MYSQLI_ASSOC);

$calendareventtitle = $row['title'];
$Newcalendareventtitle = "$calendareventtitle 2";
$calendareventdescription = $row['description'];
$calendareventtagcolor = $row['tagcolor'];
$calendareventassignment = $row['assignment'];

/*Start Date*/
$calendareventyear = $row['year'];
$calendareventmonth = $row['month'];
$calendareventday = $row['day'];
if(strlen($calendareventday)>1){$calendareventdayFormat=$calendareventday;}else{$calendareventdayFormat="0$calendareventday";}
$calendareventFullDate = "$calendareventyear-$calendareventmonth-$calendareventdayFormat";

/*End Date*/
$calendareventendyear = $row['endyear'];
$calendareventendmonth = $row['endmonth'];
$calendareventendday = $row['endday'];

$sql = "INSERT INTO calendarevents (id, title, description, year, month, day, endyear, endmonth, endday, tagcolor, Created_By, Date_Created, assignment)
VALUES ('', '$Newcalendareventtitle', '$calendareventdescription', '$calendareventyear', '$calendareventmonth', '$calendareventday', '', '', '', '$calendareventtagcolor', '$fullusername', '$today', '$calendareventassignment')";
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


///////////// DELETE calendarevent
if($_POST['actionform']=="delete")
{
$calendareventid = $_POST['calendareventid'];
$calendareventtitle = $_POST['title'];
	$sql = "DELETE FROM calendarevents WHERE id='$calendareventid'";
 if ($con->query($sql) === TRUE) {
$sentformmessage = "<div class=\"SentFormMessageYes\">Event deleted successfully.</div>";
$firstname = $_SESSION["FirstName"];
$lastname = $_SESSION["LastName"];
$fulldate = date('l F jS Y h:i:s A');	
$sql2 = "INSERT INTO log (id, name, date, event) VALUES('', '" . $firstname ."' '" . $lastname . "', '" . $fulldate . "','Deleted event ".$calendareventtitle.".');  ";	
if ($con->query($sql2) === TRUE)
{}
} else {
    $sentformmessage = "<div class=\"SentFormMessageNo\">An error occured, the event was not deleted.</div>";
} 
  
}



//////DO EDIT calendarevent
if($_POST['actionform']=="edit")
{
$calendareventid = $_POST['id'];
$calendareventtitle=mysqli_real_escape_string($con, $_POST['title']);                                                                                                  
$calendareventdescription=mysqli_real_escape_string($con, $_POST['description']);
$calendareventtagcolor = $_POST['tagcolor'];
$calendareventdate = $_POST['date'];   
$calendareventassignment=mysqli_real_escape_string($con, $_POST['assignment']);

$tempdate = DateTime::createFromFormat("Y-m-d", $calendareventdate);
$calendareventyear= $tempdate->format("Y");
$calendareventmonth= $tempdate->format("m");
$calendareventday= $tempdate->format("d"); 

if(!empty($_POST['enddate'])){
$calendareventenddate = $_POST['enddate'];
$tempenddate = DateTime::createFromFormat("Y-m-d", $calendareventenddate);
$calendareventendyear= $tempenddate->format("Y");
$calendareventendmonth= $tempenddate->format("m");
$calendareventendday= $tempenddate->format("d"); 
}

$sql = "UPDATE calendarevents SET title='$calendareventtitle', description='$calendareventdescription', year='$calendareventyear', month='$calendareventmonth', day='$calendareventday', endyear='$calendareventendyear', endmonth='$calendareventendmonth', endday='$calendareventendday', tagcolor='$calendareventtagcolor', Last_Modified_By='$fullusername', Date_Last_Modified='$today', assignment='$calendareventassignment' WHERE id='$calendareventid'";
if ($con->query($sql) === TRUE)
{
 //// Add event for this task's Followers
$sql = "SELECT * FROM calendarevents WHERE id = '$calendareventid'"; 
$result = mysqli_query($con,$sql);
while($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
{
$followers = explode(',',$row["Following"]);
foreach ($followers as $FollowerEmailAddress) {
$headers = "From: VGR French Dept.<noreply@vgrcanada.org>" . "\r\n";$headers .= "Reply-To: noreply@vgrcanada.org \r\n";$headers .= "MIME-Version: 1.0\r\n";$headers .= "Content-Type: text/html; charset=UTF-8\r\n";$message = '<html><body>';$message .= $Emailcontent;$message .= '</body></html>';
$subject = "Event $calendareventtitle Update notification";
$to = $FollowerEmailAddress;

$text = preg_replace("/<img[^>]+\>/i", "(image) ", $row['description']);

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
<br>
The following event has been updated:<br><i><b>
<a href=\"https://vgrcanada.org/apps/Calendar/calendarevent.php?calendareventid=$calendareventid\">$calendareventtitle</b></i></a>

<div class=\"canvas\" align=\"left\">
Task assigned to: ".$row["assignment"]."<br>
Description: ".wordwrap($text, 70, "\r\n")."<br>Followers: ";

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



$sentformmessage = "<div class=\"SentFormMessageYes\">The event was successfully updated.</div>";
$firstname = $_SESSION["FirstName"];
$lastname = $_SESSION["LastName"];
$fulldate = date('l F jS Y h:i:s A');	
$sql2 = "INSERT INTO log (id, name, date, event) VALUES('', '" . $firstname ."' '" . $lastname . "', '" . $fulldate . "','Modified event ".$calendareventtitle.".');  ";	
if ($con->query($sql2) === TRUE)
{
header('location:calendarevent.php?calendareventid='.$calendareventid.'');
}

}
else{
$sentformmessage = "<div class=\"SentFormMessageNo\">An error occured, the event $calendareventtitle was not updated.</div>";
}


}

///////EDIT calendarevent
if($_GET['action']=="edit")
{
$calendareventid=$_GET['calendareventid'];
$sql = "SELECT * FROM calendarevents WHERE id = '$calendareventid'"; 
$result = mysqli_query($con,$sql);
$row = mysqli_fetch_array($result,MYSQLI_ASSOC);

$calendareventtitle = $row['title'];
$calendareventdescription = $row['description'];
$calendareventtagcolor = $row['tagcolor'];
$calendareventassignment = $row['assignment'];

/*Start Date*/
$calendareventyear = $row['year'];
$calendareventmonth = $row['month'];
$calendareventday = $row['day'];
if(strlen($calendareventday)>1){$calendareventdayFormat=$calendareventday;}else{$calendareventdayFormat="0$calendareventday";}
$calendareventFullDate = "$calendareventyear-$calendareventmonth-$calendareventdayFormat";

/*End Date*/
$calendareventendyear = $row['endyear'];
$calendareventendmonth = $row['endmonth'];
$calendareventendday = $row['endday'];
if(strlen($calendareventendday)>1){$calendareventenddayFormat=$calendareventendday;}else{$calendareventenddayFormat="0$calendareventendday";}
$calendareventFullendDate = "$calendareventendyear-$calendareventendmonth-$calendareventenddayFormat";
}

$ParentPage = "calendarevent.php?calendareventid=$calendareventid";

   $path = $_SERVER['DOCUMENT_ROOT'];
   $path .= "/header.php";
   include_once($path);

?>

    <div class="centercontent tables">
<?
echo $sentformmessage;
?>
        <div class="page_items_holder">
        
<style type="text/css">
.input {
  display: block;
}
.input span {
  position: absolute;
  z-index: 1;
  cursor: text;
  calendareventer-events: none;
  color: #999;
  /* Input padding + input border */
  padding: 7px;
  /* Firefox does not respond well to different line heights. Use padding instead. */
  line-height: 17px;
  /* This gives a little gap between the cursor and the label */
  margin-left: 2px;
}
.input input, .input select {
  z-index: 0;
 // width:230px;
  padding: 6px;
  margin: 0;
  margin-bottom:0.5em;
  font: inherit;
  line-height: 17px;
}



.input select {
  padding: 5px;
  /* Unfortunately selects don't respond well to padding. They calendarevent an explicit height. */
  height: 31px;
}

.fieldsinline {display:inline-block; margin-right:5px; margin-top:10px; width:100%;}
.fieldsinline input{border:none; width:100%;}
</style> 

<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script>
(function($) {

  function measureWidth(deflt) {
    var dummy = $('<label></label>').text(deflt).css('visibility','hidden').appendTo(document.body);
    var result = dummy.width();
    dummy.remove();
    return result;
  }

  function toggleLabel() {
    var input = $(this);
    var deflt = input.attr('title');
    var span = input.prev('span');
    setTimeout(function() {
      if (!input.val() || (input.val() == deflt)) {
        span.css('visibility', '');
        if (deflt) {
          span.css('margin-left', measureWidth(deflt) + 2 + 'px');
        }
      } else {
        span.css('visibility', 'hidden');
      }
    }, 0);
  };

  $(document).on('cut', 'input, textarea', toggleLabel);
  $(document).on('keydown', 'input, textarea', toggleLabel);
  $(document).on('paste', 'input, textarea', toggleLabel);
  $(document).on('change', 'select', toggleLabel);

  $(document).on('focusin', 'input, textarea', function() {$(this).prev('span').css('color', '#ccc');});
  $(document).on('focusout', 'input, textarea', function() {$(this).prev('span').css('color', '#999');
  });

  function init() {
    $('input, textarea, select').each(toggleLabel);
  };

  // Set things up as soon as the DOM is ready.
  $(init);

  // Do it again to detect Chrome autofill.
  $(window).load(function() {
    setTimeout(init, 0);
  });

})(jQuery);


</script>
<script>

document.onkeydown = checkKey;

function checkKey(e) {

    e = e || window.event;

  if (e.ctrlKey && e.keyCode == 13) {
    // Ctrl-Enter pressed
        document.getElementById("calendareventsForm").submit();    
  }

}

</script>


    
<form id="calendareventsForm" action="calendareventedit.php" method="post" style="text-align:left;">

<div class="fieldsinline">
<label class="input">
  <span><? echo $calendareventapp; ?> Title</span>
  <input type="text" name="title" <?if($_GET['action']=="edit"){echo"value=\"$calendareventtitle\"";}?> required/>
</label>
</div>
<div class="contactseparator"></div>


<div class="fieldsinline">
<label class="input">
  Date
  <input type="date" name="date" <?if($_GET['action']=="edit"){echo"value=\"$calendareventFullDate\"";}elseif(isset($_GET['evdate'])){echo"value=\"".$_GET['evdate']."\"";}?> style="width:150px;" required/>       
</label>
</div>
<div class="contactseparator"></div>

<div class="fieldsinline">
<label class="input">
  Finish Date
  <input type="date" name="enddate" <?if($_GET['action']=="edit"){echo"value=\"$calendareventFullendDate\"";}?> style="width:150px;"/>       
</label>
</div>
<div class="contactseparator"></div>

<div class="fieldsinline">
<label class="input">
  <span>Assignment</span>
  <select type="text" name="assignment" style="width:150px;" title="Assignment" />
  <option></option>
  <option<? if($calendareventassignment=="No assignment"){echo" selected";} ?>>No assignment</option>
  <option<? if($calendareventassignment=="stevek@branham.org"){echo" selected";} ?> value="stevek@branham.org">Steve</option>
  <option<? if($calendareventassignment=="mathieu@vgroffice.org"){echo" selected";} ?> value="mathieu@vgroffice.org">Mathieu</option>
  <option<? if($calendareventassignment=="guy@vgroffice.org"){echo" selected";} ?> value="guy@vgroffice.org">Guy</option>
  <option<? if($calendareventassignment=="ruth@branham.org"){echo" selected";} ?> value="ruth@branham.org">Ruth</option>
  <option<? if($calendareventassignment=="claudine@vgroffice.org"){echo" selected";} ?> value="claudine@vgroffice.org">Claudine</option>
  <option<? if($calendareventassignment=="alix@vgroffice.org"){echo" selected";} ?> value="alix@vgroffice.org">Alix</option>
  <option<? if($calendareventassignment=="denis@vgroffice.org"){echo" selected";} ?> value="denis@vgroffice.org">Denis</option>
  <option<? if($calendareventassignment=="1937@vgroffice.org"){echo" selected";} ?> value="1937@vgroffice.org">George</option>
  </select>
</label>
</div>
<div class="contactseparator"></div>


<div class="fieldsinline">
<label class="input">
  Color
  <input type="color" name="tagcolor" id="tagcolor" <?if($_GET['action']=="edit"){echo"value=\"$calendareventtagcolor\"";}else{echo"value=\"#ff8000\"";}?> style="width:30px; height:25px; padding:0; margin:0;"/>       
</label>
</div>

<script>
function ChoosePreviousColor(ChosenColor){
document.getElementById('tagcolor').value = ChosenColor;
}
</script>

<!--Colors recently used-->
<div style="background-color:#F7F7F7; padding:5px;">
<h4>Pick from colors previously used:</h4>

<?
$sql = "SELECT tagcolor,id,title FROM calendarevents GROUP BY(tagcolor) ORDER BY id DESC" ;

$result = mysqli_query($con,$sql);
while($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
{

$calendareventtitle = $row['title'];
$calendareventtagcolor = $row['tagcolor'];

echo"
<div style=\"width:80px; height:40px; background-color:$calendareventtagcolor; display:inline-block; overflow:hidden; padding:2px; border:1px lightgray solid; cursor:pointer;\" title=\"$calendareventtitle\" onclick='ChoosePreviousColor(\"$calendareventtagcolor\")'>
$calendareventtitle
</div>
";
}
?>

</div>




<div class="contactseparator"></div>




<!-- include libraries(jQuery, bootstrap) -->
<link href="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script> 
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script> 

<!-- include summernote css/js -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  $('#description').summernote({
  defaultFontName: 'Arial',
  height:200
  });
});
</script>
Description:
  <textarea id="description" name="description" style="min-height:150px; width:100%; display:none;"/><?if($_GET['action']=="edit"){echo"$calendareventdescription";}?></textarea>

<div class="contactseparator"></div>


<input type="hidden" name="id" value="<? echo $calendareventid;?>">
<input type="hidden" name="actionform" value="<?if($_GET['action']=="edit"){echo"edit";}else{echo"new";}?>">

<hr>

<center>
<button type="submit" class="formbutton" ><?if($_GET['action']=="edit"){echo"Update event";}else{echo"Import event";}?></button>
     </center>
</form>




<?if($_GET['action']=="edit"){
echo"
<hr style=\"margin-top:10px;\">
<form action=\"calendareventedit.php\" onsubmit=\"return confirm('Are you sure?');\" method=\"post\" style=\"text-align:left;\">
<center>
<button type=\"submit\" class=\"formbuttondelete\">Delete event</button>
</center> 
<input type=\"hidden\" name=\"calendareventid\" value=\"$calendareventid\">
<input type=\"hidden\" name=\"title\" value=\"$calendareventcalendarevent\">
<input type=\"hidden\" name=\"city\" value=\"$calendareventcity\">
<input type=\"hidden\" name=\"actionform\" value=\"delete\">
</form>  
";
}
?>            
        </div>
    </div>

        </div>

        
    

<?
}
   $path = $_SERVER['DOCUMENT_ROOT'];
   $path .= "/footer.php";
   include_once($path);
?>