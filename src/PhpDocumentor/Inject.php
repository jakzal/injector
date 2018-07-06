<?php
declare(strict_types=1);

namespace Zalas\Injector\PhpDocumentor;

use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod;
use phpDocumentor\Reflection\DocBlock\Tags\Formatter;

final class Inject implements Tag, StaticMethod
{
    /**
     * @var string
     */
    private $serviceId;

    public function __construct(string $serviceId)
    {
        $this->serviceId = $serviceId;
    }

    public function __toString(): string
    {
        return $this->serviceId;
    }

    public function getName(): string
    {
        return 'inject';
    }

    public static function create($body): self
    {
        return new self($body);
    }

    public function render(Formatter $formatter = null)
    {
        if ($formatter instanceof Formatter) {
            return $formatter->format($this);
        }

        return \sprintf('@%s %s', $this->getName(), $this->serviceId);
    }
}
