<?php

namespace App\Support;

use App\Models\Block;
use App\Models\Entry;
use App\Models\MenuItem;
use App\Models\Page;

/** Move os itens guardados dentro dos blocos para os módulos de conteúdo. Pode rodar mais de uma vez. */
class CollectionMigrator
{
    /** [página, tipo do bloco, título em português ou null, módulo] */
    private const MAP = [
        ['', 'hero_slider', null, 'banners'],
        ['', 'testimonials', null, 'testimonials'],
        ['', 'about_accordion', null, 'plan'],
        ['', 'faq', null, 'faq'],
        ['', 'cards', 'Últimas notícias e atualizações', 'news'],
        ['', 'cards', 'Parceiros', 'partners'],
        ['noticias', 'cards', null, 'news'],
        ['casos-de-sucesso', 'cards', null, 'success_cases'],
        ['portifolio', 'cards', null, 'portfolio'],
        ['a-empresa', 'features', null, 'values'],
        ['a-empresa', 'cards', null, 'certifications'],
        ['servicos', 'downloads', null, 'services'],
        ['contato', 'contact_form', null, 'subjects'],
    ];

    public static function run(): void
    {
        foreach (self::MAP as [$slug, $type, $title, $collection]) {
            $page = Page::where('slug', $slug)->first();
            if (! $page) {
                continue;
            }
            foreach (Block::where('page_id', $page->id)->where('type', $type)->get() as $block) {
                $data = $block->data ?? [];
                if (isset($data['source']) || ($title && ($data['title']['pt'] ?? null) !== $title)) {
                    continue;
                }
                self::import($collection, (array) ($data['items'] ?? []));
                unset($data['items']);
                $data['source'] = $collection;
                $block->update(['data' => $data]);
            }
        }

        self::ensureAboutPage();
    }

    private static function import(string $collection, array $items): void
    {
        if (Entry::where('collection', $collection)->exists()) {
            return; // a mesma lista aparece em mais de uma página: importa só uma vez
        }
        $def = Entry::definition($collection);
        foreach (array_values($items) as $i => $item) {
            $entry = new Entry(['collection' => $collection, 'data' => $item, 'position' => $i, 'is_published' => true]);
            if ($def['dated'] ?? false) {
                $entry->published_at = now()->subMinutes($i); // mantém a ordem atual
            }
            if ($def['detail'] ?? false) {
                $entry->slug = Entry::uniqueSlug($collection, $entry->slugSource());
            }
            $entry->save();
        }
    }

    /** "Sobre nós" ganha página própria, como o link original do site previa. */
    private static function ensureAboutPage(): void
    {
        if (Page::where('slug', 'sobre-nos')->exists()) {
            return;
        }
        $page = Page::create(['slug' => 'sobre-nos', 'is_published' => true, 'data' => [
            'title' => ['pt' => 'Sobre Nós', 'en' => 'About Us', 'es' => 'Sobre Nosotros'],
            'meta_description' => ['pt' => 'Conheça a Forest Ark Investiments.', 'en' => 'Get to know Forest Ark Investiments.', 'es' => 'Conozca Forest Ark Investiments.'],
        ]]);
        Block::create(['page_id' => $page->id, 'type' => 'rich_text', 'position' => 0, 'data' => [
            'title' => ['pt' => 'Sobre Nós', 'en' => 'About Us', 'es' => 'Sobre Nosotros'],
            'image' => '',
            'body' => [
                'pt' => '<p>Uma empresa que trabalha a produção de madeira de forma renovável, com resultados econômico-financeiros sustentáveis e sem danos ao ecossistema global.</p><p>Contrato de Concessão Florestal Nº 1/2022 com o Serviço Florestal Brasileiro, na FLONA do Jamari, com vigência de 40 anos, de 2022 a 2062.</p>',
                'en' => '<p>A company that produces timber renewably, with sustainable economic and financial results and no harm to the global ecosystem.</p><p>Forest Concession Agreement No. 1/2022 with the Brazilian Forest Service, in the Jamari National Forest, for a term of 40 years, from 2022 to 2062.</p>',
                'es' => '<p>Una empresa que produce madera de forma renovable, con resultados económico-financieros sostenibles y sin daños al ecosistema global.</p><p>Contrato de Concesión Forestal N.º 1/2022 con el Servicio Forestal Brasileño, en el Bosque Nacional de Jamari, con una vigencia de 40 años, de 2022 a 2062.</p>',
            ],
        ]]);

        MenuItem::where('location', 'footer')->get()->each(function (MenuItem $item) {
            if (($item->data['url'] ?? null) === '/a-empresa') {
                $item->update(['data' => ['url' => '/sobre-nos'] + $item->data]);
            }
        });
        Block::where('type', 'about_accordion')->get()->each(function (Block $block) {
            if (($block->data['link'] ?? null) === '/a-empresa') {
                $block->update(['data' => ['link' => '/sobre-nos'] + $block->data]);
            }
        });
    }
}
