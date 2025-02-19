<?php

require 'vendor/autoload.php';

use Smalot\PdfParser\Parser;

function extractLicenses(string $filePath)
{
  $parser = new Parser();
  $pdf = $parser->parseFile($filePath);
  $text = $pdf->getText();

  $licensesSection = extractLicenseTextSection(
    $text,
    'Сведения о лицензиях',
    'Сведения о записях, внесенных в Единый государственный реестр юридических лиц'
  );

  if (!$licensesSection) {
    return [];
  }

  $licenses = parseLicenses($licensesSection);

  header('Content-Type: application/json');
  return json_encode($licenses, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function extractLicenseTextSection(string $text, string $startSectionTitle, string $endSectionTitle): string|null
{
  $pattern = '/' . preg_quote($startSectionTitle, '/') . '(.*?)' . preg_quote($endSectionTitle, '/') . '/si';
  preg_match($pattern, $text, $matches);

  return $matches[1] ?? null;
}

function parseLicenses(string $licenseTextSection)
{
  $licenses = [];
  $blocks = preg_split('/\n\d+\n/', $licenseTextSection);

  foreach ($blocks as $block) {
    if (trim($block) === '') {
      continue;
    }

    $licenseData = [
      'officialNum' => null,
      'issuerName' => null,
      'dateStart' => null,
      'activity' => null,
    ];
    $officialNumReestr = '';

    if (preg_match('/Номер лицензии\s*(.+?)(?=\n|$)/si', $block, $match)) {
      $licenseData['officialNum'] = trim($match[1]);
    }

    if (preg_match('/реестре учета лицензий\s*(.+?)(?=\n|$)/si', $block, $match)) {
      $officialNumReestr = trim($match[1]);
    }

    if (preg_match('/Наименование лицензирующего органа,?\s*выдавшего или переоформившего лицензию\s*(.+?)(?=\n\d+|\n$|\nГРН|$)/si', $block, $match)) {
      $licenseData['issuerName'] = trim(preg_replace('/\s+/', ' ', $match[1]));
    }

    if (preg_match('/Дата начала действия лицензии\s*(.+?)(?=\n|$)/si', $block, $match)) {
      $licenseData['dateStart'] = trim($match[1]);
    }

    if (preg_match('/Вид лицензируемой деятельности, на который\s*выдана лицензия\s*(.+?)(?=\nНаименование лицензирующего органа|$)/si', $block, $match)) {
      $licenseData['activity'] = trim(preg_replace('/\s+/', ' ', $match[1]));
    }

    if (array_filter($licenseData)) {
      if (strlen($officialNumReestr) > 0) {
        $licenseData['officialNum'] = $officialNumReestr;
      }

      $duplicatedLicense = array_filter($licenses, function ($license) use ($licenseData) {
        return $license['officialNum'] === $licenseData['officialNum'];
      });

      if (empty($duplicatedLicense)) {
        $licenses[] = $licenseData;
      }
    }
  }

  return $licenses;
}

// $filePath = './files/7842388475.pdf';
// $licenses = extractLicenses($filePath);
// echo $licenses;