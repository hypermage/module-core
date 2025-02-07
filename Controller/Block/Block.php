<?php

declare(strict_types=1);

namespace Hypermage\Core\Controller\Block;

use Exception;
use Hypermage\Core\Model\BlockFactory;
use Hypermage\Core\Model\BlockResultFactory;
use Hypermage\Core\Model\BlockSpecificationFactory;
use Hypermage\Core\Model\Signature;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Element\BlockInterface;

class Block implements HttpGetActionInterface
{
    public function __construct(
        private readonly BlockFactory              $blockFactory,
        private readonly BlockSpecificationFactory $blockSpecificationFactory,
        private readonly BlockResultFactory        $blockResultFactory,
        private readonly RequestInterface          $request,
        private readonly Signature                 $signature,
    )
    {
    }

    /**
     * @throws Exception
     */
    public function execute(): ResultInterface|ResponseInterface
    {
        if (!$this->validateRequest()) {
            throw new Exception('Invalid request');
        }

        $block = $this->getBlock();
        if (!$block) {
            throw new Exception('Block not found');
        }

        return $this->blockResultFactory->create($block);
    }

    private function validateRequest(): bool
    {
        $params = $this->request->getParams();

        if (!isset($params['signature'])) {
            return false;
        }

        return $this->signature->validate($params);
    }

    private function getBlock(): ?BlockInterface
    {
        $blockSpecification = $this->blockSpecificationFactory->fromRequest($this->request);
        return $this->blockFactory->create($blockSpecification);
    }
}
