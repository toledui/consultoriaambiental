<?php
/**
 * Blog Posts Seeder
 * 
 * Run: php seed_blog_posts.php
 * Inserts 4 sample blog posts with stock images from Unsplash.
 */

require_once __DIR__ . '/config/init.php';

use App\Models\BlogPost;
use App\Models\BlogCategory;

// Get category IDs
$categories = BlogCategory::getAll();
$catMap = [];
foreach ($categories as $cat) {
    $catMap[$cat['slug']] = (int)$cat['id'];
}

$posts = [
    [
        'title'            => 'Nueva Normativa SEMARNAT 2026: Lo que tu empresa debe saber',
        'slug'             => 'nueva-normativa-semarnat-2026',
        'excerpt'          => 'La SEMARNAT ha publicado nuevas disposiciones que afectan a los sectores industrial, inmobiliario y de servicios. Conoce los plazos, requisitos y cómo preparar tu empresa para cumplir con la normativa actualizada.',
        'content'          => '<h2>¿Qué cambios trae la nueva normativa?</h2>
<p>La Secretaría de Medio Ambiente y Recursos Naturales (SEMARNAT) ha publicado en el Diario Oficial de la Federación una serie de modificaciones a la normativa ambiental que entrarán en vigor durante 2026. Estos cambios impactan directamente en la operación de empresas industriales, desarrollos inmobiliarios y prestadores de servicios.</p>

<h3>Principales modificaciones</h3>
<ul>
<li><strong>Actualización de la NOM-001-SEMARNAT-2021:</strong> Se refuerzan los límites máximos permisibles de contaminantes en descargas de aguas residuales.</li>
<li><strong>Nuevos requisitos para Manifestaciones de Impacto Ambiental (MIA):</strong> Se exige mayor detalle en la línea base ambiental y en los planes de manejo de residuos.</li>
<li><strong>Digitalización de trámites:</strong> A partir de junio de 2026, todos los trámites ambientales deberán iniciarse a través de la Ventanilla Digital Ambiental.</li>
</ul>

<h3>Plazos clave</h3>
<p>Las empresas tienen un plazo de 180 días naturales a partir de la publicación para actualizar sus programas de cumplimiento. Recomendamos iniciar el diagnóstico cuanto antes para evitar contratiempos.</p>

<h3>¿Cómo preparar tu empresa?</h3>
<ol>
<li>Realizar una auditoría ambiental interna para identificar brechas de cumplimiento.</li>
<li>Actualizar los planes de manejo de residuos y programas de contingencia.</li>
<li>Capacitar al personal responsable en materia ambiental.</li>
<li>Contactar a un consultor especializado para la gestión de trámites.</li>
</ol>

<p>En Consultoría Ambiental te acompañamos en todo el proceso de actualización normativa. Contáctanos para un diagnóstico gratuito.</p>',
        'featured_image'   => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
        'published'        => 1,
        'category_id'      => $catMap['normatividad-ambiental'] ?? null,
        'meta_title'       => 'Nueva Normativa SEMARNAT 2026 | Consultoría Ambiental',
        'meta_description' => 'Conoce las nuevas disposiciones de SEMARNAT para 2026: plazos, requisitos y cómo preparar tu empresa para el cumplimiento ambiental.',
    ],
    [
        'title'            => 'Guía Completa para la Gestión de Residuos de Manejo Especial en Jalisco',
        'slug'             => 'gestion-residuos-manejo-especial-jalisco',
        'excerpt'          => 'La gestión de residuos de manejo especial es una obligación legal para las empresas en Jalisco. Te explicamos paso a paso cómo cumplir con la normativa estatal y evitar sanciones.',
        'content'          => '<h2>¿Qué son los residuos de manejo especial?</h2>
<p>Los residuos de manejo especial son aquellos generados en procesos productivos que no están clasificados como peligrosos ni como residuos sólidos urbanos. En Jalisco, la Secretaría de Medio Ambiente y Desarrollo Territorial (SEMADET) es la autoridad encargada de regular su gestión.</p>

<h3>Ejemplos comunes</h3>
<ul>
<li>Residuos de la construcción y demolición</li>
<li>Lodos de plantas de tratamiento</li>
<li>Residuos agroindustriales (bagazo, pulpas, etc.)</li>
<li>Neumáticos usados</li>
<li>Residuos de la industria tequilera (vinazas, bagazo de agave)</li>
</ul>

<h3>Obligaciones del generador</h3>
<p>Todo generador de residuos de manejo especial debe:</p>
<ol>
<li>Registrarse ante SEMADET como generador.</li>
<li>Elaborar un Plan de Manejo de Residuos.</li>
<li>Llevar una bitácora mensual de generación.</li>
<li>Contratar servicios de recolección con empresas autorizadas.</li>
<li>Presentar informes semestrales y anuales.</li>
</ol>

<h3>Sanciones por incumplimiento</h3>
<p>Las multas por no cumplir con la normativa pueden alcanzar hasta 50,000 UMAS (aproximadamente $5 millones de pesos). Además, la autoridad puede clausurar temporal o definitivamente las instalaciones.</p>

<p>En Consultoría Ambiental te ayudamos a regularizar tu situación. Realizamos diagnósticos, elaboramos planes de manejo y gestionamos los registros ante SEMADET.</p>',
        'featured_image'   => 'https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
        'published'        => 1,
        'category_id'      => $catMap['gestion-de-residuos'] ?? null,
        'meta_title'       => 'Guía Gestión Residuos Manejo Especial Jalisco | Consultoría Ambiental',
        'meta_description' => 'Guía completa para la gestión de residuos de manejo especial en Jalisco. Obligaciones, registros, planes de manejo y sanciones. Cumple con SEMADET.',
    ],
    [
        'title'            => 'Beneficios Fiscales por Prácticas de Economía Circular en tu Empresa',
        'slug'             => 'beneficios-fiscales-economia-circular',
        'excerpt'          => 'Implementar prácticas de economía circular no solo beneficia al medio ambiente, también puede generar importantes ahorros fiscales para tu empresa. Descubre los incentivos disponibles.',
        'content'          => '<h2>¿Qué es la economía circular?</h2>
<p>La economía circular es un modelo de producción y consumo que implica compartir, alquilar, reutilizar, reparar, renovar y reciclar materiales y productos existentes todas las veces que sea posible para crear un valor añadido. De esta forma, el ciclo de vida de los productos se extiende.</p>

<h3>Incentivos fiscales vigentes</h3>
<p>El gobierno mexicano, a través del SAT y en coordinación con SEMARNAT, ha establecido diversos estímulos fiscales para empresas que adopten prácticas de economía circular:</p>

<ul>
<li><strong>Deducción de inversiones en equipos de reciclaje y tratamiento:</strong> Hasta el 100% de la inversión en maquinaria para el tratamiento de residuos puede ser deducible en el primer año.</li>
<li><strong>Crédito fiscal por certificaciones ambientales:</strong> Las empresas que obtengan certificaciones como ISO 14001 o el Programa de Cumplimiento Ambiental Voluntario de SEMADET pueden acceder a créditos fiscales.</li>
<li><strong>Exención de IEPS en combustibles alternos:</strong> El uso de combustibles derivados de residuos (CDR) puede estar exento del Impuesto Especial sobre Producción y Servicios.</li>
</ul>

<h3>Casos de éxito</h3>
<p>Empresas del sector tequilero en Jalisco han logrado reducir hasta un 40% sus costos operativos al implementar sistemas de reciclaje de vinazas y bagazo de agave, transformándolos en composta y biocombustibles.</p>

<h3>¿Cómo empezar?</h3>
<ol>
<li>Realiza un diagnóstico de residuos y oportunidades de circularidad.</li>
<li>Identifica qué materiales pueden ser reincorporados a tu proceso productivo.</li>
<li>Evalúa la viabilidad técnica y económica de las alternativas.</li>
<li>Implementa un sistema de gestión ambiental con enfoque de economía circular.</li>
</ol>

<p>En Consultoría Ambiental te asesoramos para identificar y aprovechar estos beneficios. Solicita una consultoría especializada.</p>',
        'featured_image'   => 'https://images.unsplash.com/photo-1569163139599-0f4517e36f51?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
        'published'        => 1,
        'category_id'      => $catMap['sostenibilidad'] ?? null,
        'meta_title'       => 'Beneficios Fiscales Economía Circular | Consultoría Ambiental',
        'meta_description' => 'Descubre los incentivos fiscales por implementar economía circular en tu empresa: deducciones, créditos fiscales y exenciones. Ahorra mientras cuidas el medio ambiente.',
    ],
    [
        'title'            => 'Inspecciones de PROEPA: Guía de Preparación y Respuesta Legal',
        'slug'             => 'inspecciones-proepa-preparacion-respuesta-legal',
        'excerpt'          => 'Recibir una visita de inspección de PROEPA puede ser intimidante. Te explicamos tus derechos, obligaciones y cómo prepararte para salir bien librado de una auditoría ambiental.',
        'content'          => '<h2>¿Qué es PROEPA?</h2>
<p>La Procuraduría Estatal de Protección al Ambiente (PROEPA) es el organismo encargado de vigilar el cumplimiento de la normativa ambiental en el estado de Jalisco. Realiza visitas de inspección tanto programadas como derivadas de denuncias ciudadanas.</p>

<h3>Tipos de inspección</h3>
<ul>
<li><strong>Inspección ordinaria:</strong> Programada por la autoridad como parte de sus labores de vigilancia rutinaria.</li>
<li><strong>Inspección extraordinaria:</strong> Derivada de una denuncia, accidente ambiental o solicitud expresa de la autoridad.</li>
<li><strong>Visita de verificación:</strong> Para constatar el cumplimiento de medidas correctivas o condicionantes de una resolución.</li>
</ul>

<h3>Derechos del inspeccionado</h3>
<p>Durante una inspección, tienes derecho a:</p>
<ol>
<li>Exigir que los inspectores se identifiquen con credencial vigente.</li>
<li>Solicitar la orden de inspección y verificar que esté debidamente fundada y motivada.</li>
<li>Contar con asesoría legal y técnica durante todo el proceso.</li>
<li>No declarar en tu contra (derecho a guardar silencio).</li>
<li>Firmar el acta con observaciones si no estás de acuerdo con lo asentado.</li>
</ol>

<h3>Consecuencias de una inspección desfavorable</h3>
<p>Si se detectan irregularidades, PROEPA puede imponer desde multas económicas hasta la clausura parcial o total de las instalaciones. Las multas pueden oscilar entre 20 y 50,000 UMAS dependiendo de la gravedad.</p>

<h3>Recomendaciones</h3>
<ul>
<li>Mantén actualizada tu documentación ambiental (registros, bitácoras, planes de manejo).</li>
<li>Designa a un responsable ambiental que acompañe a los inspectores.</li>
<li>No obstruyas la labor de inspección, pero ejerce tus derechos.</li>
<li>Contacta a un consultor especializado para que te asesore durante el proceso.</li>
</ul>

<p>En Consultoría Ambiental contamos con amplia experiencia en atención a inspecciones de PROEPA. Hemos logrado reducciones de multa de hasta el 100% para nuestros clientes. Contáctanos para recibir asesoría.</p>',
        'featured_image'   => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
        'published'        => 1,
        'category_id'      => $catMap['cumplimiento-legal'] ?? null,
        'meta_title'       => 'Guía Inspecciones PROEPA | Consultoría Ambiental',
        'meta_description' => 'Guía completa para prepararte ante una inspección de PROEPA. Conoce tus derechos, obligaciones y cómo responder legalmente para evitar sanciones.',
    ],
];

$inserted = 0;
foreach ($posts as $post) {
    try {
        $id = BlogPost::createPost($post);
        echo "✓ Insertado: {$post['title']} (ID: {$id})\n";
        $inserted++;
    } catch (\Exception $e) {
        echo "✗ Error al insertar '{$post['title']}': {$e->getMessage()}\n";
    }
}

echo "\n---\nTotal: {$inserted} de " . count($posts) . " posts insertados correctamente.\n";
