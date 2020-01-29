<?php

   _log("--------------------------------------------");
   _log("Script iniciado");


   // Make sure that a POST is asked for
      header('Access-Control-Allow-Origin: *');
      if ($_SERVER['REQUEST_METHOD'] != 'POST') {
         die("POST request expected");
      }

   //Errors catching for closed flash file   
      error_reporting(E_ALL && E_WARNING && E_NOTICE);
      ini_set('display_errors', 0);
      ini_set('log_errors', 1);
      $errors = 0;

   //include scripts to handle different questions types in iSpring QuizMaker (produced by iSpring team and available in their forum)
      require_once("includes/common.inc.php");
      try {
         $requestParameters = RequestParametersParser::getRequestParameters($_POST, !empty($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : null);
         _log((string)$requestParameters);
      } catch (Exception $e){ //WRITE ERROR IN A LOG FILE
         error_log($e);
         echo "Error: " . $e->getMessage();
         _errorlog("Erro no try: " . $e->getMessage());
      }


   //GET RESULTS FROM QUIZ AND POST TO SERVER
      try {
         //GET RESULTS AND PARSE THEM
            $quizResults = new QuizResults();
            $quizResults->InitFromRequest($requestParameters);
            $generator = QuizReportFactory::CreateGenerator($quizResults, $requestParameters);
            $report = $generator->createReport();
      $lastname = $_POST['USER_LAST_NAME'];
      $firstname = $_POST['USER_FIRST_NAME'];
      $studentid = $_POST['STUDENTID'];
      $teachername = $_POST['TEACHER_NAME'];
      $period = $_POST['PERIOD'];
            $tp = $_POST['tp'];
            $psp = $_POST['psp'];
            $sp = $_POST['sp'];
            $qt = $_POST['qt'];
            $detailed_results_xml = $_POST["dr"];
            $dateTime = date('Y-m-d_H-i-s');

         //WRITE COMPLETE RESULTS TO txt FILE
            $lastname = mb_convert_encoding($lastname, "ISO-8859-1", "auto");
            $resultFilename = dirname(__FILE__) . "/result/" . $qt . "_" . $teachername . "_" . $period . "_" . $lastname . ".txt";
            @file_put_contents($resultFilename, $report);
            

         //WRITE COMPLETE RESULTS TO xml FILE
            $resultFilename = dirname(__FILE__) . "/result/" . $qt . "_". $teachername . "_" . $period . "_" . $lastname . ".xml";
            @file_put_contents($resultFilename, $detailed_results_xml);     


         //IF EVERYTHING WENT FINE, THE FLASH QUIZ WILL NEED TO RECEIVE 'OK'. OTHERWISE IT WILL SAY "CANNOT SEND RESULTS TO SERVER"...
            if($errors==0) {
               echo "OK";
               _log("Written answer.");
            } else {
               $errors--;
               _errorlog("There were errors. Answer not written");
            }
   
      } catch (Exception $e){ //WRITE ERROR IN A LOG FILE
         error_log($e);
         echo "Error: " . $e->getMessage();
         _errorlog("Erro no try: " . $e->getMessage());
      }

   _log("Script terminado com " . $errors . " erro(s)");

   function _errorlog($message){
      global $errors;
      date_default_timezone_set("Europe/Lisbon");
      $logFilename = dirname(__FILE__) . '/log_quiz.log';
      //$str=str_replace("\r\n","",$str);
      $logMessage    = date('Y-m-d H:i:s') . " - * " . $message . PHP_EOL;
      @file_put_contents($logFilename, $logMessage, FILE_APPEND);
      $errors++;
   }


   function _log($message){
      date_default_timezone_set("Europe/Lisbon");
      $logFilename = dirname(__FILE__) . '/log_quiz.log';
      //$str=str_replace("\r\n","",$str);
      $logMessage    = date('Y-m-d H:i:s') . " - " . $message . PHP_EOL;
      @file_put_contents($logFilename, $logMessage, FILE_APPEND);
   }

?> 