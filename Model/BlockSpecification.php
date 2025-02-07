<?php

declare(strict_types=1);

namespace Hypermage\Core\Model;

readonly class BlockSpecification
{
    public function __construct(
        protected object $class,
        protected string $template,

        protected string $fullActionName,
        protected array  $layoutHandles,
        protected string $nameInLayout,

        protected array  $objects = [],
        protected array  $data = [],
    )
    {
    }

    public function __toString(): string
    {
        return http_build_query($this->toArray());
    }

    public function toArray(): array
    {
        return [
            'class' => get_class($this->getClass()),
            'template' => $this->getTemplate(),
            'fullActionName' => $this->getFullActionName(),
            'layoutHandles' => $this->getLayoutHandles(),
            'nameInLayout' => $this->getNameInLayout(),
            'objects' => $this->getObjects(),
            'data' => $this->getData(),
        ];
    }

    public function getClass(): object
    {
        return $this->class;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getFullActionName(): string
    {
        return $this->fullActionName;
    }

    public function getLayoutHandles(): array
    {
        return $this->layoutHandles;
    }

    public function getNameInLayout(): string
    {
        return $this->nameInLayout;
    }

    public function getObjects(): array
    {
        return $this->objects;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
