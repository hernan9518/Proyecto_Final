# Proyecto_Final

# Integrantes:
# Brayan Perenguez
# Steven Velásquez
# Sebastian Barahona

Sistema Web de Reportes de Infraestructura — Universidad de Nariño
¿Qué es?
Una plataforma web desarrollada por estudiantes de Ingeniería de Sistemas de la Universidad de Nariño, que permite a la comunidad universitaria (estudiantes, docentes y personal administrativo) reportar problemas de infraestructura dentro del campus de forma organizada y centralizada.
¿Para qué sirve?
Antes de este sistema, los problemas como luminarias dañadas, proyectores fallidos o conexiones de red caídas se reportaban de manera informal, lo que hacía difícil su seguimiento y solución. Esta plataforma centraliza esos reportes y permite hacerles seguimiento hasta que sean resueltos.
Archivos del proyecto
ArchivoFunciónauth.htmlPágina de login y registroauth.jsLógica de autenticaciónindex.htmlDashboard principal (reportes)script.jsLógica del dashboardstyle.cssEstilos compartidos de toda la app
Tecnologías usadas
La interfaz está construida con HTML, CSS y JavaScript puro. La base de datos y autenticación de usuarios se manejan con Supabase, que provee una tabla llamada reportes donde se almacenan las incidencias.
¿Cómo funciona?
El usuario entra por auth.html, inicia sesión o crea una cuenta. Si ya tiene sesión activa, el sistema lo lleva directamente al dashboard. Si intenta entrar a index.html sin sesión, es redirigido al login automáticamente.
Una vez dentro, puede ver el listado de todos los reportes registrados con sus estadísticas (total, pendientes, en proceso, resueltos), filtrar por estado desde la barra lateral, y registrar nuevos reportes desde la sección "Nuevo Reporte".
Campos de un reporte
Cada reporte registra el tipo de problema (luminarias, proyector, red, baños, mobiliario, infraestructura civil, equipos de cómputo, instalación eléctrica u otro), la ubicación dentro del campus, una descripción detallada, la prioridad (baja, media o alta) y el nombre de quien reporta (opcional). El estado inicial siempre es "Pendiente".
Estructura de la tabla en Supabase
La tabla reportes debe tener las columnas: id, tipo, ubicacion, descripcion, prioridad, estado, reportado_por y created_at.
