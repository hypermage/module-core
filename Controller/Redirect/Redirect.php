<?php

declare(strict_types=1);

namespace Hypermage\Core\Controller\Redirect;

use Exception;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Redirect implements HttpGetActionInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly ResponseInterface $response,
    )
    {
    }

    public function execute(): ResultInterface|ResponseInterface
    {
        $params = $this->request->getParams();

        if (!isset($params['url'])) {
            throw new Exception('URL parameter is required');
        }

        $url = $params['url'];

        $this->response->setRedirect($url);

        return $this->response;
    }
}
