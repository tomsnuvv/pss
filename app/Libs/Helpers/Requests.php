<?php

namespace App\Libs\Helpers;

use voku\helper\HtmlMin;
use App\Models\RequestContent;

/**
 * Requests Helper class.
 *
 * @todo If HTML minify fails, might indicate that the website is broken. Maybe we should create a finding?
 */
class Requests
{
    /**
     * Ignored path extensions.
     *
     * @var array
     */
    const EXTENSIONS_IGNORED = [
        # Web files
        'css', 'js',
        # Images
        'png', 'jpeg', 'jpg', 'bmp', 'svg', 'gif', 'ico',
        # Fonts
        'woff2', 'woff', 'ttf', 'eot', 'otf',
    ];

    /**
     * Extensions allowed to store it's content.
     *
     * @var array
     */
    const EXTENSIONS_ALLOWED_CONTENT = [
        'txt', 'html', 'php', 'asp', 'do', 'aspx',
    ];

    /**
     * Store a Request model.
     *
     * @param  \App\Models\Website       $website
     * @param  string                    $url     Url or path
     * @param  string                    $method
     * @param  int                       $status
     * @param  string                    $body
     * @return \App\Models\Request|void
     */
    public static function storeRequest($website, $url, $method = null, $status = null, $body = null)
    {
        $parse = parse_url($url);
        $path = isset($parse['path']) ? $parse['path'] : '';
        if ($path == '') {
            $path = '/';
        }

        $path = self::clean($path);

        if (!$method) {
            $method = 'GET';
        } else {
            $method = strtoupper($method);
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, self::EXTENSIONS_IGNORED)) {
            return;
        }

        if (strlen($path)>191) {
            return;
        }

        $request = $website->requests()->firstOrNew(['method' => $method, 'path' => $path]);
        if (isset($parse['query']) && $parse['query']) {
            $request->parameters = self::clean($parse['query']);
        }
        $request->status = $status;
        if ($body && (!$ext || in_array($ext, self::EXTENSIONS_ALLOWED_CONTENT))) {
            $content = self::storeContent($body);
            if ($content) {
                $request->content()->associate($content);
            }
        }
        $request->save();

        return $request;
    }

    /**
     * Store a request content.
     *
     * @param  string $content
     * @return \App\Models\RequestContent
     */
    public static function storeContent($body)
    {
        $body = self::clean($body);

        $htmlMin = new HtmlMin();
        try {
            $body = $htmlMin->minify($body);
        } catch (\Exception $e) {
            // Do nothing. Some websites contain HTML errors, or are not HTML at all
        }
        $hash = sha1($body);

        $content = RequestContent::firstOrNew(['hash' => $hash]);
        $content->body = $body;
        $content->save();

        return $content;
    }

    /**
     * Removes content not suported by the DB.
     *
     * @param string $data
     * @return string
     */
    private static function clean($data)
    {
        // Remove 4(-and-more)-byte characters
        return preg_replace('/[\x{10000}-\x{10FFFF}]/u', "\xEF\xBF\xBD", $data);
    }
}
