<?php

namespace Botble\JobBoard\Supports\JobImport\Sources;

use Botble\JobBoard\Supports\JobImport\RemoteJobRecord;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\CssSelector\CssSelectorConverter;

class HtmlRemoteJobSource extends AbstractRemoteJobSource
{
    protected CssSelectorConverter $converter;

    public function __construct(string $key, array $config)
    {
        parent::__construct($key, $config);

        $this->converter = new CssSelectorConverter();
    }

    public function fetch(): array
    {
        $records = [];

        foreach ((array) Arr::get($this->config, 'list_urls', []) as $listUrl) {
            $listHtml = $this->client()
                ->accept('text/html,application/xhtml+xml')
                ->get($listUrl)
                ->throw()
                ->body();

            foreach ($this->extractListingUrls($listHtml, $listUrl) as $detailUrl) {
                $detailHtml = $this->client()
                    ->accept('text/html,application/xhtml+xml')
                    ->get($detailUrl)
                    ->throw()
                    ->body();

                $record = $this->mapDetailPage($detailHtml, $detailUrl);

                if ($record) {
                    $records[$record->externalId] = $record;
                }
            }
        }

        return array_values($records);
    }

    protected function extractListingUrls(string $html, string $baseUrl): array
    {
        $document = $this->makeDocument($html);
        $xpath = new DOMXPath($document);
        $selector = (string) Arr::get($this->config, 'listing_link_selector', 'a[href]');
        $links = [];

        foreach ($xpath->query($this->converter->toXPath($selector)) ?: [] as $node) {
            if (! $node instanceof DOMElement || ! $node->hasAttribute('href')) {
                continue;
            }

            $url = $this->absoluteUrl($node->getAttribute('href'), $this->baseUrl() ?: $baseUrl);

            if (! $this->matchesPatterns($url, (array) Arr::get($this->config, 'listing_link_patterns', []))) {
                continue;
            }

            $links[] = $url;
        }

        return array_values(array_unique($links));
    }

    protected function mapDetailPage(string $html, string $detailUrl): ?RemoteJobRecord
    {
        $document = $this->makeDocument($html);
        $xpath = new DOMXPath($document);
        $jsonLd = $this->extractJobPostingJsonLd($xpath);

        $title = $this->selectorText($xpath, Arr::get($this->config, 'title_selector')) ?: $this->normalizeText(Arr::get($jsonLd, 'title'));
        $content = $this->selectorHtml($xpath, Arr::get($this->config, 'description_selector'))
            ?: $this->normalizeHtml(Arr::get($jsonLd, 'description'));
        $description = $this->normalizeText($content);
        $companyName = $this->selectorText($xpath, Arr::get($this->config, 'company_name_selector'))
            ?: $this->normalizeText(data_get($jsonLd, 'hiringOrganization.name'))
            ?: $this->label();
        $location = $this->selectorText($xpath, Arr::get($this->config, 'location_selector'))
            ?: $this->normalizeText(data_get($jsonLd, 'jobLocation.address.addressLocality'));
        $applyUrl = $this->selectorAttribute($xpath, Arr::get($this->config, 'apply_url_selector'), 'href')
            ?: Arr::get($jsonLd, 'url')
            ?: $detailUrl;
        $employmentType = $this->normalizeList(Arr::get($jsonLd, 'employmentType', []));
        $externalId = $this->normalizeText(Arr::get($jsonLd, 'identifier.value'))
            ?: $this->normalizeText(Arr::get($jsonLd, 'identifier.name'))
            ?: md5($detailUrl);

        if (! $title || ! $content) {
            return null;
        }

        return RemoteJobRecord::fromArray([
            'external_id' => $externalId,
            'title' => $title,
            'url' => $detailUrl,
            'apply_url' => $this->absoluteUrl((string) $applyUrl, $detailUrl),
            'description' => $description,
            'content' => $content,
            'company' => [
                'name' => $companyName,
                'website' => data_get($jsonLd, 'hiringOrganization.sameAs'),
                'logo' => data_get($jsonLd, 'hiringOrganization.logo'),
            ],
            'categories' => array_merge(
                $this->normalizeList(Arr::get($this->config, 'categories', [])),
                $this->normalizeList($this->selectorText($xpath, Arr::get($this->config, 'category_selector')))
            ),
            'types' => array_merge($employmentType, $this->sourceTypes()),
            'tags' => $this->sourceTags(),
            'location' => $location,
            'country' => data_get($jsonLd, 'jobLocation.address.addressCountry') ?: Arr::get($this->config, 'country'),
            'state' => data_get($jsonLd, 'jobLocation.address.addressRegion'),
            'city' => data_get($jsonLd, 'jobLocation.address.addressLocality'),
            'address' => data_get($jsonLd, 'jobLocation.address.streetAddress'),
            'published_at' => Arr::get($jsonLd, 'datePosted'),
            'closing_at' => Arr::get($jsonLd, 'validThrough'),
            'expires_at' => Arr::get($jsonLd, 'validThrough'),
            'source_updated_at' => Arr::get($jsonLd, 'datePosted'),
            'metadata' => [
                'source' => 'html',
                'raw' => $jsonLd,
            ],
        ]);
    }

    protected function extractJobPostingJsonLd(DOMXPath $xpath): array
    {
        foreach ($xpath->query('//script[@type="application/ld+json"]') ?: [] as $script) {
            $decoded = json_decode((string) $script->textContent, true);

            if (! is_array($decoded)) {
                continue;
            }

            $candidates = Arr::isAssoc($decoded) ? [$decoded] : $decoded;

            foreach ($candidates as $candidate) {
                if (data_get($candidate, '@type') === 'JobPosting') {
                    return $candidate;
                }
            }
        }

        return [];
    }

    protected function selectorText(DOMXPath $xpath, ?string $selector): ?string
    {
        if (! $selector) {
            return null;
        }

        foreach (explode(',', $selector) as $candidate) {
            $candidate = trim($candidate);

            if (! $candidate) {
                continue;
            }

            $node = $xpath->query($this->converter->toXPath($candidate))?->item(0);

            if ($node) {
                return $this->normalizeText($node->textContent);
            }
        }

        return null;
    }

    protected function selectorHtml(DOMXPath $xpath, ?string $selector): ?string
    {
        if (! $selector) {
            return null;
        }

        foreach (explode(',', $selector) as $candidate) {
            $candidate = trim($candidate);

            if (! $candidate) {
                continue;
            }

            $node = $xpath->query($this->converter->toXPath($candidate))?->item(0);

            if (! $node) {
                continue;
            }

            $html = '';

            foreach ($node->childNodes as $child) {
                $html .= $node->ownerDocument->saveHTML($child);
            }

            $html = $this->normalizeHtml($html ?: $node->ownerDocument->saveHTML($node));

            if ($html) {
                return $html;
            }
        }

        return null;
    }

    protected function selectorAttribute(DOMXPath $xpath, ?string $selector, string $attribute): ?string
    {
        if (! $selector) {
            return null;
        }

        foreach (explode(',', $selector) as $candidate) {
            $candidate = trim($candidate);

            if (! $candidate) {
                continue;
            }

            $node = $xpath->query($this->converter->toXPath($candidate))?->item(0);

            if ($node instanceof DOMElement && $node->hasAttribute($attribute)) {
                return $this->normalizeText($node->getAttribute($attribute));
            }
        }

        return null;
    }

    protected function matchesPatterns(string $url, array $patterns): bool
    {
        if ($patterns === []) {
            return true;
        }

        foreach ($patterns as $pattern) {
            if (Str::startsWith((string) $pattern, '#') && preg_match($pattern, $url)) {
                return true;
            }

            if (Str::contains($url, (string) $pattern)) {
                return true;
            }
        }

        return false;
    }

    protected function makeDocument(string $html): DOMDocument
    {
        $document = new DOMDocument();

        libxml_use_internal_errors(true);
        $document->loadHTML($html);
        libxml_clear_errors();

        return $document;
    }
}
