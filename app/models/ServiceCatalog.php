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
                'title'            => 'Gestión Integral de Residuos y Cumplimiento Ambiental',
                'slug'             => 'gestion-de-residuos',
                'sort_order'       => 1,
                'eyebrow'          => 'Residuos de manejo especial y peligrosos',
                'description'      => 'Regularización, planes de manejo, recolección, señalética y seguimiento técnico para residuos de manejo especial y residuos peligrosos.',
                'meta_title'       => 'Gestión Integral de Residuos y Cumplimiento Ambiental',
                'meta_description' => 'Registro como generador, planes de manejo, Cédula de Operación Anual, recolección y señalética para residuos empresariales.',
                'icon'             => 'fas fa-recycle',
                'hero_image'       => 'images/nosotros/gestion de residuos.webp',
                'hero_alt'         => 'Área de gestión de residuos empresariales',
                'accent'           => '#2E7D32',
                'soft_accent'      => '#EEF7EF',
                'intro'            => [
                    'Ayudamos a empresas e industrias a cumplir sus obligaciones ambientales en materia de residuos de manejo especial y residuos peligrosos.',
                    'Integramos regularización, gestión documental, seguimiento técnico, recolección autorizada y organización de áreas para sostener el cumplimiento ambiental.',
                ],
                'metrics'          => [
                    ['value' => 'Registro', 'label' => 'Generador de residuos', 'detail' => 'Alta o regularización ante autoridades ambientales.'],
                    ['value' => 'Plan de manejo', 'label' => 'Residuos especiales y peligrosos', 'detail' => 'Estrategias de minimización, valorización y seguimiento.'],
                    ['value' => 'Trazabilidad', 'label' => 'Recolección y señalética', 'detail' => 'Manifiestos, proveedores autorizados y áreas identificadas.'],
                ],
                'highlights'       => [
                    ['icon' => 'fas fa-clipboard-list', 'title' => 'Registro como generador', 'body' => 'Gestionamos el registro de empresas generadoras de residuos ante autoridades estatales y federales.'],
                    ['icon' => 'fas fa-file-signature', 'title' => 'Planes de manejo', 'body' => 'Desarrollamos planes con diagnóstico de generación, metas, indicadores y acciones de minimización.'],
                    ['icon' => 'fas fa-route', 'title' => 'Recolección y señalética', 'body' => 'Apoyamos con trazabilidad, manifiestos, cartas anuales y organización de áreas de residuos.'],
                ],
                'deliverables'     => [
                    'Registro como generador de residuos de manejo especial o peligrosos.',
                    'Plan de manejo con diagnóstico, metas, indicadores y seguimiento.',
                    'Cédula de Operación Anual e informe de generación cuando aplique.',
                    'Trazabilidad de recolección mediante proveedores autorizados y manifiestos.',
                    'Señalética y recomendaciones para áreas de almacenamiento y manejo.',
                ],
                'process'          => [
                    ['title' => 'Identificación', 'body' => 'Determinamos tipo de residuos, obligaciones aplicables, clasificación y requisitos regulatorios.'],
                    ['title' => 'Regularización', 'body' => 'Integramos registros, planes de manejo y reportes anuales ante la autoridad correspondiente.'],
                    ['title' => 'Operación', 'body' => 'Organizamos recolección, manifiestos, señalética, almacenamiento y control documental.'],
                    ['title' => 'Seguimiento', 'body' => 'Verificamos cumplimiento de proveedores, evidencias y presentación en tiempo de trámites ambientales.'],
                ],
                'compliance'       => [
                    'Registro ambiental de residuos actualizado.',
                    'Plan de manejo de residuos cuando aplique.',
                    'Cédula de Operación Anual presentada en tiempo.',
                    'Manifiestos, señalética y evidencia para inspecciones.',
                ],
                'cta'              => [
                    'title' => 'Regulariza el manejo de residuos de tu empresa',
                    'body'  => 'Te ayudamos a cumplir obligaciones ambientales, mejorar procesos y reducir riesgos regulatorios.',
                ],
                'benefits'         => [
                    'title' => 'Por qué es importante regularizar el manejo de residuos',
                    'items' => [
                        'Evita multas y sanciones ambientales.',
                        'Reduce riesgos durante inspecciones.',
                        'Cumple con obligaciones ambientales vigentes.',
                        'Mejora la organización y trazabilidad de residuos.',
                        'Facilita auditorías y verificaciones ambientales.',
                        'Protege la continuidad operativa de la empresa.',
                    ],
                ],
                'faqs'             => [
                    [
                        'question' => '¿Qué empresas deben registrarse como generadores de residuos?',
                        'answer'   => 'Las empresas que generan residuos de manejo especial o residuos peligrosos durante sus operaciones deben cumplir con obligaciones de registro y control ambiental conforme a la normativa aplicable.',
                    ],
                    [
                        'question' => '¿Qué es un plan de manejo de residuos?',
                        'answer'   => 'Es un documento técnico que establece procedimientos para el manejo adecuado de residuos, incluyendo minimización, valorización, metas, almacenamiento, control, transporte y disposición final.',
                    ],
                    [
                        'question' => '¿Qué pasa si una empresa no cuenta con registro ambiental de residuos?',
                        'answer'   => 'Puede enfrentar apercibimientos, sanciones o clausuras durante inspecciones por parte de autoridades ambientales.',
                    ],
                    [
                        'question' => '¿Qué residuos se consideran de manejo especial?',
                        'answer'   => 'Son residuos generados por procesos productivos, comerciales o de servicios que requieren manejo específico conforme a la legislación ambiental.',
                    ],
                    [
                        'question' => '¿La Cédula de Operación Anual es obligatoria?',
                        'answer'   => 'Dependiendo del tipo de actividad y de las obligaciones ambientales aplicables, algunas empresas deben presentar anualmente la Cédula de Operación Anual ante la autoridad correspondiente.',
                    ],
                    [
                        'question' => '¿Ofrecen recolección de residuos?',
                        'answer'   => 'Sí. El servicio contempla recolección de residuos de manejo especial en la Zona Metropolitana de Guadalajara y distintas regiones de Jalisco.',
                    ],
                ],
                'related'          => [
                    'ley-general-de-economa-circular-lgec',
                    'certificacion-ambiental-empresarial',
                    'estudios-de-impacto-ambiental',
                ],
            ],
            'emisiones-a-la-atmosfera' => [
                'title'            => 'Gestión y Cumplimiento Ambiental en Emisiones a la Atmósfera',
                'slug'             => 'emisiones-a-la-atmosfera',
                'sort_order'       => 2,
                'eyebrow'          => 'Licencia ambiental, contingencias y monitoreo',
                'description'      => 'Regularización, análisis técnico, licencia ambiental única, planes de contingencia y reportes para emisiones atmosféricas.',
                'meta_title'       => 'Gestión y Cumplimiento Ambiental en Emisiones a la Atmósfera',
                'meta_description' => 'Licencia ambiental única, planes de contingencia, Cédula de Operación Anual y análisis de emisiones atmosféricas para empresas.',
                'icon'             => 'fas fa-industry',
                'hero_image'       => 'images/impacto ambiental imagen2.webp',
                'hero_alt'         => 'Instalación industrial con control de emisiones',
                'accent'           => '#0F766E',
                'soft_accent'      => '#EAF7F5',
                'intro'            => [
                    'Ayudamos a empresas e industrias a cumplir obligaciones ambientales relacionadas con gases de combustión, polvos y compuestos orgánicos volátiles.',
                    'El servicio integra regularización, análisis técnicos, memorias de cálculo, monitoreo y seguimiento ante autoridades ambientales.',
                ],
                'metrics'          => [
                    ['value' => 'Licencia', 'label' => 'Ambiental única', 'detail' => 'Obtención o regularización según giro y autoridad.'],
                    ['value' => 'Reporte anual', 'label' => 'Cédula de Operación Anual', 'detail' => 'Integración de datos, análisis y documentación.'],
                    ['value' => 'Fuentes fijas', 'label' => 'Equipos y procesos regulados', 'detail' => 'Identificación, monitoreo y control de emisiones.'],
                ],
                'highlights'       => [
                    ['icon' => 'fas fa-smog', 'title' => 'Licencia ambiental única', 'body' => 'Gestionamos la obtención o regularización para establecimientos con fuentes fijas de emisión.'],
                    ['icon' => 'fas fa-vial', 'title' => 'Análisis y monitoreo', 'body' => 'Coordinamos estudios conforme a normas ambientales aplicables y validamos resultados de laboratorio.'],
                    ['icon' => 'fas fa-folder-open', 'title' => 'Planes y reportes', 'body' => 'Integramos planes de contingencia atmosférica, memorias de cálculo y reportes anuales.'],
                ],
                'deliverables'     => [
                    'Licencia ambiental única en materia de emisiones atmosféricas.',
                    'Identificación de fuentes de emisión, equipos, procesos y combustibles.',
                    'Plan de contingencia atmosférica con medidas preventivas y protocolos.',
                    'Cédula de Operación Anual para obligaciones de reporte ambiental.',
                    'Análisis y monitoreo de emisiones con laboratorio acreditado cuando aplique.',
                ],
                'process'          => [
                    ['title' => 'Identificación', 'body' => 'Revisamos fuentes fijas, combustibles, procesos, chimeneas y puntos de emisión.'],
                    ['title' => 'Cálculo y análisis', 'body' => 'Integramos memorias de cálculo, estudios de laboratorio y parámetros normativos.'],
                    ['title' => 'Gestión normativa', 'body' => 'Preparamos licencias, planes, reportes y expedientes para evaluación de autoridad.'],
                    ['title' => 'Seguimiento', 'body' => 'Ordenamos evidencias, plazos de reporte y próximas actualizaciones regulatorias.'],
                ],
                'compliance'       => [
                    'Licencia ambiental única vigente cuando corresponda.',
                    'Plan de contingencia atmosférica documentado.',
                    'Cédula de Operación Anual presentada en plazo.',
                    'Análisis de emisiones y evidencia técnica de cumplimiento.',
                ],
                'cta'              => [
                    'title' => 'Regulariza las emisiones atmosféricas de tu empresa',
                    'body'  => 'Te ayudamos a cumplir obligaciones ambientales, reducir riesgos regulatorios y mantener operaciones alineadas con la normativa vigente.',
                ],
                'benefits'         => [
                    'title' => 'Por qué regularizar las emisiones atmosféricas',
                    'items' => [
                        'Evita sanciones y observaciones ambientales.',
                        'Facilita inspecciones y auditorías.',
                        'Cumple con normatividad federal aplicable.',
                        'Reduce riesgos operativos y regulatorios.',
                        'Mantiene la continuidad operativa de la empresa.',
                        'Mejora el control ambiental de procesos industriales que generan emisiones.',
                    ],
                ],
                'standards'        => [
                    'title' => 'Normas mencionadas para análisis de emisiones',
                    'items' => [
                        'NOM-085-SEMARNAT: emisión de gases de combustión.',
                        'NOM-035-SEMARNAT: emisión de partículas al ambiente.',
                        'NOM-043-SEMARNAT: emisión de partículas al ambiente a través de chimeneas.',
                        'NOM-081-SEMARNAT: emisión de ruido al ambiente.',
                    ],
                ],
                'faqs'             => [
                    [
                        'question' => '¿Qué empresas deben contar con Licencia Ambiental Única?',
                        'answer'   => 'Las empresas que operan fuentes fijas de emisiones atmosféricas, como gases de combustión, polvos o compuestos orgánicos volátiles sujetos a regulación ambiental, pueden requerir una Licencia Ambiental Única conforme a la normativa aplicable.',
                    ],
                    [
                        'question' => '¿Qué son las emisiones atmosféricas?',
                        'answer'   => 'Son sustancias liberadas al aire provenientes de procesos industriales, equipos de combustión o actividades productivas que pueden generar impactos ambientales.',
                    ],
                    [
                        'question' => '¿Qué es la Cédula de Operación Anual y cuándo aplica?',
                        'answer'   => 'Es un reporte ambiental obligatorio para ciertas empresas sujetas a regulación en emisiones, residuos y operación industrial. La presentación federal se realiza en el segundo trimestre del año; a nivel estatal el plazo depende de cada entidad federativa.',
                    ],
                    [
                        'question' => '¿Qué normas aplican para análisis de emisiones?',
                        'answer'   => 'Dependiendo del tipo de proceso y equipo, pueden aplicar normas relacionadas con gases de combustión, partículas y ruido ambiental, como las NOM-085, NOM-035, NOM-043 y NOM-081 mencionadas por el cliente.',
                    ],
                    [
                        'question' => '¿Qué pasa si una empresa no cumple con obligaciones atmosféricas?',
                        'answer'   => 'Puede recibir apercibimientos, sanciones o clausura durante inspecciones ambientales por parte de autoridades competentes.',
                    ],
                    [
                        'question' => '¿Qué incluye un análisis de emisiones?',
                        'answer'   => 'Incluye monitoreo y evaluación técnica de parámetros relacionados con emisiones generadas por equipos o procesos industriales, realizado mediante laboratorio acreditado cuando aplica.',
                    ],
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
                    ['value' => 'Descarga', 'label' => 'Aguas residuales', 'detail' => 'Registro y regularización ante la autoridad aplicable.'],
                    ['value' => 'Análisis', 'label' => 'Laboratorio y cumplimiento', 'detail' => 'Coordinación y validación de parámetros cuando aplica.'],
                    ['value' => 'Expediente', 'label' => 'Documentación técnica', 'detail' => 'Integración, evidencia y seguimiento ante autoridades.'],
                ],
                'highlights'       => [
                    ['icon' => 'fas fa-map-marker-alt', 'title' => 'Registro de descarga', 'body' => 'Gestionamos el registro y regularización de descargas al sistema de alcantarillado.'],
                    ['icon' => 'fas fa-flask', 'title' => 'Análisis de laboratorio', 'body' => 'Coordinamos y validamos análisis para evaluar parámetros de cumplimiento ambiental.'],
                    ['icon' => 'fas fa-shield-alt', 'title' => 'Seguimiento ante autoridad', 'body' => 'Integramos evidencia técnica y documental para evitar observaciones o sanciones.'],
                ],
                'deliverables'     => [
                    'Registro de descarga de aguas residuales al alcantarillado.',
                    'Integración documental e identificación de obligaciones aplicables.',
                    'Coordinación y validación de análisis de laboratorio.',
                    'Expediente técnico para cumplimiento y seguimiento ante autoridad.',
                    'Recomendaciones para mantener la descarga dentro de límites permisibles.',
                ],
                'process'          => [
                    ['title' => 'Diagnóstico hídrico', 'body' => 'Revisamos origen, destino, proceso, tratamiento y condiciones de descarga.'],
                    ['title' => 'Ruta regulatoria', 'body' => 'Definimos obligaciones, registro y autoridad competente para la descarga.'],
                    ['title' => 'Análisis técnico', 'body' => 'Coordinamos laboratorio y validamos resultados cuando el expediente lo requiere.'],
                    ['title' => 'Regularización', 'body' => 'Entregamos expediente, evidencias y seguimiento documental ante autoridad.'],
                ],
                'compliance'       => [
                    'Registro de descarga de aguas residuales.',
                    'Análisis de laboratorio cuando aplique.',
                    'Evidencia técnica y documental de cumplimiento.',
                    'Seguimiento ante autoridades competentes.',
                ],
                'cta'              => [
                    'title' => 'Regulariza las descargas de agua residual de tu empresa',
                    'body'  => 'Te ayudamos a cumplir obligaciones ambientales y reducir riesgos regulatorios relacionados con el manejo de aguas residuales.',
                ],
                'benefits'         => [
                    'title' => 'Por qué regularizar las descargas de aguas residuales',
                    'items' => [
                        'Evita multas y sanciones ambientales.',
                        'Cumple con obligaciones regulatorias.',
                        'Facilita inspecciones y auditorías.',
                        'Reduce riesgos operativos y legales.',
                        'Mejora el control ambiental de la operación.',
                        'Mantiene la continuidad operativa de la empresa.',
                    ],
                ],
                'standards'        => [
                    'title' => 'Normas mencionadas para descargas y reúso',
                    'items' => [
                        'NOM-001-SEMARNAT: descargas de aguas residuales tratadas vertidas a cuerpos de agua o suelo natural.',
                        'NOM-002-SEMARNAT: descargas de aguas residuales vertidas al sistema de alcantarillado municipal.',
                        'NOM-003-SEMARNAT: aguas residuales tratadas descargadas en riego de áreas verdes o reúso en servicios básicos.',
                    ],
                ],
                'faqs'             => [
                    [
                        'question' => '¿Qué empresas deben registrar sus descargas de aguas residuales?',
                        'answer'   => 'Las empresas, industrias, comercios o desarrollos que descargan aguas residuales al alcantarillado generadas en procesos productivos están sujetos a obligaciones de registro y cumplimiento ambiental.',
                    ],
                    [
                        'question' => '¿Qué son las aguas residuales?',
                        'answer'   => 'Son aguas generadas por procesos industriales, comerciales, de servicios o actividades humanas que requieren manejo y control conforme a la normativa aplicable.',
                    ],
                    [
                        'question' => '¿Qué pasa si una empresa no cuenta con registro de descarga?',
                        'answer'   => 'Puede recibir apercibimientos, sanciones o clausuras durante inspecciones ambientales o revisiones por parte de autoridades competentes.',
                    ],
                    [
                        'question' => '¿Se requieren análisis de laboratorio?',
                        'answer'   => 'Dependiendo del tipo de descarga y actividad, puede ser necesario realizar análisis para evaluar parámetros de cumplimiento ambiental.',
                    ],
                    [
                        'question' => '¿Qué tipo de negocios necesitan este trámite?',
                        'answer'   => 'Puede aplicar para industrias, restaurantes, tequileras, desarrollos inmobiliarios, empresas alimenticias y comercios con generación de aguas residuales.',
                    ],
                    [
                        'question' => '¿Ayudan con la regularización completa?',
                        'answer'   => 'Sí. El servicio contempla acompañamiento técnico y documental durante todo el proceso de regularización y cumplimiento.',
                    ],
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
                'eyebrow'          => 'Manifestaciones, daños ambientales y gestión de proyectos',
                'description'      => 'Evaluación de impactos ambientales, estudios de daños, residencia ambiental y atención a procedimientos administrativos.',
                'meta_title'       => 'Manifiesta de Impacto Ambiental | Estudios y Regularización',
                'meta_description' => 'Manifestaciones de impacto ambiental, estudios de daños, residencia ambiental, auditorías y acompañamiento ante autoridades.',
                'icon'             => 'fas fa-leaf',
                'hero_image'       => 'images/impacto ambiental imagen3.webp',
                'hero_alt'         => 'Evaluación de impacto ambiental en campo',
                'accent'           => '#4D7C0F',
                'soft_accent'      => '#F0F8E8',
                'intro'            => [
                    'Desarrollamos la evaluación de impactos ambientales para proyectos industriales, inmobiliarios y operaciones sujetas a regulación ambiental.',
                    'Integramos trabajos de campo, gestión documental, medidas de prevención, mitigación y compensación, así como seguimiento de condicionantes ambientales.',
                ],
                'metrics'          => [
                    ['value' => 'Manifestación', 'label' => 'Impacto ambiental', 'detail' => 'Evaluación técnica para proyectos municipales, estatales o federales.'],
                    ['value' => 'Residencia', 'label' => 'Seguimiento ambiental', 'detail' => 'Verificación de términos, condicionantes y medidas autorizadas.'],
                    ['value' => 'Daños ambientales', 'label' => 'Estudios y afectaciones', 'detail' => 'Evaluación técnica de daños y afectaciones ambientales.'],
                ],
                'highlights'       => [
                    ['icon' => 'fas fa-search-location', 'title' => 'Manifestaciones de impacto', 'body' => 'Desarrollamos estudios para proyectos inmobiliarios, industriales, tequileros, comerciales y de tratamiento de residuos.'],
                    ['icon' => 'fas fa-project-diagram', 'title' => 'Regularización de proyectos', 'body' => 'Atendemos condicionantes, residencia ambiental, requerimientos y supervisión de cumplimiento.'],
                    ['icon' => 'fas fa-comments', 'title' => 'Auditorías y procedimientos', 'body' => 'Evaluamos cumplimiento ambiental y acompañamos visitas de inspección o procedimientos administrativos.'],
                ],
                'deliverables'     => [
                    'Manifestación de Impacto Ambiental según competencia aplicable.',
                    'Estudio de daños y afectaciones ambientales.',
                    'Trabajos de campo para diagnóstico de flora, fauna y condiciones del sitio.',
                    'Seguimiento de autorizaciones, términos y condicionantes ambientales.',
                    'Auditorías ambientales y atención a procedimientos administrativos.',
                ],
                'process'          => [
                    ['title' => 'Evaluación inicial', 'body' => 'Revisamos giro, ubicación, dimensiones, etapa del proyecto y autoridad competente.'],
                    ['title' => 'Campo y gabinete', 'body' => 'Integramos información técnica, documental, fotográfica y diagnóstico del sitio.'],
                    ['title' => 'Manifestación', 'body' => 'Evaluamos impactos y proponemos medidas de prevención, mitigación y compensación ambiental.'],
                    ['title' => 'Seguimiento', 'body' => 'Acompañamos autorizaciones, requerimientos, residencia ambiental e inspecciones.'],
                ],
                'compliance'       => [
                    'Autorización ambiental previa a construcción u operación.',
                    'Términos y condicionantes ambientales atendidos.',
                    'Residencia ambiental y supervisión de cumplimiento.',
                    'Atención documental y técnica durante inspecciones.',
                ],
                'cta'              => [
                    'title' => 'Desarrolla y opera proyectos con respaldo ambiental',
                    'body'  => 'Te ayudamos a cumplir obligaciones ambientales, gestionar autorizaciones y reducir riesgos regulatorios.',
                ],
                'benefits'         => [
                    'title' => 'Por qué es importante el cumplimiento en impacto ambiental',
                    'items' => [
                        'Facilita autorizaciones y permisos ambientales.',
                        'Reduce riesgos regulatorios y administrativos.',
                        'Evita multas y observaciones ambientales.',
                        'Mejora la viabilidad de proyectos.',
                        'Facilita inspecciones y auditorías.',
                        'Protege la continuidad operativa del proyecto.',
                    ],
                ],
                'standards'        => [
                    'title' => 'Materias que puede cubrir una auditoría ambiental',
                    'items' => [
                        'Emisiones a la atmósfera.',
                        'Gestión de residuos.',
                        'Abastecimiento, tratamiento y descarga de aguas.',
                        'Impacto y riesgo ambiental.',
                        'Ruido.',
                    ],
                ],
                'faqs'             => [
                    [
                        'question' => '¿Qué es una Manifestación de Impacto Ambiental?',
                        'answer'   => 'Es un estudio técnico que evalúa los posibles impactos ambientales de un proyecto y establece medidas de prevención, mitigación y control conforme a la normativa aplicable.',
                    ],
                    [
                        'question' => '¿Qué proyectos requieren autorización de impacto ambiental?',
                        'answer'   => 'El giro, dimensiones y ubicación del proyecto determinan la competencia de evaluación, que puede ser federal, estatal o municipal.',
                    ],
                    [
                        'question' => '¿Qué pasa si un proyecto no cuenta con autorización ambiental?',
                        'answer'   => 'Puede enfrentar sanciones y clausuras temporales por parte de autoridades ambientales.',
                    ],
                    [
                        'question' => '¿Qué es la residencia ambiental?',
                        'answer'   => 'Es el seguimiento técnico mediante personal calificado durante la ejecución de proyectos para verificar el cumplimiento de condicionantes ambientales y obligaciones regulatorias.',
                    ],
                    [
                        'question' => '¿Qué incluye una auditoría ambiental?',
                        'answer'   => 'Incluye evaluación del grado de cumplimiento ambiental, identificación de riesgos regulatorios y propuestas de mejora para la operación o proyecto.',
                    ],
                    [
                        'question' => '¿Apoyan durante inspecciones ambientales?',
                        'answer'   => 'Sí. El servicio contempla acompañamiento técnico y documental durante visitas de inspección y procedimientos administrativos instaurados por autoridades ambientales.',
                    ],
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
                'description'      => 'Acompañamiento para obtener distintivos ambientales oficiales, fortalecer cumplimiento y mejorar reputación empresarial en Jalisco.',
                'meta_title'       => 'Certificación Ambiental Empresarial en Jalisco',
                'meta_description' => 'Acompañamiento para obtener Compromiso Ambiental o Líder Ambiental ante SEMADET, con auditoría, convenio y seguimiento.',
                'icon'             => 'fas fa-award',
                'hero_image'       => 'images/nosotros/licenciasypermisos.webp',
                'hero_alt'         => 'Documentación para certificación ambiental empresarial',
                'accent'           => '#A16207',
                'soft_accent'      => '#FBF6E8',
                'intro'            => [
                    'Acompañamos a empresas en Jalisco para obtener distintivos ambientales oficiales ante SEMADET.',
                    'El proceso fortalece cumplimiento, reputación y competitividad mediante auditoría, convenios, plan de acción y verificación final.',
                ],
                'metrics'          => [
                    ['value' => 'Evaluación', 'label' => 'Diagnóstico inicial', 'detail' => 'Revisión de cumplimiento y condiciones operativas.'],
                    ['value' => 'Plan de acción', 'label' => 'Cierre de observaciones', 'detail' => 'Prioridades, responsables, fechas y evidencias.'],
                    ['value' => 'Distintivo', 'label' => 'Compromiso o Líder Ambiental', 'detail' => 'Proceso ante SEMADET con vigencia de dos años.'],
                ],
                'highlights'       => [
                    ['icon' => 'fas fa-clipboard-check', 'title' => 'Evaluación inicial', 'body' => 'Revisamos documentación, instalaciones, controles y prácticas ambientales antes de iniciar el proceso.'],
                    ['icon' => 'fas fa-tasks', 'title' => 'Convenios y plan de acción', 'body' => 'Acompañamos solicitud de adhesión, firma de convenios y cierre de acciones correctivas.'],
                    ['icon' => 'fas fa-certificate', 'title' => 'Certificado ambiental', 'body' => 'Preparamos reporte, verificación final y emisión del certificado con vigencia de dos años.'],
                ],
                'deliverables'     => [
                    'Evaluación inicial para determinar viabilidad de certificación.',
                    'Solicitud de adhesión al Programa de Cumplimiento Ambiental Voluntario.',
                    'Reporte de auditoría con hallazgos, evidencias y resultado técnico.',
                    'Plan de acción cuando se requiera atender observaciones.',
                    'Acompañamiento hasta verificación final y emisión del certificado.',
                ],
                'process_heading'  => [
                    'eyebrow' => 'Ruta de la certificación ambiental',
                    'title'   => 'Ruta de la Certificación Ambiental',
                ],
                'process'          => [
                    ['number' => '1', 'title' => 'Selección del equipo auditor', 'body' => 'Designación y coordinación del equipo responsable de la auditoría.'],
                    ['number' => '2', 'title' => 'Solicitud de adhesión al Programa de Cumplimiento Ambiental Voluntario', 'body' => 'Integración y presentación de la solicitud ante el programa aplicable.'],
                    ['number' => '3', 'title' => 'Firma de convenios entre empresa, SEMADET y PROEPA', 'body' => 'Formalización de acuerdos y alcances de trabajo entre las partes.'],
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
                    'Distintivo Compromiso Ambiental o Líder Ambiental.',
                    'Convenio de baja prioridad de inspección por parte de PROEPA.',
                    'Convenio con SEMADET para evaluación de trámites ambientales.',
                    'Uso del distintivo en instalaciones, productos y comunicación corporativa.',
                ],
                'cta'              => [
                    'title' => 'Agenda una evaluación inicial para certificar tu empresa',
                    'body'  => 'Revisamos la viabilidad del proceso y te guiamos hasta la emisión del distintivo ambiental.',
                ],
                'benefits'         => [
                    'title' => 'Beneficios de la certificación',
                    'items' => [
                        'El certificado no tiene costo oficial, solo honorarios del equipo auditor.',
                        'Firma de convenio de baja prioridad de inspección por parte de PROEPA.',
                        'Firma de convenio con SEMADET para prioridad de evaluación de trámites ambientales.',
                        'Uso del distintivo en instalaciones, productos, redes sociales y papelería corporativa.',
                        'Vigencia por 2 años.',
                    ],
                ],
                'faqs'             => [
                    [
                        'question' => '¿Dónde aplica la certificación ambiental empresarial?',
                        'answer'   => 'El contenido proporcionado la plantea para empresas en Jalisco, mediante proceso ante SEMADET.',
                    ],
                    [
                        'question' => '¿Qué distintivos ambientales se pueden obtener?',
                        'answer'   => 'Los distintivos indicados son Compromiso Ambiental y Líder Ambiental.',
                    ],
                    [
                        'question' => '¿Qué incluye Compromiso Ambiental?',
                        'answer'   => 'Incluye cumplimiento de obligaciones ambientales en gestión de residuos, agua, emisiones a la atmósfera, ruido ambiental, impacto ambiental, suelo, recursos naturales, seguridad y riesgo ambiental.',
                    ],
                    [
                        'question' => '¿Qué incluye Líder Ambiental?',
                        'answer'   => 'Incluye medidas, prácticas e infraestructura que van más allá de lo indicado por la ley, como sistema de gestión ambiental, eficiencia energética, huella de carbono y registro de emisiones y transferencia de contaminantes.',
                    ],
                    [
                        'question' => '¿Cuánto dura el certificado ambiental?',
                        'answer'   => 'El documento del cliente indica una vigencia de 2 años.',
                    ],
                ],
                'related'          => [
                    'gestion-de-residuos',
                    'emisiones-a-la-atmosfera',
                    'gestion-y-regularizacion-de-descargas-de-aguas-residuales',
                ],
            ],
            'ley-general-de-economa-circular-lgec' => [
                'title'            => 'Ley General de Economía Circular',
                'slug'             => 'ley-general-de-economa-circular-lgec',
                'sort_order'       => 6,
                'eyebrow'          => 'Nuevas obligaciones ambientales en México',
                'description'      => 'Diagnóstico, estrategia de economía circular, responsabilidad extendida del productor y análisis de ciclo de vida para empresas.',
                'meta_title'       => 'Ley General de Economía Circular | Preparación para Empresas',
                'meta_description' => 'Preparación empresarial para economía circular: diagnóstico, reducción de residuos, valorización, trazabilidad y análisis de ciclo de vida.',
                'icon'             => 'fas fa-sync-alt',
                'hero_image'       => 'images/nosotros/gestion de residuos.webp',
                'hero_alt'         => 'Materiales separados para economía circular',
                'accent'           => '#2563EB',
                'soft_accent'      => '#EEF4FF',
                'intro'            => [
                    'Ayudamos a empresas a prepararse e implementar estrategias alineadas con la Ley General de Economía Circular.',
                    'El enfoque fortalece cumplimiento ambiental, reducción de residuos, reutilización, reciclaje, trazabilidad y sostenibilidad corporativa.',
                ],
                'metrics'          => [
                    ['value' => 'Diagnóstico', 'label' => 'Cumplimiento ambiental', 'detail' => 'Riesgos y oportunidades relacionados con economía circular.'],
                    ['value' => 'Estrategia', 'label' => 'Reducción y valorización', 'detail' => 'Planes para residuos, materiales y recursos.'],
                    ['value' => 'Ciclo de vida', 'label' => 'Producto y proceso', 'detail' => 'Evaluación de impactos desde producción hasta disposición final.'],
                ],
                'highlights'       => [
                    ['icon' => 'fas fa-chart-pie', 'title' => 'Diagnóstico de economía circular', 'body' => 'Evaluamos cumplimiento, riesgos y oportunidades de reducción, reutilización, reciclaje y trazabilidad.'],
                    ['icon' => 'fas fa-retweet', 'title' => 'Estrategia de circularidad', 'body' => 'Diseñamos planes para valorización de materiales, optimización de recursos y sostenibilidad operativa.'],
                    ['icon' => 'fas fa-handshake', 'title' => 'Responsabilidad del productor', 'body' => 'Asesoramos estrategias para la gestión responsable de productos y residuos al final de su vida útil.'],
                ],
                'deliverables'     => [
                    'Diagnóstico de cumplimiento ambiental y economía circular.',
                    'Estrategia para reducción de residuos y valorización de materiales.',
                    'Plan de responsabilidad extendida del productor cuando aplique.',
                    'Análisis de ciclo de vida de productos o procesos.',
                    'Recomendaciones para trazabilidad, sostenibilidad y comunicación interna.',
                ],
                'process'          => [
                    ['title' => 'Diagnóstico', 'body' => 'Evaluamos obligaciones, riesgos y oportunidades asociadas con economía circular.'],
                    ['title' => 'Estrategia', 'body' => 'Diseñamos acciones para reducción de residuos, valorización y optimización de recursos.'],
                    ['title' => 'Ciclo de vida', 'body' => 'Analizamos impactos de productos y procesos desde producción hasta disposición final.'],
                    ['title' => 'Implementación', 'body' => 'Definimos responsables, evidencias e indicadores para sostener avances.'],
                ],
                'compliance'       => [
                    'Diagnóstico de economía circular por empresa o proceso.',
                    'Estrategia de reducción, reutilización y reciclaje.',
                    'Trazabilidad de materiales y residuos valorizables.',
                    'Análisis de ciclo de vida y sostenibilidad operativa.',
                ],
                'cta'              => [
                    'title' => 'Prepara tu operación para economía circular',
                    'body'  => 'Te ayudamos a identificar oportunidades reales y a convertirlas en una ruta ejecutable.',
                ],
                'benefits'         => [
                    'title' => 'Beneficios para tu empresa',
                    'items' => [
                        'Anticipación regulatoria.',
                        'Reducción de riesgos ambientales.',
                        'Mejora de imagen corporativa.',
                        'Optimización de recursos.',
                        'Fortalecimiento de sostenibilidad empresarial.',
                        'Ventaja competitiva.',
                    ],
                ],
                'sectors'          => [
                    'title' => 'Sectores que pueden verse impactados',
                    'items' => [
                        'Manufactura.',
                        'Alimentos.',
                        'Plásticos.',
                        'Automotriz.',
                        'Retail.',
                        'Logística.',
                        'Electrónica.',
                        'Construcción.',
                    ],
                ],
                'faqs'             => [
                    [
                        'question' => '¿Qué incluye el diagnóstico de economía circular?',
                        'answer'   => 'Evalúa el nivel de cumplimiento ambiental de la empresa e identifica riesgos y oportunidades relacionadas con economía circular.',
                    ],
                    [
                        'question' => '¿Qué considera una estrategia de economía circular?',
                        'answer'   => 'Incluye planes para reducción de residuos, valorización de materiales, optimización de recursos y sostenibilidad operativa.',
                    ],
                    [
                        'question' => '¿Qué es la responsabilidad extendida del productor?',
                        'answer'   => 'Es la implementación de estrategias para la gestión responsable de productos y residuos al final de su vida útil.',
                    ],
                    [
                        'question' => '¿Qué evalúa el análisis de ciclo de vida?',
                        'answer'   => 'Evalúa el impacto ambiental de productos y procesos en sus etapas de producción, distribución, consumo, reutilización y disposición final.',
                    ],
                    [
                        'question' => '¿Qué empresas pueden verse impactadas?',
                        'answer'   => 'El documento menciona empresas de manufactura, alimentos, plásticos, automotriz, retail, logística, electrónica y construcción.',
                    ],
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
