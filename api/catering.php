<?php
/**
 * Created by PhpStorm.
 * User: fhasanli
 * Date: 4/23/2019
 * Time: 9:44 AM
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/src/Exception.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

if (isset($_SERVER['HTTP_ORIGIN']))
{
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
{
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    exit(0);
}

include "../caspimanager/pages/includes/config.php";

$response = json_encode(array("status"=>false, "type"=>"catering", "err" => "Error system"));

$phpInput = file_get_contents("php://input");

if($phpInput)
{
    $data = json_decode($phpInput, true);

    if(!strlen($data['name'])>0)
    {
        $response = json_encode(array("status"=>false, "err_param"=>"name", "err" => "Name is required"));
    }
    elseif(!strlen($data['surname'])>0)
    {
        $response = json_encode(array("status"=>false, "err_param"=>"surname", "err" => "Surname is required"));
    }
    elseif(!strlen($data['month'])>0)
    {
        $response = json_encode(array("status"=>false, "err_param"=>"month", "err" => "Month is required"));
    }
    elseif(!strlen($data['day'])>0)
    {
        $response = json_encode(array("status"=>false, "err_param"=>"day", "err" => "Day is required"));
    }
    elseif(!strlen($data['year'])>0)
    {
        $response = json_encode(array("status"=>false, "err_param"=>"year", "err" => "Year is required"));
    }
    elseif(!strlen($data['email'])>0)
    {
        $response = json_encode(array("status"=>false, "err_param"=>"email", "err" => "Email is required"));
    }
    elseif(!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
    {
        $response = json_encode(array("status"=>false, "err_param"=>"email", "err" => "Invalid email"));
    }
    elseif(!strlen($data['subject'])>0)
    {
        $response = json_encode(array("status"=>false, "err_param"=>"subject", "err" => "Subject is required"));
    }
    elseif(strlen($data['message'])<5)
    {
        $response = json_encode(array("status"=>false, "err_param"=>"message", "err" => "Message must be minimum 5 characters"));
    }
    else
    {
        // Send email

        $mail = new PHPMailer(true);

        try
        {
            //Server settings
            $mail->SMTPDebug = 0;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.yandex.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'fuad.hasanli@yandex.com';                 // SMTP username
            $mail->Password = '159357fh!)(';                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom('fuad.hasanli@yandex.com', 'Caspi Lounge catering form');
            $mail->addAddress('fhesenli92@gmail.com', 'Caspi Lounge catering form');     // Add a recipient
            $mail->addReplyTo($data['email'], $data['name']." ".$data['surname']);

            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $data['subject'];
            $mail->Body    = '<table style="font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;">
                                  <tr>
                                    <th style="border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;">Name</th>
                                    <th style="border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;">Surname</th>
                                    <th style="border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;">Date</th>
                                    <th style="border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;">Email</th>
                                    <th style="border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;">Subject</th>
                                    <th style="border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;">Message</th>
                                  </tr>
                                  <tr>
                                    <td style="border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;">'.safe($data['name']).'</td>
                                    <td style="border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;">'.safe($data['surname']).'</td>
                                    <td style="border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;">'.safe($data['month']).'/'.safe($data['day']).'/'.safe($data['year']).'</td>
                                    <td style="border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;">'.safe($data['email']).'</td>
                                    <td style="border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;">'.safe($data['subject']).'</td>
                                    <td style="border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;">'.safe($data['message']).'</td>
                                  </tr>
                                </table>';

            $mail->send();

            $response = json_encode(array("status"=>true, "type"=>"catering", "message" => "Success"));
        }
        catch (Exception $e)
        {
            $response = json_encode(array("status"=>false, "type"=>"catering", "err" => "Message could not be sent. Mailer Error: ", $mail->ErrorInfo));
        }


    }
}
else
{
    $response = json_encode(array("status"=>false, "type"=>"catering", "err" => "Not data"));
}

echo $response;



