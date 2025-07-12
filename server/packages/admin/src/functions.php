<?php
/**
 * Here is your custom functions.
 */

if (!function_exists('full_url')) {
    /**
     * 获取资源完整url地址；若安装了云存储或 config/upload.php 配置了cdn_url，则自动使用对应的cdn_url
     *
     * @param string      $relativeUrl 资源相对地址 不传入则获取域名
     * @param bool|string $domain      是否携带域名 或者直接传入域名
     * @param string      $default     默认值
     *
     * @return string
     */
    function full_url(string $relativeUrl = '', bool|string $domain = true, string $default = ''): string
    {
        // 从配置获取 CDN URL
        $cdnUrl = config('upload.cdn_url', '');

        // 如果 CDN URL 为空，则使用默认的主机名
        if (empty($cdnUrl)) {
            $cdnUrl = '//' . request()->host();
        }

        // 处理域名
        if ($domain === true) {
            $domain = $cdnUrl;
        } elseif ($domain === false) {
            $domain = '';
        }

        // 如果没有相对 URL，使用默认值
        $relativeUrl = $relativeUrl ?: $default;
        if (!$relativeUrl) {
            return $domain;
        }

        // 检查是否为绝对 URL 或数据 URL
        $isAbsoluteUrl = preg_match('/^http(s)?:\/\//', $relativeUrl) || preg_match("/^((?:[a-z]+:)?\/\/|data:image\/)(.*)/i", $relativeUrl);
        if ($isAbsoluteUrl || $domain === false) {
            return $relativeUrl;
        }

        // 拼接最终 URL
        $url = $domain . $relativeUrl;

        // 添加 CDN URL 参数
        $cdnUrlParams = config('upload.cdn_url_params');
        if ($domain === $cdnUrl && $cdnUrlParams) {
            $separator = str_contains($url, '?') ? '&' : '?';
            $url       .= $separator . $cdnUrlParams;
        }
        return $url;
    }

}

if (!function_exists('array_except')) {
    function array_except(array $array, array $keys): array
    {
        return array_diff_key($array, array_flip($keys));
    }
}