<?php
    require_once ('vendor/autoload.php');

    //Replace your access token and location ID
    $accessToken = 'EAAAEATqGfLVs31hLJ08fmh5XjNXMOfQd5_da3NF_Uyph4Vh0jJw8jMFT3TOqV2p';
    $locationId = '016WQTVDBAFAK';

    // Create and configure a new API client object
    $defaultApiConfig = new \SquareConnect\Configuration();
    $defaultApiConfig->setAccessToken($accessToken);
    $defaultApiClient = new \SquareConnect\ApiClient($defaultApiConfig);
    $checkoutClient = new SquareConnect\Api\CheckoutApi($defaultApiClient);

    //Create a Money object to represent the price of the line item.
    $price = new \SquareConnect\Model\Money;
    $price->setAmount(600);
    $price->setCurrency('USD');

    //Create the line item and set details
    $book = new \SquareConnect\Model\CreateOrderRequestLineItem;
    $book->setName('The Shining');
    $book->setQuantity('2');
    $book->setBasePriceMoney($price);

    //Puts our line item object in an array called lineItems.
    $lineItems = array();
    array_push($lineItems, $book);

    // Create an Order object using line items from above
    $order = new \SquareConnect\Model\CreateOrderRequest();

    $order->setIdempotencyKey(uniqid()); //uniqid() generates a random string.

    //sets the lineItems array in the order object
    $order->setLineItems($lineItems);

    $checkout = new \SquareConnect\Model\CreateCheckoutRequest();

    $checkout->setIdempotencyKey(uniqid()); //uniqid() generates a random string.
    $checkout->setOrder($order); //this is the order we created in the previous step

    try {
        $result = $checkoutClient->createCheckout(
            $locationId,
            $checkout
        );
        //Save the checkout ID for verifying transactions
        $checkoutId = $result->getCheckout()->getId();
        //Get the checkout URL that opens the checkout page.
        $checkoutUrl = $result->getCheckout()->getCheckoutPageUrl();
        print_r('Complete your transaction: ' . $checkoutUrl);
    } catch (Exception $e) {
        echo 'Exception when calling CheckoutApi->createCheckout: ', $e->getMessage(), PHP_EOL;
    }
?>