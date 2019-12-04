ReadMe 
========

Ce starter kit est à utiliser dans le cadre d'un projet symfony. Il embarque [le starter kit ](https://github.com/umanit/front-kit)
Avec des fonctionnalités en plus : un guide de style 

Pré-requis
--------


* Utiliser symfony 4
* Avoir dockerisé node dans le projet cible dans un fichier **docker-compose.yaml** 

```
services:
  node:
    image: node:12
    working_dir: /var/www/html
    user: node
    tty: true
    volumes:
      - .:/var/www/html:delegated
```

Installation
--------

Il faut ajouter dans le composer.json de votre projet l'entré suivante 

```
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/umanit/front-bundle"
        }
    ],
```

Ensuite à la racine du projet lancer la commande 

```
composer require umanit/front-bundle
```

Une fois le bundle installé il **faut supprimer le package.json et le webpack.config.js** qui ont été rajoutés par la 
recipe flex du webpack-encore-bundle

Ensuite il faut lancer la commande suivante : 

```
php bin/console umanit:front-bundle:init 
```

Une fois terminée il faut installer les dépendances front : 

```
docker-compose exec node yarn install
```

Enregistrer les routes 

```
# app/config/routes/dev/styleguide.yaml
style_guide:
    resource: "@UmanitFrontBundle/Resources/config/routes/dev/routes.yaml"
```

### Le dossier et les fichiers du guide de style
Dans ```projet/templates```, créer le dossier ```style_guide```, puis structurer comme suit :
```
style_guide
    |_ index.html.twig
    |_ modules
        |_ buttons.html.twig
```

#### index.html.twig
```
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
          }  %}
    </table>
{% endblock %}
```
Le ``` {% include %} ``` est à répéter autant de fois qu'il y a d'éléments ajoutés dans le dossier.

Utilisation
--------

Pour toute la partie css l'utilisation est la même que le [front kit](https://github.com/umanit/front-kit#utilisation)
