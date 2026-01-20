<p align="center"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="SIGMA Logo"></p>

<h1 align="center">SIGMA - Sistema de Gestión de Activos TI</h1>

<p align="center">
    <a href="https://laravel.com"><img src="https://img.shields.io/badge/Framework-Laravel-FF2D20?style=flat&logo=laravel" alt="Laravel"></a>
    <a href="#"><img src="https://img.shields.io/badge/Status-MVP%20Desplegado-success" alt="Status"></a>
    <a href="#"><img src="https://img.shields.io/badge/Version-1.0.0-blue" alt="Version"></a>
</p>

## 📋 Sobre SIGMA

**SIGMA** es una plataforma integral diseñada para la administración eficiente de infraestructura tecnológica y recursos humanos. Su objetivo principal es mantener el control total sobre el inventario de equipos electrónicos, su ciclo de vida y su asignación a los colaboradores de la organización.

Este sistema centraliza la información para facilitar la toma de decisiones por parte del departamento de TI y la gerencia.

## 🚀 Características del MVP

La versión actual (MVP) incluye los siguientes módulos operativos:

### 1. 📦 Gestión de Activos (Inventario)
Control detallado de todo el equipo electrónico:
* **Ciclo de Vida:** Monitoreo de estados en tiempo real (Disponible, En Uso, En Mantenimiento, Retirado).
* **Trazabilidad:** Historial de asignaciones y cambios de estado.

### 2. 👥 Gestión de Empleados
Administración de la plantilla laboral para asignación de recursos:
* **Expediente Digital:** Gestión de documentos relacionados con SIGMA.
* **Gestión de Perfiles:** Operaciones CRUD completas y optimización de almacenamiento (compresión de fotografías).

### 3. 🚨 Mesa de Ayuda (Reportes)
Canal directo para el mantenimiento operativo:
* Generación de reportes de alta prioridad por fallas en equipos.
* Notificaciones directas al departamento de TI.

### 4. ⚙️ Administración y Seguridad
Módulo de configuración para Super Administradores:
* **Gestión de Usuarios y Roles:** Control de acceso basado en permisos (RBAC).
* **Catálogos Dinámicos:** Edición total de los catálogos del sistema sin necesidad de tocar código.

## 🛠️ Stack Tecnológico

* **Framework:** Laravel (PHP)
* **Base de Datos:** MySQL / MariaDB
* **Frontend:** Blade / JavaScript

## 🔧 Instalación y Configuración Local

Si deseas levantar el proyecto en un entorno local para desarrollo o pruebas:

1.  **Clonar el repositorio**
    ```bash
    git clone [https://github.com/tu-usuario/sigma.git](https://github.com/tu-usuario/sigma.git)
    cd sigma
    ```

2.  **Instalar dependencias**
    ```bash
    composer install
    npm install && npm run build
    ```

3.  **Configurar entorno**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Base de datos**
    Asegúrate de configurar tus credenciales en el archivo `.env` y ejecuta:
    ```bash
    php artisan migrate --seed
    ```

5.  **Ejecutar servidor**
    ```bash
    php artisan serve
    ```

## 📄 Licencia

Este es un software propietario desarrollado para uso interno. Todos los derechos reservados.

---
<p align="center">Desarrollado con ❤️ para el control eficiente de TI.</p>
