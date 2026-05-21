# Credenciales de Acceso — Usuarios de Prueba (Kiosko-Uner)

Este documento detalla todas las cuentas de prueba creadas mediante los seeders del sistema. Contiene usuarios de prueba para todos los roles de la aplicación (`superadmin`, `alumno`, `profesor`, `directivo`) con contraseñas fáciles y rápidas de tipear.

---

## 1. Administradores del Sistema (`superadmin`)

| Nombre completo | Correo Electrónico | Nombre de Usuario | Rol | Contraseña |
| :--- | :--- | :--- | :--- | :--- |
| **Super Admin** | `superadmin@kiosko.uner` | `superadmin` | `superadmin` | `superadmin` |

---

## 2. Alumnos de Prueba (`alumno`)

Cuentas destinadas al panel principal del Kiosco (POS, Ingresos, Egresos, Productos). Todas estas cuentas tienen perfiles vinculados automáticamente en la tabla `personnel`.

| Nombre completo | DNI | Teléfono | Correo Electrónico | Nombre de Usuario | Contraseña |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Lucas Gómez** | `42000001` | `3434-000001` | `alumno1@kiosko.uner` | `alumno1` | `alumno1` |
| **Sofía Rodríguez** | `42000002` | `3434-000002` | `alumno2@kiosko.uner` | `alumno2` | `alumno2` |
| **Mateo Fernández** | `42000003` | `3434-000003` | `alumno3@kiosko.uner` | `alumno3` | `alumno3` |
| **Valentina Silva** | `42000004` | `3434-000004` | `alumno4@kiosko.uner` | `alumno4` | `alumno4` |
| **Thiago Díaz** | `42000005` | `3434-000005` | `alumno5@kiosko.uner` | `alumno5` | `alumno5` |
| **Camila Alvarez** | `42000006` | `3434-000006` | `alumno6@kiosko.uner` | `alumno6` | `alumno6` |
| **Lautaro Romero** | `42000007` | `3434-000007` | `alumno7@kiosko.uner` | `alumno7` | `alumno7` |
| **Isabella González** | `42000008` | `3434-000008` | `alumno8@kiosko.uner` | `alumno8` | `alumno8` |
| **Benjamín Medina** | `42000009` | `3434-000009` | `alumno9@kiosko.uner` | `alumno9` | `alumno9` |
| **Martina Flores** | `42000010` | `3434-000010` | `alumno10@kiosko.uner` | `alumno10` | `alumno10` |

---

## 3. Profesores de Prueba (`profesor`)

| Nombre completo | DNI | Teléfono | Correo Electrónico | Nombre de Usuario | Contraseña |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Carlos Pérez** | `35000001` | `3434-100001` | `profesor1@kiosko.uner` | `profesor1` | `profesor1` |
| **Ana Martínez** | `35000002` | `3434-100002` | `profesor2@kiosko.uner` | `profesor2` | `profesor2` |
| **Jorge Sánchez** | `35000003` | `3434-100003` | `profesor3@kiosko.uner` | `profesor3` | `profesor3` |

---

## 4. Directivos de Prueba (`directivo`)

| Nombre completo | DNI | Teléfono | Correo Electrónico | Nombre de Usuario | Contraseña |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Clara Benítez** | `28000001` | `3434-200001` | `directivo1@kiosko.uner` | `directivo1` | `directivo1` |
| **Eduardo Castro** | `28000002` | `3434-200002` | `directivo2@kiosko.uner` | `directivo2` | `directivo2` |

---

> [!NOTE]
> Todos estos usuarios han sido inyectados con contraseñas que coinciden con su respectivo **nombre de usuario** para agilizar las pruebas y optimizar el proceso de inicio de sesión durante la fase de desarrollo. El sistema utiliza hashing a nivel de modelo para almacenar estas contraseñas de forma segura en la base de datos local SQLite.
