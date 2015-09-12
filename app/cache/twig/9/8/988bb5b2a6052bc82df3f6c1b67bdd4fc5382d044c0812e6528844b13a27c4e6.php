<?php

/* index.html.twig */
class __TwigTemplate_988bb5b2a6052bc82df3f6c1b67bdd4fc5382d044c0812e6528844b13a27c4e6 extends Twig_Template
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

    <link href=\"";
        // line 7
        echo twig_escape_filter($this->env, (isset($context["web_path"]) ? $context["web_path"] : null), "html", null, true);
        echo "/css/style.css\" rel=\"stylesheet\" />
</head>
<body>
    <h1>";
        // line 10
        echo twig_escape_filter($this->env, (isset($context["name"]) ? $context["name"] : null), "html", null, true);
        echo "</h1>

<p>
<a href=\"";
        // line 13
        echo twig_escape_filter($this->env, (isset($context["link"]) ? $context["link"] : null), "html", null, true);
        echo "\">lien généré ver cette route</a>
</p>
cds
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
        return array (  42 => 13,  36 => 10,  30 => 7,  25 => 5,  19 => 1,);
    }
}
