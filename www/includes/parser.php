<?php
if (!defined("__MINI_PROJECT_#1_INSTANT_UPLOAD_#1_INIT"))
    die("<pre>" . print_r(debug_backtrace(), true) . "</pre>");

/*
Parser class
- data.json 에 관한 입출력 및 검증 함수등을 제공합니다.
*/
class Parser {

    private static $data;

    public function __construct() {
        $file       = file_get_contents(IU_FILE_INFO);
        self::$data = json_decode($file, true);
        $this->makeJson();
    }

    //
    // parser->checkFileExists
    // 파일의 존재여부 확인
    //
    public function checkFileExists($year, $month, $days, $unique, $time, $secret): bool {

        // json array에 해당 id가 있나 확인
        if (isset(self::$data["map"][$year][$month][$days][$unique])) {

            $array = self::$data["map"][$year][$month][$days][$unique];

            // ($array["file"] === $time . "-" . $unique . "-" . $secret)
            if (strcmp($array["file"], $time . "-" . $unique . "-" . $secret) === 0) {
                return true;
            }

        }

        return false;

    }

    // JSON을 반환
    public function getData(): array {
        return self::$data;
    }

    // JSON이 비어있는경우 바로쓸수있게 형식화
    private function makeJson(){

        if(!isset(self::$data["uniqueid"]))
            self::$data["uniqueid"] = 1;

        if(!isset(self::$data["map"]))
            self::$data["map"] = array();

        $time  = time();
        $year  = date("Y", $time);
        $month = date("m", $time);
        $days  = date("d", $time);

        if(!isset(self::$data["map"][$year]))
            self::$data["map"][$year] = array();

        if(!isset(self::$data["map"][$year][$month]))
            self::$data["map"][$year][$month] = array();

        if(!isset(self::$data["map"][$year][$month][$days]))
            self::$data["map"][$year][$month][$days] = array();

    }

    // JSON 물리저장
    public function commit(array $jsond){
        $jsond["uniqueid"]++;
        file_put_contents(IU_FILE_INFO, json_encode($jsond), LOCK_EX);
    }


}