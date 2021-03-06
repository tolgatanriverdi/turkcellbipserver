<?php
return array (
  'generalDesc' => 
  array (
    'NationalNumberPattern' => '
          [17]\\d{6,9}|
          [2-589]\\d{3}(?:\\d{3,6})?|
          6\\d{3}
        ',
    'PossibleNumberPattern' => '\\d{4,10}',
    'ExampleNumber' => '',
  ),
  'fixedLine' => 
  array (
    'NationalNumberPattern' => '
          (?:
            [2-489]|
            7[2-46-8]
          )\\d{7}
        ',
    'PossibleNumberPattern' => '\\d{7,9}',
    'ExampleNumber' => '21234567',
  ),
  'mobile' => 
  array (
    'NationalNumberPattern' => '
          5(?:
            [02346-9]\\d{2}|
            5(?:
              22|
              33|
              44|
              5[58]|
              66|
              77|
              88
            )
          )\\d{5}
        ',
    'PossibleNumberPattern' => '\\d{9}',
    'ExampleNumber' => '501234567',
  ),
  'tollFree' => 
  array (
    'NationalNumberPattern' => '
          1(?:
            80[019]\\d{3}|
            255
          )\\d{3}
        ',
    'PossibleNumberPattern' => '\\d{7,10}',
    'ExampleNumber' => '1800123456',
  ),
  'premiumRate' => 
  array (
    'NationalNumberPattern' => '
          1(?:
            212|
            (?:
              919|
              200
            )\\d{2}
          )\\d{4}
        ',
    'PossibleNumberPattern' => '\\d{8,10}',
    'ExampleNumber' => '1919123456',
  ),
  'sharedCost' => 
  array (
    'NationalNumberPattern' => '1700\\d{6}',
    'PossibleNumberPattern' => '\\d{10}',
    'ExampleNumber' => '1700123456',
  ),
  'noInternationalDialling' => 
  array (
    'NationalNumberPattern' => '
          1700\\d{6}|
          [2-689]\\d{3}
        ',
    'PossibleNumberPattern' => '\\d{4,10}',
    'ExampleNumber' => '1700123456',
  ),
  'id' => 'IL',
  'countryCode' => 972,
  'internationalPrefix' => '0(?:0|1[2-48])',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '0',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' => 
  array (
    0 => 
    array (
      'pattern' => '([2-489])(\\d{3})(\\d{4})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' => 
      array (
        0 => '[2-489]',
      ),
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
    ),
    1 => 
    array (
      'pattern' => '([57]\\d)(\\d{3})(\\d{4})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' => 
      array (
        0 => '[57]',
      ),
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
    ),
    2 => 
    array (
      'pattern' => '(1)([7-9]\\d{2})(\\d{3})(\\d{3})',
      'format' => '$1-$2-$3-$4',
      'leadingDigitsPatterns' => 
      array (
        0 => '1[7-9]',
      ),
      'nationalPrefixFormattingRule' => '$1',
      'domesticCarrierCodeFormattingRule' => '',
    ),
    3 => 
    array (
      'pattern' => '(1255)(\\d{3})',
      'format' => '$1-$2',
      'leadingDigitsPatterns' => 
      array (
        0 => '125',
      ),
      'nationalPrefixFormattingRule' => '$1',
      'domesticCarrierCodeFormattingRule' => '',
    ),
    4 => 
    array (
      'pattern' => '(1200)(\\d{3})(\\d{3})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' => 
      array (
        0 => '120',
      ),
      'nationalPrefixFormattingRule' => '$1',
      'domesticCarrierCodeFormattingRule' => '',
    ),
    5 => 
    array (
      'pattern' => '(1212)(\\d{2})(\\d{2})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' => 
      array (
        0 => '121',
      ),
      'nationalPrefixFormattingRule' => '$1',
      'domesticCarrierCodeFormattingRule' => '',
    ),
    6 => 
    array (
      'pattern' => '(1599)(\\d{6})',
      'format' => '$1-$2',
      'leadingDigitsPatterns' => 
      array (
        0 => '15',
      ),
      'nationalPrefixFormattingRule' => '$1',
      'domesticCarrierCodeFormattingRule' => '',
    ),
    7 => 
    array (
      'pattern' => '(\\d{4})',
      'format' => '*$1',
      'leadingDigitsPatterns' => 
      array (
        0 => '[2-689]',
      ),
      'nationalPrefixFormattingRule' => '$1',
      'domesticCarrierCodeFormattingRule' => '',
    ),
  ),
  'intlNumberFormat' => 
  array (
  ),
  'mainCountryForCode' => NULL,
  'leadingZeroPossible' => NULL,
);