<?php

class Videos_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function listVideos($websites, $categories, $languages, $first_item, $offset){
		$where_video_id_array = array();
		if (strtoupper($websites) == 'ORPHANS'){ // /orphans/none/none
			$not_orphans = array();

			$where_website = 'SELECT DISTINCT video_id FROM website_videos';
			$query = $this->db->query($where_website)->result();
			if ( $query )
			{
				foreach ($query as $video_id) {
					if( ! in_array($video_id->video_id, $not_orphans) ){//video_id is not orphan
						array_push($not_orphans,$video_id->video_id);
					}
				}
			}
			$where_category = 'SELECT DISTINCT video_id FROM categories_videos';
			$query = $this->db->query($where_category)->result();
			if ( $query )
			{
				foreach ($query as $video_id) {
					if( ! in_array($video_id->video_id, $not_orphans) ){//video_id is not orphan
						array_push($not_orphans,$video_id->video_id);
					}
				}
			}
			$where_language = 'SELECT DISTINCT video_id FROM categories_videos';
			$query = $this->db->query($where_language)->result();
			if ( $query )
			{
				foreach ($query as $video_id) {
					if( ! in_array($video_id->video_id, $not_orphans) ){//video_id is not orphan
						array_push($not_orphans,$video_id->video_id);
					}
				}
			}

			$where = 'SELECT id FROM videos';
			$query = $this->db->query($where)->result();
			if ( $query ){
				foreach ($query as $video_id) {
					if( ! in_array($video_id->id, $not_orphans) ){//only insert orphan videos
						array_push($where_video_id_array,$video_id->id);
					}
				}
			}




		}
		else{//Not orphans
			if( strtoupper($websites) == strtoupper($categories) && strtoupper($categories) == strtoupper($languages) &&  strtoupper($languages) == 'ANY' ){ //All videos
				$where = 'SELECT id FROM videos';
				$query = $this->db->query($where)->result();
				if ( $query ){
					foreach ($query as $video_id) {
							array_push($where_video_id_array,$video_id->id);
						}
				}
			}
			else
			{//selected groups, excluding
				//websites
				$query_websites = NULL;
				$where_websites = array();
				if( strtoupper($websites) != 'NONE' ){
					if( strtoupper($websites) != 'ANY' ){//websites selected like 1,2,
						$where_website = 'SELECT DISTINCT video_id FROM website_videos WHERE website_id in ';
						$websites = str_replace('-',',',$websites);//change - by ,
						if($websites[strlen($websites)-1]==','){
							$websites = substr($websites, 0, -1); //delete last coma
						}
						$where_website = $where_website . '( ' . $websites . ' )';
						$query_websites = $this->db->query($where_website)->result();
					}
					else{//any website
							$where_website = 'SELECT DISTINCT video_id FROM website_videos';
							$query_websites = $this->db->query($where_website)->result();
					}	
				}
				if ( $query_websites )
				{
					foreach ($query_websites as $video_id) {
						if( ! in_array($video_id->video_id, $where_video_id_array) ){//video_id is not on current video_id list
							array_push($where_video_id_array,$video_id->video_id);
						}
						array_push($where_websites, $video_id->video_id);//videos in that languages
					}
				}
				//categories
				$query_categories = NULL;
				$where_categories = array();
				if( strtoupper($categories) != 'NONE' ){
					if( strtoupper($categories) != 'ANY' ){//categories selected like 1,2,
						$where_category = 'SELECT DISTINCT video_id FROM categories_videos WHERE category_id in ';
						$categories = str_replace('-',',',$categories);//change - by ,
						if($categories[strlen($categories)-1]==','){
							$categories = substr($categories, 0, -1); //delete last coma
						}
						$where_category = $where_category . '( ' . $categories . ' )';
						$query_categories = $this->db->query($where_category)->result();
					}
					else{
						$where_category = 'SELECT DISTINCT video_id FROM categories_videos';
						$query_categories = $this->db->query($where_category)->result();
					}
				}
				if ( $query_categories )
				{
					foreach ($query_categories as $video_id) {
						if( ! in_array($video_id->video_id, $where_video_id_array) ){//video_id is not on current video_id list
							array_push($where_video_id_array,$video_id->video_id);
						}
						array_push($where_categories, $video_id->video_id);//videos in that category
					}
				}
				//languages
				$query_languages = NULL;
				$where_languages = array();
				if( strtoupper($languages) != 'NONE' ){
					if( strtoupper($languages) != 'ANY' && strtoupper($languages) != '1-'){//languages selected like 1,2,
						$where_language = 'SELECT DISTINCT video_id FROM titles_videos WHERE language in ';
						$languages = str_replace('-',',',$languages);//change - by ,
						if($languages[strlen($languages)-1]==','){
							$languages = substr($languages, 0, -1); //delete last coma
						}
						$where_language = $where_language . '( ' . $languages . ' )';
						$query_languages = $this->db->query($where_language)->result();
					}
					else{//All videos are in english!
						$where_language = 'SELECT id AS video_id FROM videos';
						$query_languages = $this->db->query($where_language)->result();
					}
				}
				if ( $query_languages )
				{
					foreach ($query_languages as $video_id) {
						if( ! in_array($video_id->video_id, $where_video_id_array) ){//video_id is not on current video_id list
							array_push($where_video_id_array,$video_id->video_id);
						}
						array_push($where_languages, $video_id->video_id);//videos in that language
					}
				}
				//$where_video_id_array contains all the videos that have searched language or website or category
				asort($where_video_id_array);
				$where_video_id_array_excluding = array();
				foreach ($where_video_id_array as $video_id) {
					if ( in_array($video_id, $where_websites) && in_array($video_id, $where_categories) && in_array($video_id, $where_languages) ){
						array_push($where_video_id_array_excluding,$video_id);
					}
				}
				//$where_video_id_array_excluding contains videos than have languages and categories and websites searched
				$where_video_id_array = $where_video_id_array_excluding;

			}
		}//not orphans
		if(count($where_video_id_array)){
			$videos_to_select = array();
			foreach ($where_video_id_array as $video_id){
				array_push($videos_to_select, intval($video_id));
			} 
			$query = $this->db->order_by('id', 'DESC')->where_in('id', $videos_to_select)->get('videos',intval($offset),intval($first_item))->result_array();
			$query_total_size = $this->db->from('videos')->where_in('id', $videos_to_select)->count_all_results();
			return array($query_total_size,$query);
		}
		else{
			return (array(0,array()));
		}
		//return $query;
	}


	public function showVideo($id)
	{
		$where_video = 'SELECT * FROM videos WHERE id='.$id;
		$query_video = $this->db->query($where_video)->result_array()[0];

		$where_titles = 'SELECT language, title, description FROM titles_videos WHERE video_id='.$id;
		$query_titles = $this->db->query($where_titles)->result_array();
		$titles = array();
		foreach ($query_titles as $title) {
			$titles[$title['language']] = array( 'title' => $title['title'] , 'description' => $title['description'] );
		}

		$where_websites = 'SELECT website_id FROM website_videos WHERE video_id='.$id;
		$query_websites = $this->db->query($where_websites)->result_array();
		$websites = '';
		foreach ($query_websites as $website) {
			$websites .= $website['website_id'] . '-';
		}

		$where_categories = 'SELECT category_id FROM categories_videos WHERE video_id='.$id;
		$query_categories = $this->db->query($where_categories)->result_array();
		$categories = '';
		foreach ($query_categories as $category) {
			$categories .= $category['category_id'] . '-';
		}
		return array($query_video,$titles,$websites,$categories);
	}

	public function getLinksFromCloudFront($image_url, $video_url)
	{

		$this->load->library('variables');
		$this->load->library('aws_sdk_cloud');

		$image = str_replace($this->variables->s3_bucket_url,'',$image_url);
		$video = str_replace($this->variables->s3_bucket_url,'',$video_url);
		$expiry = new DateTime('+50 minutes');

		$new_image_url = $this->aws_sdk_cloud->getSignedUrl([
			'url' => "{$this->variables->cloudfront['url']}/{$image}",
			'expires' => $expiry->getTimestamp()
		]);

		$new_video_url = $this->aws_sdk_cloud->getSignedUrl([
			'url' => "{$this->variables->cloudfront['url']}/{$video}",
			'expires' => $expiry->getTimestamp()
		]);

		return array('image' => $new_image_url , 'video' => $new_video_url);
	}

}