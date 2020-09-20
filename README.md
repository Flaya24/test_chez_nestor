Ce projet constitue ma réponse au test technique de Chez Nestor.

#### Stack

Le projet a été réalisé à l'aide des éléments de programmation suivants : 

- PHP : 7.3.21
- Symfony : 5.1
- MySQL : 5.7.31

Le framework Symfony, utilisé pour construire l'intégralité du projet, est complété avec les modules complémentaires suivants :

- Doctrine : ORM 
- Nelmio CORS Bundle : Module permettant un CORS total pour l'utilisation de l'API

#### Installation

Pour installer la solution sur un serveur local, il est tout d'abord nécessaire d'installer les composants de base.

Etant une solution PHP standard, vous devrez tout d'abord télécharger un package PHP / Apache / MySQL, comme [WAMP](https://www.wampserver.com/) par exemple.

Une fois ceci installé, vous pouvez cloner le projet :

```
# git clone https://github.com/Flaya24/test_chez_nestor.git
```

Ensuite, placez vous dans le dossier du repository et lancez l'installation de Symfony via Composer :

```
# cd test_chez_nestor
# composer install
```

Une fois l'installation terminée, nous allons installer la base de données. Par défaut, la configuration est faite pour un environnement local standard sans mot de passe (mysql://root@127.0.0.1:3306) et une base de données appelée "nestor". Il est possible de modifier ces paramètres en modifiant DATABASE_URL dans le fichier .env.

Assurez vous que MySQL est bien lancé, et exécutez les commandes :

```
# php bin/console doctrine:database:create
# php bin/console doctrine:migrations:migrate
```

La base de données est créée. Vous pouvez maintenant lancer la solution :

```
# symfony server:start
```

### API

Ayant le "Cross-Origin Resource Sharing" totalement ouvert, les points d'entrée API peuvent être testé via n'importe quel outil ou interface. J'ai personnellement utilisé [Postman](https://www.postman.com/).

Les données d'entrée et de retour sont en JSON.

Pour chaque modèle de donnée, les points d'entrée API ont tous le même fonctionnement :

* Create : `[POST] /api/{nom_du_modèle}`
  * Vous devez passer en JSON les propriétés du modèle que vous souhaitez pour l'objet à créer (excepté 'id').
  * En cas de succès, la requête retourne un statut HTTP 200, ainsi qu'un JSON avec une clé "new_item" contenant les propriétés complètes de l'objet créé.
  * En cas d'erreur, la requête retourne un statut HTTP 400, ainsi qu'un JSON avec une clé "errors" contenant les propriétés ayant échouées ainsi que leurs messages d'erreur respectifs.
  * En cas d'échec, la requête retourne un statut HTTP 500, ainsi qu'un JSON avec une clé "message" contenant le message d'erreur associé.

* Read : `[GET] /api/{nom_du_modèle}`
  * Vous pouvez passer en paramètre GET n'importe quel propriété du modèle pour filtrer les résultats.
  * En cas de succès, la requête retourne un statut HTTP 200, ainsi qu'un JSON avec une clé "items" contenant une collection de tous les objets obtenus.

* Update : `[PUT] /api/{nom_du_modèle}/{id}`
  * Vous devez passer en JSON les propriétés du modèle que vous souhaitez mettre à jour (excepté 'id').
  * En cas de succès, la requête retourne un statut HTTP 200, ainsi qu'un JSON avec une clé "updated_item" contenant les propriétés complètes de l'objet modifié.
  * En cas d'erreur, la requête retourne un statut HTTP 400, ainsi qu'un JSON avec une clé "errors" contenant les propriétés ayant échouées ainsi que leurs messages d'erreur respectifs.
  * En cas d'échec, la requête retourne un statut HTTP 500, ainsi qu'un JSON avec une clé "message" contenant le message d'erreur associé.

* Delete : `[DELETE] /api/{nom_du_modèle}/{id}`
  * En cas de succès, la requête retourne un statut HTTP 200.
  * En cas d'échec, la requête retourne un statut HTTP 500, ainsi qu'un JSON avec une clé "message" contenant le message d'erreur associé.

##### Client

Le modèle Client comprend les propriétés suivantes : 

| Nom      | Description           | Format  |
| :-------------: |:-------------| -----|
| id     | Identifiant unique | integer |
| firstName      | Prénom du client     |   string |
| lastName | Nom du client     |    string |
| email | Email du client     |    string |
| phone | Téléphone du client     |    string |
| birthDate | Date de naissance du client     |    date au format "JJ/MM/AAAA" |
| nationality | Nationalité du client     |    string |

Les endpoints de l'API pour Client sont les suivants :

* Create Client : `[POST] /api/client`
  * Le champ mail est obligatoire.
  * Une erreur est retournée si le mail existe déjà en base de données.
  
* Read Client : `[GET] /api/client`

* Update Client : `[PUT] /api/client/{id}`

* Delete Client : `[DELETE] /api/client/{id}`

##### Apartment

Le modèle Apartment comprend les propriétés suivantes : 

| Nom      | Description           | Format  |
| :-------------: |:-------------| -----|
| id     | Identifiant unique | integer |
| name      | Nom de l'appartement     |   string |
| street | Rue de l'appartement      |    string |
| zipCode | Code postal de l'appartement     |    string |
| city | Ville de l'appartement     |    string |

Les endpoints de l'API pour Apartment sont les suivants :

* Create Apartment : `[POST] /api/apartment`
  * A la creation, vous devez obligatoirement passer les propriétés JSON de Apartment dans une clé "apartment" et les propriétés de Room, représentant la chambre obligatoire, dans une clé "room".
  * De la même façon, à la création, la requête retournera les données de l'objet Apartment et Room créés. 
  * Toutes les propriétés sont obligatoires.
  
* Read Apartment : `[GET] /api/apartment`

* Update Apartment : `[PUT] /api/apartment/{id}`

* Delete Apartment : `[DELETE] /api/apartment/{id}`

##### Room

Le modèle Room comprend les propriétés suivantes : 

| Nom      | Description           | Format  |
| :-------------: |:-------------| -----|
| id     | Identifiant unique | integer |
| number      | Numéro de la chambre   |   integer |
| area | Surface de la chambre     |    float |
| price | Prix de la chambre     |    integer |
| apartment_id | Identifiant unique de l'appartement     |    integer |

Les endpoints de l'API pour Room sont les suivants :

* Create Room : `[POST] /api/room`
  * Toutes les propriétés sont obligatoires.
  
* Read Room : `[GET] /api/room`

* Update Room : `[PUT] /api/room/{id}`

* Delete Room : `[DELETE] /api/room/{id}`
  * Une erreur survient si la chambre à supprimer est la seule de l'appartement.
  
##### Reservation
  
Le modèle Reservation comprend les propriétés suivantes : 
  
  | Nom      | Description           | Format  |
  | :-------------: |:-------------| -----|
  | id     | Identifiant unique | integer |
  | clientId      | Identifiant unique du client    |   integer |
  | roomId | Identifiant unique de la chambre    |    integer |
  | startDate | Date de début de la réservation     |    date au format "JJ/MM/AAAA" |
  | endDate | Date de fin de la réservation     |    date au format "JJ/MM/AAAA" |
  
  Les endpoints de l'API pour Reservation sont les suivants :
  
  * Create Reservation : `[POST] /api/reservation`
    * Toutes les propriétés sont obligatoires excepté "endDate".
    * Une erreur survient si la chambre possède déjà une réservation pour la date précisée dans "startDate".
    * Une erreur survient si le client possède déjà une réservation pour la date précisée dans "startDate".
    * Une erreur survient si le client associé n'a pas précisé toutes les informations le concernant (toutes les propriétés du modèle Client).
    
  * Read Reservation : `[GET] /api/reservation`
  
  * Update Reservation : `[PUT] /api/reservation/{id}`
  
  * Delete Reservation : `[DELETE] /api/reservation/{id}`


