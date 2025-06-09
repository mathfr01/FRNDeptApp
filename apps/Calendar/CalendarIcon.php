<?php  
session_start();


$CalendarAppIconDay = date("d");

$CalendarDayName = date("l");


$size = 5;
$spacing = 0;

$imgLink = "https://vgrcanada.org/images/calendarEmpty_$CalendarDayName.png";
$imgNumberLink = "https://vgrcanada.org/images/calendar_$CalendarAppIconDay.png";


    define("WIDTH", 180);
    define("HEIGHT", 180);

    $dest_image = imagecreatetruecolor(WIDTH, HEIGHT);

    //make sure the transparency information is saved
    imagesavealpha($dest_image, true);

    //create a fully transparent background (127 means fully transparent)
    $trans_background = imagecolorallocatealpha($dest_image, 0, 0, 0, 127);

    //fill the image with a transparent background
    imagefill($dest_image, 0, 0, $trans_background);

    //take create image resources out of the 3 pngs we want to merge into destination image
    $a = imagecreatefrompng($imgLink);
    $b = imagecreatefrompng($imgNumberLink);

    //copy each png file on top of the destination (result) png
    imagecopy($dest_image, $a, 0, 0, 0, 0, WIDTH, HEIGHT);
    imagecopy($dest_image, $b, 0, 0, 0, 0, WIDTH, HEIGHT);

    //send the appropriate headers and output the image in the browser
    header('Content-Type: image/png');
    imagepng($dest_image);

    //destroy all the image resources to free up memory
    imagedestroy($a);
    imagedestroy($b);
    imagedestroy($dest_image);


 /*

///THE FOLLOWING IS THE OLDER CODE THAT WAS INSERTING THE DAY NUMBER WITHIN A BLANK IMAGE... IT WAS WAY TOO SMALL
$img = imagecreatefrompng("https://vgrcanada.org/images/calendarEmpty.png");   
  



imagefill($img, 0, 0, $transparent);  
imagesavealpha($img, true);
imagealphablending($img, true);

$transparent = imagecolorallocatealpha($img, 0, 0, 0, 127); 
$blackfont = imagecolorallocate($img, 0, 0, 0);  
$greyshadow = imagecolorallocate($img, 128, 128, 128);

//original small numbers location
imagestring($img, $size, 3, 6, $CalendarAppIconDay, $blackfont);
 
//possible new  numbers location
// imagestring($img, 5, 25, 120, $CalendarAppIconDay, $blackfont);



// Ajout d'ombres au texte
//imagettftext($img, 20, 0, 11, 21, $greyshadow, $font, $CalendarAppIconDay);

// Ajout du texte
//imagettftext($img, 20, 0, 10, 20, $blackfont, $font, $CalendarAppIconDay);



header('Content-Type: image/png');
imagepng($img);  
imagedestroy($im);
*/





?>  