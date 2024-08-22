<?php

namespace App\Http\Controllers;

use App\Models\Bahasa;
use App\Models\Buku;
use App\Models\File;
use App\Models\GMD;
use App\Models\LampiranBuku;
use App\Models\Penerbit;
use App\Models\Pengarang;
use App\Models\PengarangBuku;
use App\Models\RelasiBuku;
use App\Models\TempatTerbit;
use App\Models\TipeKoleksi;
use App\Models\Topik;
use App\Models\TopikBuku;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class MengelolaBuku extends Controller
{
    public function tampilkanBuku()
    {
        return view('buku.index', [
            'title' => 'Bibliografi',
            'books' => Buku::all(),
        ]);
    }

    public function formTambahBuku()
    {
        return view('buku.create', [
            'title' => 'Tambah Bibliografi Baru',
            'authors' => Pengarang::all(),
            'publishers' => Penerbit::all(),
            'publishPlaces' => TempatTerbit::all(),
            'languages' => Bahasa::all(),
            'gmds' => GMD::all(),
            'topics' => Topik::all(),
            'collTypes' => TipeKoleksi::all(),
            'books' => Buku::all(),
        ]);
    }

    public function simpanBuku(Request $request)
    {
        $data = $request->validate([
            'gmd_id' => 'nullable|integer',
            'title' => 'required',
            'authors' => 'nullable|array',
            'sor' => 'nullable|max:200',
            'edition' => 'nullable|max:50',
            'isbn_issn' => 'nullable|max:20|regex:/^[0-9\-]+$/',
            'publisher_id' => 'nullable',
            'publish_place_id' => 'nullable',
            'publish_year' => 'nullable|integer|regex:/^\d{4}$/',
            'collation' => 'nullable|max:50',
            'language_id' => 'nullable|string',
            'series_title' => 'nullable|max:200',
            'call_number' => 'nullable|max:50',
            'classification' => 'nullable|max:40',
            'notes' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'image_url' => 'nullable|url',
            'opac_hide' => 'nullable|boolean',
            'promoted' => 'nullable|boolean',
            'subjects' => 'nullable|array',
            'labels' => 'nullable|array',
            'labels.*' => 'nullable|array',
            'labels.*.1' => 'required_with:labels.*.0',
            'frequency_id' => 'required|integer',
            'spec_detail_info' => 'nullable',
            'coll_type_id' => 'nullable',
            'content_type_id' => 'nullable|integer',
            'media_type_id' => 'nullable|integer',
            'carrier_type_id' => 'nullable|integer',
            'biblio_relation' => 'nullable|array',
            'attachment_file' => 'nullable|array',
            'attachment_file.*.file_title' => 'required_with:attachment_file',
            'attachment_file.*.file_name' => 'required_with:attachment_file|max:2048',
            'attachment_file.*.file_url' => 'nullable',
            'attachment_file.*.file_desc' => 'nullable',
            'attachment_file.*.access_type' => 'string|in:public,private',
            'attachment_file.*.access_limit' => 'nullable|array',
        ]);

        if (isset($data['authors']) && !empty($data['authors'])) {
            foreach($data['authors'] as $author) {
                if (!is_numeric($author)) {
                    $data['authors'][] = Pengarang::create([
                        'author_name' => $author
                    ])->author_id;
                }
            }

            $data['authors'] = array_values(array_filter(
                $data['authors'], fn($a) => is_numeric($a))
            );
        }

        if (isset($data['publisher_id']) && !is_numeric($data['publisher_id'])) {
            $data['publisher_id'] = Penerbit::create([
                'publisher_name' => $data['publisher_id']
            ])->publisher_id;
        }

        if (isset($data['publish_place_id']) && !is_numeric($data['publish_place_id'])) {
            $data['publish_place_id'] = TempatTerbit::create([
                'place_name' => $data['publish_place_id']
            ])->publish_place_id;
        }

        if (isset($data['language_id']) && strlen($data['language_id']) > 2) {
            $language = explode(':', $data['language_id']);

            if (count($language) != 2) {
                $language[1] = $language[0];
                $language[0] = substr(strtolower($language[1]), 0, 2);
            }

            $data['language_id'] = Bahasa::create([
                'language_id' => $language[0],
                'language_name' => $language[1],
            ])->language_id;
        }

        if (isset($data['subjects']) && !empty($data['subjects'])) {
            foreach($data['subjects'] as $subject) {
                if (!is_numeric($subject)) {
                    $data['subjects'][] = Topik::create([
                        'topic' => $subject,
                        'topic_type' => 't',
                        'classification' => ''
                    ])->topic_id;
                }
            }

            $data['subjects'] = array_values(array_filter(
                $data['subjects'], fn($s) => is_numeric($s))
            );
        }

        if (isset($data['coll_type_id']) && !is_numeric($data['coll_type_id'])) {
            $data['coll_type_id'] = TipeKoleksi::create([
                'coll_type_name' => $data['coll_type_id']
            ])->coll_type_id;
        }

        unset($data['image_url']);
        $data['image'] = 'storage/'.$request->file('image')?->store('cover', 'public');
        $data['labels'] = isset($data['labels']) ? serialize(array_values($data['labels'])) : null;

        $biblioId = Buku::create($data)->biblio_id;

        if (isset($data['authors'])) {
            $biblioAuthors = collect($data['authors'])->map(fn($authorId, $index) => [
                'biblio_id' => $biblioId,
                'author_id' => $authorId,
                'level' => $index + 1
            ], $data['authors'])->toArray();

            PengarangBuku::insert($biblioAuthors);
        }

        if (isset($data['biblio_relation']) && !empty($data['biblio_relation'])) {
            $biblioRelations = array_map(fn($biblioRelationId) => [
                'biblio_id' => $biblioId,
                'rel_biblio_id' => $biblioRelationId,
            ], $data['biblio_relation']);

            RelasiBuku::insert($biblioRelations);
        }

        if (isset($data['subjects']) && !empty($data['subjects'])) {
            $biblioTopics = array_map(fn($topic) => [
                'biblio_id' => $biblioId,
                'topic_id' => $topic,
            ], $data['subjects']);

            TopikBuku::insert($biblioTopics);
        }

        if (isset($data['attachment_file']) && !empty($data['attachment_file'])) {
            foreach ($data['attachment_file'] as $file) {
                $fileId = File::create([
                    'file_title' => $file['file_title'],
                    'file_name' => $file['file_name']->store('lampiran', 'public'),
                    'file_url' => $file['file_url'],
                    'file_dir' => '',
                    'mime_type' => $file['file_name']->getMimeType(),
                    'file_desc' => $file['file_desc'],
                    'uploader_id' => auth()->user()->user_id,
                ])->file_id;

                if ($file['access_type'] == 'private') {
                    $accessLimit = !empty($file['access_limit'])
                        ? serialize($file['access_limit'])
                        : null;
                } else {
                    $accessLimit = null;
                }

                LampiranBuku::create([
                    'biblio_id' => $biblioId,
                    'file_id' => $fileId,
                    'access_type' => $file['access_type'],
                    'access_limit' => $accessLimit,
                ]);
            }
        }

        return response(status: 200);
    }

    public function lacakBuku($isbn)
    {
        $scraper = 'https://api.scraperapi.com/?api_key=9abba389b59aeaf6528d5eacbb5c65ea&url=';
        $isbnPerpusnas = "https://isbn.perpusnas.go.id/Account/GetBuku?kd1=ISBN&kd2=$isbn&limit=10&offset=0";
        $googleBooks = "https://www.googleapis.com/books/v1/volumes?q=isbn:$isbn&fields=items(volumeInfo)";
        // $opacPerpusnas = $scraper."https%3A%2F%2Fopac.perpusnas.go.id%2FResultListOpac.aspx%3FpDataItem%3D$isbn%26pType%3DIsbn%26pLembarkerja%3D-1%26pPilihan%3DIsbn";
        $opacPerpusnas = "https://opac.perpusnas.go.id/ResultListOpac.aspx?pDataItem=$isbn&pType=Isbn&pLembarkerja=-1&pPilihan=Isbn";

        try {
            $responses = Http::pool(fn (Pool $pool) => [
                $pool->get($opacPerpusnas),
                $pool->get($isbnPerpusnas),
                $pool->get($googleBooks),
            ]);

            $isbnPerpusnas = $responses[1]->json('rows');
            $googleBooks = $responses[2]->json('items', []);
            $opacPerpusnas = $responses[0]->body();

            // $isbnPerpusnas = Http::get($isbnPerpusnas)['rows'];
            // $googleBooks = Http::get($googleBooks)->json('items', []);
            // $opacPerpusnas = Http::get($scraper.$opacPerpusnas)->body();
        } catch (\Throwable $th) {
            $opacPerpusnas = '';
        }

        $crawler = new Crawler($opacPerpusnas);
        $opacPerpusnas = $crawler->filter('span[title="Judul"] a');

        if (!$isbnPerpusnas && !$googleBooks && !$opacPerpusnas->count()) {
            return response(status: 404);
        }

        $book = $this->mapBook($isbnPerpusnas, $googleBooks, $crawler, $isbn);

        if ($opacPerpusnas->count()) {
            $detail = 'https://opac.perpusnas.go.id/'.$opacPerpusnas->attr('href');

            try {
                // $detail = Http::get($scraper.urlencode($detail))->body();
                $detail = Http::get($detail)->body();
            } catch (\Throwable $th) {
                $detail = '';
            }

            $book = $this->mapDetailBook($detail, $book);
        }

        $book['time'] = microtime(true) - LARAVEL_START;
        return response($book);
    }

    // private function mapBook($isbnPerpusnas, $googleBooks, $opacPerpusnas, $isbn)
    // {
    //     $book = [];

    //     if ($isbnPerpusnas) {
    //         $authors = preg_replace(
    //             '/\||penulis|;.*$|\[.*?\]|\b[A-Z]\.[A-Za-z]+\b/i',
    //             '',
    //             $isbnPerpusnas[0]['Pengarang']
    //         );

    //         $authors = array_values(array_filter(
    //             array_map('trim', explode(',', $authors)),
    //             fn($a) => $a !== '.' && $a !== '' && substr_count($a, '.') < 2
    //         ));

    //         $book['title'] = ucwords($isbnPerpusnas[0]['Judul']);
    //         $book['authors[]'] = $authors;
    //         $book['publisher_id'] = $isbnPerpusnas[0]['Penerbit'];
    //         $book['publish_year'] = $isbnPerpusnas[0]['Tahun'];
    //         $book['series_title'] = $isbnPerpusnas[0]['Seri'];
    //     }

    //     if ($googleBooks) {
    //         $data = $googleBooks[0]['volumeInfo'];
    //         preg_match('/\b(\d{4})\b/', $data['publishedDate'] ?? '', $matches);

    //         $book['title'] = $data['title'];
    //         $book['authors[]'] = $data['authors']
    //             ?? (isset($book['authors[]']) && !empty($book['authors[]'])
    //                 ? $book['authors[]']
    //                 : []);
    //         $book['publisher_id'] = $book['publisher_id'] ?? $data['publisher'] ?? null;
    //         $book['publish_year'] = $book['publish_year'] ?? $matches[1] ?? null;

    //         $book['subjects[]'] = $data['categories'] ?? [];
    //         $book['image_url'] = $data['imageLinks']['smallThumbnail'] ?? null;
    //         $book['language_id'] = $data['language'] ?? null;
    //         $book['notes'] = $data['description'] ?? null;
    //         $book['collation'] = isset($data['pageCount']) && $data['pageCount'] != 0
    //             ? $data['pageCount'] . ' halaman'
    //             : null;
    //     }

    //     if ($opacPerpusnas->filter('span[title="Judul"] a')->count()) {
    //         $titleAuthor = explode(
    //             '/', $opacPerpusnas->filter('span[title="Judul"] a')->text()
    //         );

    //         $title = count($titleAuthor) == 2
    //             ? $titleAuthor[0]
    //             : implode(' ', array_slice($titleAuthor, 0, count($titleAuthor) > 1 ? -1 : 1));

    //         $authorsFromTitle = preg_replace(
    //             '/penulis|;.*$|\b[A-Z]\.[A-Za-z]+\b/i',
    //             '',
    //             end($titleAuthor)
    //         );

    //         $authorsFromTitle = array_values(array_filter(
    //             array_map('trim', preg_split('/[&,]/', $authorsFromTitle)),
    //             fn($a) => $a !== '.' && $a !== '' && substr_count($a, '.') < 2
    //         ));

    //         $authorsFromCreator = array_values(array_filter(
    //             $opacPerpusnas->filter('#Lpengarang a')->each(fn($node) => $node->text()),
    //             fn($author) =>
    //             stripos($author, 'editor') === false && stripos($author, 'penyunting') === false
    //         ));

    //         $authorsFromCreator = array_map(function($author) {
    //             $author = trim(preg_replace('/-|[0-9]|\(.*?\)/', '', $author));
    //             $author = explode(', ', preg_replace('/,$/', '', $author));

    //             return implode(' ', array_reverse($author));
    //         }, $authorsFromCreator);

    //         $authors = !empty($authorsFromCreator) ? $authorsFromCreator : $authorsFromTitle;

    //         $book['title'] = $book['title'] ?? ucwords(trim($title));
    //         $book['authors[]'] = isset($book['authors[]']) && !empty($book['authors[]'])
    //             ? $book['authors[]']
    //             : $authors;
    //         $book['publisher_id'] = $book['publisher_id']
    //             ?? ($opacPerpusnas->filter('#ulPublisher li')->count()
    //                 ? $opacPerpusnas->filter('#ulPublisher li')->attr('id')
    //                 : null);
    //         $book['publish_year'] = $book['publish_year']
    //             ?? ($opacPerpusnas->filter('#ulPublishYear li')->count()
    //                 ? $opacPerpusnas->filter('#ulPublishYear li')->attr('id')
    //                 : null);
    //         $book['subjects[]'] = array_values(array_unique(array_merge(
    //             $book['subjects[]'] ?? [],
    //             array_merge(...$opacPerpusnas->filter('#ulSubject li')->each(function(Crawler $node) {
    //                 // Gunakan regex untuk split string yang ada di dalam kurung
    //                 preg_match_all('/[^,()]+/', $node->attr('id'), $matches);

    //                 return array_map('trim', $matches[0]);
    //             }))
    //         )));
    //         $book['image_url'] = $book['image_url']
    //             ?? ($opacPerpusnas->filter('#Image1')->count()
    //                 ? 'https://opac.perpusnas.go.id/'.$opacPerpusnas->filter('#Image1')->attr('src')
    //                 : null);
    //         $book['language_id'] = $book['language_id']
    //             ?? ($opacPerpusnas->filter('#ulLanguage li')->count()
    //                 ? $opacPerpusnas->filter('#ulLanguage li')->attr('id')
    //                 : null);

    //         $book['publish_place_id'] = $opacPerpusnas->filter('#ulPublishLocation li')->count()
    //             ? $opacPerpusnas->filter('#ulPublishLocation li')->attr('id')
    //             : null;
    //         $book['spec_detail_info'] = $opacPerpusnas->filter('#Label14')->count()
    //             ? $opacPerpusnas->filter('#Label14')->text()
    //             : null;
    //     }

    //     $book['media_type_id'] = 'unmediated';
    //     $book['carrier_type_id'] = 'volume';
    //     $book['content_type_id'] = 'text';
    //     $book['isbn_issn'] = $isbn;

    //     return $book;
    // }

    private function mapBook($isbnPerpusnas, $googleBooks, $opacPerpusnas, $isbn)
    {
        $book = [];

        if ($isbnPerpusnas) {
            $authors = preg_replace(
                '/\||penulis|;.*$|\[.*?\]|\b[A-Z]\.[A-Za-z]+\b/i',
                '',
                $isbnPerpusnas[0]['Pengarang']
            );

            $authors = array_values(array_filter(
                array_map('trim', explode(',', $authors)),
                fn($a) => $a !== '.' && $a !== '' && substr_count($a, '.') < 2
            ));

            $book['title'] = ucwords($isbnPerpusnas[0]['Judul']);
            $book['authors[]'] = $authors;
            $book['publisher_id'] = $isbnPerpusnas[0]['Penerbit'];
            $book['publish_year'] = $isbnPerpusnas[0]['Tahun'];
            $book['series_title'] = $isbnPerpusnas[0]['Seri'];
        }

        if ($googleBooks) {
            $data = $googleBooks[0]['volumeInfo'];
            preg_match('/\b(\d{4})\b/', $data['publishedDate'] ?? '', $matches);

            $book['title'] = $data['title'];
            $book['authors[]'] = $data['authors']
                ?? (isset($book['authors[]']) && !empty($book['authors[]'])
                    ? $book['authors[]']
                    : []);
            $book['publisher_id'] = $book['publisher_id'] ?? $data['publisher'] ?? null;
            $book['publish_year'] = $book['publish_year'] ?? $matches[1] ?? null;

            $book['subjects[]'] = $data['categories'] ?? [];
            $book['image_url'] = $data['imageLinks']['smallThumbnail'] ?? null;
            $book['language_id'] = $data['language'] ?? null;
            $book['notes'] = $data['description'] ?? null;
            $book['collation'] = isset($data['pageCount']) && $data['pageCount'] != 0
                ? $data['pageCount'] . ' halaman'
                : null;
        }

        if ($opacPerpusnas->filter('span[title="Judul"] a')->count()) {
            $titleAuthor = explode(
                '/', $opacPerpusnas->filter('span[title="Judul"] a')->text()
            );

            $title = count($titleAuthor) == 2
                ? $titleAuthor[0]
                : implode(' ', array_slice($titleAuthor, 0, count($titleAuthor) > 1 ? -1 : 1));

            $authorsFromTitle = preg_replace(
                '/penulis|;.*$|\b[A-Z]\.[A-Za-z]+\b/i',
                '',
                end($titleAuthor)
            );

            $authorsFromTitle = array_values(array_filter(
                array_map('trim', preg_split('/[&,]/', $authorsFromTitle)),
                fn($a) => $a !== '.' && $a !== '' && substr_count($a, '.') < 2
            ));

            $authorsFromCreator = array_values(array_filter(
                $opacPerpusnas->filter('#Lpengarang a')->each(fn($node) => $node->text()),
                fn($author) =>
                stripos($author, 'editor') === false && stripos($author, 'penyunting') === false
            ));

            $authorsFromCreator = array_map(function($author) {
                $author = trim(preg_replace('/-|[0-9]|\(.*?\)/', '', $author));
                $author = explode(', ', preg_replace('/,$/', '', $author));

                return implode(' ', array_reverse($author));
            }, $authorsFromCreator);

            $authors = !empty($authorsFromCreator) ? $authorsFromCreator : $authorsFromTitle;

            $book['title'] = $book['title'] ?? ucwords(trim($title));
            $book['authors[]'] = isset($book['authors[]']) && !empty($book['authors[]'])
                ? $book['authors[]']
                : $authors;
            $book['publisher_id'] = $book['publisher_id']
                ?? $opacPerpusnas->filter('#ulPublisher li')->attr('id', '');
            $book['publish_year'] = $book['publish_year']
                ?? $opacPerpusnas->filter('#ulPublishYear li')->attr('id', '');
            $book['subjects[]'] = array_values(array_unique(array_merge(
                $book['subjects[]'] ?? [],
                array_merge(...$opacPerpusnas->filter('#ulSubject li')->each(function(Crawler $node) {
                    // Gunakan regex untuk split string yang ada di dalam kurung
                    preg_match_all('/[^,()]+/', $node->attr('id'), $matches);

                    return array_map('trim', $matches[0]);
                }))
            )));
            $book['image_url'] = $book['image_url']
                ?? ($opacPerpusnas->filter('#Image1')->count()
                    ? 'https://opac.perpusnas.go.id/'.$opacPerpusnas->filter('#Image1')->attr('src')
                    : null);
            $book['language_id'] = $book['language_id']
                ?? $opacPerpusnas->filter('#ulLanguage li')->attr('id', '');

            $book['publish_place_id'] = $opacPerpusnas->filter('#ulPublishLocation li')->attr('id', '');
            $book['spec_detail_info'] = $opacPerpusnas->filter('#Label14')->text('');
        }

        $book['media_type_id'] = 'unmediated';
        $book['carrier_type_id'] = 'volume';
        $book['content_type_id'] = 'text';
        $book['isbn_issn'] = $isbn;
        $book['gmd_id'] = $book['subjects[]'] ?? [];
        $book['biblio_relation[]'] = $book['subjects[]'] ?? [];

        return $book;
    }

    private function mapDetailBook($detail, $book)
    {
        $crawler = new Crawler($detail);

        $book['collation'] = $crawler->filter('#lblDeskripsiFisik')->text($book['collation'] ?? '');

        $book['spec_detail_info'] = $crawler->filter('#lblCatatan')->text($book['spec_detail_info']);

        $book['notes'] = $book['notes'] ?? $crawler->filter('#lblAbstrak')->text('');
        $book['edition'] = $crawler->filter('#lblEdisi')->text('');
        $book['coll_type_id'] = ucwords($crawler->filter('#lblBentukKarya')->text(''));

        return $book;
    }

    // private function mapDetailBook($detail, $book)
    // {
    //     $crawler = new Crawler($detail);

    //     $book['collation'] = $crawler->filter('#lblDeskripsiFisik')->count()
    //         ? $crawler->filter('#lblDeskripsiFisik')->text()
    //         : ($book['collation'] ?? null);

    //     $book['spec_detail_info'] = $crawler->filter('#lblCatatan')->count()
    //         ? $crawler->filter('#lblCatatan')->text()
    //         : $book['spec_detail_info'];

    //     $book['notes'] = $book['notes']
    //         ?? ($crawler->filter('#lblAbstrak')->count()
    //             ? $crawler->filter('#lblAbstrak')->text()
    //             : null);

    //     $book['edition'] = $crawler->filter('#lblEdisi')->count()
    //         ? $crawler->filter('#lblEdisi')->text()
    //         : null;

    //     $book['coll_type_id'] = $crawler->filter('#lblBentukKarya')->count()
    //         ? ucwords($crawler->filter('#lblBentukKarya')->text())
    //         : null;

    //     return $book;
    // }
}
