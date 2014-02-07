<?php

namespace QtzCommon;

use Zend\Validator\AbstractValidator;
use Zend\Http\Client;
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
    const ERROR_INVALID_URI       = 'invalidUri';

    /**
     * @var array Message templates
     */
    protected $messageTemplates = array(
        self::ERROR_NO_CONTENT_EXISTS => "Content does not exists",
        self::ERROR_INVALID_URI       => "The input does not appear to be a valid Uri",
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
                ->send();

        } catch (\Zend\Http\Exception\InvalidArgumentException $e) {
            $this->error(self::ERROR_INVALID_URI);
            return false;
        } catch (\Exception $e) {
            $this->error(self::ERROR_NO_CONTENT_EXISTS);
            return false;
        }

        if ($response->setStatusCode() != 200) {
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
        return $this->httpClient;
    }

    /**
     * Set http client
     *
     * @param \Zend\Http\Client $httpClient
     * @return \QtzCommon\RemoteContentExists
     */
    public function setHttpClient(Client $httpClient)
    {
        $this->httpClient = $httpClient;

        return $this;
    }
}
