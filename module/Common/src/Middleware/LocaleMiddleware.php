<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Common\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as DelegateInterface;
use Zend\I18n\Translator\Translator;

class LocaleMiddleware implements MiddlewareInterface
{
    /**
     * @var Translator
     */
    private $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }



    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param Request $request
     * @param DelegateInterface $delegate
     *
     * @return Response
     */
    public function process(Request $request, DelegateInterface $delegate): Response
    {
        if (! $request->hasHeader('Accept-Language')) {
            return $delegate->handle($request);
        }

        $locale = $request->getHeaderLine('Accept-Language');
        $this->translator->setLocale($this->normalizeLocale($locale));
        return $delegate->handle($request);
    }

    /**
     * @param string $locale
     * @return string
     */
    protected function normalizeLocale($locale)
    {
        $parts = explode('_', $locale);
        if (count($parts) > 1) {
            return $parts[0];
        }

        $parts = explode('-', $locale);
        if (count($parts) > 1) {
            return $parts[0];
        }

        return $locale;
    }
}
