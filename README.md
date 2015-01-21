# Amazon.com Product Lookup from UPC Codes
Looks up product information from Amazon.com's database including Brand, Title, Sales Rank, Reviews & Average Rating from a spreadsheet of UPC codes.

Input: CSV of UPC codes, sample: <b>upc_input_sample.csv</b>

Output: CSV of Product info, sample: <b>upc_output_sample.csv</b>

Demo <a href="http://gorinsystems.com/demos/amazon_upc_lookup/" target="_blank">here</a>

To make this app work you need to enter in your Amazon.com API keys and Associates tag in "config.php" specifically the PUBLIC_KEY, PRIVATE_KEY and ASSOCIATE_TAG constants.

You can also change the Amazon.com region and search index.

By default the app only processes 1 request per second since this is the starting max query usage allowed by Amazon.com Products.

Developed 100% by Moshe Gorin
