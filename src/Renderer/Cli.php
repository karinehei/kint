<?php


class Kint_Renderer_Cli extends Kint_Renderer_Text
{
    /**
     * @var bool enable colors when Kint is run in *UNIX* command line
     */
    public static $cli_colors = true;

    /**
     * Forces utf8 output on windows.
     *
     * @var bool
     */
    public static $force_utf8 = false;

    private static $terminal_width = null;

    private $windows_output = false;

    public function __construct(array $params = array())
    {
        parent::__construct($params);

        if (!self::$force_utf8) {
            $this->windows_output = KINT_WIN;
        }

        if (!self::$terminal_width && !KINT_WIN) {
            self::$terminal_width = exec('tput cols');

            if (!self::$terminal_width) {
                self::$terminal_width = self::$default_width;
            }
        }

        $this->header_width = self::$terminal_width;
    }

    protected function utf8_to_windows($string)
    {
        return str_replace(
            array('┌', '═', '┐', '│', '└', '─', '┘'),
            array("\xda", "\xdc", "\xbf", "\xb3", "\xc0", "\xc4", "\xd9"),
            $string
        );
    }

    public function colorValue($string)
    {
        if (!self::$cli_colors) {
            return $string;
        } else {
            return "\x1b[32m".str_replace("\n", "\x1b[0m\n\x1b[32m", $string)."\x1b[0m";
        }
    }

    public function colorType($string)
    {
        if (!self::$cli_colors) {
            return $string;
        } else {
            return "\x1b[35;1m".str_replace("\n", "\x1b[0m\n\x1b[35;1m", $string)."\x1b[0m";
        }
    }

    public function colorTitle($string)
    {
        if (!self::$cli_colors) {
            return $string;
        } else {
            return "\x1b[36m".str_replace("\n", "\x1b[0m\n\x1b[36m", $string)."\x1b[0m";
        }
    }

    public function renderTitle(Kint_Object $o)
    {
        if ($this->windows_output) {
            return $this->utf8_to_windows(parent::renderTitle($o));
        } else {
            return parent::renderTitle($o);
        }
    }

    public function postRender()
    {
        if ($this->windows_output) {
            return $this->utf8_to_windows(parent::postRender());
        } else {
            return parent::postRender();
        }
    }
}
