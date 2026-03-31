<?php

function convertToMySQLDateTime($input) {
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4}) (\d{1,2}):(\d{2}) (am|pm)$/i', $input, $m)) {
        $day   = $m[1];
        $month = $m[2];
        $year  = $m[3];
        $hour  = intval($m[4]);
        $minute= $m[5];
        $ampm  = strtolower($m[6]);

        if ($ampm === 'pm' && $hour != 12) $hour += 12;
        if ($ampm === 'am' && $hour == 12) $hour = 0;

        return sprintf('%04d-%02d-%02d %02d:%02d:00', $year, $month, $day, $hour, $minute);
    }

    return null;
}

function toVNDateTime($mysqlDateTime) {
    if (empty($mysqlDateTime)) return '';
    return date("d/m/Y H:i", strtotime($mysqlDateTime));
}
?>
