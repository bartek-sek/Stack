<?php

declare(strict_types=1);

namespace Sylius\TwigHooks\Twig\Node;

use Sylius\TwigHooks\Twig\Runtime\HooksRuntime;
use Twig\Attribute\YieldReady;
use Twig\Compiler;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Node;

#[YieldReady]
final class HookNode extends Node
{
    public function __construct (
        Node $name,
        ?Node $context,
        bool $only,
        int $lineno,
        string $tag = null
    ) {
        parent::__construct(
            [
                'name' => $name,
                'hook_level_context' => $context ?? new ArrayExpression([], $lineno),
            ],
            [
                'only' => $only,
            ],
            $lineno,
            $tag,
        );
    }

    public function compile(Compiler $compiler): void
    {
        $compiler->addDebugInfo($this);

        $compiler->raw(sprintf(
            '$hooksRuntime = $this->env->getRuntime(\'%s\');',
            HooksRuntime::class,
        ))->raw("\n");

        $compiler->raw('yield $hooksRuntime->renderHook(');
        $compiler->subcompile($this->getNode('name'));
        $compiler->raw(', ');
        $compiler->subcompile($this->getNode('hook_level_context'));
        $compiler->raw(', ');
        $compiler->raw('$context["hookable_metadata"] ?? null');
        $compiler->raw(', ');
        $compiler->raw($this->getAttribute('only') ? 'true' : 'false');
        $compiler->raw(");\n");
    }
}
