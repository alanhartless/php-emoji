<?php
	$in = file_get_contents('emoji-data/emoji.json');
	$catalog = json_decode($in, true);

	#
	# build the final maps
	#

    $maps = array_merge(
        make_html_map($catalog),
        make_shortname_map($catalog)
    );

	#
	# output
	# we could just use var_dump, but we get 'better' output this way
	#

echo <<<PHP
<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CoreBundle\Helper;

/**
 * Helper class for Emoji unicodes
 *
 * Build from modified https://github.com/iamcal/php-emoji
 */
class EmojiHelper
{
    /**
     * @var array Map of unicode
     */
    private static \$map = array(

PHP;

	foreach ($maps as $k => $v){
		echo "        '$k' => array(";
		foreach ($v as $k2 => $v2){
			echo "\n            ";
			echo format_string($k2).'=>'.format_string($v2).', ';
		}

		echo "        ),\n";
	}

	echo "    );\n";


	echo file_get_contents('inc.php');


	##########################################################################################
	function make_html_map($map)
    {
        $html    = array();
        $unified = array();
        foreach ($map as $row) {
            $hex                     = unicode_hex_chars($row['unified']);
            $bytes                   = unicode_bytes($row['unified']);
            $html[$bytes]            = "<span class=\"emoji-outer emoji-sizer\"><span class=\"emoji-inner emoji$hex\"></span></span>";
            $unified[$html[$bytes]]  = $bytes;

            if ($row['google']) {
                $googleBytes = unicode_bytes($row['google']);
                if ($googleBytes != $bytes) {
                    $html[$googleBytes] = $html[$bytes];
                }
            }
            if ($row['docomo']) {
                $docomoBytes = unicode_bytes($row['docomo']);
                if ($docomoBytes != $bytes) {
                    $html[$docomoBytes] = $html[$bytes];
                }
            }
            if ($row['softbank']) {
                $softbankBytes = unicode_bytes($row['softbank']);
                if ($softbankBytes != $bytes) {
                    $html[$softbankBytes] = $html[$bytes];
                }
            }
            if ($row['au']) {
                $auBytes = unicode_bytes($row['au']);
                if ($auBytes != $bytes) {
                    $html[$auBytes] = $html[$bytes];
                }
            }
            if (count($row['variations'])) {
                foreach ($row['variations'] as $variation) {
                    $variationBytes = unicode_bytes($variation);
                    if ($variationBytes != $bytes) {
                        $html[$variationBytes] = $html[$bytes];
                    }
                }
            }
        }

        return array(
            'emoji_to_html' => $html,
            'html_to_emoji' => $unified
        );
    }

    function make_shortname_map($map)
    {
        $short   = array();
        $unified = array();

        foreach ($map as $row) {
            foreach ($row['short_names'] as $name) {
                $name           = ":$name:";
                $bytes          = unicode_bytes($row['unified']);
                $short[$bytes]  = $name;
                $unified[$name] = $bytes;

                if ($row['google']) {
                    $googleBytes = unicode_bytes($row['google']);
                    if ($googleBytes != $bytes) {
                        $short[$googleBytes] = $name;
                    }
                }
                if ($row['docomo']) {
                    $docomoBytes = unicode_bytes($row['docomo']);
                    if ($docomoBytes != $bytes) {
                        $short[$docomoBytes] = $name;
                    }
                }
                if ($row['softbank']) {
                    $softbankBytes = unicode_bytes($row['softbank']);
                    if ($softbankBytes != $bytes) {
                        $short[$softbankBytes] = $name;
                    }
                }
                if ($row['au']) {
                    $auBytes = unicode_bytes($row['au']);
                    if ($auBytes != $bytes) {
                        $short[$auBytes] = $name;
                    }
                }
                if (count($row['variations'])) {
                    foreach ($row['variations'] as $variation) {
                        $variationBytes = unicode_bytes($variation);
                        if ($variationBytes != $bytes) {
                            $short[$variationBytes] = $name;
                        }
                    }
                }
            }
        }

        return array(
            'emoji_to_short' => $short,
            'short_to_emoji' => $unified
        );
    }

	function make_mapping($mapping, $dest){

		$result = array();

		foreach ($mapping as $map){

			$src_char = unicode_bytes($map['unified']);

			if (!empty($map[$dest])){

				$dest_char = unicode_bytes($map[$dest]);
			}else{
				$dest_char = '';
			}

			$result[$src_char] = $dest_char;
		}

		return $result;
	}

	function make_mapping_flip($mapping, $src){
		$result = make_mapping($mapping, $src);
		$result = array_flip($result);
		unset($result[""]);
		return $result;
	}

	function unicode_bytes($str){

		$out = '';

		$cps = explode('-', $str);
		foreach ($cps as $cp){
			$out .= emoji_utf8_bytes(hexdec($cp));
		}

		return $out;
	}

	function unicode_hex_chars($str){

		$out = '';

		$cps = explode('-', $str);
		foreach ($cps as $cp){
			$out .= sprintf('%x', hexdec($cp));
		}

		return $out;
	}

	function emoji_utf8_bytes($cp){

		if ($cp > 0x10000){
			# 4 bytes
			return	chr(0xF0 | (($cp & 0x1C0000) >> 18)).
				chr(0x80 | (($cp & 0x3F000) >> 12)).
				chr(0x80 | (($cp & 0xFC0) >> 6)).
				chr(0x80 | ($cp & 0x3F));
		}else if ($cp > 0x800){
			# 3 bytes
			return	chr(0xE0 | (($cp & 0xF000) >> 12)).
				chr(0x80 | (($cp & 0xFC0) >> 6)).
				chr(0x80 | ($cp & 0x3F));
		}else if ($cp > 0x80){
			# 2 bytes
			return	chr(0xC0 | (($cp & 0x7C0) >> 6)).
				chr(0x80 | ($cp & 0x3F));
		}else{
			# 1 byte
			return chr($cp);
		}
	}

	function format_string($s){
		$out = '';
		for ($i=0; $i<strlen($s); $i++){
			$c = ord(substr($s,$i,1));
			if ($c >= 0x20 && $c < 0x80 && !in_array($c, array(34, 39, 92))){
				$out .= chr($c);
			}else{
				$out .= sprintf('\\x%02x', $c);
			}
		}
		return '"'.$out.'"';
	}

