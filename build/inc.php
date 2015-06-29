
    /*
     * Perform necessary instantiation functions
     */
    public function __construct()
    {
        // array flip the keys for html unified
        $this->map['html_to_unified'] = array_flip($this->map['unified_to_html']);
    }

    /*
     * Unified to html
     */
    public function emoji_unified_to_html($text)
    {
        return $this->emoji_convert($text, 'unified_to_html');
    }

    /*
     * Html to unified
     */
    function emoji_html_to_unified($text)
    {
        return $this->emoji_convert($text, 'html_to_unified');
    }

    /*
     * Emoji convert
     */
    function emoji_convert($text, $map)
    {
        return str_replace(array_keys($this->map[$map]), $this->map[$map], $text);
    }

    /*
     * Emoji Get Name
     */
    function emoji_get_name($unified_cp)
    {
        return $this->map['names'][$unified_cp] ? $this->map['names'][$unified_cp] : '?';
    }
}