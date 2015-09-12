<?php

/* index.html.twig */
class __TwigTemplate_be55de03411f699d51077679cfb1b38fa6d0c74933e269e54c731dcc126f6647 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!doctype html>
<html lang=\"fr\">
<head>
    <meta charset=\"UTF-8\">
    <title>Hello ";
        // line 5
        echo twig_escape_filter($this->env, (isset($context["name"]) ? $context["name"] : null), "html", null, true);
        echo "</title>
</head>
<body>
    <h1>";
        // line 8
        echo twig_escape_filter($this->env, (isset($context["name"]) ? $context["name"] : null), "html", null, true);
        echo "</h1>
</body>
</html>";
    }

    public function getTemplateName()
    {
        return "index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  31 => 8,  25 => 5,  19 => 1,);
    }
}
