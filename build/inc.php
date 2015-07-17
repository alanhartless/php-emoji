
    /**
     * @param        $content
     * @param string $to emoji|html|short
     *
     * @return mixed
     */
    public static function convert($content, $to = 'html')
    {
        if (is_array($content)) {
            foreach ($content as &$convert) {
                $convert = self::convert($convert, $to);
            }
        } else {
            switch ($to) {
                case 'html':
                    $content = self::toHtml($content);
                    break;
                case 'emoji':
                    $content = self::toemoji($content);
                    break;
                default:
                    $content = self::toShort($content);
                    break;
            }

        }

        return $content;
    }

    /**
     * Convert to html
     *
     * @param $text
     * @param $from
     *
     * @return mixed
     */
    public static function toHtml($text, $from = 'emoji')
    {
        return self::emoji_convert($text, $from, 'html');
    }

    /**
     * Convert to emoji
     *
     * @param $text
     * @param $from
     *
     * @return mixed
     */
    public static function toEmoji($text, $from = 'html')
    {
        return self::emoji_convert($text, $from, 'emoji');
    }

    /**
     * Convert to short code
     *
     * @param        $text
     * @param string $from
     *
     * @return mixed
     */
    public static function toShort($text, $from = 'emoji')
    {
        return self::emoji_convert($text, $from, 'short');
    }

    /**
     *
     *
     * @param $text
     * @param $from
     * @param $to
     *
     * @return mixed
     */
    private static function emoji_convert($text, $from, $to)
    {
        $maps = array();
        switch ($from) {
            case 'html':
                switch ($to) {
                    case 'emoji':
                        $maps[] = 'html_to_emoji';
                        break;
                    case 'short':
                        $maps[] = 'html_to_emoji';
                        $maps[] = 'emoji_to_short';
                        break;
                }
                break;
            case 'emoji':
                switch ($to) {
                    case 'html':
                        $maps[] = 'emoji_to_html';
                        break;
                    case 'short':
                        $maps[] = 'emoji_to_short';
                        break;
                }
                break;
            case 'short':
                switch ($to) {
                    case 'html':
                        $maps[] = 'short_to_emoji';
                        $maps[] = 'emoji_to_html';
                        break;
                    case 'emoji':
                        $maps[] = 'short_to_emoji';
                        break;
                }
                break;
        }

        foreach ($maps as $useMap) {
            $text = str_replace(array_keys(self::$map[$useMap]), self::$map[$useMap], $text);
        }

        return $text;
    }
}