<?php

declare(strict_types=1);

namespace Hypermage\Core\Model;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\BlockInterface;

readonly class ComponentFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager
    ) {
    }

    public function fromBlock(BlockInterface $input, ComponentData $componentData): Component
    {
        $block = clone $input;

        $block->setData($componentData->getData());

        foreach ($componentData->getObjects() as $key => $class) {
            $object = $this->objectManager->create($class);
            $block->setData($key, $object);
        }

        return new Component($block);
    }
}
