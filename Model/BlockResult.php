<?php

declare(strict_types=1);

namespace Hypermage\Core\Model;

use Magento\Framework\App\Response\HttpInterface as HttpResponseInterface;
use Magento\Framework\Controller\AbstractResult;
use Magento\Framework\View\Element\BlockInterface;

class BlockResult extends AbstractResult
{
    public function __construct(
        readonly private BlockInterface $block,
    )
    {
    }

    protected function render(HttpResponseInterface $response): self
    {
        $this->setHeader('Content-Type', 'text/html');

        // $this->loadParentBlocks($this->block);

        $response->setBody($this->block->toHtml());

        return $this;
    }

    /**
     * Adds compatibility to request child blocks. Ex: `$htmx->getHxParams($block->getChildBlock('name'))`
     *
     * For some reason it is necessary to render block parents in case of requesting a child block.
     * If we dont render all the parents it will miss data.
     */
    protected function loadParentBlocks(BlockInterface $block): void
    {
        if ($block->getParentBlock()) {
            $this->loadParentBlocks($block->getParentBlock());
        }
    }
}
