<?php
date_default_timezone_set('Europe/Minsk');

function get_timeago( $ptime )
{
    $etime = time() - $ptime;

    if( $etime < 1 )
    {
        return 'less than 1 second ago';
    }

    $a = array( 12 * 30 * 24 * 60 * 60  =>  'year',
                30 * 24 * 60 * 60       =>  'month',
                24 * 60 * 60            =>  'day',
                60 * 60             =>  'hour',
                60                  =>  'minute',
                1                   =>  'second'
    );

    foreach( $a as $secs => $str )
    {
        $d = $etime / $secs;

        if( $d >= 1 )
        {
            $r = round( $d );
            return '' . $r . ' ' . $str . ( $r > 1 ? 's' : '' ) . ' ago';
        }
    }
}



function GetStories(String $user){

   

         //Get user id
     
         
         $user = curl_init("https://api.storiesig.com/stories/".$user);
        
         curl_setopt($user, CURLOPT_FOLLOWLOCATION, 1);
         curl_setopt($user, CURLOPT_SSL_VERIFYPEER, true);
         curl_setopt($user, CURLOPT_SSL_VERIFYHOST, 1);
         curl_setopt($user, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($user, CURLOPT_HTTPHEADER, array(
             'x-ig-capabilities' =>'3w==',
             'user-agent'=> 'Instagram 9.5.1 (iPhone9,2; iOS 10_0_2; en_US; en-US; scale=2.61; 1080x1920) AppleWebKit/420+',
       
             
         ));
         
         $user = curl_exec($user);
         curl_close($user);

        $data = json_decode($user, true);
        $username = $data["user"]["username"];
        $userpic = $data["user"]['profile_pic_url'];
     
        $listofpreviewimagecount = $data["media_count"];
        $userdata = "
        <div class='user_info_block'>
            <div class='user_img'>
                <img src='${userpic}' alt='${username}'>
            </div>
            <div class='user_info'>
                <div class='user_name'>${username}</div>
                <div class='stories_count'>${listofpreviewimagecount} Instagram stories</div>
            </div>
        </div>";
         $videos = [];
         foreach ($data["items"] as $key => $value) {
           foreach ($data["items"][$key]["video_versions"] as $k => $v) {
          
            $videos[] = $v;

           }
         }
         $vds = [];
         $i = 0;
         foreach($videos as $element) {
          $hash = $element["id"];
          $vds[$i] = $element;
          $i++;
        }
         $images = [];
         foreach ($data["items"] as $key => $value) {
           foreach ($data["items"][$key]["image_versions2"]["candidates"] as $k => $v) {
          
            $images[] = $v;
     
           }
         }
         $time = [];
         foreach ($data["items"] as $key => $value) {
          
          
            $time[] = json_encode($data["items"][$key]["device_timestamp"]);
     
           
         }
        
         $imgs = [];
         foreach ($images as $key => $value) {
           if($value["width"] === 640 && $value["height"] === 1137){
            $imgs[] = $value;
           }
         }
         $stories = [];
         $i = 0;
         foreach($imgs as $k => $v){
           if(substr($v["url"], -1) === "2" ?? substr($v["url"], -1) === "m"){
            $stories[] = [
              "image" => $v["url"],
              "video" => $v["url"],
              "time" => $time[$i],
              "id" => $time[$i]
            ];
           }else{  
            $stories[] = [
              "image" => $v["url"],
              "video" => $vds[$i]["url"],
              "time" => $time[$i],
              "id" => $vds[$i]["id"]
            ];
          }
          $i++;
         }
        
         $userdata .= "<div class='stories_block'>";
         foreach ($stories as $key => $value) {
           $url = $value['video'];
           $img = $value['image'];
           $id = $value['id'];
         
           (int)$time = $value['time'] / 1000000;
           if(strlen(ceil($time)) === 9){
             (string)$time = ceil($time);
            $time = $time . '1' ;
            
           }
          
          $time = get_timeago($time);
          if($url === $img){
            $type = "jpg";
          }
          else{
            $type = "mp4";
          }
          $userdata .= "
         
            <div class='stories'>
                <div class='stories_time'>${time}</div>
                <a class='preview' data-video-poster='${img}' data-post-type='video' href='${img}'><img class='carousel-item photo' src='${img}' alt='${username}'>
                    <div class='video-icon'><span class='icon-play-1'></span></div>
                </a>
                <a class='download_button'  href='${url}' data-id='${id}' data-type='${type}' data-user='${username}'>Download <i class='icon-download'></i></a>
            </div>
         ";
         }
         $userdata .= "</div>";
      
             
        return $userdata;
}


if(!isset($_POST)){
  echo "error";
}

if(isset($_POST["username"]) && $_POST["action"] === "get_stories"){
 
  echo json_encode([
    "status" => true,
    "stories_block" => GetStories($_POST["username"])
  ],true);
}


if(isset($_POST["download"]) && $_POST["download"] === "true"){
  $type = $_POST["type"];
  if($type === "jpg"){
    $typename = "image";
  }
  else{
    $typename = "video";
  }
  
  $filename = $_POST["user"]." ".$typename." ".$_POST["id"].".".$type;
  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename='.$filename);
  readfile($_POST["url"]); 
  exit;
}