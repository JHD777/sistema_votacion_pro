# Sistema de Votación Pro

## Descripción del Proyecto

Sistema de Votación Pro es una plataforma electrónica segura y eficiente diseñada para gestionar y realizar procesos de votación en línea. Permite la creación de diferentes tipos de elecciones, la gestión de candidatos y usuarios, y la emisión de votos de manera segura y verificable. Cuenta con paneles de administración y usuario para una gestión y participación diferenciada.

## Características Principales

*   Gestión de Elecciones (creación, edición, activación/desactivación)
*   Gestión de Candidatos asociados a elecciones
*   Registro y autenticación de Usuarios (con roles diferenciados: usuario, administrador, administrador supremo)
*   Emisión de Votos por parte de usuarios verificados
*   Visualización de Resultados de elecciones (para administradores)
*   Panel de Administración con diferentes niveles de acceso
*   Panel de Usuario para ver elecciones activas y emitir votos
*   Configuración del sistema (zona horaria, etc.)

## Estructura del Proyecto (Patrón MVC)

El proyecto sigue un patrón de diseño Modelo-Vista-Controlador (MVC) para organizar el código de manera modular y mantenible.

*   **`controllers/`**: Contiene la lógica de la aplicación. Los controladores procesan las solicitudes del usuario, interactúan con los modelos y seleccionan la vista adecuada para responder.
*   **`models/`**: Contiene la lógica de negocio y la interacción con la base de datos. Los modelos representan los datos y las reglas para trabajar con ellos (por ejemplo, `User.php`, `Eleccion.php`, `Database.php`).
*   **`views/`**: Contiene la presentación de la aplicación (archivos HTML con PHP incrustado). Las vistas muestran la información al usuario y envían las interacciones de vuelta a los controladores.
*   **`config/`**: Archivos de configuración del sistema y la base de datos.
*   **`public/`**: El directorio raíz del servidor web. Contiene el punto de entrada principal (`index.php`) y archivos públicos como CSS, JS, imágenes, etc. El archivo `.htaccess` aquí redirige todas las solicitudes a `index.php` para el enrutamiento.
*   **`helpers/`**: Clases o funciones auxiliares utilizadas en diferentes partes del proyecto (ej. `AuthHelper.php`).
*   **`database.sql`**: Script SQL para crear la estructura de la base de datos.

## Tecnologías Utilizadas

*   PHP (>= 7.4)
*   MySQL (o compatible)
*   Apache o Nginx (con soporte PHP y mod_rewrite/equivalente)
*   Bootstrap 5 (para el frontend)
*   PDO (para la interacción con la base de datos)
*   DataTables (para tablas interactivas en el admin)
*   Flatpickr (para selectores de fecha/hora)

## Instalación y Configuración (para Desarrolladores)

1.  **Clonar el repositorio:**
    ```bash
    git clone <URL_DEL_REPOSITORIO>
    cd sistema_votacion_pro
    ```
2.  **Configurar el servidor web:**
    *   Apunta la raíz del documento de tu servidor web (Apache/Nginx) al directorio `public/` dentro del proyecto.
    *   Asegúrate de que `mod_rewrite` (Apache) o la configuración equivalente (Nginx) esté habilitada para permitir el enrutamiento basado en URL.
3.  **Configurar la base de datos:**
    *   Crea una base de datos MySQL vacía (ej. `sistema_votacion_pro`).
    *   Importa el archivo `database.sql` en tu nueva base de datos para crear las tablas necesarias.
4.  **Configurar la conexión a la base de datos:**
    *   Copia el archivo `config/dbconfig.php.example` a `config/dbconfig.php` (si existe un archivo de ejemplo).
    *   Edita `config/dbconfig.php` con tus credenciales de base de datos (hostname, nombre de la base de datos, usuario, contraseña, puerto, etc.).
5.  **Configuración adicional (opcional):**
    *   Edita `config/config.php` para ajustar configuraciones generales como el nombre de la aplicación, zona horaria por defecto, etc.
    *   (Nota: Algunas configuraciones, como la zona horaria, también pueden gestionarse desde el panel de administrador supremo y sobrescribirán las de `config.php`).
6.  **Verificar la instalación:**
    *   Abre el proyecto en tu navegador (ej. `http://localhost/sistema_votacion_pro/`). Deberías ver la página de inicio.
    *   Accede al panel de administración (ej. `http://localhost/sistema_votacion_pro/?view=admin/login`) y al panel de usuario (ej. `http://localhost/sistema_votacion_pro/?view=login`).

## Uso del Sistema (para Usuarios y Empresas)

*(Esta sección se detallará más adelante, pero aquí puedes poner un resumen)*

*   **Usuarios:** Registrarse, iniciar sesión, ver elecciones activas, emitir voto, ver perfil.
*   **Administradores:** Iniciar sesión, gestionar elecciones, gestionar candidatos, ver resultados de elecciones, gestionar usuarios.
*   **Administrador Supremo:** Acceso a configuraciones avanzadas del sistema y gestión de otros administradores.

## Estructura de la Base de Datos

Ver el archivo `database.sql` para la estructura completa de las tablas (`usuarios`, `elecciones`, `candidatos`, `votos`, `system_config`).

## Contribuir

*(Si deseas que otros contribuyan, explica cómo hacerlo: fork del repositorio, ramas, pull requests, etc.)*

## Licencia

Este proyecto está bajo la licencia [Indica el tipo de licencia, por ejemplo, MIT]. Ver el archivo LICENSE para más detalles.

## Contacto

Para soporte o preguntas, contactar a [Tu Nombre/Email].

---

*Este README será actualizado a medida que el proyecto evolucione.* 