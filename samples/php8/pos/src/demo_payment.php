<?php

namespace KodyPayTerminalDemo;

require __DIR__ . '/../vendor/autoload.php';

use Com\Kodypay\Grpc\Ecom\V1\PaymentDetailsResponse\Response\PaymentStatus;
use Com\Kodypay\Grpc\Pay\V1\KodyPayTerminalServiceClient;
use Com\Kodypay\Grpc\Pay\V1\PayRequest;
use Com\Kodypay\Grpc\Pay\V1\PayResponse;
use Com\Kodypay\Grpc\Pay\V1\CancelRequest;
use Com\Kodypay\Grpc\Pay\V1\TerminalsRequest;
use Com\Kodypay\Grpc\Pay\V1\PaymentDetailsRequest;
use Grpc\ChannelCredentials;
use Grpc\Timeval;
use Grpc\Metadata;

class KodyTerminalClient
{
    private const REQUEST_TIMEOUT_MINS = 3; // 3 minute request timeout
    private string $store;
    private string $apiKey;
    private KodyPayTerminalServiceClient $client;

    /**
     * Creates a new instance of the KodyTerminalClient class.
     */
    public function __construct()
    {
        $config = require __DIR__ . '/config.php';
        $this->store = $config['store_id'];
        $this->apiKey = $config['api_key'];
        $this->client = new KodyPayTerminalServiceClient($config['hostname'], [
            'credentials' => ChannelCredentials::createSsl()
        ]);
    }

    /**
     * Sends a payment request with the specified amount and terminal ID.
     *
     * @param float $amount The amount of the payment.
     * @param string $terminalId The terminal ID.
     * @param callable|null $orderIdCallback An optional callback function to be called with the generated order ID.
     * @return PayResponse The payment response object.
     */
    public function sendPayment(float $amount, string $terminalId, ?callable $orderIdCallback = null): PayResponse
    {
        $req = new PayRequest();
        $req->setStoreId($this->store);
        $req->setAmount(number_format($amount, 2, '.', ''));
        $req->setTerminalId($terminalId);

        $call = $this->client->Pay($req, $this->getApiKeyHeaders(), ['timeout' => $this->getTimeout()]);
        $response = new PayResponse();
        $response->setStatus(PaymentStatus::PENDING);

        foreach ($call->responses() as $reply) {
            if ($reply->getStatus() === PaymentStatus::PENDING) {
                $response = $reply;
                if ($orderIdCallback !== null) {
                    $orderIdCallback($response->getOrderId());
                }
            } else {
                $response = $reply;
                break;
            }
        }

        return $response;
    }

    /**
     * Cancels a payment with the specified amount, terminal ID, and order ID.
     *
     * @param float $amount The amount of the payment to cancel.
     * @param string $terminalId The ID of the terminal associated with the payment.
     * @param string $orderId The ID of the order associated with the payment.
     * @return PaymentStatus The status of the cancellation request.
     */
    public function cancelPayment(float $amount, string $terminalId, string $orderId): PaymentStatus
    {
        $cancel = new CancelRequest();
        $cancel->setStoreId($this->store);
        $cancel->setAmount(number_format($amount, 2, '.', ''));
        $cancel->setTerminalId($terminalId);
        $cancel->setOrderId($orderId);

        [$response, $status] = $this->client->Cancel($cancel, $this->getApiKeyHeaders(), ['timeout' => $this->getTimeout()])->wait();
        return $response->getStatus();
    }

    /**
     * Retrieves a list of payment terminals.
     *
     * @return array A list of payment terminals.
     */
    public function getTerminals(): array
    {
        $request = new TerminalsRequest();
        $request->setStoreId($this->store);

        [$response, $status] = $this->client->Terminals($request, $this->getApiKeyHeaders())->wait();
        return $response->getTerminals();
    }

    /**
     * Retrieves payment details for a given order ID.
     *
     * @param string $orderId The ID of the order associated with the payment.
     * @return PayResponse The payment response containing the payment details.
     */
    public function getDetails(string $orderId): PayResponse
    {
        $request = new PaymentDetailsRequest();
        $request->setStoreId($this->store);
        $request->setOrderId($orderId);

        [$response, $status] = $this->client->PaymentDetails($request, $this->getApiKeyHeaders(), ['timeout' => $this->getTimeout()])->wait();
        return $response;
    }

    /**
     * Gets the deadline for the request.
     *
     * @return Timeval The deadline for the request.
     */
    private function getTimeout(): Timeval
    {
        return Timeval::fromSeconds(self::REQUEST_TIMEOUT_MINS * 60);
    }

    /**
     * Gets the API key headers for the request.
     *
     * @return array The API key headers.
     */
    private function getApiKeyHeaders(): array
    {
        return ['X-API-Key' => [$this->apiKey]];
    }
}