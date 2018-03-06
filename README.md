# Superior-PHP

A PHP library for the Superior `simplewallet` JSON-RPC interface. 

For more information about Superior, please visit https://getmonero.org/home.

If you found this useful, feel free to donate!

SUP: `﻿5QaiHzo64sLDo42ky98uXtJ3zswCdpUrk1q5nSidtqovjjiC7FmxRt84Zu3HkpYQX1PLDU72aQMK6Cif4muRxwt3RyZXY6y`

## Installation

Install the library using Composer.
    
    composer require thesuperiorcoin/superior-php

## Create an Instance of the Wallet

```php
require 'vendor/autoload.php';
use Superior\Wallet;

$wallet = new Superior\Wallet();
```

Default hostname and port connects to http://127.0.0.1:16036.

To connect to an external IP or different port:

```php
$hostname = YOUR_WALLET_IP;
$port = YOUR_WALLET_PORT;
$wallet = new Superior\Wallet($hostname, $port);
```

## Wallet Methods

### getBalance

```php
$balance = $wallet->getBalance();
```

Responds with the current balance and unlocked (spendable) balance of the wallet in atomic units. Divide by 1e8 to convert.
    
Example response: 

```
{ balance: 3611980142579999, unlocked_balance: 3611980142579999 }
```

### getAddress

```php
$address = $wallet->getAddress();
```

Responds with the Superior address of the wallet.

Example response:

```
{ address: '5QaiHzo64sLDo42ky98uXtJ3zswCdpUrk1q5nSidtqovjjiC7FmxRt84Zu3HkpYQX1PLDU72aQMK6Cif4muRxwt3RyZXY6y' }
```

### transfer

```php
$tx_hash = $wallet->transfer($options);
```

Transfers Superior to a single recipient OR a group of recipients in a single transaction. Responds with the transaction hash of the payment.

Parameters:

* `options` - an array containing `destinations` (object OR array of objects), `mixin` (int), `unlock_time` (int), `payment_id` (string). Only `destinations` is required. Default mixin value is 4.

```php
$options = [
    'destinations' => (object) [
        'amount' => '1',
        'address' => '5QaiHzo64sLDo42ky98uXtJ3zswCdpUrk1q5nSidtqovjjiC7FmxRt84Zu3HkpYQX1PLDU72aQMK6Cif4muRxwt3RyZXY6y'
    ]
];
```

Example response:

```
{ tx_hash: '<a38140b8a6cfd0c8de6b7fe8f83fb2f5ae0f0eec50f6221fefc871649e7b3c68>', tx_key: '' }
```

### transferSplit

```php
$tx_hash = $wallet->transferSplit($options);
```

Same as `transfer()`, but can split into more than one transaction if necessary. Responds with a list of transaction hashes.

Additional property available for the `options` array:

* `new_algorithm` - `true` to use the new transaction construction algorithm. defaults to `false`. (*boolean*)

Example response:

```
{ tx_hash_list: [ '<a38140b8a6cfd0c8de6b7fe8f83fb2f5ae0f0eec50f6221fefc871649e7b3c68>' ] }
```

### sweepDust

```php
$tx_hashes = $wallet->sweepDust();
```

Sends all dust outputs back to the wallet, to make funds easier to spend and mix. Responds with a list of the corresponding transaction hashes.

Example response:

```
{ tx_hash_list: [ '<a38140b8a6cfd0c8de6b7fe8f83fb2f5ae0f0eec50f6221fefc871649e7b3c68>' ] }
```

### getPayments

```php
$payments = $wallet->getPayments($payment_id);
```

Returns a list of incoming payments using a given payment ID.

Parameters:

* `paymentID` - the payment ID to scan wallet for included transactions (*string*)

### getBulkPayments

```php
$payments = $wallet->getBulkPayments($payment_id, $height);
```

Returns a list of incoming payments using a single payment ID or a list of payment IDs from a given height.

Parameters:

* `paymentIDs` - the payment ID or list of IDs to scan wallet for (*array*)
* `minHeight` - the minimum block height to begin scanning from (example: 800000) (*int*)

### incomingTransfers

```php
$transfers = $wallet->incomingTransfers($type);
```

Returns a list of incoming transfers to the wallet.

Parameters:

* `type` - accepts `"all"`: all the transfers, `"available"`: only transfers that are not yet spent, or `"unavailable"`: only transfers which have been spent (*string*)

### queryKey

```php
$key = $wallet->queryKey($type);
```

Returns the wallet's spend key (mnemonic seed) or view private key.

Parameters:

* `type` - accepts `"mnemonic"`: the mnemonic seed for restoring the wallet, or `"view_key"`: the wallet's view key (*string*)

### integratedAddress

```php
$integratedAddress = $wallet->integratedAddress($payment_id);
```

Make and return a new integrated address from your wallet address and a given payment ID, or generate a random payment ID if none is given.

Parameters:

* `payment_id` - a 64 character hexadecimal string. If not provided, a random payment ID is automatically generated. (*string*, optional)

Example response:

```
{ integrated_address: '5QaiHzo64sLDo42ky98uXtJ3zswCdpUrk1q5nSidtqovjjiC7FmxRt84Zu3HkpYQX1PLDU72aQMK6Cif4muRxwt3RyZXY6y' }
```

### splitIntegrated

```php
$splitIntegrated = $wallet->splitIntegrated($integrated_address);
```

Returns the standard address and payment ID corresponding for a given integrated address.

Parameters:

* `integrated_address` - an Superior integrated address (*string*)

Example response:

```
{ payment_id: '<61eec5ffd3b9cb57>',
  standard_address: '5QaiHzo64sLDo42ky98uXtJ3zswCdpUrk1q5nSidtqovjjiC7FmxRt84Zu3HkpYQX1PLDU72aQMK6Cif4muRxwt3RyZXY6y' }
```

### getHeight 
Usage:

```
$height = $wallet->getHeight();
```

Returns the current block height of the daemon.

Example response:

```
{ height: 874458 }
```

### stopWallet

```php
$wallet->stopWallet();
```

Cleanly shuts down the current simplewallet process.


### Introduction


This is a list of the superior-wallet-rpc calls, their inputs and outputs, and examples of each. The program superior-wallet-rpc replaced the rpc interface that was in simplewallet and then superior-wallet-cli.
All superior-wallet-rpc methods use the same JSON RPC interface. For example:
```
IP=127.0.0.1
PORT=16036
METHOD="make_integrated_address"
PARAMS="{\"payment_id\":\"1234567890123456789012345678900012345678901234567890123456789000\"}"
curl \
    -X POST http://$IP:$PORT/json_rpc \
    -d '{"jsonrpc":"2.0","id":"0","method":"'$METHOD'","params":'"$PARAMS"'}' \
    -H 'Content-Type: application/json'
```

If the superior-wallet-rpc was executed with the --rpc-login argument as username:password, then follow this example:

```
IP=127.0.0.1
PORT=16036
METHOD="make_integrated_address"
PARAMS="{\"payment_id\":\"1234567890123456789012345678900012345678901234567890123456789000\"}"
curl \
    -u username:password --digest \
    -X POST http://$IP:$PORT/json_rpc \
    -d '{"jsonrpc":"2.0","id":"0","method":"'$METHOD'","params":'"$PARAMS"'}' \
    -H 'Content-Type: application/json'
```
Note: "atomic units" refer to the smallest fraction of 1 SUP according to the superiord implementation. 1 SUP = 1e8 atomic units.

### Index of JSON RPC Methods:
	•	getbalance
	•	getaddress
	•	getheight
	•	transfer
	•	transfer_split
	•	sweep_dust
	•	store
	•	get_payments
	•	get_bulk_payments
	•	get_transfers
	•	incoming_transfers
	•	query_key
	•	make_integrated_address
	•	split_integrated_address
	•	stop_wallet


### JSON RPC Methods:
```
getbalance
```
Return the wallet's balance.
Inputs: None.
Outputs:
	•	balance - unsigned int; The total balance of the current Superior-wallet-rpc in session.
	•	unlocked_balance - unsigned int; Unlocked funds are those funds that are sufficiently deep enough in the Superior blockchain to be considered safe to spend.

Example:
```
[ superior->~ ]$ curl -X POST http://127.0.0.1:16036/json_rpc -d '{"jsonrpc":"2.0","id":"0","method":"getbalance"}' -H 'Content-Type: application/json' { "id": "0", "jsonrpc": "2.0", "result": { "balance": 140000000000, "unlocked_balance": 50000000000 } }
getaddress
```
Return the wallet's address.
Inputs: None.
Outputs:

	•	address - string; The 95-character hex address string of the superior-wallet-rpc in session.

Example:
```
[ superior->~ ]$ curl -X POST http://127.0.0.1:16036/json_rpc -d '{"jsonrpc":"2.0","id":"0","method":"getaddress"}' -H 'Content-Type: application/json' { "id": "0", "jsonrpc": "2.0", "result": { "address": "5QaiHzo64sLDo42ky98uXtJ3zswCdpUrk1q5nSidtqovjjiC7FmxRt84Zu3HkpYQX1PLDU72aQMK6Cif4muRxwt3RyZXY6y" } }
getheight
```

Returns the wallet's current block height.
Inputs: None.
Outputs:
	•	height - string; The current superior-wallet-rpc's blockchain height. If the wallet has been offline for a long time, it may need to catch up with the daemon.

Example:
```
[ superior->~ ]$ curl -X POST http://127.0.0.1:16036/json_rpc -d '{"jsonrpc":"2.0","id":"0","method":"getheight"}' -H 'Content-Type: application/json' { "id": "0", "jsonrpc": "2.0", "result": { "height": 994310 } }
transfer
```
Send superior to a number of recipients.

Inputs:
	•	destinations - array of destinations to receive Sup:
	◦	amount - unsigned int; Amount to send to each destination, in atomic units.
	◦	address - string; Destination public address.
	•	fee - unsigned int; Ignored, will be automatically calculated.
	•	mixin - unsigned int; Number of outpouts from the blockchain to mix with (0 means no mixing).
	•	unlock_time - unsigned int; Number of blocks before the superior can be spent (0 to not add a lock).
	•	payment_id - string; (Optional) Random 32-byte/64-character hex string to identify a transaction.
	•	get_tx_key - boolean; (Optional) Return the transaction key after sending. Outputs: 
	•	fee - Integer value of the fee charged for the txn.
	•	tx_hash - String for the publically searchable transaction hash
	•	tx_key - String for the transaction key if get_tx_key is true, otherwise, blank string.

Example:
```
[ superior->~ ]$ curl -X POST http://127.0.0.1:16036/json_rpc -d '{"jsonrpc":"2.0","id":"0","method":"transfer","params":{"destinations":[{"amount":100000000,"address":"5QaiHzo64sLDo42ky98uXtJ3zswCdpUrk1q5nSidtqovjjiC7FmxRt84Zu3HkpYQX1PLDU72aQMK6Cif4muRxwt3RyZXY6y"},{"amount":200000000,"address":"5QaiHzo64sLDo42ky98uXtJ3zswCdpUrk1q5nSidtqovjjiC7FmxRt84Zu3HkpYQX1PLDU72aQMK6Cif4muRxwt3RyZXY6y"}],"mixin":4,"get_tx_key": true}}' -H 'Content-Type: application/json' { "id": "0", "jsonrpc": "2.0", "result": { "fee": 48958481211, "tx_hash": "a38140b8a6cfd0c8de6b7fe8f83fb2f5ae0f0eec50f6221fefc871649e7b3c68", "tx_key": "a38140b8a6cfd0c8de6b7fe8f83fb2f5ae0f0eec50f6221fefc871649e7b3c68" } }
transfer_split
```
Same as transfer, but can split into more than one tx if necessary.

Inputs:
	•	destinations - array of destinations to receive Sup:
	◦	amount - unsigned int; Amount to send to each destination, in atomic units.
	◦	address - string; Destination public address.
	•	fee - unsigned int; Ignored, will be automatically calculated.
	•	mixin - unsigned int; Number of outpouts from the blockchain to mix with (0 means no mixing).
	•	unlock_time - unsigned int; Number of blocks before the superior can be spent (0 to not add a lock).
	•	payment_id - string; (Optional) Random 32-byte/64-character hex string to identify a transaction.
	•	get_tx_key - boolean; (Optional) Return the transaction key after sending. – Ignored
	•	new_algorithm - boolean; True to use the new transaction construction algorithm, defaults to false.

Outputs:
	•	fee_list - array of: integer
	•	tx_hash_list - array of: string

Example:
```
[ superior->~ ]$ curl -X POST http://127.0.0.1:16036/json_rpc -d '{"jsonrpc":"2.0","id":"0","method":"transfer_split","params":{"destinations":[{"amount":100000000,"address":"5wNgSYy2F9qPZu7KBjvsFgZLTKE2TZgEpNFbGka9gA5zPmAXS35QzzYaLKJRkYTnzgArGNX7TvSqZC87tBwtaC5RQgJ8rm"},{"amount":200000000,"address":"5vH5D7Fv47mbpCpdcthcjU34rqiiAYRCh1tYywmhqnEk9iwCE9yppgNCXAyVHG5qJt2kExa42TuhzQfJbmbpeGLkVbg8xit"},{"amount":200000000,"address":"5vC5Q25cR1d3WzKX6dpTaLJaqZyDrtTnfadTmVuB1Wue2tyFGxUhiE4RGa74pEDJv7gSySzcd1Ao6G1nzSaqp78vLfP6MPj"},{"amount":200000000,"address":"52MSrn49ziBPJBh8ZNEhhbfyLMou6mao4C1F5TLGUatmUnCxZArDYkcbAnVkVEopWVeak2rKDrmc8JpoS7n5dvfN9YDPBTG"},{"amount":200000000,"address":"5tEDyVQ8zgRQbDYiykTdpw5kZ6qWQWcKfExEj9eQshjpGb3sdr3UyWE2AHWzUGzJjaH9HN1DdGBdyQQ4AqGMc7rr5xYwZWW"}],"mixin":4,"get_tx_key": true, "new_algorithm": true}}' -H 'Content-Type: application/json' { "id": "0", "jsonrpc": "2.0", "result": { "fee_list": [97916962422], "tx_hash_list": ["c5c389846e701c27aaf1f7ab8b9dc457b471fcea5bc9710e8020d51275afbc54"] } }
sweep_dust
```
Send all dust outputs back to the wallet's, to make them easier to spend (and mix).
Inputs: None.
Outputs:

	•	tx_hash_list - list of: string

Example (In this example, sweep_dust returns an error due to insufficient funds to sweep):
```
[ superior->~ ]$ curl -X POST http://127.0.0.1:16036/json_rpc -d '{"jsonrpc":"2.0","id":"0","method":"sweep_dust"}' -H 'Content-Type: application/json' { "error": { "code": -4, "message": "not enough money" }, "id": "0", "jsonrpc": "2.0" }
store
```
Save the blockchain.
Inputs: None.
Outputs: None.

Example:
```
[ superior->~ ]$ curl -X POST http://127.0.0.1:16036/json_rpc -d '{"jsonrpc":"2.0","id":"0","method":"store"}' -H 'Content-Type: application/json' { "id": "0", "jsonrpc": "2.0", "result": { } }
get_payments
```
Get a list of incoming payments using a given payment id.

Inputs:
	•	payment_id - string
Outputs:
	•	payments - list of:
	◦	payment_id - string
	◦	tx_hash - string
	◦	amount - unsigned int
	◦	block_height - unsigned int
	◦	unlock_time - unsigned int

Example:
```
[ superior->~ ]$ curl -X POST http://127.0.0.1:16036/json_rpc -d '{"jsonrpc":"2.0","id":"0","method":"get_payments","params":{"payment_id":"4279257e0a20608e25dba8744949c9e1caff4fcdafc7d5362ecf14225f3d9030"}}' -H 'Content-Type: application/json' { "id": "0", "jsonrpc": "2.0", "result": { "payments": [{ "amount": 10350000000000, "block_height": 994327, "payment_id": "4279257e0a20608e25dba8744949c9e1caff4fcdafc7d5362ecf14225f3d9030", "tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1", "unlock_time": 0 }] } }
get_bulk_payments
```
Get a list of incoming payments using a given payment id, or a list of payments ids, from a given height. This method is the preferred method over get_payments because it has the same functionality but is more extendable. Either is fine for looking up transactions by a single payment ID.

Inputs:
	•	payment_ids - array of: string
	•	min_block_height - unsigned int; The block height at which to start looking for payments.
Outputs:
	•	payments - list of:
	◦	payment_id - string
	◦	tx_hash - string
	◦	amount - unsigned int
	◦	block_height - unsigned int
	◦	unlock_time - unsigned int

Example:
```
[ superior->~ ]$ curl -X POST http://127.0.0.1:16036/json_rpc -d '{"jsonrpc":"2.0","id":"0","method":"get_bulk_payments","params":{"payment_ids":["4279257e0a20608e25dba8744949c9e1caff4fcdafc7d5362ecf14225f3d9030"],"min_block_height":990000}}' -H 'Content-Type: application/json' { "id": "0", "jsonrpc": "2.0", "result": { "payments": [{ "amount": 10350000000000, "block_height": 994327, "payment_id": "4279257e0a20608e25dba8744949c9e1caff4fcdafc7d5362ecf14225f3d9030", "tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1", "unlock_time": 0 }] } }
get_transfers
```
Returns a list of transfers.

Inputs:
	•	in - boolean;
	•	out - boolean;
	•	pending - boolean;
	•	failed - boolean;
	•	pool - boolean;
	•	filter_by_height - boolean;
	•	min_height - unsigned int;
	•	max_height - unsigned int;
Outputs:
	•	in array of transfers:
	◦	txid - string;
	◦	payment_id - string;
	◦	height - unsigned int;
	◦	timestamp - unsigned int;
	◦	amount - unsigned int;
	◦	fee - unsigned int;
	◦	note - string;
	◦	destinations - std::list;
	◦	type - string;
	•	out array of transfers
	•	pending array of transfers
	•	failed array of transfers
	•	pool array of transfers

Example:
```
[ superior->~ ]$ curl -X POST http://127.0.0.1:16036/json_rpc -d '{"jsonrpc":"2.0","id":"0","method":"get_transfers","params":{"pool":true}}' -H 'Content-Type: application/json' { "id": "0", "jsonrpc": "2.0", "result": { "pool": [{ "amount": 500000000000, "fee": 0, "height": 0, "note": "", "payment_id": "758d9b225fda7b7f", "timestamp": 1488312467, "txid": "da7301d5423efa09fabacb720002e978d114ff2db6a1546f8b820644a1b96208", "type": "pool" }] } }
incoming_transfers
```
Return a list of incoming transfers to the wallet.

Inputs:
	•	transfer_type - string; "all": all the transfers, "available": only transfers which are not yet spent, OR "unavailable": only transfers which are already spent.
Outputs:
	•	transfers - list of:
	◦	amount - unsigned int
	◦	spent - boolean
	◦	global_index - unsigned int; Mostly internal use, can be ignored by most users.
	◦	tx_hash - string; Several incoming transfers may share the same hash if they were in the same transaction.
	◦	tx_size - unsigned int

Example (Return "all" transaction types):
```
[ superior->~ ]$ curl -X POST http://127.0.0.1:16036/json_rpc -d '{"jsonrpc":"2.0","id":"0","method":"incoming_transfers","params":{"transfer_type":"all"}}' -H 'Content-Type: application/json' { "id": "0", "jsonrpc": "2.0", "result": { "transfers": [{ "amount": 10000000000000, "global_index": 711506, "spent": false, "tx_hash": "<c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1>", "tx_size": 5870 },{ "amount": 300000000000, "global_index": 794232, "spent": false, "tx_hash": "<c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1>", "tx_size": 5870 },{ "amount": 50000000000, "global_index": 213659, "spent": false, "tx_hash": "<c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1>", "tx_size": 5870 }] } }
```
Example (Return "available" transactions):
```
[ superior->~ ]$ curl -X POST http://127.0.0.1:16036/json_rpc -d '{"jsonrpc":"2.0","id":"0","method":"incoming_transfers","params":{"transfer_type":"available"}}' -H 'Content-Type: application/json' { "id": "0", "jsonrpc": "2.0", "result": { "transfers": [{ "amount": 10000000000000, "global_index": 711506, "spent": false, "tx_hash": "<c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1>", "tx_size": 5870 },{ "amount": 300000000000, "global_index": 794232, "spent": false, "tx_hash": "<c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1>", "tx_size": 5870 },{ "amount": 50000000000, "global_index": 213659, "spent": false, "tx_hash": "<c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1>", "tx_size": 5870 }] } }
```
Example (Return "unavailable" transaction. Note that this particular example returns 0 unavailable transactions):
```
[ superior->~ ]$ curl -X POST http://127.0.0.1:16036/json_rpc -d '{"jsonrpc":"2.0","id":"0","method":"incoming_transfers","params":{"transfer_type":"unavailable"}}' -H 'Content-Type: application/json' { "id": "0", "jsonrpc": "2.0", "result": { } }
query_key
```
Return the spend or view private key.
Inputs:
	•	key_type - string; Which key to retrieve: "mnemonic" - the mnemonic seed (older wallets do not have one) OR "view_key" - the view key
Outputs:
	•	key - string; The view key will be hex encoded, while the mnemonic will be a string of words.

Example (Query view key):
```
[ superior->~ ]$ curl -X POST http://127.0.0.1:16036/json_rpc -d '{"jsonrpc":"2.0","id":"0","method":"query_key","params":{"key_type":"view_key"}}' -H 'Content-Type: application/json' { "id": "0", "jsonrpc": "2.0", "result": { "key": "7e341d…" } }
```
Example (Query mnemonic key):
```
[ superior->~ ]$ curl -X POST http://127.0.0.1:16036/json_rpc -d '{"jsonrpc":"2.0","id":"0","method":"query_key","params":{"key_type":"mnemonic"}}' -H 'Content-Type: application/json' { "id": "0", "jsonrpc": "2.0", "result": { "key": "adapt adapt nostril …" } }
make_integrated_address
```
Make an integrated address from the wallet address and a payment id.

Inputs:
	•	payment_id - string; hex encoded; can be empty, in which case a random payment id is generated
Outputs:
	•	integrated_address - string

Example (Payment ID is empty, use a random ID):
```
[ superior->~ ]$ curl -X POST http://127.0.0.1:16036/json_rpc -d '{"jsonrpc":"2.0","id":"0","method":"make_integrated_address","params":{"payment_id":""}}' -H 'Content-Type: application/json' { "id": "0", "jsonrpc": "2.0", "result": { "integrated_address": "5BpEv3WrufwXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CGuJiWFQjhnhhwiH1FsHYGQQ8H2RRJveAtUeiFs6J" } }
split_integrated_address
```
Retrieve the standard address and payment id corresponding to an integrated address.

Inputs:
	•	integrated_address - string
Outputs:
	•	standard_address - string
	•	payment - string; hex encoded

Example:
```
[ superior->~ ]$ curl -X POST http://127.0.0.1:16036/json_rpc -d '{"jsonrpc":"2.0","id":"0","method":"split_integrated_address","params":{"integrated_address": "5BpEv3WrufwXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CGuJiWFQjhnhhwiH1FsHYGQQ8H2RRJveAtUeiFs6J"}}' -H 'Content-Type: application/json' { "id": "0", "jsonrpc": "2.0", "result": { "payment_id": "<420fa29b2d9a49f5>", "standard_address": "527ZuEhNJQRXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CGuJiWFQjhnhhwiH1FsHYGQGaDsaBA" } }
stop_wallet
```
Stops the wallet, storing the current state.

Inputs: None.
Outputs: None.

Example:
```
[ superior->~ ]$ curl -X POST http://127.0.0.1:16036/json_rpc -d '{"jsonrpc":"2.0","id":"0","method":"stop_wallet"}' -H 'Content-Type: application/json' { "id": "0", "jsonrpc": "2.0", "result": { } }
make_uri
```
Create a payment URI using the official URI spec.

Inputs:
	•	address - wallet address string
	•	amount (optional) - the integer amount to receive, in atomic units
	•	payment_id (optional) - 16 or 64 character hexadecimal payment id string
	•	recipient_name (optional) - string name of the payment recipient
	•	tx_description (optional) - string describing the reason for the tx
Outputs:
	•	uri - a string containing all the payment input information as a properly formatted payment URI

Example:
```
[ superior->~ ]$curl -X POST http://127.0.0.1:16036/json_rpc -d '{"jsonrpc":"2.0","id":"0","method":"make_uri","params":{"address":"54AFFq5kSiGBoZ4NMDwYtN18obc8AemS33DBLWs3H7otXft3XjrpDtQGv7SqSsaBYBb98uNbr2VBBEt7f2wfn3RVGQBEP3A","amount":10,"payment_id":"0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef","tx_description":"Testing out the make_uri function.","recipient_name":"Superior Project donation address"}}' -H 'Content-Type: application/json' { "id": 0, "jsonrpc": "2.0", "result": { "uri": "superior:54AFFq5kSiGBoZ4NMDwYtN18obc8AemS33DBLWs3H7otXft3XjrpDtQGv7SqSsaBYBb98uNbr2VBBEt7f2wfn3RVGQBEP3A?tx_payment_id=0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef&tx_amount=0.000000000010&recipient_name=Superior%20Project%20donation%20address&tx_description=Testing%20out%20the%20make_uri%20function." } }
parse_uri
```
Parse a payment URI to get payment information.

Inputs:
	•	uri - a string containing all the payment input information as a properly formatted payment URI
Outputs:
	•	uri - JSON object containing parment information:
	◦	address - wallet address string
	◦	amount - the decimal amount to receive, in coin units (0 if not provided)
	◦	payment_id - 16 or 64 character hexadecimal payment id string (empty if not provided)
	◦	recipient_name - string name of the payment recipient (empty if not provided)
	◦	tx_description - string describing the reason for the tx (empty if not provided)

Example:
```
[ superior->~ ]$curl -X POST http://127.0.0.1:16036/json_rpc -d '{"jsonrpc":"2.0","id":"0","method":"parse_uri","params":{"uri":"superior:44AFFq5kSiGBoZ4NMDwYtN18obc8AemS33DBLWs3H7otXft3XjrpDtQGv7SqSsaBYBb98uNbr2VBBEt7f2wfn3RVGQBEP3A?tx_payment_id=0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef&tx_amount=0.000000000010&recipient_name=Superior%20Project%20donation%20address&tx_description=Testing%20out%20the%20make_uri%20function."}}' -H 'Content-Type: application/json' { "id": 0, "jsonrpc": "2.0", "result": { "uri": { "address": "54AFFq5kSiGBoZ4NMDwYtN18obc8AemS33DBLWs3H7otXft3XjrpDtQGv7SqSsaBYBb98uNbr2VBBEt7f2wfn3RVGQBEP3A", "amount": 10, "payment_id": "0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef", "recipient_name": "Superior Project donation address", "tx_description": "Testing out the make_uri function." } } }
```
