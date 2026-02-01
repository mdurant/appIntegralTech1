<?php

namespace App\Helpers;

class ChileanDataHelper
{
    /**
     * Regiones y comunas de Chile (desde JSON oficial)
     */
    private static array $regiones = [
        [
            'region' => 'Arica y Parinacota',
            'comunas' => ['Arica', 'Camarones', 'Putre', 'General Lagos'],
        ],
        [
            'region' => 'Tarapacá',
            'comunas' => ['Iquique', 'Alto Hospicio', 'Pozo Almonte', 'Camiña', 'Colchane', 'Huara', 'Pica'],
        ],
        [
            'region' => 'Antofagasta',
            'comunas' => ['Antofagasta', 'Mejillones', 'Sierra Gorda', 'Taltal', 'Calama', 'Ollagüe', 'San Pedro de Atacama', 'Tocopilla', 'María Elena'],
        ],
        [
            'region' => 'Atacama',
            'comunas' => ['Copiapó', 'Caldera', 'Tierra Amarilla', 'Chañaral', 'Diego de Almagro', 'Vallenar', 'Alto del Carmen', 'Freirina', 'Huasco'],
        ],
        [
            'region' => 'Coquimbo',
            'comunas' => ['La Serena', 'Coquimbo', 'Andacollo', 'La Higuera', 'Paiguano', 'Vicuña', 'Illapel', 'Canela', 'Los Vilos', 'Salamanca', 'Ovalle', 'Combarbalá', 'Monte Patria', 'Punitaqui', 'Río Hurtado'],
        ],
        [
            'region' => 'Valparaíso',
            'comunas' => ['Valparaíso', 'Casablanca', 'Concón', 'Juan Fernández', 'Puchuncaví', 'Quintero', 'Viña del Mar', 'Isla de Pascua', 'Los Andes', 'Calle Larga', 'Rinconada', 'San Esteban', 'La Ligua', 'Cabildo', 'Papudo', 'Petorca', 'Zapallar', 'Quillota', 'Calera', 'Hijuelas', 'La Cruz', 'Nogales', 'San Antonio', 'Algarrobo', 'Cartagena', 'El Quisco', 'El Tabo', 'Santo Domingo', 'San Felipe', 'Catemu', 'Llaillay', 'Panquehue', 'Putaendo', 'Santa María', 'Quilpué', 'Limache', 'Olmué', 'Villa Alemana'],
        ],
        [
            'region' => 'Región del Libertador Gral. Bernardo O\'Higgins',
            'comunas' => ['Rancagua', 'Codegua', 'Coinco', 'Coltauco', 'Doñihue', 'Graneros', 'Las Cabras', 'Machalí', 'Malloa', 'Mostazal', 'Olivar', 'Peumo', 'Pichidegua', 'Quinta de Tilcoco', 'Rengo', 'Requínoa', 'San Vicente', 'Pichilemu', 'La Estrella', 'Litueche', 'Marchihue', 'Navidad', 'Paredones', 'San Fernando', 'Chépica', 'Chimbarongo', 'Lolol', 'Nancagua', 'Palmilla', 'Peralillo', 'Placilla', 'Pumanque', 'Santa Cruz'],
        ],
        [
            'region' => 'Región del Maule',
            'comunas' => ['Talca', 'Constitución', 'Curepto', 'Empedrado', 'Maule', 'Pelarco', 'Pencahue', 'Río Claro', 'San Clemente', 'San Rafael', 'Cauquenes', 'Chanco', 'Pelluhue', 'Curicó', 'Hualañé', 'Licantén', 'Molina', 'Rauco', 'Romeral', 'Sagrada Familia', 'Teno', 'Vichuquén', 'Linares', 'Colbún', 'Longaví', 'Parral', 'Retiro', 'San Javier', 'Villa Alegre', 'Yerbas Buenas'],
        ],
        [
            'region' => 'Región de Ñuble',
            'comunas' => ['Cobquecura', 'Coelemu', 'Ninhue', 'Portezuelo', 'Quirihue', 'Ránquil', 'Treguaco', 'Bulnes', 'Chillán Viejo', 'Chillán', 'El Carmen', 'Pemuco', 'Pinto', 'Quillón', 'San Ignacio', 'Yungay', 'Coihueco', 'Ñiquén', 'San Carlos', 'San Fabián', 'San Nicolás'],
        ],
        [
            'region' => 'Región del Biobío',
            'comunas' => ['Concepción', 'Coronel', 'Chiguayante', 'Florida', 'Hualqui', 'Lota', 'Penco', 'San Pedro de la Paz', 'Santa Juana', 'Talcahuano', 'Tomé', 'Hualpén', 'Lebu', 'Arauco', 'Cañete', 'Contulmo', 'Curanilahue', 'Los Álamos', 'Tirúa', 'Los Ángeles', 'Antuco', 'Cabrero', 'Laja', 'Mulchén', 'Nacimiento', 'Negrete', 'Quilaco', 'Quilleco', 'San Rosendo', 'Santa Bárbara', 'Tucapel', 'Yumbel', 'Alto Biobío'],
        ],
        [
            'region' => 'Región de la Araucanía',
            'comunas' => ['Temuco', 'Carahue', 'Cunco', 'Curarrehue', 'Freire', 'Galvarino', 'Gorbea', 'Lautaro', 'Loncoche', 'Melipeuco', 'Nueva Imperial', 'Padre las Casas', 'Perquenco', 'Pitrufquén', 'Pucón', 'Saavedra', 'Teodoro Schmidt', 'Toltén', 'Vilcún', 'Villarrica', 'Cholchol', 'Angol', 'Collipulli', 'Curacautín', 'Ercilla', 'Lonquimay', 'Los Sauces', 'Lumaco', 'Purén', 'Renaico', 'Traiguén', 'Victoria'],
        ],
        [
            'region' => 'Región de Los Ríos',
            'comunas' => ['Valdivia', 'Corral', 'Lanco', 'Los Lagos', 'Máfil', 'Mariquina', 'Paillaco', 'Panguipulli', 'La Unión', 'Futrono', 'Lago Ranco', 'Río Bueno'],
        ],
        [
            'region' => 'Región de Los Lagos',
            'comunas' => ['Puerto Montt', 'Calbuco', 'Cochamó', 'Fresia', 'Frutillar', 'Los Muermos', 'Llanquihue', 'Maullín', 'Puerto Varas', 'Castro', 'Ancud', 'Chonchi', 'Curaco de Vélez', 'Dalcahue', 'Puqueldón', 'Queilén', 'Quellón', 'Quemchi', 'Quinchao', 'Osorno', 'Puerto Octay', 'Purranque', 'Puyehue', 'Río Negro', 'San Juan de la Costa', 'San Pablo', 'Chaitén', 'Futaleufú', 'Hualaihué', 'Palena'],
        ],
        [
            'region' => 'Región Aisén del Gral. Carlos Ibáñez del Campo',
            'comunas' => ['Coihaique', 'Lago Verde', 'Aisén', 'Cisnes', 'Guaitecas', 'Cochrane', 'O\'Higgins', 'Tortel', 'Chile Chico', 'Río Ibáñez'],
        ],
        [
            'region' => 'Región de Magallanes y de la Antártica Chilena',
            'comunas' => ['Punta Arenas', 'Laguna Blanca', 'Río Verde', 'San Gregorio', 'Cabo de Hornos (Ex Navarino)', 'Antártica', 'Porvenir', 'Primavera', 'Timaukel', 'Natales', 'Torres del Paine'],
        ],
        [
            'region' => 'Región Metropolitana de Santiago',
            'comunas' => ['Cerrillos', 'Cerro Navia', 'Conchalí', 'El Bosque', 'Estación Central', 'Huechuraba', 'Independencia', 'La Cisterna', 'La Florida', 'La Granja', 'La Pintana', 'La Reina', 'Las Condes', 'Lo Barnechea', 'Lo Espejo', 'Lo Prado', 'Macul', 'Maipú', 'Ñuñoa', 'Pedro Aguirre Cerda', 'Peñalolén', 'Providencia', 'Pudahuel', 'Quilicura', 'Quinta Normal', 'Recoleta', 'Renca', 'Santiago', 'San Joaquín', 'San Miguel', 'San Ramón', 'Vitacura', 'Puente Alto', 'Pirque', 'San José de Maipo', 'Colina', 'Lampa', 'Tiltil', 'San Bernardo', 'Buin', 'Calera de Tango', 'Paine', 'Melipilla', 'Alhué', 'Curacaví', 'María Pinto', 'San Pedro', 'Talagante', 'El Monte', 'Isla de Maipo', 'Padre Hurtado', 'Peñaflor'],
        ],
    ];

    /**
     * Nombres chilenos comunes
     */
    private static array $nombres = [
        'masculinos' => ['Juan', 'Carlos', 'Luis', 'Pedro', 'Miguel', 'Francisco', 'José', 'Manuel', 'Roberto', 'Fernando', 'Ricardo', 'Mauricio', 'Patricio', 'Rodrigo', 'Sebastián', 'Andrés', 'Diego', 'Cristian', 'Felipe', 'Gonzalo'],
        'femeninos' => ['María', 'Carmen', 'Ana', 'Laura', 'Patricia', 'Sandra', 'Claudia', 'Carolina', 'Paula', 'Andrea', 'Natalia', 'Valentina', 'Francisca', 'Javiera', 'Constanza', 'Catalina', 'Daniela', 'Camila', 'Isidora', 'Antonia'],
    ];

    /**
     * Apellidos chilenos comunes
     */
    private static array $apellidos = [
        'González', 'Muñoz', 'Rojas', 'Díaz', 'Pérez', 'Soto', 'Contreras', 'Silva', 'Martínez', 'Sepúlveda',
        'Morales', 'Rodríguez', 'López', 'Fuentes', 'Hernández', 'Torres', 'Araya', 'Flores', 'Espinoza', 'Valenzuela',
        'Castillo', 'Ramírez', 'Reyes', 'Gutiérrez', 'Castro', 'Vargas', 'Álvarez', 'Vásquez', 'Tapia', 'Fernández',
    ];

    /**
     * Empresas de transporte/fletes chilenas
     */
    private static array $empresasFletes = [
        'Transportes', 'Fletes', 'Mudanzas', 'Logística', 'Carga', 'Distribución', 'Envíos', 'Delivery',
    ];

    /**
     * Genera un teléfono celular chileno en formato +569 xxxxxxxx
     */
    public static function chileanPhone(): string
    {
        return '+569'.fake()->numerify('########');
    }

    /**
     * Genera un nombre completo chileno
     */
    public static function chileanName(): string
    {
        $genero = fake()->randomElement(['masculinos', 'femeninos']);
        $nombre = fake()->randomElement(self::$nombres[$genero]);
        $apellido1 = fake()->randomElement(self::$apellidos);
        $apellido2 = fake()->randomElement(self::$apellidos);

        return "{$nombre} {$apellido1} {$apellido2}";
    }

    /**
     * Normaliza un string eliminando tildes, Ñ y convirtiendo a minúsculas
     */
    private static function normalizeForEmail(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');

        // Reemplazar tildes y caracteres especiales
        $replacements = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u',
            'ñ' => 'n', 'Ñ' => 'n',
        ];

        return strtr($text, $replacements);
    }

    /**
     * Genera un email chileno (sin tildes, sin Ñ, en minúsculas)
     */
    public static function chileanEmail(?string $name = null): string
    {
        if ($name === null) {
            $name = self::chileanName();
        }

        // Normalizar el nombre: eliminar tildes, Ñ y convertir a minúsculas
        $normalizedName = self::normalizeForEmail($name);

        // Reemplazar espacios por puntos
        $emailName = str_replace(' ', '.', $normalizedName);

        $domains = ['gmail.com', 'hotmail.com', 'yahoo.cl', 'outlook.cl', 'live.cl'];

        return $emailName.'@'.fake()->randomElement($domains);
    }

    /**
     * Obtiene una región y comuna aleatoria de Chile
     */
    public static function randomLocation(): array
    {
        $region = fake()->randomElement(self::$regiones);
        $comuna = fake()->randomElement($region['comunas']);

        return [
            'region' => $region['region'],
            'comuna' => $comuna,
            'location_text' => "{$comuna}, {$region['region']}, Chile",
        ];
    }

    /**
     * Genera una dirección chilena
     */
    public static function chileanAddress(): string
    {
        $tipos = ['Calle', 'Avenida', 'Pasaje', 'Camino'];
        $nombres = ['Los Aromos', 'Las Rosas', 'El Roble', 'Los Copihues', 'Las Acacias', 'San Martín', 'O\'Higgins', 'Arturo Prat', 'Manuel Rodríguez', 'Libertador'];
        $numero = fake()->numberBetween(100, 9999);
        $depto = fake()->optional(0.3)->numerify('###');

        $direccion = fake()->randomElement($tipos).' '.fake()->randomElement($nombres).' '.$numero;

        if ($depto) {
            $direccion .= ', Depto. '.$depto;
        }

        return $direccion;
    }

    /**
     * Genera un nombre de empresa de fletes
     */
    public static function fleteCompanyName(): string
    {
        $prefijo = fake()->randomElement(self::$empresasFletes);
        $sufijo = fake()->randomElement(['Express', 'Rápido', 'Seguro', 'Premium', 'Pro', 'Chile', 'Nacional', 'Sur', 'Norte']);

        return "{$prefijo} {$sufijo}";
    }

    /**
     * Genera un título de solicitud de flete
     */
    public static function fleteRequestTitle(): string
    {
        $titles = [
            'Necesito flete para mudanza de departamento',
            'Solicito servicio de flete para muebles',
            'Requiero transporte de carga desde almacén',
            'Busco flete para traslado de electrodomésticos',
            'Necesito flete urgente para entrega de mercadería',
            'Solicito servicio de flete para mudanza completa',
            'Requiero transporte de muebles y cajas',
            'Busco flete para traslado de oficina',
            'Necesito flete para envío de paquetes',
            'Solicito servicio de flete para mudanza local',
        ];

        return fake()->randomElement($titles);
    }

    /**
     * Genera una descripción de solicitud de flete
     */
    public static function fleteRequestDescription(): string
    {
        $descriptions = [
            'Necesito un servicio de flete confiable para trasladar muebles y pertenencias desde mi domicilio actual hasta mi nueva dirección. Los artículos incluyen muebles de sala, comedor, dormitorio y algunas cajas con objetos personales.',
            'Requiero un servicio de flete para transportar carga desde un almacén hasta mi local comercial. La carga consiste en productos de tamaño mediano que necesitan ser manejados con cuidado.',
            'Busco un servicio de flete para mudanza completa. Incluye muebles grandes, electrodomésticos, cajas y algunos artículos frágiles que requieren embalaje especial.',
            'Necesito un flete urgente para trasladar mercadería desde un proveedor hasta mi negocio. El servicio debe ser puntual y seguro.',
            'Solicito servicio de flete para traslado de oficina. Incluye escritorios, sillas, archiveros y equipos de oficina que deben ser transportados con cuidado.',
            'Requiero un flete para envío de paquetes desde Santiago a otra región. Los paquetes son de tamaño mediano y necesitan llegar en buen estado.',
            'Busco servicio de flete para mudanza local dentro de la misma comuna. Los artículos son principalmente muebles y electrodomésticos.',
            'Necesito un flete para transportar materiales de construcción desde una ferretería hasta mi obra. Los materiales son pesados y requieren un vehículo adecuado.',
        ];

        return fake()->randomElement($descriptions);
    }

    /**
     * Genera un mensaje de cotización para flete
     */
    public static function fleteBidMessage(): string
    {
        $messages = [
            'Cotización lista para revisar. Incluye seguro y embalaje básico.',
            'Precio competitivo con servicio puerta a puerta. Disponibilidad inmediata.',
            'Ofrezco servicio completo con personal capacitado y vehículo adecuado.',
            'Cotización incluye carga, descarga y transporte seguro. Disponible esta semana.',
            'Servicio profesional con experiencia en mudanzas. Precio negociable.',
            'Ofrezco el mejor precio del mercado con garantía de servicio.',
            'Cotización con opción de embalaje profesional. Disponibilidad flexible.',
            'Servicio rápido y confiable. Incluye seguro de carga.',
        ];

        return fake()->randomElement($messages);
    }

    /**
     * Formatea un número en formato chileno (separador de miles con punto, sin decimales)
     * Ejemplo: 10000000 -> "10.000.000"
     */
    public static function formatChileanCurrency(float|int|string $amount): string
    {
        // Convertir a entero (sin decimales)
        $amount = (int) round((float) $amount);

        // Formatear con separador de miles (punto)
        return number_format($amount, 0, ',', '.');
    }

    /**
     * Genera un RUT chileno válido
     * Formato: 12345678-9
     */
    public static function chileanRut(): string
    {
        $numero = fake()->numberBetween(1000000, 99999999);
        $dv = self::calculateRutDv($numero);

        return number_format($numero, 0, '', '.').'-'.$dv;
    }

    /**
     * Calcula el dígito verificador de un RUT chileno
     */
    private static function calculateRutDv(int $numero): string
    {
        $suma = 0;
        $multiplier = 2;

        $numeroStr = (string) $numero;

        for ($i = strlen($numeroStr) - 1; $i >= 0; $i--) {
            $suma += (int) $numeroStr[$i] * $multiplier;
            $multiplier = $multiplier === 7 ? 2 : $multiplier + 1;
        }

        $resto = $suma % 11;
        $dv = 11 - $resto;

        if ($dv === 11) {
            return '0';
        } elseif ($dv === 10) {
            return 'K';
        }

        return (string) $dv;
    }

    /**
     * Genera un Giro SII (código de actividad económica chileno)
     */
    public static function giroSii(): string
    {
        $giros = [
            '464910', // Venta al por mayor de productos alimenticios
            '494110', // Transporte de carga por carretera
            '494200', // Servicios de mudanza
            '522910', // Servicios de almacenamiento
            '432110', // Construcción de edificios residenciales
            '432210', // Construcción de edificios no residenciales
            '433010', // Instalaciones eléctricas
            '433020', // Instalaciones de gas, agua y alcantarillado
            '433030', // Instalaciones de calefacción y aire acondicionado
            '439010', // Terminación y acabado de edificios
        ];

        return fake()->randomElement($giros);
    }

    /**
     * Últimos 4 dígitos del RUT antes del dígito verificador (para código cartola PDF).
     * Ej: 12.345.678-9 → 5678
     */
    public static function rutCartolaCode(?string $rut): ?string
    {
        if (! $rut || trim($rut) === '') {
            return null;
        }

        $clean = preg_replace('/[^0-9kK]/', '', $rut);
        if (strlen($clean) < 5) {
            return null;
        }

        $numberPart = substr($clean, 0, -1);

        return substr($numberPart, -4) ?: null;
    }
}
