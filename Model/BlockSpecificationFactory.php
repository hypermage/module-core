<?php

declare(strict_types=1);

namespace Hypermage\Core\Model;

use InvalidArgumentException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\BlockInterface;

readonly class BlockSpecificationFactory
{
    final public const array REQUIRED_DATA_KEYS = [
        'class',
        'template',
        'fullActionName',
        'layoutHandles',
        'nameInLayout',
        'objects',
        'data'
    ];

    final public const array IGNORED_DATA_KEYS = ['jsLayout'];

    public function __construct(
        private ObjectManagerInterface $objectManger,
    )
    {
    }

    public function fromBlock(BlockInterface $block): BlockSpecification
    {
        return new BlockSpecification(
            $block,
            $block->getTemplate(),

            $this->getFullActionName($block),
            $this->getLayoutHandles($block),
            $block->getNameInLayout(),

            $this->getObjects($block),
            $this->getFilteredData($block)
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    public function fromArray(array $data): BlockSpecification
    {
        $this->parseArray($data);

        $this->validateArray($data);

        return new BlockSpecification(
            $data['class'],
            $data['template'],

            $data['fullActionName'],
            $data['layoutHandles'],
            $data['nameInLayout'],

            $data['objects'],
            $data['data']
        );
    }

    private function parseArray(array &$data): void
    {
        if (is_string($data['class'])) {
            $data['class'] = $this->objectManger->create($data['class']);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function validateArray(array $data): void
    {
        foreach (self::REQUIRED_DATA_KEYS as $key) {
            if (!array_key_exists($key, $data)) {
                throw new InvalidArgumentException("Missing required key: {$key}");
            }
        }

        if (!is_object($data['class'])) {
            throw new InvalidArgumentException("The 'class' value must be an object.");
        }
        if (!get_class($data['class']) === BlockInterface::class) {
            throw new InvalidArgumentException("The 'class' value must be an instance of " . BlockInterface::class);
        }
        if (!is_string($data['template'])) {
            throw new InvalidArgumentException("The 'template' value must be a string.");
        }
        if (!is_string($data['fullActionName'])) {
            throw new InvalidArgumentException("The 'fullActionName' value must be a string.");
        }
        if (!is_array($data['layoutHandles'])) {
            throw new InvalidArgumentException("The 'layoutHandles' value must be an array.");
        }
        if (!is_string($data['nameInLayout'])) {
            throw new InvalidArgumentException("The 'nameInLayout' value must be a string.");
        }
        if (!is_array($data['objects'])) {
            throw new InvalidArgumentException("The 'objects' value must be an array.");
        }
        if (!is_array($data['data'])) {
            throw new InvalidArgumentException("The 'data' value must be an array.");
        }

        foreach ($data['layoutHandles'] as $handle) {
            if (!is_string($handle) || trim($handle) === '') {
                throw new InvalidArgumentException("Each item in 'layoutHandles' must be a non-empty string.");
            }
        }
    }

    private function getFullActionName(BlockInterface $block): string
    {
        return $block->getRequest()->getFullActionName();
    }

    private function getLayoutHandles(BlockInterface $block): array
    {
        return $block->getData('layoutHandles') ?? $block->getLayout()->getUpdate()->getHandles();
    }

    private function getObjects(BlockInterface $block): array
    {
        $objects = [];

        foreach ($block->getData() as $key => $value) {
            if (is_object($value)) {
                $objects[$key] = get_class($value);
            }
        }

        return $objects;
    }

    private function getFilteredData(BlockInterface $block): array
    {
        $data = [];

        foreach ($block->getData() as $key => $value) {
            if (is_object($value)) {
                continue;
            }

            if (in_array($key, self::IGNORED_DATA_KEYS)) {
                continue;
            }

            $data[$key] = $value;
        }

        return $data;
    }
}
