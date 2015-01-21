<?php
/**
	B"H
	Crown Heights Cycles Amazon UPC Info App
	Developed by GorinSystems (www.gorinsystems.com)

	Page: Main Controller (main_controller.php)
	Description: Contains everything used in other controllers (e.x. template rendering)

**/

class MainController {

	public function render($name, $vars = array()){

		/**
			Renders template in view/ folder
			$name = Template name without ".phtml"
			$vars = variable array passed to template		
		**/
		
		$template_file = "app/view/" . $name . ".phtml";
		
		if (is_file($template_file)) {  
		
			// Loads template, extracts variables
			ob_start();
			extract($vars);
			require($template_file);
			$contents = ob_get_contents();
			ob_end_clean();
			echo $contents;
			
		} else {
			throw new exception('Could not load template file \'' . $template_file . '\'');
		}
		
	}
	
}
	