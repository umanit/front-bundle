ReadMe
========

Ce starter kit est à utiliser dans le cadre d'un projet symfony. Il embarque [le starter kit](https://github.com/umanit/front-kit)

Avec des fonctionnalités en plus :
* un guide de style
* une route pour gérer des intégrations statiques

Pré-requis
--------

* Utiliser Symfony 4
* Utiliser NVM pour installer node et yarn sur sa machine

Installation
--------

À la racine du projet lancer la commande

```
composer require umanit/front-bundle
```

Une fois le bundle installé **il faut supprimer le package.json et le webpack.config.js** qui ont été rajoutés par la
recipe flex de `webpack-encore-bundle`.

Ensuite, il faut lancer la commande suivante :

```
php bin/console umanit:front-bundle:init
```

Une fois terminée, il faut installer les dépendances front :

```
nvm exec yarn install
```

Enregistrer les routes

```yaml
# app/config/routes/dev/umanit_front_bundle.yaml
umanit_front_bundle:
    resource: "@UmanitFrontBundle/Resources/config/routes/dev/routes.yaml"
```

### Le dossier et les fichiers du guide de style
Dans `projet/templates`, créer le dossier `style_guide`, puis structurer comme suit :
```
style_guide
    |_ index.html.twig
    |_ modules
        |_ buttons.html.twig
```

#### index.html.twig
```twig
{% extends '@UmanitFront/style_guide/base.html.twig' %}

{% block title %}{% endblock %}

{% block body %}
    <table>
        {% include '@UmanitFront/style_guide/partials/progress.html.twig' with {
             template: 'block',
             title: 'Block',
             tags: ['layout'],
             description: 'Block description',
             progress: 30
         } %}
    </table>
{% endblock %}
```

Le `{% include %}` est à répéter autant de fois qu'il y a d'éléments ajoutés dans le dossier.

### Route d'intégration statique

La route `/static/{path}` permet d'afficher les Twigs intégrés de manière statiques
La variable `{path}` correspond au chemin d'accès d'un template Twig se trouvant dans `templates/`, qu'importe sa profondeur dans
l'arborescence.

* `https://domain.wip/static/nom-du-twig.html` rendra la vue Twig `templates/nom-du-twig.html.twig`
* `https://domain.wip/static/sous-dossier/ma_vue.html` rendra la vue Twig `templates/sous-dossier/ma_vue.html.twig`

Utilisation
--------

Pour toute la partie CSS l'utilisation est la même que le [front kit](https://github.com/umanit/front-kit#utilisation).
