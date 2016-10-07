<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Wordcloud extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('crud_model', 'excel');
    }

    public function index() {
        $this->load->view('upload');
    }

    public function wordCloud($id) {
        $list = $this->excel->getdata($id);
        $count = count($list);
        $word = "";
        for ($i = 0; $i < $count; $i++) {
            $valEx = explode(" ", $list[$i]->val);
            for ($j = 0; $j < count($valEx); $j++) {
                $word .= round(strlen($valEx[$j]) * 1.2) . ' ' . $valEx[$j] . '\n';
            }
        }
        $data['word'] = $word;
        $this->load->view('view_wordcloud', $data);
    }

    public function postExcel($id) {
        try {
            $list = $this->excel->get_data_excel($id)[0];
            $file = $list->name_file;
//            echo $file;die;
            require_once APPPATH . 'third_party/PHPExcel.php';
            $tmpfname = BASEPATH . "../assets/" . $file;
            if (file_exists($tmpfname)) {

                ini_set('memory_limit', '2048M');
                set_time_limit('1200');
                $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
                $cacheSettings = array(' memoryCacheSize ' => '256MB');
                PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

                $excelReader = PHPExcel_IOFactory::createReaderForFile($tmpfname);
                $excelReader->setReadDataOnly(true);
                $excelObj = $excelReader->load($tmpfname);
                $worksheet = $excelObj->getSheet(0);
                $lastRow = $worksheet->getHighestRow();
                $word = "";

                $alphas = range('A', 'Z');
                for ($r = 1; $r < 5; $r++) {
                    for ($c = 0; $c < 10; $c++) {
                        if (strtolower($worksheet->getCell($alphas[$c] . $r)->getValue()) == 'content') {
                            for ($row = $r; $row <= $lastRow; $row++) {
                                if ($worksheet->getCell($alphas[$c] . $row)->getValue() != NULL) {
                                    $valEx = explode(" ", $worksheet->getCell($alphas[$c] . $row)->getValue());
                                    for ($j = 0; $j < count($valEx); $j++) {
                                        $val_split = explode("\n", $valEx[$j]);
                                        $split = "";
                                        for ($sp = 0; $sp < count($val_split); $sp++) {
                                            $split .= $val_split[$sp];
                                        }
                                        $word .= round(strlen($valEx[$j]) * 1.2) . ' ' . $split . '\n';
                                    }
                                }
                            }
                        }
                    }
                }
                if ($word == NULL) {
                    echo 'Content in Excel not found';
                } else {
                    if (strlen($word) > 3200) {
                        $word = substr($word, 0, 3200);
                    }
                    $data['word'] = $word;
                    $this->load->view('view_wordcloud', $data);
                }
            } else {
                echo "File not found";
            }
        } catch (Exception $exc) {
            echo "Error .!";
        }
    }

    public function saveExcel() {
        $file = $_POST['fileName'];
        $id = $_POST['id'];
        try {
            require_once APPPATH . 'third_party/PHPExcel.php';
            $tmpfname = BASEPATH . "../assets/" . $file;
            if (file_exists($tmpfname)) {
//                ini_set('max_execution_time', 999900000000000000000000);
//                ini_set('memory_limit', '248MB');
//                    $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
//                    $cacheSettings = array('memoryCacheSize' => '256MB');
//                    PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
//                    
                ini_set('memory_limit', '2048M');
                set_time_limit('1200');
                $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
                $cacheSettings = array(' memoryCacheSize ' => '256MB');
                PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
//                ini_set('max_execution_time', 92345678999999900000000);
//                
//                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
//                    $objPHPExcel = $objReader->load("test.xlsx");


                $excelReader = PHPExcel_IOFactory::createReaderForFile($tmpfname);
                $excelReader->setReadDataOnly(true);
                $excelObj = $excelReader->load($tmpfname);
                $worksheet = $excelObj->getSheet(0);
                $lastRow = $worksheet->getHighestRow();

//                $message = "";
                $alphas = range('A', 'Z');
                for ($r = 1; $r < 5; $r++) {
                    for ($c = 0; $c < 10; $c++) {
                        if ($worksheet->getCell($alphas[$c] . $r)->getValue() == 'content') {
//                        $message = $alphas[$c] . '==' . $r;

                            for ($row = $r; $row <= $lastRow; $row++) {
                                if ($worksheet->getCell($alphas[$c] . $r)->getValue() != NULL) {
                                    $this->excel->save_val_upload($id, $worksheet->getCell($alphas[$c] . $row)->getValue());
//                                $message .= $worksheet->getCell($alphas[$c] . $row)->getValue();
                                }
                            }
                        }
                    }
                }
//            $status = array('status' => 0, 'message' => $message);
                $status = array('status' => 1, 'message' => 'success');
            } else {
                $status = array('status' => 0, 'message' => 'File not found');
            }
        } catch (Exception $exc) {
            $status = array('status' => 0, 'message' => 'File not found');
        }
        echo json_encode($status);
    }

    public function getupload() {
        $nameFile = "";
        $id = "";
        $status = 0;
        $message = "";
        try {
            if (isset($_FILES['file']['tmp_name'])) {
                if ($_FILES["file"]["size"] > 0 && $_FILES["file"]["size"] < 2097152) {
                    $last_name_file = (new \DateTime())->format('Ymd');
                    $allowed = array('xlsx');
                    $filename = $last_name_file . '_' . $_FILES['file']['name'];
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    if (!in_array($ext, $allowed)) {
                        $message = 'file type does not match.';
                        $status = 0;
                    } else {
                        if (0 < $_FILES['file']['error']) {
                            $message = 'Error: ' . $_FILES['file']['error'];
                        } else {
                            move_uploaded_file($_FILES['file']['tmp_name'], BASEPATH . '../assets/' . $filename);
                            $idVal = $this->excel->insert_upload($filename);
                            $id = $idVal;
                            $nameFile = $filename;
                            $message = 'Success';
                            $status = 1;
                        }
                    }
                } else {
                    $message = 'Max Size file 2MB';
                }
            } else {
                $message = 'File not found';
            }
        } catch (Exception $exc) {
            $message = 'Error :' . $exc;
            $status = 0;
        }
        echo json_encode(array('status' => $status, 'message' => $message, 'file' => $nameFile, 'id' => $id));
    }

}
