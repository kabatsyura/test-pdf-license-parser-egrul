<?php

require 'vendor/autoload.php';

use Lib\LicenseService;

$pdfFilePath = './files/7842388475.pdf';
$licenseService = new LicenseService();
$result = $licenseService->extractLicenses($pdfFilePath);
echo $result;