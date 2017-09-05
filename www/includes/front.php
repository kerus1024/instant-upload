<?php
if (!defined("__MINI_PROJECT_#1_INSTANT_UPLOAD_#1_INIT"))
    die("<pre>" . print_r(debug_backtrace(), true) . "</pre>");

class Front {

    private $buffer;

    public function __construct(){

    }

    public function readyUploadForm(){
        $this->buffer = '
                <!DOCTYPE html>
                <html lang="en">
                <head>
                  <meta charset="utf-8"/>
                  <title>Instant Upload</title>
                  <meta name="viewport" content="width=device-width, initial-scale=1.0">
                </head>
                <body>
                  <form action="'.IU_URI.'/" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="mode" value="2">
                  <input type="hidden" name="secretcode" value="secretKey@_@kerusashe">
                  <input type="file" name="upfile">
                  <button type="submit">Upload</button>
                </form>
                </body>
                </html>
                ';
    }

    public function render(){
        echo $this->buffer;
    }

}

