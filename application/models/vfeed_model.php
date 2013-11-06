<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class VFeed_Model extends CI_Model {

	public function __construct(){
		// Call the Model constructor
		parent::__construct();
		$this->load->library('zend');
		$this->zend->load('Zend/Loader');

		//load yt related libraries
		Zend_Loader::loadClass("Zend_Gdata");
		Zend_Loader::loadClass("Zend_Gdata_YouTube");
		Zend_Loader::loadClass("Zend_Gdata_YouTube_VideoFeed");
		Zend_Loader::loadClass("Zend_Gdata_YouTube_VideoEntry");
		Zend_Loader::loadClass("Zend_Gdata_YouTube_VideoQuery");
		Zend_Loader::loadClass("Zend_Gdata_YouTube_Extension_MediaGroup");
	}

 }

?>







<!-- The old searchResults.php need to add some of this -->
<!-- require_once 'Zend/Loader.php';
Zend_Loader::loadClass("Zend_Gdata");
Zend_Loader::loadClass("Zend_Gdata_YouTube");
Zend_Loader::loadClass("Zend_Gdata_YouTube_VideoFeed");
Zend_Loader::loadClass("Zend_Gdata_YouTube_VideoEntry");
Zend_Loader::loadClass("Zend_Gdata_YouTube_VideoQuery");
Zend_Loader::loadClass("Zend_Gdata_YouTube_Extension_MediaGroup");


$searchBar = $_POST['searchBar'];

$yt = new Zend_Gdata_YouTube();
//$yt->setMajorProtocolVersion(2);
$q = $yt->newVideoQuery();
$q->setQuery($searchBar);
//$q->videoQuery = $searchBar;
$q->orderBy = 'viewCount';
$feed = $yt->getVideoFeed($q);



//searching with URL query
/*
$yt = new Zend_Gdata_YouTube();
$url = 'http://gdata.youtube.com/feeds/standardfeeds/top_rated?time=today'
$videoFeed = $yt->getVideoFeed($url);
 */

//$output = array();
// foreach ($feed as $key => $value){
//     $output[$key]['videoID'] = $value->getVideoId();
//     $output[$key]['title'] = (string)$value->getVideoTitle();
//     $output[$key]['desc'] = (string)$value->getVideoDescription();
//     $thumbnail = $value->getVideoThumbnails();
//     $output[$key]['thumbnail'] = $thumbnail[0]['url'];
// }



foreach ($feed as $key => $value){
    $videoId = $value->getVideoId();
    $duration = $value->getVideoDuration();
    $title = (string)$value->getVideoTitle();
    $description = (string)$value->getVideoDescription();
    $thumbnails = $value->getVideoThumbnails();
    $thumbnail = $thumbnails[0]['url'];
    $editButton  = displayEditButton($videoId, $duration);



    echo <<<ITEM
   
    <li id="resultItem" class="media">
        
        <div id="resultContainer" class="accordion-group">
            <div class="accordion-heading">
                <div class="accordion-toggle" data-toggle="collapse" data-parent="#searchResults" href="#{$videoId}">
                    <a class="pull-left" href="#">
                       <img class="media-object" src="{$thumbnail}" width="124px" style="padding-bottom: 20px;" alt="{$title}" /> 
                    </a>
                    
                    <div id="resultInfo" class="media-body">
                        <a class="media-heading" id="videoTitle">{$title}</a>
                        <p id="videoDesc">{$description}</p>    
                    </div>
                </div>
            </div>

            <div id="{$videoId}" class="accordion-body collapse out">
                <div id="videoOptions" class="accordion-inner">
                    
                    {$editButton}
                    <a id="videoPlay" role="button" class="btn" href="./player.php?id={$videoId}">
                        Play Video
                    </a>
                </div>
            </div>
        
        </div>

    </li>
   
ITEM;
}


/*Displays the edit button for the video feed
 if the user is logged in
 */
function displayEditButton($videoId, $duration){
    $button = "<a id=\"videoEdit\" role=\"button\" class=\"btn\" 
            href=\"./editor.php?vID={$videoId}\">
                Add Description
            </a>";

    if(isset($_SESSION['userId']) && isset($_SESSION['token'])){ 
        return $button;
    }
    return null;

}


//
//echo("<pre>");
//print_r($output);

//
//function printVideoEntry($videoEntry) 
//{
//  // the videoEntry object contains many helper functions
//  // that access the underlying mediaGroup object
//  echo 'Video: ' . $videoEntry->getVideoTitle() . "\n";
//  echo 'Video ID: ' . $videoEntry->getVideoId() . "\n";
//  echo 'Updated: ' . $videoEntry->getUpdated() . "\n";
//  echo 'Description: ' . $videoEntry->getVideoDescription() . "\n";
//  echo 'Category: ' . $videoEntry->getVideoCategory() . "\n";
//  echo 'Tags: ' . implode(", ", $videoEntry->getVideoTags()) . "\n";
//  echo 'Watch page: ' . $videoEntry->getVideoWatchPageUrl() . "\n";
//  echo 'Flash Player Url: ' . $videoEntry->getFlashPlayerUrl() . "\n";
//  echo 'Duration: ' . $videoEntry->getVideoDuration() . "\n";
//  echo 'View count: ' . $videoEntry->getVideoViewCount() . "\n";
//  echo 'Rating: ' . $videoEntry->getVideoRatingInfo() . "\n";
//  echo 'Geo Location: ' . $videoEntry->getVideoGeoLocation() . "\n";
//  echo 'Recorded on: ' . $videoEntry->getVideoRecorded() . "\n";
//  
//  // see the paragraph above this function for more information on the 
//  // 'mediaGroup' object. in the following code, we use the mediaGroup
//  // object directly to retrieve its 'Mobile RSTP link' child
//  foreach ($videoEntry->mediaGroup->content as $content) {
//    if ($content->type === "video/3gpp") {
//      echo 'Mobile RTSP link: ' . $content->url . "\n";
//    }
//  }
//  
//  echo "Thumbnails:\n";
//  $videoThumbnails = $videoEntry->getVideoThumbnails();
//
//  foreach($videoThumbnails as $videoThumbnail) {
//    echo $videoThumbnail['time'] . ' - ' . $videoThumbnail['url'];
//    echo ' height=' . $videoThumbnail['height'];
//    echo ' width=' . $videoThumbnail['width'] . "\n";
//  }
//}
//
//
//
//function getAndPrintStandardFeeds() 
//{
//  // constructing URL manually
//  $YOUTUBE_GDATA_SERVER = 'http://gdata.youtube.com';
//  $STANDARD_FEED_PREFIX = $YOUTUBE_GDATA_SERVER . '/feeds/api/standardfeeds/';
//  $TOP_RATED_FEED = $STANDARD_FEED_PREFIX . 'top_rated';
//  getAndPrintVideoFeed($TOP_RATED_FEED);
//
//  // URL as a constant in Zend_Gdata_YouTube
//  getAndPrintVideoFeed(Zend_Gdata_YouTube::STANDARD_TOP_RATED_URI);
//
//  // using helper method
//  $yt = new Zend_Gdata_YouTube();
//  $yt->setMajorProtocolVersion(2);
//  getAndPrintVideoFeed($yt->getRecentlyFeaturedVideoFeed());
//
//  // choosing the time period for a standard feed
//  $yt = new Zend_Gdata_YouTube();
//  $yt->setMajorProtocolVersion(2);
//  $query = $yt->newVideoQuery(Zend_Gdata_YouTube::STANDARD_TOP_RATED_URI);
//  $query->setTime('today');
//  getAndPrintVideoFeed($query);
//}
//
//
//function searchAndPrintVideosByKeywords($searchTermsArray)
//{
//  $yt = new Zend_Gdata_YouTube(); 
//  $query = $yt->newVideoQuery();
//  $query->setOrderBy('viewCount');
//  $query->setRacy('include');
//  $query->setCategory('News/sports/football');
//
//  /*
//   * The following commented-out code block demonstrates how to generate 
//   * the value that is passed to $query->setCategory
//   * 
//   * $keywordQuery = '';
//   * foreach ($searchTermsArray as $searchTerm) {
//   *   $keywordQuery .= strtolower($searchTerm) . '/';
//   * }
//   * $query->setCategory($keywordQuery);
//   */
//
//  $videoFeed = $yt->getVideoFeed($query);
//  printVideoFeed($videoFeed, 'Search results for keyword search:');
//}
//
//function getAndPrintVideoFeed($location = Zend_Gdata_YouTube::VIDEO_URI)
//{
//  $yt = new Zend_Gdata_YouTube();
//  // set the version to 2 to receive a version 2 feed of entries
//  $yt->setMajorProtocolVersion(2);
//  $videoFeed = $yt->getVideoFeed($location);
//  printVideoFeed($videoFeed);
//}
// 
//function printVideoFeed($videoFeed)
//{
//  $count = 1;
//  foreach ($videoFeed as $videoEntry) {
//    echo "Entry # " . $count . "\n";
//    printVideoEntry($videoEntry);
//    echo "\n";
//    $count++;
//  }
//} -->



