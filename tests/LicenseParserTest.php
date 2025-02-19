<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Lib\LicenseService;

class LicenseParserTest extends TestCase
{
  #[Test]
  public function testLicenseParserTest(): void
  {
    $pdfEgrulFilepath = __DIR__ . '/../files/7842388475.pdf';
    $expectedData = json_decode(file_get_contents(__DIR__ . '/../fixtures/license_example.json'), true);

    $licenseService = new LicenseService();
    $result = json_decode($licenseService->extractLicenses($pdfEgrulFilepath), true);

    $this->assertEquals($expectedData, $result);
  }
}