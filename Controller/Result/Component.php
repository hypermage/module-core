<?php

declare(strict_types=1);

namespace Hypermage\Core\Controller\Result;

use Magento\Framework\Controller\AbstractResult;
use Magento\Framework\View\Element\BlockInterface;

use Magento\Framework\App\Response\HttpInterface as HttpResponseInterface;

class Component extends AbstractResult
{
    protected BlockInterface $block;

    public function setBlock(BlockInterface $block): self
    {
        $this->block = $block;

        return $this;
    }

    protected function render(HttpResponseInterface $response): self
    {
        $this->setHeader('Content-Type', 'text/html');

        $response->setBody($this->block->toHtml());

        return $this;
    }
}
