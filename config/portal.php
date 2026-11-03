<?php

// Campos: type = text|textarea|html|url|image|file|toggle|color|repeater; tr = traduzível por idioma.
$t = fn (string $name, string $label, string $type = 'text') => ['name' => $name, 'label' => $label, 'type' => $type, 'tr' => true];
$s = fn (string $name, string $label, string $type = 'text') => ['name' => $name, 'label' => $label, 'type' => $type, 'tr' => false];
// Imagem com o tamanho ideal: largura, altura e orientação para quem envia.
$img = fn (string $name, string $label, int $w, int $h, string $note = '') => ['name' => $name, 'label' => $label, 'type' => 'image', 'tr' => false, 'size' => [$w, $h], 'note' => $note];
$items = fn (string $label, array $fields) => ['name' => 'items', 'label' => $label, 'type' => 'repeater', 'tr' => false, 'fields' => $fields];

return [
    'developer' => [
        'name' => 'GAPI Tecnologia & Sistemas',
        'slogan' => 'Soluções Inteligentes em Arquitetura Web',
        'url' => 'https://gapitecnologia.com.br',
    ],

    'actions' => ['acessar' => 'Acessar', 'incluir' => 'Incluir', 'editar' => 'Editar', 'excluir' => 'Excluir'],

    'modules' => [
        'pages' => ['label' => 'Páginas, Blocos e Banners', 'group' => 'Conteúdo'],
        'menus' => ['label' => 'Menus', 'group' => 'Conteúdo'],
        'messages' => ['label' => 'Contatos', 'group' => 'Mensagens'],
        'subscribers' => ['label' => 'Inscritos', 'group' => 'Mensagens'],
        'newsletters' => ['label' => 'Newsletter', 'group' => 'Mensagens'],
        'settings' => ['label' => 'Configurações', 'group' => 'Sistema'],
        'languages' => ['label' => 'Idiomas', 'group' => 'Sistema'],
        'users' => ['label' => 'Usuários', 'group' => 'Sistema'],
    ],

    'newsletter_fields' => [
        $t('subject', 'Assunto'),
        $t('body', 'Conteúdo', 'html'),
    ],

    'page_fields' => [
        $t('title', 'Título da página'),
        $t('meta_description', 'Descrição para buscadores', 'textarea'),
    ],

    'menu_fields' => [
        $t('label', 'Rótulo'),
        $s('url', 'Link (ex.: /a-empresa)', 'url'),
    ],

    'settings_groups' => [
        'Identidade' => [
            $s('site_name', 'Nome do site'),
            $img('logo', 'Logo', 200, 103, 'PNG com fundo transparente. Aparece no menu e no rodapé.'),
            $img('favicon', 'Favicon', 512, 512, 'Quadrado, em PNG. É o ícone da aba do navegador.'),
        ],
        'Cores' => [
            $s('color_brand', 'Cor principal', 'color'),
            $s('color_header', 'Fundo do menu', 'color'),
            $s('color_footer', 'Fundo do rodapé', 'color'),
            $s('color_footer_bottom', 'Fundo da barra final', 'color'),
        ],
        'Contato' => [
            $s('email', 'E-mail'),
            $s('phone', 'Telefone'),
            $s('whatsapp', 'WhatsApp (somente números, com DDI)'),
            $s('address', 'Endereço'),
            $s('city', 'Cidade - UF'),
            $s('notify_email', 'E-mail que recebe o formulário de contato'),
        ],
        'Textos do cabeçalho e rodapé' => [
            $t('label_email', 'Rótulo "Email"'),
            $t('label_phone', 'Rótulo "Telefone"'),
            $t('footer_call', 'Chamada do telefone'),
            $t('footer_send', 'Chamada do e-mail'),
            $t('footer_about_title', 'Título "Sobre nós"'),
            $t('footer_about', 'Texto "Sobre nós"', 'textarea'),
            $t('footer_where', 'Título "Onde estamos"'),
            $t('footer_links', 'Título dos links'),
            $t('footer_comments', 'Título da coluna central'),
            $t('footer_subscribe', 'Título da inscrição'),
            $t('footer_placeholder', 'Campo de e-mail da inscrição'),
            $t('footer_subscribe_text', 'Texto da inscrição', 'textarea'),
            $t('footer_copyright', 'Direitos autorais (:year = ano, :name = nome)'),
        ],
        'Mensagens do site' => [
            $t('msg_contact_ok', 'Contato enviado'),
            $t('msg_subscribe_ok', 'Inscrição concluída'),
            $t('msg_404_title', 'Erro 404: título'),
            $t('msg_404_text', 'Erro 404: texto'),
            $t('msg_404_button', 'Erro 404: botão'),
        ],
    ],

    'blocks' => [
        'hero_slider' => ['label' => 'Banner principal', 'fields' => [
            $items('Banners', [
                $img('image', 'Imagem', 1920, 1000, 'Mantenha o principal no centro: as laterais são cortadas em telas menores.'),
                $t('title', 'Título'),
                $t('text', 'Texto', 'textarea'),
                $s('show_text', 'Mostrar título e texto', 'toggle'),
                $s('show_button', 'Mostrar botão', 'toggle'),
                $t('button', 'Texto do botão'),
                $s('link', 'Link do botão', 'url'),
            ]),
        ]],
        'testimonials' => ['label' => 'Depoimentos', 'fields' => [
            $img('background', 'Imagem de fundo', 1920, 1000, 'Ocupa toda a largura e pode ser cortada nas bordas.'),
            $items('Depoimentos', [
                $t('text', 'Texto', 'html'),
                $t('name', 'Nome'),
                $t('role', 'Cargo'),
            ]),
        ]],
        'about_accordion' => ['label' => 'Texto com acordeão e imagens', 'fields' => [
            $t('title', 'Título'),
            $t('subtitle', 'Subtítulo'),
            $t('intro', 'Introdução', 'html'),
            $t('button', 'Texto do botão'),
            $s('link', 'Link do botão', 'url'),
            $img('image_1', 'Imagem grande', 960, 1780, 'Vertical. Ocupa a coluna alta ao lado do texto.'),
            $img('image_2', 'Imagem superior', 960, 890, 'Quase quadrada.'),
            $img('image_3', 'Imagem inferior', 960, 890, 'Quase quadrada.'),
            ['name' => 'documents', 'label' => 'Documentos (opcional)', 'type' => 'repeater', 'tr' => false, 'fields' => [
                $t('label', 'Nome do documento'),
                $s('file', 'Arquivo (PDF ou imagem)', 'file'),
                $s('url', 'Ou link externo', 'url'),
            ]],
            $items('Acordeão', [$t('title', 'Título'), $t('body', 'Conteúdo', 'html')]),
        ]],
        'faq' => ['label' => 'Perguntas frequentes', 'fields' => [
            $t('title', 'Título'),
            $img('image', 'Imagem lateral', 1000, 640, 'Exibida no tamanho original, à esquerda das perguntas: uma imagem maior invade o texto. Não aparece em telas menores.'),
            $items('Perguntas', [$t('title', 'Pergunta'), $t('body', 'Resposta', 'html')]),
        ]],
        'band' => ['label' => 'Faixa com logos', 'fields' => [
            $img('background', 'Imagem de fundo', 1920, 1080, 'Fica fixa ao rolar a página, sob uma camada colorida.'),
            $items('Logos', [$img('image', 'Logo', 300, 96, 'PNG com fundo transparente.')]),
        ]],
        'cards' => ['label' => 'Cards (carrossel ou grade)', 'fields' => [
            $t('title', 'Título'),
            $s('grid', 'Mostrar em grade, sem carrossel', 'toggle'),
            $items('Cards', [
                $img('image', 'Imagem', 1000, 667, 'Use a mesma proporção em todos os cards para ficarem alinhados.'),
                $t('badge', 'Etiqueta sobre a imagem'),
                $t('by', 'Prefixo do autor (ex.: Por)'),
                $s('author', 'Autor'),
                $s('author_url', 'Link do autor', 'url'),
                $t('title', 'Título'),
                $t('highlight', 'Destaque do título'),
                $t('text', 'Texto', 'textarea'),
            ]),
        ]],
        'features' => ['label' => 'Carrossel de destaques', 'fields' => [
            $items('Destaques', [
                $t('title', 'Título'),
                $t('text', 'Texto', 'textarea'),
                $t('button', 'Texto do link'),
                $s('link', 'Link', 'url'),
            ]),
        ]],
        'downloads' => ['label' => 'Grade de documentos', 'fields' => [
            $t('title', 'Título'),
            $items('Documentos', [
                $img('image', 'Imagem', 450, 232, 'Horizontal, em PNG com fundo transparente.'),
                $t('title', 'Título'),
                $t('button', 'Texto do botão'),
                $s('file', 'Arquivo', 'file'),
            ]),
        ]],
        'contact_form' => ['label' => 'Formulário de contato', 'fields' => [
            $t('title', 'Título'),
            $t('name', 'Campo nome'),
            $t('email', 'Campo e-mail'),
            $t('phone', 'Campo telefone'),
            $t('other', 'Campo outro assunto'),
            $t('message', 'Campo mensagem'),
            $t('button', 'Texto do botão'),
            $items('Assuntos', [$t('label', 'Assunto')]),
        ]],
        'rich_text' => ['label' => 'Texto livre', 'fields' => [
            $t('title', 'Título'),
            $t('body', 'Conteúdo', 'html'),
        ]],
    ],
];
