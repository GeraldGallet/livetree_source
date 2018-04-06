<?php

/* reservations/bornes.html.twig */
class __TwigTemplate_b0fdce7e94e74478edb2f8ca850f77d1517a7324729cee331a4f7d090f94ab69 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "reservations/bornes.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "reservations/bornes.html.twig"));

        // line 2
        echo "
<h1>Bornes</h1>
<h2>Hello ";
        // line 4
        echo twig_escape_filter($this->env, (isset($context["first_name"]) || array_key_exists("first_name", $context) ? $context["first_name"] : (function () { throw new Twig_Error_Runtime('Variable "first_name" does not exist.', 4, $this->source); })()), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, (isset($context["last_name"]) || array_key_exists("last_name", $context) ? $context["last_name"] : (function () { throw new Twig_Error_Runtime('Variable "last_name" does not exist.', 4, $this->source); })()), "html", null, true);
        echo "</h2>
";
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    public function getTemplateName()
    {
        return "reservations/bornes.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  33 => 4,  29 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# templates/reservations/bornes.html.twig #}

<h1>Bornes</h1>
<h2>Hello {{ first_name }} {{ last_name }}</h2>
", "reservations/bornes.html.twig", "C:\\Users\\geral\\Desktop\\Projets\\Projet Live Tree\\livetree_web\\templates\\reservations\\bornes.html.twig");
    }
}
