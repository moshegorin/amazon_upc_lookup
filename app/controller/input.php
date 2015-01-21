<?php
/**
	B"H
	Amazon UPC Info App	
	Developed by GorinSystems (www.gorinsystems.com)

	Page: Input Controller (input.php)
	Description: Controls input form

**/

class Input extends MainController {

	public function show(){

		// Show upload form
		$this->render('upload_form',array());
				
	}
	
}