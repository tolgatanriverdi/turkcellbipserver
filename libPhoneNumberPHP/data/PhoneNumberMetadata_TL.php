<?php
return array (
  'generalDesc' => 
  array (
    'NationalNumberPattern' => '[2-47-9]\\d{6}',
    'PossibleNumberPattern' => '\\d{7}',
    'ExampleNumber' => '',
  ),
  'fixedLine' => 
  array (
    'NationalNumberPattern' => '
          (?:
            2[1-5]|
            3[1-9]|
            4[1-4]
          )\\d{5}
        ',
    'PossibleNumberPattern' => '\\d{7}',
    'ExampleNumber' => '2112345',
  ),
  'mobile' => 
  array (
    'NationalNumberPattern' => '7[2-49]\\d{5}',
    'PossibleNumberPattern' => '\\d{7}',
    'ExampleNumber' => '7212345',
  ),
  'tollFree' => 
  array (
    'NationalNumberPattern' => '80\\d{5}',
    'PossibleNumberPattern' => '\\d{7}',
    'ExampleNumber' => '8012345',
  ),
  'premiumRate' => 
  array (
    'NationalNumberPattern' => '90\\d{5}',
    'PossibleNumberPattern' => '\\d{7}',
    'ExampleNumber' => '9012345',
  ),
  'sharedCost' => 
  array (
    'NationalNumberPattern' => 'NA',
    'PossibleNumberPattern' => 'NA',
    'ExampleNumber' => '',
  ),
  'noInternationalDialling' => 
  array (
    'NationalNumberPattern' => 'NA',
    'PossibleNumberPattern' => 'NA',
    'ExampleNumber' => '',
  ),
  'id' => 'TL',
  'countryCode' => 670,
  'internationalPrefix' => '00',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' => 
  array (
    0 => 
    array (
      'pattern' => '(\\d{3})(\\d{4})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' => 
      array (
      ),
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
    ),
  ),
  'intlNumberFormat' => 
  array (
  ),
  'mainCountryForCode' => NULL,
  'leadingZeroPossible' => NULL,
);