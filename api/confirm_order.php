<?php
/**
 * Created by PhpStorm.
 * User: fhasanli
 * Date: 4/23/2019
 * Time: 9:44 AM
 */

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

$response = json_encode(array("status"=>false, "type"=>"confirm_order", "err" => "Error system"));

$phpInput = file_get_contents("php://input");

if($phpInput)
{
    if(isset($_SERVER['HTTP_SECRET']) && !empty($_SERVER['HTTP_SECRET']))
    {
        $data = json_decode($phpInput, true);

        if(!strlen($data['city'])>0)
        {
            $response = json_encode(array("status"=>false, "err_param"=>"city", "err" => "City is required"));
        }
        elseif(!strlen($data['no'])>0)
        {
            $response = json_encode(array("status"=>false, "err_param"=>"no", "err" => "No is required"));
        }
        elseif(!strlen($data['street'])>0)
        {
            $response = json_encode(array("status"=>false, "err_param"=>"street", "err" => "Street is required"));
        }
        elseif(!strlen($data['firstname'])>0)
        {
            $response = json_encode(array("status"=>false, "err_param"=>"firstname", "err" => "Firstname is required"));
        }
        elseif(!strlen($data['phone'])>0)
        {
            $response = json_encode(array("status"=>false, "err_param"=>"phone", "err" => "Phone is required"));
        }
        elseif(!strlen($data['email'])>0)
        {
            $response = json_encode(array("status"=>false, "err_param"=>"email", "err" => "Email is required"));
        }
        elseif(!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
        {
            $response = json_encode(array("status"=>false, "err_param"=>"email", "err" => "Invalid email"));
        }
        elseif(!intval($data['pay_type'])>0)
        {
            $response = json_encode(array("status"=>false, "err_param"=>"pay_type", "err" => "Pay type is required"));
        }
        else
        {
            $token = safe($_SERVER['HTTP_SECRET']);
            $city = safe($data['city']);
            $no = safe($data['no']);
            $floor = safe($data['floor']);
            $street = safe($data['street']);
            $apt = safe($data['apt']);
            $firstname = safe($data['firstname']);
            $lastname = safe($data['lastname']);
            $phone = safe($data['phone']);
            $email = safe($data['email']);
            $pay_type = intval($data['pay_type']); // 1=>Cash, 2=>card
            $special_req = safe($data['special_req']);

            $created_at = time();
            $updated_at = $status_order = 0;

            $stmt_insert = mysqli_prepare($db, "INSERT INTO `customers` (`firstname`,`lastname`,`phone`,`email`,`city`,`street`,`apartment`,`floor`,`no`,`created_at`,`updated_at`) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt_insert->bind_param('sssssssssii', $firstname,$lastname,$phone,$email,$city,$street,$apt,$floor,$no,$created_at,$updated_at);
            $insert = $stmt_insert->execute();

            if($insert==1)
            {
                $customer_id = $stmt_insert->insert_id;

                $stmt_select = mysqli_prepare($db,"SELECT `id` FROM `orders` WHERE `token`=(?) and `status`=(?) LIMIT 1");
                $stmt_select->bind_param('si', $token,$status_order);
                $stmt_select->execute();
                $stmt_select->bind_result($order_id);
                $stmt_select->store_result();
                $stmt_select->fetch();

                if($stmt_select->num_rows==1 && $order_id>0)
                {
                    $updated_at = time();

                    if($pay_type==1)
                    {
                        $status_order_new = 1;

                        $paid = 0;

                        $stmt_update = mysqli_prepare($db, "UPDATE `orders` SET `customer_id`=(?),`pay_type`=(?),`status`=(?),`paid`=(?),`special_req`=(?) WHERE `token`=(?) and `status`=(?)");
                        $stmt_update->bind_param('iiiissi', $customer_id,$pay_type,$status_order_new,$paid,$special_req,$token,$status_order);
                        $update = $stmt_update->execute();

                        if($update==1)
                        {
                            $response = json_encode(array("status"=>true, "type"=>"update_order", "message" => "Success"));
                        }
                        else
                        {
                            $response = json_encode(array("status"=>false, "err_param"=>"update_order", "err" => "Update order error"));
                        }
                    }
                    else
                    {
                        // Note this line needs to change if you don't use Composer:
                        // require('connect-php-sdk/autoload.php');
                        require 'vendor/autoload.php';

                        // dotenv is used to read from the '.env' file created for credentials
                        $dotenv = Dotenv\Dotenv::create(__DIR__);
                        $dotenv->load();

                        # Replace these values. You probably want to start with your Sandbox credentials
                        # to start: https://docs.connect.squareup.com/articles/using-sandbox/

                        # The access token to use in all Connect API requests. Use your *sandbox* access
                        # token if you're just testing things out.
                        $access_token = ($_ENV["USE_PROD"] == 'true')  ?  $_ENV["PROD_ACCESS_TOKEN"]
                            :  $_ENV["SANDBOX_ACCESS_TOKEN"];
                        $location_id =  ($_ENV["USE_PROD"] == 'true')  ?  $_ENV["PROD_LOCATION_ID"]
                            :  $_ENV["SANDBOX_LOCATION_ID"];

                        // Initialize the authorization for Square
                        \SquareConnect\Configuration::getDefaultConfiguration()->setAccessToken($access_token);

                        # Helps ensure this code has been reached via form submission
                        if ($_SERVER['REQUEST_METHOD'] != 'POST' || $_SERVER['REQUEST_METHOD']=='OPTIONS') {
                            error_log("Received a non-POST request");
                            echo "Request not allowed";
                            http_response_code(405);
                            return;
                        }

                        # Fail if the card form didn't send a value for `nonce` to the server

                        if(!$data['order']>0)
                        {
                            $response = json_encode(array("status"=>false, "err_param"=>"order", "err" => "Order ID is required"));
                        }
                        elseif(!strlen($data['nonce'])>3)
                        {
                            $response = json_encode(array("status"=>false, "err_param"=>"nonce", "err" => "Card nonce is required"));
                        }
                        elseif(!$data['total']>0)
                        {
                            $response = json_encode(array("status"=>false, "err_param"=>"total", "err" => "Total amount is required"));
                        }
                        else
                        {
                            $nonce = safe($data['nonce']);
                            $total = intval($data['total']);
                            $order_id = intval($data['order']);

                            if (is_null($nonce))
                            {
                                $response = json_encode(array("status"=>false, "err_param"=>"nonce", "err" => "Invalid card data"));
                                echo $response;
                                http_response_code(422);
                                exit;
                            }

                            $transactions_api = new \SquareConnect\Api\TransactionsApi();

                            # To learn more about splitting transactions with additional recipients,
                            # see the Transactions API documentation on our [developer site]
                            # (https://docs.connect.squareup.com/payments/transactions/overview#mpt-overview).
                            $request_body = array (
                                "card_nonce" => $nonce,
                                # Monetary amounts are specified in the smallest unit of the applicable currency.
                                # This amount is in cents. It's also hard-coded for $1.00, which isn't very useful.
                                "amount_money" => array (
                                    "amount" => $total,
                                    "currency" => "USD"
                                ),
                                # Every payment you process with the SDK must have a unique idempotency key.
                                # If you're unsure whether a particular payment succeeded, you can reattempt
                                # it with the same idempotency key without worrying about double charging
                                # the buyer.
                                "idempotency_key" => uniqid()
                            );

                            # The SDK throws an exception if a Connect endpoint responds with anything besides
                            # a 200-level HTTP code. This block catches any exceptions that occur from the request.
                            try
                            {
                                $result = $transactions_api->charge($location_id, $request_body);

                                $response_json = json_encode(json_decode($result,true));

                                $updated_at = $created_at = time();
                                $status_active = $paid = 1;
                                $updated_at_trans = 0;

                                $stmt_update = mysqli_prepare($db, "UPDATE `orders` SET `customer_id`=(?),`pay_type`=(?),`payment_date`=(?),`status`=(?),`paid`=(?),`special_req`=(?),`updated_at`=(?) WHERE `id`=(?)");
                                $stmt_update->bind_param('iiiiisii', $customer_id,$pay_type,$created_at,$status_active,$paid,$special_req,$updated_at,$order_id);
                                $update = $stmt_update->execute();

                                $stmt_select = mysqli_prepare($db,"SELECT `id` FROM `transactions` WHERE `order_id`=(?) LIMIT 1");
                                $stmt_select->bind_param('i', $order_id);
                                $stmt_select->execute();
                                $stmt_select->bind_result($order_id_trans);
                                $stmt_select->store_result();
                                $stmt_select->fetch();

                                if($stmt_select->num_rows==1 && $order_id_trans>0)
                                {
                                    $stmt_update = mysqli_prepare($db, "UPDATE `transactions` SET `order_id`=(?),`response`=(?),`update_at`=(?) WHERE `order_id`=(?)");
                                    $stmt_update->bind_param('isii', $order_id,$response_json,$updated_at,$order_id);
                                    $trans_sql = $stmt_update->execute();
                                }
                                else
                                {
                                    $stmt_insert = mysqli_prepare($db, "INSERT INTO `transactions` (`order_id`,`response`,`created_at`,`updated_at`) VALUES (?,?,?,?)");
                                    $stmt_insert->bind_param('isii', $order_id,$response_json,$created_at,$updated_at_trans);
                                    $trans_sql = $stmt_insert->execute();
                                }

                                if($update==1 && $trans_sql==1)
                                {
                                    $response = json_encode(array("status"=>true, "type"=>"process_card", "message" => "Success"));
                                }
                                else
                                {
                                    $response = json_encode(array("status"=>false, "type"=>"process_card", "err" => "Update or insert error"));
                                }
                            }
                            catch (\SquareConnect\ApiException $e)
                            {
                                echo "Caught exception!<br/>";
                                print_r("<strong>Response body:</strong><br/>");
                                echo "<pre>"; var_dump($e->getResponseBody()); echo "</pre>";
                                echo "<br/><strong>Response headers:</strong><br/>";
                                echo "<pre>"; var_dump($e->getResponseHeaders()); echo "</pre>";

                                $response = json_encode(array("status"=>false, "type"=>"process_card", "err" => $e));
                            }
                        }
                    }
                }
                else
                {
                    $response = json_encode(array("status"=>false, "type"=>"confirm_order", "err" => "Not found order"));
                }
            }
            else
            {
                $response = json_encode(array("status"=>false, "type"=>"confirm_order", "err" => "Error customer insert"));
            }
        }
    }
    else
    {
        $response = json_encode(array("status"=>false, "err_param"=>"token", "err" => "Token is required"));
    }
}
else
{
    $response = json_encode(array("status"=>false, "type"=>"confirm_order", "err" => "Not data"));
}

echo $response;



