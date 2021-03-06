<?php
if (!defined("ACCESS")) {
	die("Error: You don't have permission to access here...");
}

class Users_Controller extends ZP_Load
{
	public function __construct()
	{
		$this->Templates = $this->core("Templates");
		$this->Users_Model = $this->model("Users_Model");

		$this->application = $this->app("users");
		$this->language = whichLanguage();

		$this->Templates->theme();
		$this->helper("router");
		$this->CSS("forms");
	}

	public function index()
	{
		redirect();
	}

	public function service($service = "facebook", $login = false)
	{
		if ($service === "facebook") {
			$this->facebookLogin($login);
		} elseif ($service === "twitter") {
			$this->twitterLogin();
		}
	}

	public function twitterLogin()
	{
		$this->helper(array("alerts", "twitter", "forms", "html"));

		$this->Twitter = $this->library("twitter", "EpiTwitter", array(TW_CONSUMER_KEY, TW_CONSUMER_SECRET));
		
		$oauthToken = GET("oauth_token");

		if (!$oauthToken) {
			redirect($this->Twitter->getAuthenticateUrl());
     	} else {
     		$vars = getTwitterUser($oauthToken, $this->Twitter);

     		if (is_array($vars)) {
     			$data = $this->Users_Model->checkUserService($vars["serviceID"], "Twitter");
 
     			if ($data) {
     				createLoginSessions($data[0]);
     			} else {
     				SESSION("socialUser", $vars);

		    		$vars["view"] = $this->view("socialregister", true);

		    		$this->render("content", $vars);
     			}
     		}
     	}
	}

	public function facebookLogin($login = false)
	{
		$this->helper(array("alerts", "facebook", "forms", "html"));

		$code = REQUEST("code");

		if (!$code) {
			if ($login) {
				getFacebookLogin();
			}
		} else {
			if (isConnectedToFacebook()) {
				$facebookUser = getFacebookUser($code);

		     	if ($facebookUser) {
		     		$data = $this->Users_Model->checkUserService($facebookUser["serviceID"]);

		     		if ($data) {
		     			createLoginSessions($data[0]);
		     		} else {
		     			$vars = array(
		     				"service" 	=> "facebook",
		     				"serviceID" => $facebookUser["serviceID"],
		     				"username" 	=> $facebookUser["username"],
		     				"name" 		=> $facebookUser["name"],
		     				"email" 	=> $facebookUser["email"],
		     				"birthday" 	=> $facebookUser["birthday"],
		     				"avatar" 	=> $facebookUser["avatar"]
		     			);

						SESSION("socialUser", $vars);

		     			$vars["view"] = $this->view("socialregister", true);

		     			$this->render("content", $vars);
		     		}
		     	} else {
		     		showAlert(__("An unknown problem occurred, try to login again"), path());
		     	} 
		    } else {
		     	showAlert(__("Invalid Token, try to login again"), path());
			}
		}
	}

	public function logout()
	{
		unsetSessions();
	}
	
	public function activate($user = null, $code = false)
	{
		$this->helper("alerts");
		
		if (!$user or !$code) {
			redirect();
		} else {
			$data = $this->Users_Model->activate($user, $code);
			
			if ($data) {
				createLoginSessions($data[0], false);
					 
				showAlert(__("Your account has been activated correctly!"), path());
			} else {
				showAlert(__("An error occurred when attempting to activate your account!"), path());
			}
		}
	}

	public function deactivate()
	{
		isConnected();

		if (POST("option") and POST("username") and POST("password")) {
			$this->helper("alerts");

			if ($this->Users_Model->isMember()) {
				switch (POST("option")) {
					case "deactivate": case "delete":
						if ($this->Users_Model->deactivateOrDelete(POST("option"))) {
							showAlert(__("Your account has been ". POST("option") ."d"), "users/logout/");
							break;
						}

					default:
						showAlert(__("Something went wrong! Try again later"), "users/logout/");
				}
			} else {
				showAlert(__("Incorrect password. Try again later"), "users/logout/");
			}
		} else {
			$this->title(__("Deactivate my account"));
			$this->CSS("deactivate", $this->application);
			$this->js("bootstrap");
			$this->js("deactivate", $this->application);

			$this->config("deactivate", $this->application);

			$vars["view"] = $this->view("deactivate", true);
			$vars["username"] = SESSION("ZanUser");
				
			$this->render("content", $vars);
		}
	}

	public function login()
	{
		$this->helper(array("html", "alerts"));
				
		if (!SESSION("ZanUser")) {
			$this->title(decode(__("Sign in")));

			$this->helper("forms");
			$this->config("login", $this->application);

			$vars["href"] = path("users/login");

			$data = false;

			if (POST("login")) {
				if ($this->Users_Model->isMember()) {
					$data = $this->Users_Model->getUserData();
				} 
		
				if ($data) {
					createLoginSessions($data[0], false);

					redirect(GET("return_to") ? GET("return_to") : false);
				} else { 
					$this->helper("alerts");

					$vars["alert"] = getAlert(__("Username or password incorrect"));
				}
			}

			if (GET("type") === "1") {
				$this->helper("alerts");

				$vars["alert"] = getAlert(__("You must be logged in"), "notice");
			}

			$this->CSS(CORE_PATH ."/vendors/css/frameworks/bootstrap/bootstrap-codejobs.css", null, false, true);
			$this->CSS("user_login", $this->application);

			$vars["view"] = $this->view("user_login", true);
			
			$this->render("include", $vars);
			$this->rendering("header", "footer");
		} else {
			redirect(GET("return_to") ? GET("return_to") : false);
		} 
	}
	
	public function recover($token = false)
	{
		$this->title(decode(__("Recover Password")));
		
		$this->helper(array("forms", "html", "alerts"));

		if (POST("change")) {
			$vars["alert"] = $this->Users_Model->change();
			$vars["tokenID"] = $token;
		} elseif (POST("recover")) {
			$status = $this->Users_Model->recover();
			
			$vars["alert"] = $status;
		} elseif ($token) {
			$tokenID = $this->Users_Model->isToken($token, "Recover");
			
			if ($tokenID > 0) {
				$vars["tokenID"] = $tokenID;
			} else {
				redirect();
			}
		} 

		$vars["view"] = $this->view("recover", true);
		
		$this->render("content", $vars);
	}
	
	public function register($service = false)
	{
		$this->helper(array("html", "alerts"));

		if (!SESSION("ZanUser")) {
			$this->title(decode(__("Register")));

			$this->helper("forms");

			if (POST("register")) {
				$vars["name"] = POST("name") ? POST("name") : null;
				$vars["email"] = POST("email") ? POST("email") : null;
				$vars["pwd"] = POST("password") ? POST("password") : null;

				if (POST("username")) {
					$status = $this->Users_Model->addUser($service);

					$vars["inserted"] = $status["inserted"];
					$vars["alert"] = $status["alert"];
					$vars["first"] = true;
				}
			}

			if (!$service) {
				$vars["view"] = $this->view("new", true);
			} else {
				$vars["view"] = $this->view("socialregister", true);
			}

			$this->render("content", $vars);
		} else {
			redirect();
		}
	}

	public function about()
	{
		isConnected();

		if (POST("save")) {
			$this->helper("alerts");
			$vars["alert"] = $this->Users_Model->saveInformation();
		}

		$data = $this->Users_Model->getInformation();

		if ($data) {
			$this->helper(array("forms", "html"));
			$this->config("users", $this->application);
			$this->css("forms", "cpanel");
			$this->css("users", $this->application);
			$this->js("about", $this->application);
			$this->js("jquery.jdpicker.js");

			$this->Configuration_Model = $this->model("Configuration_Model");
			$this->Cache = $this->core("Cache");
			$list_of_countries = $this->Cache->data("countries", "world", $this->Configuration_Model, "getCountries", array(), 86400);

			foreach ($list_of_countries as $country) {
				$countries[] = array(
					"option" => $country["Country"],
					"value" => $country["Country"]
				);
			}

			$this->title(__("About me"));

			$vars["countries"] = $countries;
			$vars["view"] = $this->view("about", true);
			$vars["href"] = path("users/about/");
			$vars["data"] = $data;

			if ($country = recoverPOST("country", encode($vars["data"][0]["Country"]))) {
				$list_of_cities = $this->Cache->data("$country-cities", "world", $this->Configuration_Model, "getCities", array($country), 86400);

				foreach ($list_of_cities as $city) {
					$cities[] = array(
						"option" => $city["District"],
						"value" => $city["District"]
					);
				}

				$vars["cities"] = $cities;
			}

			$this->render("content", $vars);
		} else {
			redirect();
		}
	}

	public function password()
	{
		isConnected();

		$this->helper(array("forms", "html"));
		$this->config("users", $this->application);
		$this->css("forms", "cpanel");
		$this->css("users", $this->application);

		if (POST("save")) {
			$this->helper("alerts");
			$vars["alert"] = $this->Users_Model->changePassword();
		}

		$this->js("bootstrap");
		$this->js("www/lib/themes/newcodejobs/js/requestpassword.js");

		$this->title(decode(__("Change password")));

		$vars["view"] = $this->view("password", true);
		$vars["href"] = path("users/password/");

		$this->render("content", $vars);
	}

	public function email()
	{
		isConnected();

		$data = $this->Users_Model->getEmail();

		if ($data) {
			$this->helper(array("forms", "html"));
			$this->config("users", $this->application);
			$this->css("forms", "cpanel");
			$this->css("users", $this->application);

			if (POST("save")) {
				$this->helper("alerts");
				$vars["alert"] = $this->Users_Model->changeEmail();
			}

			$this->js("bootstrap");
			$this->js("www/lib/themes/newcodejobs/js/requestpassword.js");

			$this->title(decode(__("Change e-mail")));

			$vars["view"] = $this->view("email", true);
			$vars["href"] = path("users/email/");
			$vars["data"] = $data;

			$this->render("content", $vars);
		} else {
			redirect();
		}
	}

	public function avatar()
	{
		isConnected();

		if (POST("delete")) {
			$this->helper("alerts");
			$vars["alert"] = $this->Users_Model->deleteAvatar();
		} elseif (POST("save")) {
			$this->helper("alerts");
			$vars["alert"] = $this->Users_Model->saveAvatar();
		} elseif (POST("nosupport")) {
			$user = SESSION("ZanUser");
			if (isset($_FILES['avatar']) and $user) {
				$this->helper("alerts");

				$file = $_FILES['avatar'];
				$error = $file['error'];
				$type = $file['type'];
				$name = $file['name'];
				$tmp_name = $file['tmp_name'];

				if ($error === 1 or $error == 2) {
					$vars["alert"] = getAlert(__("The file size exceeds the limit allowed"), "error");
				} elseif ($error > 0) {
					$vars["alert"] = getAlert(__("An error occurred while handling file upload", "error"));
				} elseif (!preg_match('/^image/', $type)) {
					$vars["alert"] = getAlert(__("The file is not an image", "error"));
				} elseif ($type != "image/png" and $type != "image/jpeg" and $type != "image/gif") {
					$vars["alert"] = getAlert(__("The file is not a known image format", "error"));
				} elseif (is_uploaded_file($tmp_name)) {
					$this->Images = $this->core("Images");
					$this->Images->load($tmp_name);
					$filename = sha1($user . "_O");
					$resized = sha1($user);
					$path = "www/lib/files/images/users";
					$this->Images->png("$path/$filename.png");
					$width = $this->Images->getWidth();
					$coordinates = $this->Images->crop(true, 90, 90);
					$this->Images->png("$path/$resized.png");

					if ($width > 700) {
						$aspect = 700 / $width;
						$coordinates[0] = (int)($coordinates[0] * $aspect);
						$coordinates[1] = 0;
						$coordinates[2] = (int)($coordinates[2] * $aspect);
						$coordinates[3] = $coordinates[2];
					}

					if ($this->Users_Model->setAvatar("$resized.png", $coordinates)) {
						SESSION("ZanUserAvatar", "$resized.png?". time());

						$vars["alert"] = getAlert(__("The avatar has been saved correctly"), "success");
					} else {
						$vars["alert"] = getAlert(__("An error occurred while handling file upload", "error"));
					}
				}
			}
		}

		$data = $this->Users_Model->getAvatar();
		
		if ($data) {
			$this->helper(array("forms", "html"));
			$this->config("users", $this->application);
			$this->css("forms", "cpanel");
			$this->css("users", $this->application);
			$this->css("avatar", $this->application);
			$this->js("jquery.jcrop.js");
			$this->js("avatar", $this->application);

			$this->title(__("Avatar"));

			$vars["view"] = $this->view("avatar", true);
			$vars["href"] = path("users/avatar/");
			$vars["data"] = $data;

			$this->render("content", $vars);
		} else {
			redirect();
		}
	}

	public function options()
	{
		isConnected();

		if (POST("save")) {
			$this->helper("alerts");
			$vars["alert"] = $this->Users_Model->saveOptions();
		} elseif(POST("delete")) {
			$this->helper("alerts");
			$vars["alert"] = $this->Users_Model->deleteOptions();
		}

		$data = $this->Users_Model->getOptions();

		if ($data) {
			$this->helper(array("forms", "html"));
			$this->config("users", $this->application);
			$this->css("forms", "cpanel");
			$this->css("users", $this->application);

			$this->title(__("Options"));

			$vars["ckeditor"] = $this->js("ckeditor", "basic", true);
			$vars["view"] = $this->view("options", true);
			$vars["href"] = path("users/options/");
			$vars["data"] = $data;

			$this->render("content", $vars);
		} else {
			redirect();
		}
	}

	public function social()
	{
		isConnected();

		if (POST("save")) {
			$this->helper("alerts");

			$vars["alert"] = $this->Users_Model->saveSocial();
		}

		$data = $this->Users_Model->getSocial();

		if ($data) {
			$this->helper(array("forms", "html"));
			$this->config("users", $this->application);
			$this->css("forms", "cpanel");
			$this->css("users", $this->application);

			$this->title(__("Social Networks"));

			$vars["view"] = $this->view("social", true);
			$vars["href"] = path("users/social/");
			$vars["data"] = $data;

			$this->render("content", $vars);
		} else {
			redirect();
		}
	}

	public function cv()
	{
		isConnected();

		$data = $this->Users_Model->getInformation();

		if ($data) {
			//mostrar el form con toda la info
			$this->helper(array("forms", "html"));
			$this->config("users", $this->application);
			$this->css("forms", "cpanel");
			$this->css("users", $this->application);
			$this->css("cv", $this->application);

			if (POST("save")) {
				$this->helper("alerts");
				$vars["alert"] = $this->Users_Model->saveCv();
			}

			$this->js("jquery.jdpicker.js");

			$this->Configuration_Model = $this->model("Configuration_Model");
			$this->Cache = $this->core("Cache");

			$vars["user"] = $data[0];
			$vars["view"] = $this->view("cv", true);
			$vars["href"] = path("users/cv/");

			$this->title("Curriculum Vitae");

			$this->render("content", $vars);
		} else {
			redirect();
		}
	}

	public function profile($user = null)
	{
		$data = $this->Users_Model->getByUsername($user);

		if ($data) {
			$this->title(__("Profile of") ." $user");

			$vars["user"] = $data[0];
			$vars["view"] = $this->view("profile", true);

			$this->render("content", $vars);
		} else {
			redirect();
		}

	}
}
