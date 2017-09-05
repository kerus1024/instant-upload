<?php
if(!defined("__MINI_PROJECT_#1_INSTANT_UPLOAD_#1_CALL")) die("<pre>".print_r(debug_backtrace(), true)."</pre>");
define("__MINI_PROJECT_#1_INSTANT_UPLOAD_#1_INIT", true);

// ERROR REPORTING FOR PHP
error_reporting(E_ALL);
ini_set("display_errors", "on");

// 파일 정보 저장 경로
define("IU_FILE_INFO", "/home/kerus1024/webservices/instant-upload/dat.json");

// 파일 저장 경로
define("IU_FILE_DIRECTORY", "/home/kerus1024/webservices/instant-upload/data");

// 나만 업로드 하기 위한 시크릿 코드
define("IU_SECRET_CODE", "123456789");

// 서버주소 - 마지막 /는 제외
define("IU_URI", "https://uploads.kerus.net");

// PHP Auto Loading
// Warning
// This feature has been DEPRECATED as of PHP 7.2.0. Relying on this feature is highly discouraged.

function __autoload($class_name){
  include_once "./includes/".strtolower($class_name).".php";
}

ob_start();
