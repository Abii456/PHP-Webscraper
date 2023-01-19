<?php
 
header("Content-Type: text/plain"); // We choose to display the content as plain text

include 'simple_html_dom.php';
$scraped_data = [];
 
$url = 'https://www.melbourne.vic.gov.au/building-and-development/property-information/planning-building-registers/Pages/town-planning-permits-register-search-results.aspx?std=05/01/2023&end=19/01/2023';

$curl = curl_init();
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_REFERER, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
$str = curl_exec($curl);
curl_close($curl);

// Create a DOM object
$html_base = new simple_html_dom();
// Load HTML from a string
$html_base->load($str);

//get all category links
$table_rows = $html_base->find('table.permits-list tbody tr');
foreach($table_rows as $table_row) {
    $application = $table_row->find('.column1 a',0);
    $date_received = $table_row->find('.column3',0);
    $address = $table_row->find('.column2',0);
    $proposal = $table_row->find('.column4 div',0);
    $status = $table_row->find('.column5',0);

    $scraped_data[] = [
        'Application' => $application->innertext,
        'Date Received' => $date_received->innertext,
        'Address' => $address->innertext,
        'Proposal' => $proposal->innertext,
        'Status' => $status->innertext
    ];
};

file_put_contents('file.json', json_encode($scraped_data)); // Saving the scraped data in a .json file
 
// Saving the scraped data as a csv
/*$csv_file = fopen('file.csv', 'w');
fputcsv($csv_file, array_keys($scraped_data[0]));
 
foreach ($scraped_data as $row) {
    fputcsv($csv_file, array_values($row));
}
 
fclose($csv_file);*/

$html_base->clear(); 
unset($html_base);

?>