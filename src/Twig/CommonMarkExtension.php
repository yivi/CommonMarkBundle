<?php

declare(strict_types=1);

namespace Yivoff\CommonmarkBundle\Twig;

use InvalidArgumentException;
use League\CommonMark\CommonMarkConverter;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Yivoff\CommonmarkBundle\YivoffCommonmarkBundle;
use function array_key_first;
use function array_keys;
use function count;
use function implode;
use function sprintf;

/**
 * @internal
 */
class CommonMarkExtension extends AbstractExtension
{
    public function __construct(private ServiceLocator $serviceLocator)
    {
    }

    /** @return TwigFilter[] */
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'commonmark',
                [$this, 'convertMarkdown'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function convertMarkdown(?string $markdown, ?string $converterName = null): string
    {
        if (null === $markdown || '' === $markdown) {
            return '';
        }

        if (null === $converterName && 1 === count($this->serviceLocator->getProvidedServices())) {
            $converterName = array_key_first($this->serviceLocator->getProvidedServices());

            if (null === $converterName) {
                return '';
            }

            $converter = $this->serviceLocator->get($converterName);
            if (!$converter instanceof CommonMarkConverter) {
                return '';
            }

            return $converter->convertToHtml($markdown)->getContent();
        }

        $converterName = YivoffCommonmarkBundle::BUNDLE_PREFIX.'.converters.'.$converterName;
        if (false === $this->serviceLocator->has($converterName)) {
            $message             = 'The "%s" converter does not exist. You need to use one of these: %s';
            $availableConverters = implode(', ', array_keys($this->serviceLocator->getProvidedServices()));

            throw new InvalidArgumentException(sprintf($message, $converterName, $availableConverters));
        }

        /** @var CommonMarkConverter $converter */
        $converter = $this->serviceLocator->get($converterName);

        return $converter->convertToHtml($markdown)->getContent();
    }
}
