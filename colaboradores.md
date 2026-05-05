# 🤝 Guía para Colaboradores - Kiosko UNER

¡Bienvenido al equipo de desarrollo de **Kiosko UNER**! Esta guía detalla los pasos necesarios para configurar tu entorno local, trabajar en equipo y contribuir al proyecto de manera organizada y profesional.

---

## 🚀 1. Configuración Inicial del Entorno

Si es la primera vez que te unes al proyecto, sigue estos pasos para dejar todo listo:

### 📥 1.1 Clonar el Repositorio
Abre tu terminal y ejecuta:
```bash
git clone <URL_DEL_REPOSITORIO>
cd Kiosko-Uner
```

### 👤 1.2 Configurar Identidad (Si es necesario)
Si es tu primera vez usando Git en esta PC, identifícate:
```bash
git config --global user.name "Tu Nombre"
git config --global user.email "tu@email.com"
```

### 📦 1.3 Instalar Dependencias
Asegúrate de tener instalados **Composer** y **Node.js** en tu sistema.
```bash
composer install
npm install
```

### ⚙️ 1.3 Configuración de Variables de Entorno
Copia el archivo de ejemplo para crear tu configuración local y genera la clave de seguridad:
```bash
cp .env.example .env
php artisan key:generate
```

> [!IMPORTANT]
> **Configuración de Base de Datos:** Abre el archivo `.env` recién creado y configura los parámetros de tu base de datos local:
> - `DB_DATABASE=kiosko_uner`
> - `DB_USERNAME=tu_usuario`
> - `DB_PASSWORD=tu_contraseña`

### 🗄️ 1.4 Migraciones y Datos de Prueba
Crea la estructura de tablas y carga los datos iniciales necesarios:
```bash
php artisan migrate --seed
```

### 🌐 1.5 Iniciar la Aplicación
Para ver el proyecto en funcionamiento, necesitas dos terminales corriendo:
- **Terminal 1 (Backend):** `php artisan serve`
- **Terminal 2 (Frontend/Vite):** `npm run dev`

---

## 🛠️ 2. Flujo de Trabajo (Git Flow)

Para mantener el orden y evitar errores en el código principal, sigue estos comandos:

### 🌿 2.1 Crear una Rama de Trabajo
**Nunca** trabajes directamente en `main`. Crea una rama específica para lo que vas a desarrollar:
```bash
git checkout -b feature/nombre-de-tu-tarea
```
*(Ejemplo: `feature/ajuste-estilos-login`)*

### 💾 2.2 Guardar y Confirmar Cambios
Una vez que realices avances, guarda tus cambios localmente:
```bash
git add .
git commit -m "feat: descripción breve de la mejora o cambio"
```
> [!TIP]
> Usa prefijos descriptivos en tus commits:
> - `feat:` para nuevas funcionalidades.
> - `fix:` para corregir errores.
> - `style:` para cambios visuales que no afectan la lógica.

### 📤 2.3 Subir Cambios al Servidor
Envía tu rama al repositorio remoto para que otros puedan verla:
```bash
git push origin tu-nombre | nombre-de-tu-tarea
```

---

## 🔀 3. Integración (Merge) y Pull Requests

Cuando tu tarea esté terminada y subida:

1. **Crear Pull Request (PR):** Entra a GitHub/GitLab y abre un PR desde tu rama hacia `main`.
2. **Revisión:** Un compañero revisará tu código. Si hay observaciones, corrígelas en tu misma rama y vuelve a hacer `push`.
3. **Merge:** Una vez aprobado, el administrador realizará el merge para integrar tus cambios al proyecto raíz.

---

## ✅ Buenas Prácticas
- **Sincronización:** Antes de empezar a trabajar cada día, actualiza tu rama local:
  ```bash
  git checkout main
  git pull origin main
  ```
- **Seguridad:** Jamás compartas o subas tu archivo `.env`.
- **Calidad:** Revisa que no queden `dd()`, `console.log()` o comentarios de prueba en el código final.

---
*Guía generada para el equipo de Kiosko UNER - 2026*
