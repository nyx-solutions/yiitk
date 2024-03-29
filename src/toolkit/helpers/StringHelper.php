<?php

    namespace yiitk\helpers;

    use Exception as BaseException;

    /**
     * Class StringHelper
     *
     * @package yiitk\helpers
     */
    class StringHelper extends \yii\helpers\StringHelper
    {
        use EncodingTrait;
        use RandomStringsTrait;

        public const CASE_UPPER = MB_CASE_UPPER;
        public const CASE_LOWER = MB_CASE_LOWER;
        public const CASE_TITLE = MB_CASE_TITLE;

        public const ID_PATTERN_LENGTH = 9;

        #region Numbers
        /**
         * Recebe uma string formatada e retorna apenas os seus números.
         *
         * @param string $content
         *
         * @return string
         */
        public static function justNumbers($content = '')
        {
            $content = (string)$content;

            /** @noinspection NotOptimalRegularExpressionsInspection */
            return (string)preg_replace('/([^0-9]+)/', '', $content);
        }

        /**
         * @param string $content
         *
         * @return string
         */
        public static function justLetters($content = '')
        {
            $content = (string)$content;

            return (string)preg_replace('/([^A-Za-z]+)/', '', $content);
        }

        /**
         * @param int|string $value
         * @param bool|int $upper
         *
         * @return string
         *
         * @noinspection NestedTernaryOperatorInspection
         * @noinspection TypeUnsafeComparisonInspection
         * @noinspection OpAssignShortSyntaxInspection
         * @noinspection ForeachInvariantsInspection
         * @noinspection ElvisOperatorCanBeUsedInspection
         * @noinspection PhpTernaryExpressionCanBeReducedToShortVersionInspection
         */
        public static function toSpelledNumber(mixed $value = 0, $upper = false)
        {
            $value = (string)$value;

            if (strpos($value, ',') > 0) {
                $value = str_replace(['.', ','], ['', '.'], $value);
            }

            $singular = ['centavo', 'real', 'mil', 'milhão', 'bilhão', 'trilhão', 'quatrilhão'];
            $plural = ['centavos', 'reais', 'mil', 'milhões', 'bilhões', 'trilhões', 'quatrilhões'];
            $c = [
                '',
                'cem',
                'duzentos',
                'trezentos',
                'quatrocentos',
                'quinhentos',
                'seiscentos',
                'setecentos',
                'oitocentos',
                'novecentos'
            ];
            $d = ['', 'dez', 'vinte', 'trinta', 'quarenta', 'cinquenta', 'sessenta', 'setenta', 'oitenta', 'noventa'];
            $d10 = [
                'dez',
                'onze',
                'doze',
                'treze',
                'quatorze',
                'quinze',
                'dezesseis',
                'dezesete',
                'dezoito',
                'dezenove'
            ];
            $u = ['', 'um', 'dois', 'três', 'quatro', 'cinco', 'seis', 'sete', 'oito', 'nove'];

            $z = 0;

            $value = number_format($value, 2, '.', '.');
            $inteiro = explode('.', $value);
            $cont = count($inteiro);

            for ($i = 0; $i < $cont; $i++) {
                for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++) {
                    $inteiro[$i] = '0'.$inteiro[$i];
                }
            }

            $fim = $cont - ($inteiro[$cont - 1] > 0 ? 1 : 2);
            $rt = '';

            for ($i = 0; $i < $cont; $i++) {
                $value = $inteiro[$i];
                $rc = (($value > 100) && ($value < 200)) ? 'cento' : $c[$value[0]];
                $rd = ($value[1] < 2) ? '' : $d[$value[1]];
                $ru = ($value > 0) ? (($value[1] == 1) ? $d10[$value[2]] : $u[$value[2]]) : '';

                $r = $rc.(($rc && ($rd || $ru)) ? ' e ' : '').$rd.(($rd && $ru) ? ' e ' : '').$ru;
                $t = $cont - 1 - $i;
                $r .= $r ? ' '.($value > 1 ? $plural[$t] : $singular[$t]) : '';
                if ($value == '000') {
                    $z++;
                } elseif ($z > 0) {
                    $z--;
                }
                if (($t == 1) && ($z > 0) && ($inteiro[0] > 0)) {
                    $r .= (($z > 1) ? ' de ' : '').$plural[$t];
                }
                if ($r) {
                    $rt = $rt.((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? (($i < $fim) ? ', ' : ' e ') : ' ').$r;
                }
            }

            if (!$upper) {
                return trim($rt ? $rt : 'zero');
            }

            if ((int)$upper === 2) {
                return trim(strtoupper($rt) ? strtoupper($rt) : 'Zero');
            }

            return trim(ucwords($rt) ? ucwords($rt) : 'Zero');
        }

        /**
         * @param int|string $numbers
         * @param array      $charsTable
         *
         * @return string
         *
         * @throws BaseException
         *
         * @noinspection CallableParameterUseCaseInTypeContextInspection
         */
        public static function stringfyNumbers($numbers, $charsTable = [])
        {
            $numbers = str_split((string)$numbers);

            if (!is_array($charsTable) || count($charsTable) !== 11) {
                $charsTable = ['y', 'p', 'k', 'a', 't', 'n', 'o', 'e', 'q', 'f', 'm'];
            }

            $string = '';

            foreach ($numbers as $number) {
                $number = (int)$number;

                if ($number > 9) {
                    $number = 9;
                }

                $string .= ((random_int(1, 2) % 2 === 0) ? strtoupper((string)$charsTable[$number]) : (string)$charsTable[$number]);
            }

            return $string;
        }
        #endregion

        #region Compare
        /**
         * @param string $originalValue
         * @param string $targetValue
         *
         * @return bool
         */
        public static function compare($originalValue = '', $targetValue = '')
        {
            $originalValue = static::asSlug($originalValue, '_', MB_CASE_UPPER);
            $targetValue   = static::asSlug($targetValue, '_', MB_CASE_UPPER);

            return ($originalValue === $targetValue);
        }
        #endregion

        #region Slug & Filters
        /**
         * @param string $value
         * @param string $spaces
         * @param int    $case
         *
         * @return string
         */
        public static function asSlug($value = '', $spaces = '-', $case = MB_CASE_LOWER)
        {
            return InflectorHelper::slug($value, $spaces, ($case === MB_CASE_LOWER));
        }

        /**
         * @param string $value
         * @param string $spaces
         * @param int    $case
         *
         * @return string
         */
        public static function slug($value = '', $spaces = '-', $case = MB_CASE_LOWER)
        {
            return static::asSlug($value, $spaces, $case);
        }

        /**
         * Remove todos os acentos de uma string
         *
         * @param string $string
         *
         * @return string
         */
        public static function removeAccents($string)
        {
            if (!preg_match('/[\x80-\xff]/', $string)) {
                return $string;
            }

            if (static::seemsUtf8($string)) {
                $chars = [
                    // Decompositions for Latin-1 Supplement
                    chr(194).chr(170)          => 'a',
                    chr(194).chr(186)          => 'o',
                    chr(195).chr(128)          => 'A',
                    chr(195).chr(129)          => 'A',
                    chr(195).chr(130)          => 'A',
                    chr(195).chr(131)          => 'A',
                    chr(195).chr(132)          => 'A',
                    chr(195).chr(133)          => 'A',
                    chr(195).chr(134)          => 'AE',
                    chr(195).chr(135)          => 'C',
                    chr(195).chr(136)          => 'E',
                    chr(195).chr(137)          => 'E',
                    chr(195).chr(138)          => 'E',
                    chr(195).chr(139)          => 'E',
                    chr(195).chr(140)          => 'I',
                    chr(195).chr(141)          => 'I',
                    chr(195).chr(142)          => 'I',
                    chr(195).chr(143)          => 'I',
                    chr(195).chr(144)          => 'D',
                    chr(195).chr(145)          => 'N',
                    chr(195).chr(146)          => 'O',
                    chr(195).chr(147)          => 'O',
                    chr(195).chr(148)          => 'O',
                    chr(195).chr(149)          => 'O',
                    chr(195).chr(150)          => 'O',
                    chr(195).chr(153)          => 'U',
                    chr(195).chr(154)          => 'U',
                    chr(195).chr(155)          => 'U',
                    chr(195).chr(156)          => 'U',
                    chr(195).chr(157)          => 'Y',
                    chr(195).chr(158)          => 'TH',
                    chr(195).chr(159)          => 's',
                    chr(195).chr(160)          => 'a',
                    chr(195).chr(161)          => 'a',
                    chr(195).chr(162)          => 'a',
                    chr(195).chr(163)          => 'a',
                    chr(195).chr(164)          => 'a',
                    chr(195).chr(165)          => 'a',
                    chr(195).chr(166)          => 'ae',
                    chr(195).chr(167)          => 'c',
                    chr(195).chr(168)          => 'e',
                    chr(195).chr(169)          => 'e',
                    chr(195).chr(170)          => 'e',
                    chr(195).chr(171)          => 'e',
                    chr(195).chr(172)          => 'i',
                    chr(195).chr(173)          => 'i',
                    chr(195).chr(174)          => 'i',
                    chr(195).chr(175)          => 'i',
                    chr(195).chr(176)          => 'd',
                    chr(195).chr(177)          => 'n',
                    chr(195).chr(178)          => 'o',
                    chr(195).chr(179)          => 'o',
                    chr(195).chr(180)          => 'o',
                    chr(195).chr(181)          => 'o',
                    chr(195).chr(182)          => 'o',
                    chr(195).chr(184)          => 'o',
                    chr(195).chr(185)          => 'u',
                    chr(195).chr(186)          => 'u',
                    chr(195).chr(187)          => 'u',
                    chr(195).chr(188)          => 'u',
                    chr(195).chr(189)          => 'y',
                    chr(195).chr(190)          => 'th',
                    chr(195).chr(191)          => 'y',
                    chr(195).chr(152)          => 'O',
                    // Decompositions for Latin Extended-A
                    chr(196).chr(128)          => 'A',
                    chr(196).chr(129)          => 'a',
                    chr(196).chr(130)          => 'A',
                    chr(196).chr(131)          => 'a',
                    chr(196).chr(132)          => 'A',
                    chr(196).chr(133)          => 'a',
                    chr(196).chr(134)          => 'C',
                    chr(196).chr(135)          => 'c',
                    chr(196).chr(136)          => 'C',
                    chr(196).chr(137)          => 'c',
                    chr(196).chr(138)          => 'C',
                    chr(196).chr(139)          => 'c',
                    chr(196).chr(140)          => 'C',
                    chr(196).chr(141)          => 'c',
                    chr(196).chr(142)          => 'D',
                    chr(196).chr(143)          => 'd',
                    chr(196).chr(144)          => 'D',
                    chr(196).chr(145)          => 'd',
                    chr(196).chr(146)          => 'E',
                    chr(196).chr(147)          => 'e',
                    chr(196).chr(148)          => 'E',
                    chr(196).chr(149)          => 'e',
                    chr(196).chr(150)          => 'E',
                    chr(196).chr(151)          => 'e',
                    chr(196).chr(152)          => 'E',
                    chr(196).chr(153)          => 'e',
                    chr(196).chr(154)          => 'E',
                    chr(196).chr(155)          => 'e',
                    chr(196).chr(156)          => 'G',
                    chr(196).chr(157)          => 'g',
                    chr(196).chr(158)          => 'G',
                    chr(196).chr(159)          => 'g',
                    chr(196).chr(160)          => 'G',
                    chr(196).chr(161)          => 'g',
                    chr(196).chr(162)          => 'G',
                    chr(196).chr(163)          => 'g',
                    chr(196).chr(164)          => 'H',
                    chr(196).chr(165)          => 'h',
                    chr(196).chr(166)          => 'H',
                    chr(196).chr(167)          => 'h',
                    chr(196).chr(168)          => 'I',
                    chr(196).chr(169)          => 'i',
                    chr(196).chr(170)          => 'I',
                    chr(196).chr(171)          => 'i',
                    chr(196).chr(172)          => 'I',
                    chr(196).chr(173)          => 'i',
                    chr(196).chr(174)          => 'I',
                    chr(196).chr(175)          => 'i',
                    chr(196).chr(176)          => 'I',
                    chr(196).chr(177)          => 'i',
                    chr(196).chr(178)          => 'IJ',
                    chr(196).chr(179)          => 'ij',
                    chr(196).chr(180)          => 'J',
                    chr(196).chr(181)          => 'j',
                    chr(196).chr(182)          => 'K',
                    chr(196).chr(183)          => 'k',
                    chr(196).chr(184)          => 'k',
                    chr(196).chr(185)          => 'L',
                    chr(196).chr(186)          => 'l',
                    chr(196).chr(187)          => 'L',
                    chr(196).chr(188)          => 'l',
                    chr(196).chr(189)          => 'L',
                    chr(196).chr(190)          => 'l',
                    chr(196).chr(191)          => 'L',
                    chr(197).chr(128)          => 'l',
                    chr(197).chr(129)          => 'L',
                    chr(197).chr(130)          => 'l',
                    chr(197).chr(131)          => 'N',
                    chr(197).chr(132)          => 'n',
                    chr(197).chr(133)          => 'N',
                    chr(197).chr(134)          => 'n',
                    chr(197).chr(135)          => 'N',
                    chr(197).chr(136)          => 'n',
                    chr(197).chr(137)          => 'N',
                    chr(197).chr(138)          => 'n',
                    chr(197).chr(139)          => 'N',
                    chr(197).chr(140)          => 'O',
                    chr(197).chr(141)          => 'o',
                    chr(197).chr(142)          => 'O',
                    chr(197).chr(143)          => 'o',
                    chr(197).chr(144)          => 'O',
                    chr(197).chr(145)          => 'o',
                    chr(197).chr(146)          => 'OE',
                    chr(197).chr(147)          => 'oe',
                    chr(197).chr(148)          => 'R',
                    chr(197).chr(149)          => 'r',
                    chr(197).chr(150)          => 'R',
                    chr(197).chr(151)          => 'r',
                    chr(197).chr(152)          => 'R',
                    chr(197).chr(153)          => 'r',
                    chr(197).chr(154)          => 'S',
                    chr(197).chr(155)          => 's',
                    chr(197).chr(156)          => 'S',
                    chr(197).chr(157)          => 's',
                    chr(197).chr(158)          => 'S',
                    chr(197).chr(159)          => 's',
                    chr(197).chr(160)          => 'S',
                    chr(197).chr(161)          => 's',
                    chr(197).chr(162)          => 'T',
                    chr(197).chr(163)          => 't',
                    chr(197).chr(164)          => 'T',
                    chr(197).chr(165)          => 't',
                    chr(197).chr(166)          => 'T',
                    chr(197).chr(167)          => 't',
                    chr(197).chr(168)          => 'U',
                    chr(197).chr(169)          => 'u',
                    chr(197).chr(170)          => 'U',
                    chr(197).chr(171)          => 'u',
                    chr(197).chr(172)          => 'U',
                    chr(197).chr(173)          => 'u',
                    chr(197).chr(174)          => 'U',
                    chr(197).chr(175)          => 'u',
                    chr(197).chr(176)          => 'U',
                    chr(197).chr(177)          => 'u',
                    chr(197).chr(178)          => 'U',
                    chr(197).chr(179)          => 'u',
                    chr(197).chr(180)          => 'W',
                    chr(197).chr(181)          => 'w',
                    chr(197).chr(182)          => 'Y',
                    chr(197).chr(183)          => 'y',
                    chr(197).chr(184)          => 'Y',
                    chr(197).chr(185)          => 'Z',
                    chr(197).chr(186)          => 'z',
                    chr(197).chr(187)          => 'Z',
                    chr(197).chr(188)          => 'z',
                    chr(197).chr(189)          => 'Z',
                    chr(197).chr(190)          => 'z',
                    chr(197).chr(191)          => 's',
                    // Decompositions for Latin Extended-B
                    chr(200).chr(152)          => 'S',
                    chr(200).chr(153)          => 's',
                    chr(200).chr(154)          => 'T',
                    chr(200).chr(155)          => 't',
                    // Euro Sign
                    chr(226).chr(130).chr(172) => 'E',
                    // GBP (Pound) Sign
                    chr(194).chr(163)          => '',
                    // Vowels with diacritic (Vietnamese)
                    // unmarked
                    chr(198).chr(160)          => 'O',
                    chr(198).chr(161)          => 'o',
                    chr(198).chr(175)          => 'U',
                    chr(198).chr(176)          => 'u',
                    // grave accent
                    chr(225).chr(186).chr(166) => 'A',
                    chr(225).chr(186).chr(167) => 'a',
                    chr(225).chr(186).chr(176) => 'A',
                    chr(225).chr(186).chr(177) => 'a',
                    chr(225).chr(187).chr(128) => 'E',
                    chr(225).chr(187).chr(129) => 'e',
                    chr(225).chr(187).chr(146) => 'O',
                    chr(225).chr(187).chr(147) => 'o',
                    chr(225).chr(187).chr(156) => 'O',
                    chr(225).chr(187).chr(157) => 'o',
                    chr(225).chr(187).chr(170) => 'U',
                    chr(225).chr(187).chr(171) => 'u',
                    chr(225).chr(187).chr(178) => 'Y',
                    chr(225).chr(187).chr(179) => 'y',
                    // hook
                    chr(225).chr(186).chr(162) => 'A',
                    chr(225).chr(186).chr(163) => 'a',
                    chr(225).chr(186).chr(168) => 'A',
                    chr(225).chr(186).chr(169) => 'a',
                    chr(225).chr(186).chr(178) => 'A',
                    chr(225).chr(186).chr(179) => 'a',
                    chr(225).chr(186).chr(186) => 'E',
                    chr(225).chr(186).chr(187) => 'e',
                    chr(225).chr(187).chr(130) => 'E',
                    chr(225).chr(187).chr(131) => 'e',
                    chr(225).chr(187).chr(136) => 'I',
                    chr(225).chr(187).chr(137) => 'i',
                    chr(225).chr(187).chr(142) => 'O',
                    chr(225).chr(187).chr(143) => 'o',
                    chr(225).chr(187).chr(148) => 'O',
                    chr(225).chr(187).chr(149) => 'o',
                    chr(225).chr(187).chr(158) => 'O',
                    chr(225).chr(187).chr(159) => 'o',
                    chr(225).chr(187).chr(166) => 'U',
                    chr(225).chr(187).chr(167) => 'u',
                    chr(225).chr(187).chr(172) => 'U',
                    chr(225).chr(187).chr(173) => 'u',
                    chr(225).chr(187).chr(182) => 'Y',
                    chr(225).chr(187).chr(183) => 'y',
                    // tilde
                    chr(225).chr(186).chr(170) => 'A',
                    chr(225).chr(186).chr(171) => 'a',
                    chr(225).chr(186).chr(180) => 'A',
                    chr(225).chr(186).chr(181) => 'a',
                    chr(225).chr(186).chr(188) => 'E',
                    chr(225).chr(186).chr(189) => 'e',
                    chr(225).chr(187).chr(132) => 'E',
                    chr(225).chr(187).chr(133) => 'e',
                    chr(225).chr(187).chr(150) => 'O',
                    chr(225).chr(187).chr(151) => 'o',
                    chr(225).chr(187).chr(160) => 'O',
                    chr(225).chr(187).chr(161) => 'o',
                    chr(225).chr(187).chr(174) => 'U',
                    chr(225).chr(187).chr(175) => 'u',
                    chr(225).chr(187).chr(184) => 'Y',
                    chr(225).chr(187).chr(185) => 'y',
                    // acute accent
                    chr(225).chr(186).chr(164) => 'A',
                    chr(225).chr(186).chr(165) => 'a',
                    chr(225).chr(186).chr(174) => 'A',
                    chr(225).chr(186).chr(175) => 'a',
                    chr(225).chr(186).chr(190) => 'E',
                    chr(225).chr(186).chr(191) => 'e',
                    chr(225).chr(187).chr(144) => 'O',
                    chr(225).chr(187).chr(145) => 'o',
                    chr(225).chr(187).chr(154) => 'O',
                    chr(225).chr(187).chr(155) => 'o',
                    chr(225).chr(187).chr(168) => 'U',
                    chr(225).chr(187).chr(169) => 'u',
                    // dot below
                    chr(225).chr(186).chr(160) => 'A',
                    chr(225).chr(186).chr(161) => 'a',
                    chr(225).chr(186).chr(172) => 'A',
                    chr(225).chr(186).chr(173) => 'a',
                    chr(225).chr(186).chr(182) => 'A',
                    chr(225).chr(186).chr(183) => 'a',
                    chr(225).chr(186).chr(184) => 'E',
                    chr(225).chr(186).chr(185) => 'e',
                    chr(225).chr(187).chr(134) => 'E',
                    chr(225).chr(187).chr(135) => 'e',
                    chr(225).chr(187).chr(138) => 'I',
                    chr(225).chr(187).chr(139) => 'i',
                    chr(225).chr(187).chr(140) => 'O',
                    chr(225).chr(187).chr(141) => 'o',
                    chr(225).chr(187).chr(152) => 'O',
                    chr(225).chr(187).chr(153) => 'o',
                    chr(225).chr(187).chr(162) => 'O',
                    chr(225).chr(187).chr(163) => 'o',
                    chr(225).chr(187).chr(164) => 'U',
                    chr(225).chr(187).chr(165) => 'u',
                    chr(225).chr(187).chr(176) => 'U',
                    chr(225).chr(187).chr(177) => 'u',
                    chr(225).chr(187).chr(180) => 'Y',
                    chr(225).chr(187).chr(181) => 'y',
                    // Vowels with diacritic (Chinese, Hanyu Pinyin)
                    chr(201).chr(145)          => 'a',
                    // macron
                    chr(199).chr(149)          => 'U',
                    chr(199).chr(150)          => 'u',
                    // acute accent
                    chr(199).chr(151)          => 'U',
                    chr(199).chr(152)          => 'u',
                    // caron
                    chr(199).chr(141)          => 'A',
                    chr(199).chr(142)          => 'a',
                    chr(199).chr(143)          => 'I',
                    chr(199).chr(144)          => 'i',
                    chr(199).chr(145)          => 'O',
                    chr(199).chr(146)          => 'o',
                    chr(199).chr(147)          => 'U',
                    chr(199).chr(148)          => 'u',
                    chr(199).chr(153)          => 'U',
                    chr(199).chr(154)          => 'u',
                    // grave accent
                    chr(199).chr(155)          => 'U',
                    chr(199).chr(156)          => 'u',
                ];

                $string = strtr($string, $chars);
            } else {
                // Assume ISO-8859-1 if not UTF-8
                $chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158).chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194).chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202).chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210).chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218).chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227).chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235).chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243).chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251).chr(252).chr(253).chr(255);
                $chars['out'] = 'EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy';

                $string = strtr($string, $chars['in'], $chars['out']);
                $double_chars['in'] = [
                    chr(140),
                    chr(156),
                    chr(198),
                    chr(208),
                    chr(222),
                    chr(223),
                    chr(230),
                    chr(240),
                    chr(254)
                ];
                $double_chars['out'] = ['OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th'];

                $string = str_replace($double_chars['in'], $double_chars['out'], $string);
            }

            return $string;
        }
        #endregion

        #region Lower/Upper/Title Cases
        /**
         * @param string $string
         * @param int    $mode
         *
         * @return string
         */
        public static function convertCase($string, $mode = self::CASE_UPPER)
        {
            return mb_convert_case((string)$string, $mode, 'UTF-8');
        }

        /**
         * @param string $string
         * @param bool   $trim
         *
         * @return string
         */
        public static function toLowerCase($string, $trim = false)
        {
            if ($trim) {
                $string = trim($string);
            }

            return static::convertCase($string, self::CASE_LOWER);
        }

        /**
         * @param string $string
         * @param bool   $trim
         *
         * @return string
         *
         * @noinspection PhpRedundantOptionalArgumentInspection
         */
        public static function toUpperCase($string, $trim = false)
        {
            if ($trim) {
                $string = trim($string);
            }

            return static::convertCase($string, self::CASE_UPPER);
        }

        /**
         * @param string $string
         * @param bool   $trim
         *
         * @return string
         */
        public static function toTitleCase($string, $trim = false)
        {
            if ($trim) {
                $string = trim($string);
            }

            return static::convertCase($string, self::CASE_TITLE);
        }
        #endregion

        #region Passwords

        /**
         * @param int $length
         * @param int $upper
         * @param int $lower
         * @param int $digit
         * @param int $special
         *
         * @return string
         * @throws BaseException
         */
        public static function generatePassword($length = 0, $upper = 0, $lower = 0, $digit = 0, $special = 0)
        {
            return static::generateRandomString($length, $upper, $lower, $digit, $special);
        }
        #endregion

        #region E-mails
        /**
         * @param string $email
         *
         * @return string
         */
        public static function obfuscateEmail($email)
        {
            $emailSplit = explode('@', $email);
            $email      = $emailSplit[0];
            $len        = strlen($email) - 1;

            for ($i = 1; $i < $len; $i++) {
                $email[$i] = '*';
            }

            return $email.'@'.$emailSplit[1];
        }
        #endregion

        #region Currency
        /**
         * @param float $amount
         * @param bool  $withPrefix
         *
         * @return string
         */
        public static function toBrazilianCurrency($amount, $withPrefix = true)
        {
            $amount = (float)$amount;
            $withPrefix = (bool)$withPrefix;

            return (($withPrefix) ? 'R$ ' : '').number_format($amount, 2, ',', '.');
        }
        #endregion

        #region Name & Surname
        /**
         * @param string $fullName
         *
         * @return string
         */
        public static function firstName($fullName)
        {
            $firstName = '';

            $fullName = (string)$fullName;

            if (!empty($fullName)) {
                $fullNameParts = explode(' ', $fullName);

                if (!empty($fullNameParts)) {
                    $firstName = static::convertCase(reset($fullNameParts), self::CASE_TITLE);
                }
            }

            return $firstName;
        }

        /**
         * @param string $name
         *
         * @return string
         */
        public static function asFirstName($name)
        {
            return static::firstName($name);
        }

        /**
         * @param string $fullName
         *
         * @return string
         */
        public static function lastName($fullName)
        {
            $lastName = '';

            $fullName = (string)$fullName;

            if (!empty($fullName)) {
                $fullNameParts = explode(' ', $fullName);

                if (!empty($fullNameParts)) {
                    $lastName = static::convertCase(end($fullNameParts), self::CASE_TITLE);
                }
            }

            return $lastName;
        }

        /**
         * @param string $name
         *
         * @return string
         */
        public static function asLastName($name)
        {
            return static::lastName($name);
        }

        /**
         * @param string $fullName
         *
         * @return string
         */
        public static function surname($fullName)
        {
            return static::lastName($fullName);
        }
        #endregion

        #region Other
        /**
         * @param string $text
         * @param int    $max
         * @param string $suffix
         *
         * @return string
         */
        public static function getTextWithLimit($text, $max = 255, $suffix = '...')
        {
            $text = (string)$text;

            if (strlen($text) > $max) {
                $textParts = explode(' ', $text);

                if (is_array($textParts) && count($textParts) > 1) {
                    $tempNewText = '';

                    $newText = $tempNewText;

                    foreach ($textParts as $word) {
                        $tempNewText .= ((!empty($tempNewText)) ? ' ' : '').$word;

                        if (strlen($tempNewText) > $max) {
                            break;
                        }

                        $newText = $tempNewText.$suffix;
                    }

                    return $newText;
                }

                return substr($text, 0, $max).$suffix;
            }

            return $text;
        }

        /**
         * @param string $singular
         * @param string $plural
         * @param int    $n
         * @param bool   $emptyOnZero
         *
         * @return string
         */
        public static function pluralize($singular, $plural, $n = 0, $emptyOnZero = false)
        {
            $n = (int)$n;

            if ($n === 1) {
                return sprintf($singular, $n);
            }

            if ((bool)$emptyOnZero && $n === 0) {
                return '';
            }

            return sprintf($plural, $n);
        }

        /**
         * @param string $str
         * @param string $needleStart
         * @param string $needleEnd
         * @param string $replacement
         *
         * @return string
         *
         * @noinspection PhpStrictComparisonWithOperandsOfDifferentTypesInspection
         */
        public static function replaceBetween($str, $needleStart, $needleEnd, $replacement) {
            $pos = strpos($str, $needleStart);
            $start = $pos === false ? 0 : $pos + strlen($needleStart);

            $pos = strpos($str, $needleEnd, $start);
            $end = $start === false ? strlen($str) : $pos;

            return substr_replace($str,$replacement,  $start, $end - $start);
        }
        #endregion
    }
