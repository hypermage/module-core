<?php

declare(strict_types=1);

namespace Hypermage\Core\Model;

use Magento\Framework\View\Element\BlockInterface;

enum Method: string {
    case GET = 'GET';
    case POST = 'POST';
}

class HxParams
{
    final public const string COMPONENT_ENDPOINT = '/hypermage/component/component';

    private Method $method = Method::GET;
    private string $hxSwap = '';
    private string $hxTarget = '';
    private array $hxTriggers = [];
    private array $hxVals = [];

    public function __construct(
        private readonly BlockInterface       $block,
        private readonly ComponentDataFactory $componentDataFactory,
        private readonly Signature            $signature,
    )
    {
    }

    public function __toString(): string
    {
        $params = [
            $this->method === Method::GET ? $this->getHxGet() : $this->getHxPost(),
            $this->getHxSwap(),
            $this->getHxTriggers(),
            $this->getHxTarget(),
        ];

        return implode(' ', array_filter($params));
    }

    public function setMethod(Method $method): void
    {
        $this->method = $method;
    }

    public function getRequestUrl(): string
    {
        $componentData = $this->componentDataFactory->fromBlock($this->block);
        $signature = $this->signature->sign($componentData->toArray());

        return self::COMPONENT_ENDPOINT . '?' . $componentData . '&signature=' . $signature;
    }

    public function getHxGet(): string
    {
        $requestUrl = $this->getRequestUrl();

        return "hx-get=\"$requestUrl\"";
    }

    public function getHxPost(): string
    {
        $requestUrl = $this->getRequestUrl();

        return "hx-post=\"$requestUrl\"";
    }

    public function setSwap(string $swap): void
    {
        $this->hxSwap = $swap;
    }

    public function getSwap(): string
    {
        return $this->hxSwap;
    }

    public function getHxSwap(): string
    {
        if (empty($this->hxSwap)) {
            return '';
        }

        return "hx-swap=\"$this->hxSwap\"";
    }

    public function addTrigger(string $trigger): void
    {
        $this->hxTriggers[] = $trigger;
    }

    public function getTriggers(): array
    {
        return $this->hxTriggers;
    }

    public function getHxTriggers(): string
    {
        if (empty($this->hxTriggers)) {
            return '';
        }

        $triggers = implode(', ', $this->hxTriggers);

        return "hx-trigger=\"$triggers\"";
    }

    public function setTarget(string $target): void
    {
        $this->hxTarget = $target;
    }

    public function getTarget(): string
    {
        return $this->hxTarget;
    }

    public function getHxTarget(): string
    {
        if (empty($this->hxTarget)) {
            return '';
        }

        return "hx-target=\"$this->hxTarget\"";
    }

    public function addVal(string $name, string $value): void
    {
        $this->hxVals[$name] = $value;
    }

    public function removeVal(string $name): void
    {
        unset($this->hxVals[$name]);
    }

    public function getVals(): array
    {
        return $this->hxVals;
    }

    public function getHxVals(): string
    {
        if (empty($this->hxVals)) {
            return '';
        }

        $vals = [];
        foreach ($this->hxVals as $name => $value) {
            $vals[] = "$name=$value";
        }

        return 'hx-vals="' . implode(' ', $vals) . '"';
    }
}
