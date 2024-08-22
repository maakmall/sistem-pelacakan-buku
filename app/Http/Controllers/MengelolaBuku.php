<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class MengelolaBuku extends Controller
{
    public function tampilkanBuku()
    {
        $books = DB::table('biblio')
            ->leftJoin('mst_publisher', 'biblio.publisher_id', '=', 'mst_publisher.publisher_id')
            ->leftJoin('biblio_author', 'biblio.biblio_id', '=', 'biblio_author.biblio_id')
            ->leftJoin('mst_author', 'biblio_author.author_id', '=', 'mst_author.author_id')
            ->select(
                'biblio.biblio_id',
                'biblio.title',
                'biblio.isbn_issn',
                'biblio.image',
                'mst_publisher.publisher_name',
                DB::raw('GROUP_CONCAT(mst_author.author_name ORDER BY biblio_author.level) as authors')
            )
            ->groupBy(
                'biblio.biblio_id',
                'biblio.title',
                'biblio.isbn_issn',
                'biblio.image',
                'mst_publisher.publisher_name'
            )
            ->get();

        return view('buku.index', ['title' => 'Bibliografi', 'books' => $books]);
    }

    public function formTambahBuku()
    {
        return view('buku.create', [
            'title' => 'Tambah Bibliografi Baru',
            'authors' => DB::table('mst_author')->get(),
            'publishers' => DB::table('mst_publisher')->get(),
            'publishPlaces' => DB::table('mst_place')->get(),
            'languages' => DB::table('mst_language')->get(),
            'gmds' => DB::table('mst_gmd')->get(),
            'topics' => DB::table('mst_topic')->get(),
            'books' => DB::table('biblio')->get(),
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
                    $data['authors'][] = DB::table('mst_author')->insertGetId([
                        'author_name' => $author
                    ]);
                }
            }

            $data['authors'] = array_values(array_filter(
                $data['authors'], fn($a) => is_numeric($a))
            );
        }

        if (isset($data['publisher_id']) && !is_numeric($data['publisher_id'])) {
            $data['publisher_id'] = DB::table('mst_publisher')->insertGetId([
                'publisher_name' => $data['publisher_id']
            ]);
        }

        if (isset($data['publish_place_id']) && !is_numeric($data['publish_place_id'])) {
            $data['publish_place_id'] = DB::table('mst_place')->insertGetId([
                'place_name' => $data['publish_place_id']
            ]);
        }

        if (isset($data['language_id']) && strlen($data['language_id']) > 2) {
            $language = explode(':', $data['language_id']);

            if (count($language) != 2) {
                $language[1] = $language[0];
                $language[0] = substr(strtolower($language[1]), 0, 2);
            }

            $data['language_id'] = DB::table('mst_language')->insertGetId([
                'language_id' => $language[0],
                'language_name' => $language[1],
            ]);
        }

        if (isset($data['subjects']) && !empty($data['subjects'])) {
            foreach($data['subjects'] as $subject) {
                if (!is_numeric($subject)) {
                    $data['subjects'][] = DB::table('mst_topic')->insertGetId([
                        'topic' => $subject,
                        'topic_type' => 't',
                        'classification' => ''
                    ]);
                }
            }

            $data['subjects'] = array_values(array_filter(
                $data['subjects'], fn($s) => is_numeric($s))
            );
        }

        $biblioId = DB::table('biblio')->insertGetId([
            'gmd_id' => $data['gmd_id'] ?? null,
            'title' => $data['title'],
            'sor' => $data['sor'] ?? null,
            'edition' => $data['edition'] ?? null,
            'isbn_issn' => $data['isbn_issn'] ?? null,
            'publisher_id' => $data['publisher_id'] ?? null,
            'publish_year' => $data['publish_year'] ?? null,
            'collation' => $data['collation'] ?? null,
            'series_title' => $data['series_title'] ?? null,
            'call_number' => $data['call_number'] ?? null,
            'language_id' => $data['language_id'] ?? null,
            'publish_place_id' => $data['publish_place_id'] ?? null,
            'classification' => $data['classification'] ?? null,
            'notes' => $data['notes'] ?? null,
            'image' => 'storage/'.$request->file('image')?->store('cover', 'public'),
            'opac_hide' => $data['opac_hide'],
            'promoted' => $data['promoted'],
            'labels' => isset($data['labels']) ? serialize(array_values($data['labels'])) : null,
            'frequency_id' => $data['frequency_id'],
            'spec_detail_info' => $data['spec_detail_info'] ?? null,
            'content_type_id' => $data['content_type_id'] ?? null,
            'media_type_id' => $data['media_type_id'] ?? null,
            'carrier_type_id' => $data['carrier_type_id'] ?? null,
        ]);

        if (isset($data['authors'])) {
            $biblioAuthors = collect($data['authors'])->map(fn($authorId, $index) => [
                'biblio_id' => $biblioId,
                'author_id' => $authorId,
                'level' => $index + 1
            ], $data['authors'])->toArray();

            DB::table('biblio_author')->insert($biblioAuthors);
        }

        if (isset($data['biblio_relation']) && !empty($data['biblio_relation'])) {
            $biblioRelations = array_map(fn($biblioRelationId) => [
                'biblio_id' => $biblioId,
                'rel_biblio_id' => $biblioRelationId,
            ], $data['biblio_relation']);

            DB::table('biblio_relation')->insert($biblioRelations);
        }

        if (isset($data['subjects']) && !empty($data['subjects'])) {
            $biblioTopics = array_map(fn($topic) => [
                'biblio_id' => $biblioId,
                'topic_id' => $topic,
            ], $data['subjects']);

            DB::table('biblio_topic')->insert($biblioTopics);
        }

        if (isset($data['attachment_file']) && !empty($data['attachment_file'])) {
            foreach ($data['attachment_file'] as $file) {
                $fileId = DB::table('files')->insertGetId([
                    'file_title' => $file['file_title'],
                    'file_name' => $file['file_name']->store('lampiran', 'public'),
                    'file_url' => $file['file_url'],
                    'file_dir' => '',
                    'mime_type' => $file['file_name']->getMimeType(),
                    'file_desc' => $file['file_desc'],
                    'uploader_id' => Auth::user()->user_id,
                ]);

                if ($file['access_type'] == 'private') {
                    $accessLimit = !empty($file['access_limit'])
                        ? serialize($file['access_limit'])
                        : null;
                } else {
                    $accessLimit = null;
                }

                DB::table('biblio_attachment')->insert([
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
//         return response(json_decode('{
//   "title": "Belajar Pemrograman Dan Hacking Menggunakan Python",
//   "authors[]": [
//     "Wardana"
//   ],
//   "publisher_id": null,
//   "publish_year": "2019",
//   "subjects[]": [ ],
//   "image_url": null,
//   "language_id": "id",
//   "notes": "Python adalah salah satu bahasa pemrograman yang populer digunakan untuk membuat berbagai macam program, seperti program CLI, Program GUI (desktop), Aplikasi Mobile, Web, IoT, Game, Program untuk Hacking, dan sebagainya. Python telah terkenal sebagai bahasa pemrograman yang banyak digunakan oleh hacker. Dan buku ini mengetengahkan bagaimana membuat aplikasi hacking menggunakan bahasa pemrograman Python. Buku ini pun mengajarkan dasar-dasar pemrograman Python hingga cara membuat aplikasi jaringan/hacking menggunakan bahasa pemrograman Python sehingga dapat menjadi panduan belajar Python bagi pemula. Untuk meningkatkan kemampuan pembaca maka buku ini mengetengahkan bagaimana melakukan hacking termasuk cara kerja metode hacking yang terkenal, seperti sql injection, google hacking, Cross-site request forgery (CSRF), cara membuat port scanner, dan ping sweep dengan Python. Dari aspek cyber security, buku ini mengetengahkan pula dasar-dasar persandian (kriptografi) dan steganografi (penyembunyian pesan rahasia) dan lengkap dengan script Pythonnya. Pembahasan dalam buku mencakup: • Dasar-dasar pemrograman Python • Dasar-dasar internet dan pemrograman Python • Dasar-dasar kriptografi dan steganografi • Teknik hacking • Cara membuat script hacking dengan Python • Cyber crime dan cyber security",
//   "collation": null,
//   "media_type_id": "unmediated",
//   "carrier_type_id": "volume",
//   "content_type_id": "text",
//   "isbn_issn": "9786230010507",
//   "gmd_id": [ ],
//   "biblio_relation[]": [ ]
// }', true));
        // return response([
        //     'title' => 'Belajar Pemrograman dan Hacking Menggunakan Python',
        //     'publisher_id' => 'Elex Media Komputindo',
        //     'authors[]' => ['Wardana', 'Muhammad Akmal'],
        //     'publish_year' => '2019',
        //     'series_title' => '',
        //     'notes' => 'Python adalah salah satu bahasa pemrograman yang populer digunakan untuk membuat berbagai macam program, seperti program CLI, Program GUI (desktop), Aplikasi Mobile, Web, IoT, Game, Program untuk Hacking, dan sebagainya. Python telah terkenal sebagai bahasa pemrograman yang banyak digunakan oleh hacker. Dan buku ini mengetengahkan bagaimana membuat aplikasi hacking menggunakan bahasa pemrograman Python. Buku ini pun mengajarkan dasar-dasar pemrograman Python hingga cara membuat aplikasi jaringan/hacking menggunakan bahasa pemrograman Python sehingga dapat menjadi panduan belajar Python bagi pemula. Untuk meningkatkan kemampuan pembaca maka buku ini mengetengahkan bagaimana melakukan hacking termasuk cara kerja metode hacking yang terkenal, seperti SQL Injection, Google Hacking, Cross-site Request Forgery (CSRF), cara membuat port scanner, dan ping sweep dengan Python. Dari aspek cyber security, buku ini mengetengahkan pula dasar-dasar persandian (kriptografi ) dan steganografi (penyembunyian pesan rahasia) dan lengkap dengan script Python. Pembahasan dalam buku mencakup: ¥ Dasar-dasar pemrograman Python ¥ Dasar-dasar internet dan pemprogram Python ¥ Dasar-dasar kriptografi dan steganografi ¥ Teknik hacking ¥ Cara membuat script hacking dengan Python ¥ Cyber crime dan cyber security',
        //     'collation' => 'viii, 254 halaman : ilustrasi ; 21 cm',
        //     'subjects[]' => [
        //         "Komputer", "Bahasa pemrograman", "Phyton"
        //     ],
        //     'biblio_relation[]' => ["Komputer", "Bahasa pemrograman", "Phyton"],
        //     'gmd_id' => ["Komputer", "Bahasa pemrograman", "Phyton"],
        //     'image_url' => 'http://books.google.com/books/content?id=kCvGDwAAQBAJ&printsec=frontcover&img=1&zoom=5&edge=curl&source=gbs_api',
        //     'language_id' => 'id',
        //     'publish_place_id' => 'Jakarta',
        //     'spec_detail_info' => 'Bibliografi : halaman 253-254Isi : Bab 1 Pendahuluan-- Bab 2 Cara instalasi Phyton dan editornya-- Bab 3 Dasar-dasar pemrograman Phyton-- Bab 4 Pemrograman Phyton tingkat lanjut-- Bab 5 Pemrograman GUI menggunakan Wxpyhton-- Bab 6 Pemrograman database-- Bab 7 Pengenalan konsep jaringan komputer-- Bab 8 Pemrograman jaringan dan internet-- Bab 9 Kriptografi dan steganografi-- Bab 10 Hacking menggunakan Phyton-- Bab 11 Cyber crime dan cyber security-- Bab 12 Pencegahan hacking',
        //     'content_type_id' => 'text',
        //     'carrier_type_id' => 'volume',
        //     'edition' => '',
        //     'media_type_id' => 'unmediated',
        //     'coll_type_id' => 'Bukan Fiksi',
        //     'isbn_issn' => $isbn,
        // ]);

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

            $crawler = new Crawler($detail);

            $book['collation'] = $crawler->filter('#lblDeskripsiFisik')->text($book['collation'] ?? '');

            $book['spec_detail_info'] = $crawler->filter('#lblCatatan')->text($book['spec_detail_info']);

            $book['notes'] = $book['notes'] ?? $crawler->filter('#lblAbstrak')->text('');
            $book['edition'] = $crawler->filter('#lblEdisi')->text('');
            $book['coll_type_id'] = ucwords($crawler->filter('#lblBentukKarya')->text(''));
        }

        return response($book);
    }

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
}
