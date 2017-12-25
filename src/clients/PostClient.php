<?php

namespace Platron\Starrys\clients;

use Platron\Starrys\clients\iClient;
use Platron\Starrys\SdkException;
use Platron\Starrys\services\BaseServiceRequest;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class PostClient implements iClient {

	/** @var string */
	protected $url;
    /** @var string путь до приватного ключа */
    protected $secretKeyPath;
    /** @var string путь до сертификата */
    protected $certPath;

    /** @var LoggerInterface */
    protected $logger;
    /** @var int */
    protected $connectionTimeout = 30;
    
    /**
     * Секретный ключ для подписи запросов
	 * @param string $url Путь для запросов https://<адрес, указанный в личном кабинете>:<порт, указанный в личном кабинете>
     * @param string $secretKeyPath
	 * @param string $certPath
     */
    public function __construct($url, $secretKeyPath, $certPath){
		$this->url = $url;
        $this->secretKeyPath = $secretKeyPath;
        $this->certPath = $certPath;
    }
    
    /**
     * Установить логер
     * @param LoggerInterface $logger
     * @return self
     */
    public function setLogger(LoggerInterface $logger){
        $this->logger = $logger;
        return $this;
    }
    
    /**
     * Установка максимального времени ожидания
     * @param int $connectionTimeout
     * @return self
     */
    public function setConnectionTimeout($connectionTimeout){
        $this->connectionTimeout = $connectionTimeout;
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function sendRequest(BaseServiceRequest $service) {       
        $requestParameters = $service->getParameters();
        $requestUrl = $this->url.$service->getUrlPath();
		
        $curl = curl_init($requestUrl);
        if(!empty($requestParameters)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestParameters));
        }
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSLKEY, $this->secretKeyPath);
        curl_setopt($curl, CURLOPT_SSLCERT, $this->certPath);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->connectionTimeout);
        
        $response = curl_exec($curl);
		
        if($this->logger){
            $this->logger->log(LogLevel::INFO, 'Requested url '.$requestUrl.' params '. json_encode($requestParameters));
            $this->logger->log(LogLevel::INFO, 'Response '.$response);
        }
        	
		if(curl_errno($curl)){
			throw new SdkException(curl_error($curl), curl_errno($curl));
		}
        
        if(empty(json_decode($response))){
            throw new SdkException('Not json response '.$response);
        }

		return json_decode($response);
    }

}
