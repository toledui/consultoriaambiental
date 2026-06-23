<?php

namespace App\Models;

class ServiceCatalog
{
    public static function all(): array
    {
        return array_values(self::services());
    }

    public static function navigation(): array
    {
        return array_map(static function (array $service): array {
            return [
                'title'       => $service['title'],
                'slug'        => $service['slug'],
                'description' => $service['description'],
                'icon'        => $service['icon'],
                'sort_order'  => $service['sort_order'],
            ];
        }, self::all());
    }

    public static function find(string $slug): ?array
    {
        $canonicalSlug = self::aliases()[$slug] ?? $slug;
        $services = self::services();

        return $services[$canonicalSlug] ?? null;
    }

    public static function related(string $slug, int $limit = 3): array
    {
        $service = self::find($slug);
        if (!$service) {
            return array_slice(self::navigation(), 0, $limit);
        }

        $services = self::services();
        $related = [];

        foreach ($service['related'] as $relatedSlug) {
            if (isset($services[$relatedSlug])) {
                $related[] = $services[$relatedSlug];
            }
        }

        foreach ($services as $candidateSlug => $candidate) {
            if ($candidateSlug === $service['slug']) {
                continue;
            }
            if (count($related) >= $limit) {
                break;
            }
            if (!in_array($candidateSlug, array_column($related, 'slug'), true)) {
                $related[] = $candidate;
            }
        }

        return array_map(static function (array $relatedService): array {
            return [
                'title'       => $relatedService['title'],
                'slug'        => $relatedService['slug'],
                'description' => $relatedService['description'],
                'icon'        => $relatedService['icon'],
            ];
        }, array_slice($related, 0, $limit));
    }

    private static function aliases(): array
    {
        return [
            'ley-general-de-economia-circular-lgec' => 'ley-general-de-economa-circular-lgec',
        ];
    }

    private static function services(): array
    {
        return [
            'gestion-de-residuos' => [
                'title'            => 'Gestión de Residuos',
                'slug'             => 'gestion-de-residuos',
                'sort_order'       => 1,
                'eyebrow'          => 'Residuos de manejo especial y peligrosos',
                'description'      => 'Diagnóstico, regularización, planes de manejo y evidencia operativa para residuos generados por empresas, industrias y comercios.',
                'meta_title'       => 'Gestión de Residuos para Empresas | Consultoría Ambiental',
                'meta_description' => 'Regularización de residuos, planes de manejo, registros, bitácoras y seguimiento documental para empresas e industrias.',
                'icon'             => 'fas fa-recycle',
                'hero_image'       => 'images/nosotros/gestion de residuos.webp',
                'hero_alt'         => 'Área de gestión de residuos empresariales',
                'accent'           => '#2E7D32',
                'soft_accent'      => '#EEF7EF',
                'intro'            => [
                    'Ordenamos la gestión de residuos desde la generación hasta la evidencia documental que solicita la autoridad.',
                    'El servicio se adapta al tipo de residuo, volumen, giro y ubicación de la empresa para construir una ruta de cumplimiento práctica.',
                ],
                'metrics'          => [
                    ['value' => '01', 'label' => 'Ruta de cumplimiento', 'detail' => 'Diagnóstico, trámites y control operativo.'],
                    ['value' => 'RME / RP', 'label' => 'Clasificación', 'detail' => 'Manejo especial, peligrosos o urbanos.'],
                    ['value' => '360°', 'label' => 'Evidencia', 'detail' => 'Bitácoras, manifiestos, reportes y seguimiento.'],
                ],
                'highlights'       => [
                    ['icon' => 'fas fa-clipboard-list', 'title' => 'Diagnóstico de generación', 'body' => 'Identificamos fuentes, volúmenes, almacenamiento, recolección y brechas documentales.'],
                    ['icon' => 'fas fa-file-signature', 'title' => 'Plan y registros', 'body' => 'Preparamos trámites, planes de manejo y expedientes para la autoridad correspondiente.'],
                    ['icon' => 'fas fa-route', 'title' => 'Operación trazable', 'body' => 'Estandarizamos controles internos para que cada salida de residuo tenga respaldo verificable.'],
                ],
                'deliverables'     => [
                    'Diagnóstico de residuos por área y proceso.',
                    'Clasificación por tipo de residuo y obligación aplicable.',
                    'Plan de manejo o expediente de regularización.',
                    'Formatos de bitácora, manifiestos y evidencia fotográfica.',
                    'Recomendaciones de almacenamiento, señalización y proveedores autorizados.',
                ],
                'process'          => [
                    ['title' => 'Levantamiento', 'body' => 'Revisamos procesos, áreas de generación, almacenes temporales y documentación existente.'],
                    ['title' => 'Clasificación', 'body' => 'Determinamos el tipo de residuo, el volumen y las obligaciones por autoridad.'],
                    ['title' => 'Regularización', 'body' => 'Integramos trámites, planes y registros necesarios para cerrar brechas.'],
                    ['title' => 'Implementación', 'body' => 'Acompañamos señalización, bitácoras, segregación y control documental.'],
                ],
                'compliance'       => [
                    'Registro o actualización como generador.',
                    'Plan de manejo cuando aplique.',
                    'Bitácoras y manifiestos ordenados.',
                    'Evidencia para inspecciones ambientales.',
                ],
                'cta'              => [
                    'title' => 'Regulariza la gestión de residuos de tu empresa',
                    'body'  => 'Revisamos tu operación y te entregamos una ruta clara para reducir riesgos, multas y observaciones.',
                ],
                'related'          => [
                    'ley-general-de-economa-circular-lgec',
                    'certificacion-ambiental-empresarial',
                    'estudios-de-impacto-ambiental',
                ],
            ],
            'emisiones-a-la-atmosfera' => [
                'title'            => 'Emisiones a la Atmósfera',
                'slug'             => 'emisiones-a-la-atmosfera',
                'sort_order'       => 2,
                'eyebrow'          => 'Fuentes fijas, reportes y control ambiental',
                'description'      => 'Gestión documental y técnica para fuentes fijas, emisiones, licencia ambiental, reportes y obligaciones atmosféricas aplicables.',
                'meta_title'       => 'Emisiones a la Atmósfera | LAU, COA y Cumplimiento Ambiental',
                'meta_description' => 'Consultoría para emisiones atmosféricas, fuentes fijas, licencia ambiental, COA, mediciones y regularización de obligaciones.',
                'icon'             => 'fas fa-industry',
                'hero_image'       => 'images/impacto ambiental imagen2.webp',
                'hero_alt'         => 'Instalación industrial con control de emisiones',
                'accent'           => '#0F766E',
                'soft_accent'      => '#EAF7F5',
                'intro'            => [
                    'Aterrizamos las obligaciones de emisiones para que la empresa sepa qué medir, qué reportar y qué permisos sostienen su operación.',
                    'El enfoque combina revisión documental, análisis de fuentes y coordinación con laboratorios o proveedores especializados cuando se requiere medición.',
                ],
                'metrics'          => [
                    ['value' => 'LAU', 'label' => 'Licencia ambiental', 'detail' => 'Integración o actualización documental.'],
                    ['value' => 'COA', 'label' => 'Reporte anual', 'detail' => 'Datos técnicos listos para presentación.'],
                    ['value' => 'FF', 'label' => 'Fuentes fijas', 'detail' => 'Inventario y control por equipo o proceso.'],
                ],
                'highlights'       => [
                    ['icon' => 'fas fa-smog', 'title' => 'Inventario de fuentes', 'body' => 'Ubicamos equipos, combustibles, chimeneas, procesos y puntos de emisión relevantes.'],
                    ['icon' => 'fas fa-vial', 'title' => 'Medición coordinada', 'body' => 'Definimos parámetros y coordinamos estudios con laboratorios acreditados cuando corresponde.'],
                    ['icon' => 'fas fa-folder-open', 'title' => 'Expediente listo', 'body' => 'Integramos permisos, reportes, bitácoras y evidencia para inspección o seguimiento.'],
                ],
                'deliverables'     => [
                    'Inventario de fuentes fijas y equipos asociados.',
                    'Matriz de obligaciones por fuente, proceso y autoridad.',
                    'Integración de expediente para licencia, actualización o reporte.',
                    'Coordinación de muestreo y análisis cuando aplique.',
                    'Recomendaciones de control, mantenimiento y evidencia documental.',
                ],
                'process'          => [
                    ['title' => 'Identificación', 'body' => 'Revisamos procesos, equipos, combustibles y puntos de emisión.'],
                    ['title' => 'Obligaciones', 'body' => 'Determinamos permisos, mediciones y reportes aplicables al sitio.'],
                    ['title' => 'Integración', 'body' => 'Armamos el expediente técnico y documental para presentación.'],
                    ['title' => 'Seguimiento', 'body' => 'Ordenamos evidencias, fechas clave y próximas actualizaciones.'],
                ],
                'compliance'       => [
                    'Licencia o autorización ambiental aplicable.',
                    'Cédula de Operación Anual cuando corresponda.',
                    'Estudios de emisiones con respaldo técnico.',
                    'Bitácoras de operación y mantenimiento.',
                ],
                'cta'              => [
                    'title' => 'Controla tus obligaciones por emisiones',
                    'body'  => 'Te ayudamos a ordenar fuentes, mediciones y reportes para operar con mayor certeza.',
                ],
                'related'          => [
                    'certificacion-ambiental-empresarial',
                    'gestion-y-regularizacion-de-descargas-de-aguas-residuales',
                    'gestion-de-residuos',
                ],
            ],
            'gestion-y-regularizacion-de-descargas-de-aguas-residuales' => [
                'title'            => 'Gestión y Regularización de Descargas de Aguas Residuales',
                'slug'             => 'gestion-y-regularizacion-de-descargas-de-aguas-residuales',
                'sort_order'       => 3,
                'eyebrow'          => 'Permisos, caracterización y control de descargas',
                'description'      => 'Regularización de descargas de aguas residuales, expedientes, caracterización, seguimiento y evidencia para cumplimiento ambiental.',
                'meta_title'       => 'Regularización de Descargas de Aguas Residuales | Consultoría Ambiental',
                'meta_description' => 'Gestión de permisos, registros, caracterización y evidencia documental para descargas de aguas residuales empresariales.',
                'icon'             => 'fas fa-water',
                'hero_image'       => 'images/nosotros/agua y energía.webp',
                'hero_alt'         => 'Infraestructura relacionada con agua y energía',
                'accent'           => '#0E7490',
                'soft_accent'      => '#EAF8FC',
                'intro'            => [
                    'Definimos la ruta de regularización para descargas de aguas residuales según el origen, destino, volumen y actividad de la empresa.',
                    'Integramos expediente, caracterización y controles internos para que la descarga tenga respaldo técnico y documental.',
                ],
                'metrics'          => [
                    ['value' => 'H2O', 'label' => 'Descarga controlada', 'detail' => 'Origen, destino y parámetros claros.'],
                    ['value' => 'LAB', 'label' => 'Caracterización', 'detail' => 'Muestreo coordinado cuando aplica.'],
                    ['value' => 'DOC', 'label' => 'Expediente', 'detail' => 'Permisos, reportes y evidencia.'],
                ],
                'highlights'       => [
                    ['icon' => 'fas fa-map-marker-alt', 'title' => 'Punto de descarga', 'body' => 'Identificamos origen, ubicación, caudal, destino y condiciones operativas.'],
                    ['icon' => 'fas fa-flask', 'title' => 'Parámetros aplicables', 'body' => 'Definimos análisis necesarios y coordinamos caracterización con soporte técnico.'],
                    ['icon' => 'fas fa-shield-alt', 'title' => 'Gestión preventiva', 'body' => 'Ordenamos evidencia para auditorías, inspecciones o renovaciones.'],
                ],
                'deliverables'     => [
                    'Diagnóstico de puntos de descarga y documentación disponible.',
                    'Matriz de obligaciones por tipo de descarga y autoridad.',
                    'Expediente para registro, permiso, actualización o regularización.',
                    'Coordinación de muestreo, laboratorio y reportes técnicos.',
                    'Plan de seguimiento documental y operativo.',
                ],
                'process'          => [
                    ['title' => 'Diagnóstico hídrico', 'body' => 'Revisamos consumo, proceso, tratamiento y salida de agua residual.'],
                    ['title' => 'Ruta regulatoria', 'body' => 'Definimos autoridad, permiso, registro o actualización aplicable.'],
                    ['title' => 'Caracterización', 'body' => 'Coordinamos muestreo y análisis cuando el expediente lo requiere.'],
                    ['title' => 'Expediente final', 'body' => 'Entregamos documentos, evidencias y calendario de seguimiento.'],
                ],
                'compliance'       => [
                    'Permiso o registro de descarga aplicable.',
                    'Caracterización de aguas residuales.',
                    'Evidencia de operación y mantenimiento.',
                    'Reportes y renovaciones programadas.',
                ],
                'cta'              => [
                    'title' => 'Regulariza tus descargas de aguas residuales',
                    'body'  => 'Revisamos tus puntos de descarga y armamos el expediente necesario para cerrar brechas.',
                ],
                'related'          => [
                    'emisiones-a-la-atmosfera',
                    'certificacion-ambiental-empresarial',
                    'estudios-de-impacto-ambiental',
                ],
            ],
            'estudios-de-impacto-ambiental' => [
                'title'            => 'Manifiesta de Impacto Ambiental',
                'slug'             => 'estudios-de-impacto-ambiental',
                'sort_order'       => 4,
                'eyebrow'          => 'MIA, informes preventivos y gestión de proyectos',
                'description'      => 'Estudios ambientales para proyectos inmobiliarios, industriales o de infraestructura, con integración técnica y acompañamiento ante autoridad.',
                'meta_title'       => 'Manifiesta de Impacto Ambiental | MIA e Informes Preventivos',
                'meta_description' => 'Elaboración de MIA, informes preventivos, diagnóstico ambiental, medidas de mitigación y acompañamiento técnico para proyectos.',
                'icon'             => 'fas fa-leaf',
                'hero_image'       => 'images/impacto ambiental imagen3.webp',
                'hero_alt'         => 'Evaluación de impacto ambiental en campo',
                'accent'           => '#4D7C0F',
                'soft_accent'      => '#F0F8E8',
                'intro'            => [
                    'Convertimos la información del proyecto en un expediente ambiental claro, defendible y alineado con la autoridad aplicable.',
                    'Integramos levantamiento, análisis de impactos, medidas de mitigación y seguimiento para proyectos que necesitan autorización ambiental.',
                ],
                'metrics'          => [
                    ['value' => 'MIA', 'label' => 'Manifestación', 'detail' => 'Modalidad según proyecto y autoridad.'],
                    ['value' => 'IP', 'label' => 'Informe preventivo', 'detail' => 'Alternativa cuando el caso lo permite.'],
                    ['value' => 'EDA', 'label' => 'Estudio de daños y afectaciones ambientales', 'detail' => 'Evaluación técnica de afectaciones.'],
                ],
                'highlights'       => [
                    ['icon' => 'fas fa-search-location', 'title' => 'Diagnóstico del sitio', 'body' => 'Revisamos ubicación, entorno, restricciones y sensibilidad ambiental.'],
                    ['icon' => 'fas fa-project-diagram', 'title' => 'Evaluación de impactos', 'body' => 'Relacionamos actividades del proyecto con impactos potenciales y medidas de control.'],
                    ['icon' => 'fas fa-comments', 'title' => 'Acompañamiento técnico', 'body' => 'Damos seguimiento a prevenciones, aclaraciones y ajustes solicitados por autoridad.'],
                ],
                'deliverables'     => [
                    'Ficha técnica del proyecto y alcance ambiental.',
                    'Levantamiento de línea base y revisión documental.',
                    'MIA, informe preventivo o expediente técnico aplicable.',
                    'Matriz de impactos y medidas de mitigación.',
                    'Acompañamiento durante revisión, prevención o seguimiento.',
                ],
                'process'          => [
                    ['title' => 'Viabilidad', 'body' => 'Revisamos ubicación, giro, obras, etapas y autoridad competente.'],
                    ['title' => 'Campo y gabinete', 'body' => 'Integramos información técnica, cartográfica, fotográfica y documental.'],
                    ['title' => 'Evaluación', 'body' => 'Identificamos impactos y diseñamos medidas de prevención, mitigación o compensación.'],
                    ['title' => 'Gestión', 'body' => 'Presentamos expediente y acompañamos respuestas ante observaciones.'],
                ],
                'compliance'       => [
                    'Autorización ambiental previa al desarrollo cuando aplique.',
                    'Medidas de mitigación documentadas.',
                    'Condicionantes y seguimiento ambiental.',
                    'Expediente técnico para autoridad e inversionistas.',
                ],
                'cta'              => [
                    'title' => 'Evalúa la viabilidad ambiental de tu proyecto',
                    'body'  => 'Revisamos el alcance y te indicamos qué estudio o autorización necesitas antes de avanzar.',
                ],
                'related'          => [
                    'gestion-de-residuos',
                    'gestion-y-regularizacion-de-descargas-de-aguas-residuales',
                    'certificacion-ambiental-empresarial',
                ],
            ],
            'certificacion-ambiental-empresarial' => [
                'title'            => 'Certificación Ambiental Empresarial',
                'slug'             => 'certificacion-ambiental-empresarial',
                'sort_order'       => 5,
                'eyebrow'          => 'Cumplimiento, auditoría y mejora continua',
                'description'      => 'Acompañamiento para programas de certificación, auditorías, evidencias, planes de acción y fortalecimiento del desempeño ambiental.',
                'meta_title'       => 'Certificación Ambiental Empresarial | Auditoría y Cumplimiento',
                'meta_description' => 'Diagnóstico, preparación documental, plan de acción y acompañamiento para certificación ambiental empresarial.',
                'icon'             => 'fas fa-award',
                'hero_image'       => 'images/nosotros/licenciasypermisos.webp',
                'hero_alt'         => 'Documentación para certificación ambiental empresarial',
                'accent'           => '#A16207',
                'soft_accent'      => '#FBF6E8',
                'intro'            => [
                    'Preparamos a la empresa para demostrar cumplimiento ambiental con evidencia clara, trazable y lista para auditoría.',
                    'El proceso ordena permisos, operación, residuos, agua, emisiones, capacitación y mejora continua en un solo tablero de trabajo.',
                ],
                'metrics'          => [
                    ['value' => 'GAP', 'label' => 'Brechas', 'detail' => 'Diagnóstico contra requisitos aplicables.'],
                    ['value' => 'PAC', 'label' => 'Plan de acción', 'detail' => 'Prioridades, responsables y fechas.'],
                    ['value' => 'EVI', 'label' => 'Evidencia', 'detail' => 'Expediente listo para auditoría.'],
                ],
                'highlights'       => [
                    ['icon' => 'fas fa-clipboard-check', 'title' => 'Auditoría de brechas', 'body' => 'Revisamos documentación, instalaciones, controles y prácticas ambientales.'],
                    ['icon' => 'fas fa-tasks', 'title' => 'Plan accionable', 'body' => 'Priorizamos hallazgos y convertimos requisitos en tareas claras para el equipo.'],
                    ['icon' => 'fas fa-certificate', 'title' => 'Preparación final', 'body' => 'Ordenamos evidencias y acompañamos simulacros, validación y cierre de observaciones.'],
                ],
                'deliverables'     => [
                    'Diagnóstico inicial de cumplimiento ambiental.',
                    'Matriz de brechas por tema y nivel de riesgo.',
                    'Plan de acción con responsables, fechas y evidencias.',
                    'Carpeta documental para auditoría o certificación.',
                    'Acompañamiento durante visita, revisión o cierre de hallazgos.',
                ],
                'process_heading'  => [
                    'eyebrow' => 'Ruta de la certificación ambiental',
                    'title'   => 'Ruta de la Certificación Ambiental',
                ],
                'process'          => [
                    ['number' => '1', 'title' => 'Selección del equipo auditor', 'body' => 'Designación y coordinación del equipo responsable de la auditoría.'],
                    ['number' => '2', 'title' => 'Solicitud de adhesión al Programa de Cumplimiento Ambiental Voluntario (PCAV)', 'body' => 'Integración y presentación de la solicitud ante el programa aplicable.'],
                    ['number' => '3', 'title' => 'Firma de convenios entre empresa-SEMADET-PROEPA', 'body' => 'Formalización de acuerdos y alcances de trabajo entre las partes.'],
                    ['number' => '4', 'title' => 'Ejecución de trabajos de campo', 'body' => 'Revisión documental, levantamiento en sitio y verificación de condiciones operativas.'],
                    ['number' => '5', 'title' => 'Presentación de reporte de auditoría', 'body' => 'Entrega del reporte con hallazgos, evidencias y resultado técnico.'],
                    ['number' => '5.1', 'title' => 'Cumplimiento de plan de acción en caso de requerirse', 'body' => 'Atención y cierre de acciones correctivas cuando existan observaciones.'],
                    ['number' => '6', 'title' => 'Verificación final por parte de SEMADET', 'body' => 'Validación final del cumplimiento y de la documentación presentada.'],
                    ['number' => '7', 'title' => 'Emisión de certificado ambiental con vigencia de 2 años', 'body' => 'Obtención del certificado ambiental correspondiente.'],
                ],
                'certificate_types' => [
                    'eyebrow' => 'Tipos de certificados ambientales',
                    'title'   => 'Tipos de Certificados Ambientales',
                    'logos'   => [
                        ['image' => 'images/logo1.jpg', 'alt' => 'Logo de certificación ambiental 1'],
                        ['image' => 'images/logo2.jpg', 'alt' => 'Logo de certificación ambiental 2'],
                    ],
                    'items'   => [
                        [
                            'title'       => 'Compromiso Ambiental',
                            'description' => 'Cumplimiento de obligaciones ambientales en materia de:',
                            'points'      => [
                                'Gestión de residuos',
                                'Abastecimiento, tratamiento y descarga de agua',
                                'Emisiones a la atmósfera y ruido ambiental',
                                'Impacto ambiental',
                                'Suelo y subsuelo',
                                'Recursos naturales',
                                'Seguridad y riesgo ambiental',
                            ],
                        ],
                        [
                            'title'       => 'Líder Ambiental',
                            'description' => 'Implementación de medidas, prácticas e infraestructura que van más allá de lo indicado por la ley:',
                            'points'      => [
                                'Sistema de gestión ambiental implementado',
                                'Eficiencia energética',
                                'Huella de carbono',
                                'Registro de emisiones y transferencia de contaminantes',
                            ],
                        ],
                    ],
                ],
                'compliance'       => [
                    'Carpeta de cumplimiento ambiental actualizada.',
                    'Evidencia de capacitación y controles internos.',
                    'Plan de mejora continua.',
                    'Preparación para auditoría externa o revisión voluntaria.',
                ],
                'cta'              => [
                    'title' => 'Prepara tu empresa para certificarse',
                    'body'  => 'Identificamos brechas y armamos un plan de trabajo realista para avanzar con orden.',
                ],
                'related'          => [
                    'gestion-de-residuos',
                    'emisiones-a-la-atmosfera',
                    'gestion-y-regularizacion-de-descargas-de-aguas-residuales',
                ],
            ],
            'ley-general-de-economa-circular-lgec' => [
                'title'            => 'Ley General de Economía Circular (LGEC)',
                'slug'             => 'ley-general-de-economa-circular-lgec',
                'sort_order'       => 6,
                'eyebrow'          => 'Preparación empresarial y rediseño de procesos',
                'description'      => 'Diagnóstico y acompañamiento para integrar prácticas de economía circular, reducción de residuos, trazabilidad y aprovechamiento de materiales.',
                'meta_title'       => 'Ley General de Economía Circular LGEC | Preparación para Empresas',
                'meta_description' => 'Acompañamiento para preparar a empresas ante obligaciones y oportunidades de economía circular, residuos, trazabilidad y aprovechamiento.',
                'icon'             => 'fas fa-sync-alt',
                'hero_image'       => 'images/nosotros/gestion de residuos.webp',
                'hero_alt'         => 'Materiales separados para economía circular',
                'accent'           => '#2563EB',
                'soft_accent'      => '#EEF4FF',
                'intro'            => [
                    'Ayudamos a convertir la economía circular en acciones operativas: prevención, reducción, reutilización, valorización y trazabilidad.',
                    'El objetivo es que la empresa llegue preparada a nuevas exigencias, oportunidades comerciales y requisitos de clientes o cadenas de suministro.',
                ],
                'metrics'          => [
                    ['value' => '5R', 'label' => 'Circularidad', 'detail' => 'Reducir, reutilizar, reparar, reciclar y repensar.'],
                    ['value' => 'KPI', 'label' => 'Indicadores', 'detail' => 'Materiales, residuos y aprovechamiento.'],
                    ['value' => 'MAP', 'label' => 'Hoja de ruta', 'detail' => 'Acciones por etapa y prioridad.'],
                ],
                'highlights'       => [
                    ['icon' => 'fas fa-chart-pie', 'title' => 'Diagnóstico de materiales', 'body' => 'Identificamos entradas, pérdidas, residuos, subproductos y oportunidades de valorización.'],
                    ['icon' => 'fas fa-retweet', 'title' => 'Estrategia circular', 'body' => 'Diseñamos acciones viables para reducir residuos y aprovechar materiales dentro o fuera del proceso.'],
                    ['icon' => 'fas fa-handshake', 'title' => 'Trazabilidad comercial', 'body' => 'Ordenamos evidencia útil para clientes, proveedores, auditorías y reportes de sostenibilidad.'],
                ],
                'deliverables'     => [
                    'Mapa de materiales, residuos y oportunidades de circularidad.',
                    'Matriz de riesgos, obligaciones y oportunidades por proceso.',
                    'Hoja de ruta con acciones de reducción, reúso, reciclaje o valorización.',
                    'Indicadores de seguimiento y evidencia documental.',
                    'Recomendaciones para proveedores, clientes y comunicación interna.',
                ],
                'process'          => [
                    ['title' => 'Mapa de flujo', 'body' => 'Entendemos materias primas, empaques, residuos y subproductos por etapa.'],
                    ['title' => 'Oportunidades', 'body' => 'Priorizamos reducción, reúso, valorización y cambios de proveedor o proceso.'],
                    ['title' => 'Ruta circular', 'body' => 'Definimos indicadores, responsables y acciones por horizonte de implementación.'],
                    ['title' => 'Evidencia', 'body' => 'Creamos formatos para medir avances y sostener reportes o auditorías.'],
                ],
                'compliance'       => [
                    'Diagnóstico de circularidad por proceso.',
                    'Indicadores de reducción y aprovechamiento.',
                    'Trazabilidad de residuos y materiales valorizables.',
                    'Ruta de preparación ante requisitos regulatorios o comerciales.',
                ],
                'cta'              => [
                    'title' => 'Prepara tu operación para economía circular',
                    'body'  => 'Te ayudamos a identificar oportunidades reales y a convertirlas en una ruta ejecutable.',
                ],
                'related'          => [
                    'gestion-de-residuos',
                    'certificacion-ambiental-empresarial',
                    'estudios-de-impacto-ambiental',
                ],
            ],
        ];
    }
}
