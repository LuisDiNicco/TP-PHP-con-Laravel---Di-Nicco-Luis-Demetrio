# Mini-API de Reclutamiento en Laravel

Este proyecto corresponde a la **Actividad Práctica N°2**, donde desarrollé una mini-API en Laravel que consume y expone datos de reclutamiento de manera legible y permite registrar nuevos candidatos.

---

## Descripción

La API tiene dos funcionalidades principales:

1. **GET /reclutados**  
   - Consume el recurso de Firebase:  
     `https://reclutamiento-dev-procontacto-default-rtdb.firebaseio.com/reclutier.json`  
   - Normaliza y presenta los datos en formato legible (HTML con tabla simple).  
   - Convierte `name` y `suraname` a **Title Case**.  
   - Calcula la edad a partir de la fecha de nacimiento.  
   - Filtra registros totalmente vacíos para que no aparezcan en la tabla. 
   - Elimina Duplicados 

2. **POST /recluta**  
   - Permite enviar un nuevo candidato en formato JSON:  
     ```json
     {
       "name":"TuNombre",
       "suraname":"TuApellido",
       "birthday":"1995/11/16",
       "documentType":"CUIT",
       "documentNumber":20123456781
     }
     ```
   - Normaliza y mapea los datos a:  
     ```json
     {
       "name":"TuNombre",
       "suraname":"TuApellido",
       "birthday":"1995/11/16/",
       "age":29,
       "documentType":"CUIT",
       "documentNumber":20123456781
     }
     ```
   - Valida:
     - `name` y `suraname`: Title Case.  
     - `birthday`: Formato `YYYY/MM/DD`, no posterior a hoy ni anterior a 1900/01/01. Agrega al final de la fecha otra '/'
     - `documentType`: solo `CUIT` o `DNI`.  
     - Calcula la edad automáticamente.  
   - Envía el registro a Firebase:  
     `https://reclutamiento-dev-procontacto-default-rtdb.firebaseio.com/reclutier.json`  
   - **Requiere cabecera `Accept: application/json`**, de lo contrario devuelve error 406.

---

## Requisitos

- PHP >= 8.1  
- Laravel 10  
- Composer  
- Servidor con acceso a internet para consumir Firebase  
- `.env` configurado con:  
`FIREBASE_URL=https://reclutamiento-dev-procontacto-default-rtdb.firebaseio.com`

## Instalación

1. Clonar el repositorio:  
 ```bash
 git clone <tu-repo-url>
 cd <tu-repo-folder>
```

2. Instalar dependencias:

`composer install`

3. Configurar el archivo .env (copiar de .env.example y actualizar FIREBASE_URL).


4. Generar key de Laravel:

`php artisan key:generate`

5. Ejecutar el servidor local:

`php artisan serve`

6. 6. Abra un navegador web e ingrese la siguiente URL para acceder a la API. Se mostrará la pantalla de bienvenida:
`http://127.0.0.1:8000`

---
## Endpoints

### GET /reclutados

Devuelve un HTML con todos los candidatos de Firebase en una tabla.

**Ejemplo de URL:**
`http://127.0.0.1:8000/reclutados`

---

### POST /recluta

Recibe un JSON con los datos del candidato, valida, normaliza y lo envía a Firebase.

Requiere cabecera `Accept: application/json`.

**Ejemplo en Postman** 

1. URL y método:

Método: POST

URL: http://127.0.0.1:8000/api/recluta

2. Headers:

Key:Accept	Value: application/json

Key:Content-Type	Value:application/json
	
3. Body:
Seleccionar raw y JSON y colocar el siguiente contenido:
```json
{
  "name": "Juan",
  "suraname": "Pérez",
  "birthday": "1990/05/12",
  "documentType": "DNI",
  "documentNumber": 12345678
}
```
**Ejemplo de respuesta exitosa:**
```json
{
  "name": "Juan",
  "suraname": "Pérez",
  "birthday": "1990/05/12/",
  "age": 33,
  "documentType": "DNI",
  "documentNumber": 12345678
}
```
**Si falta la cabecera Accept: application/json, devuelve:**
```json
{
  "error": "Esta ruta requiere que la cabecera Accept sea application/json"
}
```
con código HTTP 406 Not Acceptable.

---
### Reglas de negocio y validaciones
- name / suraname: Convertir a Title Case.

- birthday: YYYY/MM/DD, debe estar entre 1900/01/01 y hoy.

- age: Calculada automáticamente a partir de birthday.

- documentType: Solo CUIT o DNI.

- Salida a Firebase: birthday siempre termina con una barra (.../).

- Filtrado: Se eliminan filas completamente y los registros duplicados vacías antes de mostrar la tabla HTML.

- Errores: Solicitudes inválidas al POST devuelven 400 Bad Request con mensaje de error.

