<?php

namespace Database\Seeders;

use App\Models\Block;
use App\Models\Entry;
use App\Models\Language;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Support\CollectionMigrator;
use App\Support\Site;
use Illuminate\Database\Seeder;

class PortalContentSeeder extends Seeder
{
    private function l(string $pt, string $en, string $es): array
    {
        return compact('pt', 'en', 'es');
    }

    private function same(string $text): array
    {
        return $this->l($text, $text, $text);
    }

    public function run(): void
    {
        foreach ([['pt', 'Português', true, 'br'], ['en', 'English', false, 'us'], ['es', 'Español', false, 'es']] as $i => [$code, $name, $default, $flag]) {
            Language::updateOrCreate(['code' => $code], ['name' => $name, 'flag' => $flag, 'is_default' => $default, 'is_active' => true, 'position' => $i]);
        }
        Site::flush();

        $this->settings();
        $this->menus();
        $this->home();
        $this->company();
        $this->services();
        $this->contact();
        $this->extraPages();

        Entry::query()->delete();
        Page::where('slug', 'sobre-nos')->delete();
        CollectionMigrator::run();
    }

    private function settings(): void
    {
        SiteSetting::current()->update(['data' => [
            'site_name' => 'Forest Ark Investiments',
            'logo' => 'uploads/site/logo.png',
            'favicon' => 'uploads/site/favicon.png',
            'color_brand' => '#379c3e',
            'color_header' => '#163e19',
            'color_footer' => '#1c4e1f',
            'color_footer_bottom' => '#163e19',
            'email' => 'forestinvestimentos@gmail.com',
            'notify_email' => 'forestinvestimentos@gmail.com',
            'phone' => '(38) 99969-9938',
            'whatsapp' => '553899699938',
            'address' => 'Rodovia BR 364, S/N, KM 573.5, Zona Rural',
            'city' => 'Itapuã do Oeste - RO',
            'label_email' => $this->l('Email', 'Email', 'Correo'),
            'label_phone' => $this->l('Telefone', 'Phone', 'Teléfono'),
            'footer_call' => $this->l('Entre em contato conosco;', 'Get in touch with us;', 'Póngase en contacto con nosotros;'),
            'footer_send' => $this->l('Envie-nos;', 'Write to us;', 'Escríbanos;'),
            'footer_about_title' => $this->l('Sobre nós', 'About us', 'Sobre nosotros'),
            'footer_about' => $this->l(
                'Uma empresa que trabalha a produção de madeira de forma renovável, com resultados econômico-financeiros sustentáveis e sem danos ao ecossistema global.',
                'A company that produces timber renewably, with sustainable economic and financial results and no harm to the global ecosystem.',
                'Una empresa que produce madera de forma renovable, con resultados económico-financieros sostenibles y sin daños al ecosistema global.'
            ),
            'footer_where' => $this->l('Onde Estamos', 'Where we are', 'Dónde estamos'),
            'footer_links' => $this->l('Links Extras', 'Extra links', 'Enlaces adicionales'),
            'footer_comments' => $this->l('Comentário(s) recente(s)', 'Recent comment(s)', 'Comentario(s) reciente(s)'),
            'footer_subscribe' => $this->l('Inscreva-se', 'Subscribe', 'Suscríbase'),
            'footer_placeholder' => $this->l('Endereço de email', 'Email address', 'Correo electrónico'),
            'footer_subscribe_text' => $this->l(
                'Inscreva-se em nossa lista de e-mails para receber as últimas atualizações e notícias.',
                'Join our mailing list to receive the latest updates and news.',
                'Suscríbase a nuestra lista de correo para recibir las últimas novedades y noticias.'
            ),
            'footer_copyright' => $this->l(
                'Copyright ® 1994 - :year – Todos os direitos Reservados - :name',
                'Copyright ® 1994 - :year – All rights reserved - :name',
                'Copyright ® 1994 - :year – Todos los derechos reservados - :name'
            ),
            'msg_contact_ok' => $this->l('Mensagem enviada. Entraremos em contato em breve.', 'Message sent. We will contact you soon.', 'Mensaje enviado. Nos pondremos en contacto pronto.'),
            'msg_subscribe_ok' => $this->l('Inscrição realizada. Obrigado!', 'Subscription complete. Thank you!', 'Suscripción realizada. ¡Gracias!'),
            'msg_404_title' => $this->l('Nada foi encontrado', 'Nothing was found', 'No se encontró nada'),
            'msg_404_text' => $this->l('Talvez o link abaixo possa ajudar.', 'Perhaps the link below can help.', 'Quizás el siguiente enlace pueda ayudar.'),
            'msg_404_button' => $this->l('Ir para página inicial', 'Go to home page', 'Ir a la página de inicio'),
        ]]);
    }

    private function menus(): void
    {
        MenuItem::query()->delete();
        $header = [
            [$this->l('Início', 'Home', 'Inicio'), '/'],
            [$this->l('A Empresa', 'The Company', 'La Empresa'), '/a-empresa'],
            [$this->l('Serviços', 'Services', 'Servicios'), '/servicos'],
            [$this->l('Contato', 'Contact', 'Contacto'), '/contato'],
        ];
        $footer = [
            [$this->l('Inicio', 'Home', 'Inicio'), '/'],
            [$this->l('Sobre nós', 'About us', 'Sobre nosotros'), '/a-empresa'],
            [$this->l('Serviços', 'Services', 'Servicios'), '/servicos'],
            [$this->l('Casos de Sucesso', 'Success Stories', 'Casos de Éxito'), '/casos-de-sucesso'],
            [$this->l('Notícias', 'News', 'Noticias'), '/noticias'],
            [$this->l('Portfolio', 'Portfolio', 'Portafolio'), '/portifolio'],
            [$this->l('Contato', 'Contact', 'Contacto'), '/contato'],
        ];
        foreach (['header' => $header, 'footer' => $footer] as $location => $items) {
            foreach ($items as $i => [$label, $url]) {
                MenuItem::create(['location' => $location, 'position' => $i, 'data' => ['label' => $label, 'url' => $url]]);
            }
        }
    }

    private function page(string $slug, array $title, array $description, array $blocks): void
    {
        $page = Page::updateOrCreate(['slug' => $slug], ['data' => ['title' => $title, 'meta_description' => $description], 'is_published' => true]);
        $page->blocks()->delete();
        foreach ($blocks as $i => [$type, $data]) {
            Block::create(['page_id' => $page->id, 'type' => $type, 'data' => $data, 'position' => $i]);
        }
    }

    private function home(): void
    {
        $slide = fn (string $image, array $title, array $text, bool $on) => [
            'image' => $image, 'title' => $title, 'text' => $text, 'show_text' => $on, 'show_button' => $on,
            'button' => $this->l('Saiba mais', 'Learn more', 'Más información'), 'link' => '#',
        ];
        $mogno = $this->l('Produto Forest: Mogno', 'Forest Product: Mahogany', 'Producto Forest: Caoba');
        $founder = ['name' => $this->same('Endrigo Rocha'), 'role' => $this->l('Sócio-administrador', 'Managing partner', 'Socio administrador')];
        $partner = fn (string $image, string $name) => ['image' => "uploads/parceiros/$image", 'badge' => $this->same($name), 'by' => $this->same(''), 'author' => '', 'author_url' => '', 'title' => $this->same(''), 'highlight' => $this->same(''), 'text' => $this->same('')];

        $this->page('', $this->l('Início', 'Home', 'Inicio'), $this->l(
            'Forest Ark Investiments: produção de madeira renovável e manejo florestal sustentável na Amazônia.',
            'Forest Ark Investiments: renewable timber production and sustainable forest management in the Amazon.',
            'Forest Ark Investiments: producción de madera renovable y manejo forestal sostenible en la Amazonía.'
        ), [
            ['hero_slider', ['items' => [
                $slide('uploads/slides/2024/04/forest-ark-investiments.png', $this->same('Forest Ark Investiments'), $this->l(
                    'Uma empresa que trabalha a produção de madeira de forma renovável, com resultados econômico-financeiros sustentáveis e sem danos ao ecossistema global.',
                    'A company that produces timber renewably, with sustainable economic and financial results and no harm to the global ecosystem.',
                    'Una empresa que produce madera de forma renovable, con resultados económico-financieros sostenibles y sin daños al ecosistema global.'
                ), true),
                $slide('uploads/slides/2024/04/certificado_intuix_clt.png', $mogno, $this->l(
                    'Foi criado a partir de uma espécie exótica da Ásia, onde foi maximizado o seu crescimento biológico.',
                    'It was developed from an exotic Asian species, maximizing its biological growth.',
                    'Se creó a partir de una especie exótica de Asia, en la que se maximizó su crecimiento biológico.'
                ), false),
                $slide('uploads/slides/2024/04/certificado_intuix_esg.png', $mogno, $this->l(
                    'Ele cresce já com o cerne, fenômeno incomum e que não aparece em nenhum outro tipo de madeira. Seu corte é totalmente licenciado pelos órgãos ambientais, por ser uma espécie exótica.',
                    'It grows with its heartwood already formed, an unusual phenomenon not seen in any other type of timber. As an exotic species, its harvesting is fully licensed by environmental agencies.',
                    'Crece ya con el duramen, un fenómeno inusual que no aparece en ningún otro tipo de madera. Su corte está totalmente autorizado por los organismos ambientales, por ser una especie exótica.'
                ), false),
            ]]],
            ['testimonials', ['background' => 'uploads/site/secao_fundo.png', 'items' => [
                $founder + ['text' => $this->l(
                    '<p><b>Objetivo:</b> Plantar e comercializar madeira serrada, de qualidade nacional e internacionalmente, buscando o aproveitamento de todos os subprodutos da sua cadeia produtiva, dos quais detivermos domínio e conhecimento pleno, a curto, médio e longo prazo. Buscando viabilidade econômico-financeira para arcar, se necessário, com financiamentos do BB, Banco do Nordeste e outros.</p>',
                    '<p><b>Objective:</b> To plant and sell quality sawn timber nationally and internationally, seeking to use every by-product of its production chain over which we have full command and knowledge, in the short, medium and long term. Seeking economic and financial viability to take on, if necessary, financing from Banco do Brasil, Banco do Nordeste and others.</p>',
                    '<p><b>Objetivo:</b> Plantar y comercializar madera aserrada de calidad, a nivel nacional e internacional, buscando aprovechar todos los subproductos de su cadena productiva de los que tengamos pleno dominio y conocimiento, a corto, medio y largo plazo. Buscando viabilidad económico-financiera para asumir, si es necesario, financiamientos del Banco do Brasil, Banco do Nordeste y otros.</p>'
                )],
                $founder + ['text' => $this->l(
                    '<p><b>Meta:</b> Ser uma empresa lucrativa, com capacidade de crescimento sustentável na comercialização de madeira no Brasil e no mundo. Para atingirmos tal meta, acreditamos piamente que a empresa tem que ser economico-financeiramente viável e que seu crescimento deve ser gradativo e baseado na produtividade. Vislumbramos ainda o lançamento de ações nas bolsa de valores.</p>',
                    '<p><b>Goal:</b> To be a profitable company, capable of sustainable growth in the timber trade in Brazil and worldwide. To reach this goal, we firmly believe that the company must be economically and financially viable and that its growth must be gradual and based on productivity. We also envision listing shares on the stock exchange.</p>',
                    '<p><b>Meta:</b> Ser una empresa rentable, con capacidad de crecimiento sostenible en la comercialización de madera en Brasil y en el mundo. Para alcanzar esta meta, creemos firmemente que la empresa debe ser económica y financieramente viable y que su crecimiento debe ser gradual y basado en la productividad. También vislumbramos la salida a bolsa de sus acciones.</p>'
                )],
            ]]],
            ['about_accordion', [
                'title' => $this->l('PLANO DE NEGÓCIO', 'BUSINESS PLAN', 'PLAN DE NEGOCIO'),
                'subtitle' => $this->l('Visão Macro', 'Macro View', 'Visión Macro'),
                'intro' => $this->l(
                    '<p>"Contrato de Concessão Florestal Nº 1/2022<br>SERVIÇO FLORESTAL BRASILEIRO FOREST ARK FLONA DO JAMARI<br>VIGÊNCIA 40 ANOS - 2022 a 2062</p>',
                    '<p>"Forest Concession Agreement No. 1/2022<br>BRAZILIAN FOREST SERVICE FOREST ARK JAMARI NATIONAL FOREST<br>TERM OF 40 YEARS - 2022 to 2062</p>',
                    '<p>"Contrato de Concesión Forestal N.º 1/2022<br>SERVICIO FORESTAL BRASILEÑO FOREST ARK BOSQUE NACIONAL DE JAMARI<br>VIGENCIA DE 40 AÑOS - 2022 a 2062</p>'
                ),
                'button' => $this->l('Saiba mais', 'Learn more', 'Más información'),
                'link' => '/a-empresa',
                'image_1' => 'uploads/plano-de-negocios/plano01.jpg',
                'image_2' => 'uploads/plano-de-negocios/plano02.jpg',
                'image_3' => 'uploads/plano-de-negocios/plano03.jpg',
                'documents' => [],
                'items' => [
                    ['title' => $this->l('COMERCIALIZAÇÃO DA MADEIRA', 'TIMBER TRADE', 'COMERCIALIZACIÓN DE LA MADERA'), 'body' => $this->l(
                        '<p>É permitido a retirada e comercialização dos ativos madeireiros, conforme o inventário madeireiro elaborado de acordo com cada Unidade de Produção Florestal</p>',
                        '<p>The harvesting and sale of timber assets is permitted, according to the timber inventory prepared for each Forest Production Unit.</p>',
                        '<p>Se permite la extracción y comercialización de los activos madereros, conforme al inventario maderero elaborado para cada Unidad de Producción Forestal.</p>'
                    )],
                    ['title' => $this->l('COMERCIALIZAÇÃO DA CASTANHA DO BRASIL', 'BRAZIL NUT TRADE', 'COMERCIALIZACIÓN DE LA CASTAÑA DE BRASIL'), 'body' => $this->l(
                        '<p>Nativa da região norte do país, a castanha do Brasil é passível de comercialização, sendo um dos ativos da Concessão Florestal.</p>',
                        '<p>Native to the north of the country, the Brazil nut may be sold and is one of the assets of the Forest Concession.</p>',
                        '<p>Nativa de la región norte del país, la castaña de Brasil se puede comercializar y es uno de los activos de la Concesión Forestal.</p>'
                    )],
                    ['title' => $this->l('CRÉDITO DE CARBONO', 'CARBON CREDITS', 'CRÉDITOS DE CARBONO'), 'body' => $this->l(
                        '<p>Autorizado pela Lei 14.590/2023 a comercialização de Créditos de Carbono faz parte dos ativos passíveis de comercialização.</p>',
                        '<p>Authorized by Law 14.590/2023, the sale of Carbon Credits is among the assets that may be traded.</p>',
                        '<p>Autorizada por la Ley 14.590/2023, la comercialización de Créditos de Carbono forma parte de los activos comercializables.</p>'
                    )],
                    ['title' => $this->l('ÓLEOS, RESINAS, CASCAS, CIPÓS E FOLHAGEM', 'OILS, RESINS, BARKS, VINES AND FOLIAGE', 'ACEITES, RESINAS, CORTEZAS, LIANAS Y FOLLAJE'), 'body' => $this->l(
                        '<p>Os insumos não-madeireiros também são passíveis de comercialização, sendo muito utilizado na industria farmacêutica, e portanto encontrado em abundância na FLONA.</p>',
                        '<p>Non-timber inputs may also be sold. They are widely used in the pharmaceutical industry and are abundant in the National Forest.</p>',
                        '<p>Los insumos no madereros también se pueden comercializar. Son muy utilizados en la industria farmacéutica y abundan en el Bosque Nacional.</p>'
                    )],
                ],
            ]],
            ['faq', ['title' => $this->l('Perguntas frequentes', 'Frequently asked questions', 'Preguntas frecuentes'), 'image' => 'uploads/site/perguntas_frequentes.jpg', 'items' => [
                ['title' => $this->l('O que é proteção da floresta Amazônica?', 'What is protection of the Amazon rainforest?', '¿Qué es la protección de la selva amazónica?'), 'body' => $this->l(
                    '<p>A proteção da floresta é preservação do meio ambiente e o uso consciente dos recursos disponíveis, nosso método de trabalho é rigidamente norteado pela legislação vigente e as boas práticas e procedimentos, acolhendo nossos trabalhadores, e conscientizando a população dos benefícios trazidos pelas boas práticas, zelando de maneira totalitária pela biodiversidade das florestas..</p>',
                    '<p>Protecting the forest means preserving the environment and using the available resources consciously. Our working method is strictly guided by current legislation and by good practices and procedures, supporting our workers, raising public awareness of the benefits of good practices, and fully safeguarding the biodiversity of the forests.</p>',
                    '<p>La protección del bosque es la preservación del medio ambiente y el uso consciente de los recursos disponibles. Nuestro método de trabajo se rige estrictamente por la legislación vigente y por las buenas prácticas y procedimientos, acogiendo a nuestros trabajadores, concienciando a la población sobre los beneficios de las buenas prácticas y velando plenamente por la biodiversidad de los bosques.</p>'
                )],
                ['title' => $this->l('Que tipos de mudas são produzidas?', 'What types of seedlings are produced?', '¿Qué tipos de plántulas se producen?'), 'body' => $this->l(
                    '<p>Todas as espécies nativas, o trabalho prévio é de inventário florestal, forma eficiente e eficaz de se estimar a população ocorrente. No último senso florestal foram identificadas mais de 100 espécies, dentre elas as endêmicas e protegidas por lei.</p>',
                    '<p>All native species. The preliminary work is a forest inventory, an efficient and effective way of estimating the existing population. The latest forest census identified more than 100 species, including endemic species and species protected by law.</p>',
                    '<p>Todas las especies nativas. El trabajo previo es el inventario forestal, una forma eficiente y eficaz de estimar la población existente. En el último censo forestal se identificaron más de 100 especies, entre ellas endémicas y protegidas por ley.</p>'
                )],
                ['title' => $this->l('Como é feito o manejo sustentável de produtos madeireiros?', 'How is the sustainable management of timber products carried out?', '¿Cómo se realiza el manejo sostenible de los productos madereros?'), 'body' => $this->l(
                    '<p>O objetivo fundamental do plano de manejo florestal sustentável é maximizar a produção florestal e minimizar os danos da floresta explorada. É necessário ter um conhecimento da composição das florestas, da estrutura fitossociológica e das distribuições diamétrica e espacial das espécies.<br>O manejo florestal sustentável pertence as estratégias que visam preservar o patrimônio florestal durante os próximos anos. As florestas são um recurso que fornece oxigênio para respirar, habitats para espécies únicas e uma oportunidade para algumas pessoas ganharem a vida, por isto, o manejo florestal sustentável se torna imprescindível para garantir um ecossistema sano que forneça os recursos necessários para as gerações futuras.<br>O sistema silvicultural a ser utilizado é policíclico em escala empresarial, com ciclo de corte de 30 (trinta) anos, a intensidade máxima será 25,8 m³/ha na área de efetivo manejo. São aplicadas as técnicas de EIR -Exploração de Impacto Reduzido, e condução da recomposição florestal pelo método de regeneração natural.</p>',
                    '<p>The fundamental objective of the sustainable forest management plan is to maximize forest production and minimize damage to the harvested forest. This requires knowledge of the composition of the forests, their phytosociological structure and the diameter and spatial distributions of the species.<br>Sustainable forest management is one of the strategies aimed at preserving forest heritage in the years ahead. Forests are a resource that provides oxygen to breathe, habitats for unique species and a livelihood for some people, which is why sustainable forest management is essential to guarantee a healthy ecosystem that provides the resources future generations will need.<br>The silvicultural system to be used is polycyclic on a commercial scale, with a 30 (thirty) year cutting cycle and a maximum intensity of 25.8 m³/ha in the effective management area. RIL (Reduced Impact Logging) techniques are applied, and forest recovery is managed through natural regeneration.</p>',
                    '<p>El objetivo fundamental del plan de manejo forestal sostenible es maximizar la producción forestal y minimizar los daños en el bosque explotado. Es necesario conocer la composición de los bosques, la estructura fitosociológica y las distribuciones diamétrica y espacial de las especies.<br>El manejo forestal sostenible forma parte de las estrategias que buscan preservar el patrimonio forestal en los próximos años. Los bosques son un recurso que proporciona oxígeno para respirar, hábitats para especies únicas y una oportunidad de sustento para algunas personas; por eso, el manejo forestal sostenible es imprescindible para garantizar un ecosistema sano que aporte los recursos necesarios a las generaciones futuras.<br>El sistema silvicultural que se utilizará es policíclico a escala empresarial, con un ciclo de corta de 30 (treinta) años y una intensidad máxima de 25,8 m³/ha en el área de manejo efectivo. Se aplican técnicas de EIR (Exploración de Impacto Reducido) y la recomposición forestal se conduce por el método de regeneración natural.</p>'
                )],
                ['title' => $this->l('Quais os benefícios da recuperação de áreas degradadas?', 'What are the benefits of recovering degraded areas?', '¿Cuáles son los beneficios de recuperar áreas degradadas?'), 'body' => $this->l(
                    '<p>Muitas das espécies de interesse comercial possuem poucos indivíduos nas áreas manejadas. Cerca de 45% de todas as espécies arbóreas das florestas tropicais ocorrem em baixíssimas densidades, com menos de um indivíduo por hectare. Para favorecer estas espécies, podem ser utilizados alguns tratamentos silviculturais. Estes aumentam significativamente o crescimento das árvores em florestas tropicais. Pesquisas têm mostrado que o crescimento pode ser duplicado em relação à floresta explorada não tratada.<br>“Carvalho et al. (2007) citam que os principais tratamentos silviculturais realizados em florestas tropicais constam de corte de cipós, liberação de copas para maior captação de luz, e enriquecimento de clareiras. O corte de cipós e a liberação de copas proporcionam o crescimento mais rápido das árvores tratadas, enquanto que o enriquecimento de clareiras possibilita o aumento da qualidade produtiva da floresta, considerando que as espécies plantadas são, em sua maioria, de alto valor comercial. “<br>Além da garantia e manutenção da exploração de recursos, a recuperação de áreas degradas beneficia diretamente cada individuo do planeta, garantindo desde um ar mais puro a conservação dos rios, bem como a biodiversidade ali existente, floresta é vida, mesmo que não estejamos diretamente nela.</p>',
                    '<p>Many species of commercial interest have few individuals in managed areas. About 45% of all tree species in tropical forests occur at very low densities, with less than one individual per hectare. Certain silvicultural treatments can be used to favor these species. They significantly increase tree growth in tropical forests. Research has shown that growth can double compared with untreated harvested forest.<br>"Carvalho et al. (2007) state that the main silvicultural treatments carried out in tropical forests are vine cutting, crown release for greater light capture, and gap enrichment. Vine cutting and crown release provide faster growth of the treated trees, while gap enrichment increases the productive quality of the forest, given that the planted species are mostly of high commercial value."<br>In addition to securing and maintaining the use of resources, the recovery of degraded areas directly benefits every individual on the planet, from cleaner air to the conservation of rivers and the biodiversity found there. Forest is life, even if we are not directly in it.</p>',
                    '<p>Muchas de las especies de interés comercial tienen pocos individuos en las áreas manejadas. Cerca del 45% de todas las especies arbóreas de los bosques tropicales se presentan en densidades muy bajas, con menos de un individuo por hectárea. Para favorecer estas especies pueden utilizarse algunos tratamientos silviculturales, que aumentan significativamente el crecimiento de los árboles en los bosques tropicales. Las investigaciones han mostrado que el crecimiento puede duplicarse en comparación con el bosque explotado sin tratar.<br>«Carvalho et al. (2007) señalan que los principales tratamientos silviculturales realizados en bosques tropicales son la corta de lianas, la liberación de copas para una mayor captación de luz y el enriquecimiento de claros. La corta de lianas y la liberación de copas proporcionan un crecimiento más rápido de los árboles tratados, mientras que el enriquecimiento de claros permite aumentar la calidad productiva del bosque, ya que las especies plantadas son, en su mayoría, de alto valor comercial.»<br>Además de garantizar y mantener la explotación de los recursos, la recuperación de áreas degradadas beneficia directamente a cada individuo del planeta, desde un aire más puro hasta la conservación de los ríos y de la biodiversidad que allí existe. El bosque es vida, aunque no estemos directamente en él.</p>'
                )],
                ['title' => $this->l('Como posso contribuir com a proteção da floresta Amazônica?', 'How can I contribute to protecting the Amazon rainforest?', '¿Cómo puedo contribuir a la protección de la selva amazónica?'), 'body' => $this->l(
                    '<p>A mudança de comportamentos rotineiros e simples fazem grande diferença, economia de água, optar por produtos biodegradáveis, reutilizáveis e reciclados, se atentar para a separação do lixo e sua destinação correta, não compactuar com atividades ilegais, buscar conscientizar todos a nossa volta, a Amazonia é um patrimônio imensurável em sua grandeza, abundancia e generosidade.</p>',
                    '<p>Changing simple everyday habits makes a big difference: saving water, choosing biodegradable, reusable and recycled products, sorting waste and disposing of it correctly, refusing to condone illegal activities, and raising awareness among those around us. The Amazon is a heritage immeasurable in its grandeur, abundance and generosity.</p>',
                    '<p>Cambiar comportamientos cotidianos y sencillos marca una gran diferencia: ahorrar agua, optar por productos biodegradables, reutilizables y reciclados, separar la basura y darle el destino correcto, no ser cómplice de actividades ilegales y concienciar a quienes nos rodean. La Amazonía es un patrimonio inmensurable en su grandeza, abundancia y generosidad.</p>'
                )],
            ]]],
            ['cards', ['title' => $this->l('Últimas notícias e atualizações', 'Latest news and updates', 'Últimas noticias y novedades'), 'items' => [
                ...$this->newsItems(),
            ]]],
            ['cards', ['title' => $this->l('Parceiros', 'Partners', 'Socios'), 'items' => [
                $partner('lemaflon.jpg', 'Lemafon'),
                $partner('protseg.jpg', 'ProtSeg'),
                $partner('semagri.jpg', 'Secretaria de Meio Ambiente e Cultura'),
                $partner('castanha_ouro_verde.jpg', 'Castanha Ouro Verde'),
                $partner('imaflora.jpg', 'Imaflora'),
            ]]],
        ]);
    }

    private function newsItems(): array
    {
        $by = $this->l('Por', 'By', 'Por');
        $wealth = $this->l('A Riqueza da Flora e Fauna na Floresta do Jamari', 'The Wealth of Flora and Fauna in the Jamari Forest', 'La Riqueza de la Flora y la Fauna en el Bosque de Jamari');
        $news = fn (string $image, array $title, array $highlight, array $text) => [
            'image' => $image, 'badge' => $this->same(''), 'by' => $by, 'author' => 'Endrigo Rocha', 'author_url' => '',
            'title' => $title, 'highlight' => $highlight, 'text' => $text,
        ];

        return [
            $news('uploads/blog/001.png', $this->l('A Floresta do Jamari:', 'The Jamari Forest:', 'El Bosque de Jamari:'), $this->l('Um Tesouro da Amazônia', 'A Treasure of the Amazon', 'Un Tesoro de la Amazonía'), $this->l(
                'A Floresta do Jamari é uma importante reserva ambiental localizada em Itapuã do Oeste. Nosso compromisso é com a preservação da fauna, flora e o meio ambiente, garantindo a sustentabilidade das gerações futuras.',
                'The Jamari Forest is an important environmental reserve located in Itapuã do Oeste. Our commitment is to preserving its fauna, flora and environment, ensuring sustainability for future generations.',
                'El Bosque de Jamari es una importante reserva ambiental ubicada en Itapuã do Oeste. Nuestro compromiso es preservar la fauna, la flora y el medio ambiente, garantizando la sostenibilidad para las generaciones futuras.'
            )),
            $news('uploads/blog/002.png', $wealth, $this->l('Árvores Imponentes', 'Imposing Trees', 'Árboles Imponentes'), $this->l(
                'A Floresta do Jamari é rica em árvores frondosas com diferentes alturas e formas. Essas árvores em conjunto formam um ecossistema vital.',
                'The Jamari Forest is rich in leafy trees of different heights and shapes. Together, these trees form a vital ecosystem.',
                'El Bosque de Jamari es rico en árboles frondosos de diferentes alturas y formas. En conjunto, estos árboles forman un ecosistema vital.'
            )),
            $news('uploads/blog/003.png', $wealth, $this->l('Diversidade de Pássaros', 'Bird Diversity', 'Diversidad de Aves'), $this->l(
                'A Floresta do Jamari abriga uma grande variedade de pássaros, incluindo pica-paus, tucanos e araras. Eles contribuem para a polinização e disseminação de sementes.',
                'The Jamari Forest is home to a wide variety of birds, including woodpeckers, toucans and macaws. They contribute to pollination and seed dispersal.',
                'El Bosque de Jamari alberga una gran variedad de aves, entre ellas pájaros carpinteros, tucanes y guacamayos. Contribuyen a la polinización y a la dispersión de semillas.'
            )),
            $news('uploads/blog/004.png', $wealth, $this->l('Vida Selvagem', 'Wildlife', 'Vida Silvestre'), $this->l(
                'Diversas espécies de macacos, preguiças, coatis e jaguatiricas habitam a floresta. É importante preservar o habitat desses animais para manter o equilíbrio ecológico.',
                'Various species of monkeys, sloths, coatis and ocelots inhabit the forest. Preserving the habitat of these animals is important to maintain ecological balance.',
                'Diversas especies de monos, perezosos, coatíes y ocelotes habitan el bosque. Es importante preservar el hábitat de estos animales para mantener el equilibrio ecológico.'
            )),
            $news('uploads/blog/005.png', $wealth, $this->l('Biodiversidade Vegetal', 'Plant Biodiversity', 'Biodiversidad Vegetal'), $this->l(
                'A flora é igualmente vibrante, com muitas orquídeas, bromélias e outras plantas que compõem um dos ecossistemas mais complexos do mundo.',
                'The flora is equally vibrant, with many orchids, bromeliads and other plants that make up one of the most complex ecosystems in the world.',
                'La flora es igualmente vibrante, con muchas orquídeas, bromelias y otras plantas que componen uno de los ecosistemas más complejos del mundo.'
            )),
        ];
    }

    private function extraPages(): void
    {
        $card = fn (string $image, array $title, array $text) => [
            'image' => $image, 'badge' => $this->same(''), 'by' => $this->same(''), 'author' => '', 'author_url' => '',
            'title' => $title, 'highlight' => $this->same(''), 'text' => $text,
        ];

        $this->page('noticias', $this->l('Notícias', 'News', 'Noticias'), $this->l(
            'Últimas notícias e atualizações da Forest Ark Investiments.',
            'Latest news and updates from Forest Ark Investiments.',
            'Últimas noticias y novedades de Forest Ark Investiments.'
        ), [
            ['cards', ['title' => $this->l('Últimas notícias e atualizações', 'Latest news and updates', 'Últimas noticias y novedades'), 'grid' => true, 'items' => $this->newsItems()]],
        ]);

        $this->page('casos-de-sucesso', $this->l('Casos de Sucesso', 'Success Stories', 'Casos de Éxito'), $this->l(
            'Concessão florestal e certificações da Forest Ark Investiments.',
            'Forest concession and certifications of Forest Ark Investiments.',
            'Concesión forestal y certificaciones de Forest Ark Investiments.'
        ), [
            ['cards', ['title' => $this->l('Casos de Sucesso', 'Success Stories', 'Casos de Éxito'), 'grid' => true, 'items' => [
                $card('uploads/plano-de-negocios/plano01.jpg', $this->l('Concessão Florestal da FLONA do Jamari', 'Jamari National Forest Concession', 'Concesión Forestal del Bosque Nacional de Jamari'), $this->l(
                    'Contrato de Concessão Florestal Nº 1/2022 com o Serviço Florestal Brasileiro, com vigência de 40 anos, de 2022 a 2062.',
                    'Forest Concession Agreement No. 1/2022 with the Brazilian Forest Service, for a term of 40 years, from 2022 to 2062.',
                    'Contrato de Concesión Forestal N.º 1/2022 con el Servicio Forestal Brasileño, con una vigencia de 40 años, de 2022 a 2062.'
                )),
                $card('uploads/certificacoes/certificado-ima-fm-coc-199966.jpg', $this->l('Certificação de Manejo Florestal', 'Forest Management Certification', 'Certificación de Manejo Forestal'), $this->l(
                    'Certificado de manejo florestal emitido pela Imaflora para a Forest Ark Flona Jamari.',
                    'Forest management certificate issued by Imaflora to Forest Ark Flona Jamari.',
                    'Certificado de manejo forestal emitido por Imaflora para Forest Ark Flona Jamari.'
                )),
                $card('uploads/certificacoes/certificacao-intuix-esg.jpg', $this->l('Certificações em Compliance e ESG', 'Compliance and ESG Certifications', 'Certificaciones en Compliance y ESG'), $this->l(
                    'Certificações concedidas pela Intuix, incluindo compliance trabalhista e ESG.',
                    'Certifications granted by Intuix, including labor compliance and ESG.',
                    'Certificaciones otorgadas por Intuix, incluidos compliance laboral y ESG.'
                )),
            ]]],
        ]);

        $this->page('portifolio', $this->l('Portfolio', 'Portfolio', 'Portafolio'), $this->l(
            'Ativos da concessão florestal da Forest Ark Investiments.',
            'Forest concession assets of Forest Ark Investiments.',
            'Activos de la concesión forestal de Forest Ark Investiments.'
        ), [
            ['cards', ['title' => $this->l('Portfolio', 'Portfolio', 'Portafolio'), 'grid' => true, 'items' => [
                $card('uploads/blog/002.png', $this->l('Madeira', 'Timber', 'Madera'), $this->l(
                    'Retirada e comercialização de ativos madeireiros, conforme o inventário de cada Unidade de Produção Florestal.',
                    'Harvesting and sale of timber assets, according to the inventory of each Forest Production Unit.',
                    'Extracción y comercialización de activos madereros, conforme al inventario de cada Unidad de Producción Forestal.'
                )),
                $card('uploads/plano-de-negocios/plano03.jpg', $this->l('Castanha do Brasil', 'Brazil Nut', 'Castaña de Brasil'), $this->l(
                    'Nativa da região norte do país, é um dos ativos da Concessão Florestal.',
                    'Native to the north of the country, it is one of the assets of the Forest Concession.',
                    'Nativa de la región norte del país, es uno de los activos de la Concesión Forestal.'
                )),
                $card('uploads/blog/001.png', $this->l('Crédito de Carbono', 'Carbon Credits', 'Créditos de Carbono'), $this->l(
                    'Comercialização autorizada pela Lei 14.590/2023.',
                    'Sale authorized by Law 14.590/2023.',
                    'Comercialización autorizada por la Ley 14.590/2023.'
                )),
                $card('uploads/blog/005.png', $this->l('Óleos, Resinas, Cascas, Cipós e Folhagem', 'Oils, Resins, Barks, Vines and Foliage', 'Aceites, Resinas, Cortezas, Lianas y Follaje'), $this->l(
                    'Insumos não madeireiros, muito utilizados na indústria farmacêutica e abundantes na FLONA.',
                    'Non-timber inputs, widely used in the pharmaceutical industry and abundant in the National Forest.',
                    'Insumos no madereros, muy utilizados en la industria farmacéutica y abundantes en el Bosque Nacional.'
                )),
            ]]],
        ]);
    }

    private function company(): void
    {
        $more = $this->l('Saiba mais', 'Learn more', 'Más información');
        $cert = fn (string $file, string $author, string $url) => [
            'image' => "uploads/certificacoes/$file", 'badge' => $this->same(''), 'by' => $this->l('Por', 'By', 'Por'),
            'author' => $author, 'author_url' => $url, 'title' => $this->same(''), 'highlight' => $this->same(''), 'text' => $this->same(''),
        ];
        $intuix = fn (string $file) => $cert($file, 'Intuix', 'https://intuix.com.br');

        $this->page('a-empresa', $this->l('A Empresa', 'The Company', 'La Empresa'), $this->l(
            'Princípios, valores e certificações da Forest Ark Investiments.',
            'Principles, values and certifications of Forest Ark Investiments.',
            'Principios, valores y certificaciones de Forest Ark Investiments.'
        ), [
            ['features', ['items' => [
                ['title' => $this->l('Princípio:', 'Principle:', 'Principio:'), 'text' => $this->l(
                    'A sustentabilidade da empresa é a compatibilização do resultado econômico-financeiro com a preservação do meio ambiente.',
                    "The company's sustainability lies in reconciling economic and financial results with the preservation of the environment.",
                    'La sostenibilidad de la empresa consiste en compatibilizar el resultado económico-financiero con la preservación del medio ambiente.'
                ), 'button' => $more, 'link' => '/'],
                ['title' => $this->l('Crença:', 'Belief:', 'Creencia:'), 'text' => $this->l(
                    'Conservação e melhoria do meio ambiente para as gerações futuras.',
                    'Conservation and improvement of the environment for future generations.',
                    'Conservación y mejora del medio ambiente para las generaciones futuras.'
                ), 'button' => $more, 'link' => '/'],
                ['title' => $this->l('Valores:', 'Values:', 'Valores:'), 'text' => $this->l(
                    'Ética, honestidade, transparência e consciência ambiental.',
                    'Ethics, honesty, transparency and environmental awareness.',
                    'Ética, honestidad, transparencia y conciencia ambiental.'
                ), 'button' => $more, 'link' => '/'],
            ]]],
            ['cards', ['title' => $this->l('Certificações', 'Certifications', 'Certificaciones'), 'items' => [
                $cert('certificado-ima-fm-coc-199966.jpg', 'Imaflora', 'https://www.imaflora.org'),
                $intuix('certificacao-intuix-clt.jpg'), $intuix('selo-intuix-clt.jpg'),
                $intuix('certificacao-intuix-esg.jpg'), $intuix('selo-intuix-esg.jpg'),
                $intuix('certificacao-intuix-mulher.jpg'), $intuix('selo-intuix-mulher.jpg'),
                $intuix('certificacao-intuix-safety.jpg'), $intuix('selo-intuix-safety.jpg'),
                $intuix('certificacao-intuix-cyber.jpg'), $intuix('selo-intuix-cyber.jpg'),
            ]]],
        ]);
    }

    private function services(): void
    {
        $doc = fn (array $title, array $button, string $file) => ['image' => 'uploads/servicos/logo.png', 'title' => $title, 'button' => $button, 'file' => "uploads/servicos/$file"];

        $this->page('servicos', $this->l('Serviços', 'Services', 'Servicios'), $this->l(
            'Documentos de manejo florestal e conservação da Forest Ark Investiments.',
            'Forest management and conservation documents from Forest Ark Investiments.',
            'Documentos de manejo forestal y conservación de Forest Ark Investiments.'
        ), [
            ['downloads', ['title' => $this->l('Serviços', 'Services', 'Servicios'), 'items' => [
                $doc($this->l('Plano de Manejo Florestal Sustentável', 'Sustainable Forest Management Plan', 'Plan de Manejo Forestal Sostenible'), $this->l('Baixar PMFS', 'Download SFMP', 'Descargar PMFS'), 'pmfs.pdf'),
                $doc($this->l('Avaliação de Alto Valor de Conservação', 'High Conservation Value Assessment', 'Evaluación de Alto Valor de Conservación'), $this->l('Baixar AAVC', 'Download HCVA', 'Descargar EAVC'), 'aavc.pdf'),
                $doc($this->l('Planilha de Conclusão de HVC', 'HCV Conclusion Worksheet', 'Planilla de Conclusión de AVC'), $this->l('Baixar Planilha', 'Download Worksheet', 'Descargar Planilla'), 'planilha_conclusao_hvc.pdf'),
                $doc($this->l('Relatório de Análise das Paisagens Florestais Intactas - IFL', 'Intact Forest Landscapes Analysis Report - IFL', 'Informe de Análisis de Paisajes Forestales Intactos - IFL'), $this->l('Baixar IFL', 'Download IFL', 'Descargar IFL'), 'anexoIII-relatorio-ifl.pdf'),
            ]]],
        ]);
    }

    private function contact(): void
    {
        $this->page('contato', $this->l('Contato', 'Contact', 'Contacto'), $this->l(
            'Solicite um retorno de contato da Forest Ark Investiments.',
            'Request a callback from Forest Ark Investiments.',
            'Solicite que Forest Ark Investiments se ponga en contacto con usted.'
        ), [
            ['contact_form', [
                'title' => $this->l('Solicite um retorno de contato', 'Request a callback', 'Solicite que le contactemos'),
                'name' => $this->l('Nome', 'Name', 'Nombre'),
                'email' => $this->l('Email', 'Email', 'Correo electrónico'),
                'phone' => $this->l('Telefone/WhatsApp', 'Phone/WhatsApp', 'Teléfono/WhatsApp'),
                'other' => $this->l('Outro assunto', 'Other subject', 'Otro asunto'),
                'message' => $this->l('Mensagem', 'Message', 'Mensaje'),
                'button' => $this->l('Enviar', 'Send', 'Enviar'),
                'items' => [
                    ['label' => $this->l('Produtos', 'Products', 'Productos')],
                    ['label' => $this->l('Serviços Prestados', 'Services Provided', 'Servicios Prestados')],
                    ['label' => $this->l('Vendas', 'Sales', 'Ventas')],
                    ['label' => $this->l('Financeiro', 'Finance', 'Finanzas')],
                ],
            ]],
        ]);
    }
}
