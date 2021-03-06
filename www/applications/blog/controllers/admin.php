<?php
if (!defined("ACCESS")) {
	die("Error: You don't have permission to access here...");
}

class Admin_Controller extends ZP_Load 
{
	public function __construct() 
	{
		$this->app(whichApplication());
		$this->config($this->application);
		$this->Users_Model = $this->model("Users_Model");
		$this->Templates = $this->core("Templates");
		$this->Templates->theme();
		$this->Model = ucfirst($this->application) ."_Model";
		$this->{$this->Model} = $this->model($this->Model);
	}

	public function index() 
	{
		isConnected();

		$this->helper("time");

		$this->CSS("admin", "users");
		$this->CSS(CORE_PATH ."/vendors/css/frameworks/bootstrap/bootstrap-codejobs.css", null, false, true);
		$this->js("jquery.appear.js");
		$this->js("admin", "users");

		$this->vars["path"]	= path("blog/");
		$this->vars["records"] = $this->Users_Model->records();
		$this->vars["view"] = $this->view("admin", true, $this->application, $this->application);
		$this->vars["caption"] = __("My posts");
		$this->vars["total"] = SESSION("ZanUserPosts");
		$this->vars["ID_Column"] = "ID_Post";

		$this->title($this->vars["caption"]);
		$this->render("content", $this->vars);
	}

	public function data() 
	{
		$this->isMember();
		
		$start = (int) GET("start");
		$field = GET("field") ? GET("field") : "ID_Post";
		$order = GET("order") ? GET("order") : "DESC";
		$query = GET("query");
		$data = $this->Users_Model->records(true, $start, "$field $order", $query);

		if ($data) {
			echo json($data);
		} else {
			echo '[]';
		}
	}

	public function delete() 
	{
		$this->isMember();
		$records = GET("records");
		$start = (int) GET("start");
		$field = GET("field") ? GET("field") : "ID_Post";
		$order = GET("order") ? GET("order") : "DESC";
		$query = GET("query");

		if (is_array($records) and is_integer($start)) {
			$data = $this->Users_Model->delete($records, $start, "$field $order", $query);

			if ($data) {
				echo json($data);
			} else {
				echo '[]';
			}
		}
	}

	private function isMember() 
	{
		if (!SESSION("ZanUser")) {
			exit('[]');
		}
	}

}