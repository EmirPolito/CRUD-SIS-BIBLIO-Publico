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

- **Backend:** PHP 8+ (Sin Frameworks)
- **Base de Datos:** MySQL / MariaDB (Driver PDO)
- **Frontend:** HTML5, CSS3 Nativo, JavaScript Vanilla (Manipulación pura del DOM, sin librerías externas)
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
├── ca.pem                  # Certificado SSL para conexión segura a base de datos en la nube
├── index.php               # Landing Page publicitaria del sistema
├── login.php               # Validación de Acceso
├── register.php            # Registro de nuevos lectores
├── recover.php             # Petición de recuperación (Envío de Email)
└── reset.php               # Cambio autorizado de contraseña (Vía Token)
```

## Instalación y Configuración

1. Mueve esta carpeta a tu entorno de desarrollo (`htdocs` de apache, `htdocs` de XAMPP o `www` de WampServer).
2. **Configuración de la Base de Datos:**
   - Importa el archivo `base_de_datos.sql` a tu gestor de la base de datos local o en la nube.
   - Abre el archivo `config/database.php` y configura tus variables de conexión (`$host`, `$db`, `$user`, `$port`, `$pass`).
   - **Conexión Segura (ca.pem):** Si usas una base de datos en la nube con requerimiento SSL (ej. Aiven, AWS), reemplaza el texto en `ca.pem` con tu certificado real. Si usas una base de datos local (como XAMPP/WAMP), deshabilita o comenta las líneas que mencionan `ca.pem` (Líneas 18-19 en `config/database.php`) para evitar errores.
3. **Configuración de Recuperación de Contraseña (PHPMailer):**
   - Abre el archivo `recover.php` y localiza la configuración SMTP (Aprox. línea 47).
   - En `$mail->Username`, coloca tu correo electrónico de Gmail.
   - En `$mail->Password`, no pongas tu clave personal. Debes generar una **Contraseña de Aplicación de 16 dígitos** en la sección de seguridad de tu cuenta Google: [https://myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords) y pegarla ahí.
4. Visita en tu navegador: `http://localhost/SIS-BIBLIO/`

## Casos de Prueba Incluidos

La base de datos se entrega pre-poblada con 2 cuentas maestras para pruebas de acceso:

- **Administrador:** `admin@ejemplo.com` | Pass: `1234567`
- **Lector Estándar:** `lector@ejemplo.com` | Pass: `1234567`

---

_Desarrollado bajo los lineamientos de proyectos limpios, cumpliendo estrictamente con la rúbrica de Seguridad de Aplicaciones Web de DWP - SDA 3er Parcial._
