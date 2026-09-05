# Plan de desarrollo — Control de Eventos de Sistemas con Bitácora

**Autor:** Luis Andrés García Mendoza
**Actividad Integradora 3** · Aplicación Web con PHP, MySQL y MVC · 25 puntos
**Fecha límite:** 8/9/2026 23:30 (UTC-5) · **Base de datos obligatoria:** `integradora` · **Usuario MySQL:** `root` sin clave

---

## 1. Qué pide la evaluación (checklist de cumplimiento)

| # | Requisito del PDF | Cómo lo cumple este proyecto |
|---|---|---|
| 1 | Estructura MVC (modelo / vista / controlador separados) | Carpetas `models/`, `views/`, `controllers/` + `index.php` como enrutador |
| 2 | Estructura sugerida del proyecto | Se respeta la estructura del PDF y se agrega `sql/` para el script |
| 3 | Base de datos MySQL con mínimo una tabla principal | Tabla principal `eventos` + tabla `bitacora` (auditoría) |
| 4 | Conexión en archivo independiente | `config/conexion.php` (PDO, `root` sin clave, BD `integradora`) |
| 5 | Formulario de registro | `views/eventos/crear.php` |
| 6 | Validaciones con JavaScript (vacíos, numéricos, longitud, valores incorrectos, email) | `js/script.js` antes del `submit` |
| 7 | Registro mediante MVC (controlador recibe → modelo hace INSERT → vista muestra resultado) | `EventoController::guardar()` → `Evento::insertar()` → mensaje en la vista |
| 8 | Consulta de registros en tabla HTML | `views/eventos/listar.php` y `views/bitacora/listar.php` |
| 9 | Interfaz organizada y consistente con la temática | CSS propio (`css/estilos.css`) con Flexbox/Grid, estética de "consola de operaciones" |
| 10 | Código organizado, no todo en un archivo | Un archivo por responsabilidad |
| 11 | GitHub con mínimo 7 commits, cada uno un avance real | Secuencia de 10 commits (sección 7) |
| Opc. | Búsqueda y eliminación | Buscar por sistema/tipo/estado y eliminar con confirmación (ambos quedan en bitácora) |
| Entrega | Todo el proyecto (html, php, css, js) + script SQL | Carpeta `integradora/` en este repo + `sql/integradora.sql` |

---

## 2. Tema y alcance

**Nombre de la aplicación:** *SysTrace — Control de Eventos y Bitácora de Sistemas*

Un equipo de soporte/infraestructura registra los **eventos** que ocurren en los sistemas de una organización
(incidentes, mantenimientos, alertas, cambios, respaldos) y la aplicación mantiene una **bitácora** automática
de todo lo que sucede en la propia herramienta (quién registró, consultó, buscó o eliminó, cuándo y desde qué IP).

Dos conceptos claramente separados:

- **Evento** = el hecho ocurrido en un sistema (lo que el usuario registra por el formulario).
- **Bitácora** = el rastro de auditoría que el sistema escribe solo, sin intervención del usuario.

### Funcionalidades

| Módulo | Funcionalidad | Obligatorio |
|---|---|---|
| Eventos | Registrar un evento (formulario validado con JS) | Sí |
| Eventos | Listar todos los eventos en tabla HTML | Sí |
| Eventos | Buscar por sistema, tipo o estado | Opcional |
| Eventos | Eliminar un evento (con confirmación JS) | Opcional |
| Bitácora | Registro automático de cada acción (alta, consulta, búsqueda, borrado) | Diferenciador del proyecto |
| Bitácora | Consulta de la bitácora en tabla HTML | Diferenciador del proyecto |
| Inicio | Panel con contadores (eventos abiertos, críticos, últimos movimientos) | Deseable si sobra tiempo |

**Fuera de alcance** (para no arriesgar la entrega): login de usuarios, edición de eventos, paginación, exportación.

---

## 3. Arquitectura

### Flujo obligatorio del PDF

```
Vista (formulario) → Controlador → Modelo → MySQL
                ↑                          │
                └──── Vista (resultado) ◄──┘
```

### Enrutador único: `index.php`

Toda petición entra por `index.php?accion=...`. El enrutador usa una **lista blanca** de acciones
(nunca ejecuta lo que llegue en la URL sin validarlo) y delega en el controlador:

| URL | Controlador → método | Modelo | Vista |
|---|---|---|---|
| `index.php` (sin acción) | `EventoController::inicio()` | `Evento::contar()` | `views/inicio.php` |
| `?accion=crear` | `EventoController::crear()` | — | `views/eventos/crear.php` |
| `?accion=guardar` (POST) | `EventoController::guardar()` | `Evento::insertar()` + `Bitacora::registrar()` | redirige a listar con mensaje |
| `?accion=listar` | `EventoController::listar()` | `Evento::obtenerTodos()` + `Bitacora::registrar()` | `views/eventos/listar.php` |
| `?accion=buscar` | `EventoController::buscar()` | `Evento::buscar()` + `Bitacora::registrar()` | `views/eventos/listar.php` |
| `?accion=eliminar` (POST) | `EventoController::eliminar()` | `Evento::eliminar()` + `Bitacora::registrar()` | redirige a listar con mensaje |
| `?accion=bitacora` | `BitacoraController::listar()` | `Bitacora::obtenerTodos()` | `views/bitacora/listar.php` |

### Responsabilidades

- **Modelo** (`models/`): únicamente SQL con PDO y sentencias preparadas. No imprime HTML.
- **Vista** (`views/`): únicamente HTML + PHP de presentación (`foreach`, `htmlspecialchars`). No consulta la BD.
- **Controlador** (`controllers/`): recibe `$_POST`/`$_GET`, valida en servidor, llama al modelo, escribe bitácora, elige la vista.
- **Config** (`config/`): conexión a MySQL en un archivo aparte, como exige el punto 4.

---

## 4. Estructura de carpetas

Se entrega dentro de la carpeta `integradora/` (el PDF permite "crear una carpeta nueva" en el mismo repositorio).

```
integradora/
├── index.php                      # Enrutador (front controller)
├── config/
│   └── conexion.php               # PDO → mysql:host=localhost;dbname=integradora, user root, sin clave
├── controllers/
│   ├── EventoController.php
│   └── BitacoraController.php
├── models/
│   ├── Evento.php
│   └── Bitacora.php
├── views/
│   ├── layout/
│   │   ├── header.php             # <head>, CSS, barra de navegación
│   │   └── footer.php             # cierre, <script src="js/script.js">
│   ├── inicio.php                 # panel de resumen
│   ├── eventos/
│   │   ├── crear.php              # formulario de registro
│   │   └── listar.php             # tabla + buscador + botón eliminar
│   └── bitacora/
│       └── listar.php             # tabla de auditoría
├── css/
│   └── estilos.css
├── js/
│   └── script.js                  # validaciones + confirmación de borrado
├── sql/
│   └── integradora.sql            # CREATE DATABASE + tablas + datos de ejemplo
└── README.md                      # instalación (XAMPP), capturas, uso
```

---

## 5. Modelo de datos (`sql/integradora.sql`)

### Identidad visual

Paleta orientada a tecnología: fondo azul profundo (`#070b14`), acento cian (`#22d3ee`) y violeta (`#8b5cf6`), semáforo verde/ámbar/rojo para severidad y estado. El fondo es una rejilla con brillos radiales hecha solo con CSS (sin imágenes), con tipografía monoespaciada en etiquetas y cifras para reforzar el aire de consola de operaciones.

### Tabla principal: `eventos`

| Columna | Tipo | Regla | Validación JS asociada |
|---|---|---|---|
| `id` | INT AUTO_INCREMENT PK | — | — |
| `sistema` | VARCHAR(60) | obligatorio | vacío, longitud 3–60 |
| `tipo` | ENUM('Incidente','Mantenimiento','Alerta','Cambio','Respaldo') | obligatorio | valor incorrecto (debe ser una opción del select) |
| `severidad` | TINYINT | 1 a 5 | numérico, rango 1–5 |
| `descripcion` | VARCHAR(500) | obligatorio | vacío, longitud 10–500 |
| `responsable` | VARCHAR(80) | obligatorio | vacío, longitud 3–80 |
| `correo_responsable` | VARCHAR(120) | obligatorio | formato de email |
| `tiempo_resolucion_min` | INT UNSIGNED | 0 a 10080 (una semana) | numérico entero, no negativo |
| `estado` | ENUM('Abierto','En proceso','Resuelto','Cerrado') | por defecto 'Abierto' | valor incorrecto |
| `fecha_evento` | DATETIME | obligatorio, no futura | vacío, valor incorrecto (fecha futura) |
| `creado_en` | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | automático | — |

### Tabla de auditoría: `bitacora`

| Columna | Tipo | Descripción |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | — |
| `accion` | ENUM('REGISTRAR','CONSULTAR','BUSCAR','ELIMINAR') | qué se hizo |
| `evento_id` | INT NULL | referencia al evento afectado (NULL en consultas generales) |
| `detalle` | VARCHAR(255) | texto resumido, p. ej. "Evento #7 registrado: Alerta en Servidor BD" |
| `ip_origen` | VARCHAR(45) | `$_SERVER['REMOTE_ADDR']` (soporta IPv6) |
| `agente` | VARCHAR(255) | navegador (`HTTP_USER_AGENT`) recortado |
| `fecha_hora` | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | cuándo |

**Relación:** `bitacora.evento_id` → `eventos.id` con `ON DELETE SET NULL`, para que al eliminar un evento la
bitácora conserve el rastro del borrado (si fuera `CASCADE` se perdería la evidencia, que es justo lo contrario a lo que busca una bitácora).

El script incluye `CREATE DATABASE IF NOT EXISTS integradora` y 5–6 eventos de ejemplo para que la tabla no aparezca vacía en la demostración.

---

## 6. Validaciones

### En JavaScript (`js/script.js`) — requisito 6, antes del `submit`

| Validación exigida | Campo(s) donde se aplica |
|---|---|
| Campos vacíos | sistema, descripción, responsable, correo, fecha |
| Campos numéricos | severidad (1–5), tiempo de resolución (entero ≥ 0) |
| Longitud de datos | sistema (3–60), descripción (10–500 con contador visible), responsable (3–80) |
| Valores incorrectos | tipo y estado fuera de las opciones, fecha futura, severidad fuera de rango |
| Correo electrónico | correo del responsable con expresión regular |

Comportamiento: se marca cada campo inválido en rojo con su mensaje debajo, se bloquea el envío con
`event.preventDefault()` y se hace foco en el primer error. También: confirmación `confirm()` antes de eliminar.

### En PHP (controlador) — defensa en profundidad

Las validaciones de JS se pueden saltar desactivando JavaScript o enviando la petición con `curl`, por eso el
controlador **repite las mismas reglas** con `filter_var`, `trim`, `strlen`, `in_array` sobre listas fijas y
`DateTime::createFromFormat`. Si algo falla, vuelve a mostrar el formulario con el mensaje y los valores escritos.

---

## 7. Seguridad (buenas prácticas que se aplicarán y se documentan en el README)

Aunque la rúbrica no lo exige, es parte del valor del proyecto y de tu perfil:

1. **Inyección SQL:** todo acceso a la BD con PDO y sentencias preparadas (`prepare` + `bindValue`), nunca concatenando `$_POST` en el SQL. La búsqueda usa `LIKE :termino` con el comodín añadido en PHP.
2. **XSS:** toda salida en las vistas pasa por `htmlspecialchars($valor, ENT_QUOTES, 'UTF-8')` mediante un helper `e()`.
3. **CSRF:** token en sesión incluido como campo oculto en los formularios de registro y eliminación; el controlador lo compara con `hash_equals`.
4. **Enrutamiento seguro:** `index.php` solo acepta acciones de una lista blanca; cualquier otra devuelve 404.
5. **Acciones destructivas por POST:** eliminar nunca se hace por `GET` (evita borrados por un simple enlace o prefetch del navegador).
6. **Bitácora inmutable desde la app:** no existe ruta para editar ni borrar registros de la bitácora.
7. **Errores:** `PDO::ERRMODE_EXCEPTION` con `try/catch` en el controlador; al usuario se le muestra un mensaje genérico, nunca la traza SQL.
8. **Cabeceras:** `Content-Type: text/html; charset=UTF-8` y `X-Content-Type-Options: nosniff` desde `index.php`.

---

## 8. Plan de commits y estado

Mínimo 7 commits del PDF; se hicieron 10, cada uno con su propia revisión y pruebas antes de pasar al siguiente.

| # | Commit | Contenido | Pruebas que pasó | Estado |
|---|---|---|---|---|
| 1 | `Creación de estructura inicial del proyecto` | Carpetas MVC, `index.php` con lista blanca, funciones auxiliares, 404 | Sintaxis PHP, inicio 200, acción inválida 404, cabeceras de seguridad | ✅ |
| 2 | `Diseño de interfaz principal` | Layout, inicio, `css/estilos.css` con paleta tecnológica y fondo de rejilla | Render en Chromium, navegación activa, CSS servido | ✅ |
| 3 | `Creación del formulario de registro de eventos` | `views/eventos/crear.php`, `config/catalogos.php`, token CSRF | 10 campos con `name` correcto, opciones de catálogo, token de 64 hex | ✅ |
| 4 | `Agregadas validaciones con JavaScript` | `js/script.js` con reglas por campo, contador, confirmación de borrado | 16 pruebas en Chromium: vacíos, numéricos, longitud, valores incorrectos, correo, envío válido | ✅ |
| 5 | `Configuración de conexión con MySQL` | `config/conexion.php` (PDO) y `sql/integradora.sql` | Importación limpia, reimportación, CHECK de severidad, `ON DELETE SET NULL`, conexión desde PHP | ✅ |
| 6 | `Implementación del modelo y controlador` | `models/Evento.php`, `controllers/Controlador.php`, `EventoController`, enrutador a controladores | 16 pruebas del modelo en transacción (insertar, buscar, eliminar, resumen, inyección SQL), error de BD controlado | ✅ |
| 7 | `Registro y consulta de eventos desde MySQL` | `guardar()` con validación en servidor e INSERT, `listar()` en tabla HTML, mensajes flash | 15 pruebas HTTP: CSRF, GET rechazado, POST inválido repoblado, POST válido, XSS escapado, flash de un solo uso | ✅ |
| 8 | `Bitácora automática y vista de auditoría` | `models/Bitacora.php`, `BitacoraController`, vista, `SET NAMES utf8mb4` en el SQL | Rastro REGISTRAR con IP y navegador, CONSULTAR sin evento, acción fuera de catálogo rechazada, acentos correctos | ✅ |
| 9 | `Búsqueda y eliminación de eventos` | Filtros GET, eliminación POST con CSRF y confirmación, rastros BUSCAR y ELIMINAR | 22 pruebas HTTP + 5 en Chromium: filtros, inyección, GET no borra, token inválido, id inexistente, `confirm()` cancelar/aceptar | ✅ |
| 10 | `Documentación y ajustes finales` | README de instalación con capturas, menú activo en búsqueda, regresión completa | Suite completa sobre base recién importada | ✅ |

**Corrección encontrada por las pruebas:** con sentencias preparadas nativas de MySQL no se puede repetir el mismo marcador (`:termino`) en una consulta; se usaron tres marcadores distintos. El script SQL importado por consola corrompía los acentos; se fijó `SET NAMES utf8mb4` al inicio.

**Reserva:** probar todo desde cero en el XAMPP local (importar el SQL, registrar, buscar, eliminar, ver la bitácora) y subir la entrega en Blackboard antes del lunes 8/9 a las 23:30.

---

## 9. Entorno y pruebas

- **Entorno local:** XAMPP (Apache + PHP 8 + MySQL/MariaDB). Copiar la carpeta `integradora/` a `htdocs/` y abrir `http://localhost/integradora/`.
- **Base de datos:** importar `sql/integradora.sql` en phpMyAdmin o con `mysql -u root < sql/integradora.sql`.
- **Pruebas manuales antes de entregar (checklist):**
  - [ ] Enviar el formulario vacío → JS bloquea y marca los campos.
  - [ ] Severidad `9`, tiempo `-5`, correo `sin-arroba` → JS bloquea cada caso.
  - [ ] Descripción de 501 caracteres → JS bloquea por longitud.
  - [ ] Fecha futura → JS bloquea.
  - [ ] Registro válido → aparece en la tabla y en la bitácora con acción REGISTRAR.
  - [ ] Desactivar JS y enviar datos inválidos → PHP los rechaza (defensa en profundidad).
  - [ ] Buscar "Servidor" → filtra y deja rastro BUSCAR en bitácora.
  - [ ] Eliminar un evento → desaparece de la tabla y queda rastro ELIMINAR con `evento_id` en NULL.
  - [ ] Probar `index.php?accion=loquesea` → 404 controlado.
  - [ ] Escribir `<script>alert(1)</script>` en la descripción → se muestra como texto, no se ejecuta.
  - [ ] Escribir `' OR 1=1 --` en el buscador → no rompe la consulta.

---

## 10. Entregable final

1. Repositorio GitHub público con la carpeta `integradora/` y los 10 commits.
2. Archivo `.zip` de la carpeta `integradora/` (incluye `sql/integradora.sql`) subido a Blackboard (máx. 2.5 MB, sin `node_modules` ni archivos innecesarios).
3. `README.md` con: descripción del tema, estructura MVC, pasos de instalación, capturas del formulario, la tabla de eventos y la bitácora, y el enlace al repositorio.

---

## 11. Riesgos y decisiones

| Riesgo | Mitigación |
|---|---|
| Quedarse sin tiempo (3 días) | Los commits 1–7 cubren el 100 % de lo obligatorio; 8–10 son mejora. Si el domingo no está terminado el 7, se recorta el 9. |
| MySQL con clave en el equipo local | El PDF exige `root` sin clave; dejar las credenciales en `config/conexion.php` tal cual y documentarlo. |
| Turnitin marca código común | Nombres, comentarios y textos de la interfaz redactados con la temática propia (sistemas/bitácora), no copiados del ejemplo de productos. |
| Bitácora vista como "segunda tabla innecesaria" | Se justifica en el README como el aporte diferenciador del proyecto y refuerza la parte de seguridad. |
