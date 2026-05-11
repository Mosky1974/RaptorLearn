# RaptorLearn 🦅
## Portal educativo interactivo sobre rapaces ibéricas

📚 **Trabajo Fin de Grado (TFG)**  
🎓 Ciclo Formativo de Grado Superior en **Desarrollo de Aplicaciones Web (DAW)**  
👤 Autor: **Joan Blanch Casas**  
👨‍🏫 Tutor: **Francisco Javier Granados**  
📆 Curso académico: **2025–2026**

---

## 📝 Descripción del proyecto

**RaptorLearn** es una aplicación web educativa orientada a la divulgación y el aprendizaje sobre las aves rapaces ibéricas.  
La plataforma ofrece un entorno accesible, visual e interactivo que permite consultar información detallada sobre distintas especies, incluyendo características principales, hábitat, comportamiento, estado de conservación y material multimedia de apoyo.

El proyecto combina **tecnología web**, **educación ambiental** y **gamificación**, con el objetivo de fomentar el aprendizaje significativo y la concienciación sobre la biodiversidad.

---

## 🎯 Objetivos

### Objetivo general
Desarrollar una aplicación web educativa que permita la consulta y divulgación de información sobre aves rapaces, aplicando los conocimientos adquiridos durante el ciclo formativo de Desarrollo de Aplicaciones Web.

### Objetivos específicos
- Diseñar una interfaz clara, intuitiva y responsive.
- Implementar una enciclopedia digital de especies.
- Incorporar elementos de gamificación para incentivar el aprendizaje.
- Desarrollar mini‑juegos educativos interactivos.
- Gestionar usuarios y progreso de aprendizaje.
- Aplicar buenas prácticas de desarrollo web y control de versiones.

---

## 🧱 Arquitectura del sistema

El sistema sigue una arquitectura en capas:

- **Capa de presentación (Cliente)**  
  HTML5, CSS3 y JavaScript en navegador web moderno.  
  Diseño responsive para escritorio, tablet y móvil.

- **Capa de aplicación (Servidor)**  
  Servidor Apache con backend en PHP (arquitectura MVC).

- **Capa de datos**  
  Base de datos relacional MySQL con más de 50 tablas.

- **Control de versiones y despliegue**  
  Git (local) y GitHub (repositorio remoto).

---

## 🛠️ Tecnologías utilizadas

- **HTML5** – Estructura semántica y accesible.
- **CSS3** – Diseño responsive, Flexbox y Grid.
- **JavaScript (ES6+)** – Interactividad y validación de datos.
- **PHP 8.x** – Lógica de negocio y backend.
- **MySQL 8.x** – Base de datos relacional.
- **Leaflet.js** – Mapas interactivos.
- **Git & GitHub** – Control de versiones.
- **Composer** – Gestión de dependencias (PHPMailer).
- **Mailtrap** – Pruebas de envío de emails.

---

## 🚀 Funcionalidades principales

- Enciclopedia interactiva de rapaces ibéricas.
- Fichas de especies con contenido multimedia.
- Búsqueda y filtrado de información.
- Sistema de usuarios y autenticación.
- Gamificación: niveles, puntos e insignias.
- Mini‑juegos educativos y cuestionarios.
- Área educativa con recursos para docentes.

---

## 📦 Instalación y ejecución (entorno local)

0. Clonar el repositorio:
   ```bash
   git clone https://github.com/Mosky1974/RaptorLearn.git

1. Clona el repositorio en `htdocs/raptorlearn/`
2. Copia `config/database.example.php` como `config/database.php` y configura tus credenciales
3. Importa el esquema en phpMyAdmin: `database/raptorlearn_db_schema.sql`
4. Asegúrate de que el módulo `mod_rewrite` está activo en Apache
5. Accede a `http://localhost/raptorlearn`