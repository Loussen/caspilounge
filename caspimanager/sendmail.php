<?php
/**
 * Created by PhpStorm.
 * User: fhasanli
 * Date: 5/13/2019
 * Time: 11:30 AM
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$message = 0;

$message_text = '';

if((isset($_GET['user_id']) && !empty($_GET['user_id'])) || (isset($_POST['user_id'])) && !empty($_POST['user_id']))
{
    require '../phpmailer/src/PHPMailer.php';
    require '../phpmailer/src/SMTP.php';
    require '../phpmailer/src/Exception.php';

    include "pages/includes/config.php";

    $user_id = intval($_REQUEST['user_id']);

    $row_users = mysqli_fetch_assoc(mysqli_query($db,"select `firstname`,`lastname`,`email` from `customers` where id='$user_id'"));

    if(strlen($row_users['email'])>2)
    {
        $get_order = mysqli_fetch_assoc(mysqli_query($db,"select `id` from `orders` where customer_id='$user_id'"));

        if($get_order['id']>0)
        {
            $active_status = 1;

            $main_lang = 4;

            $stmt_select = mysqli_prepare($db,"SELECT 
                                                `cart`.`id` as `cart_id`,
                                                `foods`.`auto_id` as `food_id`,
                                                `cart`.`quantity` as `quantity`,
                                                `cart`.`special_req` as `special_req`,
                                                `cart`.`total` as `total`,
                                                `foods`.`title` as `title`,
                                                `foods`.`text` as `text`,
                                                `foods`.`price` as `price`
                                                FROM `cart`
                                                INNER JOIN `foods` on `foods`.`auto_id`=`cart`.`food_id`
                                                 WHERE `cart`.`order_id`=(?) and `foods`.`active`=(?) and `foods`.`lang_id`=(?)
                                                 ORDER BY `cart`.`id` DESC 
                                                 ");
            $stmt_select->bind_param('iii', $get_order['id'],$active_status,$main_lang);
            $stmt_select->execute();
            $result = $stmt_select->get_result();
            $stmt_select->close();

            if($result->num_rows>0)
            {
                $order_number_show = '';
                for($i=1;$i<=(8-strlen($get_order['id']));$i++)
                {
                    $order_number_show .= "0";
                }

                $message_text .= '
<style>

    table tr:nth-child(even) {
        background-color: #dddddd;
    }
</style>
<div style="border: 1px solid #ddd; padding: 5px;"><h2 align="center">#'.$order_number_show.$get_order['id'].' Orders (count: '.$result->num_rows.')</h2>
                    <table style="font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;">
                        <tr>
                            <th style="border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
        text-align: center;">Quantity</th>
                            <th style="border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
        text-align: center;">Special request</th>
                            <th style="border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
        text-align: center;">Title</th>
                            <th style="border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
        text-align: center;">Price</th>
                            <th style="border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
        text-align: center;">Total</th>
                        </tr>';

                $total = 0;
                while($row=$result->fetch_assoc())
                {
                    $message_text .= '<tr>
                            <td style="border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
        text-align: center;">'.$row['quantity'].'</td>
                            <td style="border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
        text-align: center;">'.$row['special_req'].'</td>
                            <td style="border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
        text-align: center;">'.$row['title'].'</td>
                            <td style="border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
        text-align: center;">'.$row['price'].' USD</td>
                            <td style="border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
        text-align: center;">'.$row['total'].' USD</td>
                        </tr>';

                    $total+=$row['total'];
                }

                $message_text .= '<tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
        text-align: center;background-color: green; color: #fff;">'.$total.' USD</td>
                        </tr></table></div><br />';
            }
            else
            {
                echo "Not found order";
                exit;
            }

            if(isset($_POST['message_text']) && !empty($_POST['message_text']))
            {
                $message_text .= "Info : <b><i>".safe($_POST['message_text'])."</i></b>";

                // Send email

                $mail = new PHPMailer(true);

                try
                {
                    //Server settings
                    $mail->SMTPDebug = 0;                                 // Enable verbose debug output
                    $mail->isSMTP();                                      // Set mailer to use SMTP
                    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
                    $mail->SMTPAuth = true;                               // Enable SMTP authentication
                    $mail->Username = 'info@caspilounge.com';                 // SMTP username
                    $mail->Password = '401501caspi123';                           // SMTP password
                    $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
                    $mail->Port = 465;                                    // TCP port to connect to

                    //Recipients
                    $mail->setFrom('info@caspilounge.com', 'Caspi Lounge order');
                    $mail->addAddress($row_users['email'], 'Caspi Lounge order');     // Add a recipient
                    $mail->addReplyTo('info@caspilounge.com', 'Caspi Lounge order');

                    //Content
                    $mail->isHTML(true);                                  // Set email format to HTML
                    $mail->Subject = 'Caspi Lounge order (#'.$order_number_show.$get_order['id'].')';
                    $mail->Body = $message_text;

                    $mail->send();

                    $message = 1;
                }
                catch (Exception $e)
                {
                    $message = 2;
                }
            }
        }
    }
    else
    {
        $message = 2;
    }
}
else
{
    echo "Not found user";
    exit;
}

?>

<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<?php
    if($message==1) echo '<div class="alert_success"><p><img src="images/icon_accept.png" alt="success" class="mid_align"/>Success</p></div>';
    if($message==2) echo '<div class="alert_error"><p><img src="images/icon_error.png" alt="delete" class="mid_align"/>Error</p></div>';
?>
<form action="" method="post" style="padding: 15px;">
    <h2>Send Mail for order : <?=$row_users['email']?> (#<?=$order_number_show.$get_order['id']?>)</h2>
    <select name="message_type" class="form-control" onchange="messageType(event)">
        <option value="1">Special message</option>
        <?php
            for ($i=10;$i<=100;$i+=5)
            {
                ?>
                <option value="<?=$i?>"><?=$i?></option>
                <?php
            }
        ?>
    </select><br />
    <textarea name="message_text" id="message_text" class="form-control"></textarea>
    <input type="hidden" name="user_id" value="<?=$_GET['user_id']?>">
    <br />
    <input type="submit" name="submit" value="SEND">
</form>

<script type="text/javascript">
    function messageType(e)
    {
        if(e.target.value>1)
        {
            document.getElementById("message_text").value = "Your order will reach within "+e.target.value+" minutes";
        }
        else
        {
            document.getElementById("message_text").value = "";
        }
    }
</script>