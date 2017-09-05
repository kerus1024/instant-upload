<?php
if (!defined("__MINI_PROJECT_#1_INSTANT_UPLOAD_#1_INIT"))
    die("<pre>" . print_r(debug_backtrace(), true) . "</pre>");

class Routes {

    private $protocol;
    private $method;
    private $uri;

    private $routes;

    public function __construct() {
        $this->protocol = $_SERVER["HTTP_PROTOCOL"]  ?? NULL;
        $this->method   = $_SERVER["REQUEST_METHOD"] ?? NULL;
        $this->uri      = $_SERVER["REQUEST_URI"]    ?? NULL;

        if (!$this->checkPermissions(IU_FILE_INFO, IU_FILE_DIRECTORY))
            die("Check permissions.");

    }

    public function checkPermissions(string $save_dat, string $save_dir): bool {
        return ((is_writable($save_dat) && !is_dir($save_dat)) && (is_writable($save_dir) && is_dir($save_dir)));
    }

    //
    // Routes->run();
    // 프로그램이 시작하는 구간입니다.
    // php 스크립트는 웹엔진이 요청 할 떄마다 실행합니다.
    //
    public function run() {

        $parser = new Parser();

        switch ($this->method) {

            case "GET":{

                switch($this->uri){

                    case "/":{

                        Header("Location: /upload-form");
                        exit;

                    }

                    case "/upload-form":{

                        $Front = new Front();
                        $Front->readyUploadForm();
                        $Front->render();

                        break;
                    }

                    default:{
                        $matches = false;
                        preg_match('/^\/([0-9]{4})\/([0-9]{2})\/([0-9]{2})\/([0-9]{10})\-([0-9]+)\-([0-9a-z]{6})(.*)$/', $this->uri, $matches);

                        if ($matches) {
                            // 파일존재확인

                            $year      = $matches[1];
                            $month     = $matches[2];
                            $days      = $matches[3];
                            $time      = $matches[4];
                            $unique    = $matches[5];
                            $secret    = $matches[6];
                            $extension = $matches[7];

                            if ($parser->checkFileExists($year, $month, $days, $unique, $time, $secret)) {

                                new Output($year, $month, $days, $unique, $time, $secret, $extension);

                            } else {
                                Header($this->protocol . " 404 Not Found");
                                echo '404 Not Found';
                            }

                        } else {
                            Header($this->protocol . " 404 Not Found");
                            echo '404 Not Found';
                        }


                    }


                    break;

                }

                break;

            }

            case "POST":{

                $postSecretCode = $_POST["secretcode"] ?? NULL;

                if($postSecretCode === IU_SECRET_CODE){
                    
                    if (isset($_FILES)) {
                        $mode         = $_POST["mode"] ?? 0;
                        $mode         = intval($mode);
                        $UploadHandle = new UploadHandle($_FILES, $mode);
                    } else {
                        Header($this->protocol . " 400 Bad Request");
                        echo 'Unable to handle request.';
                    }

                } else {
                    Header($this->protocol . " 401 Unauthorized");
                    echo 'Unauthorized';
                }

                break;

            }

            default:{
                Header($this->protocol . " 400 Bad Request");
                echo 'Unable to handle request.';
                break;
            }

        }

    }

}
