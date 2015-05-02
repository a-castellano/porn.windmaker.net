<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Videos_manager extends CI_Controller {


	public function __construct()
	{
		parent::__construct();
		$this->load->model('videos_model');
		
	}


	public function index()
	{
		$data['title'] = 'Porn manager';
		$this->load->view('videos/index', $data);
	}

	public function addVideo($errors = ''){

		$this->load->library('variables');
		$this->load->helper('form');
	    $this->load->library('form_validation');

		$data['title'] = 'Add a new video';
		$data['errors'] = $errors;

		$this->load->view('videos/add',$data);

	}


	public function uploadVideo()
	{

        $config['upload_path']          = './upload/';
        $config['allowed_types']        = '*';
        $config['max_size']             = 0; //ilimited

		$this->load->library('variables');
	    $this->load->helper('form');
	    $this->load->library('form_validation');
	    $this->load->library('upload', $config);
	    $this->load->library('aws_sdk');

	    $data['title'] = 'Add a new video';

	    $video_languages = '';
	    $video_sites = '';
	    $video_categories = '';

	    $data['errors'] = '';//I'm not using this shit
	    //Form files
	    foreach ($this->variables->langs as $lang => $value) {
	    	if (isset($_POST['lang_'.$value])) {
	    		$this->form_validation->set_rules('title_'.$value, $lang.' title', 'required');
	    		$this->form_validation->set_rules('description_'.$value, $lang.' description', 'required');
	    		$video_languages .= $value . '-';
	    	}
	    }
	    
		foreach ($this->variables->sites as $site => $value) {
			if (isset($_POST['site_'.$value])) {
				$video_sites .=  $value . '-';
			}
		}

		foreach ($this->variables->categories as $category => $value) {
			if (isset($_POST['category_'.$value])) {
				$video_categories .=  $value . '-';
			}
		}


	    if ($this->form_validation->run() === FALSE)
	    {
	        $this->addVideo();

	    }
	    else
	    {

	    	/* Chek if video and video screenshoot have been uploaded and rename files
			 * Format: {md5 file checksum}.{file ext}
			 */ 
			if ( ! $this->upload->do_upload('image')){ //check image
				$errors = $this->upload->display_errors();
				$this->addVideo($errors);
				return;
			}
			else {
				$image_data = $this->upload->data('image');



				if ( ! $image_data['is_image'] ){ //check image
					$errors = 'You didn\'t uploaded a valid image file!';
					unlink($image_data['full_path']);
					$this->addVideo($errors);
					return;
				}
				
				if ( ! $this->upload->do_upload('video')){ 
					$errors = $this->upload->display_errors();
					$this->addVideo($errors);
					return;
					//$this->load->view('videos/add', $errors);
				}
				else{
					$video_data = $this->upload->data('video');

					if ( ! in_array( $video_data['file_ext'] , array(".mp4", ".avi", ".webm", ".flv", ".3gp", ".mkv") ) ){ //check video
						$errors = 'You didn\'t uploaded a valid video file!';
						unlink($image_data['full_path']);
						unlink($video_data['full_path']);
						$this->addVideo($errors);
						return;
					}


					//paths
					$image_path = $image_data['full_path'] ;
					$video_path = $video_data['full_path'] ;					

					//new names
					$image_name = md5_file($image_data['full_path']) . $image_data['file_ext'] ;
					$video_name = md5_file($video_data['full_path']) . $video_data['file_ext'] ;

					//item keys
					$image_key = md5_file($image_data['full_path']) ;
					$video_key = md5_file($video_data['full_path']) ;

					//extensions
					$image_extension = explode('.',$image_data['file_ext']);
					$image_extension = strtolower(end($image_extension));
					$video_extension = explode('.',$video_data['file_ext']);
					$video_extension = strtolower(end($video_extension));

				    $video_languages_array = explode("-", $video_languages);
				    array_pop($video_languages_array); //last item is always ""

				    $video_sites_array = explode("-", $video_sites);
				    array_pop($video_sites_array); //last item is always ""

				    $video_categories_array = explode("-", $video_categories);
				    array_pop($video_categories_array); //last item is always ""

				    $all_data = array();
					$all_data['video_languages'] = $video_languages;
					$all_data['video_languages_array'] = $video_languages_array;
					foreach($video_languages_array as $lang){ 
						$all_data['title_'.$lang] = $_POST['title_'.$lang];
						$all_data['description_'.$lang] = $_POST['description_'.$lang];
					}

					$all_data['video_sites'] = $video_sites;
					$all_data['video_sites_array'] = $video_sites_array;

					$all_data['video_categories'] = $video_categories;
					$all_data['video_categories_array'] = $video_categories_array;

					//All video settings right! time to upload to S3 and store video info on db
					//Uploading image
					try{
					    $image_aws_object=$this->aws_sdk->saveObject(array(
					        'Bucket'      => $this->variables->s3_bucket,
					        'Key'         => $image_key,
					        'ACL'         => 'public-read',
					        'SourceFile'  => $image_path,
					        'ContentType' => 'image/'.$image_extension
					    ))->toArray();

					    $video_aws_object=$this->aws_sdk->saveObject(array(
					        'Bucket'      => $this->variables->s3_bucket,
					        'Key'         => $video_key,
					        'ACL'         => 'public-read',
					        'SourceFile'  => $video_path,
					        'ContentType' => 'video/'.$video_extension
					    ))->toArray();

					    //Delete local image and video uploads
						unlink($image_data['full_path']);
						unlink($video_data['full_path']);

					    $all_data['image_aws'] = $image_aws_object['ObjectURL'];
					    $all_data['video_aws'] = $video_aws_object['ObjectURL'];

					    //all data collected, tiem tostore it in db
						$this->videos_model->storeVideo($all_data);
						$this->load->view('videos/success');
					}catch (Exception $e){
					    $error = "Something went wrong saving your file.\n".$e;
					}

	                
            	}
			}

	    }

	}

	public function addMultipleVideos($numVideos=5,$errors = ''){

		$this->load->library('variables');
		$this->load->helper('form');
	    $this->load->library('form_validation');

		$data['title'] = 'Add a new video';
		$data['numVideos'] = $numVideos;
		$data['errors'] = $errors;

		$this->load->view('videos/addMultiple',$data);

	}

	public function uploadMultipleVideos($numVideos=5)
	{

        $config['upload_path']          = './upload/';
        $config['allowed_types']        = '*';
        $config['max_size']             = 0; //ilimited

		$this->load->library('variables');
	    $this->load->helper('form');
	    $this->load->library('form_validation');
	    $this->load->library('upload', $config);
	    $this->load->library('aws_sdk');

		$data['title'] = 'Add a multiple videos';   

	    $video_sites = '';
	    $video_categories = '';

	    $data['errors'] = '';//I'm not using this shit
	    
		foreach ($this->variables->sites as $site => $value) {
			if (isset($_POST['site_'.$value])) {
				$video_sites .=  $value . '-';
			}
		}

		foreach ($this->variables->categories as $category => $value) {
			if (isset($_POST['category_'.$value])) {
				$video_categories .=  $value . '-';
			}
		}

	    for ( $counter = 1; $counter <= $numVideos; $counter += 1){
	    	if (isset($_POST['lang_1_'.$counter])) {
	    		$this->form_validation->set_rules('title_1_'.$counter, 'English title', 'required');
	    		$this->form_validation->set_rules('description_1_'.$counter, 'English description', 'required');
	    	}
	    }

	   	if ($this->form_validation->run() === FALSE)
	    {
	        $this->addMultipleVideos($numVideos);

	    }

	    else //Upload all the videos
	    {
	    	for ( $counter = 1; $counter <= $numVideos; $counter += 1){

				if ( ! $this->upload->do_upload('image_'.$counter)){ //check image
					$errors = $this->upload->display_errors();
					echo "ERROR: image_".$counter." not found<br>";
					break;
					$this->uploadMultipleVideos($numVideos,$errors);
					
					return;
				}
				else {
					$image_data[$counter] = $this->upload->data('image_'.$counter);



					if ( ! $image_data[$counter]['is_image'] ){ //check image
						$errors = 'You didn\'t uploaded a valid image file!<br>';
						unlink($image_data[$counter]['full_path']);
						echo "ERROR: image_".$counter." -> " . $errors;
						break;
						$this->uploadMultipleVideos($numVideos,$errors);
						return;
					}
					
					if ( ! $this->upload->do_upload('video_'.$counter)){ 
						$errors = $this->upload->display_errors();
						echo "ERROR: video_".$counter." not found<br>";
						break;
						$this->uploadMultipleVideos($numVideos,$errors);
						return;
					}
					else{
						$video_data[$counter] = $this->upload->data('video_'.$counter);
						if ( ! in_array( $video_data[$counter]['file_ext'] , array(".mp4", ".avi", ".webm", ".flv", ".3gp", ".mkv") ) ){ //check video
							$errors = 'You didn\'t uploaded a valid video file!<br>';
							unlink($image_data[$counter]['full_path']);
							unlink($video_data[$counter]['full_path']);
							echo "ERROR: video_".$counter." -> " . $errors;
							break;
							$this->uploadMultipleVideos($numVideos,$errors);
							return;
						}
						//Image and video are ok!

					}
				}

			}//for $counter
			//All videos are right, time to store them

			for ( $counter = 1; $counter <= $numVideos; $counter += 1){

				//paths
				$image_path = $image_data[$counter]['full_path'] ;
				$video_path = $video_data[$counter]['full_path'] ;					

				//new names
				$image_name = md5_file($image_data[$counter]['full_path']) . $image_data[$counter]['file_ext'] ;
				$video_name = md5_file($video_data[$counter]['full_path']) . $video_data[$counter]['file_ext'] ;

				//item keys
				$image_key = md5_file($image_data[$counter]['full_path']) ;
				$video_key = md5_file($video_data[$counter]['full_path']) ;

				//extensions
				$image_extension = explode('.',$image_data[$counter]['file_ext']);
				$image_extension = strtolower(end($image_extension));
				$video_extension = explode('.',$video_data[$counter]['file_ext']);
				$video_extension = strtolower(end($video_extension));

			    $video_languages_array = explode("-", "1-");
			    array_pop($video_languages_array); //last item is always ""

			    $video_sites_array = explode("-", $video_sites);
			    array_pop($video_sites_array); //last item is always ""

			    $video_categories_array = explode("-", $video_categories);
			    array_pop($video_categories_array); //last item is always ""

			    $all_data = array();
				$all_data['video_languages'] = "1-"; //always is this case
				$all_data['video_languages_array'] = $video_languages_array;

				$all_data['title_1'] = $_POST['title_1_'.$counter];
				$all_data['description_1'] = $_POST['description_1_'.$counter];

				$all_data['video_sites'] = $video_sites;
				$all_data['video_sites_array'] = $video_sites_array;

				$all_data['video_categories'] = $video_categories;
				$all_data['video_categories_array'] = $video_categories_array;


				try{
				    $image_aws_object=$this->aws_sdk->saveObject(array(
				        'Bucket'      => $this->variables->s3_bucket,
				        'Key'         => $image_key,
				        'ACL'         => 'public-read',
				        'SourceFile'  => $image_path,
				        'ContentType' => 'image/'.$image_extension
				    ))->toArray();

				    $video_aws_object=$this->aws_sdk->saveObject(array(
				        'Bucket'      => $this->variables->s3_bucket,
				        'Key'         => $video_key,
				        'ACL'         => 'public-read',
				        'SourceFile'  => $video_path,
				        'ContentType' => 'video/'.$video_extension
				    ))->toArray();

				    //Delete local image and video uploads
					unlink($image_data[$counter]['full_path']);
					unlink($video_data[$counter]['full_path']);

				    $all_data['image_aws'] = $image_aws_object['ObjectURL'];
				    $all_data['video_aws'] = $video_aws_object['ObjectURL'];

				    //all data collected, tiem tostore it in db
					$this->videos_model->storeVideo($all_data);
					
				}catch (Exception $e){
				    $error = "Something went wrong saving your file.\n".$e;
				}

			}//for counter

	    }//else, upload the videos
	    $this->load->view('videos/success');
	    //$this->manageVideos();
		
	}

	public function searchVideo($multiple=NULL)
	{
		$this->load->library('variables');
		$this->load->helper('form');
	    $this->load->library('form_validation');

	    $video_languages = '';
	    $video_sites = '';
		$video_categories = '';

	    foreach ($this->variables->langs as $lang => $value) {
	    	if (isset($_POST['lang_'.$value])) {
	    		$video_languages .= $value . '-';
	    	}
	    }
	    if(! strlen($video_languages)){
	    	$video_languages = 'any';
	    }

		foreach ($this->variables->sites as $site => $value) {
			if (isset($_POST['site_'.$value])) {
				$video_sites .=  $value . '-';
			}
		}
		if(! strlen($video_sites)){
	    	$video_sites = 'any';
	    }

		foreach ($this->variables->categories as $category => $value) {
			if (isset($_POST['category_'.$value])) {
				$video_categories .=  $value . '-';
			}
		}
		if(! strlen($video_categories)){
			$video_categories = 'any';
	    }
	    if( ! $multiple ){
			$this->manageVideos($video_sites,$video_categories,$video_languages);
		}
		else{
			$this->manageMultipleVideos($video_sites,$video_categories,$video_languages,"0",$_POST['nomberNews']);
		}
		return;
	}

	public function manageVideos($websites="any", $categories="any", $languages="any", $first_item="0", $offset="5")
	{
		$this->load->library('variables');
		$this->load->helper('form');
	    $this->load->library('form_validation');

		$videos = $this->videos_model->listVideos($websites, $categories, $languages, $first_item, $offset);
		$data['number_videos'] = $videos[0];
		$data['videos'] = $videos[1];
		$data['number_indexes'] = intval(ceil($data['number_videos']/$offset));
		$data['current_index'] = intval(floor($first_item/$offset)) + 1 ;
		$data['websites'] = $websites ;
		$data['categories'] = $categories ;
		$data['languages'] = $languages ;
		$data['offset'] = $offset ;
		$this->load->view('videos/manage',$data);
	}

	public function searchMultipleVideo()
	{
		$this->searchVideo($_POST);
		return;
	}


	public function manageMultipleVideos($websites="any", $categories="any", $languages="any", $first_item="0", $offset="50")
	{
		$this->load->library('variables');
		$this->load->helper('form');
	    $this->load->library('form_validation');

	    $video_list = '';//List of shown videos

		$videos = $this->videos_model->listVideos($websites, $categories, $languages, $first_item, $offset);
		$data['number_videos'] = $videos[0];
		$data['videos'] = $videos[1];
		$data['number_indexes'] = intval(ceil($data['number_videos']/$offset));
		$data['current_index'] = intval(floor($first_item/$offset)) + 1 ;
		$data['websites'] = $websites ;
		$data['categories'] = $categories ;
		$data['languages'] = $languages ;
		$data['offset'] = $offset ;

		foreach ($data['videos'] as $video) {
			$video_list .= $video['id'].'|';
		}
		$data['video_list'] = $video_list;

		$this->load->view('videos/managemultiple',$data);
	}

	public function viewVideos($video_id,$errors=''){
		$this->load->library('variables');
		$this->load->helper('form');
	    $this->load->library('form_validation');

		$video_info = $this->videos_model->showVideo($video_id);

		$data['video'] = $video_info[0];
		//Only need this in the API
		//$new_links = $this->videos_model->getLinksFromCloudFront($data['video']['image_url'],$data['video']['video_url']);
		//$data['video']['image_url'] = $new_links['image'];
		//$data['video']['video_url'] = $new_links['video'];
		$data['titles'] = $video_info[1];
		$data['websites'] = $video_info[2];
		$data['categories'] = $video_info[3];
		$data['errors'] = $errors;
		$this->load->view('videos/view',$data);
	}

	public function updateVideo()
	{
		$this->load->library('variables');
		$this->load->helper('form');
	    $this->load->library('form_validation');


	    $video_languages = '';
	    $video_sites = '';
	    $video_categories = '';

	    $data['errors'] = '';//I'm not using this shit
	    $id=$_POST['id'];
	    //Form files
	    foreach ($this->variables->langs as $lang => $value) {
	    	if (isset($_POST['lang_'.$value])) {
	    		$this->form_validation->set_rules('title_'.$value, $lang.' title', 'required');
	    		$this->form_validation->set_rules('description_'.$value, $lang.' description', 'required');
	    		$video_languages .= $value . '-';
	    	}
	    }
	    
		foreach ($this->variables->sites as $site => $value) {
			if (isset($_POST['site_'.$value])) {
				$video_sites .=  $value . '-';
			}
		}

		foreach ($this->variables->categories as $category => $value) {
			if (isset($_POST['category_'.$value])) {
				$video_categories .=  $value . '-';
			}
		}


	    if ($this->form_validation->run() === FALSE)
	    {
	        $this->viewVideos($id,$data['errors']);

	    }
	    else
	    {

		    $video_languages_array = explode("-", $video_languages);
		    array_pop($video_languages_array); //last item is always ""

		    $video_sites_array = explode("-", $video_sites);
		    array_pop($video_sites_array); //last item is always ""

		    $video_categories_array = explode("-", $video_categories);
		    array_pop($video_categories_array); //last item is always ""

		    $all_data = array();
		    $all_data['id'] = $id;
			$all_data['video_languages'] = $video_languages;
			$all_data['video_languages_array'] = $video_languages_array;
			foreach($video_languages_array as $lang){ 
				$all_data['title_'.$lang] = $_POST['title_'.$lang];
				$all_data['description_'.$lang] = $_POST['description_'.$lang];
			}

			$all_data['video_sites'] = $video_sites;
			$all_data['video_sites_array'] = $video_sites_array;

			$all_data['video_categories'] = $video_categories;
			$all_data['video_categories_array'] = $video_categories_array;

			$this->videos_model->updateVideo($all_data);
			
			$this->viewVideos($id,"Video info updated!");

		}

	}

	public function updateMultipleVideos(){

		$this->load->library('variables');
		$this->load->helper('form');
	    $this->load->library('form_validation');


	    $video_sites = '';
	    $video_categories = '';

	    $data['errors'] = '';//I'm not using this shit
	    $all_ids = $_POST['video_list'];
	    $ids_array = explode("|", $all_ids);
	    $selected_videos = array();

	    //Selected videos
	    foreach ($ids_array as $id) {
	    	if (isset($_POST['video_'.$id])) {
	    		array_push($selected_videos, $id);
	    	}
	    }
    
		foreach ($this->variables->sites as $site => $value) {
			if (isset($_POST['site_'.$value])) {
				$video_sites .=  $value . '-';
			}
		}

		foreach ($this->variables->categories as $category => $value) {
			if (isset($_POST['category_'.$value])) {
				$video_categories .=  $value . '-';
			}
		}
		

	    $video_sites_array = explode("-", $video_sites);
	    array_pop($video_sites_array); //last item is always ""

	    $video_categories_array = explode("-", $video_categories);
	    array_pop($video_categories_array); //last item is always ""

	    $all_data = array();
	    $all_data['ids'] = $selected_videos;

		$all_data['video_sites'] = $video_sites;
		$all_data['video_sites_array'] = $video_sites_array;

		$all_data['video_categories'] = $video_categories;
		$all_data['video_categories_array'] = $video_categories_array;

		$this->videos_model->updateMultipleVideos($all_data);

		$this->load->view('videos/success');

	}

	public function deleteVideo($id)
	{
		$this->videos_model->deleteVideo($id);
		$this->manageVideos();
	}	

}
