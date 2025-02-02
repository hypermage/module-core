<?php

declare(strict_types=1);

namespace Hypermage\Core\Controller\Block;

use Exception;
use Hypermage\Core\Controller\Result\Component as ComponentResult;
use Hypermage\Core\Model\ComponentFactory;
use Hypermage\Core\Model\PageProvider;
use Hypermage\Core\Model\Request;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

class Block implements HttpGetActionInterface
{
    final public const string LAYOUT_HANDLE = 'hypermage_block_block';

    private const int HTTP_BAD_REQUEST = 400;
    private const int HTTP_UNAUTHORIZED = 401;
    private const int HTTP_SERVER_ERROR = 500;

    public function __construct(
        private readonly ComponentFactory     $componentFactory,
        private readonly ComponentResult      $componentResult,
        private readonly LoggerInterface      $logger,
        private readonly PageFactory          $pageFactory,
        private readonly PageProvider         $pageProvider,
        private readonly Request              $request,
        private readonly ResultFactory        $resultFactory,
    )
    {
    }

    public function execute(): ResultInterface|ResponseInterface
    {
        try {
            $page = $this->pageFactory->create(false, ['isIsolated' => true]);
            $page->getLayout()->getUpdate()->removeHandle(self::LAYOUT_HANDLE);

            if (!$this->request->validate()) {
                $this->logger->warning('Unauthorized request');
                return $this->returnStatusCode(self::HTTP_UNAUTHORIZED);
            }

            if (!$componentData = $this->request->getData()) {
                $this->logger->error('Invalid request data');
                return $this->returnStatusCode(self::HTTP_BAD_REQUEST);
            }

            $requestedPage = $this->pageProvider->getRequestedPage($componentData->getLayoutHandles());
            $requestedBlock = $this->pageProvider->getRequestedBlock($requestedPage, $componentData->getName());

            if (!$requestedBlock) {
                $this->logger->error('Block not found');
                return $this->returnStatusCode(self::HTTP_BAD_REQUEST);
            }

            $this->componentFactory->fromBlock($requestedBlock, $componentData);
            $this->componentResult->setBlock($requestedBlock);

            return $this->componentResult;
        } catch (Exception $e) {
            $this->logger->error('Error executing block controller', ['exception' => $e]);
            return $this->returnStatusCode(self::HTTP_SERVER_ERROR);
        }
    }

    private function returnStatusCode(int $code): mixed
    {
        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $response->setHttpResponseCode($code);

        return $response;
    }
}
