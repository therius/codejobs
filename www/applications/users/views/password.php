<?php
	if (!defined("ACCESS")) die("Error: You don't have permission to access here...");

	echo div("edit-profile", "class");
		echo formOpen($href, "form-add", "form-add");
			echo isset($alert) ? $alert : null;

			echo formInput(array(
				"type"  => "hidden",
				"name"  => "username",
				"p"     => false,
				"value" => SESSION("ZanUser")
			));

			echo formInput(array(
				"type"      => "password",
				"name"      => "new_password", 
				"class"     => "field-title span4",
				"field"     => __("Password"), 
				"p"         => true,
				"maxlength" => "40"
			));

			echo formInput(array(
				"type"      => "password",
				"name"      => "re_new_password", 
				"class"     => "field-title span4",
				"field"     => __("Confirm password"), 
				"p"         => true,
				"maxlength" => "40"
			));

			echo formInput(array(
				"name"  => "save", 
				"class" => "btn btn-success", 
				"value" => __("Save"), 
				"type"  => "submit"
			));

		echo formClose();
	echo div(false);
?>
<script>
	var acceptLabel = "<?php echo __("Accept"); ?>",
		cancelLabel = "<?php echo __("Cancel"); ?>",
		inputLabel  = "<?php echo __("Input your password"); ?>",
		btnSelector = 'input[type="submit"]:first';
</script>