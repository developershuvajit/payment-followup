<?php
// vendor/download_sample.php
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="sample_customers.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['party_name', 'whatsapp_number', 'bill_date', 'amount']);
fputcsv($output, ['রহিম স্টোর', '8801712345678', '2024-01-15', '5000']);
fputcsv($output, ['করিম ট্রেডার্স', '8801712345679', '2024-01-20', '10000']);
fputcsv($output, ['ফাতেমা এন্টারপ্রাইজ', '8801712345680', '2024-02-01', '7500']);

fclose($output);
exit();
?>