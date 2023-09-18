<?php

namespace App\Enums;

enum OcrSpaceLanguage: string
{
    case      ARABIC              = 'ara';
    case      BULGARIAN           = 'bul';
    case      CHINESE_Simplified  = 'chs';
    case      CHINESE_Traditional = 'cht';
    case      CROATIAN            = 'hrv';
    case      CZECH               = 'cze';
    case      DANISH              = 'dan';
    case      DUTCH               = 'dut';
    case      ENGLISH             = 'eng';
    case      FINNISH             = 'fin';
    case      FRENCH              = 'fre';
    case      GERMAN              = 'ger';
    case      GREEK               = 'gre';
    case      HUNGARIAN           = 'hun';
    case      KOREAN              = 'kor';
    case      ITALIAN             = 'ita';
    case      JAPANESE            = 'jpn';
    case      POLISH              = 'pol';
    case      PORTUGUESE          = 'por';
    case      RUSSIAN             = 'rus';
    case      SLOVENIAN           = 'slv';
    case      SPANISH             = 'spa';
    case      SWEDISH             = 'swe';
    case      TURKISH             = 'tur';

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
