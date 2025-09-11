# BeTravel - Sistema de Gestión de Agencia de Viajes

## Descripción
BeTravel es un sistema web completo para una agencia de viajes que incluye:
- Sitio web público con páginas alineadas estéticamente
- Sistema de gestión de contactos
- Funcionalidad de destinos favoritos
- Sistema de reservas de paquetes turísticos
- Panel de administración completo
- Base de datos MySQL integrada

## Características Principales

### Frontend
- **Diseño Responsivo**: Compatible con dispositivos móviles y desktop
- **Bootstrap 5**: Framework CSS moderno
- **Font Awesome**: Iconos profesionales
- **JavaScript Interactivo**: Funcionalidades dinámicas
- **Carruseles**: Presentación atractiva de servicios y destinos

### Backend
- **PHP 7.4+**: Lenguaje de servidor
- **MySQL**: Base de datos relacional
- **PDO**: Conexión segura a base de datos
- **AJAX**: Comunicación asíncrona
- **Validaciones**: Seguridad en formularios

### Funcionalidades
- **Gestión de Contactos**: Formulario con validación y almacenamiento
- **Destinos Favoritos**: Sistema de favoritos por usuario
- **Reservas de Paquetes**: Sistema completo de reservas
- **Panel de Administración**: Gestión completa del sistema
- **Estadísticas**: Análisis de uso y rendimiento

## Estructura de Archivos

```
BeTravel/
├── index.html              # Página principal
├── destinos.html           # Página de destinos
├── servicios.html          # Página de servicios
├── contacto.html           # Página de contacto
├── styles.css              # Estilos CSS
├── scripts.js              # JavaScript principal
├── config.php              # Configuración de base de datos
├── database.sql            # Estructura de base de datos
├── procesar_contacto.php   # Procesamiento de contactos
├── favoritos.php           # Gestión de favoritos
├── reservas.php            # Gestión de reservas
├── admin.php               # Panel de administración principal
├── admin_dashboard.php     # Dashboard del admin
├── admin_contactos.php     # Gestión de contactos (admin)
├── admin_favoritos.php     # Gestión de favoritos (admin)
├── admin_reservas.php      # Gestión de reservas (admin)
├── admin_estadisticas.php  # Estadísticas (admin)
└── README.md               # Este archivo
```

## Instalación y Configuración

### Requisitos Previos
- XAMPP (Apache + MySQL + PHP)
- Navegador web moderno
- Editor de código (opcional)

### Paso 1: Configurar XAMPP
1. Instalar XAMPP desde https://www.apachefriends.org/
2. Iniciar Apache y MySQL desde el panel de control de XAMPP
3. Verificar que Apache esté corriendo en http://localhost

### Paso 2: Configurar la Base de Datos
1. Abrir phpMyAdmin en http://localhost/phpmyadmin
2. Crear una nueva base de datos llamada `betravel_db`
3. Importar el archivo `database.sql`:
   - Seleccionar la base de datos `betravel_db`
   - Ir a la pestaña "Importar"
   - Seleccionar el archivo `database.sql`
   - Hacer clic en "Continuar"

### Paso 3: Configurar los Archivos
1. Copiar todos los archivos del proyecto a la carpeta `htdocs` de XAMPP
   - Ruta típica: `C:\xampp\htdocs\betravel\`
2. Verificar la configuración en `config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'betravel_db');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

### Paso 4: Probar la Instalación
1. Abrir http://localhost/betravel/index.html
2. Navegar por todas las páginas
3. Probar el formulario de contacto
4. Acceder al panel de administración: http://localhost/betravel/admin.php

## Uso del Sistema

### Sitio Web Público

#### Página Principal (index.html)
- Carrusel de servicios interactivo
- Destinos en promoción
- Destinos populares
- Paquetes turísticos especiales
- Footer completo con información de contacto

#### Página de Destinos (destinos.html)
- Lista de destinos populares con imágenes
- Sistema de favoritos (botón corazón)
- Filtros de búsqueda por tipo y presupuesto
- Sección de "Mis Destinos Favoritos"

#### Página de Servicios (servicios.html)
- Servicios principales con iconos
- Diseño en tarjetas
- Información detallada de cada servicio

#### Página de Contacto (contacto.html)
- Formulario completo de contacto
- Información de ubicación y horarios
- Enlaces a redes sociales
- Validación en tiempo real

### Panel de Administración

#### Acceso
- URL: http://localhost/betravel/admin.php
- No requiere autenticación (para demostración)

#### Dashboard
- Resumen de estadísticas generales
- Contactos recientes
- Destinos más populares
- Estado del sistema

#### Gestión de Contactos
- Lista completa de mensajes recibidos
- Estados: Nuevo, En Proceso, Resuelto
- Visualización de mensajes completos
- Estadísticas de contactos

#### Gestión de Favoritos
- Lista de todos los favoritos por usuario
- Estadísticas de popularidad
- Top destinos favoritos
- Análisis por usuario

#### Gestión de Reservas
- Lista completa de reservas
- Estados: Pendiente, Confirmada, Cancelada
- Detalles completos de cada reserva
- Paquetes más populares

#### Estadísticas
- Gráficos de contactos por mes
- Análisis de destinos populares
- Estado de reservas
- Actividad reciente del sitio

## Funcionalidades Técnicas

### Base de Datos
- **contactos**: Almacena mensajes del formulario de contacto
- **destinos_favoritos**: Gestiona favoritos por IP de usuario
- **reservas_paquetes**: Almacena reservas de paquetes turísticos
- **preferencias_usuarios**: Preferencias y configuraciones de usuario
- **estadisticas_web**: Registro de actividad del sitio

### APIs PHP
- **procesar_contacto.php**: Procesa formularios de contacto
- **favoritos.php**: Gestiona operaciones CRUD de favoritos
- **reservas.php**: Gestiona operaciones CRUD de reservas

### JavaScript
- Validación de formularios en tiempo real
- Comunicación AJAX con el backend
- Efectos visuales y animaciones
- Gestión de favoritos dinámicos

## Personalización

### Cambiar Colores
Editar las variables CSS en `styles.css`:
```css
:root {
    --primary-color: #4cc2bc;
    --secondary-color: #2a7f7a;
}
```

### Agregar Nuevos Destinos
1. Editar `destinos.html`
2. Agregar nuevas tarjetas de destino
3. Actualizar las imágenes y descripciones

### Modificar Información de Contacto
Editar los footers en todos los archivos HTML:
- Dirección
- Teléfonos
- Emails
- Horarios

## Solución de Problemas

### Error de Conexión a Base de Datos
1. Verificar que MySQL esté corriendo en XAMPP
2. Comprobar credenciales en `config.php`
3. Verificar que la base de datos `betravel_db` exista

### Formulario de Contacto No Funciona
1. Verificar que Apache esté corriendo
2. Comprobar permisos de archivos PHP
3. Revisar errores en la consola del navegador

### Imágenes No Se Cargan
1. Verificar conexión a internet (usa URLs de Unsplash)
2. Comprobar que las URLs de imágenes sean válidas

### Panel de Administración No Carga
1. Verificar que todos los archivos `admin_*.php` estén presentes
2. Comprobar permisos de archivos
3. Verificar conexión a base de datos

## Mejoras Futuras

### Seguridad
- Implementar sistema de autenticación para admin
- Agregar protección CSRF
- Validación más estricta de datos

### Funcionalidades
- Sistema de usuarios registrados
- Carrito de compras para paquetes
- Integración con pasarelas de pago
- Sistema de notificaciones por email

### Rendimiento
- Cache de consultas frecuentes
- Optimización de imágenes
- Minificación de CSS y JS

## Soporte

Para soporte técnico o consultas sobre el sistema:
- Email: soporte@betravel.com
- Documentación: Este archivo README.md
- Código fuente: Comentado y documentado

## Licencia

Este proyecto es de demostración educativa. Libre para uso y modificación.

---

**BeTravel** - Tu agencia de viajes de confianza
Versión 2.0 - 2025
