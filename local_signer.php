<?php
namespace Cobo\Custody;
require __DIR__ . "/vendor/autoload.php";


use Elliptic\EC;

require "api_signer.php";

class LocalSigner implements ApiSigner
{
    public $secretKey;

    public function __construct($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function sign($message):string
    {
        $message = hash("sha256", hash("sha256", $message, True), True);
        $ec = new EC('secp256k1');
        $key = $ec->keyFromPrivate($this->secretKey);
        return $key->sign(bin2hex($message))->toDER('hex');
    }

    public function getPublicKey():string
    {
        $ec = new EC('secp256k1');
        $key = $ec->keyFromPrivate($this->secretKey);
        return $key->getPublic(true,'hex');
    }

    public static function generateKeyPair(): array
    {
        $ec = new EC('secp256k1');
        $key = $ec->genKeyPair();
        return [
            "apiSecret" => $key->getPrivate('hex'),
            "apiKey" => $key->getPublic(true, 'hex')
        ];

    }
}