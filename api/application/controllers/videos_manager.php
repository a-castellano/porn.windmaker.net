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


	public function manageVideos($websites="any", $categories="any", $languages="any", $first_item="0", $offset="5",$sub=NULL)
	{
		$this->load->library('variables');
		$this->load->helper('form');
	    $this->load->library('form_validation');

		$videos = $this->videos_model->listVideos($websites, $categories, $languages, $first_item, $offset);
		$data['data']['number_videos'] = $videos[0];
		$data['data']['videos'] = $videos[1];
		$data['data']['number_indexes'] = intval(ceil($data['data']['number_videos']/$offset));
		$data['data']['current_index'] = intval(floor($first_item/$offset)) + 1 ;
		$data['data']['websites'] = $websites ;
		$data['data']['categories'] = $categories ;
		$data['data']['languages'] = $languages ;
		$data['data']['offset'] = $offset ;

		if($sub==NULL){
			$this->load->view('videos/manage',$data);
		}
		else{//make easier some querys
			$custom_data['data'] = $data['data'][$sub];
			$this->load->view('videos/manage',$custom_data);
		}
	}

	public function viewVideos($video_id,$sub=NULL,$subsub=NULL){
		$this->load->library('variables');
		$this->load->helper('form');
	    $this->load->library('form_validation');

		$video_info = $this->videos_model->showVideo($video_id);

		$data['data']['video'] = $video_info[0];
		$new_links = $this->videos_model->getLinksFromCloudFront($data['data']['video']['image_url'],$data['data']['video']['video_url']);
		$data['data']['video']['image_url'] = $new_links['image'];
		$data['data']['video']['video_url'] = $new_links['video'];
		$data['data']['titles'] = $video_info[1];
		$data['data']['websites'] = $video_info[2];
		$data['data']['categories'] = $video_info[3];
		

		if($sub==NULL){
			$this->load->view('videos/view',$data);
		}
		else{//make easier some querys
			if($subsub==NULL){
				$custom_data['data'] = $data['data'][$sub];
				$this->load->view('videos/view',$custom_data);
			}
			else{
				$custom_data['data'] = $data['data'][$sub][$subsub];
				$this->load->view('videos/view',$custom_data);
			}
		}
	}

}
