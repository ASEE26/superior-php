<?php
require "vendor/autoload.php";

use Superior\Wallet;

$wallet = new Superior\Wallet();

$destination1 = (object) [
    'amount' => '0.01',
    'address' => '47Vmj6BXSRPax69cVdqVP5APVLkcxxjjXdcP9fJWZdNc5mEpn3fXQY1CFmJDvyUXzj2Fy9XafvUgMbW91ZoqwqmQ6RjbVtp'
];

$options = [
    'destinations' => $destination1
];

echo $wallet->transfer($options);

