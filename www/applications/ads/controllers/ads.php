<?php
if (!defined("ACCESS")) {
	die("Error: You don't have permission to access here...");
}

class Ads_Controller extends ZP_Load 
{
	
	public function __construct() 
	{		
		$this->Ads_Model = $this->model("Ads_Model");
		$this->application = $this->app("ads");
	}
	
	public function index($action = null, $position = "Top") 
	{
		redirect();	
	}
	
	public function ads($position) 
	{
		$data = $this->Ads_Model->getAds($position);
	
		if ($data) {
			$vars["data"] = $data;
			
			$this->view("ads", $vars, $this->application);				
		} 

		return false;
	}

}
