<?php 
	if (!defined("ACCESS")) {
		die("Error: You don't have permission to access here...");
	}

	$ID = isset($data) ? recoverPOST("ID", $data[0]["ID_Image"]) : 0;
	$title = isset($data) ? recoverPOST("title", $data[0]["Title"]) : recoverPOST("title");
	$description = isset($data) ? recoverPOST("description", $data[0]["Description"]) : recoverPOST("description");
	$category = isset($data) ? recoverPOST("category", $data[0]["Album"]) : recoverPOST("category");
	$ID_Category = isset($data) ? recoverPOST("ID_Category", $data[0]["ID_Category"]) : recoverPOST("ID_Category");
	$medium = isset($data) ? recoverPOST("medium", $data[0]["Medium"]) : recoverPOST("medium");
	$situation = isset($data) ? recoverPOST("situation", $data[0]["Situation"]) : recoverPOST("situation");
	$edit = isset($data) ? true : false;
	$action = isset($data) ? "edit" : "save";
	$href = isset($data) ? path(whichApplication() ."/cpanel/edit/$ID") : path(whichApplication() ."/cpanel/add");

	echo div("add-form", "class");
		echo formOpen($href, "form-add", "form-add");
			echo p(__(ucfirst(whichApplication())), "resalt");
			echo isset($alert) ? $alert : null;

			echo formInput(array(
				"name" => "title", 
				"class" => "span10 required", 
				"field" => __("Title"), 
				"p" => true, 
				"value" => $title
			));

			echo formTextarea(array(
				"name" => "description", 
				"class" => "required", 
				"style" => "height: 150px;", 
				"field" => __("Description"), 
				"p" => true, 
				"value" => $description
			));

			if ($medium) { 
				echo p(img(path($medium, true)), "field");
			} 

			if ($action === "save") {
				echo formInput(array(
					"name" => "files[]", 
					"type" => "file",
					"class" => "add-img required", 
					"field" => __("Image"), 
					"p" => true
				));
			} else { 
				echo formInput(array(
					"name" => "file",
					"type"=> "file", 
					"class" => "required", 
					"field" => __("Image"), 
					"p" => true
				));
			} 

			echo formInput(array(
				"name" => "category", 
				"class" => "span10 required", 
				"field" => __("Album") ." (". __("Write a album or select") .")", 
				"p" => true
			));

			$options = array(
				0 => array("value" => "Active", "option" => __("Active"), "selected" => ($situation === "Active") ? true : false),
				1 => array("value" => "Inactive", "option" => __("Inactive"), "selected" => ($situation === "Inactive") ? true : false)
			);

			echo formSelect(array(
				"name" => "situation", 
				"class" => "required", 
				"p" => true, 
				"field" => __("Situation")), 
				$options
			);

			echo formSave($action);
			echo formInput(array("name" => "ID", "type" => "hidden", "value" => $ID));
		echo formClose();
	echo div(false);