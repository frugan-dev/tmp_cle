<?php

/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * classes/class.DateFormat.php v.1.4.0. 26/01/2021
*/

class DateFormat extends Core
{
    private static $dateVars = [];
    private static $timeVars = [];
    private static $year = 2000;
    private static $month = 1;
    private static $day = 1;
    private static $hours = 0;
    private static $minutes = 0;
    private static $seconds = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public static function convertDateBetweenFormat($data, $formatin, $formatout)
    {
        $data = DateTime::createFromFormat($formatin, $data);
        $errors = DateTime::getLastErrors();
        if ($errors['warning_count'] == 0 && $errors['error_count'] == 0) {
            return $data->format($formatout);
        } else {
            return '';
        }
    }

    public static function checkDateFormat($date, $format)
    {
        $d = DateTime::createFromFormat($format, $date);
        $errors = DateTime::getLastErrors();
        return ($errors['warning_count'] == 0 && $errors['error_count'] == 0) ? true : false;
    }

    public static function checkDateTimeIsoIniEndInterval($datetimeisoini, $datetimeisoend, $compare = '>')
    {
        $res = true;
        $res = self::checkDateFormat($datetimeisoini, 'Y-m-d H:i:s');
        if ($res == true) {
            $res = self::checkDateFormat($datetimeisoend, 'Y-m-d H:i:s');
        }
        if ($res == true) {
            $dateini = DateTime::createFromFormat('Y-m-d H:i:s', $datetimeisoini);
            $dateend = DateTime::createFromFormat('Y-m-d H:i:s', $datetimeisoend);
            switch ($compare) {
                default:
                    if ($dateini->getTimestamp() > $dateend->getTimestamp()) {
                        Core::$resultOp->error = 1;
                        $res = false;
                    }
                    break;
            }
        }
        return $res;
    }

    public static function checkDateFormatIniEndInterval($dateini, $dateend, $format, $compare = '>')
    {
        $res = true;
        if ($format == '') {
            $format = 'Y-m-d H:i:s';
        }
        $res = self::checkDateFormat($dateini, $format);
        if ($res == true) {
            $res = self::checkDateFormat($dateend, $format);
        }
        if ($res == true) {
            $dini = DateTime::createFromFormat($format, $dateini);
            $dend = DateTime::createFromFormat($format, $dateend);
            switch ($compare) {
                default:
                    if ($dini->getTimestamp() > $dend->getTimestamp()) {
                        Core::$resultOp->error = 1;
                        $res = false;
                    } else {
                    }
                    break;
            }
        }
        return $res;
    }

    public static function sumTheTime($times)
    {
        $seconds = 0;
        $sum_time = '00:00:00';
        if (isset($times) && is_array($times) && count($times) > 0) {
            foreach ($times as $time) {
                [$hour, $minute, $second] = explode(':', (string) $time);
                $seconds += $hour * 3600;
                $seconds += $minute * 60;
                $seconds += $second;
            }
            $hours = floor($seconds / 3600);
            $seconds -= $hours * 3600;
            $minutes  = floor($seconds / 60);
            $seconds -= $minutes * 60;
            $sum_time = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds); // Thanks to Patrick
        }
        return $sum_time;
    }

    public static function dateExtractFormat($d, $null = '')
    {
        // check Day -> (0[1-9]|[1-2][0-9]|3[0-1])
        // check Month -> (0[1-9]|1[0-2])
        // check Year -> [0-9]{4} or \d{4}
        $patterns = [
            '/\b\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}.\d{3,8}Z\b/' => 'Y-m-d\TH:i:s.u\Z', // format DATE ISO 8601
            '/\b\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])\b/' => 'Y-m-d',
            '/\b\d{4}-(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])\b/' => 'Y-d-m',
            '/\b(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-\d{4}\b/' => 'd-m-Y',
            '/\b(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-\d{4}\b/' => 'm-d-Y',

            '/\b\d{4}\/(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\b/' => 'Y/d/m',
            '/\b\d{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\b/' => 'Y/m/d',
            '/\b(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/\d{4}\b/' => 'd/m/Y',
            '/\b(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/\d{4}\b/' => 'm/d/Y',

            '/\b\d{4}\.(0[1-9]|1[0-2])\.(0[1-9]|[1-2][0-9]|3[0-1])\b/' => 'Y.m.d',
            '/\b\d{4}\.(0[1-9]|[1-2][0-9]|3[0-1])\.(0[1-9]|1[0-2])\b/' => 'Y.d.m',
            '/\b(0[1-9]|[1-2][0-9]|3[0-1])\.(0[1-9]|1[0-2])\.\d{4}\b/' => 'd.m.Y',
            '/\b(0[1-9]|1[0-2])\.(0[1-9]|[1-2][0-9]|3[0-1])\.\d{4}\b/' => 'm.d.Y',

            // for 24-hour | hours seconds
            '/\b(?:2[0-3]|[01][0-9]):[0-5][0-9](:[0-5][0-9])\.\d{3,6}\b/' => 'H:i:s.u',
            '/\b(?:2[0-3]|[01][0-9]):[0-5][0-9](:[0-5][0-9])\b/' => 'H:i:s',
            '/\b(?:2[0-3]|[01][0-9]):[0-5][0-9]\b/' => 'H:i',

            // for 12-hour | hours seconds
            '/\b(?:1[012]|0[0-9]):[0-5][0-9](:[0-5][0-9])\.\d{3,6}\b/' => 'h:i:s.u',
            '/\b(?:1[012]|0[0-9]):[0-5][0-9](:[0-5][0-9])\b/' => 'h:i:s',
            '/\b(?:1[012]|0[0-9]):[0-5][0-9]\b/' => 'h:i',

            '/\.\d{3}\b/' => '.v',
        ];
        //$d = preg_replace('/\b\d{2}:\d{2}\b/', 'H:i',$d);
        $d = preg_replace(array_keys($patterns), array_values($patterns), (string) $d);
        return preg_match('/\d/', (string) $d) ? $null : $d;
    }

    public static function dateFormating($date, $format = 'd/m/Y H:i', $in_format = false, $f = '')
    {
        $isformat = self::dateExtractFormat($date);
        $d = DateTime::createFromFormat($isformat, $date);
        $format = $in_format ? $isformat : $format;
        if ($format) {
            if (in_array($format, [ 'Y-m-d\TH:i:s.u\Z', 'DATE_ISO8601', 'ISO8601' ])) {
                $f = $d ? $d->format('Y-m-d\TH:i:s.') . substr($d->format('u'), 0, 3) . 'Z' : '';
            } else {
                $f = $d ? $d->format($format) : '';
            }
        }
        return $f;
    }

    // layout
    public static function getDateTimeIsoFormatString($datetimeIso = '', $format = '', $opt = null)
    {
        if ($datetimeIso != '') {
            self::explodeDateTimeIso($datetimeIso);
        }
        $s = self::getDateString($format);
        return $s;
    }

    // tools
    public static function getDateString($format = '')
    {
        $s = '';
        $month = intval(self::$month);
        $day = intval(self::$day);

        $format = preg_replace('/%DAY%/', (string) self::$day, (string) $format);
        $format = preg_replace('/%STRINGMONTH%/', ucfirst((string) Config::$langVars['lista mesi'][$month]), (string) $format);
        $format = preg_replace('/%STRINGDATADAY%/', (string) self::getDayOfDate(Config::$langVars['lista giorni'], []), (string) $format);
        $format = preg_replace('/%MONTH%/', (string) self::$month, (string) $format);
        $format = preg_replace('/%YEAR%/', (string) self::$year, (string) $format);
        $format = preg_replace('/%HH%/', (string) self::$hours, (string) $format);
        $format = preg_replace('/%II%/', (string) self::$minutes, (string) $format);
        $s = $format;

        $s = match ($format) {
            'dd StringMonth YYYY' => self::$day. ' '.ucfirst((string) Config::$langVars['lista giorni']).' '.self::$year,
            'StringDay StringMonth YYYY' => self::$day. ' '.ucfirst((string) Config::$langVars['lista giorni']).' '.self::$year,
            'StringMonth dd, YYYY' => ucfirst((string) $langMonts[$month]).' '.self::$day. ', '.self::$year,
            'StringMonth' => ucfirst((string) $langMonts[$month]),
            'dd/mm/YYYY' => self::$day.'/'.self::$month.'/'.self::$year,
            'hh:mm' => self::$hours.':'.self::$minutes,
            'YYYY-mm-dd' => self::$year.'-'.self::$month.'-'.self::$day,
            'dd' => self::$day,
            default => $s,
        };
        return $s;
    }

    public static function explodeDateTimeIso($datetime)
    {
        $d = explode(' ', (string) $datetime);
        [$date, $time] = $d;
        self::$dateVars = explode('-', $date);
        self::$timeVars =  explode(':', $time);
        self::$day = self::$dateVars[2];
        self::$month = self::$dateVars[1];
        self::$year = self::$dateVars[0];
        self::$hours = self::$timeVars[0];
        self::$minutes = self::$timeVars[1];
        self::$seconds = self::$timeVars[2];
    }

    public static function getDayOfDate($langDays, $opt)
    {
        $optDef = [];
        $opt = array_merge($optDef, $opt);
        $dt = self::$year.'-'.self::$month.'-'.self::$day;
        $date = DateTime::createFromFormat('Y-m-d', $dt);
        $errors = DateTime::getLastErrors();
        if (!is_countable($errors) || $errors['error_count'] > 0 || $errors['warning_count'] > 0) {
            return 'n.d.';
        } else {
            $d = intval($date->format('N'));
            $ds = $langDays[$d];
            return $ds;
        }
    }

}
