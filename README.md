## 👨‍💻 Autor

**Emir Polito**

- GitHub: https://github.com/EmirPolito
- Linkedin: https://www.linkedin.com/in/emir-polito-g/

# SIS-BIBLIO (Sistema de Gestión Bibliotecaria)

**SIS-BIBLIO** es una plataforma integral desarrollada para la administración eficiente de una biblioteca y un avanzado **Módulo de Seguridad Web** (Proyecto Final DWP – SDA 3er Parcial). Construido estrictamente con **PHP puro**, HTML5, CSS3, JavaScript nativo (Vanilla) y MySQL (PDO), este sistema enfatiza las mejores prácticas de **Clean Code** y **Principios SOLID**, garantizando una seguridad de grado profesional en cada interacción.

## Características Principales

### 1. Módulo de Autenticación y Seguridad

- **Login Seguro:** Validación del lado del cliente y servidor. Regeneración de ID de sesión PHP (`session_regenerate_id`).
- **Antifuerza Bruta:** El sistema bloquea temporalmente las cuentas tras múltiples intentos fallidos.
- **Tokens CSRF:** Incorporados en todos los formularios y acciones críticas (modificar/eliminar) para evitar _Cross-Site Request Forgery_.
- **Contraseñas Hasheadas:** Uso del algoritmo seguro `PASSWORD_BCRYPT`.
- **Prevención de Inyección SQL y XSS:** Uso estricto de sentencias preparadas (PDO) y sanitización de salidas HTML (`htmlspecialchars`).
- **Arquitectura de Roles:** Diferenciación estricta entre Administradores (Staff) y Lectores, con ocultación de UI y bloqueos de backend.
- **Interfaz Moderna y Responsiva:** Diseño reestructurado con modo oscuro elegante para las pantallas de autenticación, totalmente **adaptable a dispositivos móviles (smartphones y tablets)** mediante cuadrículas flexibles, botones táctiles y menú de hamburguesa. Incluye controles interactivos para visualizar/ocultar contraseñas.

### 2. Panel de Control (Dashboard)

- **Dashboard Estadístico:** Visualización en tiempo real de métricas e indicadores diferenciados por rol.
- **Estilos Modulares:** Cada página cuenta con su propio archivo `.css` encapsulado bajo la carpeta `/assets/dashboard/` garantizando un aislamiento de diseño perfecto y escalable.
- **Catálogo de Libros:** CRUD completo para gestionar el acervo bibliográfico.
- **Mis Préstamos:** Historial y estado de los libros prestados por el lector.
- **Gestión de Cuentas:** Administración jerárquica para creación, edición y eliminación (con confirmación JS nativa) de cuentas de Staff y Lectores.

### 3. Recuperación de Contraseña Segura

- Generación de un token aleatorio, único y de un solo uso, con vida útil limitada (1 hora).
- Integración al 100% con **PHPMailer** para el envío del enlace temporal al correo del usuario.

## Tecnologías Utilizadas

- ![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white) **Backend:** PHP 8+ (Sin Frameworks)
- ![MySQL](https://img.shields.io/badge/mysql-%2300f.svg?style=for-the-badge&logo=mysql&logoColor=white) ![MariaDB](https://img.shields.io/badge/MariaDB-003545?style=for-the-badge&logo=mariadb&logoColor=white) **Base de Datos:** MySQL / MariaDB (Driver PDO)
- ![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=for-the-badge&logo=html5&logoColor=white) ![CSS3](https://img.shields.io/badge/css3-%231572B6.svg?style=for-the-badge&logo=css3&logoColor=white) ![JavaScript](https://img.shields.io/badge/javascript-%23323330.svg?style=for-the-badge&logo=javascript&logoColor=%23F7DF1E) **Frontend:** HTML5, CSS3 Nativo, JavaScript Vanilla (Manipulación pura del DOM, sin librerías externas)
- **Librerías de Utilidad:** PHPMailer, FontAwesome (únicamente íconos)
- **Arquitectura:** Patrón estructural limpio con separación de responsabilidades funcionales (Configuración, Autenticación, Vistas).

## Estructura del Proyecto

```text
SIS-BIBLIO/
├── assets/                 # Estilos CSS públicos e imágenes
│   └── dashboard/          # CSS encapsulado uno a uno para cada interfaz privada
├── config/                 # Lógica CORE (conexión PDO, gestor de autenticación y CSRF)
├── modules/                # Componentes del Dashboard y Formularios de CRUD
├── PHPMailer/              # Motor para envío transaccional de correos
├── base_de_datos.sql       # Script SQL (Incluye usuarios de prueba bcrypted)
├── ca.pem                  # Certificado SSL para conexiones seguras a DB en la Nube (Aiven/Wasmer)
├── index.php               # Landing Page publicitaria del sistema
├── login.php               # Validación de Acceso
├── register.php            # Registro de nuevos lectores
├── recover.php             # Petición de recuperación (Envío de Email)
└── reset.php               # Cambio autorizado de contraseña (Vía Token)
```

## Instalación y Configuración

1. Mueve esta carpeta a tu entorno de desarrollo (`htdocs` de apache, `htdocs` de XAMPP o `www` de WampServer).
2. **Configuración de la Base de Datos:**
   - Para uso **local**: Importa el archivo `base_de_datos.sql` a MySQL Workbench o phpMyAdmin.
   - Abre el archivo `config/database.php` y configura las variables de conexión.
   - **Nota de Despliegue en la Nube:** El sistema ya está optimizado para conectarse a plataformas como Aiven Cloud / Wasmer. Si lo utilizas en un entorno de producción, asegúrate de mantener opciones de SSL activas utilizando el archivo `ca.pem` incluido.
3. **Configuración de Recuperación de Contraseña (PHPMailer):**
   - Abre el archivo `recover.php` y localiza la configuración SMTP (Aprox. línea 47).
   - En `$mail->Username`, coloca tu correo electrónico de Gmail.
   - En `$mail->Password`, no pongas tu clave personal. Debes generar una **Contraseña de Aplicación de 16 dígitos** en la sección de seguridad de tu cuenta Google: [https://myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords) y pegarla ahí.
4. Visita en tu navegador: `http://localhost/CRUD-SIS-BIBLIO-Publico/index.php`

## Casos de Prueba Incluidos

La base de datos se entrega pre-poblada con 2 cuentas maestras para pruebas de acceso:

- **Administrador:** `admin@ejemplo.com` | Pass: `1234567`
- **Lector Estándar:** `lector@ejemplo.com` | Pass: `1234567`

---
