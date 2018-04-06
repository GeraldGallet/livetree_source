<?php

/* header.html */
class __TwigTemplate_25a2ccb3121bb7fe4ba3d1e5bf0e7952807158041757bc1365c237acef105b96 extends Twig_Template
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "header.html"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "header.html"));

        // line 1
        echo "<html>
<head>
\t<meta charset=\"utf-8\">
</head>

<body>
  <a href='/'>Acceuil</a>
  <a href='/admin'>Admin</a>
  <a href='/voitures'>Voitures</a>
  <a href='/bornes'>Bornes</a>
  <a href='/profil'>Profil</a>
</body>
</html>
";
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    public function getTemplateName()
    {
        return "header.html";
    }

    public function getDebugInfo()
    {
        return array (  29 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("<html>
<head>
\t<meta charset=\"utf-8\">
</head>

<body>
  <a href='/'>Acceuil</a>
  <a href='/admin'>Admin</a>
  <a href='/voitures'>Voitures</a>
  <a href='/bornes'>Bornes</a>
  <a href='/profil'>Profil</a>
</body>
</html>
", "header.html", "C:\\Users\\geral\\Desktop\\Projets\\Projet Live Tree\\livetree_web\\templates\\header.html");
    }
}
