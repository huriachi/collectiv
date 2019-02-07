<?php namespace collectiv\core;

class View {
    private static $twig;

    /**
     * Allows for the rendering of Twig templates within our views directory.
     *
     * @param string $template
     * @param array $arguments
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function render(string $template, array $arguments = []) {
        if (self::$twig === null) {
            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/views');
            self::$twig = new \Twig_Environment($loader);
        }
        return self::$twig->render($template, $arguments);
    }
}