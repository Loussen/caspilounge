<?php
/**
 * Created by PhpStorm.
 * User: fuad
 * Date: 12/3/16
 * Time: 10:29 AM
 */
?>
<?php
include "caspimanager/pages/includes/config.php";
include "includes/functions.php";

//sec_session_start();

$response = json_encode(array("code"=>0, "content" => "Error system", "err_param" => ''));

if($_POST)
{
    if($_POST['subscribe_form'])
    {
        if(isset($_POST['msisdn']) && !empty($_POST['msisdn']))
        {
            $prefix = '251';
            $msisdn = safe($_POST['msisdn']);

            if(!preg_match('/^[1-9]\d{6}$/', substr($msisdn,2)))
            {
                $response = json_encode(array("code"=>0, "content" => "Your number is not valid. Please try again.", "err_param" => 'msisdn'));
                echo $response;
                exit;
            }

            $msisdn = $prefix.$msisdn;

            $stmt_select = mysqli_prepare($db,"SELECT `status` FROM `subscriber` WHERE `msisdn`=(?) LIMIT 1");
            $stmt_select->bind_param('s', $msisdn);
            $stmt_select->execute();
            $stmt_select->bind_result($result_status);
            $stmt_select->store_result();
            $stmt_select->fetch();

            if($stmt_select->num_rows==1)
            {
                if($result_status==1)
                {
                    $sms_code_login = mt_rand(100000, 999999);

                    $stmt_update = mysqli_prepare($db, "UPDATE `subscriber` SET `sms_code_login`=? WHERE `msisdn`=?");
                    $stmt_update->bind_param('ss', $sms_code_login,$msisdn);
                    $update = $stmt_update->execute();

                    if($update==1)
                    {
                        // Send sms for login (activation code not charging)
                        // INSERT sms_out_queue
                        $now_datetime = date('Y-m-d H:i:s');
                        $from_number = 4143; // number for login
                        $req_id = -1;
                        $mtype = 'SmsTest';
                        $dcs = $udhi = 0;

                        $select_seq_id = mysqli_fetch_assoc(mysqli_query($db,"SELECT seq('general') as `seq_id`"));

                        $g_id = $select_seq_id['seq_id'];

                        $stmt_insert = mysqli_prepare($db, "INSERT INTO `sms_out_queue` (`id`,`request_id`,`mtype`,`dt`,`fromnumber`,`tonumber`,`smstext`,`dcs`,`udhi`) VALUES (?,?,?,?,?,?,?,?,?)");
                        $stmt_insert->bind_param('iisssssii', $g_id,$req_id,$mtype,$now_datetime,$from_number,$msisdn,$sms_code_login,$dcs,$udhi);
                        $insert = $stmt_insert->execute();

                        if($insert==1)
                        {
                            $_SESSION['msisdn_step2'] = $msisdn;

                            $response = json_encode(array("code"=>2, "content" => "You are already subscriber. Please log in", "err_param" => ''));
                        }
                        else
                        {
                            $response = json_encode(array("code"=>0, "content" => "Insert sms error", "err_param" => ''));
                        }
                    }
                    else
                    {
                        $response = json_encode(array("code"=>0, "content" => "Update sms code error", "err_param" => ''));
                    }
                }
                elseif($status=2)
                {
                    $sms_id = $null_code = 0;
                    $created_at = date("Y-m-d H:i:s");
                    $null_date = '0000-00-00 00:00:00';
                    $null_param = '';
                    $status = 2; // Waiting...

                    $sms_code = mt_rand(100000, 999999);

                    // Send sms for subscribe (activation code with charging)
                    $now_datetime = date('Y-m-d H:i:s');
                    $from_number = 4143; // number for login
                    $req_id = -1;
                    $mtype = 'SmsTest';
                    $dcs = $udhi = 0;
                    $amount = 1; // Charge balance
                    $channel = 2;

                    $select_seq_id = mysqli_fetch_assoc(mysqli_query($db,"SELECT seq('general') as `seq_id`"));

                    $g_id = $select_seq_id['seq_id'];

                    // INSERT sms_out_queue
                    $stmt_insert = mysqli_prepare($db, "INSERT INTO `sms_out_queue` (`id`,`request_id`,`mtype`,`dt`,`fromnumber`,`tonumber`,`smstext`,`dcs`,`udhi`) VALUES (?,?,?,?,?,?,?,?,?)");
                    $stmt_insert->bind_param('iisssssii', $g_id,$req_id,$mtype,$now_datetime,$from_number,$msisdn,$sms_code,$dcs,$udhi);
                    $insert_sms_out_queue = $stmt_insert->execute();

                    // INSERT charging_queue
                    $stmt_insert = mysqli_prepare($db, "INSERT INTO `charging_queue` (`g_id`,`dt`,`msisdn`,`amount`,`channel`) VALUES (?,?,?,?,?)");
                    $stmt_insert->bind_param('issii', $g_id,$now_datetime,$msisdn,$amount,$channel);
                    $insert_charging_queue = $stmt_insert->execute();

                    if($insert_sms_out_queue==1 && $insert_charging_queue==1)
                    {
                        $stmt_update = mysqli_prepare($db, "UPDATE `subscriber` SET `sms_code`=? WHERE `msisdn`=?");
                        $stmt_update->bind_param('ss', $sms_code,$msisdn);
                        $update = $stmt_update->execute();

                        if($update==1)
                        {
                            $_SESSION['msisdn_step1'] = $msisdn;

                            $response = json_encode(array("code"=>1, "content" => "Success update", "err_param" => ''));
                            $stmt_insert->close();
                        }
                        else
                        {
                            $response = json_encode(array("code"=>0, "content" => "Insert error", "err_param" => ''));
                        }
                    }
                }
                else
                {
                    $response = json_encode(array("code"=>0, "content" => "Error", "err_param" => ''));
                }
                $stmt_select->close();
            }
            else
            {
                $sms_id = $null_code = 0;
                $created_at = date("Y-m-d H:i:s");
                $null_date = '0000-00-00 00:00:00';
                $null_param = '';
                $status = 0;

                $sms_code = mt_rand(100000, 999999);

                // Send sms for subscribe (activation code with charging)
                $now_datetime = date('Y-m-d H:i:s');
                $from_number = 4143; // number for login
                $req_id = -1;
                $mtype = 'SmsTest';
                $dcs = $udhi = 0;
                $amount = 1; // Charge balance
                $channel = 2;

                $select_seq_id = mysqli_fetch_assoc(mysqli_query($db,"SELECT seq('general') as `seq_id`"));

                $g_id = $select_seq_id['seq_id'];

                // INSERT sms_out_queue
                $stmt_insert = mysqli_prepare($db, "INSERT INTO `sms_out_queue` (`id`,`request_id`,`mtype`,`dt`,`fromnumber`,`tonumber`,`smstext`,`dcs`,`udhi`) VALUES (?,?,?,?,?,?,?,?,?)");
                $stmt_insert->bind_param('iisssssii', $g_id,$req_id,$mtype,$now_datetime,$from_number,$msisdn,$sms_code,$dcs,$udhi);
                $insert_sms_out_queue = $stmt_insert->execute();

                // INSERT charging_queue
                $stmt_insert = mysqli_prepare($db, "INSERT INTO `charging_queue` (`g_id`,`dt`,`msisdn`,`amount`,`channel`) VALUES (?,?,?,?,?)");
                $stmt_insert->bind_param('issii', $g_id,$now_datetime,$msisdn,$amount,$channel);
                $insert_charging_queue = $stmt_insert->execute();

                if($insert_sms_out_queue==1 && $insert_charging_queue==1)
                {
                    $stmt_insert = mysqli_prepare($db, "INSERT INTO `subscriber` (`msisdn`,`created_at`,`updated_at`,`next_date`,`ends_date`,`sms_code`,`sms_code_login`,`last_login_date`,`last_login_ip`,`sms_id`,`g_id`,`status`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
                    $stmt_insert->bind_param('sssssiissiii', $msisdn,$created_at,$null_date,$null_date,$null_date,$sms_code,$null_code,$null_date,$null_param,$sms_id,$g_id,$status);
                    $insert = $stmt_insert->execute();

                    if($insert==1)
                    {
                        $_SESSION['msisdn_step1'] = $msisdn;

                        $response = json_encode(array("code"=>1, "content" => "Success insert", "err_param" => ''));
                        $stmt_insert->close();
                    }
                    else
                    {
                        $response = json_encode(array("code"=>0, "content" => "Insert error", "err_param" => ''));
                    }
                }
                else
                {
                    $response = json_encode(array("code"=>0, "content" => "Insert error sms or charging", "err_param" => ''));
                }
            }
        }
        else
        {
            // The correct POST variables were not sent to this page.
            $response = json_encode(array("code"=>0, "content" => "Your number is not valid. Please try again.", "err_param" => 'msisdn'));
        }
    }
    elseif($_POST['check_sms_form_for_subscribe'] && isset($_SESSION['msisdn_step1']) && !empty($_SESSION['msisdn_step1']))
    {
        $sms_code = intval($_POST['sms_code']);

        if(login($sms_code, $_SESSION['msisdn_step1'], 'subscribe', $db) == 'success')
        {
            $status = 2; // Waiting
            $session_msisdn = $_SESSION['msisdn_step1'];
            $last_login_date = date("Y-m-d H:i:s");
            $last_login_ip = $_SERVER['REMOTE_ADDR'];

            $stmt_update = mysqli_prepare($db, "UPDATE `subscriber` SET `status`=?, `last_login_date`=?, `last_login_ip`=? WHERE `msisdn`=?");
            $stmt_update->bind_param('isss', $status,$last_login_date,$last_login_ip,$session_msisdn);
            $update = $stmt_update->execute();

            if($update==1)
            {
                $response = json_encode(array("code"=>1, "content" => "Success update", "err_param" => ''));
            }
            else
            {
                $response = json_encode(array("code"=>0, "content" => "Update error", "err_param" => ''));
            }

            $stmt_update->close();
            unset($_SESSION['msisdn_step1']);
        }
        elseif(login($sms_code, $_SESSION['msisdn_step1'],'subscribe', $db) == 'sms_code_empty')
        {
            $response = json_encode(array("code"=>0, "content" => "Your PIN code is not correct. Please check and try again.", "err_param" => 'sms_code'));
        }
        elseif(login($sms_code, $_SESSION['msisdn_step1'],'subscribe', $db) == 'user_not_exists')
        {
            $response = json_encode(array("code"=>0, "content" => "User is not exists!", "err_param" => ''));
        }
        elseif(login($sms_code, $_SESSION['msisdn_step1'],'subscribe', $db) == 'sms_code_incorrect')
        {
            $response = json_encode(array("code"=>0, "content" => "Your PIN code is not correct. Please check and try again.", "err_param" => 'sms_code'));
        }
        elseif(login($sms_code, $_SESSION['msisdn_step1'],'subscribe', $db) == 'account_locked')
        {
            $response = json_encode(array("code"=>0, "content" => "Your account is locked!", "err_param" => ''));
        }
        else
        {
            $response = json_encode(array("code"=>0, "content" => "subscriber not found!", "err_param" => ''));
        }
    }
//    elseif($_POST['login_form'])
//    {
//        if(isset($_POST['msisdn']) && !empty($_POST['msisdn']))
//        {
//            $prefix = '251';
//            $msisdn = safe($_POST['msisdn']);
//
//            if(!preg_match('/^[2-9]\d{6}$/', substr($msisdn,2)))
//            {
//                $response = json_encode(array("code"=>0, "content" => "Msisdn is invalid", "err_param" => 'msisdn'));
//                echo $response;
//                exit;
//            }
//
//            $msisdn = $prefix.$msisdn;
//
//            $stmt_select = mysqli_prepare($db,"SELECT `status` FROM `subscriber` WHERE `msisdn`=(?) LIMIT 1");
//            $stmt_select->bind_param('s', $msisdn);
//            $stmt_select->execute();
//            $stmt_select->bind_result($result_status);
//            $stmt_select->store_result();
//            $stmt_select->fetch();
//
//            if($stmt_select->num_rows==1)
//            {
//                if($result_status==1)
//                {
//                    $_SESSION['msisdn_step2'] = $msisdn;
//
//                    $response = json_encode(array("code"=>1, "content" => "Success", "err_param" => ''));
//                }
//                else
//                {
//                    $response = json_encode(array("code"=>0, "content" => "Error balance", "err_param" => ''));
//                }
//            }
//            else
//            {
//                $response = json_encode(array("code"=>0, "content" => "User not exists", "err_param" => ''));
//            }
//        }
//    }
    elseif($_POST['check_sms_form_for_login'] && isset($_SESSION['msisdn_step2']) && !empty($_SESSION['msisdn_step2']))
    {
        $sms_code = intval($_POST['sms_code']);

        if(login($sms_code, $_SESSION['msisdn_step2'],'login', $db) == 'success')
        {
            $session_msisdn = $_SESSION['msisdn_step2'];

            $last_login_date = date("Y-m-d H:i:s");
            $last_login_ip = $_SERVER['REMOTE_ADDR'];

            $stmt_update = mysqli_prepare($db, "UPDATE `subscriber` SET `last_login_date`=?,`last_login_ip`=? WHERE `msisdn`=?");
            $stmt_update->bind_param('sss', $last_login_date,$last_login_ip,$session_msisdn);
            $update = $stmt_update->execute();

            if($update==1)
            {
                $response = json_encode(array("code"=>1, "content" => "Success update", "err_param" => ''));
            }
            else
            {
                $response = json_encode(array("code"=>0, "content" => "Update error", "err_param" => ''));
            }

            $stmt_update->close();
            unset($_SESSION['msisdn_step2']);
        }
        elseif(login($sms_code, $_SESSION['msisdn_step2'],'login', $db) == 'sms_code_empty')
        {
            $response = json_encode(array("code"=>0, "content" => "Your PIN code is not correct. Please check and try again.", "err_param" => 'sms_code'));
        }
        elseif(login($sms_code, $_SESSION['msisdn_step2'],'login', $db) == 'user_not_exists')
        {
            $response = json_encode(array("code"=>0, "content" => "User is not exists!", "err_param" => ''));
        }
        elseif(login($sms_code, $_SESSION['msisdn_step2'],'login', $db) == 'sms_code_incorrect')
        {
            $response = json_encode(array("code"=>0, "content" => "Your PIN code is not correct. Please check and try again.", "err_param" => 'sms_code'));
        }
        elseif(login($sms_code, $_SESSION['msisdn_step2'],'login', $db) == 'account_locked')
        {
            $response = json_encode(array("code"=>0, "content" => "Your account is locked!", "err_param" => ''));
        }
        else
        {
            $response = json_encode(array("code"=>0, "content" => "subscriber not found!", "err_param" => ''));
        }
    }
    elseif(isset($_POST['type']) && !empty($_POST['type']) && $_POST['type']=='most_played')
    {
        $game_id = intval($_POST['game_id']);
        $user_id = intval($_POST['user_id']);

        $datetime = date("Y-m-d H:i:s");

        $stmt_select = mysqli_prepare($db,"SELECT `id` FROM `play_game` WHERE `users_id`=(?) and `games_id`=(?)");
        $stmt_select->bind_param('ii', $user_id,$game_id);
        $stmt_select->execute();
        $stmt_select->store_result();

        if($stmt_select->num_rows>0)
        {
            $stmt_update = mysqli_prepare($db, "UPDATE `play_game` SET `count`=`count`+1,`datetime`=? WHERE `users_id`=? and `games_id`=?");
            $stmt_update->bind_param('sii', $datetime,$user_id,$game_id);
            $update = $stmt_update->execute();

            if($update==1)
            {
                $response = json_encode(array("code"=>1, "content" => "Success update", "err_param" => ''));
                $stmt_update->close();
            }
            else
            {
                $response = json_encode(array("code"=>0, "content" => "Update error", "err_param" => ''));
            }
        }
        else
        {
            $count = 1;
            $stmt_insert = mysqli_prepare($db, "INSERT INTO `play_game` (`users_id`,`games_id`,`count`,`datetime`) VALUES (?,?,?,?)");
            $stmt_insert->bind_param('iiis', $user_id,$game_id,$count,$datetime);
            $insert = $stmt_insert->execute();

            if($insert==1)
            {
                $response = json_encode(array("code"=>1, "content" => "Success insert", "err_param" => ''));
                $stmt_insert->close();
            }
            else
            {
                $response = json_encode(array("code"=>0, "content" => "Insert error", "err_param" => ''));
            }
        }
    }
}

echo $response;
?>
