-- Creación de la base de datos
DROP DATABASE IF EXISTS eventhub_db;
CREATE DATABASE eventhub_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE eventhub_db;

-- 1. Tabla Usuarios (Para el login)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Se almacenará hash
    rol ENUM('admin', 'usuario') DEFAULT 'usuario',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar usuario admin (Password: admin123) - En producción usar password_hash en PHP
INSERT INTO usuarios (nombre, email, password, rol) VALUES 
('Admin', 'admin@eventhub.com', '$2y$10$YourHashedPasswordHere', 'admin');

-- 2. Tabla Eventos
CREATE TABLE eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    fecha_texto VARCHAR(100), -- Ej: "15-17 de marzo, 2025"
    fecha_inicio DATE, -- Para ordenar
    descripcion_breve TEXT,
    lugar VARCHAR(150),
    horario VARCHAR(100),
    precio DECIMAL(10, 2),
    imagen VARCHAR(255),
    destacado BOOLEAN DEFAULT 1
);

INSERT INTO eventos (titulo, fecha_texto, descripcion_breve, lugar, horario, precio, imagen) VALUES
('Congreso de Tecnología Digital', '15-17 de marzo, 2025', 'Un evento imperdible sobre inteligencia artificial, blockchain y desarrollo web.', 'Centro de Convenciones Madrid', '9:00 - 18:00', 299.00, 'img/eventos/tecnologia.jpg'),
('Seminario de Marketing Digital', '22 de abril, 2025', 'Aprende estrategias reales y efectivas para el crecimiento digital.', 'Hotel Business Center Barcelona', '10:00 - 17:00', 149.00, 'img/eventos/marketing.jpg'),
('Congreso Internacional de Salud', '5-7 de mayo, 2025', 'El evento más importante del año en medicina y telemedicina.', 'Palacio de Congresos Valencia', '8:30 - 19:00', 450.00, 'img/eventos/salud.jpg'),
('Foro Global de Innovación y Startups', '10-12 de junio, 2025', 'Reúne a emprendedores, inversores y líderes del ecosistema.', 'Lisboa Convention Center', '9:30 - 18:30', 320.00, 'img/eventos/startups.jpg'),
('Cumbre de Sostenibilidad', '3-4 de julio, 2025', 'Debate global sobre energías renovables y gestión ambiental.', 'Centro Ecológico de Berlín', '9:00 - 17:30', 200.00, 'img/eventos/sostenibilidad.jpg'),
('Festival de Arte Digital', '18-20 de agosto, 2025', 'Una experiencia inmersiva donde el arte y la tecnología se fusionan.', 'Museo de Arte Contemporáneo', '11:00 - 20:00', 180.00, 'img/eventos/arte-digital.jpg');

-- 3. Tabla Ponentes
CREATE TABLE ponentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    cargo VARCHAR(150),
    tema VARCHAR(200),
    imagen VARCHAR(255)
);

INSERT INTO ponentes (nombre, cargo, tema, imagen) VALUES
('Dr. Carlos Mendoza', 'Consultor en Marketing Digital', 'Tendencias en Publicidad Online 2025', 'img/ponentes/ponente1.jpg'),
('Lic. Ana Torres', 'Especialista en Finanzas', 'Innovación Financiera y Criptomonedas', 'img/ponentes/ponente2.jpg'),
('Ing. Roberto López', 'Director de Innovación', 'Transformación Digital en la Industria', 'img/ponentes/ponente3.jpg'),
('Dra. Lucía Fernández', 'Profesora Investigadora', 'Educación y Tecnología en el Siglo XXI', 'img/ponentes/ponente4.jpg'),
('Dr. Javier Ramos', 'Experto en Ciberseguridad', 'Protección de Datos en la Era de la IA', 'img/ponentes/ponente5.jpg'),
('Lic. Mariana Silva', 'Consultora de Innovación', 'Creatividad y Liderazgo', 'img/ponentes/ponente6.jpg');

-- 4. Tabla Patrocinadores
CREATE TABLE patrocinadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('Principal', 'Oficial', 'Colaborador') NOT NULL,
    sector VARCHAR(100),
    descripcion TEXT,
    imagen VARCHAR(255)
);

INSERT INTO patrocinadores (nombre, tipo, sector, descripcion, imagen) VALUES
('Microsoft', 'Principal', 'Tecnología', 'Líder global en software.', 'img/patrocinadores/microsoft.png'),
('BBVA', 'Principal', 'Finanzas', 'Banco global comprometido con la innovación.', 'img/patrocinadores/bbva.png'),
('Google', 'Principal', 'Tecnología', 'Innovación en búsqueda y nube.', 'img/patrocinadores/google.png'),
('Scotiabank', 'Oficial', 'Banca', 'Soluciones digitales.', 'img/patrocinadores/scotiabank.png'),
('BCP', 'Oficial', 'Banca', 'Banco líder del Perú.', 'img/patrocinadores/bcp.png'),
('Meta', 'Oficial', 'Redes Sociales', 'Conexión social global.', 'img/patrocinadores/facebook.png'),
('AWS', 'Oficial', 'Cloud', 'Servicios en la nube.', 'img/patrocinadores/aws.png'),
('IBM', 'Oficial', 'Tecnología', 'IA y computación cuántica.', 'img/patrocinadores/ibm.png'),
('Intel', 'Oficial', 'Hardware', 'Líder en procesadores.', 'img/patrocinadores/intel.png'),
('Twitch', 'Colaborador', NULL, NULL, 'img/patrocinadores/twitch.png'),
('Twitter', 'Colaborador', NULL, NULL, 'img/patrocinadores/twitter.png'),
('LinkedIn', 'Colaborador', NULL, NULL, 'img/patrocinadores/linkedin.png'),
('Spotify', 'Colaborador', NULL, NULL, 'img/patrocinadores/spotify.png'),
('Adobe', 'Colaborador', NULL, NULL, 'img/patrocinadores/adobe.png'),
('Cisco', 'Colaborador', NULL, NULL, 'img/patrocinadores/cisco.png'),
('Oracle', 'Colaborador', NULL, NULL, 'img/patrocinadores/oracle.png'),
('Samsung', 'Colaborador', NULL, NULL, 'img/patrocinadores/samsung.png');

-- 5. Tabla Noticias
CREATE TABLE noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    fecha_publicacion DATE DEFAULT CURRENT_DATE,
    resumen TEXT,
    contenido_completo TEXT,
    imagen VARCHAR(255),
    es_destacada BOOLEAN DEFAULT 0
);

INSERT INTO noticias (titulo, fecha_publicacion, resumen, imagen, es_destacada) VALUES
('Innovación educativa en el Congreso 2025', '2025-10-20', 'El Congreso presentó nuevas tendencias sobre metodologías digitales.', 'img/noticias/destacada.jpg', 1),
('Seminario sobre IA aplicada', '2025-10-14', 'Expertos analizaron cómo las tecnologías de IA revolucionan la enseñanza.', 'img/noticias/n1.jpg', 0),
('Éxito total en el Congreso de Innovación', '2025-10-05', 'Más de 1,200 emprendedores se reunieron para compartir estrategias.', 'img/noticias/n2.jpg', 0),
('Foro Internacional de Emprendimiento', '2025-09-25', 'Se presentaron proyectos innovadores centrados en sostenibilidad.', 'img/noticias/n3.jpg', 0);