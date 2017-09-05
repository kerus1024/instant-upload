<?php
if (!defined("__MINI_PROJECT_#1_INSTANT_UPLOAD_#1_INIT"))
    die("<pre>" . print_r(debug_backtrace(), true) . "</pre>");

/*
Output class
- 파일을 출력해주는 역할
*/
class Output {

    private $file;
    private $info;

    //
    // Routes 에서 이미 검증했으므로, 딱히 검증이 필요하진 않음.
    //
    public function __construct($year, $month, $days, $unique, $time, $secret, $extension) {
        $this->file = IU_FILE_DIRECTORY . "/" . $year . "/" . $month . "/" . $days . "/{$time}-{$unique}-{$secret}{$extension}";

        $parser = new Parser();
        $json   = $parser->getData();

        $this->info = $json["map"][$year][$month][$days][$unique];

        $this->output();
    }

    private function output() {

        $filename = $this->info["name"];
        $location = $this->file;

        $mimeType = $this->info["type"];
        if (!file_exists($location)) {
            header("HTTP/1.1 404 Not Found");
            return;
        }

        $size = filesize($location);
        $time = date('r', filemtime($location));

        $fm = @fopen($location, 'rb');
        if (!$fm) {
            header("HTTP/1.1 505 Internal server error");
            return;
        }

        $begin = 0;
        $end   = $size - 1;


        if (isset($_SERVER['HTTP_RANGE'])) {
            if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
                $begin = intval($matches[1]);
                if (!empty($matches[2])) {
                    $end = intval($matches[2]);
                }
            }
        }
        if (isset($_SERVER['HTTP_RANGE'])) {
            header('HTTP/1.1 206 Partial Content');
        } else {
            header('HTTP/1.1 200 OK');
        }

        header("Content-Type: $mimeType");
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Accept-Ranges: bytes');
        header('Content-Length:' . (($end - $begin) + 1));
        if (isset($_SERVER['HTTP_RANGE'])) {
            header("Content-Range: bytes $begin-$end/$size");
        }
        header("Content-Disposition: inline; filename=$filename");
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: $time");

        $cur = $begin;
        fseek($fm, $begin, 0);

        while (!feof($fm) && $cur <= $end && (connection_status() == 0)) {
            print fread($fm, min(1024 * 16, ($end - $cur) + 1));
            $cur += 1024 * 16;
        }

    }

}