<?php

declare(strict_types=1);

namespace Sylius\TwigHooks\Twig\Runtime;

use Sylius\TwigHooks\Hookable\AbstractHookable;
use Sylius\TwigHooks\Hookable\Renderer\HookableRendererInterface;
use Sylius\TwigHooks\Profiler\Profile;
use Sylius\TwigHooks\Registry\HookablesRegistry;
use Symfony\Component\Stopwatch\Stopwatch;
use Twig\Extension\RuntimeExtensionInterface;

final class HooksRuntime implements RuntimeExtensionInterface
{
    public const HOOKABLE_CONFIGURATION_PARAMETER = 'hookable_configuration';

    public const HOOKABLE_DATA_PARAMETER = 'hookable_data';

    private ?Stopwatch $stopwatch = null;

    public function __construct (
        private HookablesRegistry $hookablesRegistry,
        private HookableRendererInterface $compositeHookableRenderer,
        private ?Profile $profile,
        bool $debug,
    ) {
        if (class_exists(Stopwatch::class) && true === $debug) {
            $this->stopwatch = new Stopwatch();
        }
    }

    /**
     * @param array{hookable_data?: array<string, string>} $context
     * @return array<string, string>
     */
    public function getHookableData(array $context): array
    {
        return $context[self::HOOKABLE_DATA_PARAMETER] ?? [];
    }

    /**
     * @param array{hookable_configuration?: array<string, string>} $context
     * @return array<string, string>
     */
    public function getHookableConfiguration(array $context): array
    {
        return $context[self::HOOKABLE_CONFIGURATION_PARAMETER] ?? [];
    }

    /**
     * @param array<string> $hooksNames
     * @param array<string, mixed> $data
     */
    public function renderHook(array $hooksNames, array $data = []): string
    {
        $this->profile?->registerHookStart($hooksNames);
        $this->stopwatch?->start(md5(serialize($hooksNames)));

        $result = [];
        $enabledHookables = $this->hookablesRegistry->getEnabledFor($hooksNames);

        foreach ($enabledHookables as $hookable) {
            $result[] = $this->renderHookable($hookable, $data);
        }

        $this->profile?->registerHookEnd(
            $this->stopwatch?->stop(md5(serialize($hooksNames)))->getDuration(),
        );

        return implode(PHP_EOL, $result);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function renderHookable(AbstractHookable $hookable, array $data = []): string
    {
        $this->profile?->registerHookableRenderStart($hookable);
        $this->stopwatch?->start($hookable->getId());

        $result = $this->compositeHookableRenderer->render($hookable, $data);

        $this->profile?->registerHookableRenderEnd(
            $this->stopwatch?->stop($hookable->getId())->getDuration(),
        );

        return $result;
    }
}
