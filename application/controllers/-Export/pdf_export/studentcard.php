<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// new
error_reporting(E_ALL);
ini_set("display_errors", 1);

$config = [
    'mode' => '-aCJK', 
    "autoScriptToLang" => true,
    "autoLangToFont" => true,
];

$mpdf = new \Mpdf\Mpdf($config);

$data = ob_start();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"> 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300&display=swap" rel="stylesheet">
    <!-- Include Bootstrap CSS -->
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> -->
    <!-- <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> -->
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->
    <style type="text/css">
        body {
            font-family: "Arial", sans-serif;
        }
    </style>
</head>
<body>
    <header>
        <table style="width: 100%;">
            <?php foreach ($student as $key => $value) { ?>
                <?php if ( fmod($key , 3) == 0 || $key === 0 ) { ?>
                    <tr>
                <?php } ?>
                        <td>
                            <table style="width: 250px;height:220px;border: solid;border-width: 2px;" >
                                <tr style="border: none;border-width: 0px;background-color: red;">
                                    <td colspan="3">
                                        <span style="font-size: 8px;">Student Card</span> <span style="font-size: 5px;">[<?= isset($branch[0]['title']) ? $branch[0]['title']: " "; ?>]</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%;vertical-align: text-top;padding: 3px;">
                                        <img src="<?php echo empty($value['branchimage']) ? 'https://cdn.synorex.link/assets/images/robocube/tuition.png' : $value['branchimage']; ?>" style="height: 35px; width: 35px;">
                                        <br/>
                                        <!-- QR Code -->
                                        <?php 
                                            $submit_attendance = urlencode("https://system.synorex.work/highpeakedu/webapp_teacher/submit_attendance?id=".$value['pid']."&method=qr"); 
                                        ?>
                                        <img src="https://chart.googleapis.com/chart?chs=177x177&cht=qr&chl=<?= $submit_attendance; ?>&chld=L|0&choe=UTF-8" style="height: 45px; width: 45px;">
                                        <br/>
                                    </td>
                                    <td style="width: 40%;vertical-align: text-top;padding: 3px;font-size: 8px;">
                                        <?php  
                                            $fatherno = "";
                                            $motherno = "";
                                            if (isset($value['father'][0])) {
                                               $fatherno = $value['father'][0]['phone']; 
                                            }
                                            if (isset($value['mother'][0])) {
                                                $motherno = $value['mother'][0]['phone']; 
                                            }
                                        ?>
                                        <span>
                                            <?= $value['fullname_en'] !== "" ? $value['fullname_en'] : " "; ?>
                                        </span>
                                        <br/>
                                        <span>
                                            <?= $value['fullname_cn'] !== "" ? $value['fullname_cn'] : " "; ?>   
                                        </span>
                                        <br/>
                                        <span>
                                            <?= $value['code'] !== "" ? $value['code'] : " "; ?>
                                        </span>
                                        <br/>
                                        <span>
                                            <?= $value['form'] !== "" ? $value['form'] : " "; ?>
                                        </span>
                                        <br/>
                                        <span>
                                            S:<?php echo $value['pid']; ?>
                                        </span>
                                        <br/>
                                        <span>
                                            F:<?php echo $fatherno; ?>
                                        </span>
                                        <br/>
                                        <span>
                                            M:<?php echo $motherno; ?>
                                        </span>
                                        <br/>
                                        <br/>
                                        <br/>
                                    </td>
                                    <td style="width: 30%;padding: 3px;">
                                        <img src="<?php echo pointoapi_UploadSource($value['image']); ?>"
                                            style="height: 65px; width: 55px; object-fit: cover">
                                    </td>
                                </tr>
                            </table>
                        </td>
                <?php if ( fmod($key + 1, 3) === 0 || $key === 0 ) { ?>
                    </tr>
                <?php } ?>
            <?php } ?>
        </table>
    </header>
</body>
</html>

<?php

$data = ob_get_clean();

$mpdf->SetTitle('StudentCard.pdf');
$mpdf->WriteHTML($data);
$mpdf->Output('StudentCard.pdf', 'I');