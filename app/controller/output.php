<?php
/**
	B"H
	Amazon UPC Info App	
	Developed by GorinSystems (www.gorinsystems.com)

	Page: Output Controller (output.php)
	Description: Controls input form, output form, template rendering 

**/

class Output extends MainController {

	private function aws_signed_request($region, $params, $public_key, $private_key, $associate_tag=NULL, $version='2011-08-01'){
	
		// Send Signed Request to Amazon.com API

		// Paramaters
		$method = 'GET';
		$host = 'webservices.amazon.'.$region;
		$uri = '/onca/xml';
		
		// additional parameters
		$params['Service'] = 'AWSECommerceService';
		$params['AWSAccessKeyId'] = $public_key;
		// GMT timestamp
		$params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
		// API version
		$params['Version'] = $version;
		if ($associate_tag !== NULL) {
			$params['AssociateTag'] = $associate_tag;
		}
		
		// Sort the Parameters
		ksort($params);
		
		// Create the canonicalized query
		$canonicalized_query = array();
		foreach ($params as $param=>$value)
		{
			$param = str_replace('%7E', '~', rawurlencode($param));
			$value = str_replace('%7E', '~', rawurlencode($value));
			$canonicalized_query[] = $param.'='.$value;
		}
		$canonicalized_query = implode('&', $canonicalized_query);
		
		// Create the string to sign
		$string_to_sign = $method."\n".$host."\n".$uri."\n".$canonicalized_query;
		
		// Calculate HMAC with SHA256 and base64-encoding
		$signature = base64_encode(hash_hmac('sha256', $string_to_sign, $private_key, TRUE));
		
		// Encode the signature for the request
		$signature = str_replace('%7E', '~', rawurlencode($signature));
		
		// Create request
		$request = 'http://'.$host.$uri.'?'.$canonicalized_query.'&Signature='.$signature;
		
		return $request;
		
	}


	public function process($files){
		
		// Get Amazon.com info for uploaded files

		// Step 1: Read Input CSV

		$result_array = array();
		$row = 0;
		if (($handle = fopen($files["file"]["tmp_name"], "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000000, ",")) !== FALSE && $row<=999) {

				if ( $row > 0 ){
				
					$upc = $data[0];

					if ( strlen((string)$upc)==12 ){ // Valid UPC (otherwise skip)
					
						// Send UPC to Amazon.com API

						$params = array(
							"AssociateTag"=>ASSOCIATE_TAG, 
							"Operation"=>"ItemLookup",
							"IdType"=>"UPC",
							"ItemId" => $upc,
							"SearchIndex" => SEARCH_INDEX,						
							//"IdType"=>"ASIN",
							//"ItemId" => "B000YZ4CPU",
							"ResponseGroup" => "SalesRank,ItemAttributes,OfferSummary,Reviews",
							"TruncateReviewsAt" => 256,
							"IncludeReviewsSummary" => true,
						);

						
						$url = $this->aws_signed_request(REGION, $params, PUBLIC_KEY, PRIVATE_KEY, $associate_tag=NULL, $version='2011-08-01');

						// Get Contents of 'Customer Reviews iFrame'
						// Parse 'Customer Reviews iFrame' using DOM xPath

						$xml = new SimpleXMLElement(file_get_contents($url));

						$rev_url = $xml->Items->Item->CustomerReviews->IFrameURL;		
						if ( !empty($rev_url) ){
							$html = file_get_contents($rev_url);
							@$dom = DOMDocument::loadHTML($html);
							$xpath = new DOMXpath($dom);
							$img_length = $xpath->query('//span[contains(@class, "asinReviewsSummary")]//a//img')->length;
							$img = $xpath->query('//span[contains(@class, "asinReviewsSummary")]//a//img');
																	
							if ( $img_length > 0 ){							
								$rating = str_replace(" out of 5 stars","",$img->item(0)->getAttribute('title'));
								if ( !empty($rating) ){
									$revs = $xpath->query('//div[contains(@class, "crIFrameHeaderHistogram")]//div//b');
									$num_revs = (int)str_replace(" Reviews","",(string)$revs->item(0)->nodeValue);
								} else {
									$num_revs = 0;
								}								
							} else { // No reviews
								$num_revs = 0;
							}							
						}
						
						$result = array(						
							"upc"=>$upc,
							"brand"=>$xml->Items->Item->ItemAttributes->Publisher,
							"title"=>$xml->Items->Item->ItemAttributes->Title,
							"url"=>$xml->Items->Item->DetailPageURL,
							"sales_rank"=>number_format((int)$xml->Items->Item->SalesRank),
							"num_used"=>$xml->Items->Item->OfferSummary->TotalUsed,
							"price_used"=>$xml->Items->Item->OfferSummary->LowestUsedPrice->FormattedPrice,
							"num_new"=>$num_new = $xml->Items->Item->OfferSummary->TotalNew,
							"price_new"=>$xml->Items->Item->OfferSummary->LowestNewPrice->FormattedPrice,
							"num_revs"=>$num_revs,
							"rating"=>$rating
						);

						// Add to result array
						array_push($result_array,$result);					
						sleep(TIMEOUT);

					}
					
				}
				$row++;

			}
			fclose($handle);
			
			// Generate CSV
			
			// CSV name
			$csv_file_basename = "output/UPC-Results-" . date("m-d-y");
			$csv_file = $csv_file_basename . ".csv";
			
			$count=1;
			while ( file_exists($csv_file) ){
				$csv_file = $csv_file_basename . " (" . $count . ").csv";
				$count++;
			}
			
			// Write to CSV
			$fp = fopen($csv_file, 'w');
			
			// Header
			$csv_header = array("UPC","Brand","Title","URL","Sales Rank","Num Used","Price Used","Num New","Price New","Num Revs","Rating");
			fputcsv($fp, $csv_header);
			
			// Rows
			foreach ($result_array as $result) {
				if ( !empty($result["title"]) ){	
					fputcsv($fp, $result);
				}
			}
			fclose($fp);
						
			// Output Web Results
			$this->render('output_header',array("csv_file"=>$csv_file) );

			foreach ( $result_array as $result){
				if ( !empty($result["title"]) ){
					$this->render('output_item',$result);
				}
			}
			
			$this->render('output_footer',array() );

		}
		
		
	}
		

}