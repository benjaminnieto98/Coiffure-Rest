# Documentacion API - Coiffure || Barber Shop

###  URI
Accesible mediante la dirección web http://localhost/web2/coiffure-Rest/api/

- Debe especificarse un recurso con el siguiente formato: http://localhost/web2/coiffure-Rest/api/recurso  

- Si se desea, se puede especificar un id particular con el siguiente formato: http://localhost/web2/coiffure-Rest/api/recurso/id  

###  RECURSOS
Se encuentran disponibles los recursos de *products*.

###  PARAMETROS
Los parametros disponibles son los siguientes:

- **orderBy** Ordena los elementos por el campo especificado por el usuario. Funciona en combinacion con *orderMode*.
- **orderMode** Especifica el orden en que se muestran los elementos, puede tomar valores *asc* y *desc*. Por defecto se muestra ascendentemente.
- **page** Muestra el numero de pagina que se especifique. Debe ser mayor a 0 (cero). Por defecto devuelve la primera página. Funciona en combinacion con *elements*.
- **elements** Indica la cantidad de elementos que se muestran en cada pagina. Debe ser mayor a 0 (cero). Por defecto la consulta muestra de a 5 elementos por pagina.
- **filterBy** Indica el nombre de una columna de la tabla por la que se filtraran los resultados. Funciona en combinacion con *equalTo*.
- **equalTo** Obtiene el valor por el cual se filtraran los resultados de la consulta.

### ENDPOINTS
| Route        | Method         | Description   |
| :---         |     :---:      |          ---: |
| /products    | GET            | Retorna todos los productos    |
| /products/id | GET            | Retorna un articulo con el id especificado    |
| /products    | POST           | Crea un nuevo producto    |
| /products/id | DELETE         | Borra el producto con el id especificado    |
| /products/id | PUT            | Actualiza el producto con el id especificado    |

### DATOS DE AUTENTICACION
user: benjaminnieto98@gmail.com
password: 123