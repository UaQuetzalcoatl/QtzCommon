<?php

namespace QtzCommon\Validator;

use Zend\Validator\AbstractValidator;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Stdlib\ArrayUtils;

/**
 * Description of RemoteContentExists
 *
 * @author alex
 */
class RemoteContentExists extends AbstractValidator
{
    /**
     * Error constants
     */
    const ERROR_NO_CONTENT_EXISTS = 'noContentExists';

    const ERROR_INVALID_URL = 'invalidUrl';

    /**
     * @var array Message templates
     */
    protected $messageTemplates = array(
        self::ERROR_NO_CONTENT_EXISTS => "Content does not exists",
        self::ERROR_INVALID_URL => "Invalid url",
    );

    /**
     *
     * @var \Zend\Http\Client
     */
    protected $httpClient;

    /**
     * Constructor
     *
     * @param array|Traversable|Client $options Options to use for this validator
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        if ($options instanceof Client) {
            $this->setHttpClient($options);

            return;
        }

        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (is_array($options) && array_key_exists('client', $options)) {
            $this->setHttpClient($options['client']);
        }
    }

    /**
     * Validate content
     *
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        try {
            $client = $this->getHttpClient();
            $response = $client->reset()
                ->setUri($value)
                ->setMethod(Request::METHOD_HEAD)
                ->send();
        } catch (\Zend\Http\Client\Adapter\Exception\RuntimeException $e) {
            $this->error(self::ERROR_INVALID_URL);
            return false;
        } catch (\Exception $e) {
            $this->error(self::ERROR_NO_CONTENT_EXISTS);
            return false;
        }

        if ($response->getStatusCode() != 200) {
            $this->error(self::ERROR_NO_CONTENT_EXISTS);
            return false;
        }

        return true;
    }

    /**
     * Get http client
     *
     * @return \Zend\Http\Client
     */
    public function getHttpClient()
    {
        if (null === $this->httpClient) {

            $this->httpClient = new Client(
                null,
                array(
                    'adapter'   => 'Zend\Http\Client\Adapter\Curl',
                    'curloptions' => array(CURLOPT_NOBODY => true)
                )
            );
        }

        return $this->httpClient;
    }

    /**
     * Set http client
     *
     * @param \Zend\Http\Client $httpClient
     * @return \QtzCommon\Validator\RemoteContentExists
     */
    public function setHttpClient(Client $httpClient)
    {
        $this->httpClient = $httpClient;

        return $this;
    }
}
