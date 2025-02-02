<?php

declare(strict_types=1);

namespace Hypermage\Core\Model;

readonly class ComponentData
{
    public function __construct(
        protected object $class,
        protected string $template,

        protected string $fullActionName,
        protected array  $layoutHandles,
        protected string $name,

        protected array  $objects = [],
        protected array  $data = []
    )
    {
    }

    public function __toString(): string
    {
        return http_build_query([
            'class' => get_class($this->class),
            'template' => $this->template,
            'fullActionName' => $this->fullActionName,
            'layoutHandles' => $this->layoutHandles,
            'name' => $this->name,
            'objects' => $this->objects,
            'data' => $this->data
        ]);
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getObjects(): array
    {
        return $this->objects;
    }

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * We must add a signature to the request to prevent client side from manipulating the request
     * Using hash_hmac with sha256 algorithm we can ensure integrity of the request
     */
    public function getSignature(): string
    {
        return hash_hmac('sha256', (string)$this, 'secret');
    }
}
