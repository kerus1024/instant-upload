<?php
if (!defined("__MINI_PROJECT_#1_INSTANT_UPLOAD_#1_INIT"))
    die("<pre>" . print_r(debug_backtrace(), true) . "</pre>");

/*
    UploadHandle class
    - 업로드 파일을 맡은 후 경로출력까지 담당합니다.
    - 복수 파일업로드는 맡지 않습니다.
*/
class UploadHandle {

    private $protocol;
    private $method;
    private $uri;

    private $files;
    private $outputBuffer;
    private $outputMode; // 0 : JSON, 1 : XML, 2 : textRAW
    private $outputType;

    public function __construct(array $files, int $mode){

        $this->protocol = $_SERVER["SERVER_PROTOCOL"]  ?? NULL;
        $this->method   = $_SERVER["REQUEST_METHOD"] ?? NULL;
        $this->uri      = $_SERVER["REQUEST_URI"]    ?? NULL;

        $this->files = $files;

        $this->outputMode = $mode;
        $this->outputType = "text/plain";
        $this->distinguish();
    }

    //
    //  단수파일을 처리
    //
    public function distinguish(){

        if(isset($this->files['upfile']) && isset($this->files['upfile']['name'])){

            if($this->files['upfile']['error'] !== UPLOAD_ERR_OK){
                return $this->complete(503, 'Service Unavailable');
            } else {

                $parser = new Parser();

                $json = $parser->getData();
                $uniqueid = $json["uniqueid"];

                $time       = time();
                $year       = date("Y", $time);
                $month      = date("m", $time);
                $days       = date("d", $time);
                $secret     = substr(md5("MINI-PROJECT".rand()), 0, 6);
                $extension  = ".".str_replace(".", "", pathinfo($this->files["upfile"]["name"], PATHINFO_EXTENSION));

                $json["map"][$year][$month][$days][$uniqueid] = array(
                    "file"      => "{$time}-{$uniqueid}-{$secret}",
                    "name"      => $this->files["upfile"]["name"],
                    "extension" => $extension,
                    "type"      => $this->files["upfile"]["type"],
                    "size"      => $this->files["upfile"]["size"]
                );
                
                // 폴더 관리 및 파일 이동
                $textDirectory = IU_FILE_DIRECTORY."/".$year."/".$month."/".$days;
                $filePath      = "{$textDirectory}/{$time}-{$uniqueid}-{$secret}{$extension}";

                if(!is_dir($textDirectory)){
                    mkdir($textDirectory, 0770, true);
                }

                move_uploaded_file($this->files['upfile']['tmp_name'], $filePath);

                $parser->commit($json);

                $resultPath = IU_URI . "/" . $year . "/" . $month . "/" . $days . "/" . "{$time}-{$uniqueid}-{$secret}" . $extension;

                // 0 : JSON, 1 : XML, 2 : textRAW
                switch($this->outputMode){

                    case 1: // XML
                        $this->outputType   = 'text/xml';
                        $this->outputBuffer = '
                        <?xml version="1.0" encoding="UTF-8"?'.'>
                        <result>
                            <path>'.$resultPath.'</path>
                        </result>
                        ';
                        break;

                    case 2:
                        $this->outputType  = 'text/html';
                        $this->outputBuffer = '
                            Completed file upload!<br />
                            Link : <a href="'.$resultPath.'" target="_blank">'.$resultPath.'</a><br />
                            <input type="text" readonly="readonly" value="'.$resultPath.'" onclick="this.select()" style="background: #eacc7e">
                        ';
                        break;

                    default:
                        $this->outputType   = "application/json";
                        $this->outputBuffer = json_encode(array(
                            "path"      => $resultPath,
                            "name"      => $this->files['upfile']['name'],
                            "size"      => $this->files['upfile']['size'],
                            "extension" => $extension,
                            "type"      => $this->files['upfile']['type']
                        ));
                        break;

                }

                return $this->complete(200, 'OK');

            }

        } else {
            return $this->complete(503, 'Service Unavailable');
        }

    }

    public function complete(int $status, string $statusmsg, string $msg = NULL){

        Header("{$this->protocol} {$status} {$statusmsg}");
        Header("Content-Type: {$this->outputType}");
        echo $msg;
        echo $this->outputBuffer;
    }

}
