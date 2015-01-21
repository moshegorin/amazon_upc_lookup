<?php
/**
	B"H
	Crown Heights Cycles Amazon UPC Info App
	Developed by GorinSystems (www.gorinsystems.com)

	Page: Input Controller (input.com)
	Description: Controls input form

**/

class Input extends MainController {

	public function show(){

		// Show upload form
		$this->render('upload_form',array());
				
	}
	
}