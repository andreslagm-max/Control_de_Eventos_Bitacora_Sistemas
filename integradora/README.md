# SysTrace · Control de Eventos y Bitácora de Sistemas

**Autor:** Luis Andrés García Mendoza · Actividad Integradora 3

Aplicación web en PHP y MySQL con arquitectura MVC para registrar los eventos
que ocurren en los sistemas de una organización y mantener una bitácora
automática de cada acción realizada.

## Estructura

```
integradora/
├── index.php          Enrutador único (front controller)
├── config/            Conexión a MySQL y funciones auxiliares
├── controllers/       Controladores (reciben la petición, coordinan)
├── models/            Modelos (acceso a la base de datos)
├── views/             Vistas (HTML)
├── css/               Estilos
├── js/                Validaciones y comportamiento del cliente
└── sql/               Script de la base de datos `integradora`
```
