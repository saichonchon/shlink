<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Core\Transformer;

use Shlinkio\Shlink\Common\Rest\DataTransformerInterface;
use Shlinkio\Shlink\Core\Entity\ShortUrl;
use Shlinkio\Shlink\Core\Entity\Tag;

class ShortUrlDataTransformer implements DataTransformerInterface
{
    /**
     * @var array
     */
    private $domainConfig;

    public function __construct(array $domainConfig)
    {
        $this->domainConfig = $domainConfig;
    }

    /**
     * @param ShortUrl $value
     * @return array
     */
    public function transform($value): array
    {
        $dateCreated = $value->getDateCreated();
        $longUrl = $value->getLongUrl();
        $shortCode = $value->getShortCode();

        return [
            'shortCode' => $shortCode,
            'shortUrl' => \sprintf(
                '%s://%s/%s',
                $this->domainConfig['schema'] ?? 'http',
                $this->domainConfig['hostname'] ?? '',
                $shortCode
            ),
            'longUrl' => $longUrl,
            'dateCreated' => $dateCreated !== null ? $dateCreated->format(\DateTime::ATOM) : null,
            'visitsCount' => $value->getVisitsCount(),
            'tags' => \array_map([$this, 'serializeTag'], $value->getTags()->toArray()),

            // Deprecated
            'originalUrl' => $longUrl,
        ];
    }

    private function serializeTag(Tag $tag): string
    {
        return $tag->getName();
    }
}
