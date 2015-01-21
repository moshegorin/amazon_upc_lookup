<?php
/**
	B"H
	Amazon UPC Info App
	Developed by GorinSystems (www.gorinsystems.com)

	Looks up product information from Amazon.com's database for a spreadsheet of UPC codes
		
	Last updated 3/20/2014

**/

// Error reporting on
error_reporting(E_ERROR | E_WARNING | E_PARSE);

// Set timeout values
ini_set('max_execution_time',12000);
ini_set('memory_limit','256M');

require("config.php");

// Load Main Controller (for template rendering)
require("app/controller/main_controller.php");

// Routing
if (isset($_GET['route'])){
	$route = $_GET['route'];
} else {
	$route = NULL;
}

// Routing
switch ($route){

	case "output": // // Process upload and show results

		// Load output controller
		require("app/controller/output.php");
		$output = new Output();
		$output->process($_FILES); // process uploaded files	
		break;

	default: // Show input form

		// Load input controller
		require("app/controller/input.php");
		$input = new Input;
		$input->show(); // Show input form

}