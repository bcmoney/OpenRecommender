<?php 

//require_once "../../../Service.class.php";
require_once "../../../../../parser/html/simplehtmldom/simple_html_dom.php";
error_reporting(0);


class IMDB {        //extends Service {

  private $api_url;
  private $user_id;
  private $html;
  
  function __construct($api_url="http://www.imdb.com/user/~~PARAMETER~~/ratings?start=1&view=compact&sort=ratings_date:desc", $user_id="ur1272002", $pagination=1) {
    if ($pagination>1) {
      $this->api_url = str_replace("start=1", "start={$pagination}", (str_replace("~~PARAMETER~~", $user_id, $api_url)));
    }
    else {
      $this->api_url = str_replace("~~PARAMETER~~", $user_id, $api_url);
    }
    // create HTML DOM
    $this->html = file_get_html($this->api_url);
  }


  public function getRatings() {
    return $this->html->find('div .list table tbody tr');
  }

  //  .title a text()
  public function getRatingsTitle($rating) {
    return $rating->find('td.title a',0)->innertext;
  }

  //  .title a @href
  public function getRatingsTitleLink($rating) {
    return $rating->find('td.title a',0)->href;
  }  

  //  .year text()
  public function getRatingsTitleYear($rating) {  
    return $rating->find('td.year',0)->innertext;
  }
  
  //  .your_ratings a text()
  public function getRatingsYourRating($rating) {
    return $rating->find('td.your_ratings a',0)->innertext;
  }

  //  .user_rating text()
  public function getRatingsAverageRating($rating) {
    return $rating->find('td.user_ratings',0)->innertext;
  }
  
  //  .num_votes text()
  public function getRatingsNumberOfVotes($rating) {
    return $rating->find('td.num_votes',0)->innertext;
  }
  
  //  .ratings_date text()
  public function getRatingsDate($rating) {
    return $rating->find('td.ratings_date',0)->innertext;
  }
  
  /*
   *getPagination
   * If pagination encountered (i.e. user has more than 250 videos in their voting history)
   * enable skipping to next page (until no more pagination links available).
   *
   * xpath:
   *  .pagination a @href
   * EX: 
   *   ratings?start=251&view=compact&sort=ratings_date:desc
   */
  public function getPagination() {  
    return $this->html->find('div.pagination a',0)->getAttribute('href');
  }  
  
}

?>