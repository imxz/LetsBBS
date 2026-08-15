<?php

namespace App\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

final class HtmlSanitizer
{
    private HTMLPurifier $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Core.Encoding', 'UTF-8');
        $config->set(
            'HTML.Allowed',
            'p[style],br,strong,b,em,i,u,s,blockquote,h2[style],h3[style],h4[style],ul,ol,li,a[href|title|target|rel],img[src|alt|width|height],div[style],span[style]',
        );
        $config->set('CSS.AllowedProperties', ['text-align']);
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true]);
        $config->set('Attr.AllowedFrameTargets', ['_blank']);
        $config->set('HTML.Nofollow', true);
        $config->set('HTML.TargetBlank', true);
        $config->set('Cache.SerializerPath', WRITEPATH . 'cache');
        $this->purifier = new HTMLPurifier($config);
    }

    public function clean(string $html): string
    {
        $clean = $this->purifier->purify($html);
        $clean =
            preg_replace_callback(
                '/<img\b[^>]*\bsrc="([^"]+)"[^>]*>/i',
                static function (array $match): string {
                    return preg_match(
                        '#^/uploads/editor/[0-9]{4}/[0-9]{2}/[a-f0-9]{40}\.(?:jpg|png|gif|webp)$#',
                        $match[1],
                    )
                        ? $match[0]
                        : '';
                },
                $clean,
            ) ?? '';
        return trim($clean);
    }
}
