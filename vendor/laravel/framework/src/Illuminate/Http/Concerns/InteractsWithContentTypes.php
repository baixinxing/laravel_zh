<?php

namespace Illuminate\Http\Concerns;

use Illuminate\Support\Str;

trait InteractsWithContentTypes
{
    /**
     * 确定给定的内容类型是否匹配。
     *
     * @param  string  $actual 实际
     * @param  string  $type 类型
     * @return bool
     */
    public static function matchesType($actual, $type)
    {
        if ($actual === $type) {
            return true;
        }

        $split = explode('/', $actual);

        return isset($split[1]) && preg_match('#'.preg_quote($split[0], '#').'/.+\+'.preg_quote($split[1], '#').'#', $type);
    }

    /**
     * 确定请求是否正在发送JSON。
     *
     * @return bool
     */
    public function isJson()
    {
        return Str::contains($this->header('CONTENT_TYPE'), ['/json', '+json']);
    }

    /**
     * 确定当前请求是否可能期望JSON响应。
     *
     * @return bool
     */
    public function expectsJson()
    {
        return ($this->ajax() && ! $this->pjax()) || $this->wantsJson();
    }

    /**
     * 确定当前请求是否要求返回JSON。
     *
     * @return bool
     */
    public function wantsJson()
    {
        $acceptable = $this->getAcceptableContentTypes();

        return isset($acceptable[0]) && Str::contains($acceptable[0], ['/json', '+json']);
    }

    /**
     * Determines whether the current requests accepts a given content type.
     *
     * @param  string|array  $contentTypes
     * @return bool
     */
    public function accepts($contentTypes)
    {
        $accepts = $this->getAcceptableContentTypes();

        if (count($accepts) === 0) {
            return true;
        }

        $types = (array) $contentTypes;

        foreach ($accepts as $accept) {
            if ($accept === '*/*' || $accept === '*') {
                return true;
            }

            foreach ($types as $type) {
                if ($this->matchesType($accept, $type) || $accept === strtok($type, '/').'/*') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Return the most suitable content type from the given array based on content negotiation.
     *
     * @param  string|array  $contentTypes
     * @return string|null
     */
    public function prefers($contentTypes)
    {
        $accepts = $this->getAcceptableContentTypes();

        $contentTypes = (array) $contentTypes;

        foreach ($accepts as $accept) {
            if (in_array($accept, ['*/*', '*'])) {
                return $contentTypes[0];
            }

            foreach ($contentTypes as $contentType) {
                $type = $contentType;

                if (! is_null($mimeType = $this->getMimeType($contentType))) {
                    $type = $mimeType;
                }

                if ($this->matchesType($type, $accept) || $accept === strtok($type, '/').'/*') {
                    return $contentType;
                }
            }
        }
    }

    /**
     * 确定请求是否接受JSON
     *
     * @return bool
     */
    public function acceptsJson()
    {
        return $this->accepts('application/json');
    }

    /**
     * 确定请求是否接受HTML
     *
     * @return bool
     */
    public function acceptsHtml()
    {
        return $this->accepts('text/html');
    }

    /**
     * 获取响应中预期的数据格式。
     *
     * @param  string  $default
     * @return string
     */
    public function format($default = 'html')
    {
        foreach ($this->getAcceptableContentTypes() as $type) {
            if ($format = $this->getFormat($type)) {
                return $format;
            }
        }

        return $default;
    }
}
