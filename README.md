## PDF parser from EGRUL pdf file

This small program can help parse all licenses from EGRUL PDF files. EGRUL is a document that describes all the data of a company.

If you want to see the result in the terminal, execute the command below:

```php
php index.php > result.txt
```

For testing, execute the command:

```bash
make test
```

The structure of the project:

```
├── Makefile
├── README.md
├── composer.json
├── composer.lock
├── files
│   └── 7842388475.pdf
├── fixtures
│   └── license_example.json
├── index.php
├── lib
│   └── LicenseService.php
├── result.txt
├── tests
│   └── LicenseParserTest.php
└── text.txt
```

An example PDF file for parsing is located in the files folder.
