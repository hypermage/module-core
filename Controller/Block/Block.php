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

/**
 * Simple controller to fetch a block without performing any other mutations.
 * Perfect for fetching static components.
 * However, if you have an create, update or delete action, you should create a separate controller-
 * that performs the mutation and returns the updated block.
 * You can use this class as an example to create such a controller.
 */
class Block implements HttpGetActionInterface
{
    public function __construct(
        private readonly BlockFactory              $blockFactory,
        private readonly BlockResultFactory        $blockResultFactory,
        private readonly BlockSpecificationFactory $blockSpecificationFactory,
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
        $params = $this->request->getParams();

        if (!$this->validateParams($params)) {
            throw new Exception('Invalid request');
        }

        if (!isset($params['block_specification'])) {
            throw new Exception('Block specification is required');
        }

        $block = $this->getBlock($params['block_specification']);
        if (!$block) {
            throw new Exception('Block not found');
        }

        if ($blockAdditionalData = $params['block_additional_data'] ?? null) {
            $block->addData($blockAdditionalData);
        }

        return $this->blockResultFactory->create($block);
    }

    private function validateParams(array $params): bool
    {
        return $this->signature->validate($params);
    }

    private function getBlock(array $blockSpecificationData): ?BlockInterface
    {
        $blockSpecification = $this->blockSpecificationFactory->fromArray($blockSpecificationData);
        return $this->blockFactory->create($blockSpecification);
    }
}
