<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

class ClassExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('class_name', $this->getClassName(...)),
        ];
    }

    public function getTests(): array
    {
        return [
            new TwigTest('instanceof', $this->isInstanceOf(...)),
        ];
    }

    public function getClassName(object $object): string
    {
        $reflection = new \ReflectionClass($object);

        return $reflection->getShortName();
    }

    public function isInstanceOf(object $object, string $class): bool
    {
        return $object instanceof $class;
    }
}
